-- ============================================
-- SEED DATA FOR SUPERVISED DRIVING EXPERIENCE APP
-- ============================================

-- Insert weather conditions
INSERT INTO weather (label) VALUES
('Sunny'),
('Cloudy'),
('Rainy'),
('Foggy'),
('Snowy'),
('Stormy'),
('Partly Cloudy'),
('Windy');

-- Insert traffic conditions
INSERT INTO traffic (label) VALUES
('Light'),
('Moderate'),
('Heavy'),
('Very Heavy'),
('None');

-- Insert supervisors
INSERT INTO supervisor (name) VALUES
('Parent - Mom'),
('Parent - Dad'),
('Instructor - John Smith'),
('Instructor - Sarah Johnson'),
('Family Member - Uncle Tom');

-- Insert road types
INSERT INTO road_type (label) VALUES
('Highway'),
('Residential Street'),
('City Street'),
('Country Road'),
('Parking Lot'),
('School Zone'),
('Construction Zone'),
('Mountain Road');

-- Insert sample driving experiences
INSERT INTO driving_experience (drive_datetime, km, notes, weather_id, traffic_id, supervisor_id) VALUES
('2024-11-15 14:30:00', 25.50, 'First time on highway. Practiced lane changes and merging.', 1, 2, 1),
('2024-11-18 09:00:00', 12.30, 'Morning practice in residential area. Worked on parking.', 2, 1, 2),
('2024-11-22 16:45:00', 18.75, 'Rush hour practice. Learned defensive driving in heavy traffic.', 1, 3, 3),
('2024-11-25 11:00:00', 30.00, 'Highway driving in rainy conditions. Good visibility practice.', 3, 2, 1),
('2024-11-28 13:15:00', 8.50, 'Practiced parallel parking and three-point turns.', 7, 1, 2),
('2024-12-01 10:30:00', 45.20, 'Long drive combining multiple road types. Very successful session.', 1, 2, 3),
('2024-12-05 08:00:00', 15.00, 'Early morning drive. Less traffic, good for building confidence.', 2, 1, 1),
('2024-12-10 17:30:00', 22.00, 'Evening drive with challenging weather conditions.', 4, 3, 4),
('2024-12-15 14:00:00', 35.50, 'Mixed conditions practice. Highway and city driving.', 1, 2, 2),
('2024-12-20 10:00:00', 10.00, 'School zone practice during pickup time.', 1, 2, 5);

-- Insert many-to-many relationships for road types
-- Experience 1: Highway + City Street
INSERT INTO experience_road_type (experience_id, road_type_id) VALUES
(1, 1), (1, 3);

-- Experience 2: Residential + Parking Lot
INSERT INTO experience_road_type (experience_id, road_type_id) VALUES
(2, 2), (2, 5);

-- Experience 3: City Street
INSERT INTO experience_road_type (experience_id, road_type_id) VALUES
(3, 3);

-- Experience 4: Highway + City Street
INSERT INTO experience_road_type (experience_id, road_type_id) VALUES
(4, 1), (4, 3);

-- Experience 5: Residential + Parking Lot
INSERT INTO experience_road_type (experience_id, road_type_id) VALUES
(5, 2), (5, 5);

-- Experience 6: Highway + Country Road + City Street
INSERT INTO experience_road_type (experience_id, road_type_id) VALUES
(6, 1), (6, 4), (6, 3);

-- Experience 7: Residential Street
INSERT INTO experience_road_type (experience_id, road_type_id) VALUES
(7, 2);

-- Experience 8: Highway + City Street
INSERT INTO experience_road_type (experience_id, road_type_id) VALUES
(8, 1), (8, 3);

-- Experience 9: Highway + City Street + Residential
INSERT INTO experience_road_type (experience_id, road_type_id) VALUES
(9, 1), (9, 3), (9, 2);

-- Experience 10: School Zone + Residential
INSERT INTO experience_road_type (experience_id, road_type_id) VALUES
(10, 6), (10, 2);

-- ============================================
-- VERIFICATION QUERIES (optional - for testing)
-- ============================================
-- SELECT COUNT(*) as total_experiences FROM driving_experience;
-- SELECT SUM(km) as total_km FROM driving_experience;
-- SELECT rt.label, COUNT(*) as usage_count 
-- FROM experience_road_type ert
-- JOIN road_type rt ON ert.road_type_id = rt.id
-- GROUP BY rt.id, rt.label
-- ORDER BY usage_count DESC;
