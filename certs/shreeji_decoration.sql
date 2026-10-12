-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 16, 2026 at 08:42 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12
SET SESSION sql_require_primary_key = 0;

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shreeji_decoration`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_features`
--

DROP TABLE IF EXISTS `; DROP TABLE IF EXISTS `; CREATE TABLE `about_features` (
  `id` int(11) NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_features`
--

INSERT INTO `about_features` (`id`, `icon`, `title`, `description`) VALUES
(1, 'bi-patch-check-fill', '100% Authentic', 'Genuine Indian materials'),
(2, 'bi-truck', 'Pan India Delivery', 'Doorstep convenience'),
(3, 'bi-pencil-square', 'Custom Designs', 'Bespoke decor solutions'),
(4, 'bi-headset', 'Expert Support', '7 days a week assistance');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `; DROP TABLE IF EXISTS `; CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `; DROP TABLE IF EXISTS `; CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `icon`, `type`, `status`) VALUES
(1, 'Wedding', NULL, NULL, 1),
(2, 'Birthday', NULL, NULL, 1),
(3, 'Festival', NULL, NULL, 1),
(4, 'Puja', NULL, NULL, 1),
(5, 'Home Decor', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `; DROP TABLE IF EXISTS `; CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `otp` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `status` enum('Active','Blocked') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `address`, `otp`, `is_verified`, `status`, `created_at`, `last_login`) VALUES
(1, 'devam parekh', '07405670316', 'devamparekh1200@gmail.com', 'd', NULL, 1, 'Active', '2026-07-07 05:14:37', '2026-08-20 14:18:42'),
(2, 'DEVAM parekh', '8160449412', 'devamparekh120@gmail.com', 'Nutan Row House, B/H vidhyakunj Schhol , Pal gam road Adajan, Surat', NULL, 1, 'Active', '2026-07-07 08:04:01', '2026-09-16 12:00:32'),
(4, 'jaya', '9426770014', 'jayaparekh06@gmail.com', NULL, NULL, 1, 'Active', '2026-08-20 16:07:33', '2026-08-20 21:50:35');

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

DROP TABLE IF EXISTS `; DROP TABLE IF EXISTS `; CREATE TABLE `faq` (
  `id` int(11) NOT NULL,
  `question` text DEFAULT NULL,
  `answer` longtext DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `status` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faq`
--

INSERT INTO `faq` (`id`, `question`, `answer`, `display_order`, `status`) VALUES
(1, 'Hii', 'Hii', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

DROP TABLE IF EXISTS `; DROP TABLE IF EXISTS `; CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `title`, `image`, `category`, `status`) VALUES
(6, 'Op', '1787243861_WIN_20250618_18_35_50_Pro.jpg', 'Wedding', 1),
(7, 'party', '1787243925_shopping (1).webp', 'Festival', 1),
(8, 'Birthday', '1787243938_shopping.webp', 'Birthday', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `; CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_id` varchar(20) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `payment_method` varchar(100) DEFAULT NULL,
  `products` longtext DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `cart_data` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_seen` tinyint(1) DEFAULT 0,
  `customer_latitude` varchar(50) DEFAULT NULL,
  `customer_longitude` varchar(50) DEFAULT NULL,
  `distance_km` decimal(10,2) DEFAULT NULL,
  `delivery_charge` decimal(10,2) DEFAULT 0.00,
  `payment_status` varchar(20) NOT NULL DEFAULT 'pending',
  `razorpay_order_id` varchar(80) DEFAULT NULL,
  `razorpay_payment_id` varchar(80) DEFAULT NULL,
  `razorpay_signature` varchar(255) DEFAULT NULL,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `order_id`, `customer_name`, `phone`, `email`, `address`, `payment_method`, `products`, `total_amount`, `order_date`, `status`, `cart_data`, `created_at`, `admin_seen`, `customer_latitude`, `customer_longitude`, `distance_km`, `delivery_charge`, `payment_status`, `razorpay_order_id`, `razorpay_payment_id`, `razorpay_signature`, `amount_paid`, `paid_at`) VALUES
(3, 0, '65066472', 'devam parekh', '07405670316', 'devamparekh120@oksbi', '19,Nutan Row House', 'Cash On Delivery', '[{\"id\":\"1\",\"name\":\"Wedding Decoration Kit\",\"price\":\"1999.00\",\"image\":\"product1.jpg\",\"qty\":1},{\"id\":\"2\",\"name\":\"Diwali Decoration \",\"price\":\"899.00\",\"image\":\"1779116003_Screenshot 2025-09-16 144313.png\",\"qty\":9}]', 10090.00, '2026-05-19 11:34:30', 'Pending', NULL, '2026-05-29 12:05:51', 1, NULL, NULL, NULL, 0.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(4, 0, '27902094', 'devam parekh', '07405670316', 'devamparekh120@oksbi', '19,Nutan Row House', 'Cash On Delivery', '[{\"id\":\"4\",\"name\":\"Chairs\",\"price\":\"120.00\",\"image\":\"\",\"qty\":2},{\"id\":\"2\",\"name\":\"Diwali Decoration \",\"price\":\"899.00\",\"image\":\"1779116003_Screenshot 2025-09-16 144313.png\",\"qty\":2}]', 2038.00, '2026-05-25 19:11:06', 'Pending', NULL, '2026-05-29 12:05:51', 1, NULL, NULL, NULL, 0.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(5, 0, '83202279', 'devam parekh', '07405670316', 'devamparekh120@oksbi', '19,Nutan Row House', 'Cash On Delivery', '[{\"id\":\"4\",\"name\":\"Chairs\",\"price\":\"120.00\",\"image\":\"\",\"qty\":2},{\"id\":\"2\",\"name\":\"Diwali Decoration \",\"price\":\"899.00\",\"image\":\"1779116003_Screenshot 2025-09-16 144313.png\",\"qty\":2}]', 2038.00, '2026-05-25 19:11:52', 'Pending', NULL, '2026-05-29 12:05:51', 1, NULL, NULL, NULL, 0.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(6, 0, '38232265', 'devam parekh', '07405670316', 'devamparekh120@oksbi', '19,Nutan Row House', 'Cash On Delivery', '[{\"id\":\"4\",\"name\":\"Chairs\",\"price\":\"120.00\",\"image\":\"\",\"qty\":2},{\"id\":\"2\",\"name\":\"Diwali Decoration \",\"price\":\"899.00\",\"image\":\"1779116003_Screenshot 2025-09-16 144313.png\",\"qty\":2}]', 2038.00, '2026-05-26 04:04:18', 'Pending', NULL, '2026-05-29 12:05:51', 1, NULL, NULL, NULL, 0.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(7, 2, '56917991', 'DEVAM parekh', '1234567891', 'devamparekh120@gmail.com', 'Nutan Row House, B/H vidhyakunj Schhol , Pal gam road Adajan, Surat', 'Cash On Delivery', '[{\"id\":4,\"name\":\"Chairs\",\"price\":120,\"image\":\"\",\"qty\":1}]', 120.00, '2026-05-26 04:06:58', 'Pending', NULL, '2026-05-29 12:05:51', 1, NULL, NULL, NULL, 0.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(8, 0, '77262286', 'devam parekh', '07405670316', 'devamparekh120@oksbi', '19,Nutan Row House', 'Cash On Delivery', '[{\"id\":4,\"name\":\"Chairs\",\"price\":120,\"image\":\"\",\"qty\":1}]', 120.00, '2026-05-26 04:21:36', 'Pending', NULL, '2026-05-29 12:05:51', 1, NULL, NULL, NULL, 0.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(9, 0, '66585197', 'devam parekh', '07405670316', 'devamparekh120@oksbi', '19,Nutan Row House', 'Cash On Delivery', '[{\"id\":2,\"name\":\"Diwali Decoration \",\"price\":899,\"image\":\"1779116003_Screenshot 2025-09-16 144313.png\",\"qty\":1}]', 899.00, '2026-05-26 04:21:55', 'Pending', NULL, '2026-05-29 12:05:51', 1, NULL, NULL, NULL, 0.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(10, 0, '62327858', 'devam parekh', '07405670316', 'devamparekh120@oksbi', '19,Nutan Row House', 'Cash On Delivery', '[{\"id\":2,\"name\":\"Diwali Decoration \",\"price\":899,\"image\":\"1779116003_Screenshot 2025-09-16 144313.png\",\"qty\":1}]', 899.00, '2026-05-26 06:25:20', 'Pending', NULL, '2026-05-29 12:05:51', 1, NULL, NULL, NULL, 0.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(11, 0, '82547963', 'devam parekh', '07405670316', 'devamparekh120@oksbi', '19,Nutan Row House', 'Cash On Delivery', '[{\"id\":4,\"name\":\"Chairs\",\"price\":120,\"image\":\"\",\"qty\":1}]', 120.00, '2026-05-26 06:27:20', 'Pending', NULL, '2026-05-29 12:05:51', 1, NULL, NULL, NULL, 0.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(12, 0, '49101328', 'devam parekh', '07405670316', 'devamparekh120@oksbi', '19,Nutan Row House', 'Cash On Delivery', '[{\"id\":4,\"name\":\"Chairs\",\"price\":120,\"image\":\"\",\"qty\":1}]', 120.00, '2026-05-26 07:52:02', 'Pending', NULL, '2026-05-29 12:05:51', 1, NULL, NULL, NULL, 0.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(16, 2, '47913758', 'DEVAM parekh', '8164235750', 'devamparekh120@gmail.com', 'Nutan Row House, B/H vidhyakunj Schhol , Pal gam road Adajan, Surat', 'UPI', '[{\"id\":2,\"name\":\"Diwali Decoration \",\"price\":899,\"image\":\"1779116003_Screenshot 2025-09-16 144313.png\",\"qty\":1}]', 899.00, '2026-06-14 05:50:48', 'Completed', NULL, '2026-06-14 05:50:48', 1, NULL, NULL, NULL, 0.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(17, 2, '66037791', 'DEVAM parekh', '07405670316', 'devamparekh120@gmail.com', 'Nutan Row House, B/H vidhyakunj Schhol , Pal gam road Adajan, Surat', 'Cash On Delivery', '[{\"id\":8,\"name\":\"hat\",\"price\":23.75,\"image\":\"\",\"qty\":1}]', 23.75, '2026-06-14 07:06:03', 'Completed', NULL, '2026-06-14 07:06:03', 1, NULL, NULL, NULL, 0.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(18, 2, '25485208', 'DEVAM parekh', '07405670316', 'devamparekh120@gmail.com', 'Nutan Row House, B/H vidhyakunj Schhol , Pal gam road Adajan, Surat', 'Cash On Delivery', '[{\"id\":8,\"name\":\"hat\",\"price\":23.75,\"image\":\"\",\"qty\":1},{\"id\":7,\"name\":\"hat\",\"price\":23.75,\"image\":\"\",\"qty\":1},{\"id\":6,\"name\":\"ballom\",\"price\":50,\"image\":\"\",\"qty\":1}]', 97.50, '2026-06-14 07:06:32', 'Completed', NULL, '2026-06-14 07:06:32', 1, NULL, NULL, NULL, 0.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(19, 0, '71679808', 'devam parekh', '07405670316', 'devamparekh120@oksbi', '19,Nutan Row House', 'Cash On Delivery', '[{\"id\":9,\"name\":\"Wedding Decoration Kit\",\"price\":475,\"image\":\"\",\"qty\":2}]', 950.00, '2026-06-14 09:38:28', 'Completed', NULL, '2026-06-14 09:38:28', 1, NULL, NULL, NULL, 0.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(20, 0, '43628216', 'devam parekh', '07405670316', 'devamparekh120@oksbi', '19,Nutan Row House', 'Cash On Delivery', '[{\"id\":9,\"name\":\"Wedding Decoration Kit\",\"price\":475,\"image\":\"\",\"gst_rate\":5,\"qty\":1},{\"id\":\"10\",\"name\":\"Bell\",\"price\":\"15.00\",\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":\"1.68\",\"qty\":1}]', 634.00, '2026-06-15 17:16:18', 'Completed', NULL, '2026-06-15 17:16:18', 1, '21.204934195282362', '72.78032562735078', 6.53, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(21, 0, '70184907', 'devam parekh', '7405670316', 'devamparekh120@oksbi', '19,Nutan Row House', 'Cash On Delivery', '[{\"id\":9,\"name\":\"Wedding Decoration Kit\",\"price\":475,\"image\":\"\",\"gst_rate\":5,\"qty\":3}]', 1496.25, '2026-06-20 18:08:43', 'Completed', NULL, '2026-06-20 18:08:43', 1, '21.204946000000003', '72.78027333333333', 6.53, 0.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(22, 0, '28108604', 'devam parekh', '1234567890', 'devamparekh120@oksbi', '19,Nutan Row House', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1}]', 135.25, '2026-06-20 18:39:29', 'Return Rejected', NULL, '2026-06-20 18:39:29', 1, '21.2049541900019', '72.78021204017014', 6.54, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(23, 2, '50693199', 'DEVAM parekh', '8160449412', 'devamparekh120@gmail.com', 'Nutan Row House, B/H vidhyakunj Schhol , Pal gam road Adajan, Surat', 'Cash On Delivery', '[{\"id\":6,\"name\":\"ballom\",\"price\":50,\"image\":\"\",\"gst_rate\":18,\"qty\":1}]', 179.00, '2026-06-21 14:11:16', 'Return Approved', NULL, '2026-06-21 14:11:16', 1, '21.204935', '72.780281', 6.54, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(24, 0, '64900407', 'DEVAM parekh', '1234567890', 'abc@gmail.com', 'Nutan Row House, B/H vidhyakunj Schhol , Pal gam road Adajan, Surat', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1}]', 135.25, '2026-06-23 05:56:36', 'Pending', NULL, '2026-06-23 05:56:36', 1, '21.204954546039165', '72.78027157002975', 6.54, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(25, 0, '65004572', 'DEVAM parekh', '1234567890', 'abc@gmail.com', 'Nutan Row House, B/H vidhyakunj Schhol , Pal gam road Adajan, Surat', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1}]', 135.25, '2026-06-23 05:59:27', 'Pending', NULL, '2026-06-23 05:59:27', 1, '21.204954546039165', '72.78027157002975', 6.54, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(26, 0, '74178987', 'DEVAM parekh', '1234567890', 'devamparekh120@sbo.vom', 'Nutan Row House, B/H vidhyakunj Schhol , Pal gam road Adajan, Surat', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1}]', 135.25, '2026-06-23 05:59:48', 'Pending', NULL, '2026-06-23 05:59:48', 1, '21.204970669011427', '72.78025757907614', 6.54, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(27, 0, '31993013', 'DEVAM parekh', '1234567890', 'devamparekh120@sbo.vom', 'Nutan Row House, B/H vidhyakunj Schhol , Pal gam road Adajan, Surat', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1}]', 135.25, '2026-06-23 06:00:14', 'Pending', NULL, '2026-06-23 06:00:14', 1, '21.204970669011427', '72.78025757907614', 6.54, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(28, 0, '58307116', 'DEVAM parekh', '1234567890', 'devamparekh120@sbo.vom', 'Nutan Row House, B/H vidhyakunj Schhol , Pal gam road Adajan, Surat', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1}]', 135.25, '2026-06-23 06:00:18', 'Pending', NULL, '2026-06-23 06:00:18', 1, '21.204970669011427', '72.78025757907614', 6.54, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(29, 0, '51423431', 'DEVAM parekh', '1234567890', 'devamparekh120@sbo.vom', 'Nutan Row House, B/H vidhyakunj Schhol , Pal gam road Adajan, Surat', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1}]', 135.25, '2026-06-23 06:00:40', 'Pending', NULL, '2026-06-23 06:00:40', 1, '21.204970669011427', '72.78025757907614', 6.54, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(30, 0, '26183223', 'DEVAM parekh', '1234567890', 'devamparekh120@sbo.vom', 'Nutan Row House, B/H vidhyakunj Schhol , Pal gam road Adajan, Surat', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1}]', 135.25, '2026-06-23 06:01:00', 'Pending', NULL, '2026-06-23 06:01:00', 1, '21.204970669011427', '72.78025757907614', 6.54, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(31, 0, '75034120', 'DEVAM parekh', '1234567890', 'devamparekh120@sbo.vom', 'Nutan Row House, B/H vidhyakunj Schhol , Pal gam road Adajan, Surat', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1}]', 135.25, '2026-06-23 06:27:03', 'Pending', NULL, '2026-06-23 06:27:03', 1, '21.204970669011427', '72.78025757907614', 6.54, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(32, 0, '98179633', 'DEVAM parekh', '1234567890', 'devamparekh120@sbo.vom', 'Nutan Row House, B/H vidhyakunj Schhol , Pal gam road Adajan, Surat', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1}]', 135.25, '2026-06-23 06:27:05', 'Pending', NULL, '2026-06-23 06:27:05', 1, '21.204970669011427', '72.78025757907614', 6.54, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(33, 2, '29643468', 'DEVAM parekh', '1234567890', 'devamparekh120@gmail.com', 'Nutan Row House, B/H vidhyakunj Schhol , Pal gam road Adajan, Surat', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1}]', 135.25, '2026-06-23 06:28:59', 'Pending', NULL, '2026-06-23 06:28:59', 1, '21.204981228816678', '72.78027412291385', 6.54, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(34, 2, '68659821', 'DEVAM parekh', '1234567890', 'devamparekh120@gmail.com', 'Nutan Row House, B/H vidhyakunj Schhol , Pal gam road Adajan, Surat', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1}]', 135.25, '2026-06-24 05:31:35', 'Pending', NULL, '2026-06-24 05:31:35', 1, '', '', 6.54, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(35, 2, '63839546', 'DEVAM parekh', '1234567890', 'devamparekh120@gmail.com', 'Nutan Row House, B/H vidhyakunj Schhol , Pal gam road Adajan, Surat', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1}]', 135.25, '2026-06-24 05:41:56', 'Pending', NULL, '2026-06-24 05:41:56', 1, '', '', 6.54, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(36, 2, '48430061', 'DEVAM parekh', '1234567890', 'devamparekh120@gmail.com', 'Nutan Row House, B/H vidhyakunj Schhol , Pal gam road Adajan, Surat', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1}]', 135.25, '2026-06-24 17:57:39', 'Pending', NULL, '2026-06-24 17:57:39', 1, '', '', 6.54, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(37, 2, '81576230', 'DEVAM parekh', '1234567890', 'devamparekh120@gmail.com', 'Nutan Row House, B/H vidhyakunj Schhol , Pal gam road Adajan, Surat', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1}]', 135.25, '2026-06-24 18:13:51', 'Pending', NULL, '2026-06-24 18:13:51', 1, '', '', 6.54, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(38, 0, '53627391', 'devam parekh', '07405670316', 'mukesh6471@gmail.com', '19,Nutan Row House', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1}]', 135.25, '2026-06-24 18:15:52', 'Pending', NULL, '2026-06-24 18:15:52', 1, '21.20494310040062', '72.78021949004768', 6.54, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(39, 2, '61966839', 'devam parekh', '07405670316', 'devamparekh120@gmail.com', '19,Nutan Row House', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1},{\"id\":9,\"name\":\"Wedding Decoration Kit\",\"price\":475,\"image\":\"\",\"gst_rate\":5,\"qty\":1},{\"id\":8,\"name\":\"hat\",\"price\":23.75,\"image\":\"\",\"gst_rate\":2.98,\"qty\":1},{\"id\":7,\"name\":\"hat\",\"price\":23.75,\"image\":\"\",\"gst_rate\":18,\"qty\":1},{\"id\":2,\"name\":\"Diwali Decoration \",\"price\":899,\"image\":\"1779116003_Screenshot 2025-09-16 144313.png\",\"gst_rate\":18,\"qty\":1},{\"id\":4,\"name\":\"Chairs\",\"price\":120,\"image\":\"\",\"gst_rate\":18,\"qty\":1},{\"id\":6,\"name\":\"ballom\",\"price\":50,\"image\":\"\",\"gst_rate\":18,\"qty\":1},{\"id\":5,\"name\":\"Ballons\",\"price\":49.5,\"image\":\"\",\"gst_rate\":18,\"qty\":1},{\"id\":1,\"name\":\"Wedding Decoration \",\"price\":1859.07,\"image\":\"product1.jpg\",\"gst_rate\":18,\"qty\":1}]', 4080.02, '2026-06-24 18:34:23', 'Pending', NULL, '2026-06-24 18:34:23', 1, '21.204940776931235', '72.78022533631078', 6.54, 0.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(40, 2, '64312716', 'DEVAM parekh', '1234567890', 'devamparekh120@gmail.com', 'Nutan Row House, B/H vidhyakunj Schhol , Pal gam road Adajan, Surat', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1},{\"id\":9,\"name\":\"Wedding Decoration Kit\",\"price\":475,\"image\":\"\",\"gst_rate\":5,\"qty\":1}]', 514.00, '2026-06-24 18:36:34', 'Pending', NULL, '2026-06-24 18:36:34', 1, '21.204956639485136', '72.78021325062984', 6.54, 0.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(41, 2, '93404013', 'devam parekh', '07405670316', 'devamparekh120@gmail.com', '19,Nutan Row House', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1},{\"id\":8,\"name\":\"hat\",\"price\":23.75,\"image\":\"\",\"gst_rate\":2.98,\"qty\":1},{\"id\":9,\"name\":\"Wedding Decoration Kit\",\"price\":475,\"image\":\"\",\"gst_rate\":5,\"qty\":1},{\"id\":7,\"name\":\"hat\",\"price\":23.75,\"image\":\"\",\"gst_rate\":18,\"qty\":1},{\"id\":2,\"name\":\"Diwali Decoration \",\"price\":899,\"image\":\"1779116003_Screenshot 2025-09-16 144313.png\",\"gst_rate\":18,\"qty\":1},{\"id\":4,\"name\":\"Chairs\",\"price\":120,\"image\":\"\",\"gst_rate\":18,\"qty\":1},{\"id\":5,\"name\":\"Ballons\",\"price\":49.5,\"image\":\"\",\"gst_rate\":18,\"qty\":1},{\"id\":6,\"name\":\"ballom\",\"price\":50,\"image\":\"\",\"gst_rate\":18,\"qty\":1},{\"id\":1,\"name\":\"Wedding Decoration \",\"price\":1859.07,\"image\":\"product1.jpg\",\"gst_rate\":18,\"qty\":1}]', 4080.02, '2026-06-24 18:40:13', 'Pending', NULL, '2026-06-24 18:40:13', 1, '21.204940776931235', '72.78022533631078', 6.54, 0.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(42, 2, '99649814', 'DEVAM parekh', '07405670316', 'devamparekh120@gmail.com', 'Nutan Row House, B/H vidhyakunj Schhol , Pal gam road Adajan, Surat', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1}]', 15.25, '2026-06-24 18:44:27', 'Pending', NULL, '2026-06-24 18:44:27', 1, '21.204940720837467', '72.78022089452834', 6.54, 0.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(43, 2, '66876559', 'devam parekh', '07405670316', 'devamparekh120@gmail.com', '19,Nutan Row House', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1}]', 135.25, '2026-06-25 04:54:23', 'Pending', NULL, '2026-06-25 04:54:23', 1, '21.20497709775251', '72.78025265697649', 6.54, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(44, 2, '87875251', 'DEVAM parekh', '7405670316', 'devamparekh120@gmail.com', 'Nutan Row House, B/H vidhyakunj Schhol , Pal gam road Adajan, Surat', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1}]', 135.25, '2026-06-25 05:05:17', 'Pending', NULL, '2026-06-25 05:05:17', 1, '21.204939169012757', '72.78027473786729', 6.54, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(45, 0, '53313314', 'Dipaliben pareshbhai parekh', '8160449412', 'parekhdiya301@gmail.com', 'alishan', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1},{\"id\":9,\"name\":\"Wedding Decoration Kit\",\"price\":475,\"image\":\"\",\"gst_rate\":5,\"qty\":1},{\"id\":8,\"name\":\"hat\",\"price\":23.75,\"image\":\"\",\"gst_rate\":2.98,\"qty\":1},{\"id\":7,\"name\":\"hat\",\"price\":23.75,\"image\":\"\",\"gst_rate\":18,\"qty\":1},{\"id\":6,\"name\":\"ballom\",\"price\":50,\"image\":\"\",\"gst_rate\":18,\"qty\":1},{\"id\":5,\"name\":\"Ballons\",\"price\":49.5,\"image\":\"\",\"gst_rate\":18,\"qty\":1},{\"id\":4,\"name\":\"Chairs\",\"price\":120,\"image\":\"\",\"gst_rate\":18,\"qty\":1},{\"id\":2,\"name\":\"Diwali Decoration \",\"price\":899,\"image\":\"1779116003_Screenshot 2025-09-16 144313.png\",\"gst_rate\":18,\"qty\":1},{\"id\":1,\"name\":\"Wedding Decoration \",\"price\":1859.07,\"image\":\"product1.jpg\",\"gst_rate\":18,\"qty\":1}]', 4080.02, '2026-06-25 09:15:45', 'Pending', NULL, '2026-06-25 09:15:45', 1, '21.20495155586118', '72.78028066352823', 6.54, 0.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(46, 0, '12613047', 'Dipaliben pareshbhai parekh', '8160449412', 'parekhdiya301@gmail.com', 'alishan', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1},{\"id\":9,\"name\":\"Wedding Decoration Kit\",\"price\":475,\"image\":\"\",\"gst_rate\":5,\"qty\":1},{\"id\":8,\"name\":\"hat\",\"price\":23.75,\"image\":\"\",\"gst_rate\":2.98,\"qty\":1},{\"id\":7,\"name\":\"hat\",\"price\":23.75,\"image\":\"\",\"gst_rate\":18,\"qty\":1},{\"id\":6,\"name\":\"ballom\",\"price\":50,\"image\":\"\",\"gst_rate\":18,\"qty\":1},{\"id\":5,\"name\":\"Ballons\",\"price\":49.5,\"image\":\"\",\"gst_rate\":18,\"qty\":1},{\"id\":4,\"name\":\"Chairs\",\"price\":120,\"image\":\"\",\"gst_rate\":18,\"qty\":1},{\"id\":2,\"name\":\"Diwali Decoration \",\"price\":899,\"image\":\"1779116003_Screenshot 2025-09-16 144313.png\",\"gst_rate\":18,\"qty\":1},{\"id\":1,\"name\":\"Wedding Decoration \",\"price\":1859.07,\"image\":\"product1.jpg\",\"gst_rate\":18,\"qty\":1}]', 4080.02, '2026-06-25 09:16:05', 'Pending', NULL, '2026-06-25 09:16:05', 1, '21.20495155586118', '72.78028066352823', 6.54, 0.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(47, 2, '23116292', 'devam parekh', '8160449412', 'devamparekh120@gmail.com', 'asferg', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1}]', 135.25, '2026-07-07 08:12:59', 'Completed', NULL, '2026-07-07 08:12:59', 1, '21.204934903820522', '72.78019842070192', 6.54, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(48, 1, '82167393', 'DEVAM parekh', '8160449412', 'devamparekh1200@gmail.com', 'd', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1}]', 135.25, '2026-07-09 17:51:29', 'Completed', NULL, '2026-07-09 17:51:29', 1, '21.20494142431994', '72.78021749267354', 6.54, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(49, 2, '94799131', 'devam parekh', '07405670316', 'devamparekh120@gmail.com', 'rsdtuyr', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1}]', 135.25, '2026-07-31 22:34:29', 'Pending', NULL, '2026-07-31 22:34:29', 1, '18.45887852058501', '73.83515577792478', 319.25, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(50, 2, '76032932', 'devam parekh', '07405670316', 'devamparekh120@gmail.com', 'rsdtuyr', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1},{\"id\":\"7\",\"name\":\"hat\",\"price\":23.75,\"image\":\"\",\"gst_rate\":\"18.00\",\"qty\":1}]', 163.28, '2026-08-02 09:16:41', 'Pending', NULL, '2026-08-02 09:16:41', 1, '18.459050108005584', '73.83531047227665', 319.24, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(51, 2, '23367055', 'devam parekh', '07405670316', 'devamparekh120@gmail.com', 'rsdtuyr', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1},{\"id\":7,\"name\":\"hat\",\"price\":23.75,\"image\":\"\",\"gst_rate\":18,\"qty\":1},{\"id\":6,\"name\":\"ballom\",\"price\":50,\"image\":\"\",\"gst_rate\":18,\"qty\":1},{\"id\":5,\"name\":\"Ballons\",\"price\":49.5,\"image\":\"\",\"gst_rate\":18,\"qty\":1}]', 280.69, '2026-08-02 10:54:20', 'Pending', NULL, '2026-08-02 10:54:20', 1, '18.45881853032469', '73.83524512762915', 319.26, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(52, 1, '80069501', 'devam parekh', '8160449412', 'devamparekh1200@gmail.com', 'd', 'Cash On Delivery', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1},{\"id\":7,\"name\":\"hat\",\"price\":23.75,\"image\":\"\",\"gst_rate\":18,\"qty\":1},{\"id\":6,\"name\":\"ballom\",\"price\":50,\"image\":\"\",\"gst_rate\":18,\"qty\":1},{\"id\":5,\"name\":\"Ballons\",\"price\":49.5,\"image\":\"\",\"gst_rate\":18,\"qty\":1}]', 280.69, '2026-08-14 07:49:40', 'Pending', NULL, '2026-08-14 07:49:40', 0, '18.51975321880313', '73.81762097194833', 312.25, 120.00, 'pending', NULL, NULL, NULL, 0.00, NULL),
(53, 2, '63113359', 'DEVAM parekh', '8160449412', 'devamparekh120@gmail.com', 'asferg', 'Razorpay', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"gst_rate\":1.68,\"qty\":3,\"total\":45},{\"id\":7,\"name\":\"hat\",\"price\":23.75,\"gst_rate\":18,\"qty\":1,\"total\":23.75}]', 193.79, '2026-09-16 06:36:25', 'Payment Failed', NULL, '2026-09-16 06:36:25', 0, '21.204858392383', '72.780246303809', 312.25, 120.00, 'failed', NULL, NULL, NULL, 0.00, NULL),
(54, 2, '48348593', 'DEVAM parekh', '8160449412', 'devamparekh120@gmail.com', 'Nutan Row House, B/H vidhyakunj Schhol , Pal gam road Adajan, Surat', 'Razorpay', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"gst_rate\":1.68,\"qty\":3,\"total\":45},{\"id\":7,\"name\":\"hat\",\"price\":23.75,\"gst_rate\":18,\"qty\":1,\"total\":23.75}]', 193.79, '2026-09-16 06:41:40', 'Payment Failed', NULL, '2026-09-16 06:41:40', 0, '21.204849', '72.780251', 6.53, 120.00, 'failed', NULL, NULL, NULL, 0.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `; CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `price`, `qty`, `total`) VALUES
(1, 66876559, 10, 'Bell', 15.00, 1, 15.00),
(2, 87875251, 10, 'Bell', 15.00, 1, 15.00),
(3, 53313314, 10, 'Bell', 15.00, 1, 15.00),
(4, 53313314, 9, 'Wedding Decoration Kit', 475.00, 1, 475.00),
(5, 53313314, 8, 'hat', 23.75, 1, 23.75),
(6, 53313314, 7, 'hat', 23.75, 1, 23.75),
(7, 53313314, 6, 'ballom', 50.00, 1, 50.00),
(8, 53313314, 5, 'Ballons', 49.50, 1, 49.50),
(9, 53313314, 4, 'Chairs', 120.00, 1, 120.00),
(10, 53313314, 2, 'Diwali Decoration ', 899.00, 1, 899.00),
(11, 53313314, 1, 'Wedding Decoration ', 1859.07, 1, 1859.07),
(12, 12613047, 10, 'Bell', 15.00, 1, 15.00),
(13, 12613047, 9, 'Wedding Decoration Kit', 475.00, 1, 475.00),
(14, 12613047, 8, 'hat', 23.75, 1, 23.75),
(15, 12613047, 7, 'hat', 23.75, 1, 23.75),
(16, 12613047, 6, 'ballom', 50.00, 1, 50.00),
(17, 12613047, 5, 'Ballons', 49.50, 1, 49.50),
(18, 12613047, 4, 'Chairs', 120.00, 1, 120.00),
(19, 12613047, 2, 'Diwali Decoration ', 899.00, 1, 899.00),
(20, 12613047, 1, 'Wedding Decoration ', 1859.07, 1, 1859.07),
(21, 23116292, 10, 'Bell', 15.00, 1, 15.00),
(22, 82167393, 10, 'Bell', 15.00, 1, 15.00),
(23, 94799131, 10, 'Bell', 15.00, 1, 15.00),
(24, 76032932, 10, 'Bell', 15.00, 1, 15.00),
(25, 76032932, 7, 'hat', 23.75, 1, 23.75),
(26, 23367055, 10, 'Bell', 15.00, 1, 15.00),
(27, 23367055, 7, 'hat', 23.75, 1, 23.75),
(28, 23367055, 6, 'ballom', 50.00, 1, 50.00),
(29, 23367055, 5, 'Ballons', 49.50, 1, 49.50),
(30, 80069501, 10, 'Bell', 15.00, 1, 15.00),
(31, 80069501, 7, 'hat', 23.75, 1, 23.75),
(32, 80069501, 6, 'ballom', 50.00, 1, 50.00),
(33, 80069501, 5, 'Ballons', 49.50, 1, 49.50);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `; CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `badge` varchar(50) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `reviews` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `discount` int(11) NOT NULL DEFAULT 0,
  `gst_rate` decimal(5,2) NOT NULL DEFAULT 18.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `slug`, `price`, `old_price`, `stock`, `category`, `type`, `description`, `image`, `badge`, `rating`, `reviews`, `status`, `created_at`, `stock_quantity`, `discount`, `gst_rate`) VALUES
(1, 'Wedding Decoration ', NULL, 1999.00, NULL, 44, 'Wedding', NULL, 'Beautiful wedding decor set', 'product1.jpg', NULL, NULL, NULL, 1, '2026-05-16 06:39:50', 0, 7, 18.00),
(2, 'Diwali Decoration ', NULL, 899.00, NULL, 992, 'Festival', NULL, 'Premium Diwali decorations', '1779116003_Screenshot 2025-09-16 144313.png', NULL, NULL, NULL, 1, '2026-05-16 06:39:50', 0, 0, 18.00),
(4, 'Chairs', NULL, 120.00, NULL, 492, 'Wedding', 'Retail', 'used in seeting', '', NULL, NULL, NULL, 1, '2026-05-25 19:10:38', 0, 0, 18.00),
(5, 'Ballons', NULL, 50.00, NULL, 44, 'Birthday', 'Both', 'birthday\r\n', '', NULL, NULL, NULL, 1, '2026-06-04 05:56:33', 0, 1, 18.00),
(6, 'ballom', NULL, 50.00, NULL, 492, 'Festival', 'Retail', 'birthday', '', NULL, NULL, NULL, 1, '2026-06-04 05:57:35', 0, 0, 18.00),
(7, 'hat', NULL, 25.00, NULL, 42, 'Birthday', 'Retail', 'hat', '', NULL, NULL, NULL, 1, '2026-06-04 06:20:53', 0, 5, 18.00),
(10, 'Bell', NULL, 15.00, NULL, 82, 'Puja', 'Retail', 'hihi\r\n', 'prod_6a3032c3cfd18.png', NULL, NULL, NULL, 1, '2026-06-15 17:13:39', 0, 0, 1.68);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `; CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `display_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image`, `display_order`) VALUES
(1, 10, 'prod_gallery_6a7451863b79d.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

DROP TABLE IF EXISTS `; CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `rating` int(11) NOT NULL,
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'Pending',
  `admin_seen` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_reviews`
--

INSERT INTO `product_reviews` (`id`, `order_id`, `product_id`, `customer_name`, `phone`, `rating`, `review`, `created_at`, `status`, `admin_seen`) VALUES
(5, 19, 0, 'devam parekh', '07405670316', 5, 'vwdswf\r\n', '2026-06-23 04:42:19', 'Approved', 1),
(6, 18, 0, 'DEVAM parekh', '07405670316', 5, '\r\nsaf', '2026-06-23 04:49:00', 'Approved', 1),
(7, 16, 0, 'DEVAM parekh', '8164235750', 5, 'asdasd\r\n', '2026-06-23 05:00:21', 'Rejected', 1),
(9, 82167393, 10, 'DEVAM parekh', '8160449412', 5, 'Hiii\r\n\r\n', '2026-07-10 18:15:03', 'Approved', 1);

-- --------------------------------------------------------

--
-- Table structure for table `quote_requests`
--

DROP TABLE IF EXISTS `; CREATE TABLE `quote_requests` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `inquiry_type` varchar(100) DEFAULT NULL,
  `occasion` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('New','Contacted','Completed') DEFAULT 'New',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_seen` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quote_requests`
--

INSERT INTO `quote_requests` (`id`, `name`, `phone`, `email`, `inquiry_type`, `occasion`, `message`, `status`, `created_at`, `admin_seen`) VALUES
(1, '', '', '', '', '', '', 'New', '2026-06-04 13:40:04', 1),
(2, '', '', '', '', '', '', 'New', '2026-06-04 13:40:25', 1),
(3, 'devam parekh', '07405670316', 'devamparekh120@oksbi', 'Retail Purchase', 'Birthday', 'asgg', 'New', '2026-06-14 09:11:55', 1),
(4, 'DEVAM parekh', '07405670316', 'devamparekh120@gmail.com', 'Wholesale / Dealer Inquiry', 'Birthday', 'fgwet', 'New', '2026-06-14 09:12:14', 1),
(5, 'devam parekh', '07405670316', 'devamparekh120@oksbi', 'Retail Purchase', 'qwd', 'Dwd', 'New', '2026-06-14 09:26:52', 1),
(6, 'Diya', '8160449412', 'diya_parekh19@gmail.com', 'Retail Purchase', 'Birthday', 'Hiii', 'New', '2026-08-02 08:35:11', 1);

-- --------------------------------------------------------

--
-- Table structure for table `retail_features`
--

DROP TABLE IF EXISTS `; CREATE TABLE `retail_features` (
  `id` int(11) NOT NULL,
  `icon` varchar(20) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `retail_features`
--

INSERT INTO `retail_features` (`id`, `icon`, `title`, `description`, `display_order`) VALUES
(1, '🚚', 'Free Delivery above ₹999', 'Pan India shipping via trusted courier partners. Same-day dispatch for orders before 2 PM.', 1),
(2, '✅', 'Quality Guaranteed', 'Every product quality-checked before dispatch. 7-day easy returns if you are not satisfied.', 2),
(3, '🎨', 'Custom Decoration Orders', 'Need something unique? We create fully customised decoration kits for your exact event theme.', 3),
(4, '💳', 'Safe & Secure Payments', 'UPI, debit/credit cards, net banking, COD & EMI options — fully encrypted checkout.', 4);

-- --------------------------------------------------------

--
-- Table structure for table `retail_trust_items`
--

DROP TABLE IF EXISTS `; CREATE TABLE `retail_trust_items` (
  `id` int(11) NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `subtitle` varchar(150) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `retail_trust_items`
--

INSERT INTO `retail_trust_items` (`id`, `icon`, `title`, `subtitle`, `display_order`) VALUES
(1, 'bi-truck-front-fill', 'Free Delivery', 'On orders above ₹999', 1),
(2, 'bi-arrow-counterclockwise', 'Easy Returns', 'Hassle-free return policy', 2),
(3, 'bi-shield-check-fill', '100% Authentic', 'Genuine Indian products', 3),
(4, 'bi-headset', 'Expert Support', 'WhatsApp 9AM–9PM', 4),
(5, 'bi-lock-fill', 'Secure Payment', 'UPI, Cards, COD', 5);

-- --------------------------------------------------------

--
-- Table structure for table `return_requests`
--

DROP TABLE IF EXISTS `; CREATE TABLE `return_requests` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT 0,
  `customer_name` varchar(150) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `customer_email` varchar(150) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `return_requests`
--

INSERT INTO `return_requests` (`id`, `order_id`, `product_id`, `customer_name`, `product_name`, `customer_email`, `reason`, `status`, `created_at`) VALUES
(1, 20, 0, 'devam parekh', '[{\"id\":9,\"name\":\"Wedding Decoration Kit\",\"price\":475,\"image\":\"\",\"gst_rate\":5,\"qty\":1},{\"id\":\"10\",\"name\":\"Bell\",\"price\":\"15.00\",\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":\"1.68\",\"qty\":1}]', NULL, 'Dont need this', 'Approved', '2026-06-20 18:57:57'),
(2, 23, 0, 'DEVAM parekh', '[{\"id\":6,\"name\":\"ballom\",\"price\":50,\"image\":\"\",\"gst_rate\":18,\"qty\":1}]', NULL, 'swsef', 'Approved', '2026-06-21 14:11:48'),
(3, 22, 0, 'devam parekh', '[{\"id\":10,\"name\":\"Bell\",\"price\":15,\"image\":\"prod_6a3032c3cfd18.png\",\"gst_rate\":1.68,\"qty\":1}]', NULL, 'cdsf', 'Rejected', '2026-06-21 14:13:02');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `; CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `website_name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `gst_percent` int(11) DEFAULT NULL,
  `delivery_charge` int(11) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `hero_title` text DEFAULT NULL,
  `hero_subtitle` text DEFAULT NULL,
  `about_text` text DEFAULT NULL,
  `footer_text` text DEFAULT NULL,
  `map_link` text DEFAULT NULL,
  `shop_timing` varchar(255) DEFAULT NULL,
  `copyright_text` text DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `hero_btn_text` varchar(255) DEFAULT NULL,
  `hero_btn_link` varchar(255) DEFAULT NULL,
  `hero_btn2_text` varchar(255) DEFAULT NULL,
  `about_image` varchar(255) DEFAULT NULL,
  `footer_logo` varchar(255) DEFAULT NULL,
  `shop_latitude` varchar(50) DEFAULT NULL,
  `shop_longitude` varchar(50) DEFAULT NULL,
  `free_delivery_km` int(11) DEFAULT 3,
  `free_delivery_charge` decimal(10,2) DEFAULT 0.00,
  `medium_delivery_km` int(11) DEFAULT 10,
  `medium_delivery_charge` decimal(10,2) DEFAULT 50.00,
  `long_delivery_charge` decimal(10,2) DEFAULT 120.00,
  `free_delivery_order_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_products` varchar(50) DEFAULT '200+',
  `happy_customers` varchar(50) DEFAULT '5000+',
  `avg_rating` varchar(50) DEFAULT '4.9★',
  `free_delivery_amount` int(11) DEFAULT 999,
  `return_policy_days` int(11) DEFAULT 7,
  `smtp_host` varchar(255) DEFAULT NULL,
  `smtp_port` varchar(50) DEFAULT NULL,
  `smtp_email` varchar(255) DEFAULT NULL,
  `smtp_password` varchar(255) DEFAULT NULL,
  `wholesale_catalogue` varchar(255) DEFAULT NULL,
  `footer_tagline` varchar(255) DEFAULT NULL,
  `footer_newsletter_heading` varchar(100) DEFAULT NULL,
  `footer_newsletter_text` varchar(255) DEFAULT NULL,
  `footer_payment_methods` varchar(255) DEFAULT NULL,
  `wholesale_hero_title` varchar(255) DEFAULT NULL,
  `wholesale_hero_subtitle` text DEFAULT NULL,
  `wholesale_stat1_num` varchar(30) DEFAULT NULL,
  `wholesale_stat1_label` varchar(60) DEFAULT NULL,
  `wholesale_stat2_num` varchar(30) DEFAULT NULL,
  `wholesale_stat2_label` varchar(60) DEFAULT NULL,
  `wholesale_stat3_num` varchar(30) DEFAULT NULL,
  `wholesale_stat3_label` varchar(60) DEFAULT NULL,
  `wholesale_stat4_num` varchar(30) DEFAULT NULL,
  `wholesale_stat4_label` varchar(60) DEFAULT NULL,
  `wholesale_group_link` varchar(255) DEFAULT NULL,
  `wholesale_group_heading` varchar(100) DEFAULT NULL,
  `wholesale_group_text` varchar(255) DEFAULT NULL,
  `wholesale_catalogue_heading` varchar(100) DEFAULT NULL,
  `wholesale_catalogue_text` varchar(255) DEFAULT NULL,
  `wholesale_phone` varchar(20) DEFAULT NULL,
  `wholesale_email` varchar(255) DEFAULT NULL,
  `retail_hero_badge` varchar(100) DEFAULT NULL,
  `retail_hero_title` varchar(255) DEFAULT NULL,
  `retail_hero_subtitle` text DEFAULT NULL,
  `retail_kpi4_num` varchar(30) DEFAULT NULL,
  `retail_kpi4_label` varchar(60) DEFAULT NULL,
  `retail_banner_chip` varchar(100) DEFAULT NULL,
  `retail_banner_heading` varchar(255) DEFAULT NULL,
  `retail_banner_text` text DEFAULT NULL,
  `retail_switch_heading` varchar(255) DEFAULT NULL,
  `retail_switch_text` text DEFAULT NULL,
  `razorpay_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `razorpay_mode` varchar(10) NOT NULL DEFAULT 'test',
  `razorpay_key_id` varchar(100) DEFAULT NULL,
  `razorpay_key_secret` varchar(100) DEFAULT NULL,
  `razorpay_webhook_secret` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `website_name`, `phone`, `email`, `address`, `instagram`, `facebook`, `whatsapp`, `gst_percent`, `delivery_charge`, `logo`, `hero_title`, `hero_subtitle`, `about_text`, `footer_text`, `map_link`, `shop_timing`, `copyright_text`, `youtube`, `twitter`, `linkedin`, `meta_description`, `meta_keywords`, `favicon`, `hero_btn_text`, `hero_btn_link`, `hero_btn2_text`, `about_image`, `footer_logo`, `shop_latitude`, `shop_longitude`, `free_delivery_km`, `free_delivery_charge`, `medium_delivery_km`, `medium_delivery_charge`, `long_delivery_charge`, `free_delivery_order_amount`, `total_products`, `happy_customers`, `avg_rating`, `free_delivery_amount`, `return_policy_days`, `smtp_host`, `smtp_port`, `smtp_email`, `smtp_password`, `wholesale_catalogue`, `footer_tagline`, `footer_newsletter_heading`, `footer_newsletter_text`, `footer_payment_methods`, `wholesale_hero_title`, `wholesale_hero_subtitle`, `wholesale_stat1_num`, `wholesale_stat1_label`, `wholesale_stat2_num`, `wholesale_stat2_label`, `wholesale_stat3_num`, `wholesale_stat3_label`, `wholesale_stat4_num`, `wholesale_stat4_label`, `wholesale_group_link`, `wholesale_group_heading`, `wholesale_group_text`, `wholesale_catalogue_heading`, `wholesale_catalogue_text`, `wholesale_phone`, `wholesale_email`, `retail_hero_badge`, `retail_hero_title`, `retail_hero_subtitle`, `retail_kpi4_num`, `retail_kpi4_label`, `retail_banner_chip`, `retail_banner_heading`, `retail_banner_text`, `retail_switch_heading`, `retail_switch_text`, `razorpay_enabled`, `razorpay_mode`, `razorpay_key_id`, `razorpay_key_secret`, `razorpay_webhook_secret`) VALUES
(1, 'Shreeji Decoration', '7874144228', 'devamparekh1200@gmail.com', 'Surat,Gujarat\r\n', 'https://www.instagram.com/shreeji_decoration_adajan?igsh=MW5sYXdvbDl5cHJwYw==', '', '', NULL, NULL, 'logo_1787246929.jpeg', 'Buy More', '', NULL, 'Hello', 'https://maps.google.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '21.1702', '72.8311', 3, 5.00, 5, 10.00, 120.00, 999.00, '200+', '5000+', '4.9★', 999, 7, 'smtp.gmail.com', '587', 'devamparekh1200@gmail.com', 'idat kfqi zxbq bjkv', 'catalogue_1785589404.pdf', 'Hii', 'Newsletter', 'Get seasonal offers, new arrivals, and festival special deals.', 'UPI,NetBanking,Cards,COD,EMI', 'India\'s #1 Wholesale Decoration Supplier', 'Serving 500+ active dealers across India with premium decoration products, competitive bulk pricing, and 48-hour dispatch — all year round.', '500+', 'Active Dealers', '40%', 'Max Discount', '₹5K', 'Min. Order', '48hr', 'Dispatch', 'https://chat.whatsapp.com/', 'Join WhatsApp Group', 'Get real-time stock updates, flash sale alerts, and new festival collections before anyone else.', 'Download Catalogue', '200+ products with MOQs, wholesale pricing tiers, and product photos — free on request.', '+919876543211', 'wholesale@shreejidecoration.com', 'For Everyone — Individuals & Families', 'Shop Beautiful Indian Decorations', '200+ handpicked decoration products for weddings, festivals, birthdays, puja & every celebration in between — delivered to your door.', 'Free', 'Delivery ₹999+', 'Why Choose Us', 'Shop with Confidence', '15+ years of expertise in Indian decoration. Every product hand-checked, every order packed with care.', '🏪 Are You a Dealer or Business?', 'Get wholesale pricing, bulk discounts up to 40%, and a dedicated account manager for your store or event business.', 1, 'test', 'rzp_test_...', 'rzp_test_...', '');

-- --------------------------------------------------------

--
-- Table structure for table `site_content`
--

DROP TABLE IF EXISTS `; CREATE TABLE `site_content` (
  `id` int(11) NOT NULL,
  `page_name` varchar(100) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `since_year` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_content`
--

INSERT INTO `site_content` (`id`, `page_name`, `title`, `subtitle`, `content`, `image`, `image2`, `since_year`) VALUES
(1, 'about', 'Hello', '', '', '1785563640_city-restaurant-lights-382297.jpg', '1785563645_2_pexels-thatguycraig000-2306281.jpg', '2016'),
(2, 'privacy', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'terms', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'shipping', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'return', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

DROP TABLE IF EXISTS `; CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `review` text DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `; CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `status` int(11) DEFAULT 1,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`, `phone`) VALUES
(3, 'Admin', 'admin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, '9876543210');

-- --------------------------------------------------------

--
-- Table structure for table `wholesale_benefits`
--

DROP TABLE IF EXISTS `; CREATE TABLE `wholesale_benefits` (
  `id` int(11) NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wholesale_benefits`
--

INSERT INTO `wholesale_benefits` (`id`, `icon`, `title`, `description`, `display_order`) VALUES
(1, 'bi-percent', 'Up to 40% Off MRP', 'Tiered dealer pricing — higher volumes unlock better margins. Exclusive rates for regular partners.', 1),
(2, 'bi-truck-front-fill', 'Pan India Delivery', 'Bulk-packed orders dispatched within 48 hours to all major cities via trusted courier partners.', 2),
(3, 'bi-patch-check-fill', 'GST Invoice & Compliance', 'Full tax invoicing from our GST-registered business — hassle-free for all your B2B accounting.', 3),
(4, 'bi-stars', 'Consistent Quality', 'Every batch quality-checked before dispatch. Same premium standard across all orders, every season.', 4),
(5, 'bi-palette2', 'Custom & OEM Orders', 'Custom colour combos, branding & packaging available on minimum quantity runs.', 5),
(6, 'bi-headset', 'Dedicated Support', 'Exclusive wholesale manager — WhatsApp support 9 AM to 9 PM, 7 days a week, year round.', 6);

-- --------------------------------------------------------

--
-- Table structure for table `wholesale_requests`
--

DROP TABLE IF EXISTS `; CREATE TABLE `wholesale_requests` (
  `id` int(11) NOT NULL,
  `business_name` varchar(255) DEFAULT NULL,
  `owner_name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_seen` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wholesale_requests`
--

INSERT INTO `wholesale_requests` (`id`, `business_name`, `owner_name`, `phone`, `email`, `city`, `address`, `message`, `status`, `created_at`, `admin_seen`) VALUES
(1, 'Shrey decor', 'Shrey', '8974561230', 'shrey@gmail.com', 'Surat', NULL, 'best', 'Contacted', '2026-06-05 14:42:17', 1),
(2, 'Shrey decor', 'Shrey', '07405670316', 'devamparekh120@oksbi', 'Surat', NULL, 'sdfgws', 'Pending', '2026-06-14 07:06:48', 1),
(3, 'Shrey decor', 'Shrey', '07405670316', 'devamparekh120@oksbi', 'Surat', NULL, 'weret', 'Pending', '2026-06-14 09:32:24', 1);

-- --------------------------------------------------------

--
-- Table structure for table `wholesale_steps`
--

DROP TABLE IF EXISTS `; CREATE TABLE `wholesale_steps` (
  `id` int(11) NOT NULL,
  `step_number` int(11) DEFAULT 0,
  `icon` varchar(100) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wholesale_steps`
--

INSERT INTO `wholesale_steps` (`id`, `step_number`, `icon`, `title`, `description`, `display_order`) VALUES
(1, 1, 'bi-person-plus-fill', 'Register', 'Submit your business name, shop details, and GST number via WhatsApp or the contact form below.', 1),
(2, 2, 'bi-journal-richtext', 'Get Catalogue', 'Receive our exclusive wholesale price list and full product catalogue within 24 hours.', 2),
(3, 3, 'bi-cart-check-fill', 'Place Order', 'Confirm your order by WhatsApp or call. We accept UPI, NEFT, cards, and bank transfer.', 3),
(4, 4, 'bi-truck-front-fill', 'Fast Delivery', 'Securely bulk-packed and dispatched within 48 working hours via trusted Pan India couriers.', 4);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

DROP TABLE IF EXISTS `; CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_features`
--
ALTER TABLE `about_features`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rzp_order` (`razorpay_order_id`),
  ADD KEY `idx_rzp_payment` (`razorpay_payment_id`),
  ADD KEY `idx_order_id` (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quote_requests`
--
ALTER TABLE `quote_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `retail_features`
--
ALTER TABLE `retail_features`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `retail_trust_items`
--
ALTER TABLE `retail_trust_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `return_requests`
--
ALTER TABLE `return_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_content`
--
ALTER TABLE `site_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wholesale_benefits`
--
ALTER TABLE `wholesale_benefits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wholesale_requests`
--
ALTER TABLE `wholesale_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wholesale_steps`
--
ALTER TABLE `wholesale_steps`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_features`
--
ALTER TABLE `about_features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `quote_requests`
--
ALTER TABLE `quote_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `retail_features`
--
ALTER TABLE `retail_features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `retail_trust_items`
--
ALTER TABLE `retail_trust_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `return_requests`
--
ALTER TABLE `return_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `site_content`
--
ALTER TABLE `site_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wholesale_benefits`
--
ALTER TABLE `wholesale_benefits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `wholesale_requests`
--
ALTER TABLE `wholesale_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wholesale_steps`
--
ALTER TABLE `wholesale_steps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
