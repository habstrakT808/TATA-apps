class Review {
  final String id;
  final String name;
  final int rating;
  final String feedback;
  final String? avatarUrl;
  final String? service;
  final String? orderUuid;
  final String? completionDate;
  final String? reviewDate;

  Review({
    required this.id,
    required this.name,
    required this.rating,
    required this.feedback,
    this.avatarUrl,
    this.service,
    this.orderUuid,
    this.completionDate,
    this.reviewDate,
  });

  factory Review.fromJson(Map<String, dynamic> json) {
    return Review(
      id: json['id']?.toString() ?? '',
      name: json['name'] ?? '',
      rating: json['rating'] ?? 5,
      feedback: json['feedback'] ?? '',
      avatarUrl: json['avatar_url'],
      service: json['service'],
      orderUuid: json['order_uuid'],
      completionDate: json['completion_date'],
      reviewDate: json['review_date'],
    );
  }
}
