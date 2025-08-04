-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 04, 2025 at 11:31 AM
-- Server version: 8.0.42-0ubuntu0.24.04.2
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sistema_reprografia`
--

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
(6, 'Informática', 'Materiais como pen drives, mouses, teclados.', 'product', '#17a2b8', 'fas fa-print', 'active', '2025-08-03 12:57:32', '2025-08-03 11:20:07'),
(7, 'Outros Produtos', 'Itens diversos de venda geral.', 'product', '#fd7e14', 'fas fa-box-open', 'active', '2025-08-03 12:57:32', '2025-08-03 12:57:32'),
(8, 'Outros Serviços', 'Serviços variados não classificados.', 'service', '#dc3545', 'fas fa-tools', 'active', '2025-08-03 12:57:32', '2025-08-03 12:57:32');

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
(2, 2, 'Compra de Papeis A4', 100.00, '2025-06-23', NULL, NULL, '2025-07-14 08:06:44', '2025-07-14 08:06:44', 1),
(3, 2, 'Pagamento de Taxi', 20.00, '2025-06-23', NULL, NULL, '2025-07-14 08:07:18', '2025-07-14 08:07:18', 3),
(4, 2, 'compra de credilec', 200.00, '2025-06-27', NULL, NULL, '2025-07-15 16:17:24', '2025-07-15 16:17:24', 5),
(5, 2, 'lanche', 20.00, '2025-06-28', NULL, NULL, '2025-07-15 16:17:56', '2025-07-15 16:17:56', 4),
(6, 2, 'Taxi e lanche e emprestimo de 10 MT', 110.00, '2025-06-30', NULL, NULL, '2025-07-15 16:19:03', '2025-07-15 16:19:03', 8),
(7, 2, 'Compra de Vassoura e Taxi', 135.00, '2025-07-01', NULL, NULL, '2025-07-15 16:20:35', '2025-07-15 16:20:35', 9),
(8, 2, 'Lanche e Taxi', 50.00, '2025-07-02', NULL, NULL, '2025-07-15 16:21:14', '2025-07-15 16:21:14', 4),
(9, 2, 'Lanche e Taxi', 60.00, '2025-07-04', NULL, NULL, '2025-07-15 16:22:17', '2025-07-15 16:22:17', 4),
(10, 2, 'Fiscal', 20.00, '2025-07-05', NULL, NULL, '2025-07-15 16:22:46', '2025-07-15 16:22:46', 7),
(11, 2, 'credilec', 200.00, '2025-07-05', NULL, NULL, '2025-07-15 16:23:12', '2025-07-15 16:23:12', 5),
(12, 2, 'fiscal', 20.00, '2025-07-07', NULL, NULL, '2025-07-15 16:23:47', '2025-07-15 16:23:47', 7),
(13, 2, 'credilec', 200.00, '2025-07-08', NULL, NULL, '2025-07-15 16:24:32', '2025-07-15 16:24:32', 5),
(14, 2, 'Taxi', 30.00, '2025-07-08', NULL, NULL, '2025-07-15 16:24:57', '2025-07-15 16:24:57', 3),
(15, 2, 'papel', 300.00, '2025-07-08', NULL, NULL, '2025-07-15 16:25:22', '2025-07-15 16:25:22', 1),
(16, 2, 'fiscal', 20.00, '2025-07-08', NULL, NULL, '2025-07-15 16:25:45', '2025-07-15 16:25:45', 7),
(17, 2, 'lanche e taxi', 45.00, '2025-07-08', NULL, NULL, '2025-07-15 16:26:28', '2025-07-15 16:26:28', 4),
(18, 2, 'Emprestimo', 1000.00, '2025-07-09', NULL, NULL, '2025-07-15 16:27:18', '2025-07-15 16:27:18', 8),
(19, 2, 'Lanche e Taxi', 70.00, '2025-07-09', NULL, NULL, '2025-07-15 16:28:10', '2025-07-15 16:28:10', 4),
(20, 2, 'Lanche e Taxi', 130.00, '2025-07-10', NULL, NULL, '2025-07-15 16:29:15', '2025-07-15 16:29:15', 4),
(21, 2, 'internet', 10.00, '2025-07-10', NULL, NULL, '2025-07-15 16:29:30', '2025-07-15 16:29:30', 6),
(22, 2, 'lanche e mascara', 30.00, '2025-07-11', NULL, NULL, '2025-07-15 16:30:16', '2025-07-15 16:30:16', 4),
(23, 2, 'fiscal', 20.00, '2025-07-14', NULL, NULL, '2025-07-15 16:30:39', '2025-07-15 16:30:39', 7),
(24, 2, 'lanche', 50.00, '2025-07-14', NULL, NULL, '2025-07-15 16:31:01', '2025-07-15 16:31:01', 4),
(25, 2, 'taxi e lanche', 70.00, '2025-07-15', NULL, NULL, '2025-07-15 16:31:43', '2025-07-15 16:31:43', 4),
(26, 2, 'fiscal', 20.00, '2025-07-15', NULL, NULL, '2025-07-15 16:32:16', '2025-07-15 16:32:16', 7),
(27, 2, 'camisetas', 850.00, '2025-07-08', NULL, NULL, '2025-07-21 16:05:03', '2025-07-21 16:05:03', 10),
(28, 2, 'lampada', 120.00, '2025-07-15', NULL, NULL, '2025-07-21 16:51:09', '2025-07-21 16:51:09', 4),
(29, 2, 'pagamento Fiscal e lanche', 45.00, '2025-07-16', NULL, NULL, '2025-07-21 16:52:19', '2025-07-21 16:52:19', 7),
(30, 2, 'fiscal e lanche', 50.00, '2025-07-17', NULL, NULL, '2025-07-21 16:53:02', '2025-07-21 16:53:02', 7),
(31, 2, 'papel, taxi e lanche', 340.00, '2025-07-18', NULL, NULL, '2025-07-21 16:54:59', '2025-07-21 16:54:59', 4),
(32, 2, 'Fiscal e lanche', 40.00, '2025-07-19', NULL, NULL, '2025-07-21 16:56:06', '2025-07-21 16:56:06', 7),
(33, 2, 'Venil 600, lanche 30, net 50, fiscal 20, compra de papel 354', 1054.00, '2025-07-21', NULL, NULL, '2025-07-21 16:59:03', '2025-07-21 16:59:03', 10),
(34, 2, 'Compra de Camisetas', 800.00, '2025-07-03', NULL, NULL, '2025-07-21 17:04:05', '2025-08-04 09:17:51', 10);

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
(10, 'Serigrafia', '2025-08-03 14:59:14', '2025-08-03 14:59:14');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `description` text,
  `type` enum('product','service') NOT NULL,
  `purchase_price` decimal(10,2) DEFAULT '0.00',
  `selling_price` decimal(10,2) NOT NULL,
  `stock_quantity` int DEFAULT '0',
  `min_stock_level` int DEFAULT '5',
  `unit` varchar(50) DEFAULT 'unit',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `type`, `purchase_price`, `selling_price`, `stock_quantity`, `min_stock_level`, `unit`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Copia/Impressão', NULL, 'product', 1.50, 2.50, 499, 5, 'unit', 1, '2025-07-11 09:41:18', '2025-07-14 06:39:35', '2025-07-14 06:39:35'),
(3, 2, 'Estampagem de Camisetas', NULL, 'service', NULL, 150.00, 0, 5, 'unit', 1, '2025-07-11 10:11:46', '2025-07-14 06:43:04', NULL),
(4, 2, 'Foto Tipo Pass Normal', NULL, 'service', NULL, 75.00, 0, 5, 'unit', 1, '2025-07-13 13:25:46', '2025-07-13 13:25:46', NULL),
(5, 2, 'Foto Tipo Pass Urgente', NULL, 'service', NULL, 100.00, 0, 5, 'unit', 1, '2025-07-13 13:26:10', '2025-07-13 13:26:10', NULL),
(6, 1, 'Papel A4', NULL, 'product', 1.50, 2.00, 500, 100, NULL, 1, '2025-07-13 18:01:13', '2025-07-13 18:21:22', '2025-07-13 18:21:22'),
(7, 1, 'Envelope A4', 'Envelops de Papel A4', 'product', 6.00, 20.00, 39, 5, NULL, 1, '2025-07-14 05:43:56', '2025-08-04 08:07:03', NULL),
(8, 1, 'Canetas Alright', 'Canetas', 'product', 5.00, 15.00, 40, 5, NULL, 1, '2025-07-14 05:46:50', '2025-08-04 06:55:49', NULL),
(9, 1, 'Canetas Claro', NULL, 'product', 5.00, 15.00, 49, 5, NULL, 1, '2025-07-14 05:48:32', '2025-08-04 08:03:03', NULL),
(10, 1, 'Pastas Plásticas', 'Pastas Plasticas', 'product', NULL, 15.00, 359, 50, NULL, 1, '2025-07-14 06:19:32', '2025-07-21 16:18:01', NULL),
(11, 1, 'Lápis', 'Lápis a Carvão', 'product', 3.00, 10.00, 13, 5, NULL, 1, '2025-07-14 06:22:55', '2025-08-04 08:27:53', NULL),
(12, 1, 'Marcador', 'Permanente', 'product', 20.00, 50.00, 11, 5, NULL, 1, '2025-07-14 06:25:27', '2025-08-04 08:27:53', NULL),
(13, 1, 'Borachas', NULL, 'product', 5.00, 10.00, 28, 5, NULL, 1, '2025-07-14 06:34:03', '2025-07-14 07:17:06', NULL),
(14, 1, 'Papel A4', NULL, 'product', 0.60, 1.00, 478, 100, NULL, 1, '2025-07-14 06:36:57', '2025-08-04 08:22:51', NULL),
(15, 2, 'Cópia/Impressão', NULL, 'service', NULL, 2.50, 0, 0, 'unit', 1, '2025-07-14 06:39:06', '2025-07-14 06:39:06', NULL),
(16, 3, 'Digitalização', 'Por página', 'service', NULL, 15.00, 0, 0, 'unit', 1, '2025-07-14 06:47:39', '2025-07-14 06:47:39', NULL),
(17, 3, 'Encadernação', NULL, 'service', NULL, 30.00, 0, 0, 'unit', 1, '2025-07-14 06:50:08', '2025-07-14 06:50:08', NULL),
(18, 3, 'Scanner', NULL, 'service', NULL, 15.00, 0, 0, 'unit', 1, '2025-07-14 06:50:42', '2025-07-14 06:50:42', NULL),
(19, 3, 'Plastificação A4', NULL, 'service', NULL, 50.00, 0, 0, 'unit', 1, '2025-07-14 06:52:03', '2025-07-14 06:52:03', NULL),
(20, 2, 'Camisetes estampadas', NULL, 'product', NULL, 400.00, 28, 5, NULL, 1, '2025-07-14 08:40:25', '2025-08-04 08:22:51', NULL),
(21, 2, 'Camisetes estampadas', NULL, 'product', NULL, 450.00, 48, 5, NULL, 1, '2025-07-14 08:41:16', '2025-08-04 08:27:53', NULL),
(22, 1, 'Caneta Preço Antigo', NULL, 'product', NULL, 10.00, 1, 2, NULL, 1, '2025-07-15 15:28:12', '2025-07-15 15:32:40', NULL),
(23, 1, 'Envelope Preço Antigo', NULL, 'product', NULL, 10.00, 2, 2, NULL, 1, '2025-07-15 15:28:42', '2025-07-15 15:39:02', NULL),
(24, 2, 'Estampagem Gurue', NULL, 'service', NULL, 300.00, 0, 0, 'unit', 1, '2025-07-15 15:57:50', '2025-07-15 15:57:50', NULL),
(25, 2, 'Outros', NULL, 'service', NULL, 0.00, 0, 0, 'unit', 1, '2025-07-15 16:07:02', '2025-07-21 16:38:41', NULL),
(26, 2, 'Topper', NULL, 'product', NULL, 100.00, 19, 5, NULL, 1, '2025-07-21 16:12:16', '2025-07-21 16:13:51', NULL),
(27, 3, 'Estampagem com Desconto', 'Para pessoas que querem mais de 1 camiseta', 'service', NULL, 100.00, 0, 0, 'unit', 1, '2025-08-04 06:51:12', '2025-08-04 06:51:12', NULL),
(28, 3, 'Impressao de Banner', NULL, 'service', 500.00, 700.00, 0, 0, 'unit', 1, '2025-08-04 07:12:08', '2025-08-04 07:12:08', NULL);

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
  `payment_method` enum('cash','card','transfer','credit') DEFAULT 'cash',
  `notes` text,
  `sale_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `user_id`, `customer_name`, `customer_phone`, `total_amount`, `payment_method`, `notes`, `sale_date`, `created_at`, `updated_at`) VALUES
(7, 2, 'Dércio O. Prinheiro', '(86) 56215-57', 250.00, 'cash', NULL, '2025-07-14', '2025-07-14 05:47:32', '2025-07-14 05:47:32'),
(8, 2, 'Wilson', '(86) 08797-67', 100.00, 'cash', 'testes', '2025-07-14', '2025-07-14 06:59:40', '2025-07-14 06:59:40'),
(9, 2, 'n/a', NULL, 145.00, 'cash', NULL, '2025-06-23', '2025-07-14 07:58:38', '2025-07-14 07:58:38'),
(10, 2, 'n/a', NULL, 20.00, 'cash', NULL, '2025-06-23', '2025-07-14 08:04:23', '2025-07-14 08:04:23'),
(11, 2, 'n/a', NULL, 72.50, 'cash', NULL, '2025-06-24', '2025-07-14 08:29:55', '2025-07-14 08:29:55'),
(12, 2, NULL, NULL, 100.00, 'cash', NULL, '2025-07-14', '2025-07-14 08:32:58', '2025-07-14 08:32:58'),
(13, 2, 'n/a', NULL, 10.00, 'cash', NULL, '2025-06-24', '2025-07-15 15:21:34', '2025-07-15 15:21:34'),
(14, 2, 'n/a', NULL, 500.00, 'cash', NULL, '2025-06-26', '2025-07-15 15:22:23', '2025-07-15 15:22:23'),
(15, 2, 'n/a', NULL, 40.00, 'cash', NULL, '2025-06-27', '2025-07-15 15:24:19', '2025-07-15 15:24:19'),
(16, 2, 'n/a', NULL, 60.00, 'cash', 'Venda de Canetas e Envelopes com preços antigos e neste caso com déficit de 15 MT', '2025-06-24', '2025-07-15 15:32:40', '2025-07-15 15:32:40'),
(17, 2, 'n/a', NULL, 100.00, 'cash', NULL, '2025-06-27', '2025-07-15 15:34:15', '2025-07-15 15:34:15'),
(18, 2, 'n/a', NULL, 5.00, 'cash', NULL, '2025-06-28', '2025-07-15 15:35:08', '2025-07-15 15:35:08'),
(19, 2, 'n/a', NULL, 105.00, 'cash', 'Falta 15 MT de Jaime na Compra de Canetas', '2025-06-30', '2025-07-15 15:39:02', '2025-07-15 15:39:02'),
(20, 2, 'n/a', NULL, 105.00, 'cash', NULL, '2025-07-01', '2025-07-15 15:40:39', '2025-07-15 15:40:39'),
(21, 2, 'n/a', NULL, 120.00, 'cash', NULL, '2025-07-02', '2025-07-15 15:45:11', '2025-07-15 15:45:11'),
(22, 2, 'n/a', NULL, 15.00, 'cash', NULL, '2025-07-03', '2025-07-15 15:51:25', '2025-07-15 15:51:25'),
(23, 2, 'n/a', NULL, 27.50, 'cash', NULL, '2025-07-04', '2025-07-15 15:52:42', '2025-07-15 15:52:42'),
(24, 2, 'n/a', NULL, 152.50, 'cash', NULL, '2025-07-05', '2025-07-15 15:54:47', '2025-07-15 15:54:47'),
(25, 2, 'n/a', NULL, 3065.00, 'cash', NULL, '2025-07-07', '2025-07-15 15:58:55', '2025-07-15 15:58:55'),
(26, 2, 'n/a', NULL, 3877.50, 'cash', 'Na camisete bolo fofo --- registamos o va,or de 100 na foto tipo pass urgente o valor total era 500 mt', '2025-07-08', '2025-07-15 16:06:12', '2025-07-15 16:06:12'),
(27, 2, 'n/a', NULL, 302.50, 'cash', NULL, '2025-07-08', '2025-07-15 16:07:55', '2025-07-15 16:07:55'),
(28, 2, 'n/a', NULL, 175.00, 'cash', NULL, '2025-07-10', '2025-07-15 16:09:07', '2025-07-15 16:09:07'),
(29, 2, 'Cliente Avulso', NULL, 45.00, 'cash', NULL, '2025-07-11', '2025-07-15 16:10:39', '2025-08-03 12:20:56'),
(30, 2, 'n/a', NULL, 404.50, 'cash', NULL, '2025-07-14', '2025-07-15 16:13:18', '2025-07-15 16:13:18'),
(31, 2, 'n/a', NULL, 12.50, 'cash', NULL, '2025-07-15', '2025-07-15 16:15:18', '2025-07-15 16:15:18'),
(32, 2, 'n/a', NULL, 100.00, 'cash', NULL, '2025-07-15', '2025-07-21 16:13:51', '2025-07-21 16:13:51'),
(33, 2, NULL, NULL, 140.00, 'cash', NULL, '2025-07-16', '2025-07-21 16:18:01', '2025-07-21 16:18:01'),
(34, 2, NULL, NULL, 95.00, 'cash', NULL, '2025-07-17', '2025-07-21 16:20:01', '2025-07-21 16:20:01'),
(35, 2, NULL, NULL, 557.50, 'card', NULL, '2025-07-18', '2025-07-21 16:24:27', '2025-07-21 16:24:27'),
(36, 2, NULL, NULL, 25.00, 'cash', NULL, '2025-07-19', '2025-07-21 16:26:11', '2025-07-21 16:26:11'),
(37, 2, '', NULL, 325.00, 'cash', NULL, '2025-07-21', '2025-07-21 16:46:56', '2025-08-03 12:23:55'),
(38, 2, 'n/a', NULL, 3200.00, 'cash', NULL, '2025-07-03', '2025-07-21 17:02:20', '2025-07-21 17:02:20'),
(42, 2, NULL, NULL, 7.50, 'cash', NULL, '2025-07-22', '2025-08-04 06:47:57', '2025-08-04 06:47:57'),
(44, 2, NULL, NULL, 265.00, 'cash', NULL, '2025-07-23', '2025-08-04 06:55:49', '2025-08-04 06:55:49'),
(45, 2, 'n/a', NULL, 737.50, 'cash', NULL, '2025-07-24', '2025-08-04 07:28:51', '2025-08-04 07:28:51'),
(46, 2, 'n/a', NULL, 1260.00, 'cash', NULL, '2025-07-25', '2025-08-04 07:37:15', '2025-08-04 07:37:15'),
(47, 2, 'n/a', NULL, 402.50, 'cash', 'passamos a vender caneta 10 mt', '2025-07-28', '2025-08-04 08:03:03', '2025-08-04 08:03:03'),
(48, 2, 'n/a', NULL, 285.00, 'cash', NULL, '2025-07-29', '2025-08-04 08:07:03', '2025-08-04 08:07:03'),
(49, 2, 'n/a', NULL, 20.00, 'cash', NULL, '2025-07-30', '2025-08-04 08:09:33', '2025-08-04 08:09:33'),
(50, 2, 'n/a', NULL, 542.50, 'cash', 'vendemos camiseta a 430. coloquei no sistema 450 e o 20 retirei das copias', '2025-07-31', '2025-08-04 08:19:01', '2025-08-04 08:19:01'),
(51, 2, 'n/a', NULL, 440.00, 'cash', NULL, '2025-08-01', '2025-08-04 08:22:51', '2025-08-04 08:22:51'),
(52, 2, 'n/a', NULL, 860.00, 'cash', NULL, '2025-08-02', '2025-08-04 08:27:53', '2025-08-04 08:27:53');

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
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `unit_price`, `total_price`, `created_at`, `updated_at`) VALUES
(5, 7, 15, 100, 2.50, 250.00, '2025-07-14 05:47:32', '2025-07-14 05:47:32'),
(6, 8, 15, 40, 2.50, 100.00, '2025-07-14 06:59:40', '2025-07-14 06:59:40'),
(7, 9, 8, 2, 10.00, 20.00, '2025-07-14 07:58:38', '2025-07-14 07:58:38'),
(8, 9, 15, 18, 2.50, 45.00, '2025-07-14 07:58:38', '2025-07-14 07:58:38'),
(9, 9, 4, 1, 75.00, 75.00, '2025-07-14 07:58:38', '2025-07-14 07:58:38'),
(10, 9, 19, 1, 5.00, 5.00, '2025-07-14 07:58:38', '2025-07-14 07:58:38'),
(11, 10, 16, 1, 20.00, 20.00, '2025-07-14 08:04:23', '2025-07-14 08:04:23'),
(12, 11, 15, 29, 2.50, 72.50, '2025-07-14 08:29:55', '2025-07-14 08:29:55'),
(13, 12, 5, 1, 100.00, 100.00, '2025-07-14 08:32:58', '2025-07-14 08:32:58'),
(14, 13, 7, 1, 10.00, 10.00, '2025-07-15 15:21:34', '2025-07-15 15:21:34'),
(15, 14, 20, 1, 400.00, 400.00, '2025-07-15 15:22:23', '2025-07-15 15:22:23'),
(16, 14, 5, 1, 100.00, 100.00, '2025-07-15 15:22:23', '2025-07-15 15:22:23'),
(17, 15, 15, 16, 2.50, 40.00, '2025-07-15 15:24:19', '2025-07-15 15:24:19'),
(18, 16, 22, 4, 10.00, 40.00, '2025-07-15 15:32:40', '2025-07-15 15:32:40'),
(19, 16, 23, 2, 10.00, 20.00, '2025-07-15 15:32:40', '2025-07-15 15:32:40'),
(20, 17, 15, 40, 2.50, 100.00, '2025-07-15 15:34:15', '2025-07-15 15:34:15'),
(21, 18, 15, 2, 2.50, 5.00, '2025-07-15 15:35:08', '2025-07-15 15:35:08'),
(22, 19, 8, 3, 15.00, 45.00, '2025-07-15 15:39:02', '2025-07-15 15:39:02'),
(23, 19, 15, 20, 2.50, 50.00, '2025-07-15 15:39:02', '2025-07-15 15:39:02'),
(24, 19, 23, 1, 10.00, 10.00, '2025-07-15 15:39:02', '2025-07-15 15:39:02'),
(25, 20, 15, 12, 2.50, 30.00, '2025-07-15 15:40:39', '2025-07-15 15:40:39'),
(26, 20, 4, 1, 75.00, 75.00, '2025-07-15 15:40:39', '2025-07-15 15:40:39'),
(27, 21, 15, 40, 2.50, 100.00, '2025-07-15 15:45:11', '2025-07-15 15:45:11'),
(28, 21, 7, 1, 20.00, 20.00, '2025-07-15 15:45:11', '2025-07-15 15:45:11'),
(29, 22, 15, 6, 2.50, 15.00, '2025-07-15 15:51:25', '2025-07-15 15:51:25'),
(30, 23, 15, 11, 2.50, 27.50, '2025-07-15 15:52:42', '2025-07-15 15:52:42'),
(31, 24, 15, 1, 2.50, 2.50, '2025-07-15 15:54:47', '2025-07-15 15:54:47'),
(32, 24, 3, 1, 150.00, 150.00, '2025-07-15 15:54:47', '2025-07-15 15:54:47'),
(33, 25, 15, 26, 2.50, 65.00, '2025-07-15 15:58:55', '2025-07-15 15:58:55'),
(34, 25, 24, 10, 300.00, 3000.00, '2025-07-15 15:58:55', '2025-07-15 15:58:55'),
(35, 26, 20, 9, 400.00, 3600.00, '2025-07-15 16:06:12', '2025-07-15 16:06:12'),
(36, 26, 15, 71, 2.50, 177.50, '2025-07-15 16:06:12', '2025-07-15 16:06:12'),
(37, 26, 5, 1, 100.00, 100.00, '2025-07-15 16:06:12', '2025-07-15 16:06:12'),
(38, 27, 25, 1, 200.00, 200.00, '2025-07-15 16:07:55', '2025-07-15 16:07:55'),
(39, 27, 15, 1, 2.50, 2.50, '2025-07-15 16:07:55', '2025-07-15 16:07:55'),
(40, 27, 5, 1, 100.00, 100.00, '2025-07-15 16:07:55', '2025-07-15 16:07:55'),
(41, 28, 15, 22, 2.50, 55.00, '2025-07-15 16:09:07', '2025-07-15 16:09:07'),
(42, 28, 7, 1, 20.00, 20.00, '2025-07-15 16:09:07', '2025-07-15 16:09:07'),
(43, 28, 5, 1, 100.00, 100.00, '2025-07-15 16:09:07', '2025-07-15 16:09:07'),
(44, 29, 15, 12, 2.50, 30.00, '2025-07-15 16:10:39', '2025-07-15 16:10:39'),
(45, 29, 16, 1, 15.00, 15.00, '2025-07-15 16:10:39', '2025-07-15 16:10:39'),
(46, 30, 8, 1, 15.00, 15.00, '2025-07-15 16:13:18', '2025-07-15 16:13:18'),
(47, 30, 15, 141, 2.50, 352.50, '2025-07-15 16:13:18', '2025-07-15 16:13:18'),
(48, 30, 7, 1, 20.00, 20.00, '2025-07-15 16:13:18', '2025-07-15 16:13:18'),
(49, 30, 14, 2, 1.00, 2.00, '2025-07-15 16:13:18', '2025-07-15 16:13:18'),
(50, 30, 18, 1, 15.00, 15.00, '2025-07-15 16:13:18', '2025-07-15 16:13:18'),
(51, 31, 15, 5, 2.50, 12.50, '2025-07-15 16:15:18', '2025-07-15 16:15:18'),
(52, 32, 26, 1, 100.00, 100.00, '2025-07-21 16:13:51', '2025-07-21 16:13:51'),
(53, 33, 15, 50, 2.50, 125.00, '2025-07-21 16:18:01', '2025-07-21 16:18:01'),
(54, 33, 10, 1, 15.00, 15.00, '2025-07-21 16:18:01', '2025-07-21 16:18:01'),
(55, 34, 8, 1, 15.00, 15.00, '2025-07-21 16:20:01', '2025-07-21 16:20:01'),
(56, 34, 15, 14, 2.50, 35.00, '2025-07-21 16:20:01', '2025-07-21 16:20:01'),
(57, 34, 16, 2, 15.00, 30.00, '2025-07-21 16:20:01', '2025-07-21 16:20:01'),
(58, 34, 18, 1, 15.00, 15.00, '2025-07-21 16:20:01', '2025-07-21 16:20:01'),
(59, 35, 8, 2, 15.00, 30.00, '2025-07-21 16:24:27', '2025-07-21 16:24:27'),
(60, 35, 15, 205, 2.50, 512.50, '2025-07-21 16:24:27', '2025-07-21 16:24:27'),
(61, 35, 18, 1, 15.00, 15.00, '2025-07-21 16:24:27', '2025-07-21 16:24:27'),
(62, 36, 15, 10, 2.50, 25.00, '2025-07-21 16:26:11', '2025-07-21 16:26:11'),
(63, 37, 15, 122, 2.50, 305.00, '2025-07-21 16:46:56', '2025-07-21 16:46:56'),
(64, 37, 11, 1, 10.00, 10.00, '2025-07-21 16:46:56', '2025-07-21 16:46:56'),
(65, 37, 14, 10, 1.00, 10.00, '2025-07-21 16:46:56', '2025-07-21 16:46:56'),
(66, 38, 20, 8, 400.00, 3200.00, '2025-07-21 17:02:20', '2025-07-21 17:02:20'),
(70, 42, 15, 3, 2.50, 7.50, '2025-08-04 06:47:57', '2025-08-04 06:47:57'),
(73, 44, 8, 1, 15.00, 15.00, '2025-08-04 06:55:49', '2025-08-04 06:55:49'),
(74, 44, 15, 26, 2.50, 65.00, '2025-08-04 06:55:49', '2025-08-04 06:55:49'),
(75, 44, 16, 1, 15.00, 15.00, '2025-08-04 06:55:49', '2025-08-04 06:55:49'),
(76, 44, 7, 3, 20.00, 60.00, '2025-08-04 06:55:49', '2025-08-04 06:55:49'),
(77, 44, 27, 1, 100.00, 100.00, '2025-08-04 06:55:49', '2025-08-04 06:55:49'),
(78, 44, 11, 1, 10.00, 10.00, '2025-08-04 06:55:49', '2025-08-04 06:55:49'),
(79, 45, 15, 15, 2.50, 37.50, '2025-08-04 07:28:51', '2025-08-04 07:28:51'),
(80, 45, 28, 1, 700.00, 700.00, '2025-08-04 07:28:51', '2025-08-04 07:28:51'),
(81, 46, 20, 3, 400.00, 1200.00, '2025-08-04 07:37:15', '2025-08-04 07:37:15'),
(82, 46, 7, 3, 20.00, 60.00, '2025-08-04 07:37:15', '2025-08-04 07:37:15'),
(83, 47, 9, 1, 15.00, 15.00, '2025-08-04 08:03:03', '2025-08-04 08:03:03'),
(84, 47, 15, 29, 2.50, 72.50, '2025-08-04 08:03:03', '2025-08-04 08:03:03'),
(85, 47, 16, 1, 15.00, 15.00, '2025-08-04 08:03:03', '2025-08-04 08:03:03'),
(86, 47, 3, 2, 150.00, 300.00, '2025-08-04 08:03:03', '2025-08-04 08:03:03'),
(87, 48, 15, 26, 2.50, 65.00, '2025-08-04 08:07:03', '2025-08-04 08:07:03'),
(88, 48, 16, 2, 15.00, 30.00, '2025-08-04 08:07:03', '2025-08-04 08:07:03'),
(89, 48, 7, 1, 20.00, 20.00, '2025-08-04 08:07:03', '2025-08-04 08:07:03'),
(90, 48, 3, 1, 150.00, 150.00, '2025-08-04 08:07:03', '2025-08-04 08:07:03'),
(91, 48, 11, 2, 10.00, 20.00, '2025-08-04 08:07:03', '2025-08-04 08:07:03'),
(92, 49, 15, 4, 2.50, 10.00, '2025-08-04 08:09:33', '2025-08-04 08:09:33'),
(93, 49, 11, 1, 10.00, 10.00, '2025-08-04 08:09:33', '2025-08-04 08:09:33'),
(94, 50, 21, 1, 450.00, 450.00, '2025-08-04 08:19:01', '2025-08-04 08:19:01'),
(95, 50, 15, 31, 2.50, 77.50, '2025-08-04 08:19:01', '2025-08-04 08:19:01'),
(96, 50, 18, 1, 15.00, 15.00, '2025-08-04 08:19:01', '2025-08-04 08:19:01'),
(97, 51, 20, 1, 400.00, 400.00, '2025-08-04 08:22:51', '2025-08-04 08:22:51'),
(98, 51, 15, 8, 2.50, 20.00, '2025-08-04 08:22:51', '2025-08-04 08:22:51'),
(99, 51, 11, 1, 10.00, 10.00, '2025-08-04 08:22:51', '2025-08-04 08:22:51'),
(100, 51, 14, 10, 1.00, 10.00, '2025-08-04 08:22:51', '2025-08-04 08:22:51'),
(101, 52, 21, 1, 450.00, 450.00, '2025-08-04 08:27:53', '2025-08-04 08:27:53'),
(102, 52, 15, 140, 2.50, 350.00, '2025-08-04 08:27:53', '2025-08-04 08:27:53'),
(103, 52, 11, 1, 10.00, 10.00, '2025-08-04 08:27:53', '2025-08-04 08:27:53'),
(104, 52, 12, 1, 50.00, 50.00, '2025-08-04 08:27:53', '2025-08-04 08:27:53');

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
(4, 7, 2, 'in', 50, 'Estoque inicial', NULL, '2025-07-14', '2025-07-14 05:43:56', '2025-07-14 05:43:56'),
(5, 8, 2, 'in', 50, 'Estoque inicial', NULL, '2025-07-14', '2025-07-14 05:46:50', '2025-07-14 05:46:50'),
(6, 9, 2, 'in', 50, 'Estoque inicial', NULL, '2025-07-14', '2025-07-14 05:48:32', '2025-07-14 05:48:32'),
(7, 10, 2, 'in', 360, 'Estoque inicial', NULL, '2025-07-14', '2025-07-14 06:19:32', '2025-07-14 06:19:32'),
(8, 11, 2, 'in', 20, 'Estoque inicial', NULL, '2025-07-14', '2025-07-14 06:22:55', '2025-07-14 06:22:55'),
(9, 12, 2, 'in', 12, 'Estoque inicial', NULL, '2025-07-14', '2025-07-14 06:25:27', '2025-07-14 06:25:27'),
(10, 13, 2, 'in', 30, 'Estoque inicial', NULL, '2025-07-14', '2025-07-14 06:34:03', '2025-07-14 06:34:03'),
(11, 14, 2, 'in', 500, 'Estoque inicial', NULL, '2025-07-14', '2025-07-14 06:36:57', '2025-07-14 06:36:57'),
(12, 13, 2, 'out', 1, 'Venda', 5, '2025-07-14', '2025-07-14 07:16:24', '2025-07-14 07:16:24'),
(13, 13, 2, 'out', 1, 'Venda', 6, '2025-07-14', '2025-07-14 07:17:06', '2025-07-14 07:17:06'),
(14, 8, 2, 'out', 2, 'Venda', 9, '2025-06-23', '2025-07-14 07:58:38', '2025-07-14 07:58:38'),
(15, 20, 2, 'in', 50, 'Estoque inicial', NULL, '2025-07-14', '2025-07-14 08:40:25', '2025-07-14 08:40:25'),
(16, 21, 2, 'in', 50, 'Estoque inicial', NULL, '2025-07-14', '2025-07-14 08:41:16', '2025-07-14 08:41:16'),
(17, 7, 2, 'out', 1, 'Venda', 13, '2025-06-24', '2025-07-15 15:21:34', '2025-07-15 15:21:34'),
(18, 20, 2, 'out', 1, 'Venda', 14, '2025-06-26', '2025-07-15 15:22:23', '2025-07-15 15:22:23'),
(19, 22, 2, 'in', 5, 'Estoque inicial', NULL, '2025-07-15', '2025-07-15 15:28:12', '2025-07-15 15:28:12'),
(20, 23, 2, 'in', 5, 'Estoque inicial', NULL, '2025-07-15', '2025-07-15 15:28:42', '2025-07-15 15:28:42'),
(21, 22, 2, 'out', 4, 'Venda', 16, '2025-06-24', '2025-07-15 15:32:40', '2025-07-15 15:32:40'),
(22, 23, 2, 'out', 2, 'Venda', 16, '2025-06-24', '2025-07-15 15:32:40', '2025-07-15 15:32:40'),
(23, 8, 2, 'out', 3, 'Venda', 19, '2025-06-30', '2025-07-15 15:39:02', '2025-07-15 15:39:02'),
(24, 23, 2, 'out', 1, 'Venda', 19, '2025-06-30', '2025-07-15 15:39:02', '2025-07-15 15:39:02'),
(25, 7, 2, 'out', 1, 'Venda', 21, '2025-07-02', '2025-07-15 15:45:11', '2025-07-15 15:45:11'),
(26, 20, 2, 'out', 9, 'Venda', 26, '2025-07-08', '2025-07-15 16:06:12', '2025-07-15 16:06:12'),
(27, 7, 2, 'out', 1, 'Venda', 28, '2025-07-10', '2025-07-15 16:09:07', '2025-07-15 16:09:07'),
(28, 8, 2, 'out', 1, 'Venda', 30, '2025-07-14', '2025-07-15 16:13:18', '2025-07-15 16:13:18'),
(29, 7, 2, 'out', 1, 'Venda', 30, '2025-07-14', '2025-07-15 16:13:18', '2025-07-15 16:13:18'),
(30, 14, 2, 'out', 2, 'Venda', 30, '2025-07-14', '2025-07-15 16:13:18', '2025-07-15 16:13:18'),
(31, 26, 2, 'in', 20, 'Estoque inicial', NULL, '2025-07-21', '2025-07-21 16:12:16', '2025-07-21 16:12:16'),
(32, 26, 2, 'out', 1, 'Venda', 32, '2025-07-15', '2025-07-21 16:13:51', '2025-07-21 16:13:51'),
(33, 10, 2, 'out', 1, 'Venda', 33, '2025-07-16', '2025-07-21 16:18:01', '2025-07-21 16:18:01'),
(34, 8, 2, 'out', 1, 'Venda', 34, '2025-07-17', '2025-07-21 16:20:01', '2025-07-21 16:20:01'),
(35, 8, 2, 'out', 2, 'Venda', 35, '2025-07-18', '2025-07-21 16:24:27', '2025-07-21 16:24:27'),
(36, 11, 2, 'out', 1, 'Venda', 37, '2025-07-21', '2025-07-21 16:46:56', '2025-07-21 16:46:56'),
(37, 14, 2, 'out', 10, 'Venda', 37, '2025-07-21', '2025-07-21 16:46:56', '2025-07-21 16:46:56'),
(38, 20, 2, 'out', 8, 'Venda', 38, '2025-07-03', '2025-07-21 17:02:20', '2025-07-21 17:02:20'),
(39, 21, 2, 'out', 1, 'Venda', 39, '2025-08-03', '2025-08-03 12:19:02', '2025-08-03 12:19:02'),
(40, 21, 2, 'in', 1, 'Reversão de venda cancelada', 39, '2025-08-03', '2025-08-03 12:19:32', '2025-08-03 12:19:32'),
(41, 8, 2, 'out', 1, 'Venda', 40, '2025-08-03', '2025-08-03 12:20:22', '2025-08-03 12:20:22'),
(42, 8, 2, 'in', 1, 'Reversão de venda cancelada', 40, '2025-08-03', '2025-08-03 12:22:51', '2025-08-03 12:22:51'),
(43, 11, 2, 'out', 1, 'Venda', 43, '2025-07-23', '2025-08-04 06:49:42', '2025-08-04 06:49:42'),
(44, 11, 2, 'in', 1, 'Reversão de venda cancelada', 43, '2025-08-04', '2025-08-04 06:50:06', '2025-08-04 06:50:06'),
(45, 8, 2, 'out', 1, 'Venda', 44, '2025-07-23', '2025-08-04 06:55:49', '2025-08-04 06:55:49'),
(46, 7, 2, 'out', 3, 'Venda', 44, '2025-07-23', '2025-08-04 06:55:49', '2025-08-04 06:55:49'),
(47, 11, 2, 'out', 1, 'Venda', 44, '2025-07-23', '2025-08-04 06:55:49', '2025-08-04 06:55:49'),
(48, 20, 2, 'out', 3, 'Venda', 46, '2025-07-25', '2025-08-04 07:37:15', '2025-08-04 07:37:15'),
(49, 7, 2, 'out', 3, 'Venda', 46, '2025-07-25', '2025-08-04 07:37:15', '2025-08-04 07:37:15'),
(50, 9, 2, 'out', 1, 'Venda', 47, '2025-07-28', '2025-08-04 08:03:03', '2025-08-04 08:03:03'),
(51, 7, 2, 'out', 1, 'Venda', 48, '2025-07-29', '2025-08-04 08:07:03', '2025-08-04 08:07:03'),
(52, 11, 2, 'out', 2, 'Venda', 48, '2025-07-29', '2025-08-04 08:07:03', '2025-08-04 08:07:03'),
(53, 11, 2, 'out', 1, 'Venda', 49, '2025-07-30', '2025-08-04 08:09:33', '2025-08-04 08:09:33'),
(54, 21, 2, 'out', 1, 'Venda', 50, '2025-07-31', '2025-08-04 08:19:01', '2025-08-04 08:19:01'),
(55, 20, 2, 'out', 1, 'Venda', 51, '2025-08-01', '2025-08-04 08:22:51', '2025-08-04 08:22:51'),
(56, 11, 2, 'out', 1, 'Venda', 51, '2025-08-01', '2025-08-04 08:22:51', '2025-08-04 08:22:51'),
(57, 14, 2, 'out', 10, 'Venda', 51, '2025-08-01', '2025-08-04 08:22:51', '2025-08-04 08:22:51'),
(58, 21, 2, 'out', 1, 'Venda', 52, '2025-08-02', '2025-08-04 08:27:53', '2025-08-04 08:27:53'),
(59, 11, 2, 'out', 1, 'Venda', 52, '2025-08-02', '2025-08-04 08:27:53', '2025-08-04 08:27:53'),
(60, 12, 2, 'out', 1, 'Venda', 52, '2025-08-02', '2025-08-04 08:27:53', '2025-08-04 08:27:53');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `role` enum('admin','staff') DEFAULT 'staff',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `remember_token`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin@shop.com', '$2y$12$pJPqkc70RRZiIjWZia7/5.AF0gSenyzNfecBw/.VnOfNFM2qD9kAS', NULL, 'admin', 1, '2025-07-11 10:05:17', '2025-08-03 09:08:07'),
(2, 'FDS MS', 'admin2@shop.com', '$2y$12$QJeTpCrWsQFo2F/XALpvvuf/4oi8EeOjb4BBnwJCNTqXGUjPSkhgW', '9DgZNeYpLMhLbC5LemRYiUwXPIwx3Xvf9P48xCtgQ6s9bBnwyC6JJq9toap9', 'admin', 1, '2025-07-11 08:16:51', '2025-07-15 09:36:20'),
(3, 'Victor Adamossene', 'victor@shop.com', '$2y$12$o1ez67j1oB.yia8P6NQW7OeNc2Y4mmVltt.prFe53nvS/1i05UQLK', 'Np6ror0c4LI38zAtAgQo9BdfURPMXqkuBgGAqQdW9duImTW4ebaeuZ57bE3r', 'staff', 1, '2025-07-13 13:47:58', '2025-07-16 02:54:00'),
(4, 'Wilson', 'wilson@shop.com', '$2y$12$t9UZrp2YND80CCR9sJx0bOND3QpTdMTJuQMlq.fxsemCTSBkiA/ty', NULL, 'staff', 1, '2025-07-16 00:51:03', '2025-07-16 00:51:03');

--
-- Indexes for dumped tables
--

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
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_products_category` (`category_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_expenses_category` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`) ON DELETE SET NULL;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
