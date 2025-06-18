import 'dart:convert';

import 'package:TATA/helper/user_preferences.dart';
import 'package:TATA/models/ReviewModels.dart';
import 'package:TATA/menu/DetailReview/DetailReview.dart' as dr;
import 'package:TATA/menu/JasaDesign/JasaDesignLogo.dart';
import 'package:TATA/menu/JasaDesign/JasaDesignBanner.dart';
import 'package:TATA/menu/JasaDesign/JasaDesignPoster.dart';
import 'package:TATA/sendApi/Server.dart';
import 'package:TATA/src/CustomColors.dart';
import 'package:TATA/src/CustomText.dart';
import 'package:TATA/src/pageTransition.dart';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;

class HomePage extends StatefulWidget {
  const HomePage({super.key});

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  String namaUser = "...";
  String imageProfil = "";

  void loadUserData() async {
    final userData = await UserPreferences.getUser();
    if (userData != null) {
      print("User dari SharedPreferences: \\${userData['user']['name']}");
      print("foto dari SharedPreferences: \\${userData['user']['foto']}");
      setState(() {
        imageProfil = userData['user']['foto'] ?? '';
        namaUser = userData['user']['name'] ?? '';
      });
    }
  }

  @override
  void initState() {
    super.initState();
    setState(() {
      loadUserData();
      fetchReviews();
    });
  }

  Future<List<Review>> fetchReviews() async {
    try {
      print('FETCHING REVIEWS FROM: ${Server.urlLaravel('users/review')}');
      final response = await http.get(
        Server.urlLaravel('users/review'),
        headers: {'Accept': 'application/json'}
      ).timeout(
        const Duration(seconds: 10),
        onTimeout: () {
          print('TIMEOUT: Review request timed out');
          return http.Response('{"message":"timeout"}', 408);
        },
      );
      
      print('RESPONSE STATUS: ${response.statusCode}');
      print('RESPONSE BODY: ${response.body}');
      
      if (response.statusCode == 200) {
        final List data = json.decode(response.body);
        return data.map((json) => Review.fromJson(json)).toList();
      } else {
        print('ERROR: Failed to load reviews with status ${response.statusCode}');
        // Return empty list instead of throwing
        return [];
      }
    } catch (e) {
      print('ERROR fetchReviews: $e');
      // Return empty list instead of throwing
      return [];
    }
  }

  @override
  Widget build(BuildContext context) {
    // Get screen width for responsive design
    final screenWidth = MediaQuery.of(context).size.width;
    final screenHeight = MediaQuery.of(context).size.height;
    final isSmallScreen = screenWidth < 360;
    
    return Scaffold(
      backgroundColor: Colors.white,
      body: SingleChildScrollView(
        child: Column(
          children: [
            _buildHeader(context),
            Stack(
              children: [
                Positioned(
                    child: Container(
                  child: Align(
                    alignment: Alignment.topLeft,
                    child: Image.asset(
                      Server.UrlGambar("atributhomecircle.png"),
                      scale: 1,
                    ),
                  ),
                )),
                Positioned(
                    bottom: 0,
                    right: 0,
                    child: Container(
                      child: Align(
                        alignment: Alignment.bottomLeft,
                        child: Image.asset(
                          Server.UrlGambar("atributhomebigcircle.png"),
                          scale: 1,
                        ),
                      ),
                    )),
                Column(
                  children: [
                    SizedBox(
                      height: 20,
                    ),
                    _buildSectionTitle("Jasa Desain"),
                    const SizedBox(height: 5),
                    _buildJasaDesainSection(context),
                    const SizedBox(height: 20),
                    _buildSectionTitle("Inspirasi Desain"),
                    const SizedBox(height: 5),
                    _buildInspirasiDesain(context),
                    const SizedBox(height: 20),
                    _buildSectionTitle("Review"),
                    const SizedBox(height: 5),
                    _buildReviewSection(context),
                    const SizedBox(height: 20),
                  ],
                )
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildHeader(BuildContext context) {
    final screenWidth = MediaQuery.of(context).size.width;
    
    return Container(
      decoration: BoxDecoration(
        color: CustomColors.threertyColor,
      ),
      child: Stack(
        children: [
          Container(
            padding: EdgeInsets.only(
              top: screenWidth * 0.15, // responsive padding
              left: screenWidth * 0.05,
              right: screenWidth * 0.05,
              bottom: 15
            ),
            child: Row(
              children: [
                Card.outlined(
                  elevation: 5,
                  shape: const RoundedRectangleBorder(
                    borderRadius: BorderRadius.only(
                        bottomLeft: Radius.circular(35),
                        bottomRight: Radius.circular(35),
                        topLeft: Radius.circular(35),
                        topRight: Radius.circular(35)),
                  ),
                  child: Container(
                    margin: EdgeInsets.all(2),
                    child: CircleAvatar(
                      radius: screenWidth < 360 ? 25 : 30, // smaller for small screens
                      backgroundImage: imageProfil.isNotEmpty
                          ? NetworkImage(
                              scale: 1,
                              Server.UrlImageProfil(imageProfil),
                            )
                          : AssetImage(
                              Server.UrlGambar('logoapk.png'),
                            ),
                    ),
                  ),
                ),
                SizedBox(width: screenWidth * 0.03), // responsive spacing
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        namaUser,
                        style: CustomText.TextArvoBold(
                          screenWidth < 360 ? 16 : 18, // smaller font for small screens
                          CustomColors.whiteColor
                        ),
                        overflow: TextOverflow.ellipsis, // prevent text overflow
                      ),
                      Text(
                        "👋 Selamat Datang!",
                        style: TextStyle(color: Colors.white70),
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ),
                ),
                const Spacer(),
              ],
            ),
          ),
          Image.asset(Server.UrlGambar("atributhome.png")),
        ],
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20),
      child: Align(
        alignment: Alignment.centerLeft,
        child: Text(title,
            style: const TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
            )),
      ),
    );
  }

  Widget _buildJasaDesainSection(BuildContext context) {
    final screenWidth = MediaQuery.of(context).size.width;
    final cardWidth = (screenWidth - 60) / 3; // 60 = padding (20*2) + spacing (10*2)
    
    return LayoutBuilder(
      builder: (context, constraints) {
        return Padding(
          padding: const EdgeInsets.symmetric(horizontal: 20),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _buildServiceCard(context, "LOGO", "homeimagelogo.png", JasaDesignLogo()),
              _buildServiceCard(context, "BANNER", "homeimagebanner.png", JasaDesignBanner()),
              _buildServiceCard(context, "POSTER", "homeimageposter.png", JasaDesignPoster()),
            ],
          ),
        );
      },
    );
  }

  Widget _buildServiceCard(BuildContext context, String title, String imageUrl, Widget nextPage) {
    final screenWidth = MediaQuery.of(context).size.width;
    final isSmallScreen = screenWidth < 360;
    
    return Expanded(
      child: TextButton(
        onPressed: () {
          print("pressed");
          Navigator.push(context, SmoothPageTransition(page: nextPage));
        },
        child: Container(
          margin: EdgeInsets.symmetric(horizontal: 5),
          child: Card(
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(10),
            ),
            color: CustomColors.greendark,
            child: Column(
              children: [
                ClipRRect(
                  borderRadius: const BorderRadius.only(
                    topLeft: Radius.circular(10),
                    topRight: Radius.circular(10),
                  ),
                  child: AspectRatio(
                    aspectRatio: 1.0, // Square aspect ratio
                    child: Image.asset(
                      Server.UrlGambar(imageUrl),
                      fit: BoxFit.cover,
                    ),
                  ),
                ),
                Padding(
                  padding: EdgeInsets.symmetric(vertical: isSmallScreen ? 3 : 5),
                  child: Text(
                    title,
                    style: TextStyle(
                      fontWeight: FontWeight.bold,
                      color: CustomColors.whiteColor,
                      fontSize: isSmallScreen ? 12 : 14,
                    ),
                  ),
                )
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildInspirasiDesain(BuildContext context) {
    final screenWidth = MediaQuery.of(context).size.width;
    final cardWidth = screenWidth * 0.6; // 60% of screen width
    
    return SizedBox(
      height: 150,
      child: ListView(
        scrollDirection: Axis.horizontal,
        padding: EdgeInsets.only(left: screenWidth * 0.05), // 5% of screen padding
        children: [
          _buildInspirasiCard(Server.UrlGambar("atributhomecircle.png"), isAsset: true, width: cardWidth),
          _buildInspirasiCard(Server.UrlGambar("logoapk.png"), isAsset: true, width: cardWidth),
          _buildInspirasiCard(Server.UrlGambar("atributhomebigcircle.png"), isAsset: true, width: cardWidth),
        ],
      ),
    );
  }

  Widget _buildInspirasiCard(String imageUrl, {bool isAsset = false, double? width}) {
    return Container(
      margin: const EdgeInsets.only(right: 10),
      width: width ?? 200,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(12),
        image: DecorationImage(
          image: isAsset ? AssetImage(imageUrl) : NetworkImage(imageUrl) as ImageProvider,
          fit: BoxFit.cover,
        ),
      ),
    );
  }

  Widget _buildReviewSection(BuildContext context) {
    final screenWidth = MediaQuery.of(context).size.width;
    
    return FutureBuilder<List<Review>>(
      future: fetchReviews(),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return SizedBox(
            height: 170,
            child: Center(child: CircularProgressIndicator()),
          );
        } else if (snapshot.hasError) {
          print('ERROR IN FUTURE BUILDER: ${snapshot.error}');
          // Display error but with fixed height instead of just text
          return SizedBox(
            height: 170,
            child: Center(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(Icons.error_outline, color: Colors.red, size: 40),
                  SizedBox(height: 8),
                  Text('Gagal memuat ulasan', style: TextStyle(color: Colors.red)),
                ],
              ),
            ),
          );
        } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
          // Create placeholder reviews if no data
          final placeholderReviews = [
            Review(
              id: '1',
              name: 'Pengguna Contoh',
              rating: 5, 
              feedback: 'Desain keren dan pengerjaan cepat!',
              avatarUrl: null
            ),
            Review(
              id: '2', 
              name: 'Client Tetap',
              rating: 4,
              feedback: 'Hasil desain sangat memuaskan',
              avatarUrl: null
            )
          ];
          
          return _buildReviewList(context, placeholderReviews, isPlaceholder: true);
        }
        
        final reviews = snapshot.data!;
        return _buildReviewList(context, reviews);
      },
    );
  }

  Widget _buildReviewList(BuildContext context, List<Review> reviews, {bool isPlaceholder = false}) {
    final screenWidth = MediaQuery.of(context).size.width;
    final reviewCardWidth = screenWidth * 0.7; // 70% of screen width for review cards
    
    return SizedBox(
      height: 170,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        padding: EdgeInsets.only(left: screenWidth * 0.05), // 5% of screen padding
        itemCount: reviews.length,
        itemBuilder: (context, index) {
          final review = reviews[index];
          return Material(
            color: Colors.transparent,
            child: InkWell(
              onTap: () {
                print("press review");
                if (!isPlaceholder) {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (_) => dr.ReviewDetailPage(review: review),
                    ),
                  );
                }
              },
              child: _buildReviewCard(
                context: context,
                name: review.name,
                rating: review.rating,
                feedback: review.feedback,
                avatarUrl: review.avatarUrl,
                width: reviewCardWidth,
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildReviewCard({
    required BuildContext context,
    required String name,
    required int rating,
    required String feedback,
    String? avatarUrl,
    double? width,
  }) {
    final screenWidth = MediaQuery.of(context).size.width;
    final isSmallScreen = screenWidth < 360;
    
    return Container(
      width: width ?? 250,
      margin: EdgeInsets.only(right: screenWidth * 0.03), // 3% of screen margin
      padding: EdgeInsets.all(isSmallScreen ? 8 : 12),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.grey.shade300),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              avatarUrl != null && avatarUrl.isNotEmpty
                  ? CircleAvatar(
                      backgroundImage: NetworkImage(Server.UrlImageProfil(avatarUrl)),
                      radius: isSmallScreen ? 16 : 20,
                    )
                  : CircleAvatar(
                      child: Icon(Icons.person, size: isSmallScreen ? 16 : 20),
                      radius: isSmallScreen ? 16 : 20,
                    ),
              SizedBox(width: isSmallScreen ? 6 : 8),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      name,
                      style: TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: isSmallScreen ? 13 : 14,
                      ),
                      overflow: TextOverflow.ellipsis,
                    ),
                  ],
                ),
              )
            ],
          ),
          SizedBox(height: isSmallScreen ? 6 : 8),
          Row(
            children: List.generate(
              5,
              (index) => Icon(
                index < rating ? Icons.star : Icons.star_border,
                color: index < rating ? Colors.amber : Colors.grey,
                size: isSmallScreen ? 14 : 16,
              ),
            ),
          ),
          SizedBox(height: isSmallScreen ? 6 : 8),
          Text(
            feedback,
            style: TextStyle(fontSize: isSmallScreen ? 11 : 13),
            overflow: TextOverflow.ellipsis,
            maxLines: 3,
          ),
        ],
      ),
    );
  }
}
