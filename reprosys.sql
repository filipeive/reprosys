-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 11, 2026 at 08:10 AM
-- Server version: 8.0.45-0ubuntu0.22.04.1
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `reprosys`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('fdsms-cache-admin@zalala.com|197.218.120.61', 'i:1;', 1759287413),
('fdsms-cache-admin@zalala.com|197.218.120.61:timer', 'i:1759287413;', 1759287413),
('fdsms-cache-admin2@zalala.com|41.220.201.192', 'i:1;', 1760276009),
('fdsms-cache-admin2@zalala.com|41.220.201.192:timer', 'i:1760276009;', 1760276009),
('fdsms-cache-user_permissions_3', 'a:17:{i:0;s:14:\"view_dashboard\";i:1;s:13:\"view_products\";i:2;s:13:\"edit_products\";i:3;s:12:\"adjust_stock\";i:4;s:10:\"view_sales\";i:5;s:12:\"create_sales\";i:6;s:14:\"edit_own_sales\";i:7;s:11:\"view_orders\";i:8;s:13:\"create_orders\";i:9;s:15:\"edit_own_orders\";i:10;s:10:\"view_debts\";i:11;s:12:\"create_debts\";i:12;s:15:\"manage_payments\";i:13;s:13:\"view_expenses\";i:14;s:15:\"create_expenses\";i:15;s:20:\"view_stock_movements\";i:16;s:18:\"view_basic_reports\";}', 1759290964),
('fdsms-cache-user_permissions_6', 'a:17:{i:0;s:14:\"view_dashboard\";i:1;s:13:\"view_products\";i:2;s:13:\"edit_products\";i:3;s:12:\"adjust_stock\";i:4;s:10:\"view_sales\";i:5;s:12:\"create_sales\";i:6;s:14:\"edit_own_sales\";i:7;s:11:\"view_orders\";i:8;s:13:\"create_orders\";i:9;s:15:\"edit_own_orders\";i:10;s:10:\"view_debts\";i:11;s:12:\"create_debts\";i:12;s:15:\"manage_payments\";i:13;s:13:\"view_expenses\";i:14;s:15:\"create_expenses\";i:15;s:20:\"view_stock_movements\";i:16;s:18:\"view_basic_reports\";}', 1758104627);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `type` enum('product','service') NOT NULL,
  `color` varchar(7) NOT NULL DEFAULT '#007bff',
  `icon` varchar(100) NOT NULL DEFAULT 'fas fa-box',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `type`, `color`, `icon`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Material de Escritório', 'Poupado por ..', 'product', '#007bff', 'fas fa-box', 'active', '2025-08-03 12:56:08', '2025-08-03 10:58:10'),
(2, 'Suprimentos', NULL, 'product', '#007bff', 'fas fa-box', 'active', '2025-08-03 12:56:08', '2025-08-03 12:56:08'),
(3, 'Serigrafia', NULL, 'product', '#007bff', 'fas fa-box', 'active', '2025-08-03 12:56:08', '2025-08-03 12:56:08'),
(4, 'Reprografia', 'Serviços de cópia, impressão e encadernação.', 'service', '#6f42c1', 'fas fa-copy', 'active', '2025-08-03 12:57:32', '2025-08-03 12:57:32'),
(5, 'Personalização', 'Serviços de brindes personalizados, agendas, canecas, etc.', 'service', '#20c997', 'fas fa-gift', 'active', '2025-08-03 12:57:32', '2025-08-03 12:57:32'),
(6, 'Informática', 'Materiais como pen drives, mouses, teclados.', 'product', '#ffffff', 'fas fa-print', 'active', '2025-08-03 12:57:32', '2025-08-28 11:24:22'),
(7, 'Outros Produtos', 'Itens diversos de venda geral.', 'product', '#fd7e14', 'fas fa-box-open', 'active', '2025-08-03 12:57:32', '2025-08-03 12:57:32'),
(8, 'Outros Serviços', 'Serviços variados não classificados.', 'service', '#dc3545', 'fas fa-tools', 'active', '2025-08-03 12:57:32', '2025-08-03 12:57:32'),
(10, 'Vesturario', 'Camisetas sem Estampa', 'product', '#f5c211', 'fas fa-box', 'active', '2025-08-28 11:25:17', '2025-08-28 11:25:17'),
(11, 'Design Gráfico', NULL, 'service', '#ffd22e', 'fas fa-palette', 'active', '2025-09-02 04:45:16', '2025-09-02 04:45:16');

-- --------------------------------------------------------

--
-- Table structure for table `debts`
--

CREATE TABLE `debts` (
  `id` int NOT NULL,
  `debt_type` enum('product','money') NOT NULL DEFAULT 'product',
  `user_id` int DEFAULT NULL,
  `employee_id` int DEFAULT NULL,
  `sale_id` int DEFAULT NULL,
  `generated_sale_id` int UNSIGNED DEFAULT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL DEFAULT 'N/A',
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_document` varchar(20) DEFAULT NULL,
  `employee_name` varchar(255) DEFAULT NULL,
  `employee_phone` varchar(255) DEFAULT NULL,
  `employee_document` varchar(255) DEFAULT NULL,
  `original_amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) DEFAULT '0.00',
  `remaining_amount` decimal(10,2) NOT NULL,
  `debt_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('active','partial','paid','overdue','cancelled') DEFAULT 'active',
  `description` text,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `debt_items`
--

CREATE TABLE `debt_items` (
  `id` bigint UNSIGNED NOT NULL,
  `debt_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `debt_payments`
--

CREATE TABLE `debt_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `debt_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card','transfer','emola','mpesa') DEFAULT 'cash',
  `payment_date` date NOT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `expense_date` date NOT NULL,
  `receipt_number` varchar(50) DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expense_category_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `user_id`, `description`, `amount`, `expense_date`, `receipt_number`, `notes`, `created_at`, `updated_at`, `expense_category_id`) VALUES
(1, 2, 'Compra de Papeis A4', '100.00', '2025-06-23', NULL, NULL, '2025-09-10 05:16:38', '2025-09-10 05:16:38', 1),
(2, 2, 'Compra de Credelec', '200.00', '2025-06-27', NULL, NULL, '2025-09-10 05:19:58', '2025-09-10 05:19:58', 6),
(3, 2, 'Pagamento de Taxi', '20.00', '2025-06-23', NULL, NULL, '2025-09-10 05:20:41', '2025-09-10 05:20:41', 5),
(4, 2, 'Compra de Lanche para os Funcionarios', '20.00', '2025-06-28', NULL, NULL, '2025-09-10 05:31:38', '2025-09-10 05:31:38', 11),
(5, 2, 'Pagamento de Taxi(Vitcha ou Wilson)', '50.00', '2025-06-30', NULL, NULL, '2025-09-11 21:49:58', '2025-09-11 21:49:58', 5),
(6, 2, 'Lanche (Victor ou Wilson)', '50.00', '2025-06-30', NULL, NULL, '2025-09-11 21:51:00', '2025-09-11 21:51:00', 11),
(7, 2, 'Emprestimo Vitcha', '10.00', '2025-06-30', NULL, NULL, '2025-09-11 21:52:24', '2025-09-11 21:52:24', 13),
(8, 2, 'Compra de Vassoura', '120.00', '2025-07-01', NULL, NULL, '2025-09-11 21:53:27', '2025-09-11 21:53:27', 12),
(9, 2, 'Pagamento de Taxi no dia de Compra da Vassoura', '15.00', '2025-07-01', NULL, NULL, '2025-09-11 21:54:17', '2025-09-11 21:54:17', 5),
(10, 2, 'Lanche e Taxi', '50.00', '2025-07-02', NULL, NULL, '2025-09-11 21:55:28', '2025-09-11 21:55:28', 5),
(11, 2, 'Lanche e Taxi', '60.00', '2025-07-04', NULL, NULL, '2025-09-11 21:56:08', '2025-09-11 21:56:08', 11),
(12, 2, 'Pagamento diario de taxa de municipio', '20.00', '2025-07-07', NULL, NULL, '2025-09-11 21:57:15', '2025-09-11 21:57:15', 14),
(13, 2, 'Compra de Credilec', '200.00', '2025-07-05', NULL, NULL, '2025-09-11 21:58:22', '2025-09-11 21:58:22', 6),
(14, 2, 'Pagamento diario de taxa de municipio', '20.00', '2025-07-07', NULL, NULL, '2025-09-11 22:01:43', '2025-09-11 22:01:43', 14),
(15, 2, 'Compra de Credilec', '200.00', '2025-08-07', NULL, NULL, '2025-09-11 22:02:08', '2025-09-11 22:02:08', 6),
(16, 2, 'Pagamento de Taxi', '30.00', '2025-08-07', NULL, NULL, '2025-09-11 22:02:47', '2025-09-11 22:02:47', 5),
(17, 2, 'Compra de Um Bloco de Resma', '300.00', '2025-07-08', NULL, NULL, '2025-09-11 22:03:56', '2025-09-11 22:03:56', 1),
(18, 2, 'Pagamento diario de taxa de municipio', '20.00', '2025-07-08', NULL, NULL, '2025-09-11 22:15:19', '2025-09-11 22:15:19', 14),
(19, 2, 'Lanche e Taxi', '45.00', '2025-07-08', NULL, NULL, '2025-09-11 22:16:07', '2025-09-11 22:16:07', 11),
(20, 2, 'Salario de Vitcha', '1000.00', '2025-07-09', NULL, NULL, '2025-09-11 22:16:54', '2025-09-11 22:16:54', 8),
(21, 2, 'Lanche (Victor ou Wilson)', '70.00', '2025-09-07', NULL, NULL, '2025-09-11 22:17:34', '2025-09-11 22:17:34', 11),
(22, 2, 'Lanche (Victor ou Wilson)', '130.00', '2025-07-10', NULL, NULL, '2025-09-11 22:18:20', '2025-09-11 22:18:20', 11),
(23, 2, 'Internet do Escritorio', '10.00', '2025-07-10', NULL, NULL, '2025-09-11 22:18:48', '2025-09-11 22:18:48', 7),
(24, 2, 'Lanche e Mascara', '30.00', '2252-07-11', NULL, NULL, '2025-09-11 22:19:29', '2025-09-11 22:19:29', 11),
(25, 2, 'Pagamento diario de taxa de municipio', '20.00', '0252-07-14', NULL, NULL, '2025-09-11 22:20:09', '2025-09-11 22:20:21', 14),
(26, 2, 'Lanche (Victor ou Wilson)', '70.00', '2025-07-15', NULL, NULL, '2025-09-11 22:21:07', '2025-09-11 22:21:07', 11),
(27, 2, 'Pagamento diario de taxa de municipio', '20.00', '2025-07-15', NULL, NULL, '2025-09-11 22:22:32', '2025-09-11 22:22:32', 14),
(28, 2, 'Compra de Camisetas', '850.00', '2025-07-08', NULL, NULL, '2025-09-11 22:24:01', '2025-09-11 22:24:01', 15),
(29, 2, 'Lampada', '120.00', '2025-07-15', NULL, NULL, '2025-09-11 22:26:56', '2025-09-11 22:26:56', 2),
(30, 2, 'Pagamento diario de taxa de municipio', '20.00', '2025-07-16', NULL, NULL, '2025-09-11 22:27:35', '2025-09-11 22:27:35', 14),
(31, 2, 'Lanche (Victor ou Wilson)', '30.00', '2025-07-17', NULL, NULL, '2025-09-11 22:28:29', '2025-09-11 22:28:29', 11),
(32, 2, 'Lanche (Victor ou Wilson)', '25.00', '2025-07-16', NULL, NULL, '2025-09-11 22:28:52', '2025-09-11 22:28:52', 11),
(33, 2, 'Compra de Papeis A4', '300.00', '2025-07-18', NULL, NULL, '2025-09-11 22:29:28', '2025-09-11 22:29:28', 1),
(34, 2, 'Lanche (Victor ou Wilson)', '20.00', '2025-07-18', NULL, NULL, '2025-09-11 22:29:52', '2025-09-11 22:29:52', 11),
(35, 2, 'Pagamento de Taxi', '20.00', '2025-07-18', NULL, NULL, '2025-09-11 22:30:15', '2025-09-11 22:30:15', 5),
(36, 2, 'Pagamento diario de taxa de municipio', '20.00', '2025-07-19', NULL, NULL, '2025-09-11 22:30:42', '2025-09-11 22:30:57', 14),
(37, 2, 'Lanche (Victor ou Wilson)', '20.00', '2025-07-19', NULL, NULL, '2025-09-11 22:31:18', '2025-09-11 22:31:18', 11),
(38, 2, 'Vinil 1 Metro', '600.00', '2025-07-21', NULL, NULL, '2025-09-11 22:32:18', '2025-09-11 22:32:18', 10),
(39, 2, 'Lanche (Victor ou Wilson)', '30.00', '2025-07-21', NULL, NULL, '2025-09-11 22:32:37', '2025-09-11 22:32:37', 11),
(40, 2, 'Internet do Escritorio', '50.00', '2025-07-21', NULL, NULL, '2025-09-11 22:33:16', '2025-09-11 22:33:16', 7),
(41, 2, 'Pagamento diario de taxa de municipio', '20.00', '2025-07-21', NULL, NULL, '2025-09-11 22:33:41', '2025-09-11 22:33:41', 14),
(42, 2, 'Internet do Escritorio', '50.00', '2025-07-17', NULL, NULL, '2025-09-11 22:36:45', '2025-09-11 22:36:45', 7),
(43, 2, 'Compra de Papeis A4', '354.00', '2025-07-21', NULL, NULL, '2025-09-11 22:37:26', '2025-09-11 22:37:26', 1),
(44, 2, 'Compra de Camisetas', '800.00', '2025-07-23', NULL, NULL, '2025-09-11 22:38:13', '2025-09-15 11:10:54', 15),
(45, 2, 'Salario de Vitcha', '1000.00', '2025-07-23', NULL, NULL, '2025-09-11 22:39:50', '2025-09-11 22:39:50', 8),
(46, 2, 'Compra de Camisetas', '1700.00', '2025-07-24', NULL, NULL, '2025-09-15 11:16:55', '2025-09-15 11:16:55', 15),
(47, 2, 'Lanche', '25.00', '2025-07-24', NULL, NULL, '2025-09-15 11:17:26', '2025-09-15 11:17:26', 11),
(48, 2, 'Internet do Escritorio', '10.00', '2025-07-24', NULL, NULL, '2025-09-15 11:17:59', '2025-09-15 11:17:59', 7),
(49, 2, 'Lanche (Victor ou Wilson)', '30.00', '2025-07-25', NULL, NULL, '2025-09-15 11:21:00', '2025-09-15 11:21:00', 11),
(50, 2, 'Internet do Escritorio', '30.00', '2015-07-25', NULL, NULL, '2025-09-15 11:22:07', '2025-09-15 11:22:07', 7),
(51, 2, 'Compra de Combustivel', '100.00', '2025-07-25', NULL, NULL, '2025-09-15 11:22:57', '2025-09-15 11:22:57', 5),
(52, 2, 'Compra de Credilec', '200.00', '2025-07-26', NULL, NULL, '2025-09-15 11:25:34', '2025-09-15 11:25:34', 6),
(53, 2, 'Lanche (Victor ou Wilson)', '60.00', '2025-07-25', NULL, NULL, '2025-09-15 11:26:13', '2025-09-15 11:26:13', 11),
(54, 2, 'Compra de Combustivel mota de Wilson', '50.00', '2025-07-26', NULL, NULL, '2025-09-15 11:27:14', '2025-09-15 11:27:14', 5),
(55, 2, 'Fiscal', '20.00', '2025-08-01', NULL, NULL, '2025-09-15 12:13:12', '2025-09-15 12:13:12', 9),
(56, 2, 'Lanche (Victor ou Wilson)', '20.00', '2025-08-02', NULL, NULL, '2025-09-15 12:14:45', '2025-09-15 12:14:45', 11),
(57, 2, 'Net', '10.00', '2025-08-01', NULL, NULL, '2025-09-15 12:15:26', '2025-09-15 12:15:26', 7),
(58, 2, 'Combustivel', '50.00', '2025-08-02', NULL, NULL, '2025-09-15 12:17:32', '2025-09-15 12:17:32', 5),
(59, 2, 'Fiscal', '20.00', '2025-08-02', NULL, NULL, '2025-09-15 12:18:20', '2025-09-15 13:24:35', 13),
(60, 2, 'Lanche (Victor ou Wilson)', '20.00', '2025-08-02', NULL, NULL, '2025-09-15 12:20:47', '2025-09-15 12:20:47', 11),
(61, 2, 'Para Mano Flavio', '1700.00', '2025-08-04', NULL, NULL, '2025-09-15 12:54:28', '2025-09-15 13:07:57', 9),
(62, 2, 'MB', '10.00', '2025-08-04', NULL, NULL, '2025-09-15 13:04:20', '2025-09-15 13:04:20', 7),
(63, 2, 'Pagamento de Taxi', '30.00', '2025-08-04', NULL, NULL, '2025-09-15 13:05:33', '2025-09-15 13:05:33', 5),
(64, 2, 'Lanche (Victor ou Wilson)', '30.00', '2025-08-04', NULL, NULL, '2025-09-15 13:07:15', '2025-09-15 13:07:15', 11),
(65, 2, 'Fiscal', '20.00', '2025-08-04', NULL, NULL, '2025-09-15 13:12:16', '2025-09-15 13:12:16', 14),
(66, 2, 'Compra de Credelec', '200.00', '2025-08-04', NULL, NULL, '2025-09-15 13:13:16', '2025-09-15 13:13:16', 6),
(67, 2, 'MB', '20.00', '2025-08-05', NULL, NULL, '2025-09-15 13:35:40', '2025-09-15 13:35:40', 7),
(68, 2, 'Lanche (Victor ou Wilson)', '30.00', '2025-08-05', NULL, NULL, '2025-09-15 13:36:31', '2025-09-15 13:36:31', 11),
(69, 2, 'Fiscal', '20.00', '2025-08-05', NULL, NULL, '2025-09-15 13:37:14', '2025-09-15 13:37:14', 14),
(70, 2, 'dr. Latibo', '1500.00', '2025-08-05', NULL, NULL, '2025-09-15 13:38:59', '2025-09-15 13:38:59', 16),
(71, 2, 'Fiscal', '20.00', '2025-08-06', NULL, NULL, '2025-09-15 13:39:31', '2025-09-15 13:39:31', 14),
(72, 2, 'Lanche (Victor ou Wilson)', '40.00', '2025-08-06', NULL, NULL, '2025-09-15 13:40:14', '2025-09-15 13:40:14', 11),
(73, 2, 'Lanche (Victor ou Wilson)', '20.00', '2025-08-07', NULL, NULL, '2025-09-15 13:53:01', '2025-09-15 13:53:01', 11),
(74, 2, 'Extensao', '250.00', '2025-08-08', NULL, NULL, '2025-09-15 13:54:15', '2025-09-15 13:54:15', 9),
(75, 2, 'Lanche (Victor ou Wilson)', '10.00', '2025-08-08', NULL, NULL, '2025-09-15 13:54:58', '2025-09-15 13:54:58', 11),
(76, 2, 'Fiscal', '20.00', '2025-08-08', NULL, NULL, '2025-09-15 13:56:20', '2025-09-15 13:56:20', 14),
(77, 2, 'Compra de Camiseta de algodao 2XL', '300.00', '2025-08-09', NULL, NULL, '2025-09-15 14:02:56', '2025-09-15 14:02:56', 10),
(78, 2, 'Compra de Venil', '300.00', '2025-08-09', NULL, NULL, '2025-09-15 14:03:52', '2025-09-15 14:03:52', 10),
(79, 2, 'Compra de Credelec', '50.00', '2025-08-11', NULL, NULL, '2025-09-15 14:04:37', '2025-09-15 14:04:37', 6),
(80, 2, 'Lanche (Victor ou Wilson)', '15.00', '2025-08-11', NULL, NULL, '2025-09-15 14:05:08', '2025-09-15 14:05:08', 11),
(81, 1, 'Wilson ou Victor', '20.00', '2025-08-12', NULL, NULL, '2025-09-17 06:07:18', '2025-09-17 06:07:18', 11),
(82, 1, 'MB', '10.00', '2025-08-12', NULL, NULL, '2025-09-17 06:08:08', '2025-09-17 06:08:08', 7),
(83, 1, 'credelec', '200.00', '2025-08-12', NULL, NULL, '2025-09-17 06:08:46', '2025-09-17 06:08:46', 6),
(84, 1, 'Cola', '100.00', '2025-08-12', NULL, NULL, '2025-09-17 06:10:01', '2025-09-17 06:10:01', 1),
(85, 1, 'Txova', '240.00', '2025-08-12', NULL, NULL, '2025-09-17 06:10:44', '2025-09-17 06:10:44', 5),
(86, 6, 'MB', '100.00', '2025-08-15', NULL, NULL, '2025-09-17 06:19:30', '2025-09-17 06:19:30', 7),
(87, 6, 'Wilson ou Victor', '30.00', '2025-08-15', NULL, NULL, '2025-09-17 06:20:23', '2025-09-17 06:20:23', 11),
(88, 6, 'Divida Latibo', '500.00', '2025-08-15', NULL, NULL, '2025-09-17 06:22:47', '2025-09-17 06:22:47', 9),
(89, 6, 'Chapeus', '450.00', '2025-08-15', NULL, NULL, '2025-09-17 06:26:09', '2025-09-17 06:26:09', 10),
(90, 6, '800', '800.00', '2025-08-15', NULL, NULL, '2025-09-17 06:27:09', '2025-09-17 06:27:09', 15),
(91, 6, 'lanche', '10.00', '2025-08-14', NULL, NULL, '2025-09-17 06:32:40', '2025-09-17 06:32:40', 11),
(92, 6, 'Fiscal', '20.00', '2025-08-14', NULL, NULL, '2025-09-17 06:33:52', '2025-09-17 06:33:52', 14),
(93, 6, '1000 para mano Carlos', '1000.00', '2025-08-16', NULL, NULL, '2025-09-17 06:39:49', '2025-09-17 06:39:49', 9),
(94, 6, '3 Camisetas para mana Piece', '600.00', '2025-08-16', NULL, NULL, '2025-09-17 06:40:59', '2025-09-17 06:40:59', 15),
(95, 6, 'lanche', '80.00', '2025-08-16', NULL, NULL, '2025-09-17 06:41:37', '2025-09-17 06:41:37', 11),
(96, 6, 'Taxi', '30.00', '2025-08-16', NULL, NULL, '2025-09-17 06:42:14', '2025-09-17 06:42:14', 5),
(97, 6, 'MB', '15.00', '2025-08-16', NULL, NULL, '2025-09-17 06:42:38', '2025-09-17 06:42:38', 7),
(98, 6, 'MB', '20.00', '2025-08-18', NULL, NULL, '2025-09-17 07:09:30', '2025-09-17 07:09:30', 7),
(99, 6, 'camiseta', '1060.00', '2025-08-18', NULL, NULL, '2025-09-17 07:10:47', '2025-09-17 07:10:47', 15),
(100, 6, 'combustivel', '50.00', '2025-08-18', NULL, NULL, '2025-09-17 07:11:48', '2025-09-17 07:11:48', 5),
(101, 6, 'Wilson ou Victor', '35.00', '2025-08-18', NULL, NULL, '2025-09-17 07:12:44', '2025-09-17 07:12:44', 11),
(102, 6, 'Taxi', '40.00', '2025-08-18', NULL, NULL, '2025-09-17 07:13:35', '2025-09-17 07:13:35', 5),
(103, 6, 'credelec', '100.00', '2025-08-18', NULL, NULL, '2025-09-17 07:14:14', '2025-09-17 07:14:14', 6),
(104, 6, 'pedal', '50.00', '2025-08-18', NULL, NULL, '2025-09-17 07:14:43', '2025-09-17 07:14:43', 5),
(105, 6, 'chamussas', '50.00', '2025-08-18', NULL, NULL, '2025-09-17 07:15:16', '2025-09-17 07:15:16', 11),
(106, 6, 'Taxi', '40.00', '2025-08-18', NULL, NULL, '2025-09-17 07:16:24', '2025-09-17 07:16:24', 5),
(107, 6, 'Venil', '600.00', '2025-08-19', NULL, NULL, '2025-09-17 07:17:39', '2025-09-17 07:17:39', 10),
(108, 6, 'MB', '20.00', '2025-08-19', NULL, NULL, '2025-09-17 07:18:11', '2025-09-17 07:18:11', 7),
(109, 6, 'Fiscal', '20.00', '2025-08-19', NULL, NULL, '2025-09-17 07:18:45', '2025-09-17 07:18:45', 14),
(110, 6, 'Wilson ou Victor', '20.00', '2025-08-19', NULL, NULL, '2025-09-17 07:19:17', '2025-09-17 07:19:17', 11),
(111, 6, 'Jaime', '500.00', '2025-08-20', NULL, NULL, '2025-09-17 07:29:17', '2025-09-17 07:29:17', 13),
(112, 6, 'Compra de Venil 2 metros', '1600.00', '2025-08-20', NULL, NULL, '2025-09-17 07:31:59', '2025-09-17 07:31:59', 10),
(113, 6, 'Fiscal', '20.00', '2025-08-20', NULL, NULL, '2025-09-17 07:32:46', '2025-09-17 07:32:46', 14),
(114, 6, 'MB', '30.00', '2025-08-20', NULL, NULL, '2025-09-17 07:33:15', '2025-09-17 07:33:15', 7),
(115, 6, 'compra de 5 camisetas', '1160.00', '2025-08-20', NULL, NULL, '2025-09-17 07:35:09', '2025-09-17 07:35:09', 15),
(116, 6, 'Sandes e Refrigerantes', '340.00', '2025-08-20', NULL, NULL, '2025-09-17 07:36:27', '2025-09-17 07:36:27', 11),
(117, 6, 'Compra de cola', '50.00', '2025-08-20', NULL, NULL, '2025-09-17 07:37:00', '2025-09-17 07:37:00', 1),
(118, 6, 'Compra de venil', '425.00', '2025-08-21', NULL, NULL, '2025-09-17 07:39:00', '2025-09-17 07:39:00', 10),
(119, 6, 'compra de 5 camisetas', '1190.00', '2025-08-21', NULL, NULL, '2025-09-17 07:40:04', '2025-09-17 07:40:04', 15),
(120, 6, 'Fiscal', '20.00', '2025-08-22', NULL, NULL, '2025-09-17 07:48:38', '2025-09-17 07:48:38', 14),
(121, 6, 'lanche', '20.00', '2025-08-22', NULL, NULL, '2025-09-17 07:49:05', '2025-09-17 07:49:05', 11),
(122, 6, 'Mano Filipe', '300.00', '2025-08-22', NULL, NULL, '2025-09-17 07:49:38', '2025-09-17 07:49:38', 13),
(123, 6, 'GGF', '10.00', '2025-08-22', NULL, NULL, '2025-09-17 07:50:01', '2025-09-17 07:50:01', 9),
(124, 6, '3100 para mano Filipe', '3100.00', '2025-08-23', NULL, NULL, '2025-09-17 07:51:16', '2025-09-17 07:51:16', 13),
(125, 6, '200 para mano Filipe', '200.00', '2025-08-23', NULL, NULL, '2025-09-17 07:51:51', '2025-09-17 07:51:51', 13),
(126, 6, 'Fiscal', '20.00', '2025-08-25', NULL, NULL, '2025-09-17 07:53:00', '2025-09-17 07:53:00', 14),
(127, 6, 'lanche', '10.00', '2025-08-25', NULL, NULL, '2025-09-17 07:53:33', '2025-09-17 07:53:33', 11),
(128, 6, 'Pagamento de encomenda', '350.00', '2025-08-25', NULL, NULL, '2025-09-17 07:54:11', '2025-09-17 07:54:11', 9),
(129, 6, 'Wilson', '1000.00', '2025-08-25', NULL, NULL, '2025-09-17 07:55:25', '2025-09-17 07:55:25', 8),
(130, 6, 'Jaime, porque pagou a divida de 500', '500.00', '2025-08-25', NULL, NULL, '2025-09-17 07:57:21', '2025-09-17 07:57:21', 8),
(131, 6, 'Mana Ivete', '500.00', '2025-08-25', NULL, NULL, '2025-09-17 07:57:58', '2025-09-17 07:57:58', 9),
(132, 6, 'Credelec 200', '200.00', '2025-08-25', NULL, NULL, '2025-09-17 07:58:34', '2025-09-17 07:58:34', 6),
(133, 6, 'Compra de 2 camisetas para mano Latibo', '400.00', '2025-08-26', NULL, NULL, '2025-09-17 08:57:54', '2025-09-17 08:57:54', 15),
(134, 6, 'Compra de acucar 80', '80.00', '2025-08-26', NULL, NULL, '2025-09-17 08:58:38', '2025-09-17 08:58:38', 11),
(135, 6, 'lanche', '60.00', '2025-08-26', NULL, NULL, '2025-09-17 08:59:34', '2025-09-17 08:59:34', 11),
(136, 6, 'Fiscal', '20.00', '2025-08-26', NULL, NULL, '2025-09-17 09:00:06', '2025-09-17 09:00:06', 14),
(137, 6, 'GGF', '10.00', '2025-08-26', NULL, NULL, '2025-09-17 09:00:58', '2025-09-17 09:00:58', 9),
(138, 6, 'Flavio', '500.00', '2025-08-27', NULL, NULL, '2025-09-17 09:03:38', '2025-09-17 09:03:38', 4),
(139, 6, 'Jaime', '25.00', '2025-08-27', NULL, NULL, '2025-09-17 09:04:48', '2025-09-17 09:04:48', 13),
(140, 6, 'divida', '240.00', '2025-08-27', NULL, NULL, '2025-09-17 09:05:49', '2025-09-17 09:05:49', 13),
(141, 6, 'credelec', '200.00', '2025-08-29', NULL, NULL, '2025-09-17 09:24:33', '2025-09-17 09:24:33', 6),
(142, 6, 'GGF', '10.00', '2025-08-29', NULL, NULL, '2025-09-17 09:25:13', '2025-09-17 09:25:13', 9),
(143, 6, 'para bancos', '400.00', '2025-08-29', NULL, NULL, '2025-09-17 09:25:51', '2025-09-17 09:25:51', 9);

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `expense_categories`
--

INSERT INTO `expense_categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Papelaria', '2025-08-03 14:36:26', '2025-08-03 14:36:26'),
(2, 'Material de Escritório', '2025-08-03 14:36:26', '2025-08-03 14:36:26'),
(3, 'Serviços de Impressão', '2025-08-03 14:36:26', '2025-08-03 14:36:26'),
(4, 'Manutenção de Equipamentos', '2025-08-03 14:36:26', '2025-08-03 14:36:26'),
(5, 'Transporte', '2025-08-03 14:36:26', '2025-08-03 14:36:26'),
(6, 'Luz e Água', '2025-08-03 14:36:26', '2025-08-03 14:36:26'),
(7, 'Internet', '2025-08-03 14:36:26', '2025-08-03 14:36:26'),
(8, 'Salários', '2025-08-03 14:36:26', '2025-08-03 14:36:26'),
(9, 'Outros', '2025-08-03 14:36:26', '2025-08-03 14:36:26'),
(10, 'Serigrafia', '2025-08-03 14:59:14', '2025-08-03 14:59:14'),
(11, 'Alimentação', '2025-09-10 05:29:46', '2025-09-10 05:29:46'),
(12, 'Material de Limpeza', '2025-09-10 05:34:37', '2025-09-10 05:34:37'),
(13, 'Emprestimo - (Funcionario)', '2025-09-11 21:51:52', '2025-09-11 21:51:52'),
(14, 'Imposto Municipal', '2025-09-11 21:56:42', '2025-09-11 21:56:42'),
(15, 'Camisetes', '2025-09-11 22:23:09', '2025-09-11 22:23:09'),
(16, 'Pagamento de renda', '2025-09-15 13:38:20', '2025-09-15 13:38:20');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) DEFAULT 'info',
  `icon` varchar(100) DEFAULT NULL,
  `read` tinyint(1) DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `action_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `description` text NOT NULL,
  `estimated_amount` decimal(10,2) DEFAULT '0.00',
  `advance_payment` decimal(10,2) DEFAULT '0.00',
  `delivery_date` datetime DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `status` enum('pending','in_progress','completed','delivered','cancelled') DEFAULT 'pending',
  `payment_status` enum('pending','partial','paid') DEFAULT 'pending',
  `notes` text,
  `internal_notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `customer_name`, `customer_phone`, `customer_email`, `description`, `estimated_amount`, `advance_payment`, `delivery_date`, `priority`, `status`, `payment_status`, `notes`, `internal_notes`, `created_at`, `updated_at`) VALUES
(2, 2, 'FIlipe Domingos dos Santos', NULL, NULL, 'Pedido: Canetas Claro (1x)', '15.00', '0.00', '2025-09-17 00:00:00', 'medium', 'cancelled', 'pending', NULL, NULL, '2025-09-10 02:17:19', '2025-09-10 02:19:41'),
(3, 2, 'FIlipe Domingos dos Santos', NULL, NULL, 'Pedido: Borachas (1x)', '10.00', '0.00', '2025-09-17 00:00:00', 'medium', 'cancelled', 'pending', NULL, NULL, '2025-09-10 11:18:05', '2025-09-10 11:18:10'),
(4, 3, 'Filipe Domingos Dos Santos', '862134230', NULL, 'Pedido: Topper (1x)', '80.00', '0.00', '2025-10-23 00:00:00', 'medium', 'pending', 'pending', NULL, NULL, '2025-10-01 02:57:36', '2025-10-22 07:51:22'),
(5, 2, 'Madrinha', NULL, NULL, 'Camisetas de Marcha', '2250.00', '0.00', NULL, 'high', 'pending', 'pending', NULL, NULL, '2025-10-12 13:34:49', '2025-10-12 13:34:49');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `product_id` int DEFAULT NULL,
  `item_name` varchar(150) NOT NULL,
  `description` text,
  `quantity` int DEFAULT '1',
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `item_name`, `description`, `quantity`, `unit_price`, `total_price`, `created_at`, `updated_at`) VALUES
(2, 2, 9, 'Canetas Claro', 'Produto: Canetas Claro', 1, '15.00', '15.00', '2025-09-10 02:17:19', '2025-09-10 02:17:19'),
(3, 3, 13, 'Borachas', 'Produto: Borachas', 1, '10.00', '10.00', '2025-09-10 11:18:05', '2025-09-10 11:18:05'),
(4, 4, 26, 'Topper', 'Produto: Topper', 1, '80.00', '80.00', '2025-10-01 02:57:36', '2025-10-01 02:57:36'),
(5, 5, 35, 'Camisetes Estampadas Algodão', '', 5, '450.00', '2250.00', '2025-10-12 13:34:49', '2025-10-12 13:34:49');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `description` text,
  `type` enum('product','service') NOT NULL,
  `purchase_price` decimal(10,2) DEFAULT '0.00',
  `selling_price` decimal(10,2) NOT NULL,
  `stock_quantity` int DEFAULT '0',
  `min_stock_level` int DEFAULT '5',
  `unit` varchar(50) DEFAULT 'unit',
  `is_active` tinyint(1) DEFAULT '1',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `original_name`, `description`, `type`, `purchase_price`, `selling_price`, `stock_quantity`, `min_stock_level`, `unit`, `is_active`, `is_deleted`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Copia/Impressão', NULL, NULL, 'product', '1.50', '2.50', 500, 5, 'unit', 1, 0, '2025-07-11 09:41:18', '2025-09-08 12:49:01', '2025-07-14 06:39:35'),
(3, 2, 'Estampagem de Camisetas', NULL, NULL, 'service', NULL, '150.00', 0, 5, 'unit', 1, 0, '2025-07-11 10:11:46', '2025-07-14 06:43:04', NULL),
(4, 2, 'Foto Tipo Pass Normal', NULL, NULL, 'service', NULL, '75.00', 0, 5, 'unit', 1, 0, '2025-07-13 13:25:46', '2025-07-13 13:25:46', NULL),
(5, 2, 'Foto Tipo Pass Urgente', NULL, NULL, 'service', NULL, '100.00', 0, 5, 'unit', 1, 0, '2025-07-13 13:26:10', '2025-07-13 13:26:10', NULL),
(7, 1, 'Envelope A4', NULL, 'Envelops de Papel A4', 'product', '6.00', '20.00', 32, 5, NULL, 1, 0, '2025-07-14 05:43:56', '2025-09-17 09:23:47', NULL),
(8, 1, 'Canetas Alright', NULL, 'Canetas', 'product', '5.00', '15.00', 25, 5, NULL, 1, 0, '2025-07-14 05:46:50', '2025-09-17 09:23:47', NULL),
(9, 1, 'Canetas Claro', NULL, NULL, 'product', '5.00', '15.00', 41, 5, NULL, 1, 0, '2025-07-14 05:48:32', '2025-09-17 09:23:47', NULL),
(10, 1, 'Pastas Plásticas', NULL, 'Pastas Plasticas', 'product', NULL, '15.00', 356, 50, NULL, 1, 0, '2025-07-14 06:19:32', '2025-09-17 08:56:30', NULL),
(11, 1, 'Lápis', NULL, 'Lápis a Carvão', 'product', '3.00', '10.00', 50, 5, NULL, 1, 0, '2025-07-14 06:22:55', '2025-09-17 09:23:47', NULL),
(12, 1, 'Marcador', NULL, 'Permanente', 'product', '20.00', '50.00', 9, 5, NULL, 1, 0, '2025-07-14 06:25:27', '2025-09-17 06:31:24', NULL),
(13, 1, 'Borachas', NULL, NULL, 'product', '5.00', '10.00', 26, 5, NULL, 1, 0, '2025-07-14 06:34:03', '2025-09-17 08:56:30', NULL),
(14, 1, 'Papel A4', NULL, NULL, 'product', '0.60', '1.00', 428, 100, NULL, 1, 0, '2025-07-14 06:36:57', '2025-09-17 09:23:47', NULL),
(15, 4, 'Cópia/Impressão', NULL, NULL, 'product', '1.00', '2.50', 1756, 10, 'unit', 1, 0, '2025-07-14 06:39:06', '2025-09-17 09:23:47', NULL),
(16, 3, 'Digitalização', NULL, 'Por página', 'service', NULL, '15.00', 0, 0, 'unit', 1, 0, '2025-07-14 06:47:39', '2025-07-14 06:47:39', NULL),
(17, 3, 'Encadernação', NULL, NULL, 'service', NULL, '30.00', 0, 0, 'unit', 1, 0, '2025-07-14 06:50:08', '2025-07-14 06:50:08', NULL),
(18, 3, 'Scanner', NULL, NULL, 'service', NULL, '15.00', 0, 0, 'unit', 1, 0, '2025-07-14 06:50:42', '2025-07-14 06:50:42', NULL),
(19, 3, 'Plastificação A4', NULL, NULL, 'service', NULL, '50.00', 0, 0, 'unit', 1, 0, '2025-07-14 06:52:03', '2025-07-14 06:52:03', NULL),
(20, 3, 'Camisetes estampadas Polo Guqui', NULL, NULL, 'product', '200.00', '400.00', 189, 5, NULL, 1, 0, '2025-07-14 08:40:25', '2025-09-17 08:56:30', NULL),
(23, 1, 'Envelope Preço Antigo', NULL, NULL, 'product', NULL, '10.00', 0, 2, NULL, 0, 1, '2025-07-15 15:28:42', '2025-09-08 09:15:52', '2025-09-08 09:15:52'),
(24, 2, 'Estampagem Gurue', NULL, NULL, 'service', '200.00', '300.00', 0, 0, 'unit', 0, 1, '2025-07-15 15:57:50', '2025-09-12 03:13:00', '2025-09-12 03:13:00'),
(25, 2, 'Outros', NULL, NULL, 'service', '0.00', '1.00', 0, 0, 'unit', 1, 0, '2025-07-15 16:07:02', '2025-09-12 03:31:08', NULL),
(26, 2, 'Topper', NULL, NULL, 'service', NULL, '100.00', 19, 5, NULL, 1, 0, '2025-07-21 16:12:16', '2025-09-08 13:11:00', NULL),
(27, 3, 'Estampagem com Desconto', NULL, 'Para pessoas que querem mais de 1 camiseta', 'service', NULL, '100.00', 0, 0, 'unit', 1, 0, '2025-08-04 06:51:12', '2025-08-04 06:51:12', NULL),
(28, 3, 'Impressao de Banner', NULL, NULL, 'service', '500.00', '700.00', 0, 0, 'unit', 1, 0, '2025-08-04 07:12:08', '2025-08-04 07:12:08', NULL),
(34, 11, 'Design de Flyers', NULL, NULL, 'service', NULL, '450.00', 0, 0, NULL, 1, 0, '2025-09-02 04:46:38', '2025-09-02 04:46:38', NULL),
(35, 3, 'Camisetes Estampadas Algodão', NULL, NULL, 'product', '200.00', '450.00', 195, 5, 'unid', 1, 0, '2025-09-08 11:17:44', '2025-09-17 09:15:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Administrador do sistema, acesso total', '2025-08-24 16:53:56', '2025-08-24 16:53:56'),
(2, 'staff', 'Funcionário comum, acesso limitado', '2025-08-24 16:53:56', '2025-08-24 16:53:56'),
(3, 'manager', 'Gestor da Loja', '2025-09-09 08:25:08', '2025-09-09 08:25:08');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_percentage` decimal(5,2) DEFAULT NULL,
  `discount_type` varchar(20) DEFAULT NULL,
  `discount_reason` text,
  `payment_method` enum('cash','card','transfer','credit','mixed') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'cash',
  `notes` text,
  `sale_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `user_id`, `customer_name`, `customer_phone`, `total_amount`, `subtotal`, `discount_amount`, `discount_percentage`, `discount_type`, `discount_reason`, `payment_method`, `notes`, `sale_date`, `created_at`, `updated_at`) VALUES
(1, 2, 'Cliente Avulso', NULL, '20.00', '20.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-06-23', '2025-09-10 04:18:31', '2025-09-10 04:18:31'),
(2, 2, 'Cliente Avulso', NULL, '145.00', '200.00', '55.00', '27.50', 'fixed', NULL, 'cash', 'A plastificação foi feita na gráfica de Carlos e Só tivemos uma margem de 5 meticais', '2025-06-23', '2025-09-10 04:23:03', '2025-09-10 04:54:31'),
(3, 2, 'Cliente Avulso', NULL, '72.50', '72.50', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-06-24', '2025-09-10 05:02:41', '2025-09-10 05:02:41'),
(4, 2, 'Cliente Avulso', NULL, '10.00', '20.00', '10.00', '50.00', 'fixed', NULL, 'cash', NULL, '2025-06-24', '2025-09-10 05:03:45', '2025-09-10 05:03:45'),
(5, 2, 'Cliente Avulso', NULL, '60.00', '100.00', '40.00', '40.00', 'fixed', NULL, 'cash', NULL, '2025-06-24', '2025-09-10 05:05:43', '2025-09-10 05:05:43'),
(6, 2, 'Cliente Avulso', NULL, '500.00', '500.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-06-26', '2025-09-10 05:13:49', '2025-09-10 05:13:49'),
(9, 2, 'Cliente Avulso', NULL, '100.00', '100.00', '0.00', '0.00', 'fixed', NULL, 'cash', 'Copia e Impressao', '2025-06-27', '2025-09-11 21:35:41', '2025-09-11 21:35:41'),
(10, 2, 'Cliente Avulso', NULL, '40.00', '40.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-06-27', '2025-09-11 21:38:46', '2025-09-11 21:38:46'),
(11, 2, 'Cliente Avulso', NULL, '5.00', '5.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-06-28', '2025-09-11 21:44:01', '2025-09-11 21:44:01'),
(12, 2, 'Cliente Avulso', NULL, '105.00', '115.00', '10.00', '8.70', 'fixed', NULL, 'cash', NULL, '2025-06-30', '2025-09-11 21:45:48', '2025-09-11 21:45:48'),
(13, 2, 'Cliente Avulso', NULL, '105.00', '105.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-07-01', '2025-09-11 21:46:50', '2025-09-11 21:46:50'),
(14, 2, 'Cliente Avulso', NULL, '120.00', '120.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-07-02', '2025-09-11 21:48:25', '2025-09-11 21:48:25'),
(15, 2, 'Cliente Avulso', NULL, '15.00', '15.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-07-03', '2025-09-11 22:41:57', '2025-09-11 22:41:57'),
(16, 2, 'Cliente Avulso', NULL, '3200.00', '3200.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-07-03', '2025-09-11 22:46:12', '2025-09-11 22:46:12'),
(17, 2, 'Cliente Avulso', NULL, '27.50', '27.50', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-07-04', '2025-09-11 22:47:07', '2025-09-11 22:47:07'),
(18, 2, 'Cliente Avulso', NULL, '152.50', '152.50', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-07-05', '2025-09-11 22:48:04', '2025-09-11 22:48:04'),
(20, 2, 'Cliente Avulso', NULL, '3065.00', '4065.00', '1000.00', '24.60', 'fixed', NULL, 'cash', NULL, '2025-07-08', '2025-09-12 03:14:30', '2025-09-12 03:14:30'),
(23, 2, 'Cliente Avulso', NULL, '3877.50', '3877.50', '0.00', '0.00', 'fixed', NULL, 'cash', 'Na camiseta bolo fofo --- registamos o va,or de 100 na foto tipo pass urgente o valor total era 500 mt', '2025-07-08', '2025-09-12 03:28:40', '2025-09-12 03:28:40'),
(24, 2, 'Cliente Avulso', NULL, '302.50', '302.50', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-07-08', '2025-09-12 03:32:15', '2025-09-12 03:32:15'),
(25, 2, 'Cliente Avulso', NULL, '175.00', '175.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-07-10', '2025-09-12 03:33:56', '2025-09-12 03:33:56'),
(26, 2, 'Cliente Avulso', NULL, '45.00', '45.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-07-11', '2025-09-12 03:34:56', '2025-09-12 03:34:56'),
(27, 2, 'Cliente Avulso', NULL, '404.50', '404.50', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-07-14', '2025-09-12 03:38:34', '2025-09-12 03:38:34'),
(28, 2, 'Cliente Avulso', NULL, '100.00', '100.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-07-14', '2025-09-12 03:39:29', '2025-09-12 03:39:29'),
(29, 2, 'DERCIO PINHEIRO', '865621557', '250.00', '250.00', '0.00', '0.00', 'fixed', NULL, 'cash', 'IMPRESSAO DE MONOGRAFIA', '2025-07-14', '2025-09-12 03:41:38', '2025-09-12 03:41:38'),
(30, 2, 'Wilson', '860879767', '100.00', '100.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-07-14', '2025-09-12 03:42:56', '2025-09-12 03:42:56'),
(31, 2, 'Cliente Avulso', NULL, '12.50', '12.50', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-07-15', '2025-09-12 03:44:53', '2025-09-12 03:44:53'),
(32, 2, 'Cliente Avulso', NULL, '100.00', '100.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-07-15', '2025-09-12 03:46:10', '2025-09-12 03:46:10'),
(33, 2, 'Cliente Avulso', NULL, '140.00', '140.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-07-16', '2025-09-12 03:47:01', '2025-09-12 03:47:01'),
(34, 2, 'Cliente Avulso', NULL, '95.00', '95.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-07-17', '2025-09-12 03:48:57', '2025-09-12 03:48:57'),
(35, 2, 'Cliente Avulso', NULL, '557.50', '557.50', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-07-18', '2025-09-12 03:50:20', '2025-09-12 03:50:20'),
(36, 2, 'Cliente Avulso', NULL, '25.00', '25.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-07-19', '2025-09-12 03:51:34', '2025-09-12 03:51:34'),
(37, 2, 'Cliente Avulso', NULL, '325.00', '325.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-07-21', '2025-09-12 03:52:46', '2025-09-12 03:52:46'),
(38, 2, 'Cliente Avulso', NULL, '7.50', '7.50', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-07-22', '2025-09-12 03:53:32', '2025-09-12 03:53:32'),
(40, 2, 'Cliente Avulso', NULL, '265.00', '315.00', '50.00', '15.87', 'fixed', NULL, 'cash', NULL, '2025-07-23', '2025-09-12 04:01:20', '2025-09-12 04:01:20'),
(41, 2, 'Cliente Avulso', NULL, '737.50', '737.50', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-07-24', '2025-09-12 04:12:53', '2025-09-12 04:12:53'),
(42, 2, 'Cliente Avulso', NULL, '1260.00', '1260.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-07-25', '2025-09-12 04:15:38', '2025-09-12 04:15:38'),
(43, 2, 'Cliente Avulso', NULL, '402.50', '402.50', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-07-28', '2025-09-12 04:23:01', '2025-09-12 04:23:01'),
(44, 2, 'Cliente Avulso', NULL, '20.00', '20.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-07-20', '2025-09-12 04:24:18', '2025-09-12 04:24:18'),
(45, 2, 'Cliente Avulso', NULL, '542.50', '542.50', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-07-31', '2025-09-12 04:26:06', '2025-09-12 04:26:06'),
(47, 2, 'Cliente Avulso', NULL, '860.00', '910.00', '50.00', '5.49', 'fixed', NULL, 'cash', NULL, '2025-08-02', '2025-09-15 12:47:25', '2025-09-15 12:47:25'),
(48, 2, 'Cliente Avulso', NULL, '451.00', '465.00', '14.00', '3.01', 'fixed', NULL, 'cash', NULL, '2025-08-01', '2025-09-15 13:28:50', '2025-09-15 13:28:50'),
(49, 2, 'Cliente Avulso', NULL, '722.50', '722.50', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-08-05', '2025-09-15 13:34:41', '2025-09-15 13:34:41'),
(50, 2, 'Cliente Avulso', NULL, '820.00', '820.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-08-06', '2025-09-15 13:45:52', '2025-09-15 13:45:52'),
(51, 2, 'Cliente Avulso', NULL, '5.00', '5.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-08-07', '2025-09-15 13:51:45', '2025-09-15 13:51:45'),
(52, 2, 'Cliente Avulso', NULL, '300.00', '325.00', '25.00', '7.69', 'fixed', NULL, 'cash', NULL, '2025-08-08', '2025-09-15 14:01:07', '2025-09-15 14:01:07'),
(53, 2, 'Cliente Avulso', NULL, '145.00', '145.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-08-11', '2025-09-15 14:06:26', '2025-09-15 14:06:27'),
(54, 2, 'Cliente Avulso', NULL, '100.00', '100.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-08-12', '2025-09-15 14:13:57', '2025-09-15 14:13:57'),
(55, 6, 'Cliente Avulso', NULL, '40.00', '40.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-08-13', '2025-09-17 06:15:19', '2025-09-17 06:15:19'),
(56, 6, 'Cliente Avulso', NULL, '37.50', '42.50', '5.00', '11.76', 'fixed', NULL, 'cash', NULL, '2025-08-14', '2025-09-17 06:18:11', '2025-09-17 06:18:11'),
(57, 6, 'Cliente Avulso', NULL, '107.50', '112.50', '5.00', '4.44', 'fixed', NULL, 'cash', NULL, '2025-08-15', '2025-09-17 06:31:24', '2025-09-17 06:31:24'),
(58, 6, 'Cliente Avulso', NULL, '77.50', '77.50', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-08-16', '2025-09-17 06:49:58', '2025-09-17 06:49:58'),
(59, 6, 'Cliente Avulso', NULL, '3255.00', '3268.50', '13.50', '0.41', 'fixed', NULL, 'cash', NULL, '2025-08-18', '2025-09-17 06:58:52', '2025-09-17 06:58:52'),
(60, 6, 'Cliente Avulso', NULL, '410.00', '410.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-08-19', '2025-09-17 07:20:50', '2025-09-17 07:20:50'),
(61, 6, 'Cliente Avulso', NULL, '3487.50', '3487.50', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-08-20', '2025-09-17 07:27:27', '2025-09-17 07:27:27'),
(62, 6, 'Cliente Avulso', NULL, '950.00', '950.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-08-21', '2025-09-17 07:41:28', '2025-09-17 07:41:28'),
(63, 6, 'Cliente Avulso', NULL, '1467.50', '1477.50', '10.00', '0.68', 'fixed', NULL, 'cash', NULL, '2025-08-21', '2025-09-17 07:46:29', '2025-09-17 07:46:29'),
(64, 6, 'Cliente Avulso', NULL, '65.00', '65.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-08-25', '2025-09-17 08:01:04', '2025-09-17 08:01:04'),
(65, 6, 'Cliente Avulso', NULL, '952.50', '952.50', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-08-26', '2025-09-17 08:56:30', '2025-09-17 08:56:30'),
(66, 6, 'Cliente Avulso', NULL, '150.00', '150.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-08-27', '2025-09-17 09:02:43', '2025-09-17 09:02:43'),
(67, 6, 'Cliente Avulso', NULL, '410.00', '410.00', '0.00', '0.00', 'fixed', NULL, 'cash', NULL, '2025-08-28', '2025-09-17 09:08:09', '2025-09-17 09:08:09'),
(68, 6, 'Cliente Avulso', NULL, '1492.50', '1492.50', '0.00', '0.00', 'fixed', NULL, 'cash', 'Wilson pagou uma divida de 65 mts e se fez uma impressao de 20 certificados cada 50mts', '2025-08-29', '2025-09-17 09:15:54', '2025-09-17 09:15:54'),
(69, 6, 'Cliente Avulso', NULL, '65.00', '65.00', '0.00', '0.00', 'fixed', NULL, 'cash', 'Wilson pagou uma divida de 65 mts', '2025-08-09', '2025-09-17 09:17:40', '2025-09-17 09:17:40'),
(70, 6, 'Cliente Avulso', NULL, '342.50', '402.50', '60.00', '14.91', 'fixed', NULL, 'cash', 'Wilson pagou uma divida de 210mts', '2025-09-17', '2025-09-17 09:23:47', '2025-09-17 09:23:47');

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` int NOT NULL,
  `sale_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `original_unit_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_percentage` decimal(5,2) DEFAULT NULL,
  `discount_type` varchar(20) DEFAULT NULL,
  `discount_reason` varchar(255) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `unit_price`, `original_unit_price`, `discount_amount`, `discount_percentage`, `discount_type`, `discount_reason`, `total_price`, `created_at`, `updated_at`) VALUES
(28, 1, 15, 2, '2.50', '2.50', '0.00', NULL, NULL, NULL, '5.00', '2025-09-10 04:18:31', '2025-09-10 04:18:31'),
(29, 1, 16, 1, '15.00', '15.00', '0.00', NULL, NULL, NULL, '15.00', '2025-09-10 04:18:31', '2025-09-10 04:18:31'),
(30, 2, 8, 2, '10.00', '15.00', '10.00', '33.33', 'item_level', 'Desconto aplicado na venda', '20.00', '2025-09-10 04:23:03', '2025-09-10 04:23:03'),
(31, 2, 15, 18, '2.50', '2.50', '0.00', NULL, NULL, NULL, '45.00', '2025-09-10 04:23:03', '2025-09-10 04:23:03'),
(32, 2, 4, 1, '75.00', '75.00', '0.00', NULL, NULL, NULL, '75.00', '2025-09-10 04:23:03', '2025-09-10 04:23:03'),
(33, 2, 19, 1, '5.00', '50.00', '45.00', '90.00', 'item_level', 'Desconto aplicado na venda', '5.00', '2025-09-10 04:23:03', '2025-09-10 04:23:03'),
(34, 3, 15, 29, '2.50', '2.50', '0.00', NULL, NULL, NULL, '72.50', '2025-09-10 05:02:41', '2025-09-10 05:02:41'),
(35, 4, 7, 1, '10.00', '20.00', '10.00', '50.00', 'item_level', 'Desconto aplicado na venda', '10.00', '2025-09-10 05:03:45', '2025-09-10 05:03:45'),
(36, 5, 8, 4, '10.00', '15.00', '20.00', '33.33', 'item_level', 'Desconto aplicado na venda', '40.00', '2025-09-10 05:05:43', '2025-09-10 05:05:43'),
(37, 5, 7, 2, '10.00', '20.00', '20.00', '50.00', 'item_level', 'Desconto aplicado na venda', '20.00', '2025-09-10 05:05:43', '2025-09-10 05:05:43'),
(38, 6, 20, 1, '400.00', '400.00', '0.00', NULL, NULL, NULL, '400.00', '2025-09-10 05:13:49', '2025-09-10 05:13:49'),
(39, 6, 5, 1, '100.00', '100.00', '0.00', NULL, NULL, NULL, '100.00', '2025-09-10 05:13:49', '2025-09-10 05:13:49'),
(42, 9, 15, 40, '2.50', '2.50', '0.00', NULL, NULL, NULL, '100.00', '2025-09-11 21:35:41', '2025-09-11 21:35:41'),
(43, 10, 15, 16, '2.50', '2.50', '0.00', NULL, NULL, NULL, '40.00', '2025-09-11 21:38:46', '2025-09-11 21:38:46'),
(44, 11, 15, 2, '2.50', '2.50', '0.00', NULL, NULL, NULL, '5.00', '2025-09-11 21:44:01', '2025-09-11 21:44:01'),
(45, 12, 8, 3, '15.00', '15.00', '0.00', NULL, NULL, NULL, '45.00', '2025-09-11 21:45:48', '2025-09-11 21:45:48'),
(46, 12, 15, 20, '2.50', '2.50', '0.00', NULL, NULL, NULL, '50.00', '2025-09-11 21:45:48', '2025-09-11 21:45:48'),
(47, 12, 7, 1, '10.00', '20.00', '10.00', '50.00', 'item_level', 'Desconto aplicado na venda', '10.00', '2025-09-11 21:45:48', '2025-09-11 21:45:48'),
(48, 13, 15, 12, '2.50', '2.50', '0.00', NULL, NULL, NULL, '30.00', '2025-09-11 21:46:50', '2025-09-11 21:46:50'),
(49, 13, 4, 1, '75.00', '75.00', '0.00', NULL, NULL, NULL, '75.00', '2025-09-11 21:46:50', '2025-09-11 21:46:50'),
(50, 14, 15, 40, '2.50', '2.50', '0.00', NULL, NULL, NULL, '100.00', '2025-09-11 21:48:25', '2025-09-11 21:48:25'),
(51, 14, 7, 1, '20.00', '20.00', '0.00', NULL, NULL, NULL, '20.00', '2025-09-11 21:48:25', '2025-09-11 21:48:25'),
(52, 15, 15, 6, '2.50', '2.50', '0.00', NULL, NULL, NULL, '15.00', '2025-09-11 22:41:57', '2025-09-11 22:41:57'),
(53, 16, 20, 8, '400.00', '400.00', '0.00', NULL, NULL, NULL, '3200.00', '2025-09-11 22:46:12', '2025-09-11 22:46:12'),
(54, 17, 15, 11, '2.50', '2.50', '0.00', NULL, NULL, NULL, '27.50', '2025-09-11 22:47:07', '2025-09-11 22:47:07'),
(55, 18, 15, 1, '2.50', '2.50', '0.00', NULL, NULL, NULL, '2.50', '2025-09-11 22:48:04', '2025-09-11 22:48:04'),
(56, 18, 3, 1, '150.00', '150.00', '0.00', NULL, NULL, NULL, '150.00', '2025-09-11 22:48:04', '2025-09-11 22:48:04'),
(59, 20, 20, 10, '300.00', '400.00', '1000.00', '25.00', 'item_level', 'Desconto aplicado na venda', '3000.00', '2025-09-12 03:14:30', '2025-09-12 03:14:30'),
(60, 20, 15, 26, '2.50', '2.50', '0.00', NULL, NULL, NULL, '65.00', '2025-09-12 03:14:30', '2025-09-12 03:14:30'),
(68, 23, 20, 9, '400.00', '400.00', '0.00', NULL, NULL, NULL, '3600.00', '2025-09-12 03:28:40', '2025-09-12 03:28:40'),
(69, 23, 15, 71, '2.50', '2.50', '0.00', NULL, NULL, NULL, '177.50', '2025-09-12 03:28:40', '2025-09-12 03:28:40'),
(70, 23, 5, 1, '100.00', '100.00', '0.00', NULL, NULL, NULL, '100.00', '2025-09-12 03:28:40', '2025-09-12 03:28:40'),
(71, 24, 15, 1, '2.50', '2.50', '0.00', NULL, NULL, NULL, '2.50', '2025-09-12 03:32:15', '2025-09-12 03:32:15'),
(72, 24, 5, 1, '100.00', '100.00', '0.00', NULL, NULL, NULL, '100.00', '2025-09-12 03:32:15', '2025-09-12 03:32:15'),
(73, 24, 25, 200, '1.00', '1.00', '0.00', NULL, NULL, NULL, '200.00', '2025-09-12 03:32:15', '2025-09-12 03:32:15'),
(74, 25, 15, 22, '2.50', '2.50', '0.00', NULL, NULL, NULL, '55.00', '2025-09-12 03:33:56', '2025-09-12 03:33:56'),
(75, 25, 7, 1, '20.00', '20.00', '0.00', NULL, NULL, NULL, '20.00', '2025-09-12 03:33:56', '2025-09-12 03:33:56'),
(76, 25, 5, 1, '100.00', '100.00', '0.00', NULL, NULL, NULL, '100.00', '2025-09-12 03:33:56', '2025-09-12 03:33:56'),
(77, 26, 15, 12, '2.50', '2.50', '0.00', NULL, NULL, NULL, '30.00', '2025-09-12 03:34:56', '2025-09-12 03:34:56'),
(78, 26, 16, 1, '15.00', '15.00', '0.00', NULL, NULL, NULL, '15.00', '2025-09-12 03:34:56', '2025-09-12 03:34:56'),
(79, 27, 8, 1, '15.00', '15.00', '0.00', NULL, NULL, NULL, '15.00', '2025-09-12 03:38:34', '2025-09-12 03:38:34'),
(80, 27, 15, 141, '2.50', '2.50', '0.00', NULL, NULL, NULL, '352.50', '2025-09-12 03:38:34', '2025-09-12 03:38:34'),
(81, 27, 7, 1, '20.00', '20.00', '0.00', NULL, NULL, NULL, '20.00', '2025-09-12 03:38:34', '2025-09-12 03:38:34'),
(82, 27, 14, 2, '1.00', '1.00', '0.00', NULL, NULL, NULL, '2.00', '2025-09-12 03:38:34', '2025-09-12 03:38:34'),
(83, 27, 18, 1, '15.00', '15.00', '0.00', NULL, NULL, NULL, '15.00', '2025-09-12 03:38:34', '2025-09-12 03:38:34'),
(84, 28, 5, 1, '100.00', '100.00', '0.00', NULL, NULL, NULL, '100.00', '2025-09-12 03:39:29', '2025-09-12 03:39:29'),
(85, 29, 15, 100, '2.50', '2.50', '0.00', NULL, NULL, NULL, '250.00', '2025-09-12 03:41:38', '2025-09-12 03:41:38'),
(86, 30, 15, 40, '2.50', '2.50', '0.00', NULL, NULL, NULL, '100.00', '2025-09-12 03:42:56', '2025-09-12 03:42:56'),
(87, 31, 15, 5, '2.50', '2.50', '0.00', NULL, NULL, NULL, '12.50', '2025-09-12 03:44:53', '2025-09-12 03:44:53'),
(88, 32, 26, 1, '100.00', '100.00', '0.00', NULL, NULL, NULL, '100.00', '2025-09-12 03:46:10', '2025-09-12 03:46:10'),
(89, 33, 15, 50, '2.50', '2.50', '0.00', NULL, NULL, NULL, '125.00', '2025-09-12 03:47:01', '2025-09-12 03:47:01'),
(90, 33, 10, 1, '15.00', '15.00', '0.00', NULL, NULL, NULL, '15.00', '2025-09-12 03:47:01', '2025-09-12 03:47:01'),
(91, 34, 8, 1, '15.00', '15.00', '0.00', NULL, NULL, NULL, '15.00', '2025-09-12 03:48:57', '2025-09-12 03:48:57'),
(92, 34, 15, 14, '2.50', '2.50', '0.00', NULL, NULL, NULL, '35.00', '2025-09-12 03:48:57', '2025-09-12 03:48:57'),
(93, 34, 16, 2, '15.00', '15.00', '0.00', NULL, NULL, NULL, '30.00', '2025-09-12 03:48:57', '2025-09-12 03:48:57'),
(94, 34, 18, 1, '15.00', '15.00', '0.00', NULL, NULL, NULL, '15.00', '2025-09-12 03:48:57', '2025-09-12 03:48:57'),
(95, 35, 8, 2, '15.00', '15.00', '0.00', NULL, NULL, NULL, '30.00', '2025-09-12 03:50:20', '2025-09-12 03:50:20'),
(96, 35, 15, 205, '2.50', '2.50', '0.00', NULL, NULL, NULL, '512.50', '2025-09-12 03:50:20', '2025-09-12 03:50:20'),
(97, 35, 18, 1, '15.00', '15.00', '0.00', NULL, NULL, NULL, '15.00', '2025-09-12 03:50:20', '2025-09-12 03:50:20'),
(98, 36, 15, 10, '2.50', '2.50', '0.00', NULL, NULL, NULL, '25.00', '2025-09-12 03:51:34', '2025-09-12 03:51:34'),
(99, 37, 15, 122, '2.50', '2.50', '0.00', NULL, NULL, NULL, '305.00', '2025-09-12 03:52:46', '2025-09-12 03:52:46'),
(100, 37, 11, 1, '10.00', '10.00', '0.00', NULL, NULL, NULL, '10.00', '2025-09-12 03:52:46', '2025-09-12 03:52:46'),
(101, 37, 14, 10, '1.00', '1.00', '0.00', NULL, NULL, NULL, '10.00', '2025-09-12 03:52:46', '2025-09-12 03:52:46'),
(102, 38, 15, 3, '2.50', '2.50', '0.00', NULL, NULL, NULL, '7.50', '2025-09-12 03:53:32', '2025-09-12 03:53:32'),
(110, 40, 8, 1, '15.00', '15.00', '0.00', NULL, NULL, NULL, '15.00', '2025-09-12 04:01:20', '2025-09-12 04:01:20'),
(111, 40, 15, 26, '2.50', '2.50', '0.00', NULL, NULL, NULL, '65.00', '2025-09-12 04:01:20', '2025-09-12 04:01:20'),
(112, 40, 16, 1, '15.00', '15.00', '0.00', NULL, NULL, NULL, '15.00', '2025-09-12 04:01:20', '2025-09-12 04:01:20'),
(113, 40, 7, 3, '20.00', '20.00', '0.00', NULL, NULL, NULL, '60.00', '2025-09-12 04:01:20', '2025-09-12 04:01:20'),
(114, 40, 3, 1, '100.00', '150.00', '50.00', '33.33', 'item_level', 'Desconto aplicado na venda', '100.00', '2025-09-12 04:01:20', '2025-09-12 04:01:20'),
(115, 40, 11, 1, '10.00', '10.00', '0.00', NULL, NULL, NULL, '10.00', '2025-09-12 04:01:20', '2025-09-12 04:01:20'),
(116, 41, 15, 15, '2.50', '2.50', '0.00', NULL, NULL, NULL, '37.50', '2025-09-12 04:12:53', '2025-09-12 04:12:53'),
(117, 41, 28, 1, '700.00', '700.00', '0.00', NULL, NULL, NULL, '700.00', '2025-09-12 04:12:53', '2025-09-12 04:12:53'),
(118, 42, 20, 3, '400.00', '400.00', '0.00', NULL, NULL, NULL, '1200.00', '2025-09-12 04:15:38', '2025-09-12 04:15:38'),
(119, 42, 7, 3, '20.00', '20.00', '0.00', NULL, NULL, NULL, '60.00', '2025-09-12 04:15:38', '2025-09-12 04:15:38'),
(120, 43, 9, 1, '15.00', '15.00', '0.00', NULL, NULL, NULL, '15.00', '2025-09-12 04:23:01', '2025-09-12 04:23:01'),
(121, 43, 15, 29, '2.50', '2.50', '0.00', NULL, NULL, NULL, '72.50', '2025-09-12 04:23:01', '2025-09-12 04:23:01'),
(122, 43, 16, 1, '15.00', '15.00', '0.00', NULL, NULL, NULL, '15.00', '2025-09-12 04:23:01', '2025-09-12 04:23:01'),
(123, 43, 3, 2, '150.00', '150.00', '0.00', NULL, NULL, NULL, '300.00', '2025-09-12 04:23:01', '2025-09-12 04:23:01'),
(124, 44, 15, 4, '2.50', '2.50', '0.00', NULL, NULL, NULL, '10.00', '2025-09-12 04:24:18', '2025-09-12 04:24:18'),
(125, 44, 11, 1, '10.00', '10.00', '0.00', NULL, NULL, NULL, '10.00', '2025-09-12 04:24:18', '2025-09-12 04:24:18'),
(126, 45, 35, 1, '450.00', '450.00', '0.00', NULL, NULL, NULL, '450.00', '2025-09-12 04:26:06', '2025-09-12 04:26:06'),
(127, 45, 15, 31, '2.50', '2.50', '0.00', NULL, NULL, NULL, '77.50', '2025-09-12 04:26:06', '2025-09-12 04:26:06'),
(128, 45, 18, 1, '15.00', '15.00', '0.00', NULL, NULL, NULL, '15.00', '2025-09-12 04:26:06', '2025-09-12 04:26:06'),
(139, 47, 35, 1, '450.00', '450.00', '0.00', NULL, NULL, NULL, '450.00', '2025-09-15 12:47:25', '2025-09-15 12:47:25'),
(140, 47, 20, 1, '350.00', '400.00', '50.00', '12.50', 'item_level', 'Desconto aplicado na venda', '350.00', '2025-09-15 12:47:25', '2025-09-15 12:47:25'),
(141, 47, 11, 1, '10.00', '10.00', '0.00', NULL, NULL, NULL, '10.00', '2025-09-15 12:47:25', '2025-09-15 12:47:25'),
(142, 47, 12, 1, '50.00', '50.00', '0.00', NULL, NULL, NULL, '50.00', '2025-09-15 12:47:25', '2025-09-15 12:47:25'),
(143, 48, 20, 1, '400.00', '400.00', '0.00', NULL, NULL, NULL, '400.00', '2025-09-15 13:28:50', '2025-09-15 13:28:50'),
(144, 48, 15, 12, '1.75', '2.50', '9.00', '30.00', 'item_level', 'Desconto aplicado na venda', '21.00', '2025-09-15 13:28:50', '2025-09-15 13:28:50'),
(145, 48, 16, 1, '10.00', '15.00', '5.00', '33.33', 'item_level', 'Desconto aplicado na venda', '10.00', '2025-09-15 13:28:50', '2025-09-15 13:28:50'),
(146, 48, 11, 1, '10.00', '10.00', '0.00', NULL, NULL, NULL, '10.00', '2025-09-15 13:28:50', '2025-09-15 13:28:50'),
(147, 48, 14, 10, '1.00', '1.00', '0.00', NULL, NULL, NULL, '10.00', '2025-09-15 13:28:50', '2025-09-15 13:28:50'),
(148, 49, 15, 161, '2.50', '2.50', '0.00', NULL, NULL, NULL, '402.50', '2025-09-15 13:34:41', '2025-09-15 13:34:41'),
(149, 49, 7, 1, '20.00', '20.00', '0.00', NULL, NULL, NULL, '20.00', '2025-09-15 13:34:41', '2025-09-15 13:34:41'),
(150, 49, 3, 2, '150.00', '150.00', '0.00', NULL, NULL, NULL, '300.00', '2025-09-15 13:34:41', '2025-09-15 13:34:41'),
(151, 50, 20, 2, '400.00', '400.00', '0.00', NULL, NULL, NULL, '800.00', '2025-09-15 13:45:52', '2025-09-15 13:45:52'),
(152, 50, 15, 8, '2.50', '2.50', '0.00', NULL, NULL, NULL, '20.00', '2025-09-15 13:45:52', '2025-09-15 13:45:52'),
(153, 51, 15, 2, '2.50', '2.50', '0.00', NULL, NULL, NULL, '5.00', '2025-09-15 13:51:45', '2025-09-15 13:51:45'),
(154, 52, 8, 3, '10.00', '15.00', '15.00', '33.33', 'item_level', 'Desconto aplicado na venda', '30.00', '2025-09-15 14:01:07', '2025-09-15 14:01:07'),
(155, 52, 15, 84, '2.50', '2.50', '0.00', NULL, NULL, NULL, '210.00', '2025-09-15 14:01:07', '2025-09-15 14:01:07'),
(156, 52, 16, 2, '10.00', '15.00', '10.00', '33.33', 'item_level', 'Desconto aplicado na venda', '20.00', '2025-09-15 14:01:07', '2025-09-15 14:01:07'),
(157, 52, 7, 2, '20.00', '20.00', '0.00', NULL, NULL, NULL, '40.00', '2025-09-15 14:01:07', '2025-09-15 14:01:07'),
(158, 53, 15, 18, '2.50', '2.50', '0.00', NULL, NULL, NULL, '45.00', '2025-09-15 14:06:27', '2025-09-15 14:06:27'),
(159, 53, 27, 1, '100.00', '100.00', '0.00', NULL, NULL, NULL, '100.00', '2025-09-15 14:06:27', '2025-09-15 14:06:27'),
(160, 54, 15, 40, '2.50', '2.50', '0.00', NULL, NULL, NULL, '100.00', '2025-09-15 14:13:57', '2025-09-15 14:13:57'),
(161, 55, 15, 16, '2.50', '2.50', '0.00', NULL, NULL, NULL, '40.00', '2025-09-17 06:15:19', '2025-09-17 06:15:19'),
(162, 56, 8, 1, '10.00', '15.00', '5.00', '33.33', 'item_level', 'Desconto aplicado na venda', '10.00', '2025-09-17 06:18:11', '2025-09-17 06:18:11'),
(163, 56, 15, 11, '2.50', '2.50', '0.00', NULL, NULL, NULL, '27.50', '2025-09-17 06:18:11', '2025-09-17 06:18:11'),
(164, 57, 8, 1, '10.00', '15.00', '5.00', '33.33', 'item_level', 'Desconto aplicado na venda', '10.00', '2025-09-17 06:31:24', '2025-09-17 06:31:24'),
(165, 57, 15, 19, '2.50', '2.50', '0.00', NULL, NULL, NULL, '47.50', '2025-09-17 06:31:24', '2025-09-17 06:31:24'),
(166, 57, 12, 1, '50.00', '50.00', '0.00', NULL, NULL, NULL, '50.00', '2025-09-17 06:31:24', '2025-09-17 06:31:24'),
(167, 58, 15, 25, '2.50', '2.50', '0.00', NULL, NULL, NULL, '62.50', '2025-09-17 06:49:58', '2025-09-17 06:49:58'),
(168, 58, 16, 1, '15.00', '15.00', '0.00', NULL, NULL, NULL, '15.00', '2025-09-17 06:49:58', '2025-09-17 06:49:58'),
(169, 59, 35, 2, '450.00', '450.00', '0.00', NULL, NULL, NULL, '900.00', '2025-09-17 06:58:52', '2025-09-17 06:58:52'),
(170, 59, 20, 5, '400.00', '400.00', '0.00', NULL, NULL, NULL, '2000.00', '2025-09-17 06:58:52', '2025-09-17 06:58:52'),
(171, 59, 15, 27, '2.00', '2.50', '13.50', '20.00', 'item_level', 'Desconto aplicado na venda', '54.00', '2025-09-17 06:58:52', '2025-09-17 06:58:52'),
(172, 59, 3, 2, '150.00', '150.00', '0.00', NULL, NULL, NULL, '300.00', '2025-09-17 06:58:52', '2025-09-17 06:58:52'),
(173, 59, 25, 1, '2000.00', '1.00', '0.00', NULL, NULL, NULL, '2000.00', '2025-09-17 06:58:52', '2025-09-17 06:58:52'),
(174, 60, 15, 164, '2.50', '2.50', '0.00', NULL, NULL, NULL, '410.00', '2025-09-17 07:20:50', '2025-09-17 07:20:50'),
(175, 61, 35, 1, '450.00', '450.00', '0.00', NULL, NULL, NULL, '450.00', '2025-09-17 07:27:27', '2025-09-17 07:27:27'),
(176, 61, 20, 7, '400.00', '400.00', '0.00', NULL, NULL, NULL, '2800.00', '2025-09-17 07:27:27', '2025-09-17 07:27:27'),
(177, 61, 15, 35, '2.50', '2.50', '0.00', NULL, NULL, NULL, '87.50', '2025-09-17 07:27:27', '2025-09-17 07:27:27'),
(178, 61, 3, 1, '150.00', '150.00', '0.00', NULL, NULL, NULL, '150.00', '2025-09-17 07:27:27', '2025-09-17 07:27:27'),
(179, 62, 20, 2, '400.00', '400.00', '0.00', NULL, NULL, NULL, '800.00', '2025-09-17 07:41:28', '2025-09-17 07:41:28'),
(180, 62, 3, 1, '150.00', '150.00', '0.00', NULL, NULL, NULL, '150.00', '2025-09-17 07:41:28', '2025-09-17 07:41:28'),
(181, 63, 35, 2, '500.00', '450.00', '0.00', NULL, NULL, NULL, '1000.00', '2025-09-17 07:46:29', '2025-09-17 07:46:29'),
(182, 63, 8, 2, '10.00', '15.00', '10.00', '33.33', 'item_level', 'Desconto aplicado na venda', '20.00', '2025-09-17 07:46:29', '2025-09-17 07:46:29'),
(183, 63, 15, 195, '2.50', '2.50', '0.00', NULL, NULL, NULL, '487.50', '2025-09-17 07:46:29', '2025-09-17 07:46:29'),
(184, 63, 16, 2, '15.00', '15.00', '0.00', NULL, NULL, NULL, '30.00', '2025-09-17 07:46:29', '2025-09-17 07:46:29'),
(185, 63, 14, 15, '1.00', '1.00', '0.00', NULL, NULL, NULL, '15.00', '2025-09-17 07:46:29', '2025-09-17 07:46:29'),
(186, 63, 10, 1, '15.00', '15.00', '0.00', NULL, NULL, NULL, '15.00', '2025-09-17 07:46:29', '2025-09-17 07:46:29'),
(187, 64, 15, 26, '2.50', '2.50', '0.00', NULL, NULL, NULL, '65.00', '2025-09-17 08:01:04', '2025-09-17 08:01:04'),
(188, 65, 13, 1, '10.00', '10.00', '0.00', NULL, NULL, NULL, '10.00', '2025-09-17 08:56:30', '2025-09-17 08:56:30'),
(189, 65, 20, 2, '500.00', '400.00', '0.00', NULL, NULL, NULL, '1000.00', '2025-09-17 08:56:30', '2025-09-17 08:56:30'),
(190, 65, 15, 43, '2.50', '2.50', '0.00', NULL, NULL, NULL, '107.50', '2025-09-17 08:56:30', '2025-09-17 08:56:30'),
(191, 65, 7, 1, '20.00', '20.00', '0.00', NULL, NULL, NULL, '20.00', '2025-09-17 08:56:30', '2025-09-17 08:56:30'),
(192, 65, 10, 1, '15.00', '15.00', '0.00', NULL, NULL, NULL, '15.00', '2025-09-17 08:56:30', '2025-09-17 08:56:30'),
(193, 66, 15, 60, '2.50', '2.50', '0.00', NULL, NULL, NULL, '150.00', '2025-09-17 09:02:43', '2025-09-17 09:02:43'),
(194, 67, 15, 44, '2.50', '2.50', '0.00', NULL, NULL, NULL, '110.00', '2025-09-17 09:08:09', '2025-09-17 09:08:09'),
(195, 67, 3, 2, '170.00', '150.00', '0.00', NULL, NULL, NULL, '340.00', '2025-09-17 09:08:09', '2025-09-17 09:08:09'),
(196, 68, 35, 2, '450.00', '450.00', '0.00', NULL, NULL, NULL, '900.00', '2025-09-17 09:15:54', '2025-09-17 09:15:54'),
(197, 68, 15, 229, '2.50', '2.50', '0.00', NULL, NULL, NULL, '572.50', '2025-09-17 09:15:54', '2025-09-17 09:15:54'),
(198, 68, 25, 20, '50.00', '1.00', '0.00', NULL, NULL, NULL, '1000.00', '2025-09-17 09:15:54', '2025-09-17 09:15:54'),
(199, 69, 25, 65, '1.00', '1.00', '0.00', NULL, NULL, NULL, '65.00', '2025-09-17 09:17:40', '2025-09-17 09:17:40'),
(200, 70, 8, 4, '10.00', '15.00', '20.00', '33.33', 'item_level', 'Desconto aplicado na venda', '40.00', '2025-09-17 09:23:47', '2025-09-17 09:23:47'),
(201, 70, 9, 8, '10.00', '15.00', '40.00', '33.33', 'item_level', 'Desconto aplicado na venda', '80.00', '2025-09-17 09:23:47', '2025-09-17 09:23:47'),
(202, 70, 15, 19, '2.50', '2.50', '0.00', NULL, NULL, NULL, '47.50', '2025-09-17 09:23:47', '2025-09-17 09:23:47'),
(203, 70, 7, 1, '20.00', '20.00', '0.00', NULL, NULL, NULL, '20.00', '2025-09-17 09:23:47', '2025-09-17 09:23:47'),
(204, 70, 11, 3, '10.00', '10.00', '0.00', NULL, NULL, NULL, '30.00', '2025-09-17 09:23:47', '2025-09-17 09:23:47'),
(205, 70, 25, 110, '1.00', '1.00', '0.00', NULL, NULL, NULL, '110.00', '2025-09-17 09:23:47', '2025-09-17 09:23:47'),
(206, 70, 14, 15, '1.00', '1.00', '0.00', NULL, NULL, NULL, '15.00', '2025-09-17 09:23:47', '2025-09-17 09:23:47');

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `user_id` int NOT NULL,
  `movement_type` enum('in','out','adjustment') NOT NULL,
  `quantity` int NOT NULL,
  `reason` varchar(200) DEFAULT NULL,
  `reference_id` int DEFAULT NULL,
  `movement_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `product_id`, `user_id`, `movement_type`, `quantity`, `reason`, `reference_id`, `movement_date`, `created_at`, `updated_at`) VALUES
(132, 8, 2, 'out', 1, 'Venda', 81, '2025-09-08', '2025-09-08 15:40:09', '2025-09-08 15:40:09'),
(133, 9, 2, 'out', 1, 'Venda', 81, '2025-09-08', '2025-09-08 15:40:09', '2025-09-08 15:40:09'),
(134, 9, 2, 'out', 1, 'Venda', 83, '2025-09-09', '2025-09-09 07:15:44', '2025-09-09 07:15:44'),
(135, 9, 2, 'in', 1, 'Reversão de venda cancelada', 83, '2025-09-09', '2025-09-09 07:16:43', '2025-09-09 07:16:43'),
(136, 14, 2, 'out', 1, 'Venda', 84, '2025-09-09', '2025-09-09 16:57:27', '2025-09-09 16:57:27'),
(137, 14, 2, 'in', 1, 'Reversão de venda cancelada', 84, '2025-09-09', '2025-09-09 16:57:35', '2025-09-09 16:57:35'),
(138, 13, 2, 'out', 1, 'Venda', 85, '2025-09-09', '2025-09-09 17:00:35', '2025-09-09 17:00:35'),
(139, 8, 2, 'out', 1, 'Venda', 85, '2025-09-09', '2025-09-09 17:00:35', '2025-09-09 17:00:35'),
(140, 9, 2, 'out', 1, 'Venda', 85, '2025-09-09', '2025-09-09 17:00:35', '2025-09-09 17:00:35'),
(141, 8, 2, 'in', 1, 'Reversão de venda cancelada', 81, '2025-09-09', '2025-09-09 17:00:52', '2025-09-09 17:00:52'),
(142, 9, 2, 'in', 1, 'Reversão de venda cancelada', 81, '2025-09-09', '2025-09-09 17:00:52', '2025-09-09 17:00:52'),
(143, 9, 2, 'out', 1, 'Venda', 86, '2025-09-10', '2025-09-10 02:14:51', '2025-09-10 02:14:51'),
(144, 9, 2, 'in', 1, 'Reversão de venda cancelada', 86, '2025-09-10', '2025-09-10 02:14:58', '2025-09-10 02:14:58'),
(145, 8, 2, 'out', 1, 'Venda', 88, '2025-06-23', '2025-09-10 03:42:43', '2025-09-10 03:42:43'),
(146, 13, 2, 'in', 1, 'Reversão de venda cancelada', 85, '2025-09-10', '2025-09-10 04:05:54', '2025-09-10 04:05:54'),
(147, 8, 2, 'in', 1, 'Reversão de venda cancelada', 85, '2025-09-10', '2025-09-10 04:05:54', '2025-09-10 04:05:54'),
(148, 9, 2, 'in', 1, 'Reversão de venda cancelada', 85, '2025-09-10', '2025-09-10 04:05:54', '2025-09-10 04:05:54'),
(149, 8, 2, 'in', 1, 'Reversão de venda cancelada', 88, '2025-09-10', '2025-09-10 04:08:29', '2025-09-10 04:08:29'),
(150, 8, 2, 'out', 2, 'Venda', 89, '2025-09-06', '2025-09-10 04:15:36', '2025-09-10 04:15:36'),
(151, 8, 2, 'in', 2, 'Reversão de venda cancelada', 89, '2025-09-10', '2025-09-10 04:15:57', '2025-09-10 04:15:57'),
(152, 8, 2, 'out', 2, 'Venda', 2, '2025-06-23', '2025-09-10 04:23:03', '2025-09-10 04:23:03'),
(153, 7, 2, 'out', 1, 'Venda', 4, '2025-06-24', '2025-09-10 05:03:45', '2025-09-10 05:03:45'),
(154, 8, 2, 'out', 4, 'Venda', 5, '2025-06-24', '2025-09-10 05:05:43', '2025-09-10 05:05:43'),
(155, 7, 2, 'out', 2, 'Venda', 5, '2025-06-24', '2025-09-10 05:05:43', '2025-09-10 05:05:43'),
(156, 20, 2, 'in', 1, 'Campra de 1 Camiseta Polo Guiqui', NULL, '2025-09-10', '2025-09-10 05:12:43', '2025-09-10 05:12:43'),
(157, 20, 2, 'out', 1, 'Venda', 6, '2025-06-26', '2025-09-10 05:13:49', '2025-09-10 05:13:49'),
(158, 13, 2, 'out', 1, 'Venda convertida em dívida', 6, '2025-09-10', '2025-09-10 17:04:34', '2025-09-10 17:04:34'),
(159, 8, 2, 'out', 3, 'Venda', 12, '2025-06-30', '2025-09-11 21:45:48', '2025-09-11 21:45:48'),
(160, 7, 2, 'out', 1, 'Venda', 12, '2025-06-30', '2025-09-11 21:45:48', '2025-09-11 21:45:48'),
(161, 7, 2, 'out', 1, 'Venda', 14, '2025-07-02', '2025-09-11 21:48:25', '2025-09-11 21:48:25'),
(162, 15, 2, 'out', 40, 'Venda', NULL, '2025-09-12', '2025-09-11 22:08:23', '2025-09-11 22:08:23'),
(163, 15, 2, 'in', 500, 'Compra de um bloco de resma no dia 08/07/2025', NULL, '2025-09-12', '2025-09-11 22:09:30', '2025-09-11 22:09:30'),
(164, 35, 2, 'in', 4, 'Compra de Camisetas referente a Dispesa do dia 08/07/2025', NULL, '2025-09-12', '2025-09-11 22:25:16', '2025-09-11 22:25:16'),
(165, 15, 2, 'in', 500, 'Compra referente a dispesa do dia 21/07/2025', NULL, '2025-09-12', '2025-09-11 22:35:53', '2025-09-11 22:35:53'),
(166, 20, 2, 'in', 4, 'Compra de Camisetas referente a saida do doa 03/072025', NULL, '2025-09-12', '2025-09-11 22:38:59', '2025-09-11 22:38:59'),
(167, 15, 2, 'out', 6, 'Venda', 15, '2025-07-03', '2025-09-11 22:41:57', '2025-09-11 22:41:57'),
(168, 20, 2, 'in', 10, 'Compradas??? Prcisamos Rever', NULL, '2025-09-12', '2025-09-11 22:45:28', '2025-09-11 22:45:28'),
(169, 20, 2, 'out', 8, 'Venda', 16, '2025-07-03', '2025-09-11 22:46:12', '2025-09-11 22:46:12'),
(170, 15, 2, 'out', 11, 'Venda', 17, '2025-07-04', '2025-09-11 22:47:07', '2025-09-11 22:47:07'),
(171, 15, 2, 'out', 1, 'Venda', 18, '2025-07-05', '2025-09-11 22:48:04', '2025-09-11 22:48:04'),
(172, 15, 2, 'out', 26, 'Venda', 19, '2025-09-12', '2025-09-12 03:09:58', '2025-09-12 03:09:58'),
(173, 15, 2, 'in', 26, 'Reversão de venda cancelada', 19, '2025-09-12', '2025-09-12 03:10:27', '2025-09-12 03:10:27'),
(174, 20, 2, 'in', 10, 'Camisetas Para Gurué GYM', NULL, '2025-09-12', '2025-09-12 03:11:32', '2025-09-12 03:11:32'),
(175, 20, 2, 'out', 10, 'Venda', 20, '2025-07-07', '2025-09-12 03:14:30', '2025-09-12 03:14:30'),
(176, 15, 2, 'out', 26, 'Venda', 20, '2025-07-07', '2025-09-12 03:14:30', '2025-09-12 03:14:30'),
(177, 20, 2, 'in', 9, 'Camisetas de Show - Jaime', NULL, '2025-09-12', '2025-09-12 03:17:53', '2025-09-12 03:17:53'),
(178, 20, 2, 'out', 9, 'Venda', 21, '2025-09-12', '2025-09-12 03:20:12', '2025-09-12 03:20:12'),
(179, 15, 2, 'out', 71, 'Venda', 21, '2025-09-12', '2025-09-12 03:20:12', '2025-09-12 03:20:12'),
(180, 20, 2, 'in', 9, 'Reversão de venda cancelada', 21, '2025-09-12', '2025-09-12 03:23:41', '2025-09-12 03:23:41'),
(181, 15, 2, 'in', 71, 'Reversão de venda cancelada', 21, '2025-09-12', '2025-09-12 03:23:41', '2025-09-12 03:23:41'),
(182, 20, 2, 'out', 9, 'Venda', 22, '2025-07-08', '2025-09-12 03:25:22', '2025-09-12 03:25:22'),
(183, 15, 2, 'out', 71, 'Venda', 22, '2025-07-08', '2025-09-12 03:25:22', '2025-09-12 03:25:22'),
(184, 20, 2, 'in', 9, 'Reversão de venda cancelada', 22, '2025-09-12', '2025-09-12 03:27:01', '2025-09-12 03:27:01'),
(185, 15, 2, 'in', 71, 'Reversão de venda cancelada', 22, '2025-09-12', '2025-09-12 03:27:01', '2025-09-12 03:27:01'),
(186, 20, 2, 'out', 9, 'Venda', 23, '2025-07-08', '2025-09-12 03:28:40', '2025-09-12 03:28:40'),
(187, 15, 2, 'out', 71, 'Venda', 23, '2025-07-08', '2025-09-12 03:28:40', '2025-09-12 03:28:40'),
(188, 15, 2, 'out', 1, 'Venda', 24, '2025-07-08', '2025-09-12 03:32:15', '2025-09-12 03:32:15'),
(189, 15, 2, 'out', 22, 'Venda', 25, '2025-07-10', '2025-09-12 03:33:56', '2025-09-12 03:33:56'),
(190, 7, 2, 'out', 1, 'Venda', 25, '2025-07-10', '2025-09-12 03:33:56', '2025-09-12 03:33:56'),
(191, 15, 2, 'out', 12, 'Venda', 26, '2025-07-11', '2025-09-12 03:34:56', '2025-09-12 03:34:56'),
(192, 8, 2, 'out', 1, 'Venda', 27, '2025-07-14', '2025-09-12 03:38:34', '2025-09-12 03:38:34'),
(193, 15, 2, 'out', 141, 'Venda', 27, '2025-07-14', '2025-09-12 03:38:34', '2025-09-12 03:38:34'),
(194, 7, 2, 'out', 1, 'Venda', 27, '2025-07-14', '2025-09-12 03:38:34', '2025-09-12 03:38:34'),
(195, 14, 2, 'out', 2, 'Venda', 27, '2025-07-14', '2025-09-12 03:38:34', '2025-09-12 03:38:34'),
(196, 15, 2, 'out', 100, 'Venda', 29, '2025-07-14', '2025-09-12 03:41:38', '2025-09-12 03:41:38'),
(197, 15, 2, 'out', 40, 'Venda', 30, '2025-07-14', '2025-09-12 03:42:56', '2025-09-12 03:42:56'),
(198, 15, 2, 'out', 5, 'Venda', 31, '2025-07-15', '2025-09-12 03:44:53', '2025-09-12 03:44:53'),
(199, 15, 2, 'out', 50, 'Venda', 33, '2025-07-16', '2025-09-12 03:47:01', '2025-09-12 03:47:01'),
(200, 10, 2, 'out', 1, 'Venda', 33, '2025-07-16', '2025-09-12 03:47:01', '2025-09-12 03:47:01'),
(201, 8, 2, 'out', 1, 'Venda', 34, '2025-07-17', '2025-09-12 03:48:57', '2025-09-12 03:48:57'),
(202, 15, 2, 'out', 14, 'Venda', 34, '2025-07-17', '2025-09-12 03:48:57', '2025-09-12 03:48:57'),
(203, 8, 2, 'out', 2, 'Venda', 35, '2025-07-18', '2025-09-12 03:50:20', '2025-09-12 03:50:20'),
(204, 15, 2, 'out', 205, 'Venda', 35, '2025-07-18', '2025-09-12 03:50:20', '2025-09-12 03:50:20'),
(205, 15, 2, 'out', 10, 'Venda', 36, '2025-07-19', '2025-09-12 03:51:34', '2025-09-12 03:51:34'),
(206, 15, 2, 'out', 122, 'Venda', 37, '2025-07-21', '2025-09-12 03:52:46', '2025-09-12 03:52:46'),
(207, 11, 2, 'out', 1, 'Venda', 37, '2025-07-21', '2025-09-12 03:52:46', '2025-09-12 03:52:46'),
(208, 14, 2, 'out', 10, 'Venda', 37, '2025-07-21', '2025-09-12 03:52:46', '2025-09-12 03:52:46'),
(209, 15, 2, 'out', 3, 'Venda', 38, '2025-07-22', '2025-09-12 03:53:32', '2025-09-12 03:53:32'),
(210, 8, 2, 'out', 1, 'Venda', 39, '2025-07-23', '2025-09-12 03:58:36', '2025-09-12 03:58:36'),
(211, 15, 2, 'out', 26, 'Venda', 39, '2025-07-23', '2025-09-12 03:58:36', '2025-09-12 03:58:36'),
(212, 7, 2, 'out', 3, 'Venda', 39, '2025-07-23', '2025-09-12 03:58:36', '2025-09-12 03:58:36'),
(213, 11, 2, 'out', 1, 'Venda', 39, '2025-07-23', '2025-09-12 03:58:36', '2025-09-12 03:58:36'),
(214, 8, 2, 'in', 1, 'Reversão de venda cancelada', 39, '2025-09-12', '2025-09-12 03:59:15', '2025-09-12 03:59:15'),
(215, 15, 2, 'in', 26, 'Reversão de venda cancelada', 39, '2025-09-12', '2025-09-12 03:59:15', '2025-09-12 03:59:15'),
(216, 7, 2, 'in', 3, 'Reversão de venda cancelada', 39, '2025-09-12', '2025-09-12 03:59:15', '2025-09-12 03:59:15'),
(217, 11, 2, 'in', 1, 'Reversão de venda cancelada', 39, '2025-09-12', '2025-09-12 03:59:15', '2025-09-12 03:59:15'),
(218, 8, 2, 'out', 1, 'Venda', 40, '2025-07-23', '2025-09-12 04:01:20', '2025-09-12 04:01:20'),
(219, 15, 2, 'out', 26, 'Venda', 40, '2025-07-23', '2025-09-12 04:01:20', '2025-09-12 04:01:20'),
(220, 7, 2, 'out', 3, 'Venda', 40, '2025-07-23', '2025-09-12 04:01:20', '2025-09-12 04:01:20'),
(221, 11, 2, 'out', 1, 'Venda', 40, '2025-07-23', '2025-09-12 04:01:20', '2025-09-12 04:01:20'),
(222, 15, 2, 'out', 15, 'Venda', 41, '2025-07-24', '2025-09-12 04:12:53', '2025-09-12 04:12:53'),
(223, 20, 2, 'out', 3, 'Venda', 42, '2025-07-25', '2025-09-12 04:15:38', '2025-09-12 04:15:38'),
(224, 7, 2, 'out', 3, 'Venda', 42, '2025-07-25', '2025-09-12 04:15:38', '2025-09-12 04:15:38'),
(225, 9, 2, 'out', 1, 'Venda', 43, '2025-07-28', '2025-09-12 04:23:01', '2025-09-12 04:23:01'),
(226, 15, 2, 'out', 29, 'Venda', 43, '2025-07-28', '2025-09-12 04:23:01', '2025-09-12 04:23:01'),
(227, 15, 2, 'out', 4, 'Venda', 44, '2025-07-20', '2025-09-12 04:24:18', '2025-09-12 04:24:18'),
(228, 11, 2, 'out', 1, 'Venda', 44, '2025-07-20', '2025-09-12 04:24:18', '2025-09-12 04:24:18'),
(229, 35, 2, 'out', 1, 'Venda', 45, '2025-07-31', '2025-09-12 04:26:06', '2025-09-12 04:26:06'),
(230, 15, 2, 'out', 31, 'Venda', 45, '2025-07-31', '2025-09-12 04:26:06', '2025-09-12 04:26:06'),
(231, 20, 2, 'out', 1, 'Venda', 46, '2025-08-01', '2025-09-15 11:03:33', '2025-09-15 11:03:33'),
(232, 15, 2, 'out', 9, 'Venda', 46, '2025-08-01', '2025-09-15 11:03:33', '2025-09-15 11:03:33'),
(233, 11, 2, 'out', 1, 'Venda', 46, '2025-08-01', '2025-09-15 11:03:33', '2025-09-15 11:03:33'),
(234, 14, 2, 'out', 10, 'Venda', 46, '2025-08-01', '2025-09-15 11:03:33', '2025-09-15 11:03:33'),
(235, 20, 2, 'in', 8, 'Compra de Camisetas', NULL, '2025-09-15', '2025-09-15 11:16:09', '2025-09-15 11:16:09'),
(236, 20, 2, 'out', 1, 'Venda', 46, '2025-09-15', '2025-09-15 11:41:23', '2025-09-15 11:41:23'),
(237, 15, 2, 'out', 12, 'Venda', 46, '2025-09-15', '2025-09-15 11:41:23', '2025-09-15 11:41:23'),
(238, 11, 2, 'out', 1, 'Venda', 46, '2025-09-15', '2025-09-15 11:41:23', '2025-09-15 11:41:23'),
(239, 14, 2, 'out', 10, 'Venda', 46, '2025-09-15', '2025-09-15 11:41:23', '2025-09-15 11:41:23'),
(240, 35, 2, 'out', 1, 'Venda', 47, '2025-08-02', '2025-09-15 12:47:25', '2025-09-15 12:47:25'),
(241, 20, 2, 'out', 1, 'Venda', 47, '2025-08-02', '2025-09-15 12:47:25', '2025-09-15 12:47:25'),
(242, 11, 2, 'out', 1, 'Venda', 47, '2025-08-02', '2025-09-15 12:47:25', '2025-09-15 12:47:25'),
(243, 12, 2, 'out', 1, 'Venda', 47, '2025-08-02', '2025-09-15 12:47:25', '2025-09-15 12:47:25'),
(244, 15, 2, 'in', 2500, 'Actualizacao de stock', NULL, '2025-07-31', '2025-09-15 13:20:34', '2025-09-15 13:23:01'),
(245, 20, 2, 'out', 1, 'Venda', 48, '2025-08-01', '2025-09-15 13:28:50', '2025-09-15 13:28:50'),
(246, 15, 2, 'out', 12, 'Venda', 48, '2025-08-01', '2025-09-15 13:28:50', '2025-09-15 13:28:50'),
(247, 11, 2, 'out', 1, 'Venda', 48, '2025-08-01', '2025-09-15 13:28:50', '2025-09-15 13:28:50'),
(248, 14, 2, 'out', 10, 'Venda', 48, '2025-08-01', '2025-09-15 13:28:50', '2025-09-15 13:28:50'),
(249, 15, 2, 'out', 161, 'Venda', 49, '2025-08-05', '2025-09-15 13:34:41', '2025-09-15 13:34:41'),
(250, 7, 2, 'out', 1, 'Venda', 49, '2025-08-05', '2025-09-15 13:34:41', '2025-09-15 13:34:41'),
(251, 20, 2, 'out', 2, 'Venda', 50, '2025-08-06', '2025-09-15 13:45:52', '2025-09-15 13:45:52'),
(252, 15, 2, 'out', 8, 'Venda', 50, '2025-08-06', '2025-09-15 13:45:52', '2025-09-15 13:45:52'),
(253, 20, 2, 'in', 200, 'Actualizacao de stock', NULL, '2025-09-15', '2025-09-15 13:48:18', '2025-09-15 13:48:18'),
(254, 35, 2, 'in', 200, 'Actualizacao de stock', NULL, '2025-09-15', '2025-09-15 13:49:40', '2025-09-15 13:49:40'),
(255, 11, 2, 'in', 50, 'Actualizacao de stock', NULL, '2025-09-15', '2025-09-15 13:50:26', '2025-09-15 13:50:26'),
(256, 15, 2, 'out', 2, 'Venda', 51, '2025-08-07', '2025-09-15 13:51:45', '2025-09-15 13:51:45'),
(257, 8, 2, 'out', 3, 'Venda', 52, '2025-08-08', '2025-09-15 14:01:07', '2025-09-15 14:01:07'),
(258, 15, 2, 'out', 84, 'Venda', 52, '2025-08-08', '2025-09-15 14:01:07', '2025-09-15 14:01:07'),
(259, 7, 2, 'out', 2, 'Venda', 52, '2025-08-08', '2025-09-15 14:01:07', '2025-09-15 14:01:07'),
(260, 15, 2, 'out', 18, 'Venda', 53, '2025-08-11', '2025-09-15 14:06:27', '2025-09-15 14:06:27'),
(261, 15, 2, 'out', 40, 'Venda', 54, '2025-08-12', '2025-09-15 14:13:57', '2025-09-15 14:13:57'),
(262, 15, 6, 'out', 16, 'Venda', 55, '2025-08-13', '2025-09-17 06:15:19', '2025-09-17 06:15:19'),
(263, 8, 6, 'out', 1, 'Venda', 56, '2025-08-14', '2025-09-17 06:18:11', '2025-09-17 06:18:11'),
(264, 15, 6, 'out', 11, 'Venda', 56, '2025-08-14', '2025-09-17 06:18:11', '2025-09-17 06:18:11'),
(265, 8, 6, 'out', 1, 'Venda', 57, '2025-08-15', '2025-09-17 06:31:24', '2025-09-17 06:31:24'),
(266, 15, 6, 'out', 19, 'Venda', 57, '2025-08-15', '2025-09-17 06:31:24', '2025-09-17 06:31:24'),
(267, 12, 6, 'out', 1, 'Venda', 57, '2025-08-15', '2025-09-17 06:31:24', '2025-09-17 06:31:24'),
(268, 15, 6, 'out', 25, 'Venda', 58, '2025-08-16', '2025-09-17 06:49:58', '2025-09-17 06:49:58'),
(269, 35, 6, 'out', 2, 'Venda', 59, '2025-08-18', '2025-09-17 06:58:52', '2025-09-17 06:58:52'),
(270, 20, 6, 'out', 5, 'Venda', 59, '2025-08-18', '2025-09-17 06:58:52', '2025-09-17 06:58:52'),
(271, 15, 6, 'out', 27, 'Venda', 59, '2025-08-18', '2025-09-17 06:58:52', '2025-09-17 06:58:52'),
(272, 15, 6, 'out', 164, 'Venda', 60, '2025-08-19', '2025-09-17 07:20:50', '2025-09-17 07:20:50'),
(273, 35, 6, 'out', 1, 'Venda', 61, '2025-08-20', '2025-09-17 07:27:27', '2025-09-17 07:27:27'),
(274, 20, 6, 'out', 7, 'Venda', 61, '2025-08-20', '2025-09-17 07:27:27', '2025-09-17 07:27:27'),
(275, 15, 6, 'out', 35, 'Venda', 61, '2025-08-20', '2025-09-17 07:27:27', '2025-09-17 07:27:27'),
(276, 20, 6, 'out', 2, 'Venda', 62, '2025-08-21', '2025-09-17 07:41:28', '2025-09-17 07:41:28'),
(277, 35, 6, 'out', 2, 'Venda', 63, '2025-08-21', '2025-09-17 07:46:29', '2025-09-17 07:46:29'),
(278, 8, 6, 'out', 2, 'Venda', 63, '2025-08-21', '2025-09-17 07:46:29', '2025-09-17 07:46:29'),
(279, 15, 6, 'out', 195, 'Venda', 63, '2025-08-21', '2025-09-17 07:46:29', '2025-09-17 07:46:29'),
(280, 14, 6, 'out', 15, 'Venda', 63, '2025-08-21', '2025-09-17 07:46:29', '2025-09-17 07:46:29'),
(281, 10, 6, 'out', 1, 'Venda', 63, '2025-08-21', '2025-09-17 07:46:29', '2025-09-17 07:46:29'),
(282, 15, 6, 'out', 26, 'Venda', 64, '2025-08-25', '2025-09-17 08:01:04', '2025-09-17 08:01:04'),
(283, 13, 6, 'out', 1, 'Venda', 65, '2025-08-26', '2025-09-17 08:56:30', '2025-09-17 08:56:30'),
(284, 20, 6, 'out', 2, 'Venda', 65, '2025-08-26', '2025-09-17 08:56:30', '2025-09-17 08:56:30'),
(285, 15, 6, 'out', 43, 'Venda', 65, '2025-08-26', '2025-09-17 08:56:30', '2025-09-17 08:56:30'),
(286, 7, 6, 'out', 1, 'Venda', 65, '2025-08-26', '2025-09-17 08:56:30', '2025-09-17 08:56:30'),
(287, 10, 6, 'out', 1, 'Venda', 65, '2025-08-26', '2025-09-17 08:56:30', '2025-09-17 08:56:30'),
(288, 15, 6, 'out', 60, 'Venda', 66, '2025-08-27', '2025-09-17 09:02:43', '2025-09-17 09:02:43'),
(289, 15, 6, 'out', 44, 'Venda', 67, '2025-08-28', '2025-09-17 09:08:09', '2025-09-17 09:08:09'),
(290, 35, 6, 'out', 2, 'Venda', 68, '2025-08-29', '2025-09-17 09:15:54', '2025-09-17 09:15:54'),
(291, 15, 6, 'out', 229, 'Venda', 68, '2025-08-29', '2025-09-17 09:15:54', '2025-09-17 09:15:54'),
(292, 8, 6, 'out', 4, 'Venda', 70, '2025-09-17', '2025-09-17 09:23:47', '2025-09-17 09:23:47'),
(293, 9, 6, 'out', 8, 'Venda', 70, '2025-09-17', '2025-09-17 09:23:47', '2025-09-17 09:23:47'),
(294, 15, 6, 'out', 19, 'Venda', 70, '2025-09-17', '2025-09-17 09:23:47', '2025-09-17 09:23:47'),
(295, 7, 6, 'out', 1, 'Venda', 70, '2025-09-17', '2025-09-17 09:23:47', '2025-09-17 09:23:47'),
(296, 11, 6, 'out', 3, 'Venda', 70, '2025-09-17', '2025-09-17 09:23:47', '2025-09-17 09:23:47'),
(297, 14, 6, 'out', 15, 'Venda', 70, '2025-09-17', '2025-09-17 09:23:47', '2025-09-17 09:23:47');

-- --------------------------------------------------------

--
-- Table structure for table `temporary_passwords`
--

CREATE TABLE `temporary_passwords` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `token` varchar(64) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '0',
  `used_at` timestamp NULL DEFAULT NULL,
  `created_by_user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `temporary_passwords`
--

INSERT INTO `temporary_passwords` (`id`, `user_id`, `token`, `password_hash`, `expires_at`, `used`, `used_at`, `created_by_user_id`, `created_at`, `updated_at`) VALUES
(3, 3, '2r1574Obyyli5eC1i4f7EUIePuBhNNi32N9thtGwHz28EUqihbqa5FKVlxx5EZq5', '$2y$12$ZLWftgKCNZ996yg6GPp27eX1jO.1.8H0uZXxo5SM797SqmouBIjWK', '2025-09-10 09:54:56', 1, '2025-09-09 09:55:27', 2, '2025-09-09 09:54:56', '2025-09-09 09:55:27'),
(4, 3, 'hwpDTv6JNxR8qTo6sq3u9GyHzWVY6EsOz1AJHOyU7bdqE1qPzUS3b0v78oyyvoxS', '$2y$12$3w0YZFHEnkvkJiz0rB8PguXx8lbMYXDHviZ8iCYMdFFwALsHtgk9G', '2025-09-10 10:02:50', 1, '2025-09-09 10:03:10', 1, '2025-09-09 10:02:50', '2025-09-09 10:03:10'),
(5, 3, '7dBV1qXc4DJ1xZ1um1qI63WPTl3RJEXzWXMiMVzjy4lDW7kaFyNoYiNlUsBltoFd', '$2y$12$5Wcd8p0TYEhm2ejAbjK26.Ky6emYNCKePO6g4Jm8x9FzYrGmNvtuS', '2025-09-10 10:22:07', 1, '2025-09-09 10:22:24', 2, '2025-09-09 10:22:07', '2025-09-09 10:22:24'),
(6, 3, 'zhYEbPhnJr44rcBlcSoxLhLcc2T5BCa27zSN69qtvCKUKGeD8XEfrRmGSk0pdCCL', '$2y$12$1qZwDsgij3k0fmkk2cq0I.aZ0JSZVgRx17S23xhxTzAryTxmDpyCS', '2025-09-10 10:31:44', 1, '2025-09-09 10:32:08', 2, '2025-09-09 10:31:44', '2025-09-09 10:32:08'),
(7, 3, 'QklaIyvxGSpfpPAtMrMKfT3g0nlAyZvPkMJmV6eHOjTqwRXSeIcTbeh7ocvI2iXP', '$2y$12$CjoPgUFo1Tma1kkPitdbjuzH7VsFQDGumXSTjrF8.8BRziV5e6/mm', '2025-09-10 10:49:50', 1, '2025-09-09 10:50:13', 1, '2025-09-09 10:49:50', '2025-09-09 10:50:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `role_id` int DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_login_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `photo_path`, `password`, `remember_token`, `role_id`, `is_active`, `created_at`, `updated_at`, `last_login_at`) VALUES
(1, 'Administrator', 'admin@shop.com', NULL, '$2y$12$pJPqkc70RRZiIjWZia7/5.AF0gSenyzNfecBw/.VnOfNFM2qD9kAS', 'cov8KW36f2xcDRnb7UrbQAQ4EVH4KMvDAreUnpDEs5WnrqHfcYxZABSR7oQY', 1, 1, '2025-07-11 10:05:17', '2025-10-22 07:48:53', '2025-10-22 07:48:53'),
(2, 'FDS MS', 'admin2@shop.com', NULL, '$2y$12$QJeTpCrWsQFo2F/XALpvvuf/4oi8EeOjb4BBnwJCNTqXGUjPSkhgW', 'qpXMQ2hP7ASfrgCaoW1ECyjGFXbQtDD9e8NLkwaoFp8oisAqVGJl4suVNWrU', 1, 1, '2025-07-11 08:16:51', '2025-10-22 08:19:28', '2025-10-22 08:19:28'),
(3, 'Victor Adamossene', 'victor@shop.com', 'user-photos/AMMUNFHj41x0lPI0gtM79eCv1bZNSIE42cQhwF9A.jpg', '$2y$12$HhXpb/vys.c0eM.FOy5kYeYQHd913y2jrEce7nH6cKDtOPiUMcvv.', '6TNjmYpsHZmvuxe2QtUpysMvUIueFCAyk1a2nwXVh1qUhnNgMUyh94pNoDFX', 2, 1, '2025-07-13 13:47:58', '2025-10-01 02:58:41', '2025-10-01 02:56:03'),
(4, 'Wilson', 'wilson@shop.com', 'user-photos/tpKxjNbvV3JsfAyhS95At7tZQxOb3tS9y8fo1gTr.png', '$2y$12$t9UZrp2YND80CCR9sJx0bOND3QpTdMTJuQMlq.fxsemCTSBkiA/ty', 'jRp7GLeJgiJARe2DyI6zdopWwS0soc0vpvL9Ae7CqLgOOc5Iu6L6p26WaZnv', 3, 1, '2025-07-16 00:51:03', '2025-09-11 20:56:21', '2025-09-11 14:20:11'),
(6, 'Wilson Candido W08', 'wilsondasilva830@gmail.com', NULL, '$2y$12$c8w69GbHkYe1Z/wBi3.iwusg1GR/r/7Gg/E8u5Hjxy4HjVb2g03Xy', NULL, 2, 1, '2025-09-01 18:44:09', '2025-09-17 06:11:45', '2025-09-17 06:11:45');

-- --------------------------------------------------------

--
-- Table structure for table `user_activities`
--

CREATE TABLE `user_activities` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `action` varchar(255) NOT NULL,
  `model_type` varchar(255) DEFAULT NULL,
  `model_id` int DEFAULT NULL,
  `description` text,
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_activities`
--

INSERT INTO `user_activities` (`id`, `user_id`, `action`, `model_type`, `model_id`, `description`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 2, 'password_reset', 'App\\Models\\User', 5, 'Resetou senha do usuário: Adamussene Junior', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 01:55:19', '2025-09-09 01:55:19'),
(2, 2, 'password_reset', 'App\\Models\\User', 5, 'Resetou senha do usuário: Adamussene Junior', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 01:55:52', '2025-09-09 01:55:52'),
(3, 2, 'status_change', 'App\\Models\\User', 5, 'Alterou status do usuário \'Adamussene Junior\' para inativo', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 01:56:08', '2025-09-09 01:56:08'),
(4, 2, 'status_change', 'App\\Models\\User', 5, 'Alterou status do usuário \'Adamussene Junior\' para ativo', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 01:56:11', '2025-09-09 01:56:11'),
(5, 2, 'password_reset', 'App\\Models\\User', 5, 'Resetou senha do usuário: Adamussene Junior', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 01:56:33', '2025-09-09 01:56:33'),
(6, 2, 'status_change', 'App\\Models\\User', 5, 'Alterou status do usuário \'Adamussene Junior\' para inativo', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 01:59:51', '2025-09-09 01:59:51'),
(7, 2, 'status_change', 'App\\Models\\User', 5, 'Alterou status do usuário \'Adamussene Junior\' para ativo', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 01:59:56', '2025-09-09 01:59:56'),
(8, 2, 'password_reset', 'App\\Models\\User', 5, 'Resetou senha do usuário: Adamussene Junior (Expira em 24h)', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 03:19:59', '2025-09-09 03:19:59'),
(9, 2, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 03:20:51', '2025-09-09 03:20:51'),
(10, 2, 'login', NULL, NULL, 'Usuário fez login no sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 03:22:42', '2025-09-09 03:22:42'),
(11, 2, 'password_invalidate', 'App\\Models\\User', 5, 'Invalidou 1 senha(s) temporária(s) do usuário: Adamussene Junior', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 05:59:35', '2025-09-09 05:59:35'),
(12, 2, 'password_reset', 'App\\Models\\User', 5, 'Resetou senha do usuário: Adamussene Junior (Expira em 24h)', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 05:59:57', '2025-09-09 05:59:57'),
(13, 2, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 06:00:39', '2025-09-09 06:00:39'),
(14, 4, 'login', NULL, NULL, 'Usuário fez login no sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 06:03:39', '2025-09-09 06:03:39'),
(15, 4, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 07:04:04', '2025-09-09 07:04:04'),
(16, 2, 'login', NULL, NULL, 'Usuário fez login no sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 07:04:09', '2025-09-09 07:04:09'),
(17, 2, 'update', 'App\\Models\\User', 5, 'Atualizou usuário de \'Adamussene Junior\' para \'Adamussene Junior\'', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 07:08:07', '2025-09-09 07:08:07'),
(18, 2, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 07:08:17', '2025-09-09 07:08:17'),
(20, 2, 'login', NULL, NULL, 'Usuário fez login no sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 07:15:05', '2025-09-09 07:15:05'),
(21, 2, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 07:19:17', '2025-09-09 07:19:17'),
(22, 4, 'login', NULL, NULL, 'Usuário fez login no sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 07:19:23', '2025-09-09 07:19:23'),
(23, 4, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 09:53:40', '2025-09-09 09:53:40'),
(24, 2, 'login', NULL, NULL, 'Usuário fez login no sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 09:53:54', '2025-09-09 09:53:54'),
(25, 2, 'password_reset', 'App\\Models\\User', 3, 'Resetou senha do usuário: Victor Adamossene (Expira em 24h)', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 09:54:56', '2025-09-09 09:54:56'),
(26, 2, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 09:55:15', '2025-09-09 09:55:15'),
(27, 3, 'login', NULL, NULL, 'Usuário fez login no sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 09:55:27', '2025-09-09 09:55:27'),
(28, 3, 'temp_password_used', NULL, NULL, 'Login realizado com senha temporária', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 09:55:27', '2025-09-09 09:55:27'),
(29, 3, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:01:10', '2025-09-09 10:01:10'),
(30, 1, 'login', NULL, NULL, 'Usuário fez login no sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:01:18', '2025-09-09 10:01:18'),
(31, 1, 'password_reset', 'App\\Models\\User', 3, 'Resetou senha do usuário: Victor Adamossene (Expira em 24h)', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:02:51', '2025-09-09 10:02:51'),
(32, 1, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:02:59', '2025-09-09 10:02:59'),
(33, 3, 'login', NULL, NULL, 'Usuário fez login no sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:03:10', '2025-09-09 10:03:10'),
(34, 3, 'temp_password_used', NULL, NULL, 'Login realizado com senha temporária', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:03:10', '2025-09-09 10:03:10'),
(35, 3, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:21:09', '2025-09-09 10:21:09'),
(36, 2, 'login', NULL, NULL, 'Usuário fez login no sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:21:45', '2025-09-09 10:21:45'),
(37, 2, 'password_reset', 'App\\Models\\User', 3, 'Resetou senha do usuário: Victor Adamossene (Expira em 24h)', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:22:07', '2025-09-09 10:22:07'),
(38, 2, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:22:15', '2025-09-09 10:22:15'),
(39, 3, 'login', NULL, NULL, 'Usuário fez login no sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:22:24', '2025-09-09 10:22:24'),
(40, 3, 'temp_password_used', NULL, NULL, 'Login realizado com senha temporária', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:22:24', '2025-09-09 10:22:24'),
(41, 3, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:31:25', '2025-09-09 10:31:25'),
(42, 2, 'login', NULL, NULL, 'Usuário fez login no sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:31:30', '2025-09-09 10:31:30'),
(43, 2, 'password_reset', 'App\\Models\\User', 3, 'Resetou senha do usuário: Victor Adamossene (Expira em 24h)', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:31:44', '2025-09-09 10:31:44'),
(44, 2, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:31:56', '2025-09-09 10:31:56'),
(45, 3, 'login', NULL, NULL, 'Usuário fez login no sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:32:08', '2025-09-09 10:32:08'),
(46, 3, 'temp_password_used', NULL, NULL, 'Login realizado com senha temporária', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:32:08', '2025-09-09 10:32:08'),
(47, 3, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:39:47', '2025-09-09 10:39:47'),
(48, 3, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:47:25', '2025-09-09 10:47:25'),
(49, 3, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:49:12', '2025-09-09 10:49:12'),
(50, 3, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:49:15', '2025-09-09 10:49:15'),
(51, 3, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:49:17', '2025-09-09 10:49:17'),
(52, 3, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:49:18', '2025-09-09 10:49:18'),
(53, 3, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:49:19', '2025-09-09 10:49:19'),
(54, 3, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:49:20', '2025-09-09 10:49:20'),
(55, 3, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:49:25', '2025-09-09 10:49:25'),
(56, 3, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:49:32', '2025-09-09 10:49:32'),
(57, 1, 'login', NULL, NULL, 'Usuário fez login no sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:49:36', '2025-09-09 10:49:36'),
(58, 1, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:49:36', '2025-09-09 10:49:36'),
(59, 1, 'password_reset', 'App\\Models\\User', 3, 'Resetou senha do usuário: Victor Adamossene (Expira em 24h)', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:49:50', '2025-09-09 10:49:50'),
(60, 1, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:50:02', '2025-09-09 10:50:02'),
(61, 3, 'login', NULL, NULL, 'Usuário fez login no sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:50:13', '2025-09-09 10:50:13'),
(62, 3, 'temp_password_used', NULL, NULL, 'Login realizado com senha temporária', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:50:13', '2025-09-09 10:50:13'),
(63, 3, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:50:13', '2025-09-09 10:50:13'),
(64, 3, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:51:20', '2025-09-09 10:51:20'),
(65, 1, 'login', NULL, NULL, 'Usuário fez login no sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:51:27', '2025-09-09 10:51:27'),
(66, 1, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:51:27', '2025-09-09 10:51:27'),
(67, 1, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 10:51:52', '2025-09-09 10:51:52'),
(68, 1, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 11:25:58', '2025-09-09 11:25:58'),
(69, 1, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 11:30:49', '2025-09-09 11:30:49'),
(70, 2, 'login', NULL, NULL, 'Usuário fez login no sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-09 16:33:50', '2025-09-09 16:33:50'),
(71, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-10 05:15:39', '2025-09-10 05:15:39'),
(72, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-10 11:37:21', '2025-09-10 11:37:21'),
(73, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-10 11:37:31', '2025-09-10 11:37:31'),
(74, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-10 17:04:06', '2025-09-10 17:04:06'),
(75, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 01:29:30', '2025-09-11 01:29:30'),
(76, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 13:33:13', '2025-09-11 13:33:13'),
(77, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 13:36:38', '2025-09-11 13:36:38'),
(78, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 13:38:25', '2025-09-11 13:38:25'),
(79, 2, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 14:20:01', '2025-09-11 14:20:01'),
(80, 4, 'login', NULL, NULL, 'Usuário fez login no sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 14:20:11', '2025-09-11 14:20:11'),
(81, 4, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 14:20:11', '2025-09-11 14:20:11'),
(82, 4, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 14:21:17', '2025-09-11 14:21:17'),
(83, 4, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 14:21:40', '2025-09-11 14:21:40'),
(84, 4, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 18:56:12', '2025-09-11 18:56:12'),
(85, 4, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 18:56:21', '2025-09-11 18:56:21'),
(86, 2, 'login', NULL, NULL, 'Usuário fez login no sistema', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 18:58:23', '2025-09-11 18:58:23'),
(87, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 18:58:23', '2025-09-11 18:58:23'),
(88, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 19:19:55', '2025-09-11 19:19:55'),
(89, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 19:20:33', '2025-09-11 19:20:33'),
(90, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 19:34:10', '2025-09-11 19:34:10'),
(91, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 19:41:42', '2025-09-11 19:41:42'),
(92, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 19:41:55', '2025-09-11 19:41:55'),
(93, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 19:43:04', '2025-09-11 19:43:04'),
(94, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 19:50:45', '2025-09-11 19:50:45'),
(95, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 19:57:48', '2025-09-11 19:57:48'),
(96, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 19:59:32', '2025-09-11 19:59:32'),
(97, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 19:59:50', '2025-09-11 19:59:50'),
(98, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:00:00', '2025-09-11 20:00:00'),
(99, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:00:19', '2025-09-11 20:00:19'),
(100, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:00:30', '2025-09-11 20:00:30'),
(101, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:02:00', '2025-09-11 20:02:00'),
(102, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:03:40', '2025-09-11 20:03:40'),
(103, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:07:35', '2025-09-11 20:07:35'),
(104, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:15:01', '2025-09-11 20:15:01'),
(105, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:15:37', '2025-09-11 20:15:37'),
(106, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:15:58', '2025-09-11 20:15:58'),
(107, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:16:43', '2025-09-11 20:16:43'),
(108, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:18:22', '2025-09-11 20:18:22'),
(109, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:19:10', '2025-09-11 20:19:10'),
(110, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:23:01', '2025-09-11 20:23:01'),
(111, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:24:46', '2025-09-11 20:24:46'),
(112, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:36:14', '2025-09-11 20:36:14'),
(113, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:40:16', '2025-09-11 20:40:16'),
(114, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:40:39', '2025-09-11 20:40:39'),
(115, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:42:21', '2025-09-11 20:42:21'),
(116, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:42:54', '2025-09-11 20:42:54'),
(117, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:44:36', '2025-09-11 20:44:36'),
(118, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:45:25', '2025-09-11 20:45:25'),
(119, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:49:25', '2025-09-11 20:49:25'),
(120, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:51:29', '2025-09-11 20:51:29'),
(121, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:53:30', '2025-09-11 20:53:30'),
(122, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:53:34', '2025-09-11 20:53:34'),
(123, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:54:14', '2025-09-11 20:54:14'),
(124, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:54:58', '2025-09-11 20:54:58'),
(125, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:55:51', '2025-09-11 20:55:51'),
(126, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 20:56:06', '2025-09-11 20:56:06'),
(127, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 21:07:50', '2025-09-11 21:07:50'),
(128, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 21:15:40', '2025-09-11 21:15:40'),
(129, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 21:17:37', '2025-09-11 21:17:37'),
(130, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 21:18:11', '2025-09-11 21:18:11'),
(131, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 21:18:50', '2025-09-11 21:18:50'),
(132, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 21:19:24', '2025-09-11 21:19:24'),
(133, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 21:19:52', '2025-09-11 21:19:52'),
(134, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 21:20:14', '2025-09-11 21:20:14'),
(135, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 21:20:37', '2025-09-11 21:20:37'),
(136, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-11 22:35:58', '2025-09-11 22:35:58'),
(137, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-12 04:31:13', '2025-09-12 04:31:13'),
(138, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-15 02:40:33', '2025-09-15 02:40:33'),
(139, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-15 02:41:05', '2025-09-15 02:41:05'),
(140, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-15 03:09:50', '2025-09-15 03:09:50'),
(141, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-15 03:13:05', '2025-09-15 03:13:05'),
(142, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-15 03:13:54', '2025-09-15 03:13:54'),
(143, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-15 03:14:24', '2025-09-15 03:14:24'),
(144, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-15 03:14:48', '2025-09-15 03:14:48'),
(145, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-15 03:14:54', '2025-09-15 03:14:54'),
(146, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-15 03:16:26', '2025-09-15 03:16:26'),
(147, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-15 03:43:59', '2025-09-15 03:43:59'),
(148, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-15 05:04:44', '2025-09-15 05:04:44'),
(149, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-15 05:04:45', '2025-09-15 05:04:45'),
(150, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '197.218.58.2', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-15 07:52:49', '2025-09-15 07:52:49'),
(151, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '197.218.58.2', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-15 09:17:32', '2025-09-15 09:17:32'),
(152, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 1 produtos', '197.218.58.2', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-15 11:31:52', '2025-09-15 11:31:52'),
(153, 2, 'login', NULL, NULL, 'Usuário fez login no sistema', '197.218.58.2', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-15 12:31:28', '2025-09-15 12:31:28'),
(154, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '197.218.58.2', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-15 12:31:29', '2025-09-15 12:31:29'),
(155, 2, 'low_stock_alert', NULL, NULL, 'Alerta de estoque baixo para 2 produtos', '197.218.58.2', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-15 12:33:21', '2025-09-15 12:33:21'),
(156, 6, 'login', NULL, NULL, 'Usuário fez login no sistema', '197.218.58.2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-15 14:40:18', '2025-09-15 14:40:18'),
(157, 6, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '197.218.58.2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-15 14:48:03', '2025-09-15 14:48:03'),
(158, 6, 'login', NULL, NULL, 'Usuário fez login no sistema', '41.220.200.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-17 06:01:46', '2025-09-17 06:01:46'),
(159, 6, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '41.220.200.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-17 06:02:11', '2025-09-17 06:02:11'),
(160, 1, 'login', NULL, NULL, 'Usuário fez login no sistema', '41.220.200.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-17 06:02:17', '2025-09-17 06:02:17'),
(161, 1, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '41.220.200.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-17 06:11:41', '2025-09-17 06:11:41'),
(162, 6, 'login', NULL, NULL, 'Usuário fez login no sistema', '41.220.200.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-17 06:11:45', '2025-09-17 06:11:45'),
(163, 2, 'login', NULL, NULL, 'Usuário fez login no sistema', '146.70.46.151', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-17 12:37:12', '2025-09-17 12:37:12'),
(164, 2, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '146.70.46.151', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-17 12:51:47', '2025-09-17 12:51:47'),
(165, 3, 'login', NULL, NULL, 'Usuário fez login no sistema', '146.70.46.151', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-17 12:52:30', '2025-09-17 12:52:30'),
(166, 1, 'login', NULL, NULL, 'Usuário fez login no sistema', '216.234.211.98', 'Mozilla/5.0 (X11; Linux x86_64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-09-18 07:22:47', '2025-09-18 07:22:47'),
(167, 3, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '216.234.211.221', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-24 15:55:25', '2025-09-24 15:55:25'),
(168, 2, 'login', NULL, NULL, 'Usuário fez login no sistema', '216.234.211.221', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-24 15:55:34', '2025-09-24 15:55:34'),
(169, 2, 'login', NULL, NULL, 'Usuário fez login no sistema', '197.218.58.95', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-25 04:23:46', '2025-09-25 04:23:46'),
(170, 2, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '197.218.58.95', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-25 04:26:13', '2025-09-25 04:26:13'),
(171, 1, 'login', NULL, NULL, 'Usuário fez login no sistema', '197.218.59.3', 'Mozilla/5.0 (X11; Linux x86_64; rv:128.0) Gecko/20100101 Firefox/128.0', '2025-09-25 15:54:23', '2025-09-25 15:54:23'),
(172, 2, 'login', NULL, NULL, 'Usuário fez login no sistema', '197.218.127.58', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-09-29 15:32:03', '2025-09-29 15:32:03'),
(173, 3, 'login', NULL, NULL, 'Usuário fez login no sistema', '197.218.120.61', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-10-01 02:56:03', '2025-10-01 02:56:03'),
(174, 3, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '197.218.120.61', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-10-01 02:58:41', '2025-10-01 02:58:41'),
(175, 2, 'login', NULL, NULL, 'Usuário fez login no sistema', '197.218.120.61', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-10-01 02:58:49', '2025-10-01 02:58:49'),
(176, 2, 'logout', NULL, NULL, 'Usuário fez logout do sistema', '197.218.120.61', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-10-01 03:00:32', '2025-10-01 03:00:32'),
(177, 2, 'login', NULL, NULL, 'Usuário fez login no sistema', '197.218.61.239', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '2025-10-06 12:29:24', '2025-10-06 12:29:24'),
(178, 2, 'login', NULL, NULL, 'Usuário fez login no sistema', '41.220.201.192', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-12 13:33:20', '2025-10-12 13:33:20'),
(179, 1, 'login', NULL, NULL, 'Usuário fez login no sistema', '197.218.115.157', 'Mozilla/5.0 (X11; Linux x86_64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-15 09:15:51', '2025-10-15 09:15:51'),
(180, 1, 'login', NULL, NULL, 'Usuário fez login no sistema', '197.218.43.222', 'Mozilla/5.0 (X11; Linux x86_64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-22 07:48:53', '2025-10-22 07:48:53'),
(181, 2, 'login', NULL, NULL, 'Usuário fez login no sistema', '197.218.43.222', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-22 08:19:28', '2025-10-22 08:19:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `status` (`status`),
  ADD KEY `type` (`type`),
  ADD KEY `name_2` (`name`);

--
-- Indexes for table `debts`
--
ALTER TABLE `debts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_status_due` (`status`,`due_date`),
  ADD KEY `idx_customer_name` (`customer_name`),
  ADD KEY `idx_debt_date` (`debt_date`),
  ADD KEY `debts_debt_type_status_index` (`debt_type`,`status`),
  ADD KEY `debts_employee_id_index` (`employee_id`);

--
-- Indexes for table `debt_items`
--
ALTER TABLE `debt_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `debt_id` (`debt_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `debt_payments`
--
ALTER TABLE `debt_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_debt_payment_date` (`debt_id`,`payment_date`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_expenses_category` (`expense_category_id`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notifications_user` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_status_created` (`status`,`created_at`),
  ADD KEY `idx_customer_name` (`customer_name`),
  ADD KEY `idx_delivery_date` (`delivery_date`),
  ADD KEY `idx_orders_customer_name` (`customer_name`);

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
  ADD KEY `fk_products_category` (`category_id`),
  ADD KEY `idx_products_name` (`name`);
ALTER TABLE `products` ADD FULLTEXT KEY `idx_products_name_description` (`name`,`description`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_sales_customer_name` (`customer_name`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `stock_movements_ibfk_1` (`product_id`);

--
-- Indexes for table `temporary_passwords`
--
ALTER TABLE `temporary_passwords`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_user_used` (`user_id`,`used`),
  ADD KEY `idx_expires_at` (`expires_at`),
  ADD KEY `fk_temporary_passwords_creator` (`created_by_user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_roles` (`role_id`),
  ADD KEY `idx_users_name` (`name`);

--
-- Indexes for table `user_activities`
--
ALTER TABLE `user_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_activities_user_id` (`user_id`),
  ADD KEY `idx_user_activities_action` (`action`),
  ADD KEY `idx_user_activities_model_type` (`model_type`),
  ADD KEY `idx_user_activities_created_at` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `debts`
--
ALTER TABLE `debts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `debt_items`
--
ALTER TABLE `debt_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `debt_payments`
--
ALTER TABLE `debt_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=207;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=298;

--
-- AUTO_INCREMENT for table `temporary_passwords`
--
ALTER TABLE `temporary_passwords`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_activities`
--
ALTER TABLE `user_activities`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=182;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `debts`
--
ALTER TABLE `debts`
  ADD CONSTRAINT `debts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `debts_ibfk_2` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_debts_employee_id` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_debts_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `debt_items`
--
ALTER TABLE `debt_items`
  ADD CONSTRAINT `debt_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `fk_debt_items_debt_id` FOREIGN KEY (`debt_id`) REFERENCES `debts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `debt_payments`
--
ALTER TABLE `debt_payments`
  ADD CONSTRAINT `debt_payments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_debt_payments_debt_id` FOREIGN KEY (`debt_id`) REFERENCES `debts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_expenses_category` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_movements_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `temporary_passwords`
--
ALTER TABLE `temporary_passwords`
  ADD CONSTRAINT `fk_temporary_passwords_creator` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_temporary_passwords_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_activities`
--
ALTER TABLE `user_activities`
  ADD CONSTRAINT `fk_user_activities_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
