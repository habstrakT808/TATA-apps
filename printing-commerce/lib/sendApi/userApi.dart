import 'dart:convert';
import 'package:http/http.dart' as http;
import 'Server.dart';
import 'tokenJWT.dart';

class UserApi {
  static Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      final response = await http.post(
        Uri.parse(Server.baseUrl + Server.login),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'email': email,
          'password': password,
        }),
      );

      final data = jsonDecode(response.body);
      
      if (response.statusCode == 200) {
        await TokenJWT.saveToken(data['data']['access_token']);
        await TokenJWT.saveUserData(data['data']['user']);
        return data;
      } else {
        throw Exception(data['message'] ?? 'Login failed');
      }
    } catch (e) {
      throw Exception('Failed to connect to server: $e');
    }
  }

  static Future<Map<String, dynamic>> register(String name, String email, String password) async {
    try {
      final response = await http.post(
        Uri.parse(Server.baseUrl + Server.register),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'name': name,
          'email': email,
          'password': password,
        }),
      );

      final data = jsonDecode(response.body);
      
      if (response.statusCode == 200) {
        return data;
      } else {
        throw Exception(data['message'] ?? 'Registration failed');
      }
    } catch (e) {
      throw Exception('Failed to connect to server: $e');
    }
  }

  static Future<Map<String, dynamic>> getJasa() async {
    try {
      final token = await TokenJWT.getToken();
      final response = await http.get(
        Uri.parse(Server.baseUrl + Server.jasa),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      final data = jsonDecode(response.body);
      
      if (response.statusCode == 200) {
        return data;
      } else {
        throw Exception(data['message'] ?? 'Failed to get jasa');
      }
    } catch (e) {
      throw Exception('Failed to connect to server: $e');
    }
  }
} 