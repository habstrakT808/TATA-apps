import 'package:TATA/BeforeLogin/register3Pages.dart';
import 'package:TATA/models/RegisterModels.dart';
import 'package:TATA/sendApi/Server.dart';
import 'package:TATA/sendApi/userApi.dart';
import 'package:TATA/src/CustomText.dart';
import 'package:email_otp/email_otp.dart';
import 'package:flutter/material.dart';
import 'package:TATA/src/CustomColors.dart';
import 'dart:async';
import 'package:pinput/pinput.dart';
import 'package:lottie/lottie.dart';
import 'package:TATA/helper/emailjs_otp.dart';

class Register2 extends StatefulWidget {
  final RegisterData registerData;

  const Register2({super.key, required this.registerData});
  @override
  _Register2State createState() => _Register2State();
}

class _Register2State extends State<Register2> {
  final pinController = TextEditingController();
  final focusNode = FocusNode();
  final formKey = GlobalKey<FormState>();
  String _otpError = '';
  bool _isLoading = false;
  int _timerSeconds = 60;
  Timer? _timer;
  bool _isTimerRunning = true;
  String? _sentOtp;
  bool _isSendingOtp = false;

  @override
  void initState() {
    super.initState();
    startTimer();
    resendOTP(); // Kirim OTP pertama kali
  }

  void showDataPrint() {
    print("data : ${widget.registerData.email}");
    print("data : ${widget.registerData.pasword}");
    print("data : ${widget.registerData.noHp}");
    print("data : ${widget.registerData.namaUser}");
  }

  Future<void> _registerUser() async {
    final result = await UserApi.register(
        widget.registerData.email,
        widget.registerData.noHp,
        widget.registerData.namaUser,
        widget.registerData.pasword,
        widget.registerData.pasword);
    if (result != null) {
      showDataPrint();
      if (result['status'] == "success") {
        print("Result : $result");
        // Berhasil mendaftar
        if (!mounted) return;
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (context) => const Register3pages()),
        );
      } else if (result['status'] == "error") {
        print("Resultt : $result");
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Pendaftaran gagal: ${result['message']}')),
        );
      } else {
        print("Resulttt : $result");
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
              content:
                  Text('Pendaftaran gagal: ada kesalahan pengiriman data')),
        );
      }
    } else {
      print("gagal : $result");
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
            content: Text('Pendaftaran gagal: ada kesalahan pengiriman data')),
      );
    }
    if (!mounted) return;
    setState(() {
      _isLoading = false;
    });
  }

  @override
  void dispose() {
    pinController.dispose();
    focusNode.dispose();
    _timer?.cancel();
    super.dispose();
  }

  void startTimer() {
    _timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (!mounted) {
        timer.cancel();
        return;
      }
      setState(() {
        if (_timerSeconds > 0) {
          _timerSeconds--;
        } else {
          _isTimerRunning = false;
          _timer?.cancel();
        }
      });
    });
  }

  Future<void> resendOTP() async {
    if (_isSendingOtp) return;
    if (!mounted) return;
    setState(() {
      _isSendingOtp = true;
      _timerSeconds = 60;
      _isTimerRunning = true;
      _otpError = '';
    });
    startTimer();
    // Generate OTP random 6 digit
    final otp = (100000 + (DateTime.now().millisecondsSinceEpoch % 900000)).toString();
    final sent = await EmailJsOtp.sendOtpEmailJS(email: widget.registerData.email, otp: otp);
    if (!mounted) return;
    if (sent) {
      setState(() {
        _sentOtp = otp;
        _otpError = '';
      });
      print('OTP sent and saved: $otp');
    } else {
      setState(() {
        _otpError = 'Gagal mengirim OTP. Coba lagi nanti.';
      });
      print('Gagal mengirim OTP ke EmailJS');
    }
    if (!mounted) return;
    setState(() {
      _isSendingOtp = false;
    });
  }

  bool isOTPFilled() {
    return pinController.text.length == 6;
  }

  void validateOTP() async {
    final isValid = pinController.text == _sentOtp;
    if (!mounted) return;
    setState(() {
      _otpError = isValid ? '' : 'Kode OTP salah atau tidak valid.';
    });
    if (isValid) {
      if (!mounted) return;
      setState(() => _isLoading = true);
      Future.delayed(const Duration(seconds: 1), () {
        if (!mounted) return;
        _registerUser();
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final defaultPinTheme = PinTheme(
      width: 50,
      height: 50,
      textStyle: const TextStyle(
        fontSize: 20,
        fontWeight: FontWeight.w600,
      ),
      decoration: BoxDecoration(
        color: Colors.white,
        border: Border.all(color: CustomColors.primaryColor),
        borderRadius: BorderRadius.circular(8),
      ),
    );

    return Stack(
      children: [
        Scaffold(
          body: SafeArea(
            child: LayoutBuilder(
              builder: (context, constraints) {
                return Stack(
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
                              child: ConstrainedBox(
                                constraints: BoxConstraints(
                                  minHeight:
                                      MediaQuery.of(context).size.height -
                                          MediaQuery.of(context).padding.top,
                                ),
                                child: IntrinsicHeight(
                                  child: Padding(
                                    padding: const EdgeInsets.symmetric(
                                        horizontal: 32.0),
                                    child: Column(
                                      crossAxisAlignment:
                                          CrossAxisAlignment.start,
                                      children: [
                                        const SizedBox(height: 30),
                                        Center(
                                          child: Text('Kode Verifikasi',
                                              style: CustomText.TextArvoBold(
                                                  24, CustomColors.blackColor)),
                                        ),
                                        const SizedBox(height: 20),
                                        Center(
                                          child: Pinput(
                                            length: 6,
                                            controller: pinController,
                                            focusNode: focusNode,
                                            defaultPinTheme: defaultPinTheme,
                                            separatorBuilder: (index) =>
                                                const SizedBox(width: 8),
                                            validator: (value) {
                                              return value?.length == 6
                                                  ? null
                                                  : 'Pin tidak lengkap';
                                            },
                                            onCompleted: (pin) {
                                              setState(() => _otpError = '');
                                            },
                                          ),
                                        ),
                                        if (_otpError.isNotEmpty)
                                          Padding(
                                            padding:
                                                const EdgeInsets.only(top: 8.0),
                                            child: Text(
                                              _otpError,
                                              style: const TextStyle(
                                                  color: Colors.red),
                                            ),
                                          ),
                                        const SizedBox(height: 30),
                                        Row(
                                          mainAxisAlignment:
                                              MainAxisAlignment.spaceBetween,
                                          children: [
                                            Text(
                                              'Waktu tersisa: ${_timerSeconds}s',
                                              style:
                                                  const TextStyle(fontSize: 14),
                                            ),
                                            TextButton(
                                              onPressed: _isTimerRunning
                                                  ? null
                                                  : resendOTP,
                                              child: Text(
                                                'Kirim ulang',
                                                style: TextStyle(
                                                  color: _isTimerRunning
                                                      ? Colors.grey
                                                      : CustomColors
                                                          .primaryColor,
                                                ),
                                              ),
                                            ),
                                          ],
                                        ),
                                        // const Spacer(),
                                        SizedBox(
                                          width: double.infinity,
                                          child: ElevatedButton(
                                            onPressed: isOTPFilled()
                                                ? validateOTP
                                                : null,
                                            style: ElevatedButton.styleFrom(
                                              backgroundColor:
                                                  CustomColors.primaryColor,
                                              shape: RoundedRectangleBorder(
                                                borderRadius:
                                                    BorderRadius.circular(30.0),
                                              ),
                                              padding:
                                                  const EdgeInsets.symmetric(
                                                      vertical: 15.0),
                                            ),
                                            child: const Text(
                                              'Lanjutan',
                                              style: TextStyle(
                                                fontSize: 16,
                                                color: Colors.white,
                                                fontFamily: 'NotoSanSemiBold',
                                                fontWeight: FontWeight.w400,
                                              ),
                                            ),
                                          ),
                                        ),
                                        const SizedBox(height: 30),
                                      ],
                                    ),
                                  ),
                                ),
                              ),
                            ),
                          ),
                        ),
                      ),
                    ),
                  ],
                );
              },
            ),
          ),
        ),
        if (_isLoading)
          Container(
            color: Colors.black54,
            child: Center(
              child: Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Lottie.asset(
                      'assets/animations/loading.json',
                      width: 150,
                      height: 150,
                    ),
                    const SizedBox(height: 10),
                    const Text(
                      'Mohon Tunggu...',
                      style: TextStyle(
                        fontFamily: 'NotoSanSemiBold',
                        fontSize: 14,
                        color: Colors.black,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
      ],
    );
  }
}
