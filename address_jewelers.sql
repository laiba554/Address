-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 14, 2026 at 11:33 AM
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
-- Database: `address_jewelers`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Manager') DEFAULT 'Admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`admin_id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', 'admin', 'Admin', '2025-12-29 11:20:08');

-- --------------------------------------------------------

--
-- Table structure for table `backup_logs`
--

CREATE TABLE `backup_logs` (
  `backup_id` int(11) NOT NULL,
  `backup_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `backup_by` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `backup_logs`
--

INSERT INTO `backup_logs` (`backup_id`, `backup_date`, `backup_by`, `remarks`) VALUES
(1, '2026-01-04 09:28:33', 'Admin (ID: 1)', 'Manual backup created: backup_2026-01-04_10-28-33.sql'),
(2, '2026-01-12 11:05:51', 'Admin (ID: 1)', 'Manual backup created: backup_2026-01-12_12-05-51.sql');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `customer_id`, `created_at`) VALUES
(1, 2, '2025-12-29 11:49:23'),
(2, 5, '2025-12-30 17:37:01'),
(3, 6, '2025-12-31 10:44:34'),
(4, 7, '2025-12-31 10:46:58'),
(5, 8, '2025-12-31 11:25:25'),
(16, 11, '2026-01-05 10:27:50'),
(17, 13, '2026-01-05 11:47:06'),
(18, 14, '2026-01-12 10:52:21'),
(19, 16, '2026-01-12 10:59:33');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`cart_item_id`, `cart_id`, `product_id`, `quantity`) VALUES
(19, 17, 8, 2),
(20, 18, 19, 3);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `description`, `created_at`) VALUES
(3, 'jewelry', 'Discover an elegant collection of finely crafted jewelry designed to enhance every look. From timeless classics to modern statement pieces, our jewelry combines quality craftsmanship with lasting beauty for every occasion.', '2025-12-29 11:33:00'),
(6, 'cosmetics', 'Sophisticated cosmetics crafted for flawless finish, long-lasting wear, and timeless beauty.', '2026-01-02 10:42:07'),
(8, 'yahoo', 'yahoo', '2026-01-12 11:03:40');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `email` varchar(100) NOT NULL,
  `work_phone` varchar(20) DEFAULT NULL,
  `cell_phone` varchar(20) NOT NULL,
  `date_of_birth` date NOT NULL,
  `remarks` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `name`, `address`, `email`, `work_phone`, `cell_phone`, `date_of_birth`, `remarks`, `password`, `created_at`) VALUES
(2, 'eman', '83', 'laibakhurram554@gmail.com', '0333123456', '0333456789', '2025-12-01', 'Affordable', '', '2025-12-29 11:49:12'),
(5, 'noor fatima', '86', 'noorfatima@gmail.com', '0300456781', '0300190555', '2025-12-03', 'nice', '', '2025-12-30 17:36:42'),
(6, 'laiba', '67', 'eman@gmail.com', '03312357851', '03002357850', '2025-12-17', '', '', '2025-12-31 10:32:03'),
(7, 'Rameen', '90', 'abc@gmail.com', '03310290438', '03363134807', '2025-12-17', '', '', '2025-12-31 10:46:41'),
(8, 'Anjila', '45', 'anjila2@gmail.com', '0333222666', '0300667788', '2025-12-16', '', '', '2025-12-31 11:25:19'),
(10, 'abc', 'abc', 'abc123@gmail.com', '123', '123', '2026-01-28', 'abc', '$2y$10$75.TvByV626NXe6tNornc.1BxSD9y9c.gqgCa/YA1pv.yxS0O/zaC', '2026-01-05 10:25:11'),
(11, 'aliza', 'D-84', 'aliza@gmail.com', '786900', '780858909', '2026-01-15', '', '$2y$10$evodGEK.Ag4nllylcSZ7bOA5idLv8/yQKwh4.CtZ0QDySE9yjlXfq', '2026-01-05 10:26:32'),
(13, 'Anaya', 'D84', 'anaya@gmail.com', '3756842785948', '3462385', '2026-01-30', '', '$2y$10$t7yQ3Gv.iIzXhcylu7d6VuEEadz58JUWoUiuhfmvvzwBFAE2EntK.', '2026-01-05 11:46:48'),
(14, 'fatima', 'E-34', 'fatima@gmail.com', '03246787893', '03462015915', '2026-01-14', '', '$2y$10$YBNzt5FsPj1Yd7Z.rWlnYuVmPbqXdgJZMy0SuDQmneugkvi1s2B3O', '2026-01-12 10:50:44'),
(16, 'abc123', 'abc123', 'abc123@gmail', '090', '090', '2026-01-01', 'qwe', '$2y$10$I/7qWcCMJS.gsUgxtmExRuAmvO.XsaajEEd.4AQ1hdTi/DIE63iKG', '2026-01-12 10:59:12');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('Pending','Confirmed','Delivered','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `order_date`, `total_amount`, `status`) VALUES
(1, 2, '2025-12-29 11:49:36', 10000.00, 'Cancelled'),
(2, 6, '2025-12-31 10:44:52', 30000.00, 'Cancelled'),
(3, 7, '2025-12-31 10:47:06', 10000.00, 'Delivered'),
(4, 8, '2025-12-31 11:25:51', 20000.00, 'Cancelled'),
(5, 8, '2025-12-31 11:32:04', 10000.00, 'Cancelled'),
(6, 11, '2026-01-05 10:28:03', 5000.00, 'Delivered'),
(7, 11, '2026-01-05 11:43:30', 26000.00, 'Confirmed'),
(8, 13, '2026-01-05 11:47:18', 1500.00, 'Pending'),
(9, 13, '2026-01-09 11:59:16', 8000.00, 'Confirmed'),
(10, 13, '2026-01-12 10:27:11', 8000.00, 'Delivered'),
(11, 16, '2026-01-12 11:00:53', 6000.00, 'Confirmed');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, 10000.00),
(2, 2, 1, 3, 10000.00),
(3, 3, 1, 1, 10000.00),
(4, 4, 1, 2, 10000.00),
(5, 5, 1, 1, 10000.00),
(6, 6, 14, 1, 5000.00),
(7, 7, 17, 3, 4000.00),
(8, 7, 22, 2, 7000.00),
(9, 8, 15, 1, 1500.00),
(10, 9, 17, 2, 4000.00),
(11, 10, 17, 2, 4000.00),
(12, 11, 13, 2, 3000.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `product_description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `status` enum('Available','Out of Stock') DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `product_name`, `product_description`, `price`, `stock_quantity`, `image_url`, `status`, `created_at`) VALUES
(1, 3, 'Crystal Teardrop Halo Ring', 'A graceful teardrop-cut clear crystal set as the centerpiece, surrounded by a sparkling halo that enhances its brilliance. The split silver-toned band, delicately encrusted with fine stones, adds a modern yet timeless touch.', 1000.00, 10, '1767452347_69592ebb95457.jpeg', 'Available', '2025-12-29 11:34:25'),
(2, 3, 'Sapphire Halo Solitaire Ring', 'An exquisite solitaire ring featuring a deep blue oval-cut center stone encircled by a delicate halo of sparkling clear stones.', 1200.00, 10, '1767448177_69591e71d3c47.jpeg', 'Available', '2026-01-03 13:49:37'),
(3, 3, 'Midnight Teardrop Halo Ring', 'A striking statement ring featuring a deep black teardrop-cut center stone embraced by a graceful halo of shimmering clear stones.', 1500.00, 10, '1767448807_695920e75e76a.jpeg', 'Available', '2026-01-03 14:00:07'),
(4, 3, 'Blush Halo Elegance Ring', 'A luxurious rose-gold ring featuring a soft blush pink cushion-cut gemstone.', 1600.00, 10, '1767457595_6959433ba5374.jpeg', 'Available', '2026-01-03 16:26:35'),
(5, 3, 'Golden Blush Duo Ring', 'An elegant yellow-gold ring featuring a soft pink round-cut gemstone', 2000.00, 10, '1767457936_695944903a7e5.jpeg', 'Available', '2026-01-03 16:32:16'),
(6, 3, 'Blush Infinity Luxe Ring', 'A bold yellow-gold statement ring featuring an intricate infinity-inspired woven design crowned with a blush pink gemstone at the center.', 2000.00, 10, '1767458758_695947c62605d.jpeg', 'Available', '2026-01-03 16:45:58'),
(7, 3, 'Sapphire Splendor', 'Crafted for timeless elegance and luxury, it makes a statement of refined taste and sophistication.', 1200.00, 10, '1767459020_695948cc4f5f8.jpeg', 'Available', '2026-01-03 16:50:20'),
(8, 3, 'Aurora Ring', 'The subtle wave‑like detailing gives it an ethereal, “aurora” vibe—perfect for everyday', 2200.00, 10, '1767459431_69594a67b3f2d.jpeg', 'Available', '2026-01-03 16:57:11'),
(9, 3, 'Amethyst  Diamond Necklace', 'An elegant gold-toned necklace featuring purple amethyst stones and white diamond accents in an intricate filigree design.', 2500.00, 10, '1767459609_69594b19b0315.jpeg', 'Available', '2026-01-03 17:00:09'),
(10, 3, 'Rose Blush Crystal Jewelry Set', 'A delicate rose-gold toned necklace and earring set adorned with soft pink crystals, designed to add a touch of elegance and feminine charm to any outfit.', 2700.00, 10, '1767459777_69594bc1ed482.jpeg', 'Available', '2026-01-03 17:02:57'),
(11, 3, 'Golden Aura Crystal Pendant', 'A graceful gold-toned pendant featuring a sparkling crystal, beautifully crafted to add timeless elegance and luxury to any look.', 1200.00, 10, '1767468871_69596f47713a3.jpeg', 'Available', '2026-01-03 19:34:31'),
(12, 3, 'Royal Pearl Majesty Necklace', 'An exquisite pearl and gold-toned statement necklace adorned with shimmering crystals, crafted to bring regal elegance and timeless beauty to formal occasions.', 3000.00, 10, '1767469105_69597031d375e.jpeg', 'Available', '2026-01-03 19:38:25'),
(13, 6, 'lipsticks', 'premium lipsticks with rich color, smooth finish, and lasting elegance', 3000.00, 10, '1767554475_695abdabd484e.jpeg', 'Available', '2026-01-04 19:21:15'),
(14, 6, 'Jenny Foundation', 'Natural finish with smooth coverage for all-day confidence.', 5000.00, 10, '1767554647_695abe5758fed.jpeg', 'Available', '2026-01-04 19:24:07'),
(15, 6, 'Liquid foundation', 'Achieve a fresh, even look with Jenny Store’s silky smooth foundation.', 1500.00, 10, '1767554973_695abf9de0e2f.jpeg', 'Available', '2026-01-04 19:29:33'),
(16, 3, 'Flawless Base Duo', 'A smooth, lightweight base set that delivers natural coverage and a flawless, long-lasting finish.', 6000.00, 10, '1767555397_695ac14556329.jpeg', 'Available', '2026-01-04 19:35:07'),
(17, 6, 'Radiant Face Trio', 'A versatile trio of silky powders that add a soft glow, natural blush, and warm definition for a perfectly balanced look.', 4000.00, 10, '1767555504_695ac1b065f11.jpeg', 'Available', '2026-01-04 19:38:24'),
(18, 6, 'Professional Makeup Brush Set', 'A premium set of soft, high-quality brushes designed for smooth, precise, and flawless makeup application.', 3000.00, 10, '1767555614_695ac21e1dfa5.jpeg', 'Available', '2026-01-04 19:40:14'),
(19, 6, 'Glam Essentials Set', 'A sleek makeup set featuring rich neutral eyeshadows and elegant eye products for a polished everyday look.', 2000.00, 10, '1767555710_695ac27e12ff9.jpeg', 'Available', '2026-01-04 19:41:50'),
(20, 6, 'Radiant Glow Highlighter', 'A silky, light-reflecting highlighter that delivers a smooth, luminous glow. Designed to enhance facial features with a natural look.', 2500.00, 10, '1767556129_695ac42142549.jpeg', 'Available', '2026-01-04 19:48:49'),
(21, 6, 'Rose Luxe Glam Eyeshadow Palette', 'Elevate your eye makeup with the Rose Luxe Glam Eyeshadow Palette, a stunning blend of warm nudes, rich colors', 1500.00, 10, '1767556842_695ac6ea18666.jpeg', 'Available', '2026-01-04 20:00:42'),
(22, 6, 'Luxe Spectrum Pro Eyeshadow Collection', 'Designed for versatility, these palettes let you transition effortlessly from natural daytime looks to dramatic evening glam', 7000.00, 10, '1767557157_695ac8254127e.jpeg', 'Available', '2026-01-04 20:05:57'),
(23, 6, 'Luxe Color Nail Enamel Set', 'Add a pop of elegance to your nails with the Jenny Store Luxe Color Nail Enamel Set.', 5000.00, 10, '1767557319_695ac8c71b4d4.jpeg', 'Available', '2026-01-04 20:08:39'),
(24, 6, 'Sun Soothing Sunscreen', 'A calming and moisturizing lotion formulated to soothe sun-exposed skin. Helps reduce dryness and discomfort while restoring skin softness after sun exposure.', 9000.00, 10, '1767557531_695ac99b2fa11.jpeg', 'Available', '2026-01-04 20:12:11'),
(28, 6, 'Matte lipgloss', 'This matte liquid lipstick collection features elegant shades from bold reds to soft nudes, perfect for daily wear and special occasions.\r\nIt has a smooth, lightweight formula with rich color payoff and a long-lasting matte finish.', 3000.00, 14, '1768214508_6964cfec476bc.webp', 'Available', '2026-01-12 10:41:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `backup_logs`
--
ALTER TABLE `backup_logs`
  ADD PRIMARY KEY (`backup_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `backup_logs`
--
ALTER TABLE `backup_logs`
  MODIFY `backup_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
