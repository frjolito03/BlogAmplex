-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-02-2017 a las 21:55:44
-- Versión del servidor: 10.1.16-MariaDB
-- Versión de PHP: 5.5.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cupon`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `nombre` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fecha` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id`, `nombre`, `status`, `fecha`) VALUES
(1, 'equipos', 'activo', '2016-10-31 04:27:00'),
(2, 'tecnologia', 'activo', '2016-10-31 04:26:00'),
(3, 'ventas', 'activo', '2016-10-31 04:27:00'),
(4, 'mercadeo', 'activo', '2016-10-31 04:26:00'),
(5, 'outsorcing', 'activo', '2016-10-31 04:27:00'),
(6, 'servicios', 'activo', '2016-10-31 04:28:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria_post`
--

CREATE TABLE `categoria_post` (
  `categoria_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `comment` varchar(143) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `imagen` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `comment`
--

INSERT INTO `comment` (`id`, `post_id`, `comment`, `status`, `date`, `imagen`, `email`) VALUES
(1, 3, 'sahdgjhasgjdhgjajshgdjd', 'false', '2017-01-23 16:46:14', '', 'sosacarlos@gmail.com'),
(2, 3, 'dsfdsfsdfsdfsdf', 'false', '2017-01-24 20:31:57', '', 'igorsosa200@gmail.com'),
(4, 1, 'sahdgjhasgjdhgjajshgdjd', 'false', '2017-01-23 16:46:14', '', 'sosacarlos@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensaje`
--

CREATE TABLE `mensaje` (
  `id` int(11) NOT NULL,
  `texto` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fecha` datetime NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(13) COLLATE utf8_unicode_ci NOT NULL,
  `imagen` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `asunto` varchar(500) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `mensaje`
--

INSERT INTO `mensaje` (`id`, `texto`, `fecha`, `email`, `nombre`, `status`, `imagen`, `asunto`) VALUES
(1, 'hola prueba', '2017-01-19 00:00:00', 'carlossosa200@gmail.com', 'igorsosa200@gmail.com', 'false', '', ''),
(17, 'prueba 1', '2017-01-19 19:36:25', 'carlossosa200@gmail.com', 'carlossosa200@gmail.com', 'false', '', 'hola'),
(18, 'qweqweqweqwewqe', '2017-01-23 15:28:18', 'qweqweqweqwe@gamial.com', 'qweqwewqewqe', 'true', '', ''),
(19, 'prueba3', '2017-01-23 15:43:43', 'carlossosa200@gmail.com', 'igorsosa@gmail.com', 'false', '', ''),
(20, 'asdasdasdasdsadsad', '2017-01-25 21:00:22', 'igorsosa200@gmail.com', 'carlossosa200@gmail.com', 'true', '', 'hola'),
(21, 'prueba2', '2017-01-25 21:01:59', 'igorsosa200@gmail.com', 'carlossosa200@gmail.com', 'true', '', 'hola'),
(22, 'prueba4', '2017-01-25 21:04:10', 'carlossosa200@gmail.com', 'carlossosa200@gmail.com', 'true', '', 'prueba4'),
(23, 'aaaaaaaaaaaaaaaaaaaaa', '2017-01-27 15:38:31', 'carlossosa200@gmail.com', 'carlossosa200@gmail.com', 'true', '', 'prueba5'),
(24, 'jojojoojojojoj', '2017-01-27 16:20:10', 'igorsosa200@gmail.com', 'carlossosa200@gmail.com', 'true', '', 'prueba6');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `contenido` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fecha_publicacion` datetime NOT NULL,
  `imagen` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `titulo` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `seccion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `post`
--

INSERT INTO `post` (`id`, `contenido`, `fecha_publicacion`, `imagen`, `titulo`, `user_id`, `seccion`) VALUES
(1, '<p>sdfdsfdswwwwwwwwwwwwwaaaaaaaaaaawwwwwsssssssssfd</p>', '2017-01-05 00:00:00', '1485269552.png', 'prueba aaaaaaaaaaaaaaaaagssssssdddddddddddddddsssssssss', 4, 4),
(3, 'asdasdasdasd', '2017-01-23 16:21:30', '1485184890.png', 'mariara es bella', 2, 2),
(5, 'asdasdsadasdsadasdasd', '2017-01-23 16:18:48', '1485184728.png', 'mariara es bella', 3, 3),
(7, 'prueba7 kjnsdjfbjkdbjfbdsjffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffjbdddddddddd', '2017-01-27 19:03:32', '1485540212.png', 'prueba7', 2, 3),
(8, 'asdasdasdasd', '2017-01-23 16:21:30', '1485184890.png', 'mariara es bella', 2, 3),
(10, 'asdasdsadasdsadasdasd', '2017-01-23 16:18:48', '1485184728.png', 'mariara es bella', 3, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users1`
--

CREATE TABLE `users1` (
  `id` int(11) NOT NULL,
  `userid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `redDate` datetime NOT NULL,
  `role` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `users1`
--

INSERT INTO `users1` (`id`, `userid`, `username`, `password`, `redDate`, `role`, `email`) VALUES
(2, 'frijolito01', 'carlos sosa', '123456', '2016-11-12 04:00:00', 'superadmin', 'carlossosa200@gmail.com'),
(3, 'anonimo', 'anonimo', '1234', '2017-01-18 00:00:00', 'inhabilitar', 'anonimo@gmail.com'),
(4, 'Elsoda', 'igor sosa', '123456', '2016-11-12 04:00:00', 'inhabilitar', 'igorsosa200@gmail.com'),
(5, 'bello', 'pedro perez', '12345', '2017-01-24 00:00:00', 'admin', 'amplexcorp@gmail.com');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `categoria_post`
--
ALTER TABLE `categoria_post`
  ADD PRIMARY KEY (`categoria_id`,`post_id`),
  ADD KEY `IDX_B632A9CC3397707A` (`categoria_id`),
  ADD KEY `IDX_B632A9CC4B89032C` (`post_id`);

--
-- Indices de la tabla `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_9474526C4B89032C` (`post_id`);

--
-- Indices de la tabla `mensaje`
--
ALTER TABLE `mensaje`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5A8A6C8DA76ED395` (`user_id`);

--
-- Indices de la tabla `users1`
--
ALTER TABLE `users1`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_5A1E36CEF132696E` (`userid`),
  ADD UNIQUE KEY `UNIQ_5A1E36CEE7927C74` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT de la tabla `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `mensaje`
--
ALTER TABLE `mensaje`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT de la tabla `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT de la tabla `users1`
--
ALTER TABLE `users1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `categoria_post`
--
ALTER TABLE `categoria_post`
  ADD CONSTRAINT `FK_B632A9CC3397707A` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_B632A9CC4B89032C` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `FK_9474526C4B89032C` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`);

--
-- Filtros para la tabla `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `FK_5A8A6C8DA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users1` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
