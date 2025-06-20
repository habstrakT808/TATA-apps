-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2025 at 06:25 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tata_printing_testing`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `nama_admin` varchar(50) NOT NULL,
  `id_auth` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `uuid`, `nama_admin`, `id_auth`) VALUES
(1, '5b325ecb-7833-4600-8967-3322b1d0cfd0', 'Super Admin', 1),
(2, 'e4bbc1b7-187f-4a9c-9912-8b245d017e8a', 'Admin Chat1', 2),
(3, '7a19e6d4-bfec-459f-b918-fd32c5f9ca86', 'Admin Chat2', 3),
(4, '6cc147b4-4f11-4b57-8827-9c395d91563f', 'Admin Chat3', 4),
(5, 'b1275a71-c9a9-4566-89c5-ebb72bb23f0d', 'Admin Chat4', 5),
(6, '476e7da4-a477-4df0-b6e4-c3cc4577daf4', 'Admin Chat5', 6),
(7, '2b4ab9d3-30dc-4b52-a567-b4a72f856391', 'Admin Chat6', 7),
(8, '3786ff9f-f0a6-4598-9803-c4715e498104', 'Admin Chat7', 8),
(9, '85c548ca-ae6a-4285-89d3-f90730e49355', 'Admin Chat8', 9),
(10, '1aca0ab7-228a-425e-ab34-bac24982adb9', 'Admin Chat9', 10),
(11, 'b24e072b-6fc5-4c43-93db-bde5f0b3fb95', 'Admin Chat10', 11),
(12, '3a4afefd-2847-49dc-9c19-020ea6933a82', 'Admin Pemesanan1', 12),
(13, '265706a3-008c-4a78-b0d5-828ab18a5cbb', 'Admin Pemesanan2', 13),
(14, '48c3ca40-47d1-4d5e-a800-1b48b2815694', 'Admin Pemesanan3', 14),
(15, '8f9087a6-13ad-4d2a-a26b-832e31e51c09', 'Admin Pemesanan4', 15),
(16, '7be8b860-a276-41dd-a51e-c91897aa7d2b', 'Admin Pemesanan5', 16),
(17, '6098580a-bec8-474c-86cc-75d06914750d', 'Admin Pemesanan6', 17),
(18, 'a4cf3cb0-54e5-41d3-b9e4-8472fda6d450', 'Admin Pemesanan7', 18),
(19, '985cc9ae-abe8-4762-b674-a93f8fb165a2', 'Admin Pemesanan8', 19),
(20, 'da5b9e79-ca84-4224-804c-6da5e33b7817', 'Admin Pemesanan9', 20),
(21, 'f2b2c0c7-1e61-47ee-bc45-d78ee9998ca8', 'Admin Pemesanan10', 21);

-- --------------------------------------------------------

--
-- Table structure for table `auth`
--

CREATE TABLE `auth` (
  `id_auth` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(45) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin_chat','admin_pemesanan','user') NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `auth`
--

INSERT INTO `auth` (`id_auth`, `email`, `password`, `role`, `remember_token`) VALUES
(1, 'SuperAdmin@gmail.com', '$2y$12$eYTxl3klfjhOfO.BsvkByODoawb.NPFL1fZm9YyIKEUAfAoHcwtuq', 'super_admin', NULL),
(2, 'AdminTestingChat1@gmail.com', '$2y$12$7ZUrL.0TgBJqSkUm.EO8R.ArqlWyS6nTskV1bB0Z0x7xAtTluB3ka', 'admin_chat', NULL),
(3, 'AdminTestingChat2@gmail.com', '$2y$12$zIakUt501k4kIaO.Cc3ateHA6JWU8z5eG.jfjxJhva7A2DKEZkjtW', 'admin_chat', NULL),
(4, 'AdminTestingChat3@gmail.com', '$2y$12$e4Kysc2FooCAImdSlHIhZO1oCFa62xw9LtT7eh7F0cyFWCOKhLpSG', 'admin_chat', NULL),
(5, 'AdminTestingChat4@gmail.com', '$2y$12$xNzy.hvKNEiKMSiyIxVIqeAIa9S8jW84rpzfp9oUExeNvuXUGEpz.', 'admin_chat', NULL),
(6, 'AdminTestingChat5@gmail.com', '$2y$12$mL7Xo6nJavRftCuruSGyLuccIdHH6aaKmtDUHjNM8dr7rQ0XZgf9m', 'admin_chat', NULL),
(7, 'AdminTestingChat6@gmail.com', '$2y$12$sFbfzNjG73XafWsL9yBKV.tGk3vag8Ca87tGIA38CAZ8JLGx8Lpk6', 'admin_chat', NULL),
(8, 'AdminTestingChat7@gmail.com', '$2y$12$GTsv93Mu97k5WNmo8L3t1ecVurRt2NhYDqoTzi0DISFLp40BzuZX2', 'admin_chat', NULL),
(9, 'AdminTestingChat8@gmail.com', '$2y$12$tZqb16YHAh0fAwVWu.hh6uQeV800xGgh81cscRGN96CiOC8ZnnF9O', 'admin_chat', NULL),
(10, 'AdminTestingChat9@gmail.com', '$2y$12$35s9ve0SqBnCZGzpXl5o3uNKZdt49kSiufFJTMAWK9OM0g4hjtQMy', 'admin_chat', NULL),
(11, 'AdminTestingChat10@gmail.com', '$2y$12$jkiHUm5FymK69mTuuRqVfekCctV/ROQaZ52b7zElxtyklindBRZ4u', 'admin_chat', NULL),
(12, 'AdminTestingPemesanan1@gmail.com', '$2y$12$CvPSLYyQzmqy4tBXWQHT/elavVDVP6ESapbWH1MTzcs7f7FIv3ORe', 'admin_pemesanan', NULL),
(13, 'AdminTestingPemesanan2@gmail.com', '$2y$12$zZMKq9xO3FNB/jGpjZY03Osf1aNVHCoZ9Ckt4ZXWNtvZ.61J77O2W', 'admin_pemesanan', NULL),
(14, 'AdminTestingPemesanan3@gmail.com', '$2y$12$D19ahGj6UDRHPeFxaBXASOfqdaiwspoWiLmHOzx.QkPFYV8Jz772u', 'admin_pemesanan', NULL),
(15, 'AdminTestingPemesanan4@gmail.com', '$2y$12$lPIKZDz4lPul89/p78b6SevtM8EQmkvAfOmT5GBARc4zKZw7exmea', 'admin_pemesanan', NULL),
(16, 'AdminTestingPemesanan5@gmail.com', '$2y$12$VdA7yy6wpxRj3eAIQ7hNze8n3n2ZO6a9mqXUqFJH3LMFu31RpzpSe', 'admin_pemesanan', NULL),
(17, 'AdminTestingPemesanan6@gmail.com', '$2y$12$47XyAVtDtKGaJN4uAqovmeC7iRb.jsP60rZC68T8A7Z7ZQJBKEo3i', 'admin_pemesanan', NULL),
(18, 'AdminTestingPemesanan7@gmail.com', '$2y$12$HF2HXrrRmHqzUZQcSEMqRehWV/osh8vCt5mJ.lLOuRjVVja4y3zpq', 'admin_pemesanan', NULL),
(19, 'AdminTestingPemesanan8@gmail.com', '$2y$12$9hvodIEfa9hatvM4qZxhJO7Bc98K8J9oxDl3dBaIXH9UcjBhuCCM2', 'admin_pemesanan', NULL),
(20, 'AdminTestingPemesanan9@gmail.com', '$2y$12$SVJLnlLjcAECxqhaQrlhY.lz5o/Cmdp4OnKiVWYY.RJQD2NtP7aay', 'admin_pemesanan', NULL),
(21, 'AdminTestingPemesanan10@gmail.com', '$2y$12$7v5ndeLJGcZzHKdTaaUCKeaDCFH5e9Q/lVqj8Phe4TewERZajjQdi', 'admin_pemesanan', NULL),
(22, 'UserTesting1@gmail.com', '$2y$12$b6WraaPKlz54qKeaxenqrOmFbwxnOwTPsbVpxNswB16uH0hPms4ay', 'user', NULL),
(23, 'UserTesting2@gmail.com', '$2y$12$EYxic4JXK.SFY8ECPZlAnuF4.CFjMmZxJb8kyD9h7IYwcjcxXny5.', 'user', NULL),
(24, 'UserTesting3@gmail.com', '$2y$12$swNKq.nSty06aZ2..HVqAuKTTEihhcEqnKttdmfLsfB.gpsc0wf62', 'user', NULL),
(25, 'UserTesting4@gmail.com', '$2y$12$VnGUOi8oJ0jxbBRJJboSG.UYtWZ6br6MG7ghwKtXO8yvmQlFU8lae', 'user', NULL),
(26, 'UserTesting5@gmail.com', '$2y$12$KqAjGX1dagq7n.phKGLGJeZo61rUY8ofS2dX5ZNyOWLxrTdRm37RC', 'user', NULL),
(27, 'UserTesting6@gmail.com', '$2y$12$7LlFOqyl8r5IlhXhgFc5MufN8eMY.Log2UxtSxns8srucuGv72Ely', 'user', NULL),
(28, 'UserTesting7@gmail.com', '$2y$12$moaKnNQXPErdWeNQysCqduhrMzTUjyEJpKgigXdmIhyUUKcGfJC9G', 'user', NULL),
(29, 'UserTesting8@gmail.com', '$2y$12$Rewlh6P84ZYmQBh2db3r1eu8RaTjA87kqsP0cPEssC66euuQ48hRe', 'user', NULL),
(30, 'UserTesting9@gmail.com', '$2y$12$A/OSkA2fM2wCkK1Ydd/yIeRucsZg608bSVNO2.yx4zwSAIVffHvzm', 'user', NULL),
(31, 'UserTesting10@gmail.com', '$2y$12$vrIJNq0zmSm6Kwo/cLFjT.KeIyEgL4T.OeKmbTvtzK5sfcci1bmtW', 'user', NULL),
(32, 'UserTesting11@gmail.com', '$2y$12$nkSPUArjKreLkc0cz6MEg.EbVOhl1JPay39q.ZriDS25UNqoenJxG', 'user', NULL),
(33, 'UserTesting12@gmail.com', '$2y$12$CtSIJ7FPGl3KaGEOAnNg5u5cm7uVhJg8rq4.NMDNDI0EjDFPenAoO', 'user', NULL),
(34, 'UserTesting13@gmail.com', '$2y$12$HqAMawuyYZuPINnJJRzCveQ.5gLnO19IT9S0PlIrc2tQ/jK/efu1e', 'user', NULL),
(35, 'UserTesting14@gmail.com', '$2y$12$nzeciR21Uz0cZb9yB40Mfe9nYEg.elbYQGMpiyD.vX9LjqtQQn1fa', 'user', NULL),
(36, 'UserTesting15@gmail.com', '$2y$12$qTx6GaeOx4K3ozIKlG5yFOeL3OBW.nf.HW5sGrN54o.RJRv/igBIi', 'user', NULL),
(37, 'UserTesting16@gmail.com', '$2y$12$ZO/0ImtWmoOc0ig1eiYdyeqN.hOXqL46y7WM7NFebGoO6as3LaD/q', 'user', NULL),
(38, 'UserTesting17@gmail.com', '$2y$12$cEygfmB0Z5Mv5HYGhC4ovuBEqrDgX6yqpJjLpjxsRAXnW2RQEM2qi', 'user', NULL),
(39, 'UserTesting18@gmail.com', '$2y$12$CCeIwI9Yvi1nMi4uOFW3x.miHQiD10TPgA.Ep64dKJHxdTgWQUONi', 'user', NULL),
(40, 'UserTesting19@gmail.com', '$2y$12$8/rFdQXY2BUxiPIa1Rvql.S.BqOERfCjDd9DSNoYLUgAluQyn4Cke', 'user', NULL),
(41, 'UserTesting20@gmail.com', '$2y$12$2asFjvkwOOooHYX1h6Wh5ujnafYjJEn0mPBZYsZ07kCxlC2hTM08q', 'user', NULL),
(42, 'UserTesting21@gmail.com', '$2y$12$Me0QIuzIM92/.UrIVOLfpetuPZF2XFeD7pB6VHCWKNXInkDmmjl9S', 'user', NULL),
(43, 'UserTesting22@gmail.com', '$2y$12$7WY/AiI5jLzSlwfuYXIB1.A2MWdfeME0YeCEmTMSCg16JpaePpq0y', 'user', NULL),
(44, 'UserTesting23@gmail.com', '$2y$12$mNAyy/Bjx2IYodyE99RRteXHCD3mRDkuPclRJH4bLoB3IZnhX7EzC', 'user', NULL),
(45, 'UserTesting24@gmail.com', '$2y$12$z7Gwg/w2UvYHqqoJ9iFPxurRAzhXNpKFQj2zNxlhNa4OleFQdwtLe', 'user', NULL),
(46, 'UserTesting25@gmail.com', '$2y$12$LRqHs.wQHjDSZAFMZvrGku79ftS.Glj9NLVsjsOmJlER470nK5Od6', 'user', NULL),
(47, 'UserTesting26@gmail.com', '$2y$12$klBFKN9w3VtFNxZoWT2V/OMMmoaMKYmfJ5dZY59lEBWiqIQfi5DTy', 'user', NULL),
(48, 'UserTesting27@gmail.com', '$2y$12$XnwAjb9vyY.NkrMMrHuTvewG5kwf2yi3ejz.8RgmtAAVN5ii1QGbW', 'user', NULL),
(49, 'UserTesting28@gmail.com', '$2y$12$0/CP5uK1LvULSuVCBTAkd.yqoGfaUbHY//u2yLJgKM/rYY4DDCmuW', 'user', NULL),
(50, 'UserTesting29@gmail.com', '$2y$12$IwWWFUGhj5IjbIHK9AuOzuJeyLYnHKYrwaQjQLXM0Kx3quue1gR0e', 'user', NULL),
(51, 'UserTesting30@gmail.com', '$2y$12$dWFb5mZpYCQWqFpTuEATYeulHuoQAwHqk4d4r72JJW90lGFyKm3V2', 'user', NULL),
(52, 'UserTesting31@gmail.com', '$2y$12$ihe6lpbAmnqMilT2WUc3w.i8Gd3HGOAexuGdLSWIfRe/21c2xFgqe', 'user', NULL),
(53, 'UserTesting32@gmail.com', '$2y$12$ZE4zOlWBzQ9W4zlm1/I9ouTtRRb98ABUmMjjhtKFFJdrSnN9KcjSK', 'user', NULL),
(54, 'UserTesting33@gmail.com', '$2y$12$KztUmxhU17HWCN8QsvTowujU9nKV9OsGjzekP550Xt8j5hpOr0amO', 'user', NULL),
(55, 'UserTesting34@gmail.com', '$2y$12$nUArTBimiDN4Ecp5HIegw.CoQxvAqEanJ.GtD4HB4gfegg8kCaAJ.', 'user', NULL),
(56, 'UserTesting35@gmail.com', '$2y$12$ejob9gae8ad3cQXDAbPnKO.wbfsJnedXygfAzEo5YdMV7nlzX0sLi', 'user', NULL),
(57, 'UserTesting36@gmail.com', '$2y$12$Bm4oHJFnSuWQ8qC7TzRgl.arqfyCeXTKxuskYDsahAUviq1zWDmwi', 'user', NULL),
(58, 'UserTesting37@gmail.com', '$2y$12$3d1Hg5q0bnSwv55tXGr9bORR7TyvvJueIs0MU1YilEwLqoK7KXGoa', 'user', NULL),
(59, 'UserTesting38@gmail.com', '$2y$12$is7OHYd1R0HIDduYFuckmOJ0XrxGrjFMHHtHKzjzerGrzvlVbd8xO', 'user', NULL),
(60, 'UserTesting39@gmail.com', '$2y$12$5KtFf3OFr4yaRyHnJ8sJ1ujAabEaLGs86cQjsNtKOfZYUkxx2Kwh2', 'user', NULL),
(61, 'UserTesting40@gmail.com', '$2y$12$1ZZZoGhMwjazULQUXQXU1O4xPQM0GBUeqxvKJJYjdacUS1PmkBe/G', 'user', NULL),
(62, 'UserTesting41@gmail.com', '$2y$12$lMH4Xa3upA0xRloebbX2uOVEJKal0/4B1mVSIAwRR11RS3Nh2vUTS', 'user', NULL),
(63, 'UserTesting42@gmail.com', '$2y$12$N8Up/apGdFmyMM7WsOCRSOOLA7HC4QDy4Ut.jRJOIpxpcwaN7zcv.', 'user', NULL),
(64, 'UserTesting43@gmail.com', '$2y$12$ckR413GalWvv.4xL8br9BedLPlPRMoCPjTGs4RgJ12IZFyOyd261i', 'user', NULL),
(65, 'UserTesting44@gmail.com', '$2y$12$4gTiS3fQ6/05hyC2aHT/p.Er8UJ0wUYAPmwDxXfuszfOj2QDhuFYa', 'user', NULL),
(66, 'UserTesting45@gmail.com', '$2y$12$zC.PBXp6aMmwwX8LiTFe5OH4e7D0UdVumGcFDUlWW16l1jITY914u', 'user', NULL),
(67, 'UserTesting46@gmail.com', '$2y$12$YckD3JsVg2.6iegsmV6oPOh.pzilLWccpusHF3iaAzMs9bNUtcohO', 'user', NULL),
(68, 'UserTesting47@gmail.com', '$2y$12$3qsMhq99yNFRNdnFBRm/B.sB8vht8g27dtPS8X6GPqHSXEz9TZwEy', 'user', NULL),
(69, 'UserTesting48@gmail.com', '$2y$12$OuBUyoYlmEFT.E/1t9xYmesus1A/yEIH2Pegv2U98uNe/jPlv5SGS', 'user', NULL),
(70, 'UserTesting49@gmail.com', '$2y$12$7m46ZSKnpY/XZQLVWHh2RecQVTw1oKV5pBB7lQWmjKq3GdvCDILlq', 'user', NULL),
(71, 'UserTesting50@gmail.com', '$2y$12$2mXfpXmucGpMy83IU/siVePHRZvpaKIHpxogtECsjCgrcTyAr6LRi', 'user', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `catatan_pesanan`
--

CREATE TABLE `catatan_pesanan` (
  `id_catatan_pesanan` bigint(20) UNSIGNED NOT NULL,
  `catatan_pesanan` text NOT NULL,
  `gambar_referensi` varchar(255) DEFAULT NULL,
  `id_pesanan` bigint(20) UNSIGNED NOT NULL,
  `id_user` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `editor`
--

CREATE TABLE `editor` (
  `id_editor` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `nama_editor` varchar(50) NOT NULL,
  `jenis_kelamin` enum('laki-laki','perempuan') DEFAULT NULL,
  `no_telpon` varchar(15) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `editor`
--

INSERT INTO `editor` (`id_editor`, `uuid`, `nama_editor`, `jenis_kelamin`, `no_telpon`, `created_at`, `updated_at`) VALUES
(1, '509e6987-019b-48fa-9da3-53fc12437fbf', 'Editor 1', 'perempuan', '085576191379', NULL, NULL),
(2, 'b7bf3633-150b-45c9-b452-842ca1a9e5f7', 'Editor 2', 'laki-laki', '085595310033', NULL, NULL),
(3, '2904bd9d-866e-468d-9eb1-5bb7dff76920', 'Editor 3', 'laki-laki', '08559613205', NULL, NULL),
(4, '5f12c586-3a2d-4642-9493-bd953dd29c47', 'Editor 4', 'laki-laki', '085520856594', NULL, NULL),
(5, '051dbfde-c57d-4098-bfb3-715a5f1b5293', 'Editor 5', 'perempuan', '085599649321', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fcm_token`
--

CREATE TABLE `fcm_token` (
  `id_fcm_token` bigint(20) UNSIGNED NOT NULL,
  `fcm_token` varchar(255) DEFAULT NULL,
  `fcm_token_updated_at` timestamp NULL DEFAULT NULL,
  `device_id` varchar(255) DEFAULT NULL,
  `device_type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_user` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jasa`
--

CREATE TABLE `jasa` (
  `id_jasa` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `kategori` enum('logo','banner','poster') NOT NULL,
  `deskripsi_jasa` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jasa`
--

INSERT INTO `jasa` (`id_jasa`, `uuid`, `kategori`, `deskripsi_jasa`) VALUES
(1, '198ec1a3-6da5-46a6-9662-79183f0daf4a', 'logo', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Nulla, placeat? Vitae quam error laudantium suscipit nulla soluta? Vero nobis cupiditate, quam similique nisi eligendi veniam nesciunt odit ipsam, at quis?'),
(2, '270ac37d-be9b-4f82-95e0-0664f5a1638b', 'banner', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Nulla, placeat? Vitae quam error laudantium suscipit nulla soluta? Vero nobis cupiditate, quam similique nisi eligendi veniam nesciunt odit ipsam, at quis?'),
(3, '229897e4-3e97-40da-a701-8b94e92d201a', 'poster', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Nulla, placeat? Vitae quam error laudantium suscipit nulla soluta? Vero nobis cupiditate, quam similique nisi eligendi veniam nesciunt odit ipsam, at quis?');

-- --------------------------------------------------------

--
-- Table structure for table `jasa_images`
--

CREATE TABLE `jasa_images` (
  `id_jasa_image` bigint(20) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `id_jasa` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jasa_images`
--

INSERT INTO `jasa_images` (`id_jasa_image`, `image_path`, `id_jasa`) VALUES
(1, '11.png', 1),
(2, '12.jpg', 1),
(3, '13.jpg', 1),
(4, '14.png', 1),
(5, '15.jpg', 1),
(6, '21.jpg', 2),
(7, '22.jpg', 2),
(8, '23.png', 2),
(9, '24.jpg', 2),
(10, '25.jpg', 2),
(11, '31.jpg', 3),
(12, '32.jpg', 3),
(13, '33.jpg', 3),
(14, '34.jpg', 3);

-- --------------------------------------------------------

--
-- Table structure for table `metode_pembayaran`
--

CREATE TABLE `metode_pembayaran` (
  `id_metode_pembayaran` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `nama_metode_pembayaran` varchar(12) NOT NULL,
  `no_metode_pembayaran` varchar(20) NOT NULL,
  `deskripsi_1` varchar(500) NOT NULL,
  `deskripsi_2` varchar(500) NOT NULL,
  `thumbnail` varchar(50) NOT NULL,
  `icon` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `metode_pembayaran`
--

INSERT INTO `metode_pembayaran` (`id_metode_pembayaran`, `uuid`, `nama_metode_pembayaran`, `no_metode_pembayaran`, `deskripsi_1`, `deskripsi_2`, `thumbnail`, `icon`) VALUES
(1, '97f561ee-d052-476c-afc2-8da83c5808c4', 'BRI', '973530284542', 'fwffw', 'disi', '1.jpg', '1.jpeg'),
(2, '974d1cb7-8a2b-4a82-ae75-aa1a371244da', 'BCA', '973530284542', 'fwffw', 'disi', '1.jpg', '1.jpeg'),
(3, '9c18fad2-4f02-45fe-b3dc-036b438ceacf', 'GOPAY', '973530284542', 'fwffw', 'disi', '1.jpg', '1.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(2, '2019_08_19_000000_create_failed_jobs_table', 1),
(3, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(4, '2025_05_06_135749_create_auth', 1),
(5, '2025_05_06_135770_create_users_table', 1),
(6, '2025_05_06_135856_create_verifikasi_user', 1),
(7, '2025_05_06_135910_create_admin', 1),
(8, '2025_05_06_135916_create_verifikasi_admin', 1),
(9, '2025_05_06_135920_create_editor', 1),
(10, '2025_05_06_135925_create_jasa', 1),
(11, '2025_05_06_135930_create_jasa_images_table', 1),
(12, '2025_05_06_135932_create_paket_jasa', 1),
(13, '2025_05_06_135949_create_pesanan', 1),
(14, '2025_05_06_135950_create_catatan_pesanan_table', 1),
(15, '2025_05_06_135952_create_revisi_table', 1),
(16, '2025_05_06_135955_create_revisi_editor_table', 1),
(17, '2025_05_06_135960_create_revisi_user_table', 1),
(18, '2025_05_06_140010_create_review', 1),
(19, '2025_05_06_140017_create_metode_pembayaran', 1),
(20, '2025_05_06_140029_create_transaksi', 1),
(21, '2025_06_15_133851_create_fcm_token_users_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `paket_jasa`
--

CREATE TABLE `paket_jasa` (
  `id_paket_jasa` bigint(20) UNSIGNED NOT NULL,
  `kelas_jasa` enum('basic','standard','premium') NOT NULL,
  `deskripsi_singkat` varchar(300) NOT NULL,
  `harga_paket_jasa` int(11) NOT NULL,
  `waktu_pengerjaan` varchar(50) NOT NULL,
  `maksimal_revisi` tinyint(4) NOT NULL,
  `id_jasa` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `paket_jasa`
--

INSERT INTO `paket_jasa` (`id_paket_jasa`, `kelas_jasa`, `deskripsi_singkat`, `harga_paket_jasa`, `waktu_pengerjaan`, `maksimal_revisi`, `id_jasa`) VALUES
(1, 'basic', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Nulla, placeat? Vitae quam error laudantium suscipit nulla soluta? Vero nobis cupiditate, quam similique nisi eligendi veniam nesciunt odit ipsam, at quis?', 73265, '3 hari', 4, 1),
(2, 'standard', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Nulla, placeat? Vitae quam error laudantium suscipit nulla soluta? Vero nobis cupiditate, quam similique nisi eligendi veniam nesciunt odit ipsam, at quis?', 25621, '7 hari', 2, 1),
(3, 'premium', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Nulla, placeat? Vitae quam error laudantium suscipit nulla soluta? Vero nobis cupiditate, quam similique nisi eligendi veniam nesciunt odit ipsam, at quis?', 16567, '14 hari', 2, 1),
(4, 'basic', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Nulla, placeat? Vitae quam error laudantium suscipit nulla soluta? Vero nobis cupiditate, quam similique nisi eligendi veniam nesciunt odit ipsam, at quis?', 56074, '3 hari', 2, 2),
(5, 'standard', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Nulla, placeat? Vitae quam error laudantium suscipit nulla soluta? Vero nobis cupiditate, quam similique nisi eligendi veniam nesciunt odit ipsam, at quis?', 19795, '7 hari', 4, 2),
(6, 'premium', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Nulla, placeat? Vitae quam error laudantium suscipit nulla soluta? Vero nobis cupiditate, quam similique nisi eligendi veniam nesciunt odit ipsam, at quis?', 31238, '14 hari', 3, 2),
(7, 'basic', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Nulla, placeat? Vitae quam error laudantium suscipit nulla soluta? Vero nobis cupiditate, quam similique nisi eligendi veniam nesciunt odit ipsam, at quis?', 26772, '3 hari', 1, 3),
(8, 'standard', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Nulla, placeat? Vitae quam error laudantium suscipit nulla soluta? Vero nobis cupiditate, quam similique nisi eligendi veniam nesciunt odit ipsam, at quis?', 73077, '7 hari', 4, 3),
(9, 'premium', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Nulla, placeat? Vitae quam error laudantium suscipit nulla soluta? Vero nobis cupiditate, quam similique nisi eligendi veniam nesciunt odit ipsam, at quis?', 20227, '14 hari', 2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `status_pesanan` enum('pending','diproses','menunggu_editor','dikerjakan','menunggu_review','revisi','selesai','dibatalkan') NOT NULL,
  `total_harga` int(10) UNSIGNED NOT NULL,
  `estimasi_waktu` datetime NOT NULL,
  `maksimal_revisi` tinyint(3) UNSIGNED NOT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `assigned_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_user` bigint(20) UNSIGNED NOT NULL,
  `id_jasa` bigint(20) UNSIGNED NOT NULL,
  `id_paket_jasa` bigint(20) UNSIGNED NOT NULL,
  `id_editor` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `id_review` bigint(20) UNSIGNED NOT NULL,
  `review` varchar(250) NOT NULL,
  `rating` enum('1','2','3','4','5') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_pesanan` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `revisi`
--

CREATE TABLE `revisi` (
  `id_revisi` bigint(20) UNSIGNED NOT NULL,
  `urutan_revisi` tinyint(3) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_pesanan` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `revisi_editor`
--

CREATE TABLE `revisi_editor` (
  `id_revisi_editor` bigint(20) UNSIGNED NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `catatan_editor` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_editor` bigint(20) UNSIGNED NOT NULL,
  `id_revisi` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `revisi_user`
--

CREATE TABLE `revisi_user` (
  `id_revisi_user` bigint(20) UNSIGNED NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `catatan_user` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_revisi` bigint(20) UNSIGNED DEFAULT NULL,
  `id_user` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` bigint(20) UNSIGNED NOT NULL,
  `order_id` varchar(255) NOT NULL,
  `jumlah` int(10) UNSIGNED NOT NULL,
  `status_transaksi` enum('belum_bayar','menunggu_konfirmasi','lunas','dibatalkan','expired') NOT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `waktu_pembayaran` datetime DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `catatan_transaksi` text DEFAULT NULL,
  `alasan_penolakan` text DEFAULT NULL,
  `expired_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_metode_pembayaran` bigint(20) UNSIGNED NOT NULL,
  `id_pesanan` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `nama_user` varchar(50) NOT NULL,
  `jenis_kelamin` enum('laki-laki','perempuan') DEFAULT NULL,
  `no_telpon` varchar(15) DEFAULT NULL,
  `alamat` varchar(400) DEFAULT NULL,
  `no_rekening` varchar(20) DEFAULT NULL,
  `foto` varchar(50) DEFAULT NULL,
  `email_verified_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_auth` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `uuid`, `nama_user`, `jenis_kelamin`, `no_telpon`, `alamat`, `no_rekening`, `foto`, `email_verified_at`, `created_at`, `updated_at`, `id_auth`) VALUES
(1, 'ec2eb4d3-8d59-4383-8eb2-0c374f7464ab', 'User 1', 'laki-laki', '08587582897', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 22),
(2, 'c3ee6b4a-4a8c-4521-9a88-7f48d772f454', 'User 2', 'laki-laki', '085491518925', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 23),
(3, '7b518652-3216-4557-8b77-8346783f0698', 'User 3', 'perempuan', '085599832690', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 24),
(4, 'ca08aa0c-c09b-4516-b409-af2b67047383', 'User 4', 'laki-laki', '085603098411', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 25),
(5, '5aba567b-92ae-4fc9-9861-d6397a86e8ae', 'User 5', 'laki-laki', '085782207566', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 26),
(6, '4bcc7b11-6f38-4248-9677-05c105fbc354', 'User 6', 'perempuan', '085957280303', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 27),
(7, '09082f36-8ac6-4e11-bb7c-0dee6d66e31a', 'User 7', 'laki-laki', '085144924562', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 28),
(8, 'c98b868c-74ac-46b0-b681-053a3dcf1edb', 'User 8', 'perempuan', '085772436402', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 29),
(9, '34c14301-5b7e-40eb-93eb-5038d9df5596', 'User 9', 'laki-laki', '085407303121', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 30),
(10, '5274f3bb-2072-45b7-b73b-45badbe43c92', 'User 10', 'laki-laki', '085376413602', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 31),
(11, '0f93d6f9-0c51-44b1-81d5-f9bf61d7021d', 'User 11', 'laki-laki', '085866955494', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 32),
(12, '587cbbb7-4475-431e-bad1-176ab2cba6d1', 'User 12', 'laki-laki', '085973858290', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 33),
(13, '15c08e99-284c-42ae-ba97-f9dbf39ff0ec', 'User 13', 'perempuan', '085702468697', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 34),
(14, 'e6d1ae8d-19b9-4237-8c3a-796257b7a855', 'User 14', 'laki-laki', '085824422054', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 35),
(15, '730238bf-a676-414b-b946-56d8ddc45992', 'User 15', 'perempuan', '085179858130', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 36),
(16, '8f066a91-1338-4097-8bd8-034d4c0d11cf', 'User 16', 'perempuan', '085903499715', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 37),
(17, 'b215ed0a-392f-4ed3-b172-474b9d9e292a', 'User 17', 'laki-laki', '085734328130', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 38),
(18, 'f3020619-62a7-41ab-b816-0ca8bcd13321', 'User 18', 'laki-laki', '085190668198', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 39),
(19, '3fd84893-c6ab-4d6b-bdad-e6129653524c', 'User 19', 'perempuan', '085354724002', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 40),
(20, '99e0ea86-f03a-420d-a85a-604430d8d9e6', 'User 20', 'perempuan', '085301106066', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 41),
(21, 'e8b3eee2-3570-43ca-98c4-bf2cc44b9d05', 'User 21', 'laki-laki', '085223374645', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 42),
(22, '5041ddd4-84b3-44c3-b40e-ec27d47f63dc', 'User 22', 'perempuan', '085106464476', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 43),
(23, '10559290-ebdb-4093-8683-e18aa7269457', 'User 23', 'laki-laki', '085947047552', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 44),
(24, '3298f7be-cd8d-4da0-a260-16eef81ed462', 'User 24', 'laki-laki', '085767693955', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 45),
(25, 'dd344a97-86ee-451d-b587-94d1d06f61b9', 'User 25', 'perempuan', '085607230495', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 46),
(26, '802ce067-23ad-4ae5-97bf-3bc40391546b', 'User 26', 'perempuan', '085883783839', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 47),
(27, 'ea5f151b-ca02-4422-9f9a-9a4ec549a77c', 'User 27', 'perempuan', '085786461931', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 48),
(28, '9a012833-f06e-46ac-8454-117103a7ce6f', 'User 28', 'laki-laki', '085946414004', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 49),
(29, '67af9860-45f0-44a1-848f-a6d186365bad', 'User 29', 'laki-laki', '085639085153', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 50),
(30, 'd774584b-ea07-408d-8a97-337fee9f0c40', 'User 30', 'laki-laki', '085163827720', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 51),
(31, '6f23bbf9-7a0f-43b8-b4b0-2221e66e2556', 'User 31', 'perempuan', '085743154720', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 52),
(32, '1a062cfd-e0b0-4fe1-9d5f-1f5a6d54a546', 'User 32', 'laki-laki', '085638521564', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 53),
(33, '4df94f2f-6565-4544-bdfc-dbae76882ffe', 'User 33', 'perempuan', '085627365720', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 54),
(34, '65a74428-89fa-447b-9540-78ad45e1d7af', 'User 34', 'perempuan', '085452245293', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 55),
(35, '1c223866-4a3d-4e72-b209-128db30601ed', 'User 35', 'perempuan', '085746481576', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 56),
(36, 'a6ac23e5-0511-4dce-b793-06477ff4c1f6', 'User 36', 'laki-laki', '08531814192', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 57),
(37, 'd6a6f766-be02-4c98-8039-96cdbb88bb2a', 'User 37', 'perempuan', '085215404679', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 58),
(38, '5f567e16-b783-4dd0-95a7-5bcd15f27fca', 'User 38', 'laki-laki', '085636987990', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 59),
(39, '6a264fe1-071d-4be1-9dc5-f61925ede0ae', 'User 39', 'laki-laki', '085306028242', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 60),
(40, '8b4337a8-8c14-4a68-85a3-452580025a5e', 'User 40', 'perempuan', '085901706864', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 61),
(41, '615dc58b-8b3e-49ad-8311-952401880244', 'User 41', 'laki-laki', '085547237810', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 62),
(42, 'a1ad8013-4217-473f-9f3e-60ef5f6b15c8', 'User 42', 'perempuan', '085562866647', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 63),
(43, '89ffdd12-7a61-47ad-b8df-13742806209f', 'User 43', 'perempuan', '085928962646', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 64),
(44, 'f22e840b-cb08-41b3-a615-a506e24c2acb', 'User 44', 'perempuan', '085328955723', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 65),
(45, '81bc048e-b86f-4566-bd41-c498aba8f16b', 'User 45', 'perempuan', '085895790314', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 66),
(46, 'e3e83b2b-5fe1-49f1-a03f-8d28abeac9a6', 'User 46', 'perempuan', '085949101126', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 67),
(47, 'f9ca2155-b08f-4937-ba2c-b833f08c86c7', 'User 47', 'laki-laki', '085325434144', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 68),
(48, '1251c15f-7d55-4c41-9493-341cd2fa4067', 'User 48', 'perempuan', '085526887955', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 69),
(49, 'cb3da52c-2197-42e5-b282-632d4c748e5c', 'User 49', 'laki-laki', '085190367477', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 70),
(50, '6bc56393-dd9e-4ad6-a6eb-60ec7720ac70', 'User 50', 'perempuan', '085440378265', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur nesciunt provident explicabo a repudiandae delectus, numquam totam accusantium perspiciatis quam adipisci iure autem incidunt, illum accusamus facilis expedita dignissimos deserunt.', NULL, NULL, NULL, NULL, NULL, 71);

-- --------------------------------------------------------

--
-- Table structure for table `verifikasi_admin`
--

CREATE TABLE `verifikasi_admin` (
  `id_verifikasi_admin` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(45) NOT NULL,
  `kode_otp` varchar(6) NOT NULL,
  `link_verifikasi` varchar(255) NOT NULL,
  `deskripsi` enum('password','email') NOT NULL,
  `terkirim` smallint(5) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_admin` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `verifikasi_user`
--

CREATE TABLE `verifikasi_user` (
  `id_verifikasi_user` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(45) NOT NULL,
  `kode_otp` varchar(6) NOT NULL,
  `link_verifikasi` varchar(255) NOT NULL,
  `deskripsi` enum('password','email') NOT NULL,
  `terkirim` smallint(5) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_user` bigint(20) UNSIGNED NOT NULL
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
  MODIFY `id_admin` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `auth`
--
ALTER TABLE `auth`
  MODIFY `id_auth` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `catatan_pesanan`
--
ALTER TABLE `catatan_pesanan`
  MODIFY `id_catatan_pesanan` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `editor`
--
ALTER TABLE `editor`
  MODIFY `id_editor` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fcm_token`
--
ALTER TABLE `fcm_token`
  MODIFY `id_fcm_token` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jasa`
--
ALTER TABLE `jasa`
  MODIFY `id_jasa` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `jasa_images`
--
ALTER TABLE `jasa_images`
  MODIFY `id_jasa_image` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  MODIFY `id_metode_pembayaran` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `paket_jasa`
--
ALTER TABLE `paket_jasa`
  MODIFY `id_paket_jasa` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `id_review` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `revisi`
--
ALTER TABLE `revisi`
  MODIFY `id_revisi` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `revisi_editor`
--
ALTER TABLE `revisi_editor`
  MODIFY `id_revisi_editor` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `revisi_user`
--
ALTER TABLE `revisi_user`
  MODIFY `id_revisi_user` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `verifikasi_admin`
--
ALTER TABLE `verifikasi_admin`
  MODIFY `id_verifikasi_admin` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `verifikasi_user`
--
ALTER TABLE `verifikasi_user`
  MODIFY `id_verifikasi_user` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
