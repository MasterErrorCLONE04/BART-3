-- Crear base de datos
CREATE DATABASE IF NOT EXISTS barbershop_management;
USE barbershop_management;

-- Tabla de roles
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar roles por defecto
INSERT INTO roles (name, description) VALUES 
('admin', 'Administrador del sistema'),
('barbero', 'Barbero profesional'),
('cliente', 'Cliente de la barbería');
