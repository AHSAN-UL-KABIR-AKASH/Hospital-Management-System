-- ============================================================
-- Cancer Care - Database Schema
-- Database: cancer_care
-- Import this file through phpMyAdmin or the mysql CLI.
-- ============================================================

CREATE DATABASE IF NOT EXISTS cancer_care CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cancer_care;

-- ------------------------------------------------------------
-- Table: users
-- ------------------------------------------------------------
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(30) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('donor','fundraiser','admin') NOT NULL DEFAULT 'donor',
    profile_photo VARCHAR(255) DEFAULT NULL,
    status ENUM('active','suspended') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_users_email (email),
    INDEX idx_users_role (role)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table: campaigns
-- ------------------------------------------------------------
CREATE TABLE campaigns (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    patient_name VARCHAR(150) NOT NULL,
    patient_photo VARCHAR(255) DEFAULT NULL,
    age INT UNSIGNED DEFAULT NULL,
    cancer_type VARCHAR(100) NOT NULL,
    cancer_stage VARCHAR(50) DEFAULT NULL,
    hospital VARCHAR(150) NOT NULL,
    treatment_details TEXT,
    target_amount DECIMAL(12,2) NOT NULL,
    raised_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    story TEXT,
    contact_information VARCHAR(255) DEFAULT NULL,
    verification_status ENUM('pending','verified','rejected','suspended') NOT NULL DEFAULT 'pending',
    campaign_status ENUM('active','fully_funded','closed') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_campaigns_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_campaigns_status (verification_status, campaign_status),
    INDEX idx_campaigns_type (cancer_type),
    INDEX idx_campaigns_hospital (hospital),
    FULLTEXT INDEX ft_campaigns_search (patient_name, cancer_type, hospital)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table: medical_documents
-- ------------------------------------------------------------
CREATE TABLE medical_documents (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campaign_id INT UNSIGNED NOT NULL,
    document_name VARCHAR(255) NOT NULL,
    document_path VARCHAR(255) NOT NULL,
    document_type VARCHAR(50) DEFAULT NULL,
    verification_status ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending',
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_docs_campaign FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
    INDEX idx_docs_campaign (campaign_id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table: donations
-- ------------------------------------------------------------
CREATE TABLE donations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campaign_id INT UNSIGNED NOT NULL,
    donor_id INT UNSIGNED NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    anonymous TINYINT(1) NOT NULL DEFAULT 0,
    message VARCHAR(500) DEFAULT NULL,
    payment_method VARCHAR(50) NOT NULL DEFAULT 'demo',
    transaction_id VARCHAR(100) NOT NULL UNIQUE,
    payment_status ENUM('pending','completed','failed') NOT NULL DEFAULT 'completed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_donations_campaign FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
    CONSTRAINT fk_donations_donor FOREIGN KEY (donor_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_donations_campaign (campaign_id),
    INDEX idx_donations_donor (donor_id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table: saved_campaigns
-- ------------------------------------------------------------
CREATE TABLE saved_campaigns (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    campaign_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_saved_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_saved_campaign FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_user_campaign (user_id, campaign_id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table: reports
-- ------------------------------------------------------------
CREATE TABLE reports (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campaign_id INT UNSIGNED NOT NULL,
    reported_by INT UNSIGNED NOT NULL,
    reason VARCHAR(150) NOT NULL,
    description TEXT,
    status ENUM('open','reviewed','dismissed') NOT NULL DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reports_campaign FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
    CONSTRAINT fk_reports_user FOREIGN KEY (reported_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- SAMPLE / DEMO DATA
-- Plain-text password for ALL demo accounts below is:  Passw0rd!
-- ============================================================

INSERT INTO users (name, email, phone, password, role, status) VALUES
('Site Admin', 'admin@cancercare.test', '01700000000', '$2y$10$/OkxXPGJcD7Qk8e/3KNLy.EYbwfgfyrbPGI7aHcsHmcO0Cy23p73e', 'admin', 'active'),
('Nusrat Jahan', 'donor@cancercare.test', '01711111111', '$2y$10$/OkxXPGJcD7Qk8e/3KNLy.EYbwfgfyrbPGI7aHcsHmcO0Cy23p73e', 'donor', 'active'),
('Karim Uddin', 'fundraiser@cancercare.test', '01722222222', '$2y$10$/OkxXPGJcD7Qk8e/3KNLy.EYbwfgfyrbPGI7aHcsHmcO0Cy23p73e', 'fundraiser', 'active');

INSERT INTO campaigns (user_id, patient_name, patient_photo, age, cancer_type, cancer_stage, hospital, treatment_details, target_amount, raised_amount, story, contact_information, verification_status, campaign_status) VALUES
(3, 'Rahim Ahmed', 'default-patient.jpg', 34, 'Blood Cancer', 'Stage 2', 'XYZ Hospital, Dhaka', 'Chemotherapy sessions followed by bone marrow transplant evaluation.', 500000.00, 325000.00, 'Rahim is a school teacher and sole earner for his family of four. He was diagnosed with leukemia earlier this year and urgently needs continued chemotherapy.', '01722222222', 'verified', 'active'),
(3, 'Fatema Begum', 'default-patient.jpg', 52, 'Breast Cancer', 'Stage 3', 'National Cancer Institute, Dhaka', 'Surgery followed by radiotherapy.', 350000.00, 350000.00, 'Fatema is a mother of three who was recently diagnosed with breast cancer. Her family has already spent their savings on diagnosis.', '01722222222', 'verified', 'fully_funded'),
(3, 'Anisur Rahman', 'default-patient.jpg', 45, 'Lung Cancer', 'Stage 1', 'Square Hospital, Dhaka', 'Targeted therapy and regular monitoring.', 600000.00, 120000.00, 'Anisur is a rickshaw driver diagnosed early. Early treatment gives him a strong chance of recovery.', '01722222222', 'verified', 'active'),
(3, 'Shirin Akter', 'default-patient.jpg', 29, 'Ovarian Cancer', 'Stage 2', 'Dhaka Medical College Hospital', 'Surgery and chemotherapy.', 450000.00, 40000.00, 'Shirin is a young mother recently diagnosed. Her family needs urgent help to start treatment.', '01722222222', 'pending', 'active');

INSERT INTO donations (campaign_id, donor_id, amount, anonymous, message, payment_method, transaction_id, payment_status) VALUES
(1, 2, 5000.00, 0, 'Get well soon, Rahim!', 'demo', 'TXN-DEMO-0001', 'completed'),
(1, 2, 10000.00, 1, 'Praying for a full recovery.', 'demo', 'TXN-DEMO-0002', 'completed'),
(2, 2, 15000.00, 0, 'Stay strong.', 'demo', 'TXN-DEMO-0003', 'completed');
