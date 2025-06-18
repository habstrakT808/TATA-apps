# Flutter Chat Implementation Guide

## 1. Dependencies (pubspec.yaml)

```yaml
dependencies:
  firebase_core: ^2.24.2
  firebase_messaging: ^14.7.10
  cloud_firestore: ^4.13.6
  firebase_storage: ^11.5.6
  http: ^1.1.0
  image_picker: ^1.0.4
  cached_network_image: ^3.3.0
  flutter_chat_ui: ^1.6.10
```

## 2. Firebase Service (Dart)

```dart
// lib/services/firebase_service.dart
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:firebase_storage/firebase_storage.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'dart:io';

class FirebaseService {
  static final FirebaseFirestore _firestore = FirebaseFirestore.instance;
  static final FirebaseMessaging _messaging = FirebaseMessaging.instance;
  static final FirebaseStorage _storage = FirebaseStorage.instance;
  
  // Get FCM Token
  static Future<String?> getFCMToken() async {
    try {
      String? token = await _messaging.getToken();
      print('FCM Token: $token');
      return token;
    } catch (e) {
      print('Error getting FCM token: $e');
      return null;
    }
  }
  
  // Listen to messages realtime
  static Stream<QuerySnapshot> getChatMessages(String pesananUuid) {
    return _firestore
        .collection('chats')
        .doc(pesananUuid)
        .collection('messages')
        .orderBy('timestamp', descending: true)
        .snapshots();
  }
  
  // Send message to Laravel API
  static Future<bool> sendMessage({
    required String pesananUuid,
    required String message,
    required String messageType,
    File? imageFile,
    required String authToken,
  }) async {
    try {
      var request = http.MultipartRequest(
        'POST',
        Uri.parse('https://your-api.com/api/chat/send'),
      );
      
      request.headers['Authorization'] = 'Bearer $authToken';
      request.fields['pesanan_uuid'] = pesananUuid;
      request.fields['message_type'] = messageType;
      
      if (messageType == 'text') {
        request.fields['message'] = message;
      } else if (messageType == 'image' && imageFile != null) {
        request.files.add(
          await http.MultipartFile.fromPath('image', imageFile.path),
        );
      }
      
      var response = await request.send();
      var responseData = await response.stream.bytesToString();
      var jsonResponse = json.decode(responseData);
      
      return jsonResponse['status'] == 'success';
    } catch (e) {
      print('Error sending message: $e');
      return false;
    }
  }
  
  // Update FCM Token
  static Future<bool> updateFCMToken(String authToken) async {
    try {
      String? fcmToken = await getFCMToken();
      if (fcmToken == null) return false;
      
      var response = await http.post(
        Uri.parse('https://your-api.com/api/chat/update-fcm-token'),
        headers: {
          'Authorization': 'Bearer $authToken',
          'Content-Type': 'application/json',
        },
        body: json.encode({'fcm_token': fcmToken}),
      );
      
      var jsonResponse = json.decode(response.body);
      return jsonResponse['status'] == 'success';
    } catch (e) {
      print('Error updating FCM token: $e');
      return false;
    }
  }
  
  // Mark messages as read
  static Future<bool> markAsRead({
    required String pesananUuid,
    required List<String> messageIds,
    required String authToken,
  }) async {
    try {
      var response = await http.post(
        Uri.parse('https://your-api.com/api/chat/mark-read'),
        headers: {
          'Authorization': 'Bearer $authToken',
          'Content-Type': 'application/json',
        },
        body: json.encode({
          'pesanan_uuid': pesananUuid,
          'message_ids': messageIds,
        }),
      );
      
      var jsonResponse = json.decode(response.body);
      return jsonResponse['status'] == 'success';
    } catch (e) {
      print('Error marking as read: $e');
      return false;
    }
  }
}
```

## 3. Chat Screen Widget

```dart
// lib/screens/chat_screen.dart
import 'package:flutter/material.dart';
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:image_picker/image_picker.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../services/firebase_service.dart';
import 'dart:io';

class ChatScreen extends StatefulWidget {
  final String pesananUuid;
  final String authToken;
  
  const ChatScreen({
    Key? key,
    required this.pesananUuid,
    required this.authToken,
  }) : super(key: key);

  @override
  _ChatScreenState createState() => _ChatScreenState();
}

class _ChatScreenState extends State<ChatScreen> {
  final TextEditingController _messageController = TextEditingController();
  final ScrollController _scrollController = ScrollController();
  final ImagePicker _imagePicker = ImagePicker();
  
  @override
  void initState() {
    super.initState();
    // Update FCM token when chat opens
    FirebaseService.updateFCMToken(widget.authToken);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Chat Pesanan'),
        backgroundColor: Colors.blue,
      ),
      body: Column(
        children: [
          // Messages List
          Expanded(
            child: StreamBuilder<QuerySnapshot>(
              stream: FirebaseService.getChatMessages(widget.pesananUuid),
              builder: (context, snapshot) {
                if (snapshot.hasError) {
                  return Center(child: Text('Error: ${snapshot.error}'));
                }
                
                if (snapshot.connectionState == ConnectionState.waiting) {
                  return Center(child: CircularProgressIndicator());
                }
                
                var messages = snapshot.data?.docs ?? [];
                
                return ListView.builder(
                  controller: _scrollController,
                  reverse: true,
                  itemCount: messages.length,
                  itemBuilder: (context, index) {
                    var messageData = messages[index].data() as Map<String, dynamic>;
                    return _buildMessageBubble(messageData);
                  },
                );
              },
            ),
          ),
          
          // Message Input
          _buildMessageInput(),
        ],
      ),
    );
  }
  
  Widget _buildMessageBubble(Map<String, dynamic> messageData) {
    bool isUser = messageData['sender_type'] == 'user';
    bool isImage = messageData['message_type'] == 'image';
    
    return Container(
      margin: EdgeInsets.symmetric(vertical: 4, horizontal: 8),
      child: Row(
        mainAxisAlignment: isUser ? MainAxisAlignment.end : MainAxisAlignment.start,
        children: [
          if (!isUser) ...[
            CircleAvatar(
              radius: 16,
              backgroundColor: Colors.grey[300],
              child: Icon(Icons.admin_panel_settings, size: 16),
            ),
            SizedBox(width: 8),
          ],
          
          Flexible(
            child: Container(
              padding: EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: isUser ? Colors.blue[100] : Colors.grey[200],
                borderRadius: BorderRadius.circular(16),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Sender name
                  Text(
                    messageData['sender_name'] ?? 'Unknown',
                    style: TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.bold,
                      color: Colors.grey[600],
                    ),
                  ),
                  SizedBox(height: 4),
                  
                  // Message content
                  if (isImage && messageData['image_url'] != null)
                    _buildImageMessage(messageData['image_url'])
                  else
                    Text(
                      messageData['message'] ?? '',
                      style: TextStyle(fontSize: 14),
                    ),
                  
                  SizedBox(height: 4),
                  
                  // Timestamp
                  Text(
                    _formatTimestamp(messageData['created_at']),
                    style: TextStyle(
                      fontSize: 10,
                      color: Colors.grey[500],
                    ),
                  ),
                ],
              ),
            ),
          ),
          
          if (isUser) ...[
            SizedBox(width: 8),
            CircleAvatar(
              radius: 16,
              backgroundColor: Colors.blue[300],
              child: Icon(Icons.person, size: 16),
            ),
          ],
        ],
      ),
    );
  }
  
  Widget _buildImageMessage(String imageUrl) {
    return GestureDetector(
      onTap: () => _showFullImage(imageUrl),
      child: Container(
        constraints: BoxConstraints(maxWidth: 200, maxHeight: 200),
        child: ClipRRect(
          borderRadius: BorderRadius.circular(8),
          child: CachedNetworkImage(
            imageUrl: imageUrl,
            fit: BoxFit.cover,
            placeholder: (context, url) => Container(
              height: 100,
              child: Center(child: CircularProgressIndicator()),
            ),
            errorWidget: (context, url, error) => Container(
              height: 100,
              child: Icon(Icons.error),
            ),
          ),
        ),
      ),
    );
  }
  
  Widget _buildMessageInput() {
    return Container(
      padding: EdgeInsets.all(8),
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.3),
            spreadRadius: 1,
            blurRadius: 3,
            offset: Offset(0, -1),
          ),
        ],
      ),
      child: Row(
        children: [
          // Image picker button
          IconButton(
            icon: Icon(Icons.image, color: Colors.blue),
            onPressed: _pickImage,
          ),
          
          // Text input
          Expanded(
            child: TextField(
              controller: _messageController,
              decoration: InputDecoration(
                hintText: 'Ketik pesan...',
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(20),
                ),
                contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              ),
              maxLines: null,
            ),
          ),
          
          SizedBox(width: 8),
          
          // Send button
          IconButton(
            icon: Icon(Icons.send, color: Colors.blue),
            onPressed: _sendTextMessage,
          ),
        ],
      ),
    );
  }
  
  void _sendTextMessage() async {
    String message = _messageController.text.trim();
    if (message.isEmpty) return;
    
    _messageController.clear();
    
    bool success = await FirebaseService.sendMessage(
      pesananUuid: widget.pesananUuid,
      message: message,
      messageType: 'text',
      authToken: widget.authToken,
    );
    
    if (!success) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Gagal mengirim pesan')),
      );
    }
  }
  
  void _pickImage() async {
    final XFile? image = await _imagePicker.pickImage(
      source: ImageSource.gallery,
      maxWidth: 1024,
      maxHeight: 1024,
      imageQuality: 80,
    );
    
    if (image != null) {
      _sendImageMessage(File(image.path));
    }
  }
  
  void _sendImageMessage(File imageFile) async {
    bool success = await FirebaseService.sendMessage(
      pesananUuid: widget.pesananUuid,
      message: '',
      messageType: 'image',
      imageFile: imageFile,
      authToken: widget.authToken,
    );
    
    if (!success) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Gagal mengirim gambar')),
      );
    }
  }
  
  void _showFullImage(String imageUrl) {
    showDialog(
      context: context,
      builder: (context) => Dialog(
        child: Container(
          child: CachedNetworkImage(
            imageUrl: imageUrl,
            fit: BoxFit.contain,
          ),
        ),
      ),
    );
  }
  
  String _formatTimestamp(String? timestamp) {
    if (timestamp == null) return '';
    try {
      DateTime dateTime = DateTime.parse(timestamp);
      return '${dateTime.hour.toString().padLeft(2, '0')}:${dateTime.minute.toString().padLeft(2, '0')}';
    } catch (e) {
      return '';
    }
  }
}
```

## 4. FCM Background Handler

```dart
// lib/main.dart
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';

// Background message handler
Future<void> _firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  await Firebase.initializeApp();
  print('Background message: ${message.messageId}');
}

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await Firebase.initializeApp();
  
  // Set background message handler
  FirebaseMessaging.onBackgroundMessage(_firebaseMessagingBackgroundHandler);
  
  runApp(MyApp());
}
```

## 5. Usage Example

```dart
// Navigate to chat
Navigator.push(
  context,
  MaterialPageRoute(
    builder: (context) => ChatScreen(
      pesananUuid: 'your-pesanan-uuid',
      authToken: 'your-auth-token',
    ),
  ),
);
```

## 6. Firebase Structure

```
Firestore Database:
├── chats/
│   └── {pesanan_uuid}/
│       └── messages/
│           ├── {message_id_1}
│           ├── {message_id_2}
│           └── ...
└── users/
    └── {user_uuid}/
        ├── fcm_token
        └── last_updated

Firebase Storage:
└── chat_images/
    └── {pesanan_uuid}/
        ├── image1.jpg
        ├── image2.png
        └── ...
```

## 7. Environment Variables (.env)

```env
FIREBASE_SERVER_KEY=your_server_key
FIREBASE_SENDER_ID=your_sender_id
FIREBASE_PROJECT_ID=your_project_id
FIREBASE_DATABASE_URL=your_database_url
FIREBASE_STORAGE_BUCKET=your_storage_bucket
FIREBASE_CREDENTIALS_PATH=storage/app/firebase/service-account.json
``` 