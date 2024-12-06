-- CREATE TABLE `usuarios` 
-- ( 
-- `id` int(11) NOT NULL AUTO_INCREMENT, 
-- `nombre` varchar(250) COLLATE utf8_unicode_ci NOT NULL, 
-- `apellido` varchar(250) COLLATE utf8_unicode_ci NOT NULL, 
-- `mail` varchar(250) COLLATE utf8_unicode_ci NOT NULL, 
-- `clave` varchar(250) COLLATE utf8_unicode_ci NOT NULL, 
-- `puesto` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
-- `estado` varchar(250) COLLATE utf8_unicode_ci NOT NULL, 
-- `fechaInicio` date NOT NULL, 
-- `fechaBaja` date DEFAULT NULL, 
-- PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `mail`, `clave`, `puesto`, `estado`, `fechaInicio`, `fechaBaja`) VALUES
-- (1, 'Jack', 'Shepard', 'jshepard@gmail.com', '$2y$10$e', 'Socio', 'Activo', '2024-11-29', NULL),
-- (2, 'Katherine', 'Austen', 'kausten@gmail.com', '$2y$10$u', 'Bartender', 'Activo', '2024-11-29', NULL),
-- (3, 'Hugo', 'Reyes', 'hreyes@gmail.com', '123456', 'Cocinero', 'Activo', '2024-11-29', NULL),
-- (4, 'Charlie', 'Pace', 'cpace@gmail.com', '$2y$10$R', 'Mozo', 'Activo', '2024-11-29', NULL),
-- (5, 'Ezequiel', 'Martinez', 'emartinez@gmail.com', '$2y$10$o', 'Mozo', 'Activo', '2024-11-29', NULL),
-- (6, 'James', 'Ford', 'jford@gmail.com', '$2y$10$2', 'Cervecero', 'Activo', '2024-11-29', NULL);


-- CREATE TABLE `productos` 
-- ( 
-- `id` int(11) NOT NULL AUTO_INCREMENT, 
-- `descripcion` varchar(250) COLLATE utf8_unicode_ci NOT NULL,  
-- `precio` int(11) NOT NULL, 
-- `sector` varchar(250) COLLATE utf8_unicode_ci NOT NULL, 
-- PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- INSERT INTO `productos` (`id`, `descripcion`, `precio`, `sector`) VALUES
-- (1, 'Milanesa a Caballo', 5000, 'Cocinero'),
-- (2, 'Hamburguesa de Garbanzo', 8000, 'Cocinero'),
-- (3, 'Corona', 1500, 'Cervecero'),
-- (4, 'Daikiri', 3000, 'Bartender');


CREATE TABLE `pedidos` 
( 
`id` int(11) NOT NULL AUTO_INCREMENT, 
`nombreCliente` varchar(250) COLLATE utf8_unicode_ci NOT NULL, 
`fotoCliente` varchar(250) COLLATE utf8_unicode_ci NOT NULL, 
`codigoPedido` varchar(250) COLLATE utf8_unicode_ci NOT NULL, 
`estado` varchar(250) COLLATE utf8_unicode_ci NOT NULL,  -- PENDIENTE, EN PREPARACION, LISTO PARA SERVIR
`horaInicio` datetime NOT NULL,  
`horaFinal` datetime DEFAULT NULL, 
`horaEstimadaFinal` datetime DEFAULT NULL, 

PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `productosPedidos` 
( 
`id` int(11) NOT NULL AUTO_INCREMENT,
`idProducto` int(11) NOT NULL, 
`idPedido` int(11) NOT NULL, 
`idUsuario` int(11) NOT NULL,  
`estado` varchar(250) COLLATE utf8_unicode_ci NOT NULL,  -- PENDIENTE, EN PREPARACION, LISTO PARA SERVIR

PRIMARY KEY (`id`), 
FOREIGN KEY (`idProducto`) REFERENCES `productos`(`id`), 
FOREIGN KEY (`idUsuario`) REFERENCES `usuarios`(`id`),
FOREIGN KEY (`idPedido`) REFERENCES `pedidos`(`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `mesas` 
( 
`id` int(11) NOT NULL AUTO_INCREMENT, 
`idPedido` int(11) DEFAULT NULL, 
`codigoMesa` varchar(250) COLLATE utf8_unicode_ci NOT NULL, 
`idMozoAsignado` int(11), 
`estado` varchar(250) COLLATE utf8_unicode_ci NOT NULL,  

PRIMARY KEY (`id`), FOREIGN KEY (`idPedido`) REFERENCES `pedidos`(`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `encuestas` 
( 
`id` int(11) NOT NULL AUTO_INCREMENT, 
`codigoMesa` int(11) NOT NULL, 
`codigoPedido` int(11) NOT NULL, 
`puntosMesa` int(11), 
`puntosRestaurante` int(11), 
`puntosMozo` int(11), 
`puntosCocinero` int(11), 
`comentario` varchar(66) COLLATE utf8_unicode_ci NOT NULL,  
`fecha` date NOT NULL,  


PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

