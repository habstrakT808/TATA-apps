import 'package:flutter/foundation.dart';
import 'dart:io';

class Server {
  // Base URL for Laravel backend
  static String _baseUrl = kIsWeb ? "http://localhost:8000" : "http://10.0.2.2:8000";
  
  // Initialize with default URL or custom URL
  static void init({String? baseUrl}) {
    if (baseUrl != null) {
      _baseUrl = baseUrl;
    } else {
      // Use appropriate base URL based on platform
      if (kIsWeb) {
        _baseUrl = "http://localhost:8000";
      } else if (Platform.isAndroid) {
        // Use 10.0.2.2 for Android emulator or localhost for real device
        _baseUrl = "http://10.0.2.2:8000";
      } else {
        _baseUrl = "http://localhost:8000";
      }
    }
    debugPrint("Server initialized with base URL: $_baseUrl");
  }
  
  // Get base URL
  static String get baseUrl => _baseUrl;
  
  // Set base URL
  static set baseUrl(String url) {
    _baseUrl = url;
    debugPrint("Server base URL changed to: $_baseUrl");
  }
  
  // Build full Laravel API URL
  static Uri urlLaravel(String path) {
    // Jangan tambahkan prefix mobile/ secara otomatis
    // Biarkan pengembang menentukan path yang tepat
    
    // Periksa apakah path sudah dimulai dengan 'api/'
    String fullPath;
    if (path.startsWith('api/')) {
      // Path sudah memiliki prefix 'api/', gunakan langsung
      fullPath = "$_baseUrl/${path}";
    } else {
      // Path belum memiliki prefix 'api/', tambahkan
      fullPath = "$_baseUrl/api/${path.startsWith('/') ? path.substring(1) : path}";
    }
    
    debugPrint("URL: $fullPath");
    return Uri.parse(fullPath);
  }
  
  // Build full Laravel API URL as string
  static String urlLaravelString(String path) {
    // Periksa apakah path sudah dimulai dengan 'api/'
    String fullPath;
    if (path.startsWith('api/')) {
      // Path sudah memiliki prefix 'api/', gunakan langsung
      fullPath = "$_baseUrl/${path}";
    } else {
      // Path belum memiliki prefix 'api/', tambahkan
      fullPath = "$_baseUrl/api/${path.startsWith('/') ? path.substring(1) : path}";
    }
    
    debugPrint("URL String: $fullPath");
    return fullPath;
  }
  
  // Helper function untuk URL gambar
  static String UrlGambar(String fileName) {
    if (fileName.startsWith("http")) {
      return fileName;
    }
    return "assets/images/$fileName";
  }
  
  // Helper function untuk URL gambar pesanan
  static String UrlImageReferensi(String url, String pesananUuid) {
    if (url == null || url.isEmpty) {
      return "assets/images/default.png";
    }

    // Bersihkan UUID dari karakter yang tidak diinginkan
    String cleanUuid = pesananUuid.replaceAll(RegExp(r'[^a-zA-Z0-9]'), '');
      
    // Jika URL sudah lengkap, gunakan secara langsung
    if (url.startsWith('http')) {
      return url;
    }
      
    // Untuk web, gunakan image proxy untuk menghindari masalah CORS
    if (kIsWeb) {
      return "$_baseUrl/image-proxy.php?type=pesanan&uuid=$cleanUuid&file=$url";
    }
    return "$_baseUrl/assets3/img/pesanan/$cleanUuid/catatan_pesanan/$url";
  }

  static String UrlImageProfil(String url) {
    // Untuk mengatasi masalah CORS di web, gunakan URL relatif
    if (kIsWeb) {
      // Cek jika URL valid
      if (url == null || url.isEmpty) {
        return "assets/images/default.png";
      }
      return "$_baseUrl/image-proxy.php?type=profil&file=$url";
    }
    return "$_baseUrl/assets3/img/user/$url";
  }
  
  // Check if server is reachable
  static Future<bool> isReachable({int timeoutSeconds = 5}) async {
    try {
      final hostname = _baseUrl.replaceAll(RegExp(r'https?://'), '').split('/')[0];
      final result = await InternetAddress.lookup(hostname)
          .timeout(Duration(seconds: timeoutSeconds));
      return result.isNotEmpty && result[0].rawAddress.isNotEmpty;
    } on SocketException catch (_) {
      debugPrint("Server is not reachable: $_baseUrl");
      return false;
    } catch (e) {
      debugPrint("Error checking server: $e");
      return false;
    }
  }
}
