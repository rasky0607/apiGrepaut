
-- Tabla Empresas
INSERT INTO `empresas` (`id`, `nombre`, `direccion`, `tlf`, `created_at`, `updated_at`) VALUES (NULL, 'Shaco SA', 'direc1', '638485721', CURRENT_DATE(), CURRENT_DATE());
INSERT INTO `empresas` (`id`, `nombre`, `direccion`, `tlf`, `created_at`, `updated_at`) VALUES (NULL, 'Mango S A', 'direc2', '637253474', CURRENT_DATE(), CURRENT_DATE());

-- Tabla Usuarios
INSERT INTO `usuarios` (`id`, `email`, `password`, `nombre`, `created_at`, `updated_at`, `token`,`idempresa`) VALUES (NULL, 'usuario1@gmail.com', '1234', 'Juan', CURRENT_DATE(), CURRENT_DATE(), 'Addhfgrua1',1);
INSERT INTO `usuarios` (`id`, `email`, `password`, `nombre`, `created_at`, `updated_at`, `token`,`idempresa`) VALUES (NULL, 'usuario2@gmail.com', '1234', 'Pedro', CURRENT_DATE(), CURRENT_DATE(), '7d3hGgruaG',1);
INSERT INTO `usuarios` (`id`, `email`, `password`, `nombre`, `created_at`, `updated_at`, `token`,`idempresa`) VALUES (NULL, 'usuario3@gmail.com', '1234', 'Carlos', CURRENT_DATE(), CURRENT_DATE(), 'BZl1f2ru3p',2);

-- Tabla Clientes
INSERT INTO `clientes` (`id`, `nombre`, `apellido`, `empresa`, `tlf`, `email`, `created_at`, `updated_at`) VALUES (NULL, 'Maria', 'Garcia', '1', '674659835', 'mMiralles@gmail.com', CURRENT_DATE(), CURRENT_DATE());
INSERT INTO `clientes` (`id`, `nombre`, `apellido`, `empresa`, `tlf`, `email`, `created_at`, `updated_at`) VALUES (NULL, 'Manolo', 'Martin', '1', '674362535', 'mMartin@mail.com', CURRENT_DATE(), CURRENT_DATE());
INSERT INTO `clientes` (`id`, `nombre`, `apellido`, `empresa`, `tlf`, `email`, `created_at`, `updated_at`) VALUES (NULL, 'Manolo', 'Martin', '2', '674362535', 'mMartin@mail.com', CURRENT_DATE(), CURRENT_DATE());
-- Tabla Coches
INSERT INTO `coches` (`id`, `matricula`, `idcliente`, `modelo`, `marca`, `created_at`, `updated_at`) VALUES (NULL, 'hfgd7R', '1', 'Megan', 'Renaul', CURRENT_DATE(), CURRENT_DATE()), (NULL, 'T893J', '2', 'Benz', 'Mercedes', CURRENT_DATE(), CURRENT_DATE()), (NULL, '874TY', '1', 'Corola', 'Toyota', CURRENT_DATE(), CURRENT_DATE());
INSERT INTO `coches` (`id`, `matricula`, `idcliente`, `modelo`, `marca`, `created_at`, `updated_at`) VALUES (NULL, 'hfgd7R', '1', 'Megan', 'Renaul', CURRENT_DATE(), CURRENT_DATE()), (NULL, 'T893J', '2', 'Benz', 'Mercedes', CURRENT_DATE(), CURRENT_DATE());
INSERT INTO `coches` (`id`, `matricula`, `idcliente`, `modelo`, `marca`, `created_at`, `updated_at`) VALUES (NULL, '7636LB', '3', 'Impresa', 'Subaru', CURRENT_DATE(), CURRENT_DATE());

-- Tabla Servicios
INSERT INTO `servicios` (`id`, `nombre`, `empresa`, `precio`, `descripcion`, `created_at`, `updated_at`) VALUES (NULL, 'Cambio de capo', '1', '97.21', 'descrip1', CURRENT_DATE(), CURRENT_DATE()), (NULL, 'Retrovisor', '1', '25.34', 'desc2', CURRENT_DATE(), CURRENT_DATE());
INSERT INTO `servicios` (`id`, `nombre`, `empresa`, `precio`, `descripcion`, `created_at`, `updated_at`) VALUES (NULL, 'Cambio de aceite', '1', '18,47', 'descri3', CURRENT_DATE(), CURRENT_DATE());

-- Tabla UsuariosEmpresas En DESUSO
-- INSERT INTO `usuariosempresas` (`usuario`, `empresa`, `tipoUsuario`, `permisoEscritura`, `created_at`, `updated_at`) VALUES ('1', '1', 'admin', '1', CURRENT_DATE(), CURRENT_DATE()), ('2', '1', 'user', '0', CURRENT_DATE(), CURRENT_DATE()), ('2', '2', 'admin', '1', CURRENT_DATE(), CURRENT_DATE()), ('3', '2', 'admin', '1', CURRENT_DATE(), CURRENT_DATE());

-- Tabla Reparaciones
INSERT INTO `reparaciones` (`id`, `estadoReparacion`, `idusuario`, `idcoche`, `created_at`, `updated_at`) VALUES 
(NULL, 'facturado', '1','1', CURRENT_DATE(), CURRENT_DATE()), 
(NULL, 'facturado', '1','1', CURRENT_DATE(), CURRENT_DATE()), 
(NULL, 'facturado', '2','2', CURRENT_DATE(), CURRENT_DATE());
INSERT INTO `reparaciones` (`id`, `estadoReparacion`, `idusuario`,`idcoche`, `created_at`, `updated_at`) VALUES (NULL, 'no facturado', '2','5', CURRENT_DATE(), CURRENT_DATE());
INSERT INTO `reparaciones` (`id`, `estadoReparacion`, `idusuario`,`idcoche`, `created_at`, `updated_at`) VALUES (NULL, 'facturado', '2','6', CURRENT_DATE(), CURRENT_DATE());

-- Tabla ServiciosReparaciones
INSERT INTO `serviciosreparaciones` (`idreparacion`, `numerotrabajo`, `servicio`,`precioServicio`, `created_at`, `updated_at`) VALUES (1, 1, '1','97.21', CURRENT_DATE(), CURRENT_DATE());
INSERT INTO `serviciosreparaciones` (`idreparacion`, `numerotrabajo`, `servicio`,`precioServicio`, `created_at`, `updated_at`) VALUES (1, 2, '2','18.47', CURRENT_DATE(), CURRENT_DATE());
INSERT INTO `serviciosreparaciones` (`idreparacion`, `numerotrabajo`, `servicio`,`precioServicio`, `created_at`, `updated_at`) VALUES (2, 1, '1','97.21', CURRENT_DATE(), CURRENT_DATE());
INSERT INTO `serviciosreparaciones` (`idreparacion`, `numerotrabajo`, `servicio`,`precioServicio`, `created_at`, `updated_at`) VALUES (2, 2, '2','18.47', CURRENT_DATE(), CURRENT_DATE());

-- Tabla Facturas
INSERT INTO `facturas` (`numerofactura`, `idreparacion`,`idusuario`, `fecha`, `estado`, `numerofacturanulada`, `idreparacionanulada`, `created_at`, `updated_at`) VALUES ('1', '1','1', CURRENT_DATE(), 'vigente', NULL, NULL, NULL, NULL),
 ('2', '2','1', CURRENT_DATE(), 'vigente', NULL, NULL, NULL, NULL);
INSERT INTO `facturas` (`numerofactura`, `idreparacion`, `idusuario`,`fecha`, `estado`, `numerofacturanulada`, `idreparacionanulada`, `created_at`, `updated_at`) VALUES ('3', '3','2', CURRENT_DATE(), 'vigente', NULL, NULL, NULL, NULL);
INSERT INTO `facturas` (`numerofactura`, `idreparacion`, `idusuario`,`fecha`, `estado`, `numerofacturanulada`, `idreparacionanulada`, `created_at`, `updated_at`) VALUES ('1', '5','2', CURRENT_DATE(), 'vigente', NULL, NULL, NULL, NULL);
