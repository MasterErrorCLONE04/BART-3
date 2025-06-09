USE barbershop_management;

-- Tabla de sesiones para auditoría
CREATE TABLE user_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    session_id VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    logout_time TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Tabla de comisiones
CREATE TABLE commissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    barber_id INT NOT NULL,
    appointment_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'paid') DEFAULT 'pending',
    paid_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (barber_id) REFERENCES users(id),
    FOREIGN KEY (appointment_id) REFERENCES appointments(id)
);
