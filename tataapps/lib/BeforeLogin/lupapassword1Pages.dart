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
import 'package:TATA/services/AuthService.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:TATA/BeforeLogin/page_login.dart';

class LupaPassword1 extends StatefulWidget {
  const LupaPassword1({super.key});

  @override
  _LupaPassword1State createState() => _LupaPassword1State();
}

class _LupaPassword1State extends State<LupaPassword1> {
  final AuthService _authService = AuthService();
  TextEditingController emailController = TextEditingController();
  bool isLoading = false;
  String errorText = '';
  
  Future<void> _resetPassword() async {
    setState(() {
      isLoading = true;
      errorText = '';
    });
    
    if (emailController.text.isEmpty) {
      setState(() {
        isLoading = false;
        errorText = 'Email tidak boleh kosong';
      });
      return;
    }
    
    try {
      // Kirim email reset password menggunakan Firebase Auth
      await _authService.resetPassword(emailController.text);
      
      // Berhasil mengirim email reset password
      if (mounted) {
        setState(() {
          isLoading = false;
        });
        
        // Tampilkan dialog sukses
        showDialog(
          context: context,
          barrierDismissible: false,
          builder: (BuildContext context) {
            return AlertDialog(
              title: const Text('Email Terkirim'),
              content: const Text(
                'Link untuk reset password telah dikirim ke email Anda. '
                'Silakan cek email Anda dan ikuti instruksi untuk reset password.'
              ),
              actions: <Widget>[
                TextButton(
                  child: const Text('OK'),
                  onPressed: () {
                    Navigator.of(context).pop();
                    // Kembali ke halaman login
                    Navigator.of(context).pushReplacement(
                      MaterialPageRoute(builder: (context) => const page_login()),
                    );
                  },
                ),
              ],
            );
          },
        );
      }
    } on FirebaseAuthException catch (e) {
      setState(() {
        isLoading = false;
        
        switch (e.code) {
          case 'user-not-found':
            errorText = 'Email tidak terdaftar';
            break;
          case 'invalid-email':
            errorText = 'Format email tidak valid';
            break;
          default:
            errorText = 'Gagal mengirim email reset password: ${e.message}';
        }
      });
    } catch (e) {
      setState(() {
        isLoading = false;
        errorText = 'Terjadi kesalahan: $e';
      });
    }
  }

  void _validateInputs() async {
    setState(() {
      if (emailController.text.isEmpty) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Email tidak boleh kosong')),
        );
      } else if (emailController.text.length <= 10) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Email tidak valid')),
        );
      } else {
        _resetPassword();
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
                                  controller: emailController,
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
