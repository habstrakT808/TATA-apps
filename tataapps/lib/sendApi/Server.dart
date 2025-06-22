import 'package:flutter/foundation.dart';
import 'dart:io';

class Server {
  // URL yang berbeda berdasarkan platform (web atau mobile)
  static const String ROUTE = kIsWeb
      ? "http://localhost:8000/api/"
      : "http://10.0.2.2:8000/api/";
  static const bool isDevelopment = true; // Set ke false saat produksi

  static Uri urlLaravel(String url) {
    // Pastikan semua endpoint menggunakan prefix mobile/
    if (!url.startsWith("mobile/") && !url.contains("auth/")) {
      // Khusus untuk endpoint users/register dan users/login, tambahkan prefix mobile/
      if (url == "users/register" || url == "users/login" || url == "users/check-email" || 
          url == "users/forgot-password" || url.startsWith("users/")) {
        return Uri.parse("${ROUTE}mobile/$url");
      }
      // Pastikan endpoint user/profile menggunakan prefix mobile/
      if (url == "user/profile") {
        return Uri.parse("${ROUTE}mobile/$url");
      }
      return Uri.parse("${ROUTE}mobile/$url");
    }
    return Uri.parse("${ROUTE}$url");
  }

  static String UrlImageReferensi(String uuid, String url) {
    // Jika URL kosong atau null, kembalikan string kosong untuk mencegah error
    if (url == null || url.isEmpty) {
      return "";
    }
    
    // Clean UUID jika ada karakter khusus
    final cleanUuid = uuid.replaceAll("#", "");
    
    // Jika dalam mode development, gunakan URL lokal
    if (isDevelopment) {
      // Untuk web, gunakan image proxy untuk menghindari masalah CORS
      if (kIsWeb) {
        try {
          // Tambahkan timestamp untuk menghindari cache
          final timestamp = DateTime.now().millisecondsSinceEpoch;
          
          // Cek apakah ini adalah file hasil desain
          final isHasilDesain = url.contains("jpg") || url.contains("jpeg") || url.contains("png") || url.contains("_");
          final folderPath = isHasilDesain ? "hasil_desain" : "catatan_pesanan";
          
          return "http://localhost:8000/image-proxy.php?type=pesanan&uuid=$cleanUuid&file=$url&folder=$folderPath&t=$timestamp";
        } catch (e) {
          print("Error generating image URL: $e");
          return "";
        }
      }
      
      // Cek apakah ini adalah file hasil desain
      final isHasilDesain = url.contains("jpg") || url.contains("jpeg") || url.contains("png") || url.contains("_");
      final folderPath = isHasilDesain ? "hasil_desain" : "catatan_pesanan";
      
      return "http://localhost:8000/assets3/img/pesanan/$cleanUuid/$folderPath/$url";
    } else {
      // Cek apakah ini adalah file hasil desain
      final isHasilDesain = url.contains("jpg") || url.contains("jpeg") || url.contains("png") || url.contains("_");
      final folderPath = isHasilDesain ? "hasil_desain" : "catatan_pesanan";
      
      return "https://tata-test.my.id/assets3/img/pesanan/$cleanUuid/$folderPath/$url";
    }
  }

  static String UrlImageProfil(String url) {
    // Untuk mengatasi masalah CORS di web, gunakan URL relatif
    if (isDevelopment) {
      // Cek jika URL valid
      if (url == null || url.isEmpty) {
        return "assets/images/logotext.png"; // Gunakan gambar default
      }
      
      // Cek apakah URL adalah URL eksternal (Google profil)
      final isExternalUrl = url.startsWith('http://') || url.startsWith('https://');
      
      // Gunakan proxy PHP untuk menghindari masalah CORS
      if (kIsWeb) {
        if (isExternalUrl) {
          // Untuk URL eksternal seperti Google photo, gunakan proxy dengan tipe external
          return "http://localhost:8000/image-proxy.php?type=external&url=${Uri.encodeComponent(url)}";
        }
        return "http://localhost:8000/image-proxy.php?type=user&file=$url";
      } else {
        // Mobile - jika eksternal URL, gunakan langsung
        if (isExternalUrl) {
          return url; // Gunakan langsung URL eksternal di mobile
        }
        // Mobile tetap menggunakan URL langsung untuk gambar lokal
        return "http://localhost:8000/assets3/img/user/$url";
      }
    } else {
      // Production environment
      // Cek apakah URL adalah URL eksternal (Google profil)
      final isExternalUrl = url.startsWith('http://') || url.startsWith('https://');
      if (isExternalUrl) {
        if (kIsWeb) {
          // Gunakan proxy untuk URL eksternal di web production
          return "https://tata-test.my.id/image-proxy.php?type=external&url=${Uri.encodeComponent(url)}";
        }
        return url; // Gunakan URL eksternal langsung di mobile
      }
      return "https://tata-test.my.id/assets3/img/user/$url";
    }
  }

  static String UrlGambar(String url) {
    return "assets/images/$url";
  }
}
