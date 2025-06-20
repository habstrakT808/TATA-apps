import 'package:TATA/helper/user_preferences.dart';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

import 'package:TATA/main.dart';
import 'package:TATA/src/pageTransition.dart';
import 'package:TATA/menu/JasaDesign/DeskripsiPesanan/DeskripsiLogo.dart';
import 'package:TATA/src/CustomColors.dart';
import 'package:TATA/sendApi/Server.dart';

class JasaDesignLogo extends StatefulWidget {
  const JasaDesignLogo({super.key});

  @override
  _LogoPackagePageState createState() => _LogoPackagePageState();
}

class _LogoPackagePageState extends State<JasaDesignLogo>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;

  List<Map<String, dynamic>> packages = [];
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    fetchPackages();
  }

  int _convertToDays(String duration) {
    // Jika format durasi misalnya "3 hari", kita ambil angkanya
    final match = RegExp(r'(\d+)').firstMatch(duration);
    if (match != null) {
      return int.parse(match.group(1)!);
    }
    return 0;
  }

  Future<void> fetchPackages() async {
    try {
      // Gunakan TokenJwt.getToken() untuk mendapatkan token yang valid
      final token = await UserPreferences.getUser()?.then((user) => user?['access_token']);
      
      final Map<String, String> headers = {
        'Accept': 'application/json',
      };
      
      // Tambahkan token ke header jika tersedia
      if (token != null) {
        headers['Authorization'] = 'Bearer $token';
      }
      
      final response = await http.get(
        Server.urlLaravel('jasa/1'),
        headers: headers,
      ).timeout(
        const Duration(seconds: 20),
        onTimeout: () {
          print('TIMEOUT: Jasa request timed out');
          return http.Response('{"message":"timeout"}', 408);
        },
      );

      print('RESPONSE STATUS: ${response.statusCode}');
      print('RESPONSE BODY: ${response.body}');

      if (response.statusCode == 200) {
        final jsonData = json.decode(response.body);

        if (jsonData['status'] == 'success') {
          // Ambil data jasa dan paket_jasa dari respons
          final jasa = jsonData['data']['jasa'];
          List<dynamic> data = jsonData['data']['paket_jasa'];
          
          // Jika paket_jasa kosong, gunakan data dummy
          if (data == null || data.isEmpty) {
            print('WARNING: Paket jasa kosong, menggunakan data dummy');
            setDummyPackages();
            return;
          }

          setState(() {
            packages = data.map<Map<String, dynamic>>((item) {
              final kelas = item["kelas_jasa"] ?? "-";
              final waktu = item["waktu_pengerjaan"] ?? "0";
              final maksimalRevisi = item["maksimal_revisi"] ?? 1;
              
              return {
                "id_paket_jasa": item["id_paket_jasa"].toString(),
                "id_jasa": item["id_jasa"].toString(),
                "jenis_pesanan": jasa["kategori"] ?? "-",
                "title": kelas,
                "price": "Rp ${item["harga_paket_jasa"] ?? "0"}",
                "duration": waktu ?? "0 hari",
                "revision": "${maksimalRevisi}x",
                "konsep": "2 Konsep",
                "logo_transparan": true,
                "konsep_simple": kelas == "basic",
                "konsep_premium": kelas == "standard" || kelas == "premium",
                "konsep_3d": kelas == "premium",
              };
            }).toList();

            isLoading = false;
          });
        } else {
          print("API returned error: ${jsonData['message']}");
          setDummyPackages();
        }
      } else if (response.statusCode == 401) {
        // Error unauthorized - token tidak valid atau tidak ditemukan
        print("Unauthorized: ${response.body}");
        setDummyPackages(); // Gunakan data dummy
        
        // Tampilkan notifikasi jika perlu
        // ScaffoldMessenger.of(context).showSnackBar(
        //   SnackBar(content: Text('Sesi berakhir. Silakan login kembali.')),
        // );
      } else {
        print("Failed to fetch data, status code: ${response.statusCode}");
        setDummyPackages();
      }
    } catch (e) {
      print("Error fetching packages: $e");
      setDummyPackages();
    }
  }
  
  // Metode untuk mengatur data dummy
  void setDummyPackages() {
    setState(() {
      packages = [
        {
          "id_paket_jasa": "1",
          "id_jasa": "1",
          "jenis_pesanan": "Logo",
          "title": "basic",
          "price": "Rp 100000",
          "duration": "3 hari",
          "revision": "2x",
          "konsep": "2 Konsep",
          "logo_transparan": true,
          "konsep_simple": true,
          "konsep_premium": false,
          "konsep_3d": false,
        },
        {
          "id_paket_jasa": "2",
          "id_jasa": "1",
          "jenis_pesanan": "Logo",
          "title": "standard",
          "price": "Rp 250000",
          "duration": "5 hari",
          "revision": "5x",
          "konsep": "2 Konsep", 
          "logo_transparan": true,
          "konsep_simple": false,
          "konsep_premium": true,
          "konsep_3d": false,
        },
        {
          "id_paket_jasa": "3",
          "id_jasa": "1",
          "jenis_pesanan": "Logo",
          "title": "premium",
          "price": "Rp 500000",
          "duration": "7 hari",
          "revision": "10x",
          "konsep": "2 Konsep",
          "logo_transparan": true,
          "konsep_simple": false,
          "konsep_premium": true,
          "konsep_3d": true,
        }
      ];
      isLoading = false;
    });
  }

  void _pesanSekarang() {
    final selectedPackage = packages[_tabController.index];

    Navigator.push(
      context,
      SmoothPageTransition(
        page: DeskripsiLogo(paket: selectedPackage),
      ),
    );
    print(selectedPackage);
  }

  Widget _buildCheck(bool isIncluded) {
    return isIncluded
        ? Icon(Icons.check, color: Colors.black, size: 25)
        : Icon(Icons.close, color: Colors.grey, size: 25);
  }

  @override
  Widget build(BuildContext context) {
    if (isLoading || packages.isEmpty) {
      return Scaffold(
        backgroundColor: CustomColors.whiteColor,
        body: Center(child: CircularProgressIndicator()),
      );
    }

    final selected = packages[_tabController.index];

    return Scaffold(
      backgroundColor: CustomColors.whiteColor,
      body: Column(
        children: [
          Container(
            color: CustomColors.primaryColor,
            child: Stack(
              children: [
                Positioned(
                  top: 0,
                  child: Image.asset(Server.UrlGambar("atributhome.png")),
                ),
                Container(
                  padding: EdgeInsets.only(top: 30),
                  child: Column(
                    children: [
                      Align(
                        alignment: Alignment.topLeft,
                        child: BackButton(color: CustomColors.whiteColor),
                      ),
                      Padding(
                        padding: const EdgeInsets.all(15.0),
                        child: Center(
                          child: Image.asset(
                            Server.UrlGambar('designlogo.png'),
                            width: double.infinity,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          Expanded(
            child: Stack(
              children: [
                Positioned(
                  bottom: 0,
                  right: 0,
                  child: Image.asset(
                    Server.UrlGambar("atributhomebigcircle.png"),
                    scale: 1,
                  ),
                ),
                SingleChildScrollView(
                  padding: EdgeInsets.only(bottom: 100),
                  child: Column(
                    children: [
                      Align(
                        alignment: Alignment.centerLeft,
                        child: Padding(
                          padding: const EdgeInsets.fromLTRB(10, 10, 10, 5),
                          child: Text(
                            "Desain Logo",
                            style: TextStyle(
                                fontSize: 14, fontWeight: FontWeight.bold),
                          ),
                        ),
                      ),
                      TabBar(
                        controller: _tabController,
                        labelColor: CustomColors.primaryColor,
                        unselectedLabelColor: CustomColors.HintColor,
                        indicatorColor: CustomColors.greensoft,
                        tabs: [
                          Tab(text: 'Basic'),
                          Tab(text: 'Standard'),
                          Tab(text: 'Premium'),
                        ],
                        onTap: (_) {
                          setState(() {});
                        },
                      ),
                      Padding(
                        padding: const EdgeInsets.all(16.0),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              selected['price'],
                              style: TextStyle(
                                  fontSize: 22, fontWeight: FontWeight.bold),
                            ),
                            SizedBox(height: 2),
                            Text(
                              "${selected['konsep']} konsep & PNG Transparan",
                              style: TextStyle(
                                  fontSize: 14, fontWeight: FontWeight.bold),
                            ),
                            SizedBox(height: 16),
                            ...[
                              ["Waktu Pengerjaan", selected["duration"]],
                              ["Revisi", selected["revision"]],
                              ["Konsep", selected["konsep"]],
                            ].map((e) => Padding(
                                  padding:
                                      const EdgeInsets.symmetric(vertical: 4),
                                  child: Row(
                                    mainAxisAlignment:
                                        MainAxisAlignment.spaceBetween,
                                    children: [
                                      Text(e[0]),
                                      Text(e[1]),
                                    ],
                                  ),
                                )),
                            ...[
                              ["Logo Transparan", selected["logo_transparan"]],
                              ["Konsep Simple", selected["konsep_simple"]],
                              ["Konsep Premium", selected["konsep_premium"]],
                              ["3D Konsep", selected["konsep_3d"]],
                            ].map((e) => Padding(
                                  padding:
                                      const EdgeInsets.symmetric(vertical: 1),
                                  child: Row(
                                    mainAxisAlignment:
                                        MainAxisAlignment.spaceBetween,
                                    children: [
                                      Text(e[0]),
                                      _buildCheck(e[1]),
                                    ],
                                  ),
                                )),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
                Positioned(
                  bottom: 20,
                  left: 20,
                  right: 20,
                  child: Row(
                    children: [
                      SizedBox(
                        width: 60,
                        height: 60,
                        child: Card(
                            color: CustomColors.whiteColor,
                            elevation: 5,
                            child: IconButton(
                                onPressed: () {
                                  Navigator.pushAndRemoveUntil(
                                    context,
                                    MaterialPageRoute(
                                        builder: (_) =>
                                            MainPage(initialIndex: 2)),
                                    (route) => false,
                                  );
                                },
                                icon: Image.asset(
                                    Server.UrlGambar("chaticon.png")))),
                      ),
                      SizedBox(width: 10),
                      Expanded(
                        child: ElevatedButton(
                          onPressed: _pesanSekarang,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: CustomColors.primaryColor,
                            minimumSize: Size(double.infinity, 50),
                            shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(10)),
                          ),
                          child: Text("Pesan Sekarang",
                              style: TextStyle(color: CustomColors.whiteColor)),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
