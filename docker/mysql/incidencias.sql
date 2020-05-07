-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-05-2020 a las 16:38:58
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
-- Estructura de tabla para la tabla `aulas`
--

CREATE TABLE `aulas` (
  `id` bigint(20) NOT NULL,
  `descripcion_corta` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion_larga` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `aulas`
--

INSERT INTO `aulas` (`id`, `descripcion_corta`, `descripcion_larga`, `capacidad`) VALUES
(1, '007', 'Aula 007 situada en la planta baja frente a la cantina.', 10),
(2, 'Biblioteca', 'Aula biblioteca situada en la planta baja detrás de la conserjería.', 60),
(3, '219', 'Aula 219 situada en la planta segunda al final del pasillo.', 32),
(4, '220', 'Aula 220 situada en la planta segunda al final del pasillo.', 24);

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
(1, 'Se ha roto la persiana más lejana al profesor.', NULL, '2020-04-01', 4, 5),
(2, 'No enciende el ordenador.', NULL, '2020-04-02', 4, 5),
(3, 'Se ha roto la persiana más lejana al profesor.', NULL, '2020-04-02', 4, 2),
(4, 'Hay una bombilla fundida.', NULL, '2020-04-03', 3, 2),
(5, 'Se ha roto la cerradura de la puerta de acceso a la clase.', NULL, '2020-04-03', 1, 3),
(6, 'Se ha caído la percha para la ropa de la pared.', NULL, '2020-04-03', 4, 5),
(7, 'Hay una pintada en la parte central de la pared del fondo.', NULL, '2020-04-04', 3, 2),
(8, 'Se ha roto una persiana.', NULL, '2020-04-04', 1, 2),
(9, 'No enciende el ordenador.', NULL, '2020-04-05', 3, 2),
(10, 'Se ha roto la persiana más lejana al profesor.', NULL, '2020-04-05', 4, 3),
(11, 'Hay una bombilla fundida.', NULL, '2020-04-06', 3, 2),
(12, 'Se ha roto la cerradura de la puerta de acceso a la clase.', NULL, '2020-04-06', 1, 2),
(13, 'Se ha caído la percha para la ropa de la pared.', NULL, '2020-04-06', 3, 2),
(14, 'Hay una pintada en la parte central de la pared del fondo.', NULL, '2020-04-07', 3, 2),
(15, 'Se ha roto una persiana.', NULL, '2020-04-07', 3, 2),
(16, 'No enciende el ordenador.', NULL, '2020-04-08', 1, 2),
(17, 'Se ha roto la persiana más lejana al profesor.', NULL, '2020-04-08', 3, 2),
(18, 'Hay una bombilla fundida.', NULL, '2020-04-08', 1, 2),
(19, 'Se ha roto la cerradura de la puerta de acceso a la clase.', NULL, '2020-04-09', 3, 3),
(20, 'Se ha caído la percha para la ropa de la pared.', NULL, '2020-04-09', 4, 2),
(21, 'Hay una pintada en la parte central de la pared del fondo.', NULL, '2020-04-10', 3, 3),
(22, 'Se ha roto una persiana.', NULL, '2020-04-10', 3, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesores`
--

CREATE TABLE `profesores` (
  `id` bigint(20) NOT NULL,
  `nombre` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `profesores`
--

INSERT INTO `profesores` (`id`, `nombre`, `apellidos`) VALUES
(1, 'Pepe', 'Jiménez Castillo'),
(2, 'María', 'Sánchez Esposito'),
(3, 'Laura', 'Periago Jiménez'),
(4, 'Antonio', 'Péraz Sandobal'),
(5, 'Javier', 'Carvajal Nevado'),
(6, 'Marisol', 'Suárez Clemente');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `aulas`
--
ALTER TABLE `aulas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `incidencias`
--
ALTER TABLE `incidencias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aula_id` (`aula_id`),
  ADD KEY `profesor_id` (`profesor_id`);

--
-- Indices de la tabla `profesores`
--
ALTER TABLE `profesores`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `aulas`
--
ALTER TABLE `aulas`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `incidencias`
--
ALTER TABLE `incidencias`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `profesores`
--
ALTER TABLE `profesores`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `incidencias`
--
ALTER TABLE `incidencias`
  ADD CONSTRAINT `incidencias_ibfk_1` FOREIGN KEY (`aula_id`) REFERENCES `aulas` (`id`),
  ADD CONSTRAINT `incidencias_ibfk_2` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
