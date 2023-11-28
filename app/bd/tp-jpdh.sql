-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-11-2023 a las 18:03:49
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tp-jpdh`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` varchar(6) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `fecha_alta` datetime NOT NULL,
  `fecha_modificado` datetime DEFAULT NULL,
  `baja` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comandas`
--

CREATE TABLE `comandas` (
  `id` int(11) NOT NULL,
  `id_cliente` varchar(6) NOT NULL,
  `id_mesa` int(11) NOT NULL,
  `cantidad_clientes` int(11) NOT NULL,
  `precio_total` decimal(10,0) NOT NULL,
  `fecha_alta` datetime NOT NULL,
  `fecha_modificado` datetime DEFAULT NULL,
  `baja` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int(11) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `contrasenia` varchar(100) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `apellido` varchar(30) NOT NULL,
  `dni` int(8) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `fecha_alta` datetime NOT NULL,
  `fecha_modificado` datetime DEFAULT NULL,
  `baja` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `mail`, `contrasenia`, `nombre`, `apellido`, `dni`, `id_rol`, `activo`, `fecha_alta`, `fecha_modificado`, `baja`) VALUES
(1, 'juanpablo@gmail.com', '$2y$10$/D6eq5Hj/u.d7629TA/svOFAN.s66Yz5QqVJAUSqLilbtsLAf/E1e', 'juan pablo', 'dongo huaman', 43000111, 1, 1, '2023-11-26 12:33:58', NULL, 0),
(2, 'kevin@gmail.com', '$2y$10$/HOiS9cakJZfn1sgh9qrjOb6o40tkkX5sgN/Qs3xXLcPQISBDhZ8e', 'kevin', 'robles', 43222333, 2, 1, '2023-11-26 12:33:58', NULL, 0),
(3, 'santiago@gmail.com', '$2y$10$dBmAnWEkE7U7mch68dZNSuk1ybhyK84FQ.IynY2US4CWjC7KutY5C', 'santiago', 'cespedes', 43444555, 3, 1, '2023-11-26 12:33:58', NULL, 0),
(4, 'alan@gmail.com', '$2y$10$M30IOQjXVPV74ehwBUyFBuEBA7YxJkzLTAxrXAKJfKB7LbZehk52S', 'alan', 'ornat', 43666777, 4, 1, '2023-11-26 12:33:58', NULL, 0),
(5, 'adrian@gmail.com', '$2y$10$/dw5eX1XrTPQFQG91IBfS.7MavRQnv6IzTla/EbKNBOodPBhK4I.C', 'adrian', 'barrientos', 43888999, 5, 1, '2023-11-26 12:33:58', NULL, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuestas`
--

CREATE TABLE `encuestas` (
  `id` int(11) NOT NULL,
  `id_comanda` int(11) NOT NULL,
  `id_mesa` int(11) NOT NULL,
  `puntuacion_cervezero` int(11) NOT NULL,
  `puntuacion_bartender` int(11) NOT NULL,
  `puntuacion_mozo` int(11) NOT NULL,
  `puntuacion_cocinero` int(11) NOT NULL,
  `puntuacion_restaurante` int(11) NOT NULL,
  `tipo_resenia` varchar(15) NOT NULL,
  `resenia` varchar(66) NOT NULL,
  `fecha_alta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `id` int(11) NOT NULL,
  `id_cliente` varchar(6) DEFAULT NULL,
  `id_comanda` int(11) DEFAULT NULL,
  `id_tipo_mesa` int(30) NOT NULL,
  `estado` varchar(40) NOT NULL,
  `fecha_alta` datetime NOT NULL,
  `fecha_modificado` datetime DEFAULT NULL,
  `baja` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesa_tipos`
--

CREATE TABLE `mesa_tipos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `capacidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mesa_tipos`
--

INSERT INTO `mesa_tipos` (`id`, `nombre`, `capacidad`) VALUES
(1, 'CHICA', 2),
(2, 'GRANDE', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos`
--

CREATE TABLE `movimientos` (
  `id` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `accion` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_comanda` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad_unidades` int(11) NOT NULL,
  `precio_unidades` decimal(10,0) NOT NULL,
  `duracion_estimada` int(11) NOT NULL,
  `fecha_inicio_elaboracion` datetime DEFAULT NULL,
  `fecha_fin_elaboracion` datetime DEFAULT NULL,
  `duracion_real` int(11) DEFAULT NULL,
  `estado` varchar(40) NOT NULL,
  `fecha_alta` datetime NOT NULL,
  `fecha_modificado` datetime DEFAULT NULL,
  `baja` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `id_tipo_producto` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `duracion_estimada` int(11) NOT NULL,
  `stock` int(11) NOT NULL,
  `precio_unidades` decimal(10,0) NOT NULL,
  `fecha_alta` datetime NOT NULL,
  `fecha_modificado` datetime DEFAULT NULL,
  `baja` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_tipos`
--

CREATE TABLE `producto_tipos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto_tipos`
--

INSERT INTO `producto_tipos` (`id`, `nombre`) VALUES
(1, 'BEBIDA'),
(2, 'BEBIDA-ALCOHOL'),
(3, 'COMIDA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_tipos`
--

CREATE TABLE `rol_tipos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol_tipos`
--

INSERT INTO `rol_tipos` (`id`, `nombre`) VALUES
(1, 'CERVEZERO'),
(2, 'BARTENDER'),
(3, 'COCINERO'),
(4, 'MOZO'),
(5, 'SOCIO');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `comandas`
--
ALTER TABLE `comandas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mesa_tipos`
--
ALTER TABLE `mesa_tipos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `producto_tipos`
--
ALTER TABLE `producto_tipos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rol_tipos`
--
ALTER TABLE `rol_tipos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `mesa_tipos`
--
ALTER TABLE `mesa_tipos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `producto_tipos`
--
ALTER TABLE `producto_tipos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `rol_tipos`
--
ALTER TABLE `rol_tipos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
