import 'package:TATA/helper/fcm_helper.dart';
import 'package:TATA/helper/user_preferences.dart';
import 'package:TATA/menu/AkunPage.dart';
import 'package:TATA/menu/ChatPage.dart';
import 'package:TATA/menu/HomePage.dart';
import 'package:TATA/menu/PemesananPage.dart';
import 'package:TATA/splashscreen.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:flutter/material.dart';
import 'package:TATA/src/bottomnav.dart';
import 'firebase_options.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/foundation.dart' show kIsWeb;

// Key global untuk navigasi di seluruh aplikasi
final GlobalKey<NavigatorState> navigatorKey = GlobalKey<NavigatorState>();

void main() async {
  WidgetsFlutterBinding.ensureInitialized(); // ⬅️ Panggil ini DULU
  
  // Initialize Firebase with platform-specific options
  await Firebase.initializeApp(
    options: DefaultFirebaseOptions.currentPlatform,
  );
  
  // Set locale Firebase Auth
  FirebaseAuth.instance.setLanguageCode('id');
  
  // Initialize FCM helper
  final fcmHelper = FCMHelper();
  await fcmHelper.initialize();

  // Additional setup for web platform
  if (kIsWeb) {
    await fcmHelper.setupWebFCM();
  }
  
  // Initialize SharedPreferences for user data
  await UserPreferences.init();
  
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      navigatorKey: navigatorKey, // Tambahkan navigatorKey di sini
      debugShowCheckedModeBanner: false,
      home: SplashScreen(),
    );
  }
}

class MainPage extends StatefulWidget {
  final int initialIndex;

  const MainPage({super.key, this.initialIndex = 0});

  @override
  _MainPageState createState() => _MainPageState();
}

class _MainPageState extends State<MainPage> {
  late int _currentIndex;
  String? id_user;
  List<Widget> _pages = [];

  @override
  void initState() {
    super.initState();
    _currentIndex = widget.initialIndex;
    getIdUser();
  }

  Future<void> getIdUser() async {
    final data = await UserPreferences.getUser();
    print("ALLDATAA : $data");
    setState(() {
      id_user = data!['user']['id'] ?? '';
      _pages = [
        const HomePage(),
        PemesananPage(),
        ChatPage(),
        Akunpage(),
      ];
    });
  }

  void _onItemTapped(int index) {
    setState(() {
      _currentIndex = index;
    });
  }

  @override
  Widget build(BuildContext context) {
    if (id_user == null || _pages.isEmpty) {
      return Scaffold(
        body: Center(child: CircularProgressIndicator()),
      );
    }

    return Scaffold(
      body: _pages[_currentIndex],
      bottomNavigationBar: Bottomnav(
        currentIndex: _currentIndex,
        onTap: _onItemTapped,
      ),
    );
  }
}
