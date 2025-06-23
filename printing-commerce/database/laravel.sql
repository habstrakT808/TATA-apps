-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 23, 2025 at 04:21 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laravel`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` bigint UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_admin` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_telpon` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_auth` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `uuid`, `nama_admin`, `no_telpon`, `id_auth`) VALUES
(1, '402ac4ab-df77-4a36-9c50-a954ee6e7574', 'Super Admin', NULL, 1),
(27, 'ab819eb2-2b79-4087-8c6f-c719ca15c881', 'Hafiyan Admin Chat', '081368071919', 77),
(28, '4a1d9fed-6911-4b90-9b00-aeb267124ea6', 'Editor', NULL, 81);

-- --------------------------------------------------------

--
-- Table structure for table `auth`
--

CREATE TABLE `auth` (
  `id_auth` bigint UNSIGNED NOT NULL,
  `email` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('super_admin','admin_chat','admin_pemesanan','user','editor') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fcm_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `auth`
--

INSERT INTO `auth` (`id_auth`, `email`, `password`, `role`, `fcm_token`, `remember_token`) VALUES
(1, 'SuperAdmin@gmail.com', '$2y$12$.DTxTHdRQwgdn0XMwn8CLeEYtW12Jyh2DUdUGiHR/CLssoBdOGuN6', 'super_admin', NULL, NULL),
(77, 'adminchat@gmail.com', '$2y$12$kAIqdVykJFR4OKlmlnQVIuj8Bzg3BiKGxqbkQBo6p3s7JG0r8pvnm', 'admin_chat', NULL, NULL),
(79, 'hafiyancallahan@gmail.com', '$2y$12$VBJPU7OokJIt0b70hg1V4O71IJ/oBc0EzmlHrndBKOq.AzOegBX7y', 'user', NULL, NULL),
(80, 'jhodywiraputra@gmail.com', '$2y$12$hogAj2yukBtcd2yAsX8wQ.viJd2FeBkYT9lxAWbJlQ3k.6qRAZZ76', 'user', NULL, NULL),
(81, 'editor@gmail.com', '$2y$12$CBE9Kr/5XBgczo09HWbSJeynMK46/4yFEpQnRSqMZbRLDpLOBOuYC', 'editor', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `catatan_pesanan`
--

CREATE TABLE `catatan_pesanan` (
  `id_catatan_pesanan` bigint UNSIGNED NOT NULL,
  `catatan_pesanan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `gambar_referensi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_pesanan` bigint UNSIGNED NOT NULL,
  `id_user` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `catatan_pesanan`
--

INSERT INTO `catatan_pesanan` (`id_catatan_pesanan`, `catatan_pesanan`, `gambar_referensi`, `id_pesanan`, `id_user`) VALUES
(1, 'Buatkan logo untuk universitas saya! Dengan warna biru dan logo mirip dengan universitas Brawijaya!', 'R2LimEFhvIKu7i8TarZVazBmclkD51RdML8O1bYv.jpg', 1, 2),
(2, 'Buatkan banner untuk desain saya yaitu desain pantad hitam!', '3qHPX29gSuuuIZG3F5hYQngWZjYuXkR8lO7RaJ8z.png', 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `admin_id` bigint UNSIGNED DEFAULT NULL,
  `pesanan_uuid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_message` text COLLATE utf8mb4_unicode_ci,
  `unread_count` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chats`
--

INSERT INTO `chats` (`id`, `uuid`, `user_id`, `admin_id`, `pesanan_uuid`, `last_message`, `unread_count`, `created_at`, `updated_at`) VALUES
(1, 'c826e72a-d4a0-4435-86b8-b657f86bd5e8', 2, 27, '5010f568-a28a-4065-a97e-136bafdc3bee', '📷 Gambar', 0, '2025-06-22 12:01:15', '2025-06-22 12:34:41'),
(2, '35982b39-efc7-4b90-92d1-7704b678e889', 2, 27, 'cf064030-ff17-4388-a814-03402a5b90bd', 'Tes min! Buat banner dong', 0, '2025-06-22 14:08:26', '2025-06-22 14:08:49');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chat_uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender_type` enum('user','admin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `message_type` enum('text','image','file') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `file_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `uuid`, `chat_uuid`, `sender_id`, `sender_type`, `message`, `message_type`, `file_url`, `is_read`, `created_at`, `updated_at`) VALUES
(1, '4c4e0a3f-fa0a-40e4-8001-a934d9481b2c', 'c826e72a-d4a0-4435-86b8-b657f86bd5e8', '77', 'admin', 'Halo! Admin TATA siap membantu Anda terkait pesanan Desain Logo paket Premium #5010f568-a28a-4065-a97e-136bafdc3bee', 'text', NULL, 1, '2025-06-22 12:01:15', '2025-06-22 12:01:21'),
(2, '185e070d-50ea-4b26-a31f-5074dcd682ba', 'c826e72a-d4a0-4435-86b8-b657f86bd5e8', '77', 'admin', 'Silahkan sampaikan kebutuhan atau pertanyaan Anda terkait pesanan ini. Kami akan membantu sebaik mungkin.', 'text', NULL, 1, '2025-06-22 12:01:15', '2025-06-22 12:01:21'),
(3, 'df07f53d-1943-498a-bbe2-fd47f0bd05a8', 'c826e72a-d4a0-4435-86b8-b657f86bd5e8', '79', 'user', 'Tes Admin! Apakah anda bisa mendengar?', 'text', NULL, 1, '2025-06-22 12:01:33', '2025-06-22 12:02:09'),
(4, 'cdacff74-206a-45f7-b1b1-5bad46742be1', 'c826e72a-d4a0-4435-86b8-b657f86bd5e8', '77', 'admin', 'Tes! Halo! Pesan anda masuk! Silahkan lanjutkan!', 'text', NULL, 1, '2025-06-22 12:02:22', '2025-06-22 12:02:29'),
(5, '9e3b3391-a88e-445b-937c-19cf69990faf', 'c826e72a-d4a0-4435-86b8-b657f86bd5e8', '79', 'user', 'Mengirim gambar', 'image', 'http://localhost:8000/image-proxy.php?type=chat&file=b950a59c-2695-40e6-92b9-ea7480af6d10.jpg', 0, '2025-06-22 12:02:51', '2025-06-22 12:02:51'),
(6, 'b9bf6ede-ec83-4ae2-96cd-6de2c6131f2b', '35982b39-efc7-4b90-92d1-7704b678e889', '77', 'admin', 'Halo! Admin TATA siap membantu Anda terkait pesanan Desain Banner paket Premium #cf064030-ff17-4388-a814-03402a5b90bd', 'text', NULL, 1, '2025-06-22 14:08:26', '2025-06-22 14:08:33'),
(7, '4db803aa-7d83-4b8d-9c60-582d5677b60c', '35982b39-efc7-4b90-92d1-7704b678e889', '77', 'admin', 'Silahkan sampaikan kebutuhan atau pertanyaan Anda terkait pesanan ini. Kami akan membantu sebaik mungkin.', 'text', NULL, 1, '2025-06-22 14:08:26', '2025-06-22 14:08:33'),
(8, '5bb6798f-7302-4b35-a1df-7766f2cc93bb', '35982b39-efc7-4b90-92d1-7704b678e889', '79', 'user', 'Tes min! Buat banner dong', 'text', NULL, 0, '2025-06-22 14:08:38', '2025-06-22 14:08:38');

-- --------------------------------------------------------

--
-- Table structure for table `editor`
--

CREATE TABLE `editor` (
  `id_editor` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_editor` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_kelamin` enum('laki-laki','perempuan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_telpon` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `editor`
--

INSERT INTO `editor` (`id_editor`, `uuid`, `nama_editor`, `email`, `jenis_kelamin`, `no_telpon`, `created_at`, `updated_at`) VALUES
(9, '7004f5ce-a32f-4d0c-9e18-4030ae224589', 'Sket Editor', 'editor@gmail.com', NULL, '081368078181', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fcm_token`
--

CREATE TABLE `fcm_token` (
  `id_fcm_token` bigint UNSIGNED NOT NULL,
  `fcm_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fcm_token_updated_at` timestamp NULL DEFAULT NULL,
  `device_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_user` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jasa`
--

CREATE TABLE `jasa` (
  `id_jasa` bigint UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` enum('logo','banner','poster') COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi_jasa` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jasa`
--

INSERT INTO `jasa` (`id_jasa`, `uuid`, `kategori`, `deskripsi_jasa`) VALUES
(1, 'd3aacd6b-b888-4764-824d-6e60950ce63b', 'logo', 'Jasa pembuatan logo profesional untuk kebutuhan bisnis dan personal'),
(2, '435b663b-9058-4091-973a-d0293d0abf59', 'banner', 'Jasa pembuatan banner untuk kebutuhan promosi dan dekorasi'),
(3, '8158ac7d-d330-48df-b003-cd58b75212f9', 'poster', 'Jasa pembuatan poster untuk keperluan promosi dan acara');

-- --------------------------------------------------------

--
-- Table structure for table `jasa_images`
--

CREATE TABLE `jasa_images` (
  `id_jasa_image` bigint UNSIGNED NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_jasa` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jasa_images`
--

INSERT INTO `jasa_images` (`id_jasa_image`, `image_path`, `id_jasa`) VALUES
(1, 'placeholder_logo.jpg', 1),
(2, 'placeholder_banner.jpg', 2),
(3, 'placeholder_poster.jpg', 3);

-- --------------------------------------------------------

--
-- Table structure for table `metode_pembayaran`
--

CREATE TABLE `metode_pembayaran` (
  `id_metode_pembayaran` bigint UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_metode_pembayaran` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_metode_pembayaran` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi_1` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi_2` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bahan_poster` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Art Paper',
  `ukuran_poster` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A3',
  `total_harga_poster` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '150.000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `metode_pembayaran`
--

INSERT INTO `metode_pembayaran` (`id_metode_pembayaran`, `uuid`, `nama_metode_pembayaran`, `no_metode_pembayaran`, `deskripsi_1`, `deskripsi_2`, `thumbnail`, `icon`, `bahan_poster`, `ukuran_poster`, `total_harga_poster`) VALUES
(1, '75812ce2-6097-4508-815d-0798e1785519', 'BRI', '123456789', 'Rekening Ini digunakan untuk pembayaran', 'Pastikan transfer ke rekening yang benar', 'bri.jpg', 'bri-icon.png', 'Art Paper', 'A3', '150.000'),
(2, '56aca672-e865-47e6-b16d-1ff4bf71647a', 'Mandiri', '987654321', 'Rekening Ini digunakan untuk pembayaran', 'Pastikan transfer ke rekening yang benar', 'mandiri.jpg', 'mandiri-icon.png', 'Art Paper', 'A3', '150.000'),
(3, '4cd77ff1-cb79-4ab4-8315-67204f2175b7', 'OVO', '081234567890', 'Rekening Ini digunakan untuk pembayaran', 'Pastikan transfer ke rekening yang benar', 'ovo.jpg', 'ovo-icon.png', 'Art Paper', 'A3', '150.000'),
(4, '1fff718f-024f-44b4-af19-334b495045c7', 'Bank Mandiri', '1234567890', 'TATA Design Studio', 'Transfer ke rekening di atas', 'mandiri.png', 'mandiri-icon.png', 'Art Paper', 'A3', '150.000'),
(5, 'a610990f-7c5a-4b6b-9cae-f58582af266d', 'Bank BCA', '0987654321', 'TATA Design Studio', 'Transfer ke rekening di atas', 'bca.png', 'bca-icon.png', 'Art Paper', 'A3', '150.000'),
(6, '38fca570-62af-4bef-99bd-06f5a39f6f0f', 'Bank BRI', '1122334455', 'TATA Design Studio', 'Transfer ke rekening di atas', 'bri.png', 'bri-icon.png', 'Art Paper', 'A3', '150.000');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(2, '2019_08_19_000000_create_failed_jobs_table', 1),
(3, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(4, '2024_06_16_000000_create_auth', 1),
(5, '2024_06_16_100000_create_admin', 1),
(6, '2024_06_16_200000_create_users_table', 1),
(7, '2024_06_16_300000_create_editor', 1),
(8, '2024_06_17_000000_modify_auth_role_column', 1),
(9, '2024_06_17_100000_add_no_telpon_to_admin_table', 1),
(10, '2024_06_17_100001_add_email_to_editor_table', 1),
(11, '2025_05_06_135856_create_verifikasi_user', 1),
(12, '2025_05_06_135916_create_verifikasi_admin', 1),
(13, '2025_05_06_135925_create_jasa', 1),
(14, '2025_05_06_135930_create_jasa_images_table', 1),
(15, '2025_05_06_135932_create_paket_jasa', 1),
(16, '2025_05_06_135949_create_pesanan', 1),
(17, '2025_05_06_135950_create_catatan_pesanan_table', 1),
(18, '2025_05_06_135952_create_pesanan_revisi_table', 1),
(19, '2025_05_06_135955_create_revisi_editor_table', 1),
(20, '2025_05_06_135960_create_revisi_user_table', 1),
(21, '2025_05_06_140010_create_review', 1),
(22, '2025_05_06_140017_create_metode_pembayaran', 1),
(23, '2025_05_06_140029_create_transaksi', 1),
(24, '2025_06_15_133851_create_fcm_token_users_table', 1),
(25, '2025_06_19_060318_cleanup_payment_methods', 1),
(26, '2025_06_19_074756_add_poster_fields_to_metode_pembayaran', 1),
(27, '2025_06_19_081339_create_user_placeholder_image', 1),
(28, '2025_06_19_095317_create_chats_table', 1),
(29, '2025_06_19_095333_create_chat_messages_table', 1),
(30, '2025_06_20_165828_add_fcm_token_to_auth_table', 1),
(31, '2025_06_22_150809_create_statistik_pesanan_table', 2),
(32, '2025_06_22_184715_increase_foto_column_length_in_users_table', 3),
(33, '2025_06_22_122252_add_estimasi_and_hasil_fields_to_pesanan', 4),
(34, '2025_06_22_150808_add_client_confirmed_at_to_pesanan_table', 5),
(35, '2025_06_22_175921_add_nama_editor_to_pesanan_table', 6),
(36, '2025_06_22_191922_update_auth_table_add_editor_role', 7);

-- --------------------------------------------------------

--
-- Table structure for table `paket_jasa`
--

CREATE TABLE `paket_jasa` (
  `id_paket_jasa` bigint UNSIGNED NOT NULL,
  `kelas_jasa` enum('basic','standard','premium') COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi_singkat` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga_paket_jasa` int NOT NULL,
  `waktu_pengerjaan` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `maksimal_revisi` tinyint NOT NULL,
  `id_jasa` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `paket_jasa`
--

INSERT INTO `paket_jasa` (`id_paket_jasa`, `kelas_jasa`, `deskripsi_singkat`, `harga_paket_jasa`, `waktu_pengerjaan`, `maksimal_revisi`, `id_jasa`) VALUES
(1, 'basic', 'Paket basic untuk desain logo dengan 1x revisi', 50000, '3 hari', 1, 1),
(2, 'standard', 'Paket standard untuk desain logo dengan 3x revisi', 100000, '5 hari', 3, 1),
(3, 'premium', 'Paket premium untuk desain logo dengan 5x revisi dan prioritas', 200000, '7 hari', 5, 1),
(4, 'basic', 'Paket basic untuk desain banner dengan 1x revisi', 100000, '3 hari', 1, 2),
(5, 'standard', 'Paket standard untuk desain banner dengan 3x revisi', 200000, '5 hari', 3, 2),
(6, 'premium', 'Paket premium untuk desain banner dengan 5x revisi dan prioritas', 400000, '7 hari', 5, 2),
(7, 'basic', 'Paket basic untuk desain poster dengan 1x revisi', 75000, '3 hari', 1, 3),
(8, 'standard', 'Paket standard untuk desain poster dengan 3x revisi', 150000, '5 hari', 3, 3),
(9, 'premium', 'Paket premium untuk desain poster dengan 5x revisi dan prioritas', 300000, '7 hari', 5, 3);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\Auth', 78, 'google-auth-token', 'd505f29f81b341f42d96f1c69f4a3ba740611820a9b4e8e00ef7eb151e9948bd', '[\"mobile-access\"]', NULL, NULL, '2025-06-22 11:56:55', '2025-06-22 11:56:55'),
(2, 'App\\Models\\Auth', 78, 'google-auth-token', '1be7d095c912e11047d9d15e1ee62c702e9b3b19b686d3338fb7ad4d429271fd', '[\"mobile-access\"]', '2025-06-22 11:59:26', NULL, '2025-06-22 11:58:00', '2025-06-22 11:59:26'),
(5, 'App\\Models\\Auth', 80, 'google-auth-token', '0fe498a63ed4edc3c44b0a3a030bd4acfdcb5cfebb278abd44301c12d96b66e8', '[\"mobile-access\"]', '2025-06-22 12:33:21', NULL, '2025-06-22 12:10:52', '2025-06-22 12:33:21'),
(8, 'App\\Models\\Auth', 79, 'mobile-auth-token', '2c392a4ca2fe607e455615efcb6530efe30b71e4676c06bf12dfcffe4a2c22ca', '[\"mobile-access\"]', '2025-06-22 14:07:05', NULL, '2025-06-22 14:05:43', '2025-06-22 14:07:05'),
(9, 'App\\Models\\Auth', 79, 'mobile-auth-token', 'a84a57dae8c7eb389ba47098de6a02963db334a63fb6773536d8d70f9e0bd1e3', '[\"mobile-access\"]', '2025-06-22 14:11:09', NULL, '2025-06-22 14:07:05', '2025-06-22 14:11:09'),
(10, 'App\\Models\\Auth', 79, 'mobile-auth-token', '109858094441faf87227cd5208f1e0133fa13e8dacd8c711b4a961ef6712b553', '[\"mobile-access\"]', '2025-06-22 14:18:46', NULL, '2025-06-22 14:18:37', '2025-06-22 14:18:46'),
(11, 'App\\Models\\Auth', 79, 'mobile-auth-token', '9ceb6ba97add15673fdeb873a5b74e170ad3eeaa0109eb9bf9f31d2a29071d61', '[\"mobile-access\"]', '2025-06-22 14:33:57', NULL, '2025-06-22 14:33:46', '2025-06-22 14:33:57'),
(12, 'App\\Models\\Auth', 80, 'google-auth-token', '09a992d704b694ada0dbf3d62c3d738feb6f761f965a9506b29f7a593d30bee7', '[\"mobile-access\"]', '2025-06-22 14:35:26', NULL, '2025-06-22 14:35:02', '2025-06-22 14:35:26');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` bigint UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_pesanan` enum('pending','diproses','menunggu_editor','dikerjakan','revisi','menunggu_review','selesai','dibatalkan') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_pengerjaan` enum('menunggu','diproses','dikerjakan','selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `total_harga` int UNSIGNED NOT NULL,
  `estimasi_waktu` datetime NOT NULL,
  `estimasi_mulai` date DEFAULT NULL,
  `estimasi_selesai` date DEFAULT NULL,
  `file_hasil_desain` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `maksimal_revisi` tinyint UNSIGNED NOT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `assigned_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `client_confirmed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `id_jasa` bigint UNSIGNED NOT NULL,
  `id_paket_jasa` bigint UNSIGNED NOT NULL,
  `id_editor` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `uuid`, `deskripsi`, `status_pesanan`, `status_pengerjaan`, `total_harga`, `estimasi_waktu`, `estimasi_mulai`, `estimasi_selesai`, `file_hasil_desain`, `maksimal_revisi`, `confirmed_at`, `assigned_at`, `completed_at`, `client_confirmed_at`, `created_at`, `updated_at`, `id_user`, `id_jasa`, `id_paket_jasa`, `id_editor`) VALUES
(1, '5010f568-a28a-4065-a97e-136bafdc3bee', 'Buatkan logo untuk universitas saya! Dengan warna biru dan logo mirip dengan universitas Brawijaya!', 'pending', 'selesai', 200000, '2025-06-22 19:01:15', '2025-06-22', '2025-06-23', '1750620763_karang bebai 2.jpg', 5, NULL, NULL, NULL, '2025-06-22 14:06:46', '2025-06-22 12:01:15', '2025-06-22 14:06:46', 2, 1, 3, 9),
(2, 'cf064030-ff17-4388-a814-03402a5b90bd', 'Buatkan banner untuk desain saya yaitu desain pantad hitam!', 'pending', 'selesai', 400000, '2025-06-22 21:08:26', '2025-06-22', '2025-06-23', '1750626576_photo_2025-04-14_23-25-56.jpg', 5, NULL, NULL, NULL, '2025-06-22 14:10:13', '2025-06-22 14:08:26', '2025-06-22 14:10:13', 2, 2, 6, 9);

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `id_review` bigint UNSIGNED NOT NULL,
  `review` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` enum('1','2','3','4','5') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL,
  `id_pesanan` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`id_review`, `review`, `rating`, `created_at`, `id_pesanan`) VALUES
(1, 'book popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', '5', '2025-06-22 14:06:16', 1),
(2, 'Desainnya bagus banget, orangnya ganteng banget!', '5', '2025-06-22 14:10:24', 2);

-- --------------------------------------------------------

--
-- Table structure for table `revisi`
--

CREATE TABLE `revisi` (
  `id_revisi` bigint UNSIGNED NOT NULL,
  `urutan_revisi` tinyint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_pesanan` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `revisi_editor`
--

CREATE TABLE `revisi_editor` (
  `id_revisi_editor` bigint UNSIGNED NOT NULL,
  `nama_file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `catatan_editor` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_editor` bigint UNSIGNED NOT NULL,
  `id_revisi` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `revisi_user`
--

CREATE TABLE `revisi_user` (
  `id_revisi_user` bigint UNSIGNED NOT NULL,
  `nama_file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `catatan_user` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_revisi` bigint UNSIGNED DEFAULT NULL,
  `id_user` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `statistik_pesanan`
--

CREATE TABLE `statistik_pesanan` (
  `id` bigint UNSIGNED NOT NULL,
  `id_pesanan` bigint UNSIGNED NOT NULL,
  `pelanggan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_jasa` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `completed_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `statistik_pesanan`
--

INSERT INTO `statistik_pesanan` (`id`, `id_pesanan`, `pelanggan`, `jenis_jasa`, `total_harga`, `completed_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Hafiyan Al Muqaffi Umary', 'logo', 200000.00, '2025-06-22 14:04:58', '2025-06-22 14:04:58', '2025-06-22 14:04:58'),
(2, 1, 'Hafiyan Al Muqaffi Umary', 'logo', 200000.00, '2025-06-22 14:06:46', '2025-06-22 14:06:46', '2025-06-22 14:06:46'),
(3, 2, 'Hafiyan Al Muqaffi Umary', 'banner', 400000.00, '2025-06-22 14:10:13', '2025-06-22 14:10:13', '2025-06-22 14:10:13');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` bigint UNSIGNED NOT NULL,
  `order_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` int UNSIGNED NOT NULL,
  `status_transaksi` enum('belum_bayar','menunggu_konfirmasi','lunas','dibatalkan','expired') COLLATE utf8mb4_unicode_ci NOT NULL,
  `bukti_pembayaran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `waktu_pembayaran` datetime DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `catatan_transaksi` text COLLATE utf8mb4_unicode_ci,
  `alasan_penolakan` text COLLATE utf8mb4_unicode_ci,
  `expired_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_metode_pembayaran` bigint UNSIGNED NOT NULL,
  `id_pesanan` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `order_id`, `jumlah`, `status_transaksi`, `bukti_pembayaran`, `waktu_pembayaran`, `confirmed_at`, `catatan_transaksi`, `alasan_penolakan`, `expired_at`, `created_at`, `updated_at`, `id_metode_pembayaran`, `id_pesanan`) VALUES
(1, 'TRX-20250622-LTUEO1IT', 200000, 'belum_bayar', NULL, NULL, NULL, NULL, NULL, '2025-06-23 19:01:15', '2025-06-22 12:01:15', '2025-06-22 12:01:15', 1, 1),
(2, 'TRX-20250622-ERTCKOQ9', 400000, 'belum_bayar', NULL, NULL, NULL, NULL, NULL, '2025-06-23 21:08:26', '2025-06-22 14:08:26', '2025-06-22 14:08:26', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` bigint UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_user` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_kelamin` enum('laki-laki','perempuan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_telpon` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` varchar(400) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_rekening` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` text COLLATE utf8mb4_unicode_ci,
  `email_verified_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_auth` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `uuid`, `nama_user`, `jenis_kelamin`, `no_telpon`, `alamat`, `no_rekening`, `foto`, `email_verified_at`, `created_at`, `updated_at`, `id_auth`) VALUES
(2, '19fc265e-004b-4aaa-bed9-59a672588406', 'Hafiyan Al Muqaffi Umary', NULL, '081368071901', NULL, NULL, 'user_685852c041639.jpg', NULL, NULL, '2025-06-22 12:00:16', 79),
(3, '8d8e7c75-2d56-4fa5-bb46-d013ef9a3d37', 'Hafiyan Al Muqaffi Umary', NULL, NULL, NULL, NULL, 'https://lh3.googleusercontent.com/a/ACg8ocLdSqCTaDXAA24D8_IldpxU7sAzh9fhmuHyCj90QRiHKlckFxHS=s96-c', NULL, '2025-06-22 12:10:52', '2025-06-22 12:10:52', 80);

-- --------------------------------------------------------

--
-- Table structure for table `user_placeholder_image`
--

CREATE TABLE `user_placeholder_image` (
  `id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `verifikasi_admin`
--

CREATE TABLE `verifikasi_admin` (
  `id_verifikasi_admin` bigint UNSIGNED NOT NULL,
  `email` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_otp` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_verifikasi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` enum('password','email') COLLATE utf8mb4_unicode_ci NOT NULL,
  `terkirim` smallint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_admin` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `verifikasi_user`
--

CREATE TABLE `verifikasi_user` (
  `id_verifikasi_user` bigint UNSIGNED NOT NULL,
  `email` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_otp` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_verifikasi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` enum('password','email') COLLATE utf8mb4_unicode_ci NOT NULL,
  `terkirim` smallint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_user` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD KEY `admin_id_auth_foreign` (`id_auth`);

--
-- Indexes for table `auth`
--
ALTER TABLE `auth`
  ADD PRIMARY KEY (`id_auth`);

--
-- Indexes for table `catatan_pesanan`
--
ALTER TABLE `catatan_pesanan`
  ADD PRIMARY KEY (`id_catatan_pesanan`),
  ADD KEY `catatan_pesanan_id_pesanan_foreign` (`id_pesanan`),
  ADD KEY `catatan_pesanan_id_user_foreign` (`id_user`);

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chats_uuid_unique` (`uuid`),
  ADD KEY `chats_user_id_index` (`user_id`),
  ADD KEY `chats_admin_id_index` (`admin_id`),
  ADD KEY `chats_pesanan_uuid_index` (`pesanan_uuid`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chat_messages_uuid_unique` (`uuid`),
  ADD KEY `chat_messages_chat_uuid_index` (`chat_uuid`),
  ADD KEY `chat_messages_sender_id_index` (`sender_id`),
  ADD KEY `chat_messages_sender_type_index` (`sender_type`),
  ADD KEY `chat_messages_is_read_index` (`is_read`);

--
-- Indexes for table `editor`
--
ALTER TABLE `editor`
  ADD PRIMARY KEY (`id_editor`),
  ADD UNIQUE KEY `editor_uuid_unique` (`uuid`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `fcm_token`
--
ALTER TABLE `fcm_token`
  ADD PRIMARY KEY (`id_fcm_token`),
  ADD KEY `fcm_token_id_user_foreign` (`id_user`);

--
-- Indexes for table `jasa`
--
ALTER TABLE `jasa`
  ADD PRIMARY KEY (`id_jasa`);

--
-- Indexes for table `jasa_images`
--
ALTER TABLE `jasa_images`
  ADD PRIMARY KEY (`id_jasa_image`),
  ADD KEY `jasa_images_id_jasa_foreign` (`id_jasa`);

--
-- Indexes for table `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  ADD PRIMARY KEY (`id_metode_pembayaran`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paket_jasa`
--
ALTER TABLE `paket_jasa`
  ADD PRIMARY KEY (`id_paket_jasa`),
  ADD KEY `paket_jasa_id_jasa_foreign` (`id_jasa`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD KEY `pesanan_id_user_foreign` (`id_user`),
  ADD KEY `pesanan_id_jasa_foreign` (`id_jasa`),
  ADD KEY `pesanan_id_paket_jasa_foreign` (`id_paket_jasa`),
  ADD KEY `pesanan_id_editor_foreign` (`id_editor`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id_review`),
  ADD KEY `review_id_pesanan_foreign` (`id_pesanan`);

--
-- Indexes for table `revisi`
--
ALTER TABLE `revisi`
  ADD PRIMARY KEY (`id_revisi`),
  ADD KEY `revisi_id_pesanan_foreign` (`id_pesanan`);

--
-- Indexes for table `revisi_editor`
--
ALTER TABLE `revisi_editor`
  ADD PRIMARY KEY (`id_revisi_editor`),
  ADD KEY `revisi_editor_id_editor_foreign` (`id_editor`),
  ADD KEY `revisi_editor_id_revisi_foreign` (`id_revisi`);

--
-- Indexes for table `revisi_user`
--
ALTER TABLE `revisi_user`
  ADD PRIMARY KEY (`id_revisi_user`),
  ADD KEY `revisi_user_id_revisi_foreign` (`id_revisi`),
  ADD KEY `revisi_user_id_user_foreign` (`id_user`);

--
-- Indexes for table `statistik_pesanan`
--
ALTER TABLE `statistik_pesanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `statistik_pesanan_id_pesanan_foreign` (`id_pesanan`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD UNIQUE KEY `transaksi_order_id_unique` (`order_id`),
  ADD KEY `transaksi_id_metode_pembayaran_foreign` (`id_metode_pembayaran`),
  ADD KEY `transaksi_id_pesanan_foreign` (`id_pesanan`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `users_id_auth_foreign` (`id_auth`);

--
-- Indexes for table `user_placeholder_image`
--
ALTER TABLE `user_placeholder_image`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `verifikasi_admin`
--
ALTER TABLE `verifikasi_admin`
  ADD PRIMARY KEY (`id_verifikasi_admin`),
  ADD KEY `verifikasi_admin_id_admin_foreign` (`id_admin`);

--
-- Indexes for table `verifikasi_user`
--
ALTER TABLE `verifikasi_user`
  ADD PRIMARY KEY (`id_verifikasi_user`),
  ADD KEY `verifikasi_user_id_user_foreign` (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `auth`
--
ALTER TABLE `auth`
  MODIFY `id_auth` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `catatan_pesanan`
--
ALTER TABLE `catatan_pesanan`
  MODIFY `id_catatan_pesanan` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `editor`
--
ALTER TABLE `editor`
  MODIFY `id_editor` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fcm_token`
--
ALTER TABLE `fcm_token`
  MODIFY `id_fcm_token` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jasa`
--
ALTER TABLE `jasa`
  MODIFY `id_jasa` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `jasa_images`
--
ALTER TABLE `jasa_images`
  MODIFY `id_jasa_image` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  MODIFY `id_metode_pembayaran` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `paket_jasa`
--
ALTER TABLE `paket_jasa`
  MODIFY `id_paket_jasa` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `id_review` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `revisi`
--
ALTER TABLE `revisi`
  MODIFY `id_revisi` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `revisi_editor`
--
ALTER TABLE `revisi_editor`
  MODIFY `id_revisi_editor` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `revisi_user`
--
ALTER TABLE `revisi_user`
  MODIFY `id_revisi_user` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `statistik_pesanan`
--
ALTER TABLE `statistik_pesanan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_placeholder_image`
--
ALTER TABLE `user_placeholder_image`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `verifikasi_admin`
--
ALTER TABLE `verifikasi_admin`
  MODIFY `id_verifikasi_admin` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `verifikasi_user`
--
ALTER TABLE `verifikasi_user`
  MODIFY `id_verifikasi_user` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_id_auth_foreign` FOREIGN KEY (`id_auth`) REFERENCES `auth` (`id_auth`) ON DELETE CASCADE;

--
-- Constraints for table `catatan_pesanan`
--
ALTER TABLE `catatan_pesanan`
  ADD CONSTRAINT `catatan_pesanan_id_pesanan_foreign` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE,
  ADD CONSTRAINT `catatan_pesanan_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `chats`
--
ALTER TABLE `chats`
  ADD CONSTRAINT `chats_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL,
  ADD CONSTRAINT `chats_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`) ON DELETE SET NULL;

--
-- Constraints for table `fcm_token`
--
ALTER TABLE `fcm_token`
  ADD CONSTRAINT `fcm_token_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `jasa_images`
--
ALTER TABLE `jasa_images`
  ADD CONSTRAINT `jasa_images_id_jasa_foreign` FOREIGN KEY (`id_jasa`) REFERENCES `jasa` (`id_jasa`) ON DELETE CASCADE;

--
-- Constraints for table `paket_jasa`
--
ALTER TABLE `paket_jasa`
  ADD CONSTRAINT `paket_jasa_id_jasa_foreign` FOREIGN KEY (`id_jasa`) REFERENCES `jasa` (`id_jasa`) ON DELETE CASCADE;

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_id_editor_foreign` FOREIGN KEY (`id_editor`) REFERENCES `editor` (`id_editor`) ON DELETE SET NULL,
  ADD CONSTRAINT `pesanan_id_jasa_foreign` FOREIGN KEY (`id_jasa`) REFERENCES `jasa` (`id_jasa`) ON DELETE CASCADE,
  ADD CONSTRAINT `pesanan_id_paket_jasa_foreign` FOREIGN KEY (`id_paket_jasa`) REFERENCES `paket_jasa` (`id_paket_jasa`) ON DELETE CASCADE,
  ADD CONSTRAINT `pesanan_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_id_pesanan_foreign` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE;

--
-- Constraints for table `revisi`
--
ALTER TABLE `revisi`
  ADD CONSTRAINT `revisi_id_pesanan_foreign` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE;

--
-- Constraints for table `revisi_editor`
--
ALTER TABLE `revisi_editor`
  ADD CONSTRAINT `revisi_editor_id_editor_foreign` FOREIGN KEY (`id_editor`) REFERENCES `editor` (`id_editor`) ON DELETE CASCADE,
  ADD CONSTRAINT `revisi_editor_id_revisi_foreign` FOREIGN KEY (`id_revisi`) REFERENCES `revisi` (`id_revisi`) ON DELETE CASCADE;

--
-- Constraints for table `revisi_user`
--
ALTER TABLE `revisi_user`
  ADD CONSTRAINT `revisi_user_id_revisi_foreign` FOREIGN KEY (`id_revisi`) REFERENCES `revisi` (`id_revisi`) ON DELETE CASCADE,
  ADD CONSTRAINT `revisi_user_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `statistik_pesanan`
--
ALTER TABLE `statistik_pesanan`
  ADD CONSTRAINT `statistik_pesanan_id_pesanan_foreign` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_id_metode_pembayaran_foreign` FOREIGN KEY (`id_metode_pembayaran`) REFERENCES `metode_pembayaran` (`id_metode_pembayaran`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaksi_id_pesanan_foreign` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_id_auth_foreign` FOREIGN KEY (`id_auth`) REFERENCES `auth` (`id_auth`) ON DELETE CASCADE;

--
-- Constraints for table `verifikasi_admin`
--
ALTER TABLE `verifikasi_admin`
  ADD CONSTRAINT `verifikasi_admin_id_admin_foreign` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE CASCADE;

--
-- Constraints for table `verifikasi_user`
--
ALTER TABLE `verifikasi_user`
  ADD CONSTRAINT `verifikasi_user_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
