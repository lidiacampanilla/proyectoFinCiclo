-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-06-2025 a las 20:50:37
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
-- Base de datos: `cofradia`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `operaciones`
--

CREATE TABLE `operaciones` (
  `id_ope` int(11) NOT NULL,
  `Nomb_ope` varchar(50) NOT NULL,
  `Descrip_ope` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `operaciones`
--

INSERT INTO `operaciones` (`id_ope`, `Nomb_ope`, `Descrip_ope`) VALUES
(1, 'insertar', 'insertar'),
(2, 'modificar', 'modificar'),
(3, 'borrar', 'borrar'),
(4, 'filtrar', 'filtrar');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pertenecen`
--

CREATE TABLE `pertenecen` (
  `id_usu` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pertenecen`
--

INSERT INTO `pertenecen` (`id_usu`, `id_tipo`) VALUES
(11, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `realizan`
--

CREATE TABLE `realizan` (
  `id_ope` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `realizan`
--

INSERT INTO `realizan` (`id_ope`, `id_tipo`) VALUES
(1, 1),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 5),
(3, 6),
(4, 1),
(4, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo`
--

CREATE TABLE `tipo` (
  `id_tipo` int(11) NOT NULL,
  `Nomb_tipo` varchar(50) NOT NULL,
  `Descrip_tipo` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo`
--

INSERT INTO `tipo` (`id_tipo`, `Nomb_tipo`, `Descrip_tipo`) VALUES
(1, 'administrador', 'administra'),
(2, 'junta', 'junta'),
(3, 'costalero', 'costalero'),
(4, 'nazareno', 'nazareno'),
(5, 'mantilla', 'mantilla'),
(6, 'otros', 'otros');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usu` int(11) NOT NULL,
  `DNI` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `Nomb_usu` varchar(50) NOT NULL,
  `Ape_usu` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `poblacion` varchar(100) NOT NULL,
  `cod_postal` int(11) NOT NULL,
  `provincia` varchar(100) DEFAULT NULL,
  `cta_bancaria` varchar(24) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usu`, `DNI`, `email`, `Nomb_usu`, `Ape_usu`, `password`, `direccion`, `poblacion`, `cod_postal`, `provincia`, `cta_bancaria`) VALUES
(11, '18111938j', 'lopezmartinlidia@gmail.com', 'lidia', 'lopez', '$2y$10$KpQ6ZUEp5yoV2PtVb.nX5uq.MHikpzWDN1PZnSbf9j.X4iH1s1qsK', 'Magistral Seco de Herrera', 'CORDOBA', 14005, 'CORDOBA', 'ES4574185296332145698741');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `operaciones`
--
ALTER TABLE `operaciones`
  ADD PRIMARY KEY (`id_ope`),
  ADD UNIQUE KEY `operaciones_UQ` (`Nomb_ope`);

--
-- Indices de la tabla `pertenecen`
--
ALTER TABLE `pertenecen`
  ADD PRIMARY KEY (`id_usu`,`id_tipo`),
  ADD KEY `pertenecen_FK2` (`id_tipo`);

--
-- Indices de la tabla `realizan`
--
ALTER TABLE `realizan`
  ADD PRIMARY KEY (`id_ope`,`id_tipo`),
  ADD KEY `realizan_FK2` (`id_tipo`);

--
-- Indices de la tabla `tipo`
--
ALTER TABLE `tipo`
  ADD PRIMARY KEY (`id_tipo`),
  ADD UNIQUE KEY `tipo_UQ` (`Nomb_tipo`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usu`),
  ADD UNIQUE KEY `DNI` (`DNI`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `usuario_UQ1` (`DNI`),
  ADD UNIQUE KEY `usuario_UQ2` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `operaciones`
--
ALTER TABLE `operaciones`
  MODIFY `id_ope` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tipo`
--
ALTER TABLE `tipo`
  MODIFY `id_tipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `pertenecen`
--
ALTER TABLE `pertenecen`
  ADD CONSTRAINT `pertenecen_FK1` FOREIGN KEY (`id_usu`) REFERENCES `usuario` (`id_usu`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pertenecen_FK2` FOREIGN KEY (`id_tipo`) REFERENCES `tipo` (`id_tipo`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `realizan`
--
ALTER TABLE `realizan`
  ADD CONSTRAINT `realizan_FK1` FOREIGN KEY (`id_ope`) REFERENCES `operaciones` (`id_ope`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `realizan_FK2` FOREIGN KEY (`id_tipo`) REFERENCES `tipo` (`id_tipo`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
