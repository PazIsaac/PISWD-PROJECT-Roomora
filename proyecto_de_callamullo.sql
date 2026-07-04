-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-07-2026 a las 20:23:46
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `proyecto de callamullo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamentos`
--

CREATE TABLE `departamentos` (
  `id` int(11) NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `ambientes` int(11) DEFAULT NULL,
  `metros_cuadrados` int(11) DEFAULT NULL,
  `disponible` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `departamentos`
--

INSERT INTO `departamentos` (`id`, `tipo`, `precio`, `ambientes`, `metros_cuadrados`, `disponible`) VALUES
(1, 'Monoambiente', 450000.00, 1, 32, 1),
(2, 'Duplex', 780000.00, 3, 65, 1),
(3, 'Departamento', 520000.00, 2, 48, 1),
(4, 'PH', 610000.00, 3, 72, 1),
(5, 'Loft', 950000.00, 2, 55, 1),
(6, 'Monoambiente', 380000.00, 1, 28, 1),
(7, 'Duplex', 490000.00, 2, 50, 0),
(8, 'Casa', 720000.00, 4, 120, 1),
(9, 'Departamento', 410000.00, 2, 45, 1),
(10, 'Monoambiente', 450000.00, 1, 32, 1),
(11, 'Duplex', 780000.00, 3, 65, 1),
(12, 'Departamento', 520000.00, 2, 48, 1),
(13, 'PH', 610000.00, 3, 72, 1),
(14, 'Loft', 950000.00, 2, 55, 1),
(15, 'Monoambiente', 380000.00, 1, 28, 1),
(16, 'Duplex', 490000.00, 2, 50, 0),
(17, 'Casa', 720000.00, 4, 120, 1),
(18, 'Departamento', 410000.00, 2, 45, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL,
  `renta_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`id`, `renta_id`, `usuario_id`, `mensaje`, `created_at`) VALUES
(1, 1, 2, 'hola', '2026-07-04 16:45:06'),
(2, 1, 1, 'hola', '2026-07-04 16:54:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rentas`
--

CREATE TABLE `rentas` (
  `id` int(11) NOT NULL,
  `departamento_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado` enum('pendiente','aprobado','rechazado','cancelado') NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rentas`
--

INSERT INTO `rentas` (`id`, `departamento_id`, `usuario_id`, `fecha_inicio`, `fecha_fin`, `estado`, `created_at`) VALUES
(1, 3, 2, '2025-02-21', '2026-12-12', 'pendiente', '2026-07-04 16:44:58'),
(2, 3, 3, '2025-02-02', '2026-09-09', 'pendiente', '2026-07-04 16:55:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('admin','cliente') NOT NULL DEFAULT 'cliente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password_hash`, `rol`, `created_at`) VALUES
(1, 'Administrador', 'admin@roomora.com', '$2y$10$T6lbri0e8yXmklXIMMt4oOBJVlIRv829PZa8xP/jTP8Bvw6oruEIK', 'admin', '2026-07-04 16:31:14'),
(2, 'asd', 'asd@123', '$2y$10$.9C9SlSD5hDiK7fWEXN0COmUVf3NdUk4nPjARVXpP/F5hooBmU8oO', 'cliente', '2026-07-04 16:31:45'),
(3, 'asd2', 'asd2@123', '$2y$10$uihgNR8I1aSA.cQLW/913eMFvATT27K0QqFX3l1G4zsufZlwEHrU.', 'cliente', '2026-07-04 16:54:57');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `renta_id` (`renta_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `rentas`
--
ALTER TABLE `rentas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `departamento_id` (`departamento_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `rentas`
--
ALTER TABLE `rentas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `mensajes_ibfk_1` FOREIGN KEY (`renta_id`) REFERENCES `rentas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mensajes_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `rentas`
--
ALTER TABLE `rentas`
  ADD CONSTRAINT `rentas_ibfk_1` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rentas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
