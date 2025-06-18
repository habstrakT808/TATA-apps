import 'dart:io';
import 'dart:typed_data';
import 'package:TATA/menu/JasaDesign/DeskripsiPesanan/PembayaranSection/BankBNI.dart';
import 'package:TATA/menu/JasaDesign/DeskripsiPesanan/PembayaranSection/BankMandiri.dart';
import 'package:TATA/menu/JasaDesign/DeskripsiPesanan/PembayaranSection/WalletOVO.dart';
import 'package:TATA/sendApi/Server.dart';
import 'package:TATA/src/CustomColors.dart';
import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter/material.dart';

class MetodePembayaranPage extends StatefulWidget {
  final String id_jasa;
  final String id_paket_jasa;
  final String harga;
  final String durasi;
  final String jenisPesanan;
  final String kelas_jasa;
  final String revisi;
  final String deskripsi;
  final File? imageFile;
  final Uint8List? webImageBytes;
  final bool? cetak;
  final String? ukuran;
  final String? bahan;
  final int? jumlahCetak;

  const MetodePembayaranPage({
    super.key,
    required this.id_jasa,
    required this.id_paket_jasa,
    required this.harga,
    required this.durasi,
    required this.jenisPesanan,
    required this.kelas_jasa,
    required this.revisi,
    required this.deskripsi,
    this.imageFile,
    this.webImageBytes,
    this.cetak,
    this.ukuran,
    this.bahan,
    this.jumlahCetak,
  });

  @override
  _MetodePembayaranPageState createState() => _MetodePembayaranPageState();
}

class _MetodePembayaranPageState extends State<MetodePembayaranPage> {
  int? selectedMethod;

  final List<Map<String, String>> metodePembayaran = [
    {"nama": "Rekening Mandiri", "gambar": "assets/images/BankMandiri.png"},
    {"nama": "Rekening BNI", "gambar": "assets/images/BankBNI.png"},
    {"nama": "OVO", "gambar": "assets/images/OVO.png"},
  ];

  void _prosesPembayaran() {
    if (selectedMethod == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text("Silakan pilih metode pembayaran terlebih dahulu."),
        ),
      );
      return;
    }

    Widget targetPage;
    switch (selectedMethod) {
      case 0:
        targetPage = MandiriPaymentPage(
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
        );
        break;
      case 1:
        targetPage = BNIPaymentPage(
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
        );
        break;
      case 2:
        targetPage = OVOPaymentPage(
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
        );
        break;
      default:
        return;
    }

    Navigator.push(
      context,
      MaterialPageRoute(builder: (context) => targetPage),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: CustomColors.whiteColor,
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
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 10),
            child: Text(
              "Metode Pembayaran untuk ${widget.jenisPesanan.toUpperCase()}",
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
                color: CustomColors.blackColor,
              ),
            ),
          ),
          SizedBox(height: 16),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16.0),
            child: Text(
              "Transfer Bank",
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.bold,
                color: CustomColors.blackColor,
              ),
            ),
          ),
          SizedBox(height: 8),
          ...List.generate(metodePembayaran.length, (index) {
            return Padding(
              padding:
                  const EdgeInsets.symmetric(horizontal: 16.0, vertical: 8.0),
              child: Container(
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(12),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.2),
                      blurRadius: 6,
                      offset: Offset(0, 3),
                    ),
                  ],
                ),
                child: ListTile(
                  contentPadding:
                      EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                  leading: Container(
                    width: 60,
                    height: 60,
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Padding(
                      padding: const EdgeInsets.all(6.0),
                      child: Image.asset(
                        metodePembayaran[index]['gambar']!,
                        fit: BoxFit.contain,
                      ),
                    ),
                  ),
                  title: Text(
                    metodePembayaran[index]['nama']!,
                    style: TextStyle(fontSize: 14, fontWeight: FontWeight.w500),
                  ),
                  trailing: selectedMethod == index
                      ? Icon(Icons.check_circle, color: Colors.green)
                      : null,
                  onTap: () {
                    setState(() {
                      selectedMethod = index;
                    });
                  },
                ),
              ),
            );
          }),
          SizedBox(height: 16),
        ],
      ),
      bottomNavigationBar: Padding(
        padding: const EdgeInsets.all(16.0),
        child: ElevatedButton.icon(
          onPressed: _prosesPembayaran,
          icon: Icon(Icons.arrow_forward),
          label: Text("Lanjut"),
          style: ElevatedButton.styleFrom(
            backgroundColor: CustomColors.primaryColor,
            foregroundColor: Colors.white,
            minimumSize: Size(double.infinity, 50),
            shape:
                RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
          ),
        ),
      ),
    );
  }
}
