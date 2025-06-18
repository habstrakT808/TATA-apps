// File: lib/pages/pembayaran/BNIPaymentPage.dart
import 'dart:convert';
import 'dart:io';
import 'dart:math';
import 'dart:typed_data';
import 'package:TATA/helper/user_preferences.dart';
import 'package:TATA/menu/JasaDesign/DeskripsiPesanan/PembayaranSection/BuktiPemesanan/BuktiPemesanan.dart';
import 'package:TATA/sendApi/Server.dart';
import 'package:TATA/src/CustomColors.dart';
import 'package:TATA/src/pageTransition.dart';
import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:http_parser/http_parser.dart';

class BNIPaymentPage extends StatefulWidget {
  final String id_jasa;
  final String harga;
  final String jenisPesanan;
  final String kelas_jasa;
  final String revisi;
  final String deskripsi;
  final String id_paket_jasa;
  final File? imageFile;
  final Uint8List? webImageBytes;
  final bool? cetak;
  final String? ukuran;
  final String? bahan;
  final int? jumlahCetak;

  const BNIPaymentPage({
    super.key,
    required this.id_jasa,
    required this.harga,
    required this.jenisPesanan,
    required this.kelas_jasa,
    required this.revisi,
    required this.deskripsi,
    required this.id_paket_jasa,
    this.imageFile,
    this.webImageBytes,
    this.cetak,
    this.ukuran,
    this.bahan,
    this.jumlahCetak,
  });

  @override
  State<BNIPaymentPage> createState() => _BNIPaymentPageState();
}

class _BNIPaymentPageState extends State<BNIPaymentPage> {
  late String nomorPemesanan;

  @override
  void initState() {
    super.initState();
    nomorPemesanan = _generateKodePemesanan();
  }

  String _generateKodePemesanan() {
    final rand = Random();
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    return List.generate(8, (_) => chars[rand.nextInt(chars.length)]).join();
  }

  Future<void> kirimPesanan() async {
    final uri = Server.urlLaravel('pesanan/create-with-transaction');
    final request = http.MultipartRequest('POST', uri);
    final data = await UserPreferences.getUser();

    request.headers['Authorization'] = 'Bearer ${data!['access_token']}';
    request.headers['Accept'] = 'application/json';

    // 🔁 Step 1: Mapping jenisPesanan ke ID Jasa
    String jasaId = widget.id_jasa;

    // 🔁 Step 2: Mapping kombinasi jasaId + paket → id_paket_jasa (1–9)

    String paketId = widget.id_paket_jasa;
    String revisiClean = widget.revisi.replaceAll('x', '');

    // 🔁 Step 3: Kirim field ke backend
    request.fields['id_jasa'] = jasaId;
    request.fields['id_paket_jasa'] = paketId;
    request.fields['catatan_user'] = widget.deskripsi;
    request.fields['maksimal_revisi'] = int.parse(revisiClean).toString();
    request.fields['id_metode_pembayaran'] =
        "9712fbe3-b51e-4b7e-95e6-33566021ed3b"; // UUID untuk BNI
    request.fields['catatan_user'] = widget.deskripsi;

    // 🔁 Step 4: Upload gambar (jika ada)
    if (kIsWeb && widget.webImageBytes != null) {
      // Untuk web, gunakan bytes langsung
      final image = http.MultipartFile.fromBytes(
        'gambar_referensi',
        widget.webImageBytes!,
        filename: 'web_image.jpg',
        contentType: MediaType('image', 'jpeg'),
      );
      request.files.add(image);
    } else if (!kIsWeb && widget.imageFile != null && widget.imageFile!.existsSync()) {
      // Untuk mobile
      final image = await http.MultipartFile.fromPath(
        'gambar_referensi',
        widget.imageFile!.path,
        contentType: MediaType('image', 'jpeg'),
      );
      request.files.add(image);
    }

    try {
      final response = await request.send();
      final resString = await response.stream.bytesToString();
      final resJson = jsonDecode(resString);

      if (response.statusCode == 201) {
        print("Pesanan berhasil: ${resJson['data']['id_pesanan']}");

        Navigator.push(
          context,
          SmoothPageTransition(
            page: BuktiPemesananPage(
              nomorPemesanan: nomorPemesanan,
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
              uuidmetodePembayaran: "9712fbe3-b51e-4b7e-95e6-33566021ed3b",
              metodePembayaran: "BNI",
            ),
          ),
        );
      } else {
        print("Gagal membuat pesanan: ${resJson['message']}");
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
              content: Text('Gagal membuat pesanan: ${resJson['message']}')),
        );
      }
    } catch (e) {
      print("Exception saat kirim pesanan: $e");
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Terjadi kesalahan: $e')),
      );
    }
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
          Padding(
            padding: const EdgeInsets.all(10),
            child: Text(
              'Rekening Bank BNI',
              style: TextStyle(fontWeight: FontWeight.w400, fontSize: 18),
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(10),
            child: _buildRekeningInfo(),
          ),
          Padding(
            padding: const EdgeInsets.all(15),
            child: _buildInstructionSection(
              "Tatacara Pembayaran dari BNI Mobile",
              [
                'Buka aplikasi BNI Mobile Banking dan login.',
                'Pilih menu "Transfer" → "Antar Rekening BNI".',
                'Masukkan Nomor Rekening Tujuan yang diberikan. \n→ Contoh: 0843894839843 (atau nomor rekening yang ditampilkan di aplikasi).',
                'Masukkan jumlah pembayaran sesuai tagihan.',
                'Periksa kembali detail penerima (nama rekening) dan nominal, pastikan sudah benar.',
                'Konfirmasi transaksi dan masukkan MPIN untuk menyelesaikan pembayaran.',
                'Simpan bukti transfer sebagai referensi.',
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(15),
            child: _buildInstructionSection(
              "Tatacara Pembayaran dari Bank Lain",
              [
                'Masuk ke Mobile Banking / Internet Banking / ATM bank lain yang kamu gunakan.',
                'Pilih menu "Transfer ke Bank Lain" (tewat jaringan ATM Bersama, PRIMA, atau ALTO).',
                'Masukkan Kode Bank BNI: 009.',
                'Masukkan Nomor Rekening Tujuan yang diberikan. \n→ Contoh: 0843894839843 (atau nomor rekening yang ditampilkan di aplikasi).',
                'Masukkan jumlah pembayaran sesuai tagihan.',
                'Periksa kembali data penerima (nama) dan jumlah pembayaran, pastikan sudah benar.',
                'Selesaikan transaksi sesuai instruksi.',
                'Simpan bukti transfer untuk konfirmasi pembayaran (jika diperlukan).',
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(15),
            child: _buildDeskripsiPembayaran(),
          ),
          SizedBox(height: 10),
          Padding(
            padding: const EdgeInsets.all(15),
            child: _buildLanjutButton(context),
          ),
        ],
      ),
    );
  }

  Widget _buildRekeningInfo() {
    return Container(
      padding: EdgeInsets.all(12),
      decoration: BoxDecoration(
        border: Border.all(color: Colors.grey.shade300),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Row(
        children: [
          Image.asset('assets/images/BankBNI.png',
              width: 36), // Pastikan gambar ini tersedia
          SizedBox(width: 12),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('Nomor Rekening',
                  style: TextStyle(fontSize: 12, color: Colors.grey[600])),
              SizedBox(height: 4),
              Text('0843894839843',
                  style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
            ],
          )
        ],
      ),
    );
  }

  Widget _buildInstructionSection(String title, List<String> steps) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Divider(thickness: 1),
        SizedBox(height: 8),
        Text(
          title,
          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
        ),
        SizedBox(height: 8),
        ...List.generate(steps.length, (index) {
          return Padding(
            padding: const EdgeInsets.symmetric(vertical: 4.0),
            child: Text("${index + 1}. ${steps[index]}",
                style: TextStyle(fontSize: 13)),
          );
        }),
      ],
    );
  }

  Widget _buildDeskripsiPembayaran() {
    return Column(
      children: [
        Container(
          alignment: Alignment.centerLeft,
          padding: EdgeInsets.all(12),
          decoration: BoxDecoration(
            border: Border.all(color: Colors.grey.shade300),
            borderRadius: BorderRadius.circular(6),
          ),
          child: Text("Deskripsi Pembayaran",
              style: TextStyle(fontWeight: FontWeight.bold)),
        ),
        Container(
          alignment: Alignment.center,
          padding: EdgeInsets.all(12),
          decoration: BoxDecoration(
            border: Border.all(color: Colors.grey.shade300),
            borderRadius: BorderRadius.circular(6),
          ),
          child: Table(
            columnWidths: {
              2: IntrinsicColumnWidth(),
              3: FlexColumnWidth(),
            },
            children: [
              TableRow(children: [
                Padding(
                  padding: EdgeInsets.symmetric(vertical: 4),
                  child: Text("Jasa",
                      style: TextStyle(fontWeight: FontWeight.bold)),
                ),
                Padding(
                  padding: EdgeInsets.symmetric(vertical: 4),
                  child: Text(": Design ${widget.jenisPesanan}"),
                ),
              ]),
              TableRow(children: [
                Padding(
                  padding: EdgeInsets.symmetric(vertical: 4),
                  child: Text("Paket",
                      style: TextStyle(fontWeight: FontWeight.bold)),
                ),
                Padding(
                  padding: EdgeInsets.symmetric(vertical: 4),
                  child: Text(": ${widget.kelas_jasa}"),
                ),
              ]),
              TableRow(children: [
                Padding(
                  padding: EdgeInsets.symmetric(vertical: 4),
                  child: Text("Total Harga",
                      style: TextStyle(fontWeight: FontWeight.bold)),
                ),
                Padding(
                  padding: EdgeInsets.symmetric(vertical: 4),
                  child: Text(": Rp. ${widget.harga}"),
                ),
              ]),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildLanjutButton(BuildContext context) {
    return ElevatedButton.icon(
      onPressed: _lanjutkan,
      icon: Icon(Icons.arrow_forward),
      label: Text("Lanjut"),
      style: ElevatedButton.styleFrom(
        backgroundColor: CustomColors.primaryColor,
        foregroundColor: Colors.white,
        minimumSize: Size(double.infinity, 48),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(6)),
      ),
    );
  }
}
