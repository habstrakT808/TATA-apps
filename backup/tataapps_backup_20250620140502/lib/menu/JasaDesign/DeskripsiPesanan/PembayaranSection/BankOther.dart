import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'dart:math';
import 'package:TATA/sendApi/Server.dart';
import 'package:TATA/sendApi/tokenJWT.dart';
import 'package:TATA/menu/StatusPesanan/DetailPesanan.dart';
import 'package:TATA/src/CustomColors.dart';
import 'package:TATA/src/pageTransition.dart';
import 'package:TATA/sendApi/PesananApi.dart';
import 'package:TATA/sendApi/AuthManager.dart';
import 'package:TATA/sendApi/ChatApiService.dart';

class BankOther extends StatefulWidget {
  final String jenisBank;
  final String id_jasa;
  final String id_paket_jasa;
  final String jenisPesanan;
  final String paket;
  final String harga;
  final String durasi;
  final String revisi;
  final String deskripsi;

  const BankOther({
    Key? key,
    required this.jenisBank,
    required this.id_jasa,
    required this.id_paket_jasa,
    required this.jenisPesanan,
    required this.paket,
    required this.harga,
    required this.durasi,
    required this.revisi,
    required this.deskripsi,
  }) : super(key: key);

  @override
  _BankOtherState createState() => _BankOtherState();
}

class _BankOtherState extends State<BankOther> {
  bool isLoading = false;
  final AuthManager _authManager = AuthManager();

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
      
      // UUID metode pembayaran untuk Bank Lainnya
      const String uuidMetodePembayaran = "5d2beb2e-3f22-423f-bc1e-46cc7a0da78c";
      
      // Buat pesanan menggunakan PesananApi
      final result = await PesananApi.createPesananWithTransaction(
        idJasa: widget.id_jasa,
        idPaketJasa: widget.id_paket_jasa,
        catatanUser: widget.deskripsi,
        maksimalRevisi: revisiValue,
        idMetodePembayaran: uuidMetodePembayaran,
        gambarReferensi: null, // Tidak ada gambar referensi untuk metode ini
        webImageBytes: null,
      );
      
      setState(() {
        isLoading = false;
      });
      
      debugPrint("Create order result: $result");
      
      if (result['status'] == 'success') {
        // Buat chat room untuk pesanan ini
        final String pesananUuid = result['data']['id_pesanan'];
        await ChatApiService.createOrGetChatRoom(pesananUuid);
        
        // Tampilkan halaman detail pesanan
        Navigator.pushReplacement(
          context,
          SmoothPageTransition(
            page: DetailPesanan(
              id_pesanan: pesananUuid,
              selectedIndex: 0,
            ),
          ),
        );
      } else if (result['code'] == 401) {
        _authManager.showSessionExpiredDialog(context);
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
      builder: (context) => AlertDialog(
        title: Text('Error'),
        content: Text(message),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text('OK'),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Bank ${widget.jenisBank}'),
        backgroundColor: CustomColors.primaryColor,
      ),
      body: SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.all(16.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'Tatacara Pembayaran dari Bank Lain',
                style: TextStyle(
                  fontSize: 18.0,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 16.0),
              _buildStepText(
                '1. Masuk ke Mobile Banking / Internet Banking / ATM bank lain yang kamu gunakan.',
              ),
              _buildStepText(
                '2. Pilih menu \'Transfer ke Bank Lain\' (gunakan jaringan ATM Bersama / Prima jika diperlukan).',
              ),
              _buildStepText(
                '3. Masukkan Kode Bank Mandiri: 008',
              ),
              _buildStepText(
                '4. Masukkan Nomor Rekening Tujuan yang diberikan.\n   → Contoh: 084389483984 (atau nomor rekening yang ditampilkan di aplikasi).',
              ),
              _buildStepText(
                '5. Masukkan jumlah pembayaran sesuai tagihan.',
              ),
              _buildStepText(
                '6. Periksa kembali data penerima (nama) dan jumlah pembayaran, pastikan sudah benar.',
              ),
              _buildStepText(
                '7. Selesaikan transaksi sesuai instruksi.',
              ),
              _buildStepText(
                '8. Setelah transfer berhasil, pembayaran akan otomatis diverifikasi atau ikuti petunjuk selanjutnya di aplikasi.',
              ),
              const SizedBox(height: 20),
              Card(
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Deskripsi Pembayaran',
                        style: TextStyle(
                          fontSize: 16.0,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 12.0),
                      _buildDetailRow('Jasa', ': ${widget.jenisPesanan}'),
                      _buildDetailRow('Paket', ': ${widget.paket}'),
                      _buildDetailRow('Total Harga', ': ${widget.harga}'),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 30.0),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: isLoading ? null : kirimPesanan,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: CustomColors.primaryColor,
                    padding: const EdgeInsets.symmetric(vertical: 15.0),
                  ),
                  child: isLoading
                      ? const CircularProgressIndicator(color: Colors.white)
                      : const Text(
                          'Lanjut',
                          style: TextStyle(
                            fontSize: 16.0,
                            color: Colors.white,
                          ),
                        ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildStepText(String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12.0),
      child: Text(
        text,
        style: const TextStyle(fontSize: 14.0),
      ),
    );
  }

  Widget _buildDetailRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8.0),
      child: Row(
        children: [
          Expanded(
            flex: 2,
            child: Text(
              label,
              style: const TextStyle(fontSize: 14.0),
            ),
          ),
          Expanded(
            flex: 3,
            child: Text(
              value,
              style: const TextStyle(fontSize: 14.0),
            ),
          ),
        ],
      ),
    );
  }
}

int min(int a, int b) {
  return a < b ? a : b;
} 