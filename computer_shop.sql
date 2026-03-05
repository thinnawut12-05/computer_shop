-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 05, 2026 at 09:53 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `computer_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `name`, `email`, `created_at`) VALUES
(2, 'admin', '$2y$10$4eo17dFAC1f410yrL/VEv.ACedMRfsp3c6xiGFJNla9xbbfquhIJS', 'ผู้ดูแลระบบ', 'admin@shop.com', '2026-03-05 08:50:55');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT 'fa-microchip',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `description`, `created_at`) VALUES
(1, 'CPU / โปรเซสเซอร์', 'cpu', 'fa-microchip', 'ซีพียูสำหรับเดสก์ท็อปและแล็ปท็อป', '2026-03-05 08:50:23'),
(2, 'GPU / การ์ดจอ', 'gpu', 'fa-tv', 'กราฟิกการ์ดสำหรับเกมและงานออกแบบ', '2026-03-05 08:50:23'),
(3, 'RAM / แรม', 'ram', 'fa-memory', 'หน่วยความจำ DDR4/DDR5', '2026-03-05 08:50:23'),
(4, 'SSD / M.2', 'ssd', 'fa-hdd', 'ฮาร์ดดิสก์ SSD และ M.2 NVMe', '2026-03-05 08:50:23'),
(5, 'เคสคอมพิวเตอร์', 'case', 'fa-desktop', 'เคสตั้งโต๊ะทุกขนาด', '2026-03-05 08:50:23'),
(6, 'จอมอนิเตอร์', 'monitor', 'fa-tv', 'จอมอนิเตอร์ทุกขนาดและความละเอียด', '2026-03-05 08:50:23');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `shipping_address` text NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','shipping','delivered','cancelled') DEFAULT 'pending',
  `payment_method` enum('cash','transfer','cod') DEFAULT 'transfer',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT 'default.jpg',
  `specs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specs`)),
  `featured` tinyint(1) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `brand`, `model`, `description`, `price`, `stock`, `image`, `specs`, `featured`, `status`, `created_at`) VALUES
(1, 1, 'Intel Core i9-14900K', 'Intel', 'i9-14900K', 'ซีพียูเรือธงจาก Intel 24 Cores 32 Threads', 19900.00, 15, 'default.jpg', '{\"cores\":\"24\",\"threads\":\"32\",\"base_clock\":\"3.2GHz\",\"boost_clock\":\"6.0GHz\",\"socket\":\"LGA1700\",\"tdp\":\"125W\"}', 1, 'active', '2026-03-05 08:50:24'),
(2, 1, 'Intel Core i7-14700K', 'Intel', 'i7-14700K', 'ซีพียูระดับกลาง-สูง 20 Cores 28 Threads', 13500.00, 20, 'default.jpg', '{\"cores\":\"20\",\"threads\":\"28\",\"base_clock\":\"3.4GHz\",\"boost_clock\":\"5.6GHz\",\"socket\":\"LGA1700\",\"tdp\":\"125W\"}', 0, 'active', '2026-03-05 08:50:24'),
(3, 1, 'AMD Ryzen 9 7950X', 'AMD', 'Ryzen 9 7950X', 'ซีพียู AMD สำหรับงาน Workstation 16 Cores', 22500.00, 10, 'default.jpg', '{\"cores\":\"16\",\"threads\":\"32\",\"base_clock\":\"4.5GHz\",\"boost_clock\":\"5.7GHz\",\"socket\":\"AM5\",\"tdp\":\"170W\"}', 1, 'active', '2026-03-05 08:50:24'),
(4, 1, 'AMD Ryzen 5 7600X', 'AMD', 'Ryzen 5 7600X', 'ซีพียูคุ้มค่า 6 Cores AM5 Socket', 7900.00, 30, 'default.jpg', '{\"cores\":\"6\",\"threads\":\"12\",\"base_clock\":\"4.7GHz\",\"boost_clock\":\"5.3GHz\",\"socket\":\"AM5\",\"tdp\":\"105W\"}', 0, 'active', '2026-03-05 08:50:24'),
(5, 2, 'NVIDIA RTX 4090 24GB', 'NVIDIA', 'RTX 4090', 'การ์ดจอเรือธง NVIDIA Ada Lovelace 24GB GDDR6X', 59900.00, 5, 'default.jpg', '{\"vram\":\"24GB GDDR6X\",\"cuda_cores\":\"16384\",\"boost_clock\":\"2.52GHz\",\"power\":\"450W\",\"interface\":\"PCIe 4.0 x16\"}', 1, 'active', '2026-03-05 08:50:24'),
(6, 2, 'NVIDIA RTX 4070 12GB', 'NVIDIA', 'RTX 4070', 'การ์ดจอระดับกลาง-สูง เล่นเกม 4K ได้สบาย', 24900.00, 12, 'default.jpg', '{\"vram\":\"12GB GDDR6X\",\"cuda_cores\":\"5888\",\"boost_clock\":\"2.48GHz\",\"power\":\"200W\",\"interface\":\"PCIe 4.0 x16\"}', 1, 'active', '2026-03-05 08:50:24'),
(7, 2, 'AMD RX 7900 XTX 24GB', 'AMD', 'RX 7900 XTX', 'การ์ดจอ AMD RDNA3 สำหรับเกม 4K', 34900.00, 8, 'default.jpg', '{\"vram\":\"24GB GDDR6\",\"stream_processors\":\"6144\",\"boost_clock\":\"2.5GHz\",\"power\":\"355W\",\"interface\":\"PCIe 4.0 x16\"}', 0, 'active', '2026-03-05 08:50:24'),
(8, 2, 'NVIDIA RTX 4060 Ti 16GB', 'NVIDIA', 'RTX 4060 Ti', 'การ์ดจอคุ้มค่า สำหรับ 1080p-1440p', 16900.00, 20, 'default.jpg', '{\"vram\":\"16GB GDDR6\",\"cuda_cores\":\"4352\",\"boost_clock\":\"2.54GHz\",\"power\":\"160W\",\"interface\":\"PCIe 4.0 x16\"}', 0, 'active', '2026-03-05 08:50:24'),
(9, 3, 'Corsair Vengeance DDR5 32GB', 'Corsair', 'CMK32GX5M2B5600C36', 'RAM DDR5 32GB (2x16GB) 5600MHz RGB', 4900.00, 25, 'default.jpg', '{\"capacity\":\"32GB (2x16GB)\",\"type\":\"DDR5\",\"speed\":\"5600MHz\",\"latency\":\"CL36\",\"rgb\":\"Yes\"}', 1, 'active', '2026-03-05 08:50:24'),
(10, 3, 'G.Skill Trident Z5 DDR5 64GB', 'G.Skill', 'F5-6000J3238G32GX2-TZ5K', 'RAM DDR5 64GB (2x32GB) 6000MHz RGB', 8900.00, 10, 'default.jpg', '{\"capacity\":\"64GB (2x32GB)\",\"type\":\"DDR5\",\"speed\":\"6000MHz\",\"latency\":\"CL32\",\"rgb\":\"Yes\"}', 0, 'active', '2026-03-05 08:50:24'),
(11, 3, 'Kingston FURY Beast DDR4 16GB', 'Kingston', 'KF436C18BB/16', 'RAM DDR4 16GB 3600MHz ราคาคุ้มค่า', 1890.00, 50, 'default.jpg', '{\"capacity\":\"16GB\",\"type\":\"DDR4\",\"speed\":\"3600MHz\",\"latency\":\"CL18\",\"rgb\":\"No\"}', 0, 'active', '2026-03-05 08:50:24'),
(12, 4, 'Samsung 990 Pro 2TB NVMe', 'Samsung', '990 Pro', 'SSD M.2 NVMe Gen4 ความเร็วสูงสุด 7450MB/s', 4500.00, 20, 'default.jpg', '{\"capacity\":\"2TB\",\"interface\":\"M.2 NVMe PCIe 4.0\",\"read\":\"7450 MB/s\",\"write\":\"6900 MB/s\",\"form_factor\":\"M.2 2280\"}', 1, 'active', '2026-03-05 08:50:24'),
(13, 4, 'WD Black SN850X 1TB', 'Western Digital', 'SN850X', 'SSD NVMe สำหรับเกมเมอร์ PCIe Gen4', 2900.00, 15, 'default.jpg', '{\"capacity\":\"1TB\",\"interface\":\"M.2 NVMe PCIe 4.0\",\"read\":\"7300 MB/s\",\"write\":\"6300 MB/s\",\"form_factor\":\"M.2 2280\"}', 0, 'active', '2026-03-05 08:50:24'),
(14, 4, 'Kingston NV2 500GB SATA', 'Kingston', 'SNV2S/500G', 'SSD SATA 2.5 นิ้ว ราคาประหยัด', 1290.00, 40, 'default.jpg', '{\"capacity\":\"500GB\",\"interface\":\"SATA III\",\"read\":\"3500 MB/s\",\"write\":\"2100 MB/s\",\"form_factor\":\"2.5 inch\"}', 0, 'active', '2026-03-05 08:50:24'),
(15, 5, 'Lian Li PC-O11 Dynamic EVO', 'Lian Li', 'PC-O11DEW', 'เคสมิดทาวเวอร์สวยงาม รองรับ E-ATX', 4900.00, 10, 'default.jpg', '{\"form_factor\":\"Mid Tower\",\"motherboard\":\"E-ATX/ATX/mATX\",\"gpu_clearance\":\"420mm\",\"drive_bay\":\"2x 2.5 + 2x 3.5\",\"rgb\":\"No\"}', 1, 'active', '2026-03-05 08:50:24'),
(16, 5, 'NZXT H7 Flow RGB', 'NZXT', 'CM-H71BW-R1', 'เคส ATX ระบายอากาศดี พร้อม RGB', 3500.00, 12, 'default.jpg', '{\"form_factor\":\"Mid Tower\",\"motherboard\":\"ATX/mATX/ITX\",\"gpu_clearance\":\"400mm\",\"drive_bay\":\"2x 3.5 + 4x 2.5\",\"rgb\":\"Yes\"}', 0, 'active', '2026-03-05 08:50:24'),
(17, 5, 'Fractal Design Meshify 2', 'Fractal Design', 'FD-C-MES2A-05', 'เคส ATX ระบายอากาศสูงสุด Mesh Front', 3200.00, 8, 'default.jpg', '{\"form_factor\":\"Mid Tower\",\"motherboard\":\"E-ATX/ATX/mATX\",\"gpu_clearance\":\"461mm\",\"drive_bay\":\"2x 3.5 + 4x 2.5\",\"rgb\":\"No\"}', 0, 'active', '2026-03-05 08:50:24'),
(18, 6, 'ASUS ROG Swift 4K 144Hz 27\"', 'ASUS', 'PG27UQ', 'จอ 4K 144Hz IPS HDR1000 G-Sync Ultimate', 32900.00, 6, 'default.jpg', '{\"size\":\"27 inch\",\"resolution\":\"3840x2160 (4K)\",\"panel\":\"IPS\",\"refresh_rate\":\"144Hz\",\"response_time\":\"1ms\",\"hdr\":\"HDR1000\",\"g_sync\":\"Yes\"}', 1, 'active', '2026-03-05 08:50:24'),
(19, 6, 'LG UltraGear 27\" 2K 165Hz', 'LG', '27GP850-B', 'จอเกม 2K 165Hz IPS Nano คุ้มค่า', 9900.00, 15, 'default.jpg', '{\"size\":\"27 inch\",\"resolution\":\"2560x1440 (QHD)\",\"panel\":\"IPS Nano\",\"refresh_rate\":\"165Hz\",\"response_time\":\"1ms GTG\",\"freesync\":\"Yes\",\"g_sync\":\"Compatible\"}', 1, 'active', '2026-03-05 08:50:24'),
(20, 6, 'Samsung Odyssey G7 32\" Curved', 'Samsung', 'LC32G75TQSNXZA', 'จอโค้ง 32\" 1000R 2K 240Hz VA', 18500.00, 8, 'default.jpg', '{\"size\":\"32 inch\",\"resolution\":\"2560x1440 (QHD)\",\"panel\":\"VA Curved 1000R\",\"refresh_rate\":\"240Hz\",\"response_time\":\"1ms\",\"g_sync\":\"Compatible\"}', 0, 'active', '2026-03-05 08:50:24'),
(21, 6, 'DELL S2421HS 24\" FHD 75Hz', 'DELL', 'S2421HS', 'จอ FHD 24 นิ้ว ราคาประหยัด เหมาะสำนักงาน', 4500.00, 25, 'default.jpg', '{\"size\":\"24 inch\",\"resolution\":\"1920x1080 (FHD)\",\"panel\":\"IPS\",\"refresh_rate\":\"75Hz\",\"response_time\":\"4ms\",\"freesync\":\"Yes\"}', 0, 'active', '2026-03-05 08:50:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
