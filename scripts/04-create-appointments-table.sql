USE barbershop_management;

-- Tabla de citas
CREATE TABLE appointments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_id INT NOT NULL,
    barber_id INT NOT NULL,
    service_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('scheduled', 'completed', 'cancelled', 'no_show') DEFAULT 'scheduled',
    notes TEXT,
    total_amount DECIMAL(10,2) NOT NULL,
    commission_amount DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id),
    FOREIGN KEY (barber_id) REFERENCES users(id),
    FOREIGN KEY (service_id) REFERENCES services(id)
);
