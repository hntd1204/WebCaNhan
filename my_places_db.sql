-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 29, 2026 at 10:35 AM
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
-- Database: `my_places_db`
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
(3, 'Giải trí'),
(1, 'Quán ăn'),
(2, 'Quán uống');

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
(40, 'iệm cà phê Thập niên 2000', '125/2B Hoà Hưng, Phường 12, Quận 10, Thành phố Hồ Chí Minh 700000, Việt Nam', 'Mở cửa 7h30-22h30', 10.77821380, 106.66995620, 5, '2026-01-29 09:01:26', 2, 'Quận 10', 'https://www.google.com/maps/place/Ti%E1%BB%87m+c%C3%A0+ph%C3%AA+Th%E1%BA%ADp+ni%C3%AAn+2000/@10.7782138,106.6699562,17z/data=!3m1!4b1!4m6!3m5!1s0x31752f6696cf54ed:0x7b1ddf0978c7c86!8m2!3d10.7782138!4d106.6725365!16s%2Fg%2F11jt02s8st?entry=ttu&g_ep=EgoyMDI2MDEyNy4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh'),
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
(51, 'HẸ QUÁN - Bánh canh hẹ', '113 Cao Thắng, Phường 10, Quận 10, Thành phố Hồ Chí Minh, Việt Nam', 'Mở cửa 7h30-21h30', 10.77277460, 106.67621540, 5, '2026-01-29 09:12:21', 1, 'Quận 10', 'https://www.google.com/maps/place/H%E1%BA%B8+QU%C3%81N+-+B%C3%A1nh+canh+h%E1%BA%B9+_+B%C3%A1nh+b%C3%A8o+n%C3%B3ng+Ph%C3%BA+Y%C3%AAn/@10.7727746,106.6762154,17z/data=!3m1!4b1!4m6!3m5!1s0x31752f0715717629:0x21b050446eb31191!8m2!3d10.7727746!4d106.6787957!16s%2Fg%2F11fm_ll_mt?entry=ttu&g_ep=EgoyMDI2MDEyNy4wIKXMDSoASAFQAw%3D%3D', 'Hồ Chí Minh');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `places`
--
ALTER TABLE `places`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
