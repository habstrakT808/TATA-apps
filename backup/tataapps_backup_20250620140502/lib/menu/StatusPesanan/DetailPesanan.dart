import 'dart:convert';
import 'dart:io';
import 'dart:math';
import 'dart:typed_data';
import 'package:TATA/main.dart';
import 'package:TATA/menu/StatusPesanan/RatingPage.dart';
import 'package:TATA/sendApi/tokenJWT.dart';
import 'package:TATA/src/pageTransition.dart';
import 'package:http/http.dart' as http;

import 'package:TATA/helper/user_preferences.dart';
import 'package:TATA/sendApi/Server.dart';
import 'package:TATA/src/CustomColors.dart';
import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:TATA/menu/ChatPage.dart';
import 'package:TATA/menu/ChatDetailScreen.dart';
import 'package:TATA/services/ChatService.dart';
// import 'package:image_gallery_saver/image_gallery_saver.dart';
import 'package:permission_handler/permission_handler.dart';
import 'package:path_provider/path_provider.dart';

class OrderDetailPage extends StatefulWidget {
  final String orderId;
  final String productTitle;
  final String productSubtitle;
  final String status;
  final int price;
  final String description;
  final String imageUrl;
  final String createdAt;
  final String? imageHasil;
  final String? estimateDate;
  final String? tanggalselesai;
  final int? remainingRevisions;

  const OrderDetailPage({
    super.key,
    required this.orderId,
    required this.productTitle,
    required this.productSubtitle,
    required this.status,
    required this.price,
    required this.description,
    required this.imageUrl,
    required this.createdAt,
    this.tanggalselesai,
    this.imageHasil,
    this.estimateDate,
    this.remainingRevisions,
  });

  @override
  State<OrderDetailPage> createState() => _OrderDetailPageState();
}

class _OrderDetailPageState extends State<OrderDetailPage> {
  Map<String, dynamic>? orderDetail;
  final ChatService _chatService = ChatService();
  bool _isCreatingChat = false;

  @override
  void initState() {
    super.initState();
    // Buat data lokal berdasarkan parameter widget
    orderDetail = {
      'image_hasil': widget.imageHasil,
      'status': widget.status,
      'total_harga': widget.price,
      'tanggal_selesai': widget.tanggalselesai,
      'estimate_date': widget.estimateDate,
    };
    
    checkIfMoreThan24Hours();
  }

  final bool visibility = true;
  void checkIfMoreThan24Hours() {
    if (widget.tanggalselesai != null) {
      final DateTime selesai = DateTime.parse(widget.tanggalselesai.toString())
          .toLocal();

      final DateTime batasWaktu = selesai.add(Duration(hours: 24));
      final DateTime sekarang = DateTime.now();

      if (sekarang.isAfter(batasWaktu)) {
        print("sudah habis batas waktu.");
      } else {
        print("Masih dalam batas waktu.");
      }
    }
  }

  Future<void> downloadImage(BuildContext context, String imageUrl) async {
    try {
      // Minta izin akses penyimpanan
      var status = await Permission.storage.request();
      if (!status.isGranted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Izin penyimpanan ditolak')),
        );
        return;
      }

      // Dapatkan direktori untuk menyimpan file
      Directory directory;
      if (Platform.isAndroid) {
        directory = Directory('/storage/emulated/0/Pictures');
      } else {
        directory = await getApplicationDocumentsDirectory();
      }

      // Nama file dari URL
      String fileName = imageUrl.split('/').last;

      String savePath = '${directory.path}/$fileName';

      // Unduh file
      Dio dio = Dio();
      await dio.download(imageUrl, savePath);

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            'Gambar berhasil disimpan',
          ),
          backgroundColor: CustomColors.primaryColor,
        ),
      );
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Gagal mengunduh gambar: $e')),
      );
    }
  }

  Color getStatusColor(String status) {
    switch (status.toLowerCase()) {
      case 'menunggu':
        return Colors.green;
      case 'diproses':
        return Colors.orange;
      case 'dikerjakan':
        return Colors.blue;
      case 'selesai':
        return Colors.teal;
      case 'batal':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  Widget getImageByKategori(String kategori) {
    kategori = kategori.toLowerCase();
    if (kategori.contains("logo")) {
      return Image.asset(
        "assets/images/designlogo.png",
        width: 70,
        height: 70,
        fit: BoxFit.cover,
      );
    } else if (kategori.contains("banner")) {
      return Image.asset(
        "assets/images/designbanner.png",
        width: 70,
        height: 70,
        fit: BoxFit.cover,
      );
    } else if (kategori.contains("poster")) {
      return Image.asset(
        "assets/images/designposter.png",
        width: 70,
        height: 70,
        fit: BoxFit.cover,
      );
    } else {
      return Icon(Icons.insert_drive_file, color: Colors.grey, size: 40);
    }
  }

  Future<void> batalkanPesanan(BuildContext context) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('Konfirmasi Pembatalan'),
        content: Text('Apakah kamu yakin ingin membatalkan pesanan ini?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(false),
            child:
                Text('Batal', style: TextStyle(color: CustomColors.blackColor)),
          ),
          ElevatedButton(
            onPressed: () => Navigator.of(context).pop(true),
            style: ElevatedButton.styleFrom(
              backgroundColor: CustomColors.redsoft,
            ),
            child: Text(
              'Ya, Batalkan',
              style: TextStyle(color: CustomColors.redColor),
            ),
          ),
        ],
      ),
    );

    if (confirm == true) {
      try {
        // Gunakan TokenJwt.getToken() untuk mendapatkan token yang valid
        final token = await TokenJwt.getToken();
        
        if (token == null) {
          print("Error: Token tidak ditemukan atau gagal di-refresh");
          TokenJwt.showSessionExpiredDialog(context);
          return;
        }

        final response = await http.post(
          Server.urlLaravel('mobile/pesanan/cancel/${widget.orderId}'),
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': 'Bearer $token',
          },
        );

        if (response.statusCode == 200) {
          final result = json.decode(response.body);
          if (result['status'] == 'success') {
            setState(() {
              orderDetail?['status'] = 'Batal';
            });
            
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text('Pesanan berhasil dibatalkan'),
                backgroundColor: CustomColors.primaryColor,
              ),
            );
            
            Navigator.pushReplacement(context, MaterialPageRoute(builder: (context) => MainPage()));
          } else {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(content: Text('Gagal membatalkan pesanan: ${result['message']}')),
            );
          }
        } else if (response.statusCode == 401) {
          // Token tidak valid, tampilkan dialog login
          TokenJwt.showSessionExpiredDialog(context);
        } else {
          final result = json.decode(response.body);
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text('Gagal membatalkan pesanan: ${result['message'] ?? "Terjadi kesalahan"}')),
          );
        }
      } catch (e) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Terjadi kesalahan: $e')),
        );
      }
    }
  }

  // Metode untuk membuat obrolan dengan admin
  Future<void> createChatWithAdmin() async {
    if (_isCreatingChat) return; // Hindari klik ganda
    setState(() => _isCreatingChat = true);

    try {
      // Gunakan TokenJwt.getToken() untuk mendapatkan token yang valid
      final token = await TokenJwt.getToken();
      
      if (token == null) {
        print("Error: Token tidak ditemukan atau gagal di-refresh");
        TokenJwt.showSessionExpiredDialog(context);
        setState(() => _isCreatingChat = false);
        return;
      }

      final userData = await UserPreferences.getUser();
      if (userData == null) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Informasi pengguna tidak ditemukan')),
        );
        setState(() => _isCreatingChat = false);
        return;
      }

      String? userId = userData['user']?['id'];
      if (userId == null) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('ID pengguna tidak ditemukan')),
        );
        setState(() => _isCreatingChat = false);
        return;
      }

      // Dapatkan atau buat chat untuk pesanan ini menggunakan getOrCreateChatForOrder
      final String orderIdClean = widget.orderId.replaceAll("#", "");
      final chatId = await _chatService.getOrCreateChatForOrder(userId, orderIdClean);
      
      if (chatId.isNotEmpty) {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => ChatDetailScreen(
              chatId: chatId,
            ),
          ),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Gagal membuat obrolan dengan admin')),
        );
      }
    } catch (e) {
      print('Error creating chat: $e');
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Terjadi kesalahan saat membuat obrolan')),
      );
    } finally {
      setState(() => _isCreatingChat = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final String imageName = widget.imageHasil ?? "";
    final String imageUrl = imageName.isNotEmpty
        ? Server.UrlImageReferensi(widget.orderId, imageName)
        : "";

    return WillPopScope(
      onWillPop: () async {
        Navigator.pop(context);
        return false;
      },
      child: Scaffold(
        backgroundColor: CustomColors.whiteColor,
        body: Column(
          children: [
            Stack(
              children: [
                Container(
                  height: 140,
                  decoration: BoxDecoration(color: CustomColors.primaryColor),
                ),
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
                    'Detail Pesanan',
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ],
            ),
            Expanded(
              child: SingleChildScrollView(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      children: [
                        Container(
                          decoration: BoxDecoration(
                            color: CustomColors.whiteColor,
                            borderRadius: BorderRadius.circular(16),
                            boxShadow: [
                              BoxShadow(
                                color: Colors.black12,
                                blurRadius: 6,
                                offset: Offset(0, 2),
                              ),
                            ],
                          ),
                          padding: const EdgeInsets.all(16),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                children: [
                                  ClipRRect(
                                    borderRadius: BorderRadius.circular(8),
                                    child: SizedBox(
                                      width: 70,
                                      height: 70,
                                      child: getImageByKategori(widget.productTitle),
                                    ),
                                  ),
                                  const SizedBox(width: 14),
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment:
                                          CrossAxisAlignment.start,
                                      children: [
                                        Text(
                                          widget.productTitle,
                                          style: TextStyle(
                                            fontSize: 16,
                                            fontWeight: FontWeight.bold,
                                          ),
                                        ),
                                        Text(
                                          widget.productSubtitle,
                                          style: TextStyle(
                                              color: Colors.grey[600]),
                                        ),
                                        Text(
                                          'Rp ${widget.price.toString()}',
                                          style: TextStyle(
                                            fontSize: 16,
                                            fontWeight: FontWeight.bold,
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                  Text(
                                    widget.status,
                                    style: TextStyle(
                                      color: getStatusColor(widget.status),
                                      fontWeight: FontWeight.bold,
                                    ),
                                  )
                                ],
                              ),
                              const SizedBox(height: 16),
                              Text(
                                'Deskripsi :',
                                style: TextStyle(fontWeight: FontWeight.bold),
                              ),
                              const SizedBox(height: 8),
                              Text(
                                widget.description,
                                style: TextStyle(fontSize: 14),
                              ),
                              const SizedBox(height: 12),
                              if (widget.imageUrl.isNotEmpty)
                                ClipRRect(
                                  borderRadius: BorderRadius.circular(12),
                                  child: Image.network(
                                    Server.UrlImageReferensi(
                                        widget.orderId, widget.imageUrl),
                                    height: 140,
                                    width: double.infinity,
                                    fit: BoxFit.cover,
                                    errorBuilder: (context, error, stackTrace) {
                                      print("Error loading image: $error");
                                      return Container(
                                        height: 140,
                                        width: double.infinity,
                                        color: Colors.grey[300],
                                        child: Column(
                                          mainAxisAlignment: MainAxisAlignment.center,
                                          children: [
                                            Icon(Icons.broken_image, size: 40),
                                            SizedBox(height: 8),
                                            Text('Gambar tidak tersedia', 
                                              style: TextStyle(color: Colors.grey[700]),
                                            ),
                                          ],
                                        ),
                                      );
                                    },
                                  ),
                                ),
                            ],
                          ),
                        ),
                        const SizedBox(height: 16),
                        Builder(
                          builder: (_) {
                            switch (widget.status.toLowerCase()) {
                              case 'menunggu':
                                return Row(
                                  mainAxisAlignment:
                                      MainAxisAlignment.spaceBetween,
                                  children: [
                                    ElevatedButton(
                                      onPressed: () {},
                                      style: ElevatedButton.styleFrom(
                                        backgroundColor:
                                            CustomColors.blacksoft,
                                        shape: RoundedRectangleBorder(
                                          borderRadius:
                                              BorderRadius.circular(8),
                                        ),
                                      ),
                                      child: Text('Menunggu Konfirmasi',
                                          style: TextStyle(
                                              color:
                                                  CustomColors.whiteColor)),
                                    ),
                                    ElevatedButton(
                                      onPressed: () =>
                                          batalkanPesanan(context),
                                      style: ElevatedButton.styleFrom(
                                        backgroundColor:
                                            CustomColors.redsoft,
                                        shape: RoundedRectangleBorder(
                                          borderRadius:
                                              BorderRadius.circular(8),
                                        ),
                                      ),
                                      child: Text(
                                        'Batalkan Pesanan',
                                        style: TextStyle(
                                          fontWeight: FontWeight.bold,
                                          color: CustomColors.redColor,
                                        ),
                                      ),
                                    ),
                                  ],
                                );
                              case 'diproses':
                                return Row(
                                  children: [
                                    Align(
                                      alignment: Alignment.centerLeft,
                                      child: Container(
                                        padding: EdgeInsets.symmetric(
                                            horizontal: 12, vertical: 6),
                                        decoration: BoxDecoration(
                                          color: Colors.orange[100],
                                          borderRadius:
                                              BorderRadius.circular(8),
                                        ),
                                        child: Text(
                                          'Menunggu Tim Pengerjaan',
                                          style: TextStyle(
                                              color: Colors.orange[900]),
                                        ),
                                      ),
                                    ),
                                  ],
                                );
                              case 'dikerjakan':
                                return Row(
                                  mainAxisAlignment:
                                      MainAxisAlignment.spaceBetween,
                                  children: [
                                    ElevatedButton(
                                      onPressed: null,
                                      style: ElevatedButton.styleFrom(
                                        backgroundColor: Colors.green,
                                        shape: RoundedRectangleBorder(
                                          borderRadius:
                                              BorderRadius.circular(8),
                                        ),
                                      ),
                                      child:
                                          Text('Dalam Proses Pengerjaan'),
                                    ),
                                    if (widget.estimateDate != null)
                                      Container(
                                        padding: EdgeInsets.symmetric(
                                            horizontal: 12, vertical: 6),
                                        decoration: BoxDecoration(
                                          color: Colors.red[100],
                                          borderRadius:
                                              BorderRadius.circular(8),
                                        ),
                                        child: Text(
                                          'Estimasi : ${widget.estimateDate}',
                                          style: TextStyle(
                                              color: Colors.red[800]),
                                        ),
                                      ),
                                  ],
                                );
                              default:
                                return SizedBox();
                            }
                          },
                        ),
                      ],
                    ),
                  ),
            ),
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                boxShadow: [
                  BoxShadow(
                    color: Colors.black12,
                    blurRadius: 4,
                    offset: Offset(0, -2),
                  ),
                ],
              ),
              child: Row(
                children: [
                  Expanded(
                    child: ElevatedButton.icon(
                      onPressed: _isCreatingChat ? null : createChatWithAdmin,
                      icon: _isCreatingChat 
                        ? SizedBox(
                            width: 20,
                            height: 20,
                            child: CircularProgressIndicator(
                              color: CustomColors.whiteColor,
                              strokeWidth: 2,
                            ),
                          )
                        : Icon(
                            Icons.chat,
                            color: CustomColors.whiteColor,
                          ),
                      label: Text(_isCreatingChat ? 'Memuat...' : 'Chat Dengan Admin',
                          style: TextStyle(color: CustomColors.whiteColor)),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: CustomColors.primaryColor,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(8),
                        ),
                        minimumSize: Size(double.infinity, 48),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
