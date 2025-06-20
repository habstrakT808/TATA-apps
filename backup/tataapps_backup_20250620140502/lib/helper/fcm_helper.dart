import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'package:TATA/helper/user_preferences.dart';
import 'package:TATA/main.dart';
import 'package:TATA/menu/ChatPage.dart';
import 'package:TATA/menu/ChatDetailScreen.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:http/http.dart' as http;
import 'package:TATA/sendApi/Server.dart';

// Background handler untuk FCM (harus di luar class, top-level function)
@pragma('vm:entry-point')
Future<void> _firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  await Firebase.initializeApp();
  print('Handling a background message: ${message.messageId}');
} 

class FCMHelper {
  static final FCMHelper _instance = FCMHelper._internal();
  factory FCMHelper() => _instance;

  FCMHelper._internal();

  final FirebaseMessaging _firebaseMessaging = FirebaseMessaging.instance;
  final FlutterLocalNotificationsPlugin _flutterLocalNotificationsPlugin = 
      FlutterLocalNotificationsPlugin();
  
  // Stream controller untuk broadcast pesan chat
  final StreamController<Map<String, dynamic>> _chatMessageController = 
      StreamController<Map<String, dynamic>>.broadcast();
  
  // Getter untuk stream
  Stream<Map<String, dynamic>> get chatMessageStream => _chatMessageController.stream;

  // Flag untuk menghindari multiple initialization
  bool _isInitialized = false;
  
  // Method untuk menginisialisasi FCM
  Future<void> initialize() async {
    // Hindari inisialisasi ganda
    if (_isInitialized) return;

    // Konfigurasikan notification channel untuk Android
    if (!kIsWeb && Platform.isAndroid) {
      const AndroidNotificationChannel channel = AndroidNotificationChannel(
        'high_importance_channel',
        'High Importance Notifications',
        description: 'Channel untuk notifikasi dengan prioritas tinggi',
        importance: Importance.high,
      );

      // Konfigurasikan flutter_local_notifications
      await _flutterLocalNotificationsPlugin
          .resolvePlatformSpecificImplementation<AndroidFlutterLocalNotificationsPlugin>()
          ?.createNotificationChannel(channel);
    }

    // Inisialisasi flutter local notifications (skip untuk web)
    if (!kIsWeb) {
      const AndroidInitializationSettings initializationSettingsAndroid =
          AndroidInitializationSettings('@mipmap/ic_launcher');
      const DarwinInitializationSettings initializationSettingsIOS =
          DarwinInitializationSettings();
      const InitializationSettings initializationSettings = InitializationSettings(
        android: initializationSettingsAndroid,
        iOS: initializationSettingsIOS,
      );

      await _flutterLocalNotificationsPlugin.initialize(
        initializationSettings,
        onDidReceiveNotificationResponse: (NotificationResponse details) {
          // Handle notification tap
          if (details.payload != null) {
            try {
              final Map<String, dynamic> data = json.decode(details.payload!);
              _handleNotificationTap(data);
            } catch (e) {
              print('Error decoding notification payload: $e');
            }
          }
        },
      );
    }

    // Minta izin notifikasi (iOS/Web)
    try {
      NotificationSettings settings = await _firebaseMessaging.requestPermission(
        alert: true,
        badge: true,
        sound: true,
        provisional: false,
      );

      print('User granted permission: ${settings.authorizationStatus}');

      if (settings.authorizationStatus == AuthorizationStatus.authorized ||
          settings.authorizationStatus == AuthorizationStatus.provisional) {
        // Tangani FCM message ketika app di foreground
        FirebaseMessaging.onMessage.listen((RemoteMessage message) {
          handleForegroundMessage(message);
        });

        // Tangani ketika notifikasi diklik (app di background)
        FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
          handleBackgroundMessageOpen(message);
        });

        // Ambil dan update FCM token
        await updateFCMToken();
      } else {
        print('Notification permissions denied or not determined');
      }
    } catch (e) {
      print('Error requesting notification permissions: $e');
    }

    // Setup background handler (skip untuk web)
    if (!kIsWeb) {
      FirebaseMessaging.onBackgroundMessage(_firebaseMessagingBackgroundHandler);
    }
    
    // Set flag bahwa sudah diinisialisasi
    _isInitialized = true;
  }

  // Metode untuk update FCM token ke server
  Future<void> updateFCMToken() async {
    try {
      String? token = await _firebaseMessaging.getToken();
      if (token != null) {
        print('FCM Token: $token');
        
        // Simpan token secara lokal juga
        await UserPreferences.saveFcmToken(token);
        
        // Kirim token ke server jika user sudah login
        final userData = await UserPreferences.getUser();
        if (userData != null) {
          // Kirim token ke server
          try {
            final response = await http.post(
              Server.urlLaravel('api/chat/update-fcm-token'),
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer ${userData['access_token']}',
              },
              body: jsonEncode({
                'fcm_token': token,
                'device_id': _getDeviceId(),
                'device_type': kIsWeb ? 'web' : (Platform.isAndroid ? 'android' : 'ios'),
              }),
            );

            if (response.statusCode == 200) {
              print('FCM token berhasil diupdate ke server');
            } else {
              print('Gagal update FCM token: ${response.body}');
            }
          } catch (e) {
            print('Error sending FCM token to server: $e');
          }
        } else {
          print('User belum login, token FCM hanya disimpan lokal');
        }
      } else {
        print('Failed to get FCM token');
      }
    } catch (e) {
      print('Error updating FCM token: $e');
    }
  }

  // Helper method untuk mendapatkan device ID
  String _getDeviceId() {
    return kIsWeb ? 'web-${DateTime.now().millisecondsSinceEpoch}' : 'mobile-${DateTime.now().millisecondsSinceEpoch}';
  }

  // Menangani message saat app di foreground
  void handleForegroundMessage(RemoteMessage message) {
    print('Foreground Message: ${message.data}');
    RemoteNotification? notification = message.notification;
    
    // Tampilkan notifikasi lokal (skip untuk web)
    if (!kIsWeb && notification != null) {
      _flutterLocalNotificationsPlugin.show(
        notification.hashCode,
        notification.title,
        notification.body,
        NotificationDetails(
          android: const AndroidNotificationDetails(
            'high_importance_channel',
            'High Importance Notifications',
            channelDescription: 'Channel untuk notifikasi dengan prioritas tinggi',
            icon: '@mipmap/ic_launcher',
            priority: Priority.high,
            importance: Importance.high,
          ),
          iOS: const DarwinNotificationDetails(
            presentAlert: true,
            presentBadge: true,
            presentSound: true,
          ),
        ),
        payload: jsonEncode(message.data),
      );
    }

    // Proses pesan chat jika ada
    if (message.data.containsKey('type') && message.data['type'] == 'chat') {
      _chatMessageController.add(message.data);
    }
  }

  // Menangani klik notifikasi (app di background)
  void handleBackgroundMessageOpen(RemoteMessage message) {
    print('Background Message Opened: ${message.data}');
    _handleNotificationTap(message.data);
  }

  // Handler untuk notifikasi yang di-tap
  void _handleNotificationTap(Map<String, dynamic> data) {
    // Navigasikan berdasarkan tipe notifikasi
    if (data.containsKey('type')) {
      switch (data['type']) {
        case 'chat':
          // Jika ada chat_id, buka halaman chat untuk ID tersebut
          if (data.containsKey('chat_id')) {
            navigatorKey.currentState?.push(
              MaterialPageRoute(
                builder: (_) => ChatDetailScreen(chatId: data['chat_id']),
              ),
            );
          }
          break;
        case 'order_status':
          // Navigasikan ke halaman detail pesanan jika ada
          if (data.containsKey('order_id')) {
            // Implementasikan navigasi ke detail pesanan
          }
          break;
        default:
          navigatorKey.currentState?.push(
            MaterialPageRoute(builder: (_) => MainPage()),
          );
      }
    }
  }

  // Metode untuk membuat token FCM di platform web
  Future<void> setupWebFCM() async {
    if (kIsWeb) {
      try {
        // Setup service worker untuk Firebase Messaging di web
        // Pastikan service-worker.js ada di folder web
        
        // Minta izin notifikasi di web
        await _firebaseMessaging.requestPermission(
          alert: true,
          announcement: false,
          badge: true,
          carPlay: false,
          criticalAlert: false,
          provisional: false,
          sound: true,
        );
        
        // Dapatkan token FCM untuk web
        String? token = await _firebaseMessaging.getToken(
          vapidKey: 'BNKHnrA8nKuQboQWWxjM2qq_yHt1WkHlCpwIgFgQOvOd2rGjUB-HCg1RvmpKRrqybwGjxG7iAI3pzCUi7XnUDGY', // Ganti dengan VAPID key dari Firebase Console
        );
        
        print('Web FCM Token: $token');
        if (token != null) {
          await UserPreferences.saveFcmToken(token);
          
          // Kirim token ke server jika user sudah login
          final userData = await UserPreferences.getUser();
          if (userData != null) {
            try {
              final response = await http.post(
                Server.urlLaravel('api/chat/update-fcm-token'),
                headers: {
                  'Content-Type': 'application/json',
                  'Accept': 'application/json',
                  'Authorization': 'Bearer ${userData['access_token']}',
                },
                body: jsonEncode({
                  'fcm_token': token,
                  'device_id': 'web-${DateTime.now().millisecondsSinceEpoch}',
                  'device_type': 'web',
                }),
              );
              
              if (response.statusCode == 200) {
                print('Web FCM token berhasil diupdate ke server');
              } else {
                print('Gagal update Web FCM token: ${response.body}');
              }
            } catch (e) {
              print('Error sending Web FCM token to server: $e');
            }
          }
        }
        
        // Setup handlers for web
        FirebaseMessaging.onMessage.listen((RemoteMessage message) {
          print('Web FCM message received: ${message.notification?.title}');
          
          // Menampilkan notifikasi web
          if (message.notification != null) {
            // Create browser notification
            // (Web browser sudah menampilkan notifikasi FCM secara otomatis)
          }
          
          // Jika pesan chat, broadcast ke listeners
          if (message.data.containsKey('type') && message.data['type'] == 'chat') {
            _chatMessageController.add(message.data);
          }
        });
      } catch (e) {
        print('Error setting up web FCM: $e');
      }
    }
  }
  
  // Metode untuk mendapatkan token yang tersimpan
  Future<String?> getSavedFcmToken() async {
    return await UserPreferences.getFcmToken();
  }
} 