class Review {
  final String id;
  final String name;
  final int rating;
  final String feedback;
  final String? avatarUrl;

  Review({
    required this.id,
    required this.name,
    required this.rating,
    required this.feedback,
    this.avatarUrl,
  });

  factory Review.fromJson(Map<String, dynamic> json) {
    return Review(
      id: json['id'] ?? '',
      name: json['name'] ?? '',
      rating: json['rating'] ?? 5,
      feedback: json['feedback'] ?? '',
      avatarUrl: json['avatar_url'],
    );
  }
}
