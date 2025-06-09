USE barbershop_management;

-- Tabla de servicios
CREATE TABLE services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    duration_minutes INT NOT NULL,
    commission_percentage DECIMAL(5,2) DEFAULT 50.00,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertar servicios por defecto
INSERT INTO services (name, description, price, duration_minutes, commission_percentage) VALUES 
('Corte de Cabello', 'Corte de cabello profesional', 25.00, 30, 50.00),
('Barba', 'Arreglo y diseño de barba', 15.00, 20, 50.00),
('Corte + Barba', 'Servicio completo de corte y barba', 35.00, 45, 50.00),
('Lavado', 'Lavado de cabello', 10.00, 15, 40.00);
