-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.4.3 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para mi_base
CREATE DATABASE IF NOT EXISTS `mi_base` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `mi_base`;

-- Volcando estructura para tabla mi_base.admins
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `creado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla mi_base.admins: ~2 rows (aproximadamente)
INSERT INTO `admins` (`id`, `usuario`, `password`, `creado`) VALUES
	(5, 'Ezequiel', '$2y$12$x4Lq.0AKlcIi.eB7FwDQfu5psobHXM9W2dgV8B7iFSoQucAw/zwyK', '2025-09-21 21:28:16'),
	(7, 'nuevo_admin', '$2y$12$9tV3BbIfPRSgcq/38XZjkOWiHEbMXF79xqIuGSUDiJEu5xGfaWT9e', '2025-09-21 21:47:58');

-- Volcando estructura para tabla mi_base.carrito
CREATE TABLE IF NOT EXISTS `carrito` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `producto_id` int NOT NULL,
  `cantidad` int NOT NULL DEFAULT '1',
  `creado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `carrito_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla mi_base.carrito: ~2 rows (aproximadamente)
INSERT INTO `carrito` (`id`, `usuario_id`, `producto_id`, `cantidad`, `creado`) VALUES
	(27, 4, 4, 1, '2025-09-26 19:35:04'),
	(28, 4, 5, 1, '2025-09-26 19:35:10');

-- Volcando estructura para tabla mi_base.categorias
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla mi_base.categorias: ~5 rows (aproximadamente)
INSERT INTO `categorias` (`id`, `nombre`) VALUES
	(1, 'Electrónica'),
	(2, 'Ropa'),
	(3, 'Hogar'),
	(4, 'Libros'),
	(5, 'Accesorios');

-- Volcando estructura para tabla mi_base.ordenes
CREATE TABLE IF NOT EXISTS `ordenes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `ordenes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla mi_base.ordenes: ~18 rows (aproximadamente)
INSERT INTO `ordenes` (`id`, `usuario_id`, `fecha`, `total`) VALUES
	(1, 4, '2025-09-21 07:56:50', 84.49),
	(2, 4, '2025-09-21 08:16:20', 218.97),
	(3, 4, '2025-09-21 08:16:25', 218.97),
	(4, 4, '2025-09-21 16:30:13', 84.49),
	(5, 4, '2025-09-21 16:32:37', 84.49),
	(6, 4, '2025-09-21 16:33:01', 49.99),
	(7, 4, '2025-09-21 16:41:17', 84.49),
	(8, 4, '2025-09-21 16:50:03', 99.98),
	(9, 4, '2025-09-21 16:51:02', 49.99),
	(10, 4, '2025-09-21 17:31:53', 99.98),
	(11, 4, '2025-09-21 17:42:14', 149.97),
	(12, 4, '2025-09-21 17:57:51', 249.95),
	(13, 4, '2025-09-21 21:37:36', 449.85),
	(14, 4, '2025-09-21 22:58:25', 99.98),
	(15, 5, '2025-09-21 23:32:46', 104.98),
	(16, 4, '2025-09-23 01:30:05', 249.95),
	(17, 4, '2025-09-23 01:30:27', 99.98),
	(18, 4, '2025-09-23 02:57:44', 39.98);

-- Volcando estructura para tabla mi_base.orden_detalle
CREATE TABLE IF NOT EXISTS `orden_detalle` (
  `id` int NOT NULL AUTO_INCREMENT,
  `orden_id` int NOT NULL,
  `producto_id` int NOT NULL,
  `cantidad` int NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `orden_id` (`orden_id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `orden_detalle_ibfk_1` FOREIGN KEY (`orden_id`) REFERENCES `ordenes` (`id`),
  CONSTRAINT `orden_detalle_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla mi_base.orden_detalle: ~17 rows (aproximadamente)
INSERT INTO `orden_detalle` (`id`, `orden_id`, `producto_id`, `cantidad`, `precio`) VALUES
	(1, 5, 2, 1, 49.99),
	(2, 5, 3, 1, 34.50),
	(3, 6, 2, 1, 49.99),
	(4, 7, 2, 1, 49.99),
	(5, 7, 3, 1, 34.50),
	(6, 8, 2, 2, 49.99),
	(7, 9, 2, 1, 49.99),
	(8, 10, 2, 2, 49.99),
	(9, 11, 2, 3, 49.99),
	(10, 12, 2, 5, 49.99),
	(11, 13, 11, 15, 29.99),
	(12, 14, 2, 2, 49.99),
	(13, 15, 13, 1, 54.99),
	(14, 15, 2, 1, 49.99),
	(15, 16, 2, 5, 49.99),
	(16, 17, 2, 2, 49.99),
	(17, 18, 4, 2, 19.99);

-- Volcando estructura para tabla mi_base.productos
CREATE TABLE IF NOT EXISTS `productos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `imagen` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `creado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `categoria_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_categoria` (`categoria_id`),
  CONSTRAINT `fk_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla mi_base.productos: ~13 rows (aproximadamente)
INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `stock`, `imagen`, `creado`, `categoria_id`) VALUES
	(2, 'Figura Coleccionable', 'Figura de acción edición limitada', 49.99, 0, 'img/figura1.webp', '2025-09-21 05:58:14', NULL),
	(3, 'Mochila Escolar', 'Mochila resistente con diseño de anime', 34.50, 35, 'img/mochila1.webp', '2025-09-21 05:58:14', NULL),
	(4, 'Remera Anime', 'Remera de algodón con estampado de personaje popular', 19.99, 43, 'img/remera1.jpg', '2025-09-21 18:05:02', NULL),
	(5, 'Taza Gamer', 'Taza de cerámica con diseño gamer en alta definición', 14.99, 60, 'img/taza2.png', '2025-09-21 18:05:02', NULL),
	(6, 'Poster Anime A3', 'Poster tamaño A3 en papel satinado', 8.99, 80, 'img/poster2.jpg', '2025-09-21 18:05:02', NULL),
	(7, 'Llaveros Acrílicos', 'Set de 3 llaveros acrílicos de colección', 9.50, 100, 'img/llavero1.jpg', '2025-09-21 18:05:02', NULL),
	(8, 'Sudadera Oversize', 'Sudadera unisex con capucha y diseño exclusivo', 39.99, 30, 'img/sudadera1.jpg', '2025-09-21 18:05:02', NULL),
	(9, 'Mousepad XL', 'Mousepad gamer de gran tamaño con diseño RGB', 24.99, 25, 'img/mousepad1.jpg', '2025-09-21 18:05:02', NULL),
	(10, 'Cuaderno Coleccionable', 'Cuaderno con tapa dura y hojas rayadas', 11.99, 70, 'img/cuaderno1.webp', '2025-09-21 18:05:02', NULL),
	(11, 'Figura Chibi', 'Figura estilo chibi en PVC de edición limitada', 29.99, 0, 'img/figura2.webp', '2025-09-21 18:05:02', NULL),
	(12, 'Gorra Anime', 'Gorra ajustable con bordado de personaje', 16.50, 40, 'img/gorra1.webp', '2025-09-21 18:05:02', NULL),
	(13, 'Mochila Gamer', 'Mochila resistente con compartimentos para laptop', 54.99, 19, 'img/mochila2.jpg', '2025-09-21 18:05:02', NULL),
	(14, 'oppay', 'tetas', 100.00, 12, 'img/68d0785bdbb8e.png', '2025-09-21 22:12:43', NULL);

-- Volcando estructura para tabla mi_base.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `creado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla mi_base.usuarios: ~7 rows (aproximadamente)
INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `creado`) VALUES
	(4, 'ezequiel', 'ezequielbranchessi@gmail.com', '$2y$10$blN8BUOVUSBya5Icr3UJ0utr5NwegtDqZVPZzMAXbi0LmHVg5Y5Ga', '2025-09-21 07:38:37'),
	(5, 'Eze', 'lol@gmail.com', '$2y$10$MbdWj6E237txbQVPB4nx/umjBNjhIZNLMLk3o2ksEVTdciAxuzsMu', '2025-09-21 23:30:58'),
	(6, 'Hola', 'Almeidabrian249@gmail.com', '$2y$10$WSmXQ5QuWvc.NNZwxHxJf.fqsBTFEH7eSxM58ToxLNSORakovkSFK', '2025-09-23 22:10:46'),
	(7, 'hola', 'hola@gmail.com', '$2y$10$asRj9rIqk9as2F6GSPQ0JuvQ5jYJ7SzYOl2u8qQWeh019SM5nskj2', '2025-09-25 03:12:56'),
	(8, 'Gg', 'g@g', '$2y$10$86ix7gL4WGLDVVGhLepyQ.mnm8IhdZXSk0aoT3pz3soGQe995Vdgq', '2025-09-25 04:47:04'),
	(9, 'branchessi', 'branchessi@gmail.com', '$2y$10$Nd.Wy9UUsJzYE3BaiTCKtODbjiWT2AVLergND5fxPtIvLHjF3lahO', '2025-09-25 17:39:00'),
	(10, 'jaja', 'jaja@jaja', '$2y$10$gw5vaTr.xoPRTguMi8SHr.jI.URNhMFVlPkkrbGXD5b549HPGp5.y', '2025-09-25 19:05:58'),
	(11, 'ezequiel', 'ezequiel22@gmail.com', '$2y$10$EjpGHmfTVkSvPPaF5LDDcus9lCKivdJAIz6U3nExrpzazB7Eu0xQy', '2025-09-25 19:59:12'),
	(12, 'ff', 'f@f', '$2y$10$M3l9vx2u1RiN89q5cTnmh.d2JGlPGn1EyLQBmvTMIfXCkmjgoJNLC', '2025-09-25 20:47:35'),
	(13, 'Tt', 'g@gh', '$2y$10$iwVG5RJxFe6/WL99Hpjrj.U/fhfxJb9vqicHr1gjal1bv7uge3PRK', '2025-09-26 18:37:00');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
