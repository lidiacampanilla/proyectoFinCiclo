CREATE DATABASE IF NOT EXISTS cofradia;
USE cofradia;

CREATE TABLE IF NOT EXISTS usuario(
    id_usu INT AUTO_INCREMENT NOT NULL,
    DNI VARCHAR(20) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    Nomb_usu VARCHAR(50) NOT NULL,
    Ape_usu VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    poblacion VARCHAR(100) NOT NULL,
    cod_postal INT NOT NULL,
    provincia VARCHAR(100),
    cta_bancaria VARCHAR(24),
    CONSTRAINT usuario_PK PRIMARY KEY (id_usu),
    CONSTRAINT usuario_UQ1 UNIQUE (DNI),
    CONSTRAINT usuario_UQ2 UNIQUE (email)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tipo(
    id_tipo INT AUTO_INCREMENT,
    Nomb_tipo VARCHAR (50) NOT NULL,
    Descrip_tipo VARCHAR (150),
    CONSTRAINT tipo_PK PRIMARY KEY (id_tipo),
    CONSTRAINT tipo_UQ UNIQUE(Nomb_tipo)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS operaciones(
    id_ope INT AUTO_INCREMENT,
    Nomb_ope VARCHAR (50) NOT NULL,
    Descrip_ope VARCHAR (150),
    CONSTRAINT operaciones_PK PRIMARY KEY (id_ope),
    CONSTRAINT operaciones_UQ UNIQUE(Nomb_ope)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pertenecen(
    id_usu INT NOT NULL,
    id_tipo INT NOT NULL,
    CONSTRAINT pertenecen_PK1 PRIMARY KEY (id_usu,id_tipo),
    CONSTRAINT pertenecen_FK1 FOREIGN KEY (id_usu) REFERENCES usuario (id_usu) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT pertenecen_FK2 FOREIGN KEY (id_tipo) REFERENCES tipo(id_tipo) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS realizan(
    id_ope INT NOT NULL,
    id_tipo INT NOT NULL,
    CONSTRAINT realizan_PK1 PRIMARY KEY (id_ope,id_tipo),
    CONSTRAINT realizan_FK1 FOREIGN KEY (id_ope) REFERENCES operaciones (id_ope) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT realizan_FK2 FOREIGN KEY (id_tipo) REFERENCES tipo(id_tipo) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

--
-- Volcado de datos para la tabla `operaciones`
--

INSERT INTO `operaciones` (`id_ope`, `Nomb_ope`, `Descrip_ope`) VALUES
(1, 'insertar', 'insertar'),
(2, 'modificar', 'modificar'),
(3, 'borrar', 'borrar'),
(4, 'filtrar', 'filtrar');

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


--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usu`, `DNI`, `email`, `Nomb_usu`, `Ape_usu`, `password`, `direccion`, `poblacion`, `cod_postal`, `provincia`, `cta_bancaria`) VALUES
(11, '18111938j', 'lopezmartinlidia@gmail.com', 'lidia', 'lopez', '$2y$10$KpQ6ZUEp5yoV2PtVb.nX5uq.MHikpzWDN1PZnSbf9j.X4iH1s1qsK', 'Magistral Seco de Herrera', 'CORDOBA', 14005, 'CORDOBA', 'ES4574185296332145698741');

--
-- Volcado de datos para la tabla `pertenecen`
--

INSERT INTO `pertenecen` (`id_usu`, `id_tipo`) VALUES
(11, 1);