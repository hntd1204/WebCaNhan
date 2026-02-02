-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 02, 2026 at 11:27 PM
-- Server version: 10.11.15-MariaDB-cll-lve
-- PHP Version: 8.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `thanhdat6_thanhdat`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(5, 'Ăn vặt'),
(7, 'Chỗ ở'),
(3, 'Giải trí'),
(1, 'Quán ăn'),
(2, 'Quán nước');

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `name`) VALUES
(1, 'Hồ Chí Minh'),
(2, 'Bảo Lộc'),
(3, 'Vũng Tàu'),
(4, 'Đà Lạt');

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE `districts` (
  `id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `districts`
--

INSERT INTO `districts` (`id`, `city_id`, `name`) VALUES
(1, 1, 'Quận 1'),
(2, 1, 'Quận 2'),
(3, 1, 'Quận 3'),
(4, 1, 'Quận 4'),
(5, 1, 'Quận 5'),
(6, 1, 'Quận 6'),
(7, 1, 'Quận 7'),
(8, 1, 'Quận 8'),
(9, 1, 'Quận 9'),
(10, 1, 'Quận 10'),
(11, 1, 'Quận 11'),
(12, 1, 'Quận 12'),
(13, 1, 'Bình Thạnh'),
(14, 1, 'Gò Vấp'),
(15, 1, 'Phú Nhuận'),
(16, 1, 'Tân Bình'),
(17, 1, 'Tân Phú'),
(18, 1, 'Bình Tân'),
(19, 1, 'TP. Thủ Đức'),
(20, 1, 'Huyện Bình Chánh'),
(21, 1, 'Huyện Hóc Môn'),
(22, 1, 'Huyện Nhà Bè'),
(23, 1, 'Huyện Củ Chi'),
(24, 1, 'Huyện Cần Giờ');

-- --------------------------------------------------------

--
-- Table structure for table `places`
--

CREATE TABLE `places` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `rating` int(11) DEFAULT 5,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(11) DEFAULT 1,
  `district` varchar(50) DEFAULT NULL,
  `original_link` text DEFAULT NULL,
  `city` varchar(50) DEFAULT 'Hồ Chí Minh'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `places`
--

INSERT INTO `places` (`id`, `name`, `address`, `description`, `latitude`, `longitude`, `rating`, `created_at`, `category_id`, `district`, `original_link`, `city`) VALUES
(4, 'Ốc Nhi Vườn Lài Tân Phú', '274/27 Đ. Vườn Lài, Phú Thọ Hoà, Tân Phú, Thành phố Hồ Chí Minh, Việt Nam', '', 10.78870870, 106.62094470, 5, '2026-01-29 07:06:44', 1, 'Tân Phú', 'https://www.google.com/maps/place/%E1%BB%90c+Nhi+V%C6%B0%E1%BB%9Dn+L%C3%A0i+T%C3%A2n+Ph%C3%BA/@10.7887087,106.6209447,17z/data=!3m1!4b1!4m6!3m5!1s0x31752d0044fb3ef7:0x8282ce522da847a0!8m2!3d10.7887087!4d106.6258156!16s%2Fg%2F11mslqgh35?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(5, 'Ốc Ty', '12 Đ. Vĩnh Khánh, Phường 8, Quận 4, Thành phố Hồ Chí Minh, Việt Nam', '', 10.77267440, 106.61044410, 5, '2026-01-29 07:07:34', 1, 'Quận 4', 'https://www.google.com/maps/place/%E1%BB%90c+Ty/@10.7726744,106.6104441,13z/data=!4m10!1m2!2m1!1z4buRYyB0eQ!3m6!1s0x31752f62a70d3bbf:0x732d441a666d308!8m2!3d10.7607246!4d106.7069244!15sCgfhu5FjIHR5WgkiB-G7kWMgdHmSAQZiaXN0cm_gAQA!16s%2Fg%2F11t6xdfrwl?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(6, 'Ốc Su', '53 Đ. Tôn Đản, Phường 15, Quận 4, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 17h-4h', 10.77205640, 106.65706810, 5, '2026-01-29 07:08:38', 1, 'Quận 4', 'https://www.google.com/maps/place/Qu%C3%A1n+%E1%BB%90c+Su+20k/@10.7720564,106.6570681,15z/data=!4m10!1m2!2m1!1z4buRYyBzdQ!3m6!1s0x31752f6ebf656ad1:0xd67de51b79b357e7!8m2!3d10.7603109!4d106.7073596!15sCgfhu5FjIHN1WgkiB-G7kWMgc3WSAQpyZXN0YXVyYW50mgEkQ2hkRFNVaE5NRzluUzBWSlEwRm5TVU51TW5WSU5YVlJSUkFC4AEA-gEECFQQGw!16s%2Fg%2F11ngl0kffg?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(7, 'Mì Quảng_Hủ tiếu chị Mén', '648/28 Cách Mạng Tháng Tám, Phường 11, Quận 3, Thành phố Hồ Chí Minh, Việt Nam', '', 10.78826420, 106.66413930, 5, '2026-01-29 07:10:17', 1, 'Quận 3', 'https://www.google.com/maps/place/H%E1%BB%A7+ti%E1%BA%BFu+Ch%E1%BB%8B+M%C3%A9n/@10.7882642,106.6641393,19.03z/data=!4m12!1m5!3m4!2zMTDCsDQ3JzEzLjUiTiAxMDbCsDM5JzQ5LjgiRQ!8m2!3d10.7870716!4d106.6638451!3m5!1s0x31752f0002e68f13:0x17cabcede22ca67c!8m2!3d10.7875988!4d106.6657443!16s%2Fg%2F11vqqrklgz?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(8, 'Hanuri - Quán ăn Hàn Quốc - Sư Vạn Hạnh', '736 Sư Vạn Hạnh, Phường 12, Quận 10, Thành phố Hồ Chí Minh, Việt Nam', '', 10.77205660, 106.66451400, 5, '2026-01-29 07:11:03', 1, 'Quận 10', 'https://www.google.com/maps/place/Hanuri+-+Qu%C3%A1n+%C4%83n+H%C3%A0n+Qu%E1%BB%91c+-+S%C6%B0+V%E1%BA%A1n+H%E1%BA%A1nh/@10.7720566,106.664514,17z/data=!4m6!3m5!1s0x31752ede74d9c72b:0x50e90e0b67f19942!8m2!3d10.7720566!4d106.6696638!16s%2Fg%2F1pzxnyqsf?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(9, 'Mì Ốc Hến Dì Lan Q10', '1 Cư Xá Đồng Tiến, Phường 14, Quận 10, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 8h-21h', 10.77001820, 106.66205560, 5, '2026-01-29 07:18:21', 1, 'Quận 10', 'https://www.google.com/maps/place/M%C3%AC+%E1%BB%90c+H%E1%BA%BFn+D%C3%AC+Lan+Q10/@10.7700182,106.6620556,17z/data=!3m1!4b1!4m6!3m5!1s0x31752fec4c8fce85:0x2c92cea87df13cc1!8m2!3d10.7700182!4d106.6646305!16s%2Fg%2F11h239bqfb?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(10, 'Mì Ốc Hến Dì Lan Q6', 'E56 Bis Cư Xá Phú Lâm B, Phường 13, Quận 6, Thành phố Hồ Chí Minh 70000, Việt Nam', 'Mở cửa 8h-21h', 10.75243260, 106.62581110, 5, '2026-01-29 07:18:51', 1, 'Quận 6', 'https://www.google.com/maps/place/M%C3%AC+%E1%BB%91c+h%E1%BA%BFn+D%C3%AC+Lan+Ph%C3%BA+L%C3%A2m/@10.7524326,106.6258111,17z/data=!3m1!4b1!4m6!3m5!1s0x31752d0002dd02ef:0x75b8d67bb2d7aef1!8m2!3d10.7524326!4d106.628386!16s%2Fg%2F11vxgr2hh3?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(11, 'Dookki Aeon Mall Bình Tân', '1 Đường Số 17A, Bình Trị Đông B, Bình Tân, Thành phố Hồ Chí Minh 700000, Việt Nam', '', 10.74279580, 106.60935620, 5, '2026-01-29 07:19:55', 1, 'Bình Tân', 'https://www.google.com/maps/place/Dookki+Aeon+Mall+B%C3%ACnh+T%C3%A2n/@10.7427958,106.6093562,17z/data=!3m1!4b1!4m6!3m5!1s0x31752dfe775504fb:0x961d992a4abec871!8m2!3d10.7427958!4d106.6119311!16s%2Fg%2F11ld13g4rc?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(12, 'Dookki Vincom 3 tháng 2', '3C 3 Tháng 2, Phường 10, Quận 10, Thành phố Hồ Chí Minh 700000, Việt Nam', '', 10.77622060, 106.67818510, 5, '2026-01-29 07:21:14', 1, 'Quận 10', 'https://www.google.com/maps/place/Dookki+Vincom+3+th%C3%A1ng+2/@10.7762206,106.6781851,17z/data=!3m1!4b1!4m6!3m5!1s0x31752f56290073a1:0xbd4eebc2e1ca5018!8m2!3d10.7762206!4d106.68076!16s%2Fg%2F11y6qh4rp5?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(13, 'Bún Đậu Mắm Tôm - Hẻm Đậu', '153 Tô Hiến Thành, Phường 13, Quận 10, Thành phố Hồ Chí Minh 700000, Việt Nam', '', 10.78071000, 106.66672510, 5, '2026-01-29 07:22:04', 1, 'Quận 10', 'https://www.google.com/maps/place/B%C3%BAn+%C4%90%E1%BA%ADu+M%E1%BA%AFm+T%C3%B4m+-+H%E1%BA%BBm+%C4%90%E1%BA%ADu/@10.78071,106.6667251,17z/data=!3m1!4b1!4m6!3m5!1s0x31752f006b6e2793:0x230886db79252bd5!8m2!3d10.78071!4d106.6693!16s%2Fg%2F11wvqpk502?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(14, 'Hảo Quán - Bún đậu Hà Nội', '193/16 Bà Hạt, Phường 9, Quận 10, Thành phố Hồ Chí Minh, Việt Nam', 'Chị HNgân chỉ', 10.76543300, 106.67051180, 5, '2026-01-29 07:24:12', 1, 'Quận 10', 'https://www.google.com/maps/place/H%E1%BA%A3o+Qu%C3%A1n+-+B%C3%BAn+%C4%91%E1%BA%ADu+H%C3%A0+N%E1%BB%99i/@10.765433,106.6705118,19z/data=!4m6!3m5!1s0x31752fc084f94319:0x21e5b270e35a6c13!8m2!3d10.7654186!4d106.6704518!16s%2Fg%2F11l813pbdg?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(15, 'Nem Nướng Ninh Hoà Dì Út', '62 Nguyễn Gia Trí, Phường 25, Bình Thạnh, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 9h-22h30', 10.80297270, 106.71590900, 5, '2026-01-29 07:25:06', 1, 'Bình Thạnh', 'https://www.google.com/maps/place/Nem+N%C6%B0%E1%BB%9Bng+Ninh+Ho%C3%A0+D%C3%AC+%C3%9At/@10.8029727,106.715909,17z/data=!3m1!4b1!4m6!3m5!1s0x317529c2dafe0db3:0xaa86e110ef5f3eaa!8m2!3d10.8029727!4d106.715909!16s%2Fg%2F11tw_l91q6?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(16, 'Dung Sushi', '41/23 Đ.Nghĩa Phát, Phường 6, Tân Bình, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 16h-21h30', 10.78771050, 106.66023410, 5, '2026-01-29 07:26:18', 1, 'Tân Bình', 'https://www.google.com/maps/place/Dung+Sushi/@10.7877105,106.6602341,17z/data=!3m1!4b1!4m6!3m5!1s0x31752fddbb6da3c5:0x39aa10a2d4b17d7b!8m2!3d10.7877105!4d106.6602341!16s%2Fg%2F11fq8gh2pg?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(17, 'Mì cay Asan Trần Hưng Đạo', '831 Trần Hưng Đạo, Phường 1, Quận 5, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 9h-22h', 10.75495310, 106.68049320, 5, '2026-01-29 07:27:36', 1, 'Quận 5', 'https://www.google.com/maps/place/M%C3%AC+cay+Asan+Tr%E1%BA%A7n+H%C6%B0ng+%C4%90%E1%BA%A1o/@10.7549531,106.6804932,17z/data=!3m1!4b1!4m6!3m5!1s0x31752fc0a2370f9d:0xd6d691f8574461a8!8m2!3d10.7549531!4d106.6804932!16s%2Fg%2F11srl2l1x6?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(18, 'Mì Cay Asan Tô Hiến Thành', '288 Tô Hiến Thành, Phường 15, Quận 10, Thành phố Hồ Chí Minh 100000, Việt Nam', 'Mở cửa 9h-22h', 10.77751920, 106.66475770, 5, '2026-01-29 07:28:23', 1, 'Quận 10', 'https://www.google.com/maps/place/M%C3%AC+Cay+Asan+T%C3%B4+Hi%E1%BA%BFn+Th%C3%A0nh/@10.7775192,106.6647577,17z/data=!3m1!4b1!4m6!3m5!1s0x31752f875d772aeb:0xd494f2e38dd6cab1!8m2!3d10.7775192!4d106.6647577!16s%2Fg%2F11kjpbfhf9?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(19, 'Mì Cay SEOUL Quận 5', '406 An Dương Vương, Phường 4, Quận 5, Thành phố Hồ Chí Minh 700000, Việt Nam', 'Mở cửa 8h-22h', 10.75921240, 106.67790950, 5, '2026-01-29 07:29:33', 1, 'Quận 5', 'https://www.google.com/maps/place/M%C3%AC+Cay+SEOUL+Qu%E1%BA%ADn+5/@10.7592124,106.6779095,17z/data=!3m1!4b1!4m6!3m5!1s0x31752ff8901dd71f:0x166ce76a32bade55!8m2!3d10.7592124!4d106.6779095!16s%2Fg%2F11vyybv6vz?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(20, 'Mì Cay SEOUL Quận 6', '214 Đ. Nguyễn Văn Luông, Phường 11, Quận 6, Thành phố Hồ Chí Minh 700000, Việt Nam', 'Mở cửa 8h-22h', 10.74374780, 106.63490990, 5, '2026-01-29 07:30:05', 1, 'Quận 6', 'https://www.google.com/maps/place/M%C3%AC+Cay+SEOUL+Qu%E1%BA%ADn+6/@10.7437478,106.6349099,17z/data=!3m1!4b1!4m6!3m5!1s0x31752fc6a671085d:0x5ec0b9d4cb1013fb!8m2!3d10.7437478!4d106.6349099!16s%2Fg%2F11w9zctwr3?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(21, 'Bánh canh khô', '428 Nguyễn Tri Phương, Phường 4, Quận 10, Thành phố Hồ Chí Minh 727010, Việt Nam', 'Mở cửa 6h30-1h30', 10.76524330, 106.66768920, 5, '2026-01-29 07:31:23', 1, 'Quận 10', 'https://www.google.com/maps/place/B%C3%A1nh+Canh+B%E1%BB%99t+G%E1%BA%A1o+Thanh+Quy%C3%AAn/@10.7652433,106.6676892,21z/data=!4m6!3m5!1s0x31752f0cc073156d:0xd6814a020417a30a!8m2!3d10.7652433!4d106.6678284!16s%2Fg%2F11vw_fj5qz?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(22, 'Udon Osaka (chi nhánh 2)', 'Tổ 59-khu4, Phường 1, Quận 10, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 11h-22h', 10.76636170, 106.67519470, 5, '2026-01-29 07:32:57', 1, 'Quận 10', 'https://www.google.com/maps/place/Osaka+(chi+nh%C3%A1nh+2)/@10.7663617,106.6751947,19.06z/data=!4m6!3m5!1s0x31752fe3c8cb628b:0xc8711a9e2b330203!8m2!3d10.7667319!4d106.6754849!16s%2Fg%2F11vdbfm10_?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(23, 'Tiệm Mì Mi An', '2/24 Cao Thắng, Phường 5, Quận 3, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 10h-21h30', 10.76937220, 106.68485580, 5, '2026-01-29 07:34:04', 1, 'Quận 3', 'https://www.google.com/maps/place/Ti%E1%BB%87m+M%C3%AC+Mi+An/@10.7693722,106.6848558,20.69z/data=!4m6!3m5!1s0x31752f007ee5a68b:0x28abe99fe5307ce1!8m2!3d10.7695285!4d106.6849765!16s%2Fg%2F11whpyrxwk?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(24, 'MENYA ICHIBAN(麺家一番) - Udon', '330 Tân Sơn Nhì, Tân Phú, Thành phố Hồ Chí Minh 70000, Việt Nam', 'Mở cửa 10h-22h', 10.79650450, 106.59528320, 5, '2026-01-29 07:35:38', 1, 'Tân Phú', 'https://www.google.com/maps/place/MENYA+ICHIBAN(%E9%BA%BA%E5%AE%B6%E4%B8%80%E7%95%AA)+-+T%C3%A2n+S%C6%A1n+Nh%C3%AC/@10.7965045,106.5952832,13z/data=!4m10!1m2!2m1!1smenya+ichiban!3m6!1s0x317529bc16637a25:0x6673709977dc5b86!8m2!3d10.7965045!4d106.6303021!15sCg1tZW55YSBpY2hpYmFuWg8iDW1lbnlhIGljaGliYW6SARByYW1lbl9yZXN0YXVyYW504AEA!16s%2Fg%2F11xp8lxf9k?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(25, 'Kalbi Master Buffet Nướng & Lẩu - Vincom Plaza 3/2', 'Vincom plaza, 3-3c 3 Tháng 2, Phường 10, Quận 10, Thành phố Hồ Chí Minh 700000, Việt Nam', 'Mở cửa 11h-21h45', 10.77631070, 106.68083110, 5, '2026-01-29 07:36:59', 1, 'Quận 10', 'https://www.google.com/maps/place/Kalbi+Master+Buffet+N%C6%B0%E1%BB%9Bng+%26+L%E1%BA%A9u+-+Vincom+Plaza+3%2F2/@10.7763107,106.6808311,17z/data=!3m1!4b1!4m6!3m5!1s0x31752f212ffa4bfd:0xedd6c9990a6900eb!8m2!3d10.7763107!4d106.6808311!16s%2Fg%2F11w3_ddvzp?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(26, 'HẺM FAST FOOD 2', '75 Nguyễn Cư Trinh, Phường Nguyễn Cư Trinh, Quận 1, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 10h-21h30', 10.76390990, 106.69069900, 5, '2026-01-29 07:38:11', 1, 'Quận 1', 'https://www.google.com/maps/place/H%E1%BA%BAM+FAST+FOOD+2/@10.7639099,106.690699,17z/data=!3m1!4b1!4m6!3m5!1s0x31752f325b316693:0x41f0302628e138b7!8m2!3d10.7639099!4d106.690699!16s%2Fg%2F11gy6jg301?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(27, 'Pacho Pocha Express - 파초포차', '19 Trần Ngọc Diện, Thảo Điền, Quận 2, Thành phố Hồ Chí Minh 700000, Việt Nam', 'Mở cửa 11h30-23h', 10.80446830, 106.73955480, 5, '2026-01-29 07:39:42', 1, 'Quận 2', 'https://www.google.com/maps/place/Pacho+Pocha+Express+-+%ED%8C%8C%EC%B4%88%ED%8F%AC%EC%B0%A8/@10.8044683,106.7395548,17z/data=!3m1!4b1!4m6!3m5!1s0x317527ebe604f2f1:0xadfb3c7b6cb61378!8m2!3d10.8044683!4d106.7395548!16s%2Fg%2F11vcn7z7hq?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(28, 'Nâu Food', '143 Tôn Thất Thuyết, Phường 15, Quận 4, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 11h-23h30', 10.75422750, 106.70684500, 5, '2026-01-29 07:40:54', 1, 'Quận 4', 'https://www.google.com/maps/place/N%C3%A2u+Food/@10.7542275,106.706845,18.73z/data=!4m6!3m5!1s0x31752ffe5bf9e30f:0xb2c03543cfba2569!8m2!3d10.753391!4d106.707397!16s%2Fg%2F11hzz6rf_b?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(29, 'Thuận Tường Quán', 'Cư xá Nhiêu Lộc, 315 A, Tổ 81, khu phố 5, Tân Phú, Thành phố Hồ Chí Minh, Việt Nam', 'Đóng cửa', 10.78988390, 106.63136760, 5, '2026-01-29 07:42:58', 1, 'Tân Phú', 'https://www.google.com/maps/place/Thu%E1%BA%ADn+T%C6%B0%E1%BB%9Dng+Qu%C3%A1n/@10.7898839,106.6313676,18z/data=!3m1!4b1!4m6!3m5!1s0x31752f0064900fbd:0x83c6238b6d06a61f!8m2!3d10.7898839!4d106.6313676!16s%2Fg%2F11vxmd9zkd?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(30, 'Hủ Tiếu Cô Hường', '28 Đ. Tôn Đản, Phường 13, Quận 4, Thành phố Hồ Chí Minh, Việt Nam', '', 10.76166110, 106.70764130, 5, '2026-01-29 07:43:36', 1, 'Quận 4', 'https://www.google.com/maps/place/H%E1%BB%A7+Ti%E1%BA%BFu+C%C3%B4+H%C6%B0%E1%BB%9Dng/@10.7616611,106.7076413,17z/data=!3m1!4b1!4m6!3m5!1s0x31752fec068bbb93:0xdcb88887d7709b71!8m2!3d10.7616611!4d106.7076413!16s%2Fg%2F11px9y_r1b?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(31, 'Súp Cua Út Tuyền', '162 Nguyễn Thị Nhỏ, Phường 15, Quận 11, Thành phố Hồ Chí Minh 70000, Việt Nam', 'Mở cửa 13h-23h', 10.77445380, 106.65309580, 5, '2026-01-29 07:49:33', 1, 'Tân Bình', 'https://www.google.com/maps/place/S%C3%BAp+Cua+%C3%9At+Tuy%E1%BB%81n%7C+S%C3%BAp+Cua+Ngon+G%E1%BA%A7n+%C4%90%C3%A2y%7C+S%C3%BAp+Cua+T%C3%A2n+B%C3%ACnh/@10.7744538,106.6530958,21z/data=!4m6!3m5!1s0x31752ec0a33183cb:0x1ee7d6c81f8a3808!8m2!3d10.7744559!4d106.6532145!16s%2Fg%2F11c564k8p6?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(32, 'Busan Korea Food', '577 Nguyễn Đình Chiểu, Phường 2, Quận 3, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 9h-22h', 10.76759490, 106.67984610, 5, '2026-01-29 07:50:41', 1, 'Quận 3', 'https://www.google.com/maps/place/Busan+Korea+Food/@10.7675949,106.6798461,17z/data=!3m1!4b1!4m6!3m5!1s0x31752fd525033433:0x522b7707b47aa63c!8m2!3d10.7675949!4d106.6798461!16s%2Fg%2F11nghm_q23?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(33, 'Mì trộn tốp mỡ trứng lòng đào Bãi Sậy', '566 Nguyễn Trãi, Phường 7, Quận 5, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 15h-21h30', 10.75447190, 106.66840640, 5, '2026-01-29 07:51:17', 1, 'Quận 5', 'https://www.google.com/maps/place/M%C3%AC+tr%E1%BB%99n+t%E1%BB%91p+m%E1%BB%A1+tr%E1%BB%A9ng+l%C3%B2ng+%C4%91%C3%A0o+B%C3%A3i+S%E1%BA%ADy/@10.7544719,106.6684064,17z/data=!3m1!4b1!4m6!3m5!1s0x31752ffb319192dd:0x7dd2686094d2f720!8m2!3d10.7544719!4d106.6684064!16s%2Fg%2F11rhyprr5p?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(34, 'Joseon Tteokbokki - Crescent Mall', '101 Đ. Tôn Dật Tiên, Tân Phú, Quận 7, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 10h-22h', 10.72908360, 106.71887310, 5, '2026-01-29 07:53:19', 1, 'Quận 7', 'https://www.google.com/maps/place/Joseon+Tteokbokki+-+Qu%E1%BA%ADn+7/@10.7290836,106.7188731,17z/data=!3m1!4b1!4m6!3m5!1s0x31752f005da77a1b:0x66a9698418dc6396!8m2!3d10.7290836!4d106.7188731!16s%2Fg%2F11wv6_r_b2?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(35, 'Chè Dừa Nước Phong Dừa', '2 Đ. Nguyễn Nhữ Lãm, Phú Thọ Hoà, Tân Phú, Thành phố Hồ Chí Minh, Việt Nam', '', 10.77990190, 106.60901320, 5, '2026-01-29 08:52:13', 5, 'Tân Phú', 'https://www.google.com/maps/place/Ch%C3%A8+D%E1%BB%ABa+N%C6%B0%E1%BB%9Bc+Phong+D%E1%BB%ABa/@10.7799019,106.6090132,13z/data=!4m10!1m3!11m2!2sChBHsbDN0MnVXL3vp4dDf5DowXvYZw!3e3!3m5!1s0x31752d0b60166515:0x83247ba0b2648450!8m2!3d10.7831058!4d106.6265054!16s%2Fg%2F11y5csc_9n?entry=ttu&g_ep=EgoyMDI2MDEyNy4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh'),
(36, 'Phúc Long Premium Coffee & Tea', '87 Xuân Thủy, Thảo Điền, Thủ Đức, Thành phố Hồ Chí Minh, Việt Nam', '', 10.80394860, 106.72480150, 5, '2026-01-29 08:54:28', 2, 'Quận 2', 'https://www.google.com/maps/place/Ph%C3%BAc+Long+Premium+Coffee+%26+Tea/@10.8039486,106.7248015,15z/data=!3m1!4b1!4m6!3m5!1s0x317527c7331451b5:0x33b53c6bf216457b!8m2!3d10.8039488!4d106.7350798!16s%2Fg%2F11tcst7l5g?entry=ttu&g_ep=EgoyMDI2MDEyNy4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh'),
(37, 'The Wish Coffee - Sư Vạn Hạnh', '543 Sư Vạn Hạnh, Phường 12, Quận 10, Thành phố Hồ Chí Minh 72511, Việt Nam', '', 10.77569800, 106.65679640, 5, '2026-01-29 08:56:20', 2, 'Quận 10', 'https://www.google.com/maps/place/The+Wish+Coffee+-+543+S%C6%B0+V%E1%BA%A1n+H%E1%BA%A1nh/@10.775698,106.6567964,15z/data=!3m1!4b1!4m6!3m5!1s0x31752f0052909eb3:0xe161acca7ac34798!8m2!3d10.7756982!4d106.6670747!16s%2Fg%2F11xknhnk0w?entry=ttu&g_ep=EgoyMDI2MDEyNy4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh'),
(38, 'Asa Coffee', '141 Đường số 19, Bình Trị Đông B, Bình Tân, Thành phố Hồ Chí Minh, Việt Nam', '', 10.75311300, 106.60332180, 5, '2026-01-29 08:57:50', 2, 'Bình Tân', 'https://www.google.com/maps/place/Asa+Coffee/@10.753113,106.6033218,15z/data=!3m1!4b1!4m6!3m5!1s0x31752d964e2dcd1b:0xa145ad0e3ebb8f34!8m2!3d10.7531132!4d106.6136001!16s%2Fg%2F11k48kyyht?entry=ttu&g_ep=EgoyMDI2MDEyNy4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh'),
(39, 'KATINAT Bến Bình An', 'Bến ga Waterbus Đ. Số 21, Bình An, Thủ Đức, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 7h-22h30', 10.79715910, 106.72834180, 5, '2026-01-29 08:59:35', 2, 'Quận 2', 'https://www.google.com/maps/place/KATINAT+B%E1%BA%BFn+B%C3%ACnh+An/@10.7971591,106.7283418,19z/data=!4m6!3m5!1s0x3175270035949d01:0x62863478c4e9da2!8m2!3d10.7971589!4d106.728686!16s%2Fg%2F11vz8gnnzg?entry=ttu&g_ep=EgoyMDI2MDEyNy4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh'),
(40, 'Tiệm cà phê Thập niên 2000', '125/2B Hoà Hưng, Phường 12, Quận 10, Thành phố Hồ Chí Minh 700000, Việt Nam', 'Mở cửa 7h30-22h30', 10.77821380, 106.66995620, 5, '2026-01-29 09:01:26', 2, 'Quận 10', 'https://www.google.com/maps/place/Ti%E1%BB%87m+c%C3%A0+ph%C3%AA+Th%E1%BA%ADp+ni%C3%AAn+2000/@10.7782138,106.6699562,17z/data=!3m1!4b1!4m6!3m5!1s0x31752f6696cf54ed:0x7b1ddf0978c7c86!8m2!3d10.7782138!4d106.6725365!16s%2Fg%2F11jt02s8st?entry=ttu&g_ep=EgoyMDI2MDEyNy4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh'),
(41, 'Win.D Gaming & Billiards Quận 11', '288 Lãnh Binh Thăng, Phường 8, Quận 11, Thành phố Hồ Chí Minh 700000, Việt Nam', 'Mở cửa 24/7', 10.76417620, 106.64633710, 5, '2026-01-29 09:02:11', 3, 'Quận 11', 'https://www.google.com/maps/place/Win.D+Gaming+%26+Billiards+Qu%E1%BA%ADn+11/@10.7641762,106.6463371,17z/data=!3m1!4b1!4m6!3m5!1s0x31752f7dac25d3df:0x47daffb831530b8d!8m2!3d10.7641762!4d106.6489174!16s%2Fg%2F11vsyss95b?entry=ttu&g_ep=EgoyMDI2MDEyNy4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh'),
(42, 'LỀ CAFÉ', '98 Đ. Nguyễn Thị Thập, Tân Hưng, Quận 7, Thành phố Hồ Chí Minh 70000, Việt Nam', '', 10.74160800, 106.69243450, 5, '2026-01-29 09:02:45', 2, 'Quận 7', 'https://www.google.com/maps/place/L%E1%BB%80+CAF%C3%89/@10.741608,106.6924345,17z/data=!3m1!4b1!4m6!3m5!1s0x31752f70e6487315:0x9f17257819a93eb7!8m2!3d10.741608!4d106.6950148!16s%2Fg%2F11x7hbvmjp?entry=ttu&g_ep=EgoyMDI2MDEyNy4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh'),
(43, 'KIM THUỶ TEA&COFFEE', '74 Ký Con, Phường Nguyễn Thái Bình, Quận 1, Thành phố Hồ Chí Minh, Việt Nam', '', 10.76691220, 106.69897490, 5, '2026-01-29 09:05:08', 2, 'Quận 1', 'https://www.google.com/maps/place/KIM+THU%E1%BB%B6+TEA%26COFFEE/@10.7669122,106.6989749,20.99z/data=!4m6!3m5!1s0x31752f006bdf9a71:0x96c5a4507c47d322!8m2!3d10.7667538!4d106.6989787!16s%2Fg%2F11w_fspcm_?entry=ttu&g_ep=EgoyMDI2MDEyNy4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh'),
(44, 'Cycle Lab Coffee', '149A Trịnh Đình Trọng, phường, Tân Phú, Thành phố Hồ Chí Minh 700000, Việt Nam', 'Mở cửa 7h-23h', 10.77580570, 106.63842610, 5, '2026-01-29 09:05:48', 2, 'Tân Phú', 'https://www.google.com/maps/place/Cycle+Lab+Coffee/@10.7758057,106.6384261,17z/data=!3m1!4b1!4m6!3m5!1s0x31752fb5feb61cc3:0xe5126455e96451a2!8m2!3d10.7758057!4d106.6410064!16s%2Fg%2F11x_p1qc4y?entry=ttu&g_ep=EgoyMDI2MDEyNy4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh'),
(45, 'LÀ LÁ Bakeshop & Coffee', '10 Lê Ngô Cát, phường Xuân Hoà, Quận 3, Thành phố Hồ Chí Minh 100000, Việt Nam', 'Mở cửa 7h30-22h30', 10.77694330, 106.68244050, 5, '2026-01-29 09:07:08', 2, 'Quận 3', 'https://www.google.com/maps/place/L%C3%80+L%C3%81+Bakeshop+%26+Coffee/@10.7769433,106.6824405,17z/data=!3m1!4b1!4m6!3m5!1s0x31752f5e178f39ab:0x96e9b7f2c77c2763!8m2!3d10.7769433!4d106.6850208!16s%2Fg%2F11ygzq_y72?entry=ttu&g_ep=EgoyMDI2MDEyNy4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh'),
(46, 'September Saigon Cafe & Cake', '118/1D Nguyễn Trãi, Phường 2, Quận 5, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 8h-22h', 10.75796990, 106.67645680, 5, '2026-01-29 09:07:51', 2, 'Quận 5', 'https://www.google.com/maps/place/September+Saigon+Cafe+%26+Cake/@10.7579699,106.6764568,17z/data=!3m1!4b1!4m6!3m5!1s0x31752f1d297dfb33:0x640e54913d2e9461!8m2!3d10.7579699!4d106.6790371!16s%2Fg%2F11rgh78931?entry=ttu&g_ep=EgoyMDI2MDEyNy4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh'),
(47, 'ZEN Tea', '103 Phan Xích Long, Phường 2, Phú Nhuận, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 7h-22h30', 10.79725910, 106.68781330, 5, '2026-01-29 09:08:45', 2, 'Phú Nhuận', 'https://www.google.com/maps/place/ZEN+Tea/@10.7972591,106.6878133,17z/data=!3m1!4b1!4m6!3m5!1s0x3175290021f819a9:0xb831bad3dc97c071!8m2!3d10.7972591!4d106.6903936!16s%2Fg%2F11ykpz32pz?entry=ttu&g_ep=EgoyMDI2MDEyNy4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh'),
(48, 'Phê La - Trường Sơn', 'CC 26 + CC27, Đ. Trường Sơn, Phường 15, Quận 10, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 7h-22h45', 10.78209570, 106.66194480, 5, '2026-01-29 09:09:24', 2, 'Quận 10', 'https://www.google.com/maps/place/Ph%C3%AA+La+-+Tr%C6%B0%E1%BB%9Dng+S%C6%A1n/@10.7820957,106.6619448,17z/data=!3m1!4b1!4m6!3m5!1s0x31752f583292211b:0x9849b3310d2b26fc!8m2!3d10.7820957!4d106.6645251!16s%2Fg%2F11wn5754xp?entry=ttu&g_ep=EgoyMDI2MDEyNy4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh'),
(49, 'Bros tea shop', '106 Đ. Nguyễn Hồng Đào, Phường Tân Bình, Tân Bình, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 9h-22h30', 10.79451450, 106.64268920, 5, '2026-01-29 09:10:39', 2, 'Tân Bình', 'https://www.google.com/maps/place/Bros+tea+shop+_+tr%C3%A0+s%E1%BB%AFa+Bros+(+Nguy%E1%BB%85n+H%E1%BB%93ng+%C4%90%C3%A0o+)/@10.7945145,106.6426892,14z/data=!4m10!1m2!2m1!1zYnJvcyB0w6JuIGLDrG5o!3m6!1s0x3175297309db0f8b:0x4c998b8feec84190!8m2!3d10.794543!4d106.6425657!15sCg9icm9zIHTDom4gYsOsbmhaESIPYnJvcyB0w6JuIGLDrG5okgEQYnViYmxlX3RlYV9zdG9yZZoBRENpOURRVWxSUVVOdlpFTm9kSGxqUmpsdlQydGtURTVWU1hkV2JrNHhVVlZLZEdWWGRFcFRNVGxSVXpGdk5GVnNSUkFC4AEA-gEECHAQFA!16s%2Fg%2F11vk685n2v?entry=ttu&g_ep=EgoyMDI2MDEyNy4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh'),
(50, 'Jua Coffee and Food', '53 Đ. Kinh Dương Vương, Phường 12, Quận 6, Thành phố Hồ Chí Minh 70000, Việt Nam', 'Mở cửa 7h-23h', 10.75196120, 106.62945850, 5, '2026-01-29 09:11:22', 2, 'Quận 6', 'https://www.google.com/maps/place/Jua+Coffee+and+Food/@10.7519612,106.6294585,17z/data=!3m1!4b1!4m6!3m5!1s0x31752fffbfc23b57:0x86252f76170b5b09!8m2!3d10.7519612!4d106.6320388!16s%2Fg%2F11vc2_c4kg?entry=ttu&g_ep=EgoyMDI2MDEyNy4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh'),
(51, 'HẸ QUÁN - Bánh canh hẹ', '113 Cao Thắng, Phường 10, Quận 10, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 7h30-21h30', 10.77277460, 106.67621540, 5, '2026-01-29 09:12:21', 1, 'Quận 10', 'https://www.google.com/maps/place/H%E1%BA%B8+QU%C3%81N+-+B%C3%A1nh+canh+h%E1%BA%B9+_+B%C3%A1nh+b%C3%A8o+n%C3%B3ng+Ph%C3%BA+Y%C3%AAn/@10.7727746,106.6762154,17z/data=!3m1!4b1!4m6!3m5!1s0x31752f0715717629:0x21b050446eb31191!8m2!3d10.7727746!4d106.6787957!16s%2Fg%2F11fm_ll_mt?entry=ttu&g_ep=EgoyMDI2MDEyNy4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh'),
(52, 'Còi Cafe-Matcha', '238 Lê Trọng Tấn, Tây Thạnh, Tân Phú, Thành phố Hồ Chí Minh 72000, Việt Nam', 'Chi nhánh 2: 6 Út Tịch, Phường 4, Tân Bình, Thành phố Hồ Chí Minh, Việt Nam', 10.80724880, 106.62143230, 5, '2026-01-29 09:47:16', 2, 'Tân Phú', 'https://www.google.com/maps/place/C%C3%B2i+Cafe/@10.8072488,106.6214323,17z/data=!3m1!4b1!4m6!3m5!1s0x31752b004df45fef:0xf01c7fb4ddc9a231!8m2!3d10.8072488!4d106.6240072!16s%2Fg%2F11xfp9v5f0?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(53, 'Bánh ép O Châu - Tân Bình', '11a Hoàng Hoa Thám, Phường Tân Bình, Tân Bình, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 7h-22h', 10.79682180, 106.64438000, 5, '2026-01-29 09:48:23', 1, 'Tân Bình', 'https://www.google.com/maps/place/B%C3%A1nh+%C3%A9p+O+Ch%C3%A2u+-+T%C3%A2n+B%C3%ACnh/@10.7968218,106.64438,17z/data=!3m1!4b1!4m6!3m5!1s0x317529005367a05d:0xbd8d030e263b6f2f!8m2!3d10.7968218!4d106.6469549!16s%2Fg%2F11vprshl_m?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(54, 'Kokoria - 3 tháng 2', '207/33 3 Tháng 2, Phường 10, Quận 10, Thành phố Hồ Chí Minh 00700, Việt Nam', 'Mở cửa 10h30-22h | Có chi nhánh Sư Vạn Hạnh', 10.77109270, 106.67203620, 5, '2026-01-29 09:49:23', 1, 'Quận 10', 'https://www.google.com/maps/place/Kokoria/@10.7710927,106.6720362,17z/data=!3m1!4b1!4m6!3m5!1s0x31752ff067f8673d:0x1cd441dc210a95a0!8m2!3d10.7710927!4d106.6746111!16s%2Fg%2F11kmysxrmj?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(55, 'PixelB Vintage Photobooth', '143 Bờ Bao Tân Thắng, Sơn Kỳ, Tân Phú, Thành phố Hồ Chí Minh 700000, Việt Nam', 'Mở cửa 9h-23h', 10.79978880, 106.61301100, 5, '2026-01-29 09:51:20', 3, 'Tân Phú', 'https://www.google.com/maps/place/PixelB+Vintage+Photobooth/@10.7997888,106.613011,17z/data=!3m1!4b1!4m6!3m5!1s0x31752b000c19b4dd:0xef320877e6fc6efe!8m2!3d10.7997888!4d106.6155859!16s%2Fg%2F11ms1bdjlh?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(56, 'PHOTO OBJET Quận 7', 'Crescent Residence 2, Đ. Morison, Khu đô thị Phú Mỹ Hưng, Quận 7, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 9h30-22h', 10.72606560, 106.71789440, 5, '2026-01-29 09:52:14', 3, 'Quận 7', 'https://www.google.com/maps/place/PHOTO+OBJET+Qu%E1%BA%ADn+7/@10.7260656,106.7178944,17z/data=!3m1!4b1!4m6!3m5!1s0x31752f0073874247:0x3f2f345de046f78f!8m2!3d10.7260656!4d106.7204693!16s%2Fg%2F11y6xh0zzw?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(57, 'Photo Palette - Quận 1', '10 Huỳnh Thúc Kháng, Bến Nghé, Quận 1, Thành phố Hồ Chí Minh 71000, Việt Nam', 'Mở cửa 9h30-23h', 10.77328380, 106.70022700, 5, '2026-01-29 09:53:08', 3, 'Quận 1', 'https://www.google.com/maps/place/Photo+Palette+-+Qu%E1%BA%ADn+1/@10.7732838,106.700227,17z/data=!3m1!4b1!4m6!3m5!1s0x31752f0071e29fc1:0xa46438c8ca944792!8m2!3d10.7732838!4d106.7028019!16s%2Fg%2F11y7gfv4jm?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(58, 'Chong Chóng Bakery - Đồng Nai', '84 Đ. Đồng Nai, Phường 15, Quận 10, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 7h-22h', 10.77987560, 106.65961790, 5, '2026-01-29 09:54:14', 5, 'Quận 10', 'https://www.google.com/maps/place/Chong+Ch%C3%B3ng+Bakery+-+%C4%90%E1%BB%93ng+Nai/@10.7798756,106.6596179,17z/data=!3m1!4b1!4m6!3m5!1s0x31752f9778acff49:0x572f1cb798e31edb!8m2!3d10.7798756!4d106.6621928!16s%2Fg%2F11w57vwm99?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(59, 'Sữa dừa Tica', '30 Đ. Phạm Quý Thích, Tân Quý, Tân Phú, Thành phố Hồ Chí Minh, Việt Nam', 'Bán mang đi', 10.78786710, 106.62085940, 5, '2026-01-29 09:55:42', 2, 'Tân Phú', 'https://www.google.com/maps/place/S%E1%BB%AFa+d%E1%BB%ABa+Tica/@10.7878671,106.6208594,18.59z/data=!4m6!3m5!1s0x31752d0053d27f53:0x8dabcbcae7981d3a!8m2!3d10.7886267!4d106.6209856!16s%2Fg%2F11md4v939c?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(60, 'NamBa Roastery', '153 Bà Hom, Phường 13, Quận 6, Thành phố Hồ Chí Minh 20000, Việt Nam', 'Mở cửa 7h-19h', 10.75500990, 106.62817820, 5, '2026-01-29 09:59:37', 2, 'Quận 6', 'https://www.google.com/maps/place/NamBa+Roastery/@10.7550099,106.6281782,19z/data=!3m1!4b1!4m6!3m5!1s0x31752d0e9ec99bc5:0xe2b5443cc6453592!8m2!3d10.7550099!4d106.6288219!16s%2Fg%2F11h8krjc0p?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(62, 'Wok of love Quận 7', '48 Khu Phố Hưng Phước 2, Tân Phong, Quận 7, Thành phố Hồ Chí Minh 700000, Việt Nam', 'Mở cửa 10h30-00h', 10.73129440, 106.70695450, 5, '2026-01-29 10:03:43', 1, 'Quận 7', 'https://www.google.com/maps/place/Wok+of+love+Qu%E1%BA%ADn+7/@10.7312944,106.7069545,20.45z/data=!4m6!3m5!1s0x31752f72e1ae4369:0x569241ebd2f24d3f!8m2!3d10.7315098!4d106.7067903!16s%2Fg%2F11vsdlh5vc?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(63, 'Bánh tráng nướng Cao Thắng', '61 Cao Thắng, Phường 3, Quận 3, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 17h-23h', 10.77098340, 106.68091330, 5, '2026-01-29 10:18:34', 5, 'Quận 3', 'https://www.google.com/maps/place/B%C3%A1nh+tr%C3%A1ng+n%C6%B0%E1%BB%9Bng+61+Cao+Th%E1%BA%AFng/@10.7709834,106.6809133,20z/data=!4m6!3m5!1s0x31752f219c2a3603:0xcdabd7604ab8888e!8m2!3d10.7708267!4d106.681027!16s%2Fg%2F11b6p21xf3?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(64, 'Nhà Hàng Nướng - Lẩu Mako', '2-4-6 Đường số 28, Bình Trị Đông B, Bình Tân, Thành phố Hồ Chí Minh 700000, Việt Nam', 'Mở cửa 10h-22h', 10.75200520, 106.61458000, 5, '2026-01-29 11:05:32', 1, 'Bình Tân', 'https://www.google.com/maps/place/Nh%C3%A0+H%C3%A0ng+Mako+T%C3%AAn+L%E1%BB%ADa/@10.7520052,106.61458,21z/data=!4m6!3m5!1s0x31752d3a8bab214d:0xe8dd4bdad1876cec!8m2!3d10.7519229!4d106.614651!16s%2Fg%2F11vyhdq1zd?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(65, 'Bánh Flan Q6 Bình Phú', '13Q Đường Số 32A, Khu III, Quận 6, Thành phố Hồ Chí Minh 73115, Việt Nam', 'Mở cửa 7h-21h', 10.73912460, 106.62986790, 5, '2026-01-29 11:07:18', 5, 'Quận 6', 'https://www.google.com/maps/place/B%C3%A1nh+Flan+13Q/@10.7391246,106.6298679,20.6z/data=!4m6!3m5!1s0x31752f00588d6641:0xc7e72ba253a9d9a6!8m2!3d10.739072!4d106.629856!16s%2Fg%2F11wg5xx26x?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(66, 'Chuyện Cà Phê', '70 Đường số 5, Phường 11, Quận 6, Thành phố Hồ Chí Minh 700000, Việt Nam', 'Mở cửa 6h30-22h30', 10.74619500, 106.63101940, 5, '2026-01-29 11:08:25', 2, 'Quận 6', 'https://www.google.com/maps/place/Chuy%E1%BB%87n+C%C3%A0+Ph%C3%AA/@10.746195,106.6310194,17.81z/data=!4m6!3m5!1s0x31752f6a59c15943:0x7e3fa43c41535336!8m2!3d10.7471585!4d106.6331213!16s%2Fg%2F11t3fg6hkr?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(67, 'Hotdog trà sữa Tâm Tâm', '240/88 Đ. Nguyễn Văn Luông, Phường 11, Quận 6, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa ~14h', 10.74418180, 106.63617640, 5, '2026-01-29 11:09:09', 5, 'Quận 6', 'https://www.google.com/maps/place/Hotdog+tr%C3%A0+s%E1%BB%AFa+T%C3%A2m+T%C3%A2m/@10.7441818,106.6361764,20.28z/data=!4m6!3m5!1s0x31752f004c06bd29:0x2d92bebaf963b5cd!8m2!3d10.7443526!4d106.636093!16s%2Fg%2F11mcp__8nq?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(68, 'Trân Châu dừa Cô hiền', '187/30 Đ. Mai Xuân Thưởng, Phường 2, Quận 6, Thành phố Hồ Chí Minh, Việt Nam', 'Bán mang đi', 10.74884350, 106.64741920, 5, '2026-01-29 11:10:03', 2, 'Quận 6', 'https://www.google.com/maps/place/Tr%C3%A2n+Ch%C3%A2u+d%E1%BB%ABa+C%C3%B4+hi%E1%BB%81n/@10.7488435,106.6474192,20.86z/data=!4m6!3m5!1s0x31752f82dd35763d:0x168d00aec8bec196!8m2!3d10.7488793!4d106.647425!16s%2Fg%2F11s8gg5v9v?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(69, 'Phá lấu bò Pham Van Chi', '26 Đ. Phạm Văn Chí, Phường 1, Quận 6, Thành phố Hồ Chí Minh, Việt Nam', '', 10.74711920, 106.65068860, 5, '2026-01-29 11:13:25', 1, 'Quận 6', 'https://www.google.com/maps/place/Ph%C3%A1+L%E1%BA%A5u+B%C3%B2/@10.7471192,106.6506886,21z/data=!4m7!3m6!1s0x31752e6173ace895:0x58e859ee16e9320a!4b1!8m2!3d10.7471212!4d106.6508757!16s%2Fg%2F11hylcd0_1?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(70, 'Mì Cay SEOUL Quận 5', '406 An Dương Vương, Phường 4, Quận 5, Thành phố Hồ Chí Minh 700000, Việt Nam', 'Mở cửa 8h-22h', 10.75927580, 106.67766670, 5, '2026-01-29 11:15:23', 1, 'Quận 5', 'https://www.google.com/maps/place/M%C3%AC+Cay+SEOUL+Qu%E1%BA%ADn+5/@10.7592758,106.6776667,19.12z/data=!4m6!3m5!1s0x31752ff8901dd71f:0x166ce76a32bade55!8m2!3d10.7592124!4d106.6779095!16s%2Fg%2F11vyybv6vz?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA2N0gBUAM%3D', 'Hồ Chí Minh'),
(71, 'Tiệm Trà Yên - Trà Sữa Shan Tuyết', '9 Trưng Nhị, Phường 1, Vũng Tàu, Bà Rịa - Vũng Tàu 790000, Việt Nam', 'Mở cửa 8h-22h', 10.34803460, 107.07453510, 5, '2026-01-29 13:01:34', 2, '', 'https://www.google.com/maps/place/Ti%E1%BB%87m+Tr%C3%A0+Y%C3%AAn+-+Tr%C3%A0+S%E1%BB%AFa+Shan+Tuy%E1%BA%BFt/@10.3480346,107.0745351,20.17z/data=!4m6!3m5!1s0x31756f51c746bee7:0x6652bcee7c380564!8m2!3d10.3482144!4d107.0745876!16s%2Fg%2F11krjmqnrz?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA3MUgBUAM%3D', 'Vũng Tàu'),
(72, 'Quán cơm tấm Ngày Xưa', '3 Trưng Nhị, Phường 1, Vũng Tàu, Bà Rịa - Vũng Tàu 79000, Việt Nam', 'Mở cửa 6h-20h30', 10.34804870, 107.07446460, 5, '2026-01-29 13:02:11', 1, '', 'https://www.google.com/maps/place/Qu%C3%A1n+c%C6%A1m+t%E1%BA%A5m+Ng%C3%A0y+X%C6%B0a/@10.3480487,107.0744646,20.76z/data=!4m6!3m5!1s0x31756f870d615563:0xcfc5d5da37d9be4c!8m2!3d10.3481702!4d107.0744526!16s%2Fg%2F11q227q4bb?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA3MUgBUAM%3D', 'Vũng Tàu'),
(73, 'Fati Boutique Hotel & Apartment', 'Trần Phú, Phường 5, Vũng Tàu, Bà Rịa - Vũng Tàu 78200, Việt Nam', '', 10.36775760, 107.06067180, 5, '2026-01-29 13:02:59', 7, '', 'https://www.google.com/maps/place/Fati+Boutique+Hotel+%26+Apartment/@10.3677576,107.0606718,17z/data=!3m1!4b1!4m9!3m8!1s0x31756f047af05ecf:0xc8ad2bc3593e2cea!5m2!4m1!1i2!8m2!3d10.3677576!4d107.0632467!16s%2Fg%2F11ptvb2tjr?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA3MUgBUAM%3D', 'Vũng Tàu'),
(74, 'V Boutique Hotel - Complimentary refreshments', '32 Phan Huy Ích, Phường 2, Vũng Tàu, Bà Rịa - Vũng Tàu 790000, Việt Nam', '', 10.33767630, 107.08027950, 5, '2026-01-29 13:03:30', 7, '', 'https://www.google.com/maps/place/V+Boutique+Hotel+-+Complimentary+refreshments/@10.3376763,107.0802795,17z/data=!3m1!4b1!4m9!3m8!1s0x31756fdc8eb4efaf:0x4e88c87e989a9bf4!5m2!4m1!1i2!8m2!3d10.3376763!4d107.0828544!16s%2Fg%2F11vpty9trb?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA3MUgBUAM%3D', 'Vũng Tàu'),
(75, '5-homestay', '154/4 Bình Giã, Phường 8, Vũng Tàu, Bà Rịa - Vũng Tàu, Việt Nam', '', 10.35485120, 107.08996580, 5, '2026-01-29 13:03:58', 7, '', 'https://www.google.com/maps/place/5-homestay+V%C5%A9ng+T%C3%A0u/@10.3548512,107.0899658,17z/data=!3m1!4b1!4m9!3m8!1s0x31756f4662f5d83d:0x82b72470497cf17a!5m2!4m1!1i2!8m2!3d10.3548512!4d107.0925407!16s%2Fg%2F11kxgq18_q?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA3MUgBUAM%3D', 'Vũng Tàu'),
(76, 'Quán Ốc Thiên Nhiên 2', '245 Trương Công Định, Phường 3, Vũng Tàu, Bà Rịa - Vũng Tàu 790000, Việt Nam', 'Mở cửa 10h-23h', 10.35117220, 107.08022590, 5, '2026-01-29 13:04:48', 1, '', 'https://www.google.com/maps/place/Qu%C3%A1n+%E1%BB%90c+Thi%C3%AAn+Nhi%C3%AAn+2/@10.3511722,107.0802259,17z/data=!3m1!4b1!4m6!3m5!1s0x31756febe6f092e7:0x81b28dcc3e62caf5!8m2!3d10.3511722!4d107.0828008!16s%2Fg%2F11bxd6zv9b?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA3MUgBUAM%3D', 'Vũng Tàu'),
(77, 'Donut Nga', '130A Phan Chu Trinh, Phường 2, Vũng Tàu, Bà Rịa - Vũng Tàu, Việt Nam', 'Mở cửa ~11h-19h', 10.33935270, 107.08219500, 5, '2026-01-29 13:05:49', 5, '', 'https://www.google.com/maps/place/Donut+Nga/@10.3393527,107.082195,16.1z/data=!4m6!3m5!1s0x31756ff593abb289:0x7d84f4e0fb53f2d7!8m2!3d10.334569!4d107.0816462!16s%2Fg%2F11f_1j_53v?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA3MUgBUAM%3D', 'Vũng Tàu'),
(78, 'The Hill Coffee Vung Tau', '2 Hải Đăng, Phường 2, Vũng Tàu, Bà Rịa - Vũng Tàu, Việt Nam', 'Mở cửa 7h-22h | Bánh chuối siêu ngon', 10.33444620, 107.06825470, 5, '2026-01-29 13:06:32', 2, '', 'https://www.google.com/maps/place/The+Hill+Coffee+Vung+Tau/@10.3344462,107.0682547,16z/data=!4m6!3m5!1s0x31756f5b4a499c0b:0xe792ae9e39de9b63!8m2!3d10.3344487!4d107.073061!16s%2Fg%2F11sds82h2z?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA3MUgBUAM%3D', 'Vũng Tàu'),
(79, 'Bánh canh Cô Ngân', '933F+GPW, Phường 4, Vũng Tàu, Bà Rịa - Vũng Tàu, Việt Nam', 'Mở cửa 5h-11h', 10.35280020, 107.07495180, 5, '2026-01-29 13:09:34', 1, '', 'https://www.google.com/maps/place/B%C3%A1nh+canh+C%C3%B4+Ng%C3%A2n/@10.3528002,107.0749518,17.68z/data=!4m6!3m5!1s0x31756fbcf1f480b5:0xd479e90e08c044ea!8m2!3d10.3538686!4d107.074341!16s%2Fg%2F11gjt4ntb_?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA3MUgBUAM%3D', 'Vũng Tàu'),
(80, 'Lẩu Cá Đuối Hoàng Minh', '44 Trương Công Định, Phường 3, Vũng Tàu, Bà Rịa - Vũng Tàu, Việt Nam', 'Mở cửa 9h-00h', 10.34504370, 107.07874990, 5, '2026-01-29 13:10:48', 1, '', 'https://www.google.com/maps/place/L%E1%BA%A9u+C%C3%A1+%C4%90u%E1%BB%91i+Ho%C3%A0ng+Minh+-+Ch%C3%ADnh+Hi%E1%BB%87u+V%C5%A9ng+T%C3%A0u/@10.3450437,107.0787499,19.37z/data=!4m6!3m5!1s0x31756ff290b0989f:0x8e3c179ac7d1fe2a!8m2!3d10.3442428!4d107.0794541!16s%2Fg%2F11b7yv6m5q?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA3MUgBUAM%3D', 'Vũng Tàu'),
(81, 'Golden Bakery- Bông lan trứng muối Vũng Tàu-CN1', '195 Lê Hồng Phong, Phường 8, Vũng Tàu, Bà Rịa - Vũng Tàu 78000, Việt Nam', 'Mở cửa 6h30-21h', 10.35313480, 107.08735910, 5, '2026-01-29 13:14:41', 5, '', 'https://www.google.com/maps/place/Golden+Bakery-+B%C3%B4ng+lan+tr%E1%BB%A9ng+mu%E1%BB%91i+V%C5%A9ng+T%C3%A0u-CN1/@10.3531348,107.0873591,19.29z/data=!4m6!3m5!1s0x31756fe859c4c6b1:0x6994d99b7ac5ac1a!8m2!3d10.3528508!4d107.0879378!16s%2Fg%2F11fnwhnlqw?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA3MUgBUAM%3D', 'Vũng Tàu'),
(82, 'Bảo tàng Bà Rịa–Vũng Tàu', '4 Trần Phú, Phường 1, Vũng Tàu, Bà Rịa - Vũng Tàu, Việt Nam', 'Thứ 2 đóng cửa', 10.35155610, 107.07027830, 5, '2026-01-29 13:18:41', 3, '', 'https://www.google.com/maps/place/B%E1%BA%A3o+t%C3%A0ng+B%C3%A0+R%E1%BB%8Ba%E2%80%93V%C5%A9ng+T%C3%A0u/@10.3515561,107.0702783,17.21z/data=!4m6!3m5!1s0x31756f91304fff3d:0x44363c5b1c085949!8m2!3d10.3501878!4d107.0695199!16s%2Fg%2F1thzr8jq?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA3MUgBUAM%3D', 'Vũng Tàu'),
(83, 'Lanh\'s Homestay', '1417/20 Trần Phú, Lộc Châu, Bảo Lộc, Lâm Đồng 66465, Việt Nam', '', 11.52571710, 107.76914100, 4, '2026-01-29 13:36:10', 7, '', 'https://www.google.com/maps/place/Lanh\'s+Homestay/@11.5257171,107.769141,17.26z/data=!4m9!3m8!1s0x3173f7de87565385:0x8e58834baec94163!5m2!4m1!1i2!8m2!3d11.5277781!4d107.7732531!16s%2Fg%2F11w1v8ck1b?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA3MUgBUAM%3D', 'Bảo Lộc'),
(84, 'NHÀ MÁ TOÁN', '138 Nguyễn Tri Phương, Lộc Thọ, Bảo Lộc, Lâm Đồng, Việt Nam', '', 11.55064200, 107.78337940, 3, '2026-01-29 13:39:52', 7, '', 'https://www.google.com/maps/place/NH%C3%80+M%C3%81+TO%C3%81N/@11.550642,107.7833794,18z/data=!3m1!4b1!4m9!3m8!1s0x3173f7691b77b145:0x5850bf72e10d79c!5m2!4m1!1i2!8m2!3d11.550642!4d107.7844361!16s%2Fg%2F11w3n4yr03?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA3MUgBUAM%3D', 'Bảo Lộc'),
(85, 'Xôi mặn ba hùng', 'GRW6+847, Lê Thị Pha, Phường 1, Bảo Lộc, Lâm Đồng, Việt Nam', 'Mở cửa 15h-23h', 11.54586200, 107.81031890, 5, '2026-01-29 13:44:23', 5, '', 'https://www.google.com/maps/place/X%C3%B4i+m%E1%BA%B7n+ba+h%C3%B9ng/@11.545862,107.8103189,20.52z/data=!4m6!3m5!1s0x3173f73c78e7df7d:0xfd3dba7828e73375!8m2!3d11.5457971!4d107.8103077!16s%2Fg%2F11hz6crxlm?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA3MUgBUAM%3D', 'Bảo Lộc'),
(86, '四季王- TRÀ TỨ QUÝ VƯƠNG', '27 Lê Hồng Phong, Phường 1, Bảo Lộc, Lâm Đồng, Việt Nam', 'Mở cửa 7h30-22h', 11.54776900, 107.80990360, 5, '2026-01-29 13:48:37', 2, '', 'https://www.google.com/maps/place/%E5%9B%9B%E5%AD%A3%E7%8E%8B-+TR%C3%80+T%E1%BB%A8+QU%C3%9D+V%C6%AF%C6%A0NG/@11.547769,107.8099036,19.35z/data=!4m6!3m5!1s0x3173f6531568e3f7:0x559a7ec4143e7d70!8m2!3d11.5482274!4d107.8097415!16s%2Fg%2F11c1wsvnp_?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA3MUgBUAM%3D', 'Bảo Lộc'),
(87, 'Mì hến - Món huế O châu', '235 Hồ Tùng Mậu, Phường 1, Bảo Lộc, Lâm Đồng, Việt Nam', 'Mở cửa 6h30-22h', 11.55815620, 107.81064710, 5, '2026-01-29 13:52:49', 1, '', 'https://www.google.com/maps/place/M%C3%B3n+hu%E1%BA%BF+O+ch%C3%A2u/@11.5581562,107.8106471,17z/data=!3m1!4b1!4m6!3m5!1s0x3173f700025ae9a9:0x351a699882c3882e!8m2!3d11.5581562!4d107.815518!16s%2Fg%2F11y86vg2r5?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA3MUgBUAM%3D', 'Bảo Lộc'),
(88, 'Bờ-Rét-Phớt (Breakfast)', '4/3 Nguyễn Trung Trực, phường 1, Bảo Lộc, Lâm Đồng, Việt Nam', '', 11.53969180, 107.79265360, 5, '2026-01-29 13:55:16', 1, '', 'https://www.google.com/maps/place/B%E1%BB%9D-R%C3%A9t-Ph%E1%BB%9Bt+(Breakfast)/@11.5396918,107.7926536,17z/data=!3m1!4b1!4m6!3m5!1s0x3173f720aaca352d:0xc7ff4cd8ceb4ba00!8m2!3d11.5396918!4d107.7952285!16s%2Fg%2F11wwzw9mhb?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA3MUgBUAM%3D', 'Bảo Lộc'),
(89, 'Coffee Nhà Gỗ Trong Rừng', 'Khu Đồi Thông Phương Bối, Lê Thị Riêng, Lộc Châu, Bảo Lộc, Lâm Đồng 66465, Việt Nam', 'Mở cửa 6h30-23h', 11.52260360, 107.75059540, 5, '2026-01-29 13:57:33', 2, '', 'https://www.google.com/maps/place/Nh%C3%A0+G%E1%BB%97+Trong+R%E1%BB%ABng/@11.5226036,107.7505954,17z/data=!4m15!1m8!3m7!1s0x3173f59cb3da8fb7:0xe269fb4a81df4d8d!2zTmjDoCBH4buXIFRyb25nIFLhu6tuZw!8m2!3d11.522736!4d107.750807!10e5!16s%2Fg%2F11lclqlbwl!3m5!1s0x3173f59cb3da8fb7:0xe269fb4a81df4d8d!8m2!3d11.522736!4d107.750807!16s%2Fg%2F11lclqlbwl?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA3MUgBUAM%3D', 'Bảo Lộc'),
(90, 'Bánh Cuốn Huệ', '30 Lê Văn Tám, Phường 2, Bảo Lộc, Lâm Đồng, Việt Nam', '', 11.54663690, 107.80488770, 5, '2026-01-29 14:00:17', 1, '', 'https://www.google.com/maps/place/B%C3%A1nh+Cu%E1%BB%91n+Hu%E1%BB%87/@11.5466369,107.8048877,17z/data=!4m15!1m8!3m7!1s0x3173f7000979da43:0x16db5ab10526dbd9!2zQsOhbmggQ3Xhu5FuIEh14buH!8m2!3d11.5468432!4d107.8049037!10e5!16s%2Fg%2F11y3rlm3rh!3m5!1s0x3173f7000979da43:0x16db5ab10526dbd9!8m2!3d11.5468432!4d107.8049037!16s%2Fg%2F11y3rlm3rh?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA3MUgBUAM%3D', 'Bảo Lộc'),
(91, 'Đen Coffee', '76/4 Lam Sơn, Lộc Sơn, Bảo Lộc, Lâm Đồng 670000, Việt Nam', 'Mở cửa 8h-22h', 11.52969230, 107.80575550, 5, '2026-01-29 14:01:33', 2, '', 'https://www.google.com/maps/place/%C4%91en+coffee/@11.5296923,107.8057555,15z/data=!4m10!1m2!2m1!1s%C4%91en+coffee!3m6!1s0x3173f740030f1f59:0xceea8c08f944aaa6!8m2!3d11.5296923!4d107.8184142!15sCgvEkWVuIGNvZmZlZVoNIgvEkWVuIGNvZmZlZZIBBGNhZmWaASNDaFpEU1VoTk1HOW5TMFZPVkZwNlRHMVZhRzl0YmtSQkVBReABAPoBBAgAEEU!16s%2Fg%2F11k39hd5t4?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA3MUgBUAM%3D', 'Bảo Lộc'),
(92, 'Zenda Glamping / Coffee', 'GRC4+QPX, Lộc Sơn, Bảo Lộc, Lâm Đồng 670000, Việt Nam', 'Mở cửa 7h-22h', 11.52203160, 107.80685410, 5, '2026-01-29 14:03:28', 2, '', 'https://www.google.com/maps/place/Zenda+Glamping+-+B%E1%BA%A3o+L%E1%BB%99c+-+L%C3%A2m+%C4%90%E1%BB%93ng/@11.5220316,107.8068541,17z/data=!4m9!3m8!1s0x3173f7be4d46433d:0x520d9c6f44436c2!5m2!4m1!1i2!8m2!3d11.5220392!4d107.8068018!16s%2Fg%2F11kjh2d7y9?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA3MUgBUAM%3D', 'Bảo Lộc'),
(93, 'Mì né bò - Mì Tươi Bảo Lộc', '22/55 Lý Tự Trọng, Phường 2, Bảo Lộc, Lâm Đồng, Việt Nam', 'Mở cửa 6h30-21h', 11.54949160, 107.80519480, 5, '2026-01-29 14:04:29', 1, '', 'https://www.google.com/maps/place/M%C3%AC+T%C6%B0%C6%A1i+B%E1%BA%A3o+L%E1%BB%99c/@11.5494916,107.8051948,17z/data=!4m15!1m8!3m7!1s0x3173f78a83b8fdbd:0x411c9c3056f02be5!2zTcOsIFTGsMahaSBC4bqjbyBM4buZYw!8m2!3d11.5495399!4d107.8053286!10e9!16s%2Fg%2F11r1m_6fng!3m5!1s0x3173f78a83b8fdbd:0x411c9c3056f02be5!8m2!3d11.5495399!4d107.8053286!16s%2Fg%2F11r1m_6fng?entry=ttu&g_ep=EgoyMDI2MDEyNi4wIKXMDSoKLDEwMDc5MjA3MUgBUAM%3D', 'Bảo Lộc'),
(94, 'Asa Coffee', '141 Đường số 19, Bình Trị Đông B, Bình Tân, Thành phố Hồ Chí Minh', '', 10.75311320, 106.61360010, 5, '2026-01-31 01:41:37', 2, 'Bình Tân', 'https://www.google.com/maps/place/Asa+Coffee/@10.7531132,106.6136001,17z/data=!3m1!4b1!4m6!3m5!1s0x31752d964e2dcd1b:0xa145ad0e3ebb8f34!8m2!3d10.7531132!4d106.6136001!16s%2Fg%2F11k48kyyht!18m1!1e1?entry=ttu&g_ep=EgoyMDI2MDEyOC4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh'),
(95, 'Mỳ ý cua - Hậu Giang Q6', '128 Đ. Hậu Giang, Phường 2, Quận 6, Thành phố Hồ Chí Minh', '', 10.74988200, 106.64515400, 5, '2026-01-31 10:05:57', 1, 'Quận 6', 'https://www.google.com/maps/place/My%CC%80+y%CC%81+Cua/@10.749882,106.645154,17z/data=!4m14!1m7!3m6!1s0x31752f007006e35f:0xd1fc72605e80c980!2zTXnMgCB5zIEgQ3Vh!8m2!3d10.749882!4d106.645154!16s%2Fg%2F11yjm89lld!3m5!1s0x31752f007006e35f:0xd1fc72605e80c980!8m2!3d10.749882!4d106.645154!16s%2Fg%2F11yjm89lld!18m1!1e1?entry=ttu&g_ep=EgoyMDI2MDEyOC4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh'),
(96, 'Bánh tráng Bình Tiên', '71/1 Đ. Bình Tiên, Phường 7, Quận 6, Thành phố Hồ Chí Minh', 'Mở cửa 11h-23h', 10.74160300, 106.64315800, 5, '2026-01-31 10:08:22', 5, 'Quận 6', 'https://www.google.com/maps/place/B%C3%A1nh+Tr%C3%A1ng+D%E1%BA%BBo+M%E1%BB%81m+Tr%E1%BB%99n/@10.741603,106.643158,17z/data=!3m1!4b1!4m6!3m5!1s0x31752f0015a2ad13:0x3fd3f74892071f2c!8m2!3d10.741603!4d106.643158!16s%2Fg%2F11yhg5mc06!18m1!1e1?entry=ttu&g_ep=EgoyMDI2MDEyOC4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh'),
(97, 'Vách-Tô tượng trà trái cây', '152/32 Lạc Long Quân, Phường 3, Quận 11, Thành phố Hồ Chí Minh', 'Mở cửa 16h-23h', 10.75945560, 106.63816070, 5, '2026-01-31 13:49:49', 3, 'Quận 11', 'https://www.google.com/maps/place/V%C3%81CH+Tr%C3%A0+tr%C3%A1i+c%C3%A2y+kh%C3%B4ng+siro/@10.7594556,106.6381607,17z/data=!3m1!4b1!4m6!3m5!1s0x31752fd4314cf309:0x4d99451ad87b4f7d!8m2!3d10.7594556!4d106.6381607!16s%2Fg%2F11wjnyclp1!18m1!1e1?entry=ttu&g_ep=EgoyMDI2MDEyOC4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh'),
(98, 'Tiệm cà phê Nhà Bên Suối', '3 Trúc Lâm Yên Tử, Phường 3, Đà Lạt, Lâm Đồng 670000, Việt Nam', 'Mở cửa 7h-18h30', 11.90268210, 108.44305920, 5, '2026-02-02 14:27:58', 2, '', 'https://www.google.com/maps/place/Ti%E1%BB%87m+c%C3%A0+ph%C3%AA+Nh%C3%A0+B%C3%AAn+Su%E1%BB%91i/@11.9026821,108.4430592,17z/data=!4m20!1m10!3m9!1s0x3171155c6580d69f:0xa20caeddd1448e8e!2zVGnhu4dtIGPDoCBwaMOqIE5ow6AgQsOqbiBTdeG7kWk!5m2!4m1!1i2!8m2!3d11.9022833!4d108.444353!16s%2Fg%2F11q21hw7k4!3m8!1s0x3171155c6580d69f:0xa20caeddd1448e8e!5m2!4m1!1i2!8m2!3d11.9022833!4d108.444353!16s%2Fg%2F11q21hw7k4?entry=ttu&g_ep=EgoyMDI2MDEyOC4wIKXMDSoASAFQAw%3D%3D', 'Đà Lạt'),
(99, 'Bánh mì xíu mại Bé Linh', '37 Đ. Hoàng Diệu, Phường 5, Đà Lạt, Lâm Đồng 66000, Việt Nam', 'Mở cửa 5h30-12h', 11.94247930, 108.42594550, 5, '2026-02-02 14:32:23', 1, '', 'https://www.google.com/maps/place/B%C3%A1nh+m%C3%AC+x%C3%ADu+m%E1%BA%A1i+B%C3%A9+Linh/@11.9424793,108.4259455,17z/data=!3m1!4b1!4m6!3m5!1s0x3171139e3197f9f1:0xb5a321e0d297523a!8m2!3d11.9424741!4d108.4285204!16s%2Fg%2F11v0c8p6nb?entry=ttu&g_ep=EgoyMDI2MDEyOC4wIKXMDSoASAFQAw%3D%3D', 'Đà Lạt'),
(100, 'Cafe persimmon', '96b Đ. Hùng Vương, Phường 9, Đà Lạt, Lâm Đồng, Việt Nam', 'Mở cửa 7h-20h30', 11.94739640, 108.47075590, 5, '2026-02-02 14:33:01', 2, '', 'https://www.google.com/maps/place/Cafe+persimmon/@11.9473964,108.4707559,17z/data=!3m1!4b1!4m6!3m5!1s0x3171135353f08715:0x4b720973b4905032!8m2!3d11.9473912!4d108.4733308!16s%2Fg%2F11m_ltt72p?entry=ttu&g_ep=EgoyMDI2MDEyOC4wIKXMDSoASAFQAw%3D%3D', 'Đà Lạt'),
(101, 'Nem nướng Bà Hùng', 'D51 & D52, Khu Quy Hoạch Hoàng Văn Thụ, Phường 4, Đà Lạt, Lâm Đồng 66115, Việt Nam', 'Mở cửa 9h-21h', 11.93742160, 108.42669630, 5, '2026-02-02 14:34:10', 1, '', 'https://www.google.com/maps/place/Nem+n%C6%B0%E1%BB%9Bng+B%C3%A0+H%C3%B9ng/@11.9374216,108.4266963,17z/data=!3m1!4b1!4m6!3m5!1s0x317113c91ba39fc3:0xf85e4a96124b0ca8!8m2!3d11.9374164!4d108.4292712!16s%2Fg%2F11lkff08br?entry=ttu&g_ep=EgoyMDI2MDEyOC4wIKXMDSoASAFQAw%3D%3D', 'Đà Lạt'),
(102, 'Làng Khét Đà Lạt', '27 Huỳnh Tấn Phát, Phường 11, Đà Lạt, Lâm Đồng 66000, Việt Nam', 'Mở cửa 16h30-22h', 11.95340640, 108.48891420, 5, '2026-02-02 14:34:51', 1, '', 'https://www.google.com/maps/place/L%C3%A0ng+Kh%C3%A9t+%C4%90%C3%A0+L%E1%BA%A1t/@11.9534064,108.4889142,17z/data=!3m1!4b1!4m6!3m5!1s0x31711350fc831e4b:0x5f450600a57c1f99!8m2!3d11.9534012!4d108.4914891!16s%2Fg%2F11t7mhwbrv?entry=ttu&g_ep=EgoyMDI2MDEyOC4wIKXMDSoASAFQAw%3D%3D', 'Đà Lạt'),
(103, 'Tao Ngộ Quán - Lẩu Gà Lá É', '10Bis Ba Tháng Tư, Phường 3, Đà Lạt, Lâm Đồng, Việt Nam', 'Mở cửa 8h-22h', 11.93162160, 108.44214730, 5, '2026-02-02 14:36:07', 1, '', 'https://www.google.com/maps/place/Tao+Ng%E1%BB%99+Qu%C3%A1n+-+L%E1%BA%A9u+G%C3%A0+L%C3%A1+%C3%89/@11.9316216,108.4421473,16z/data=!4m6!3m5!1s0x317113da5837b369:0x1c65e2a1c4583019!8m2!3d11.9315475!4d108.4460606!16s%2Fg%2F11fqbk8qs9?entry=ttu&g_ep=EgoyMDI2MDEyOC4wIKXMDSoASAFQAw%3D%3D', 'Đà Lạt'),
(104, 'Ollin Café', '9 Đ. Nguyễn Chí Thanh, Phường 1, Đà Lạt, Lâm Đồng, Việt Nam', 'Mở cửa 6h30-22h30', 11.94139130, 108.43401220, 5, '2026-02-02 14:36:52', 2, '', 'https://www.google.com/maps/place/Ollin+Caf%C3%A9/@11.9413913,108.4340122,17z/data=!3m1!4b1!4m6!3m5!1s0x3171130009eda09f:0x55ee27fce0bf76e2!8m2!3d11.9413861!4d108.4365871!16s%2Fg%2F11xfsn6tbz?entry=ttu&g_ep=EgoyMDI2MDEyOC4wIKXMDSoASAFQAw%3D%3D', 'Đà Lạt'),
(105, 'Củi Bistro Bar', 'Sân thượng, Y12 Đ. Hồng Lĩnh, Cư xá Bắc Hải, Quận 10, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 17h-1h', 10.78035830, 106.66123510, 5, '2026-02-02 14:45:29', 3, 'Quận 10', 'https://www.google.com/maps/place/C%E1%BB%A7i+Bistro+Bar/@10.7803583,106.6612351,17z/data=!4m14!1m7!3m6!1s0x31752f0d0740b711:0xb0a23fa22a444dbe!2sC%E1%BB%A7i+Bistro+Bar!8m2!3d10.780353!4d106.66381!16s%2Fg%2F11sw_1hdjr!3m5!1s0x31752f0d0740b711:0xb0a23fa22a444dbe!8m2!3d10.780353!4d106.66381!16s%2Fg%2F11sw_1hdjr?entry=ttu&g_ep=EgoyMDI2MDEyOC4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh');
INSERT INTO `places` (`id`, `name`, `address`, `description`, `latitude`, `longitude`, `rating`, `created_at`, `category_id`, `district`, `original_link`, `city`) VALUES
(106, 'Dimsum Mr. Hào', '175 Trần Tuấn Khải, Phường 5, Quận 5, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 16h30-22h', 10.75272730, 106.67370290, 5, '2026-02-02 14:48:25', 1, 'Quận 5', 'https://www.google.com/maps/place/Dimsum+Mr.+H%C3%A0o/@10.7527273,106.6737029,17z/data=!4m15!1m8!3m7!1s0x31752efdd5e22181:0xfaf341b8b517ff3f!2sDimsum+Mr.+H%C3%A0o!8m2!3d10.7526471!4d106.6735571!10e5!16s%2Fg%2F11c32bckdy!3m5!1s0x31752efdd5e22181:0xfaf341b8b517ff3f!8m2!3d10.7526471!4d106.6735571!16s%2Fg%2F11c32bckdy?entry=ttu&g_ep=EgoyMDI2MDEyOC4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh'),
(107, 'Há Cảo Minh Ký', '76 Đ. Nguyễn Thời Trung, Phường 5, Quận 5, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 15h30-21h30', 10.75242070, 106.66984500, 5, '2026-02-02 14:49:34', 1, 'Quận 5', 'https://www.google.com/maps/place/H%C3%A1+C%E1%BA%A3o+Minh+K%C3%BD/@10.7524207,106.669845,21z/data=!4m15!1m8!3m7!1s0x31752efdd5e22181:0xfaf341b8b517ff3f!2sDimsum+Mr.+H%C3%A0o!8m2!3d10.7526471!4d106.6735571!10e5!16s%2Fg%2F11c32bckdy!3m5!1s0x31752ef956d88c8f:0x14ef044954361e3a!8m2!3d10.7524092!4d106.6698776!16s%2Fg%2F11c432cpxq?entry=ttu&g_ep=EgoyMDI2MDEyOC4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','viewer') NOT NULL DEFAULT 'viewer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(3, 'thanhdat', '$2y$10$SdxzpMpiu/gWkVXo9o56suUTVfv0010LaXlthmLh02h17aRFWqhZy', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `city_id` (`city_id`);

--
-- Indexes for table `places`
--
ALTER TABLE `places`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `districts`
--
ALTER TABLE `districts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `places`
--
ALTER TABLE `places`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `districts`
--
ALTER TABLE `districts`
  ADD CONSTRAINT `districts_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
