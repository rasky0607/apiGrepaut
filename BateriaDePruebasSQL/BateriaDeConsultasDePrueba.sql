-- Tabla Usuarios
INSERT INTO `usuarios` (`id`, `email`, `password`, `nombre`, `created_at`, `updated_at`, `token`) VALUES (NULL, 'usuario1@gmail.com', '1234', 'Juan', CURRENT_DATE(), CURRENT_DATE(), 'Addhfgrua1');

-- Tabla Empresas
INSERT INTO `empresas` (`id`, `nombre`, `direccion`, `tlf`, `created_at`, `updated_at`) VALUES (NULL, 'Shaco SA', 'direc1', '638485721', CURRENT_DATE(), CURRENT_DATE());

-- Tabla Clientes
INSERT INTO `clientes` (`id`, `nombre`, `apellido`, `empresa`, `tlf`, `email`, `created_at`, `updated_at`) VALUES (NULL, 'Maria', 'Garcia', '1', '674659835', 'mMiralles@gmail.com', CURRENT_DATE(), CURRENT_DATE());
INSERT INTO `clientes` (`id`, `nombre`, `apellido`, `empresa`, `tlf`, `email`, `created_at`, `updated_at`) VALUES (NULL, 'Manolo', 'Martin', '1', '674362535', 'mMartin@mail.com', CURRENT_DATE(), CURRENT_DATE());
