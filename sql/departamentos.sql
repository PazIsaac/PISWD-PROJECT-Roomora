-- Base: proyecto de callamullo (importar con phpMyAdmin o mysql CLI)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `proyecto de callamullo`
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE `proyecto de callamullo`;

CREATE TABLE IF NOT EXISTS `departamentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(50) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `ambientes` int(11) DEFAULT NULL,
  `metros_cuadrados` int(11) DEFAULT NULL,
  `disponible` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `departamentos` (`tipo`, `precio`, `ambientes`, `metros_cuadrados`, `disponible`) VALUES
('Monoambiente', 450000.00, 1, 32, 1),
('Duplex', 780000.00, 3, 65, 1),
('Departamento', 520000.00, 2, 48, 1),
('PH', 610000.00, 3, 72, 1),
('Loft', 950000.00, 2, 55, 1),
('Monoambiente', 380000.00, 1, 28, 1),
('Duplex', 490000.00, 2, 50, 0),
('Casa', 720000.00, 4, 120, 1),
('Departamento', 410000.00, 2, 45, 1);

COMMIT;
