// import 'package:TATA/SendApi/userApi.dart';
// import 'package:TATA/menu/UserPages/lupapassword2pages.dart';
// import 'package:TATA/src/customFormfield.dart';
import 'package:TATA/BeforeLogin/lupapassword2pages.dart';
import 'package:TATA/sendApi/Server.dart';
import 'package:TATA/sendApi/userApi.dart';
import 'package:TATA/src/CustomButton.dart';
import 'package:TATA/src/CustomText.dart';
import 'package:TATA/src/CustomWidget.dart';
import 'package:TATA/src/pageTransition.dart';
import 'package:flutter/material.dart';
import 'package:TATA/src/CustomColors.dart';
import 'package:flutter/services.dart';

class Lupapassword1 extends StatefulWidget {
  const Lupapassword1({super.key});

  @override
  _Lupapassword1State createState() => _Lupapassword1State();
}

class _Lupapassword1State extends State<Lupapassword1> {
  final TextEditingController _emailController = TextEditingController();
  final String _emailError = '';
  Future<void> _CekEmail() async {
    try {
      final result = await UserApi.CekEmail(_emailController.text);
      if (result != null) {
        if (result['status'] == "error") {
          print("Result : $result");
          print("Result : ${_emailController.text}");
          Navigator.push(
            context,
            SmoothPageTransition(
              page: LupaPassword2(email: _emailController.text),
            ),
          );
        } else if (result['status'] == "success") {
          print("Resultt : $result");
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text('Email Belum Terdaftar')),
          );
        } else {
          print("Resulttt : $result");
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
                content:
                    Text('Pendaftaran gagal: ada kesalahan pengiriman data')),
          );
        }
      } else {
        print("gagal : $result");
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
              content:
                  Text('Pendaftaran gagal: ada kesalahan pengiriman data')),
        );
      }
      setState(() {});
    } catch (e) {
      print(e);
    }
  }

  void _validateInputs() async {
    setState(() {
      if (_emailController.text.isEmpty) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Email tidak boleh kosong')),
        );
      } else if (_emailController.text.length <= 10) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Email tidak valid')),
        );
      } else {
        _CekEmail();
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: Stack(
          alignment: const Alignment(0, 0),
          fit: StackFit.expand,
          children: [
            Positioned(
              bottom: 0,
              left: 0,
              child: Image.asset(Server.UrlGambar("atributlogin.png")),
            ),
            Positioned(
              top: -200,
              width: 800,
              child:
                  Center(child: Image.asset(Server.UrlGambar("bgloginn.png"))),
            ),
            Positioned(
              top: -20,
              left: 5,
              right: 5,
              height: 200,
              child: Image.asset(Server.UrlGambar("logotext.png")),
            ),
            Positioned(
              height: 500,
              top: 210,
              left: 30,
              right: 30,
              child: Container(
                child: Card(
                  color: CustomColors.whiteColor,
                  shape: const RoundedRectangleBorder(
                    borderRadius: BorderRadius.only(
                        bottomLeft: Radius.circular(10),
                        bottomRight: Radius.circular(10),
                        topLeft: Radius.circular(10),
                        topRight: Radius.circular(10)),
                  ),
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 25),
                    child: SingleChildScrollView(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Container(
                            alignment: Alignment.topCenter,
                            child: Column(
                              children: [
                                const SizedBox(height: 25),
                                Center(
                                  child: Text('Email Pengguna',
                                      style: CustomText.TextArvoBold(
                                          22, CustomColors.blackColor)
                                      // TextStyle(
                                      //   fontSize: 24,
                                      //   fontWeight: FontWeight.bold,
                                      //   color: Colors.black,
                                      // ),
                                      ),
                                ),
                                const SizedBox(height: 20),
                                TextField(
                                  inputFormatters: [
                                    FilteringTextInputFormatter.allow(
                                        RegExp(r'[0-9@.a-zA-Z]')),
                                  ],
                                  controller: _emailController,
                                  keyboardType: TextInputType.emailAddress,
                                  textAlign: TextAlign.start,
                                  textInputAction: TextInputAction.next,
                                  decoration: InputDecoration(
                                    hintText: "Masukkan Email Anda",
                                    border: const OutlineInputBorder(
                                      borderRadius:
                                          BorderRadius.all(Radius.circular(10)),
                                    ),
                                    focusedBorder: OutlineInputBorder(
                                      borderRadius: const BorderRadius.all(
                                          Radius.circular(10)),
                                      borderSide: BorderSide(
                                        color: CustomColors
                                            .primaryColor, // Warna border saat aktif
                                      ),
                                    ),
                                    enabledBorder: OutlineInputBorder(
                                      borderRadius: const BorderRadius.all(
                                          Radius.circular(10)),
                                      borderSide: BorderSide(
                                        color: CustomColors
                                            .blackColor, // Warna border saat tidak aktif
                                      ),
                                    ),
                                    contentPadding: const EdgeInsets.symmetric(
                                        vertical: 0, horizontal: 10.0),
                                    hintStyle: CustomText.TextArvo(
                                        14, CustomColors.HintColor),
                                  ),
                                  style: CustomText.TextArvo(
                                    16,
                                    CustomColors.blackColor,
                                  ),
                                  onTap: () {},
                                ),
                              ],
                            ),
                          ),
                          const SizedBox(height: 130),
                          Container(
                            alignment: Alignment.bottomCenter,
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.end,
                              children: [
                                Padding(
                                  padding:
                                      const EdgeInsets.only(top: 5, bottom: 20),
                                  child: Align(
                                    alignment: Alignment.center,
                                    child: ElevatedButton(
                                      style: CustomButton.DefaultButton(
                                          CustomColors.primaryColor),
                                      onPressed: () {
                                        _validateInputs();
                                        setState(() {});
                                      },
                                      child: Text("Lanjut",
                                          style: CustomText.TextArvoBold(
                                              18, CustomColors.whiteColor)),
                                    ),
                                  ),
                                ),
                                Padding(
                                  padding: const EdgeInsets.only(bottom: 20),
                                  child: Align(
                                    alignment: Alignment.center,
                                    child: ElevatedButton(
                                      style: CustomButton.WhiteButton(
                                          CustomColors.whiteColor),
                                      onPressed: () {
                                        Navigator.pop(context);
                                        setState(() {
                                          try {
                                            print("press");
                                          } catch ($e) {
                                            CustomWidget.NotifGagal(context);
                                          }
                                        });
                                      },
                                      child: Text(
                                        "Kembali",
                                        style: CustomText.TextArvoBold(
                                            16, CustomColors.blackColor),
                                        maxLines: 1,
                                      ),
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
