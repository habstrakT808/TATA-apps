import 'dart:developer';

import 'package:TATA/helper/user_preferences.dart';
import 'package:TATA/sendApi/Server.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:flutter/material.dart';

class RatingPage extends StatefulWidget {
  final String uuid;
  const RatingPage({super.key, required this.uuid});

  @override
  State<RatingPage> createState() => _RatingPageState();
}

class _RatingPageState extends State<RatingPage> {
  int rating = 0;
  final TextEditingController commentController = TextEditingController();

  void submitRating() async {
    final komentar = commentController.text.trim();
    final ALLDATAUSER = await UserPreferences.getUser();
    final token = ALLDATAUSER!['access_token'];
    print("token " + token);
    if (rating == 0 || komentar.isEmpty || widget.uuid.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
        content: Text('Silakan beri rating dan komentar terlebih dahulu!'),
        backgroundColor: Colors.redAccent,
      ));
      return;
    } else {
      try {
        final response = await http.post(
          Server.urlLaravel('pesanan/review/add-by-uuid'),
          headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer $token'
          },
          body: jsonEncode({
            'uuid': widget.uuid,
            'review': komentar,
            'rating': rating,
          }),
        );

        final responseData = json.decode(response.body);

        if (response.statusCode == 200 && responseData['status'] == 'success') {
          ScaffoldMessenger.of(context).showSnackBar(SnackBar(
            content: Text('Berhasil mengirim review!'),
            backgroundColor: Colors.green,
          ));
          Navigator.pop(context); // Kembali ke halaman sebelumnya
        } else {
          ScaffoldMessenger.of(context).showSnackBar(SnackBar(
            content: Text(' ${responseData['message'] ?? 'Terjadi kesalahan'}'),
            backgroundColor: Colors.redAccent,
          ));
        }
        print("ERRORR${widget.uuid}");
        print("ERRORR$responseData");
      } catch (e) {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(
          content: Text('Error: $e'),
          backgroundColor: Colors.redAccent,
        ));
        print("ERRORR$e");
      }
    }
  }

  // Gradien berdasarkan rating
  List<Color> getGradientColors() {
    switch (rating) {
      case 1:
        return [Colors.red.shade300, Colors.pink.shade100];
      case 2:
        return [Colors.deepOrange.shade400, Colors.pink.shade200];
      case 3:
        return [Colors.orange.shade300, Colors.yellow.shade200];
      case 4:
        return [Colors.lime.shade200, Colors.lightGreen.shade300];
      case 5:
        return [Colors.teal.shade300, Colors.cyan.shade200];
      default:
        return [Colors.grey.shade200, Colors.grey.shade100];
    }
  }

  Color getStarColor() {
    switch (rating) {
      case 1:
        return Colors.red;
      case 2:
        return Colors.deepOrange;
      case 3:
        return Colors.orange;
      case 4:
        return Colors.green;
      case 5:
        return Colors.teal;
      default:
        return Colors.grey;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: AnimatedContainer(
        height: double.infinity,
        duration: const Duration(milliseconds: 500),
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: getGradientColors(),
          ),
        ),
        child: SafeArea(
          child: SingleChildScrollView(
            padding: const EdgeInsets.symmetric(horizontal: 24.0, vertical: 20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // AppBar Custom
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: const [
                    Text(
                      'Nilai Pekerjaan',
                      style: TextStyle(
                        fontSize: 26,
                        fontWeight: FontWeight.bold,
                        color: Colors.black87,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 130),

                // Bintang
                const Text(
                  'Beri Penilaian:',
                  style: TextStyle(fontSize: 20, fontWeight: FontWeight.w600),
                ),
                const SizedBox(height: 12),
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: List.generate(5, (index) {
                    return AnimatedContainer(
                      duration: const Duration(milliseconds: 200),
                      child: IconButton(
                        iconSize: 48,
                        icon: Icon(
                          index < rating ? Icons.star : Icons.star_border,
                          color:
                              index < rating ? getStarColor() : Colors.black26,
                        ),
                        onPressed: () {
                          setState(() {
                            rating = index + 1;
                          });
                        },
                      ),
                    );
                  }),
                ),
                const SizedBox(height: 32),

                // Komentar
                const Text(
                  'Tulis Komentar:',
                  style: TextStyle(fontSize: 18, fontWeight: FontWeight.w500),
                ),
                const SizedBox(height: 10),
                Container(
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.9),
                    borderRadius: BorderRadius.circular(16),
                    boxShadow: const [
                      BoxShadow(
                        color: Colors.black26,
                        blurRadius: 8,
                        offset: Offset(0, 4),
                      ),
                    ],
                  ),
                  padding: const EdgeInsets.all(14),
                  child: TextField(
                    controller: commentController,
                    maxLines: 6,
                    decoration: const InputDecoration.collapsed(
                      hintText: 'Tuliskan pengalaman kamu...',
                    ),
                  ),
                ),
                const SizedBox(height: 40),

                // Tombol Kirim
                Center(
                  child: ElevatedButton.icon(
                    onPressed: submitRating,
                    label: const Text(
                      'Kirim Penilaian',
                      style: TextStyle(fontSize: 18, color: Colors.white),
                    ),
                    iconAlignment: IconAlignment.start,
                    icon: const Icon(Icons.send, color: Colors.white),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: getStarColor(),
                      padding: const EdgeInsets.symmetric(
                          horizontal: 36, vertical: 16),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(32),
                      ),
                      elevation: 6,
                      shadowColor: Colors.black45,
                    ),
                  ),
                )
              ],
            ),
          ),
        ),
      ),
    );
  }
}
