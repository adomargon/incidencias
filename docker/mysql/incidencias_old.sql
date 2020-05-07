-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-04-2020 a las 08:58:42
-- Versión del servidor: 10.1.37-MariaDB
-- Versión de PHP: 7.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `incidencias`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incidencias`
--

CREATE TABLE `incidencias` (
  `id` bigint(20) NOT NULL,
  `descripcion_corta` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion_larga` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` date NOT NULL,
  `aula_id` bigint(20) NOT NULL,
  `profesor_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `incidencias`
--

INSERT INTO `incidencias` (`id`, `descripcion_corta`, `descripcion_larga`, `fecha`, `aula_id`, `profesor_id`) VALUES
(1, 'Se ha roto la persiana más lejana al profesor.', NULL, '2020-04-01', 217, 25),
(2, 'No enciende el ordenador.', NULL, '2020-04-02', 217, 25),
(3, 'Se ha roto la persiana más lejana al profesor.', NULL, '2020-04-02', 207, 22),
(4, 'Hay una bombilla fundida.', NULL, '2020-04-03', 117, 15),
(5, 'Se ha roto la cerradura de la puerta de acceso a la clase.', NULL, '2020-04-03', 17, 5),
(6, 'Se ha caído la percha para la ropa de la pared.', NULL, '2020-04-03', 217, 25),
(7, 'Hay una pintada en la parte central de la pared del fondo.', NULL, '2020-04-04', 119, 18),
(8, 'Se ha roto una persiana.', NULL, '2020-04-04', 7, 9),
(9, 'No enciende el ordenador.', NULL, '2020-04-05', 117, 21),
(10, 'Se ha roto la persiana más lejana al profesor.', NULL, '2020-04-05', 208, 2),
(11, 'Hay una bombilla fundida.', NULL, '2020-04-06', 116, 11),
(12, 'Se ha roto la cerradura de la puerta de acceso a la clase.', NULL, '2020-04-06', 12, 15),
(13, 'Se ha caído la percha para la ropa de la pared.', NULL, '2020-04-06', 112, 20),
(14, 'Hay una pintada en la parte central de la pared del fondo.', NULL, '2020-04-07', 101, 16),
(15, 'Se ha roto una persiana.', NULL, '2020-04-07', 107, 19),
(16, 'No enciende el ordenador.', NULL, '2020-04-08', 17, 20),
(17, 'Se ha roto la persiana más lejana al profesor.', NULL, '2020-04-08', 108, 12),
(18, 'Hay una bombilla fundida.', NULL, '2020-04-08', 16, 13),
(19, 'Se ha roto la cerradura de la puerta de acceso a la clase.', NULL, '2020-04-09', 112, 5),
(20, 'Se ha caído la percha para la ropa de la pared.', NULL, '2020-04-09', 212, 23),
(21, 'Hay una pintada en la parte central de la pared del fondo.', NULL, '2020-04-10', 121, 6),
(22, 'Se ha roto una persiana.', NULL, '2020-04-10', 117, 9);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `incidencias`
--
ALTER TABLE `incidencias`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `incidencias`
--
ALTER TABLE `incidencias`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
