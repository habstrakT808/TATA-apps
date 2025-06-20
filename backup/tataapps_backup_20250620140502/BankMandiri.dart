// File: lib/pages/pembayaran/MandiriPaymentPage.dart
import 'dart:convert';
import 'dart:io';
import 'dart:math';
import 'dart:typed_data';
import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:http_parser/http_parser.dart';
import 'package:http/http.dart' as http;
import 'package:tataapps/helper/user_preferences.dart';
import 'package:tataapps/menu/JasaDesign/DeskripsiPesanan/BuktiPemesananPage.dart';
import 'package:tataapps/sendApi/Server.dart';
import 'package:tataapps/sendApi/tokenJWT.dart';
import 'package:tataapps/src/CustomColors.dart';
import 'package:tataapps/src/SmoothPageTransition.dart';
import 'package:tataapps/sendApi/PesananApi.dart';
import 'package:tataapps/sendApi/AuthManager.dart';
import 'package:tataapps/sendApi/ChatApiService.dart';
import 'package:tataapps/BeforeLogin/page_login.dart';
import 'package:tataapps/src/CustomWidget.dart';

class MandiriPaymentPage extends StatefulWidget {
  final String id_jasa;
  final String id_paket_jasa;
  final String jenisPesanan;
  final String kelas_jasa;
  final String harga;
  final String revisi;
  final String deskripsi;
  final dynamic imageFile;
  final dynamic webImageBytes;
  final bool cetak;
  final String ukuran;
  final String bahan;
  final String jumlahCetak;

  const MandiriPaymentPage({
    Key? key,
    required this.id_jasa,
    required this.id_paket_jasa,
    required this.jenisPesanan,
    required this.kelas_jasa,
    required this.harga,
    required this.revisi,
    required this.deskripsi,
    required this.imageFile,
    required this.webImageBytes,
    required this.cetak,
    required this.ukuran,
    required this.bahan,
    required this.jumlahCetak,
  }) : super(key: key);

  @override
  State<MandiriPaymentPage> createState() => _MandiriPaymentPageState();
}

class _MandiriPaymentPageState extends State<MandiriPaymentPage> {
  String nomorPemesanan = "";
  bool isLoading = false;
  final AuthManager _authManager = AuthManager();

  String _generateKodePemesanan() {
    final now = DateTime.now();
    final year = now.year.toString().substring(2); // 2 digit tahun
    final month = now.month.toString().padLeft(2, '0');
    final day = now.day.toString().padLeft(2, '0');
    final hour = now.hour.toString().padLeft(2, '0');
    final minute = now.minute.toString().padLeft(2, '0');
    final second = now.second.toString().padLeft(2, '0');
    
    // Generate 4 digit random number
    final random = (1000 + now.millisecond) % 10000;
    final randomStr = random.toString().padLeft(4, '0');
    
    return 'TTA-$year$month$day-$hour$minute$second-$randomStr';
  }

  @override
  void initState() {
    super.initState();
    nomorPemesanan = _generateKodePemesanan();
  }

  void _showSessionExpiredDialog(BuildContext context) {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text("Sesi Berakhir"),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Icon(
                Icons.error_outline,
                color: Colors.red,
                size: 48,
              ),
              SizedBox(height: 16),
              Text(
                "Sesi login Anda telah berakhir. Silakan login kembali untuk melanjutkan.",
                textAlign: TextAlign.center,
              ),
            ],
          ),
          actions: [
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: CustomColors.primaryColor,
                foregroundColor: Colors.white,
              ),
              child: Text("Login Ulang"),
              onPressed: () {
                // Hapus token dan navigasi ke halaman login
                _authManager.logout().then((_) {
                  // Navigasi ke halaman login
                  Navigator.of(context).pushAndRemoveUntil(
                    MaterialPageRoute(builder: (context) => const page_login()),
                    (route) => false,
                  );
                });
              },
            ),
          ],
        );
      },
    );
  }

  Future<void> kirimPesanan() async {
    setState(() {
      isLoading = true;
    });

    try {
      String revisiClean = widget.revisi.replaceAll('x', '');
      int revisiValue = int.tryParse(revisiClean) ?? 1;
      
      // Batasi maksimal revisi tidak lebih dari 5
      if (revisiValue > 5) {
        revisiValue = 5;
      }
      
      // UUID metode pembayaran untuk Mandiri
      const String uuidMetodePembayaran = "0ce1048e-25d2-4fbd-a366-07b660231e2c";
      
      // Buat pesanan menggunakan PesananApi
      final result = await PesananApi.createPesananWithTransaction(
        idJasa: widget.id_jasa,
        idPaketJasa: widget.id_paket_jasa,
        catatanUser: widget.deskripsi,
        maksimalRevisi: revisiValue,
        idMetodePembayaran: uuidMetodePembayaran,
        gambarReferensi: widget.imageFile,
        webImageBytes: widget.webImageBytes,
      );
      
      setState(() {
        isLoading = false;
      });
      
      debugPrint("Create order result: $result");
      
      if (result['status'] == 'success') {
        // Buat chat room untuk pesanan ini
        final String pesananUuid = result['data']['id_pesanan'];
        await ChatApiService.createOrGetChatRoom(pesananUuid);
        
        // Tampilkan bukti pemesanan
        Navigator.push(
          context,
          SmoothPageTransition(
            page: BuktiPemesananPage(
              nomorPemesanan: pesananUuid,
              id_jasa: widget.id_jasa,
              id_paket_jasa: widget.id_paket_jasa,
              jenisPesanan: widget.jenisPesanan,
              kelas_jasa: widget.kelas_jasa,
              harga: widget.harga,
              revisi: widget.revisi,
              deskripsi: widget.deskripsi,
              imageFile: widget.imageFile,
              webImageBytes: widget.webImageBytes,
              cetak: widget.cetak,
              ukuran: widget.ukuran,
              bahan: widget.bahan,
              jumlahCetak: widget.jumlahCetak,
              uuidmetodePembayaran: uuidMetodePembayaran,
              metodePembayaran: "MANDIRI",
            ),
          ),
        );
      } else if (result['code'] == 401) {
        // Tampilkan dialog sesi berakhir kustom
        _showSessionExpiredDialog(context);
      } else {
        _showErrorDialog(result['message'] ?? 'Gagal membuat pesanan');
      }
    } catch (e) {
      setState(() {
        isLoading = false;
      });
      _showErrorDialog('Terjadi kesalahan: $e');
    }
  }

  void _showErrorDialog(String message) {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text("Error"),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Icon(
                Icons.error_outline,
                color: Colors.red,
                size: 48,
              ),
              SizedBox(height: 16),
              Text(
                message,
                textAlign: TextAlign.center,
              ),
            ],
          ),
          actions: [
            TextButton(
              child: Text("OK"),
              onPressed: () {
                Navigator.of(context).pop();
              },
            ),
          ],
        );
      },
    );
  }

  void _lanjutkan() {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text("KONFIRMASI"),
          content: Text("Apakah kamu sudah melakukan pembayaran?"),
          actions: [
            TextButton(
              child: Text("Belum"),
              onPressed: () => Navigator.of(context).pop(),
            ),
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: CustomColors.primaryColor,
                foregroundColor: Colors.white,
              ),
              child: Text("Sudah"),
              onPressed: () async {
                Navigator.of(context).pop(); // Tutup dialog
                await kirimPesanan(); // Kirim data ke API
              },
            ),
          ],
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: ListView(
        padding: EdgeInsets.zero,
        children: [
          Stack(
            children: [
              Container(height: 140, color: CustomColors.primaryColor),
              Image.asset(Server.UrlGambar("atributhome.png")),
              Positioned(
                top: 60,
                left: 20,
                child: GestureDetector(
                  onTap: () => Navigator.pop(context),
                  child: Icon(Icons.arrow_back, color: Colors.white),
                ),
              ),
              Positioned(
                top: 60,
                left: 70,
                child: Text(
                  'Pembayaran',
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ),
            ],
          ),
          Container(
            padding: EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Pembayaran via Bank Mandiri',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                SizedBox(height: 16),
                Container(
                  padding: EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(10),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.grey.withOpacity(0.2),
                        spreadRadius: 2,
                        blurRadius: 5,
                        offset: Offset(0, 3),
                      ),
                    ],
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Nomor Rekening',
                        style: TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      SizedBox(height: 8),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text(
                            '1234567890',
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          ElevatedButton(
                            onPressed: () {
                              // Copy to clipboard
                              ScaffoldMessenger.of(context).showSnackBar(
                                SnackBar(
                                  content: Text('Nomor rekening disalin'),
                                  duration: Duration(seconds: 1),
                                ),
                              );
                            },
                            style: ElevatedButton.styleFrom(
                              backgroundColor: CustomColors.primaryColor,
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(8),
                              ),
                            ),
                            child: Text(
                              'Salin',
                              style: TextStyle(color: Colors.white),
                            ),
                          ),
                        ],
                      ),
                      SizedBox(height: 16),
                      Text(
                        'Atas Nama',
                        style: TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      SizedBox(height: 8),
                      Text(
                        'PT. TATA DESIGN',
                        style: TextStyle(
                          fontSize: 16,
                        ),
                      ),
                      SizedBox(height: 16),
                      Text(
                        'Total Pembayaran',
                        style: TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      SizedBox(height: 8),
                      Text(
                        widget.harga,
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                          color: CustomColors.primaryColor,
                        ),
                      ),
                    ],
                  ),
                ),
                SizedBox(height: 24),
                Text(
                  'Cara Pembayaran',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                SizedBox(height: 16),
                _buildStep('1', 'Buka aplikasi m-Banking atau ATM Mandiri'),
                _buildStep('2', 'Pilih menu Transfer'),
                _buildStep('3', 'Masukkan nomor rekening tujuan: 1234567890'),
                _buildStep('4', 'Masukkan jumlah transfer sesuai total pembayaran'),
                _buildStep('5', 'Periksa kembali data transfer dan konfirmasi'),
                _buildStep('6', 'Simpan bukti transfer'),
                SizedBox(height: 32),
                Container(
                  padding: EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: Colors.blue.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Row(
                    children: [
                      Icon(
                        Icons.info_outline,
                        color: Colors.blue,
                      ),
                      SizedBox(width: 16),
                      Expanded(
                        child: Text(
                          'Setelah melakukan pembayaran, klik tombol "Lanjut" untuk melanjutkan proses pemesanan.',
                          style: TextStyle(
                            color: Colors.blue,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                SizedBox(height: 24),
                ElevatedButton(
                  onPressed: isLoading ? null : _lanjutkan,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: CustomColors.primaryColor,
                    minimumSize: Size(double.infinity, 50),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(10),
                    ),
                  ),
                  child: isLoading
                      ? CircularProgressIndicator(color: Colors.white)
                      : Text(
                          'Lanjut',
                          style: TextStyle(
                            color: Colors.white,
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStep(String number, String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 16),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 24,
            height: 24,
            decoration: BoxDecoration(
              color: CustomColors.primaryColor,
              shape: BoxShape.circle,
            ),
            child: Center(
              child: Text(
                number,
                style: TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
          ),
          SizedBox(width: 16),
          Expanded(
            child: Text(
              text,
              style: TextStyle(fontSize: 16),
            ),
          ),
        ],
      ),
    );
  }
}

// Helper function untuk min
int min(int a, int b) {
  return a < b ? a : b;
}

