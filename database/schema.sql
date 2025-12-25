-- ============================================
-- SUPERVISED DRIVING EXPERIENCE DATABASE SCHEMA
-- ============================================

-- Drop tables if they exist (for clean installation)
DROP TABLE IF EXISTS experience_road_type;
DROP TABLE IF EXISTS driving_experience;
DROP TABLE IF EXISTS weather;
DROP TABLE IF EXISTS traffic;
DROP TABLE IF EXISTS supervisor;
DROP TABLE IF EXISTS road_type;

-- Weather conditions table
CREATE TABLE weather (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(50) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Traffic conditions table
CREATE TABLE traffic (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(50) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Supervisor table
CREATE TABLE supervisor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Road type table
CREATE TABLE road_type (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(50) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Main driving experience table
CREATE TABLE driving_experience (
    id INT AUTO_INCREMENT PRIMARY KEY,
    drive_datetime DATETIME NOT NULL,
    km DECIMAL(6,2) NOT NULL,
    notes TEXT NULL,
    weather_id INT NOT NULL,
    traffic_id INT NOT NULL,
    supervisor_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (weather_id) REFERENCES weather(id) ON DELETE RESTRICT,
    FOREIGN KEY (traffic_id) REFERENCES traffic(id) ON DELETE RESTRICT,
    FOREIGN KEY (supervisor_id) REFERENCES supervisor(id) ON DELETE RESTRICT,
    INDEX idx_drive_datetime (drive_datetime),
    INDEX idx_weather (weather_id),
    INDEX idx_traffic (traffic_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Many-to-many relationship: driving_experience <-> road_type
CREATE TABLE experience_road_type (
    experience_id INT NOT NULL,
    road_type_id INT NOT NULL,
    PRIMARY KEY (experience_id, road_type_id),
    FOREIGN KEY (experience_id) REFERENCES driving_experience(id) ON DELETE CASCADE,
    FOREIGN KEY (road_type_id) REFERENCES road_type(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ERD EXPLANATION
-- ============================================
-- 
-- ENTITIES:
-- 1. driving_experience (main entity) - stores each driving session
-- 2. weather - lookup table for weather conditions
-- 3. traffic - lookup table for traffic conditions
-- 4. supervisor - stores supervisor information
-- 5. road_type - lookup table for different road types
-- 6. experience_road_type - junction table for many-to-many
--
-- RELATIONSHIPS:
-- - driving_experience -> weather (many-to-one)
-- - driving_experience -> traffic (many-to-one)
-- - driving_experience -> supervisor (many-to-one)
-- - driving_experience <-> road_type (many-to-many via experience_road_type)
--
-- MANY-TO-MANY IMPLEMENTATION:
-- A single driving experience can involve multiple road types
-- (e.g., Highway + Residential + Parking), and each road type
-- can be associated with multiple experiences.
-- This is implemented using the experience_road_type junction table.
--
-- ============================================
