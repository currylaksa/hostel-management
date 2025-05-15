-- Hostel Management System - Billing Tables
-- Create tables for billing, payments, and invoices

-- Table for hostel room rates
CREATE TABLE IF NOT EXISTS room_rates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_type VARCHAR(50) NOT NULL,
    block VARCHAR(10) NOT NULL,
    price_per_semester DECIMAL(10,2) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for student bills
CREATE TABLE IF NOT EXISTS bills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    room_id INT,
    semester VARCHAR(20) NOT NULL,
    academic_year VARCHAR(9) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    due_date DATE NOT NULL,
    status ENUM('unpaid', 'partially_paid', 'paid', 'overdue') DEFAULT 'unpaid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Table for payments
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bill_id INT NOT NULL,
    student_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('credit_card', 'bank_transfer', 'cash', 'other') NOT NULL,
    reference_number VARCHAR(50),
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'completed',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bill_id) REFERENCES bills(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Table for invoices
CREATE TABLE IF NOT EXISTS invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(20) UNIQUE NOT NULL,
    payment_id INT NOT NULL,
    student_id INT NOT NULL,
    generated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Table for refunds
CREATE TABLE IF NOT EXISTS refunds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payment_id INT NOT NULL,
    student_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'processed') DEFAULT 'pending',
    requested_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_date TIMESTAMP NULL,
    notes TEXT,
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Insert sample room rates data
INSERT INTO room_rates (room_type, block, price_per_semester, description) VALUES
('Single Room', 'Block A', 2500.00, 'Single room with private bathroom for local male students'),
('Twin Sharing', 'Block A', 1800.00, 'Twin sharing room with shared bathroom for local male students'),
('Triple Room', 'Block A', 1500.00, 'Triple sharing room with shared bathroom for local male students'),
('Single Room', 'Block B', 2500.00, 'Single room with private bathroom for local female students'),
('Twin Sharing', 'Block B', 1800.00, 'Twin sharing room with shared bathroom for local female students'),
('Triple Room', 'Block B', 1500.00, 'Triple sharing room with shared bathroom for local female students'),
('Single Room', 'Block C', 3000.00, 'Single room with private bathroom for international male students'),
('Twin Sharing', 'Block C', 2200.00, 'Twin sharing room with shared bathroom for international male students'),
('Single Room', 'Block D', 3000.00, 'Single room with private bathroom for international female students'),
('Twin Sharing', 'Block D', 2200.00, 'Twin sharing room with shared bathroom for international female students');