CREATE TABLE `usuarios` 
( 
`id` int(11) NOT NULL AUTO_INCREMENT, 
`nombre` varchar(250) COLLATE utf8_unicode_ci NOT NULL, 
`apellido` varchar(250) COLLATE utf8_unicode_ci NOT NULL, 
`mail` varchar(250) COLLATE utf8_unicode_ci NOT NULL, 
`clave` varchar(250) COLLATE utf8_unicode_ci NOT NULL, 
`puesto` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
`estado` varchar(250) COLLATE utf8_unicode_ci NOT NULL, 
`fechaInicio` date NOT NULL, 
`fechaBaja` date DEFAULT NULL, 
PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `productos` 
( 
`id` int(11) NOT NULL AUTO_INCREMENT, 
`descripcion` varchar(250) COLLATE utf8_unicode_ci NOT NULL,  
`precio` int(11) NOT NULL, 
`tipo` varchar(250) COLLATE utf8_unicode_ci NOT NULL, 
`tiempoPreparacion` int(11) NOT NULL, 
PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `pedidos` 
( 
`id` int(11) NOT NULL AUTO_INCREMENT, 
`codigoPedido` varchar(250) COLLATE utf8_unicode_ci NOT NULL, 
`tiempoEstimado` int(11) NOT NULL, 
`nombreCliente` varchar(250) COLLATE utf8_unicode_ci NOT NULL, 
`fotoCliente` varchar(250) COLLATE utf8_unicode_ci NOT NULL, 
`estado` varchar(250) COLLATE utf8_unicode_ci NOT NULL,  
`fechaAlta` date NOT NULL,  
`fechaBaja` date DEFAULT NULL, 

PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `productosPedidos` 
( 
`id` int(11) NOT NULL AUTO_INCREMENT,
`idProducto` int(11) NOT NULL, 
`idPedido` int(11) NOT NULL, 
`idUsuario` int(11) NOT NULL,  

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
`idMesa` int(11) NOT NULL, 
`idPedido` int(11) NOT NULL, 
`puntosMesa` int(11), 
`puntosRestaurante` int(11), 
`puntosMozo` int(11), 
`puntosCocinero` int(11), 
`comentario` varchar(66) COLLATE utf8_unicode_ci NOT NULL,  
`fecha` date NOT NULL,  


PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

