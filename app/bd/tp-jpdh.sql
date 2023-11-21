-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-11-2023 a las 09:40:24
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
  `numero_cliente` varchar(6) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `baja` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comandas`
--

CREATE TABLE `comandas` (
  `numero_comanda` int(11) NOT NULL,
  `precio_total` decimal(10,0) NOT NULL,
  `numero_mesa` int(11) NOT NULL,
  `tipo_mesa` varchar(30) NOT NULL,
  `numero_cliente` varchar(6) NOT NULL,
  `nombre_cliente` varchar(30) NOT NULL,
  `cantidad_clientes` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL,
  `baja` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `numero_empleado` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `apellido` varchar(30) NOT NULL,
  `dni` int(8) NOT NULL,
  `rol` varchar(30) NOT NULL,
  `baja` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`numero_empleado`, `nombre`, `apellido`, `dni`, `rol`, `baja`) VALUES
(1, 'juan pablo', 'dongo huaman', 43445875, 'bartender', 0),
(2, 'kevin', 'robles', 43445874, 'cervezero', 0),
(3, 'santiago', 'robles', 43445876, 'cocinero', 0),
(4, 'alan', 'ornat', 43445877, 'mozo', 0),
(5, 'adrian', 'barrientos', 43445878, 'socio', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `numero_mesa` int(11) NOT NULL,
  `numero_cliente` varchar(6) NOT NULL,
  `numero_comanda` int(11) NOT NULL,
  `tipo` varchar(10) NOT NULL,
  `cantidad_clientes_maxima` int(11) NOT NULL,
  `cantidad_clientes` int(11) NOT NULL,
  `estado` varchar(40) NOT NULL,
  `baja` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos`
--

CREATE TABLE `movimientos` (
  `numero_movimiento` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `dni_empleado` int(11) NOT NULL,
  `accion` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `numero_pedido` int(11) NOT NULL,
  `numero_comanda` int(11) NOT NULL,
  `tipo` varchar(10) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `cantidad_unidades` int(11) NOT NULL,
  `precio_unidades` decimal(10,0) NOT NULL,
  `fecha_registro` datetime NOT NULL,
  `fecha_terminado` datetime NOT NULL,
  `estado` varchar(40) NOT NULL,
  `baja` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `numero_producto` int(11) NOT NULL,
  `tipo` varchar(20) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `stock` int(11) NOT NULL,
  `precio_unidades` decimal(10,0) NOT NULL,
  `baja` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
