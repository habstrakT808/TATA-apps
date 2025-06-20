import 'dart:convert';

// import 'package:TATA/pageSebelumLogin/page_lupa_katasandi.dart';
import 'package:TATA/BeforeLogin/logingoogle.dart';
import 'package:TATA/BeforeLogin/lupapassword1Pages.dart';
import 'package:TATA/BeforeLogin/register1Pages.dart';
import 'package:TATA/helper/user_preferences.dart';
import 'package:TATA/main.dart';
import 'package:TATA/sendApi/Server.dart';
import 'package:TATA/sendApi/userApi.dart';
import 'package:TATA/src/CustomWidget.dart';
// import 'package:TATA/src/Server.dart';
import 'package:TATA/src/pageTransition.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:TATA/src/CustomButton.dart';
import 'package:TATA/src/CustomColors.dart';
import 'package:TATA/src/CustomText.dart';
// import 'package:TATA/navigation/utama.dart';
import 'package:flutter/services.dart';
import 'dart:async';
import 'package:TATA/sendApi/AuthManager.dart';
import 'package:TATA/main.dart';

// import 'package:google_sign_in/google_sign_in.dart';

import 'package:http/http.dart' as http;

class page_login extends StatefulWidget {
  static String id_user = "";

  const page_login({super.key});
  @override
  State<page_login> createState() => _page_loginState();
}

class _page_loginState extends State<page_login> {
  final TextEditingController emailController = TextEditingController();
  final TextEditingController passwordController = TextEditingController();
  bool isLoading = false;
  bool obscureText = true;
  String? errorMessage;

  bool isAuthorized = false;
  String contactText = '';

  String errorText = "";
  bool isObscured = true;
  bool isHovered = true;
  bool isEmailFocused = false;
  bool isPasswordFocused = false;
  bool isWrong = false;
  double sizeerror = 0;
  bool isKeyboardActive = false;
  String statusKeyboard = "tidak aktif";
  
  @override
  void initState() {
    super.initState();
    // Initialize AuthManager and UserPreferences
    UserPreferences.init();
    RawKeyboard.instance.addListener(_handleKeyEvent);
  }

  Future _ceklogingoogle(String emailgoogle) async {
    try {
      // Tampilkan loading
      CustomWidget.NotifLoading(context);
      
      if (emailgoogle.isEmpty) {
        setState(() {
          Navigator.pop(context); // Close loading dialog
          isWrong = true;
          sizeerror = 14;
          errorText = "Email Google tidak valid!";
        });
        return;
      }
      
      // Lakukan login dengan Google menggunakan API
      final result = await UserApi.loginGoogle(emailgoogle);
      
      // Tutup dialog loading
      Navigator.pop(context);
      
      if (result == null) {
        setState(() {
          isWrong = true;
          errorText = "Terjadi kesalahan koneksi";
          sizeerror = 18;
        });
        return;
      }
      
      if (result['status'] == 'error') {
        setState(() {
          isWrong = true;
          errorText = result['message'] ?? "Email belum terdaftar!";
          sizeerror = 18;
        });
        return;
      }
      
      // Login berhasil
      isWrong = false;
      
      // Standardize data structure
      Map<String, dynamic> dataToSave = _standardizeUserData(result);
      
      // Debug
      print("STANDARDIZED GOOGLE DATA: $dataToSave");
      
      // Simpan data user
      await UserPreferences.saveUser(dataToSave);
      
      // Simpan token
      final token = dataToSave['access_token'];
      if (token != null && token.isNotEmpty) {
        await AuthManager().saveToken(token);
        
        // Save email for future token refreshes
        if (dataToSave.containsKey('user') && 
            dataToSave['user'] != null &&
            dataToSave['user'].containsKey('email')) {
          await AuthManager().saveEmail(dataToSave['user']['email']);
        }
      }
      
      // Navigasi ke halaman utama
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => const MainPage())
      );
      
    } catch (e) {
      Navigator.pop(context);
      CustomWidget.NotifGagal(context);
      print("Error: $e");
    }
  }

  FocusNode emailFocusNode = FocusNode();
  FocusNode passwordFocusNode = FocusNode();
  @override
  void dispose() {
    RawKeyboard.instance.removeListener(_handleKeyEvent);

    emailFocusNode.dispose();
    passwordFocusNode.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    MediaQueryData mediaQuery = MediaQuery.of(context);
    isKeyboardActive = mediaQuery.viewInsets.bottom > 0;
    return WillPopScope(
      onWillPop: () async {
        print("tombol kembali di tekan");
        if (isKeyboardActive) {
          // Jika keyboard aktif, hanya tutup keyboard
          setState(() {
            _keyboardActiveFunction();
            statusKeyboard = "tidak aktif";
            isEmailFocused = false;
            isPasswordFocused = false;
          });
          isWrong = false;
          FocusScope.of(context).unfocus();
          return false; // Jangan keluar dari halaman
        } else {
          // Jika keyboard tidak aktif, kembali ke halaman utama
          _keyboardInactiveFunction();
          Navigator.pushReplacement(
            context, 
            MaterialPageRoute(builder: (context) => const MainPage())
          );
          return false; // Jangan gunakan back system karena kita sudah handle navigasi
        }
      },
      child: MaterialApp(
        debugShowCheckedModeBanner: false,
        home: Scaffold(
          backgroundColor: CustomColors.whiteColor,
          body: GestureDetector(
            onTap: () {
              setState(() {
                if (isKeyboardActive) {
                  // Jika keyboard aktif
                  _keyboardActiveFunction();
                } else {
                  // Jika keyboard tidak aktif
                  _keyboardInactiveFunction();
                }
                statusKeyboard = "tidak aktif";
                isWrong = false;
                isEmailFocused = false;
                isPasswordFocused = false;
                FocusScope.of(context).unfocus();
              });
            },
            child: SafeArea(
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
                    child: Center(
                        child: Image.asset(Server.UrlGambar("bgloginn.png"))),
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
                    top: 200,
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
                        child: SingleChildScrollView(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            mainAxisAlignment: MainAxisAlignment.start,
                            children: [
                              Align(
                                  alignment: Alignment.center,
                                  child: Padding(
                                    padding: const EdgeInsets.fromLTRB(
                                        15, 25, 15, 20),
                                    child: Text(
                                      "Selamat Datang",
                                      style: CustomText.TextArvoBold(
                                          24, CustomColors.blackColor),
                                    ),
                                  )),
                              Align(
                                alignment: Alignment.center,
                                child: Padding(
                                  padding:
                                      const EdgeInsets.fromLTRB(25, 1, 25, 5),
                                  child: ElevatedButton(
                                    style: CustomButton.GoogleButton(
                                        CustomColors.whiteColor),
                                    onPressed: () async {
                                      await GoogleSignInService
                                          .signOut(); // Tambahkan ini agar bisa login ulang
                                      await FirebaseAuth.instance.signOut();

                                      final user = await GoogleSignInService
                                          .signInWithGoogle();
                                      if (user != null) {
                                        print("Login sukses: ${user.email}");
                                        setState(() {
                                          print("Login presseedd");
                                          try {
                                            _ceklogingoogle("${user.email}");
                                            print("press");
                                          } catch ($e) {
                                            CustomWidget.NotifGagal(context);
                                            print("ERRORRR ${$e}");
                                          }
                                        });
                                      } else {
                                        print("Login dibatalkan.");
                                      }
                                    },
                                    child: Row(
                                      mainAxisAlignment:
                                          MainAxisAlignment.center,
                                      children: [
                                        Container(
                                            margin: const EdgeInsets.all(10),
                                            width: 20,
                                            height: 20,
                                            child: Image.asset(Server.UrlGambar(
                                                'google.png'))),
                                        Container(
                                          child: Text(
                                            "Masuk Dengan Google",
                                            style: CustomText.TextArvoBold(
                                                13, CustomColors.blackColor),
                                          ),
                                        )
                                      ],
                                    ),
                                  ),
                                ),
                              ),
                              Align(
                                  alignment: Alignment.center,
                                  child: Container(
                                    margin: const EdgeInsets.fromLTRB(
                                        10, 15, 10, 0),
                                    child: Row(
                                        mainAxisAlignment:
                                            MainAxisAlignment.center,
                                        children: [
                                          Container(
                                            height: 2,
                                            width: 130,
                                            color: CustomColors.HintColor,
                                          ),
                                          const SizedBox(
                                            width: 5,
                                          ),
                                          Text("Atau",
                                              style: CustomText.TextArvoBold(
                                                  12, CustomColors.HintColor)),
                                          const SizedBox(
                                            width: 5,
                                          ),
                                          Container(
                                            height: 2,
                                            width: 130,
                                            color: CustomColors.HintColor,
                                          ),
                                        ]),
                                  )),
                              Align(
                                alignment: Alignment.center,
                                child: Padding(
                                  padding:
                                      const EdgeInsets.fromLTRB(15, 10, 15, 10),
                                  child: Visibility(
                                    visible: isWrong,
                                    child: Text(errorText,
                                        textAlign: TextAlign.center,
                                        style: CustomText.TextArvoBold(
                                            16, CustomColors.redColor)),
                                  ),
                                ),
                              ),
                              Container(
                                height: 55,
                                padding:
                                    const EdgeInsets.fromLTRB(30, 5, 30, 10),
                                child: TextField(
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
                                  focusNode: emailFocusNode,
                                  onTap: () {
                                    isWrong = false;
                                    setState(() {
                                      emailFocusNode.requestFocus();
                                      isEmailFocused = true;
                                      isPasswordFocused = false;
                                    });
                                  },
                                ),
                              ),
                              const SizedBox(
                                height: 10,
                              ),
                              Container(
                                height: 50,
                                padding:
                                    const EdgeInsets.fromLTRB(30, 5, 30, 5),
                                child: TextField(
                                  controller: passwordController,
                                  keyboardType: TextInputType.visiblePassword,
                                  textAlign: TextAlign.start,
                                  textInputAction: TextInputAction.next,
                                  obscureText: isObscured,
                                  decoration: InputDecoration(
                                    hintText: "Masukkan Kata Sandi Anda",
                                    border: OutlineInputBorder(
                                      borderRadius: const BorderRadius.all(
                                          Radius.circular(10)),
                                      borderSide: BorderSide(
                                        color: isPasswordFocused
                                            ? Colors.black
                                            : Colors.grey,
                                      ),
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
                                    suffixIcon: IconButton(
                                      alignment: Alignment.center,
                                      onPressed: () {
                                        setState(() {
                                          isObscured = !isObscured;
                                        });
                                      },
                                      icon: Icon(
                                        isObscured
                                            ? Icons.visibility_off
                                            : Icons.visibility,
                                        color: Colors.grey,
                                      ),
                                      iconSize: 25,
                                    ),
                                  ),
                                  style: CustomText.TextArvo(
                                    16,
                                    CustomColors.blackColor,
                                  ),
                                  focusNode: passwordFocusNode,
                                  onTap: () {
                                    if (isKeyboardActive) {
                                      // Jika keyboard aktif
                                      _keyboardActiveFunction();
                                    } else {
                                      // Jika keyboard tidak aktif
                                      _keyboardInactiveFunction();
                                    }
                                    isWrong = false;
                                    setState(() {
                                      statusKeyboard = "aktif";
                                      passwordFocusNode.requestFocus();
                                      isPasswordFocused = true;
                                      isEmailFocused = false;
                                    });
                                  },
                                ),
                              ),
                              Padding(
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 30, vertical: 10),
                                child: Align(
                                  alignment: Alignment.centerRight,
                                  child: TextButton(
                                    onPressed: () {
                                      print("Lupa sandi pressed");
                                      Navigator.push(
                                          context,
                                          PageRouteBuilder(
                                            pageBuilder: ((context, animation,
                                                    secondaryAnimation) =>
                                                const Lupapassword1()),
                                            transitionsBuilder: (context,
                                                animation,
                                                secondaryAnimation,
                                                child) {
                                              return FadeTransition(
                                                  opacity: animation,
                                                  child: child);
                                            },
                                          ));
                                    },
                                    child: Text("Lupa Kata Sandi?",
                                        style: CustomText.TextArvoItalic(
                                            16, CustomColors.HintColor)),
                                  ),
                                ),
                              ),
                              Padding(
                                padding:
                                    const EdgeInsets.only(top: 5, bottom: 20),
                                child: Align(
                                  alignment: Alignment.center,
                                  child: ElevatedButton(
                                    style: CustomButton.DefaultButton(
                                        CustomColors.primaryColor),
                                    onPressed: isLoading ? null : doLogin,
                                    child: isLoading
                                        ? const CircularProgressIndicator(
                                            color: Colors.white)
                                        : Text("Masuk",
                                            style: CustomText.TextArvoBold(
                                                20, CustomColors.whiteColor)),
                                  ),
                                ),
                              ),
                              Text(
                                'Status Keyboard: ${isKeyboardActive ? statusKeyboard = 'Aktif' : statusKeyboard = "tidak aktif"}$statusKeyboard',
                                style: const TextStyle(
                                  fontSize: 0,
                                ),
                              ),
                              Padding(
                                padding:
                                    const EdgeInsets.symmetric(vertical: 5),
                                child: Center(
                                  child: Text.rich(
                                    TextSpan(
                                      text: 'Belum Punya Akun? ',
                                      style: TextStyle(
                                          fontSize: 16,
                                          color: CustomColors.HintColor,
                                          fontFamily: 'NotoSanSemiBold',
                                          fontWeight: FontWeight.bold),
                                      children: [
                                        TextSpan(
                                          text: 'Daftar Sekarang',
                                          style: TextStyle(
                                              fontSize: 16,
                                              color: CustomColors
                                                  .secondaryColor, // Mengubah warna di sini
                                              fontFamily: 'NotoSanSemiBold',
                                              fontWeight: FontWeight.bold),
                                          recognizer: TapGestureRecognizer()
                                            ..onTap = () {
                                              print("Pressed");
                                              Navigator.push(
                                                context,
                                                SmoothPageTransition(
                                                  page: const Register(),
                                                ),
                                              );
                                            },
                                        ),
                                      ],
                                    ),
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  Future<void> doLogin() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
      isWrong = false;
    });
    
    try {
      // Validate input
      if (emailController.text.isEmpty) {
        setState(() {
          isWrong = true;
          sizeerror = 14;
          errorText = 'Email harus diisi';
          isLoading = false;
        });
        return;
      }
      
      if (passwordController.text.isEmpty) {
        setState(() {
          isWrong = true;
          sizeerror = 14;
          errorText = 'Password harus diisi';
          isLoading = false;
        });
        return;
      }
      
      // Show loading indicator
      CustomWidget.NotifLoading(context);
      
      // Send login request
      final response = await http.post(
        Server.urlLaravel('api/mobile/users/login'),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: json.encode({
          'email': emailController.text.trim(),
          'password': passwordController.text,
        }),
      );

      // Close loading dialog
      Navigator.pop(context);
      
      final responseData = json.decode(response.body);
      print("LOGIN RESPONSE: $responseData");
      
      if (response.statusCode == 200) {
        // Standardize data structure
        Map<String, dynamic> dataToSave = _standardizeUserData(responseData);
        
        // Debug
        print("STANDARDIZED DATA: $dataToSave");
        
        // Save user data to SharedPreferences
        await UserPreferences.saveUser(dataToSave);
        
        // Save token to AuthManager
        final token = dataToSave['access_token'];
        if (token != null && token.isNotEmpty) {
          await AuthManager().saveToken(token);
          
          // Save email for future token refreshes
          if (dataToSave.containsKey('user') && 
              dataToSave['user'] != null &&
              dataToSave['user'].containsKey('email')) {
            await AuthManager().saveEmail(dataToSave['user']['email']);
          }
        }
        
        // Navigate to home page
        Navigator.pushReplacement(
          context, 
          MaterialPageRoute(builder: (context) => const MainPage())
        );
      } else {
        // Handle login error
        setState(() {
          isWrong = true;
          sizeerror = 14;
          errorText = responseData['message'] ?? 'Login gagal, silakan coba lagi';
        });
      }
    } catch (e) {
      setState(() {
        isWrong = true;
        sizeerror = 14;
        errorText = 'Terjadi kesalahan: $e';
      });
    } finally {
      setState(() {
        isLoading = false;
      });
    }
  }
  
  // Helper method to standardize user data structure
  Map<String, dynamic> _standardizeUserData(Map<String, dynamic> apiResponse) {
    // Structure we want:
    // {
    //   "access_token": "the_token",
    //   "token_type": "Bearer",
    //   "user": { user object with id, name, email, etc. }
    // }
    
    Map<String, dynamic> result = {};
    
    // Extract access token
    if (apiResponse.containsKey('access_token')) {
      result['access_token'] = apiResponse['access_token'];
    } else if (apiResponse.containsKey('data') && 
               apiResponse['data'] is Map &&
               apiResponse['data'].containsKey('access_token')) {
      result['access_token'] = apiResponse['data']['access_token'];
    }
    
    // Extract token type
    if (apiResponse.containsKey('token_type')) {
      result['token_type'] = apiResponse['token_type'];
    } else if (apiResponse.containsKey('data') && 
               apiResponse['data'] is Map &&
               apiResponse['data'].containsKey('token_type')) {
      result['token_type'] = apiResponse['data']['token_type'];
    } else {
      result['token_type'] = 'Bearer'; // Default
    }
    
    // Extract user data
    if (apiResponse.containsKey('user')) {
      result['user'] = apiResponse['user'];
    } else if (apiResponse.containsKey('data') && 
               apiResponse['data'] is Map) {
      if (apiResponse['data'].containsKey('user')) {
        result['user'] = apiResponse['data']['user'];
      } else if (apiResponse['data'] is Map && apiResponse['data'].containsKey('id')) {
        // If 'data' itself is the user object
        result['user'] = apiResponse['data'];
      }
    }
    
    // Ensure user object has required fields
    if (result.containsKey('user') && result['user'] != null) {
      // Make sure user has id field
      if (!result['user'].containsKey('id') && result['user'].containsKey('uuid')) {
        result['user']['id'] = result['user']['uuid'];
      }
      
      // Make sure user has email field
      if (!result['user'].containsKey('email') && apiResponse.containsKey('email')) {
        result['user']['email'] = apiResponse['email'];
      }
    }
    
    return result;
  }

  void _keyboardActiveFunction() {
    print('Keyboard aktif');
  }

  void _keyboardInactiveFunction() {
    print('Keyboard tidak aktif');
    // Tidak perlu navigasi di sini karena sudah ditangani di onWillPop
  }

  void _handleKeyEvent(RawKeyEvent event) {
    if (event is RawKeyUpEvent &&
        event.logicalKey == LogicalKeyboardKey.backspace) {
      // Memastikan bahwa peristiwa tombol kembali terjadi
      print('Tombol kembali ditekan');
      // Mengubah variabel boolean menjadi true
      setState(() {
        isKeyboardActive = true;
      });
    }
  }
}
