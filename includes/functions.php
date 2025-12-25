<?php
/**
 * Common Functions Library
 * 
 * This file contains reusable functions for the application
 */

// Include OOP classes
require_once __DIR__ . '/classes.php';

/**
 * ID Anonymization Functions for Session Security
 * Encode/decode IDs to hide real database identifiers
 */

// Secret key for encoding (should be in config in production)
define('ENCODE_KEY', 'DriveLog2025!SecureKey#');

/**
 * Encode ID to obfuscated string
 */
function encodeId($id) {
    if (empty($id)) return null;
    $encoded = base64_encode($id . '|' . hash('sha256', $id . ENCODE_KEY));
    return rtrim(strtr($encoded, '+/', '-_'), '=');
}

/**
 * Decode obfuscated string back to ID
 */
function decodeId($encoded) {
    if (empty($encoded)) return null;
    $encoded = str_pad(strtr($encoded, '-_', '+/'), strlen($encoded) % 4, '=', STR_PAD_RIGHT);
    $decoded = base64_decode($encoded);
    if ($decoded === false) return null;
    
    list($id, $hash) = explode('|', $decoded);
    if (hash('sha256', $id . ENCODE_KEY) === $hash) {
        return intval($id);
    }
    return null;
}

/**
 * Store encoded ID in session
 */
function storeEncodedId($key, $id) {
    if (!isset($_SESSION['encoded_ids'])) {
        $_SESSION['encoded_ids'] = [];
    }
    $encoded = encodeId($id);
    $_SESSION['encoded_ids'][$key] = ['encoded' => $encoded, 'id' => $id];
    return $encoded;
}

/**
 * Retrieve original ID from session by encoded value
 */
function retrieveEncodedId($encoded) {
    if (!isset($_SESSION['encoded_ids'])) return null;
    
    foreach ($_SESSION['encoded_ids'] as $data) {
        if ($data['encoded'] === $encoded) {
            return $data['id'];
        }
    }
    
    return decodeId($encoded); // Fallback to decode
}

/**
 * Sanitize and escape output for HTML
 * 
 * @param string $string Input string
 * @return string Escaped string safe for HTML output
 */
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to another page
 * 
 * @param string $path Path to redirect to
 */
function redirect($path) {
    header("Location: $path");
    exit();
}

/**
 * Format datetime for display
 * 
 * @param string $datetime MySQL datetime string
 * @param string $format Output format
 * @return string Formatted date
 */
function formatDateTime($datetime, $format = 'Y-m-d H:i') {
    if (empty($datetime)) return '';
    $dt = new DateTime($datetime);
    return $dt->format($format);
}

/**
 * Format number with 2 decimal places
 * 
 * @param float $number Number to format
 * @return string Formatted number
 */
function formatNumber($number) {
    return number_format($number, 2, '.', ',');
}

/**
 * Get all weather conditions
 * 
 * @param PDO $pdo Database connection
 * @return array Array of weather conditions
 */
function getAllWeather($pdo) {
    $stmt = $pdo->query("SELECT id, label FROM weather ORDER BY label");
    return $stmt->fetchAll();
}

/**
 * Get all traffic conditions
 * 
 * @param PDO $pdo Database connection
 * @return array Array of traffic conditions
 */
function getAllTraffic($pdo) {
    $stmt = $pdo->query("SELECT id, label FROM traffic ORDER BY label");
    return $stmt->fetchAll();
}

/**
 * Get all supervisors
 * 
 * @param PDO $pdo Database connection
 * @return array Array of supervisors
 */
function getAllSupervisors($pdo) {
    $stmt = $pdo->query("SELECT id, name FROM supervisor ORDER BY name");
    return $stmt->fetchAll();
}

/**
 * Get all road types
 * 
 * @param PDO $pdo Database connection
 * @return array Array of road types
 */
function getAllRoadTypes($pdo) {
    $stmt = $pdo->query("SELECT id, label FROM road_type ORDER BY label");
    return $stmt->fetchAll();
}

/**
 * Get all driving experiences with related data
 * 
 * @param PDO $pdo Database connection
 * @param string $startDate Optional start date filter
 * @param string $endDate Optional end date filter
 * @return array Array of experiences
 */
function getAllExperiences($pdo, $startDate = null, $endDate = null) {
    $sql = "SELECT 
                de.id,
                de.drive_datetime,
                de.km,
                de.notes,
                w.label as weather,
                t.label as traffic,
                s.name as supervisor
            FROM driving_experience de
            JOIN weather w ON de.weather_id = w.id
            JOIN traffic t ON de.traffic_id = t.id
            JOIN supervisor s ON de.supervisor_id = s.id";
    
    $conditions = [];
    $params = [];
    
    if ($startDate) {
        $conditions[] = "de.drive_datetime >= :start_date";
        $params[':start_date'] = $startDate . ' 00:00:00';
    }
    
    if ($endDate) {
        $conditions[] = "de.drive_datetime <= :end_date";
        $params[':end_date'] = $endDate . ' 23:59:59';
    }
    
    if (count($conditions) > 0) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }
    
    $sql .= " ORDER BY de.drive_datetime DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $experiences = $stmt->fetchAll();
    
    // Get road types for each experience
    foreach ($experiences as &$exp) {
        $stmt = $pdo->prepare("
            SELECT rt.label 
            FROM experience_road_type ert
            JOIN road_type rt ON ert.road_type_id = rt.id
            WHERE ert.experience_id = :exp_id
            ORDER BY rt.label
        ");
        $stmt->execute([':exp_id' => $exp['id']]);
        $roadTypes = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $exp['road_types'] = implode(', ', $roadTypes);
    }
    
    return $experiences;
}

/**
 * Get total kilometers driven
 * 
 * @param PDO $pdo Database connection
 * @param string $startDate Optional start date filter
 * @param string $endDate Optional end date filter
 * @return float Total kilometers
 */
function getTotalKm($pdo, $startDate = null, $endDate = null) {
    $sql = "SELECT COALESCE(SUM(km), 0) as total FROM driving_experience";
    
    $conditions = [];
    $params = [];
    
    if ($startDate) {
        $conditions[] = "drive_datetime >= :start_date";
        $params[':start_date'] = $startDate . ' 00:00:00';
    }
    
    if ($endDate) {
        $conditions[] = "drive_datetime <= :end_date";
        $params[':end_date'] = $endDate . ' 23:59:59';
    }
    
    if (count($conditions) > 0) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch()['total'];
}

/**
 * Get statistics: km by weather
 * 
 * @param PDO $pdo Database connection
 * @return array Statistics data
 */
function getKmByWeather($pdo) {
    $stmt = $pdo->query("
        SELECT w.label, COALESCE(SUM(de.km), 0) as total_km
        FROM weather w
        LEFT JOIN driving_experience de ON w.id = de.weather_id
        GROUP BY w.id, w.label
        ORDER BY total_km DESC
    ");
    return $stmt->fetchAll();
}

/**
 * Get statistics: drive count by road type
 * 
 * @param PDO $pdo Database connection
 * @return array Statistics data
 */
function getDrivesByRoadType($pdo) {
    $stmt = $pdo->query("
        SELECT rt.label, COUNT(ert.experience_id) as drive_count
        FROM road_type rt
        LEFT JOIN experience_road_type ert ON rt.id = ert.road_type_id
        GROUP BY rt.id, rt.label
        ORDER BY drive_count DESC
    ");
    return $stmt->fetchAll();
}

/**
 * Get statistics: km by month
 * 
 * @param PDO $pdo Database connection
 * @return array Statistics data
 */
function getKmByMonth($pdo) {
    $stmt = $pdo->query("
        SELECT 
            DATE_FORMAT(drive_datetime, '%Y-%m') as month,
            SUM(km) as total_km
        FROM driving_experience
        GROUP BY DATE_FORMAT(drive_datetime, '%Y-%m')
        ORDER BY month ASC
    ");
    return $stmt->fetchAll();
}

/**
 * Validate driving experience input
 * 
 * @param array $data Input data
 * @return array Array of error messages (empty if valid)
 */
function validateDrivingExperience($data) {
    $errors = [];
    
    if (empty($data['drive_datetime'])) {
        $errors[] = "Date and time is required.";
    }
    
    if (empty($data['km']) || !is_numeric($data['km']) || $data['km'] <= 0) {
        $errors[] = "Please enter a valid distance (km > 0).";
    }
    
    if (empty($data['weather_id']) || !is_numeric($data['weather_id'])) {
        $errors[] = "Please select a weather condition.";
    }
    
    if (empty($data['traffic_id']) || !is_numeric($data['traffic_id'])) {
        $errors[] = "Please select a traffic condition.";
    }
    
    if (empty($data['supervisor_id']) || !is_numeric($data['supervisor_id'])) {
        $errors[] = "Please select a supervisor.";
    }
    
    if (empty($data['road_types']) || !is_array($data['road_types'])) {
        $errors[] = "Please select at least one road type.";
    }
    
    return $errors;
}

/**
 * Save a new driving experience
 * 
 * @param PDO $pdo Database connection
 * @param array $data Form data
 * @return int|false Insert ID or false on failure
 */
function saveDrivingExperience($pdo, $data) {
    try {
        $pdo->beginTransaction();
        
        // Insert main experience
        $stmt = $pdo->prepare("
            INSERT INTO driving_experience 
            (drive_datetime, km, notes, weather_id, traffic_id, supervisor_id)
            VALUES (:drive_datetime, :km, :notes, :weather_id, :traffic_id, :supervisor_id)
        ");
        
        $stmt->execute([
            ':drive_datetime' => $data['drive_datetime'],
            ':km' => $data['km'],
            ':notes' => $data['notes'] ?? '',
            ':weather_id' => $data['weather_id'],
            ':traffic_id' => $data['traffic_id'],
            ':supervisor_id' => $data['supervisor_id']
        ]);
        
        $experienceId = $pdo->lastInsertId();
        
        // Insert road types (many-to-many)
        $stmt = $pdo->prepare("
            INSERT INTO experience_road_type (experience_id, road_type_id)
            VALUES (:experience_id, :road_type_id)
        ");
        
        foreach ($data['road_types'] as $roadTypeId) {
            $stmt->execute([
                ':experience_id' => $experienceId,
                ':road_type_id' => $roadTypeId
            ]);
        }
        
        $pdo->commit();
        return $experienceId;
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Error saving driving experience: " . $e->getMessage());
        return false;
    }
}

/**
 * Add a new variable (weather, traffic, road_type)
 * 
 * @param PDO $pdo Database connection
 * @param string $table Table name (weather, traffic, road_type)
 * @param string $label Label value
 * @return bool Success status
 */
function addVariable($pdo, $table, $label) {
    $allowedTables = ['weather', 'traffic', 'road_type'];
    
    if (!in_array($table, $allowedTables)) {
        return false;
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO $table (label) VALUES (:label)");
        return $stmt->execute([':label' => trim($label)]);
    } catch (PDOException $e) {
        error_log("Error adding variable: " . $e->getMessage());
        return false;
    }
}

/**
 * Add a new supervisor
 * 
 * @param PDO $pdo Database connection
 * @param string $name Supervisor name
 * @return bool Success status
 */
function addSupervisor($pdo, $name) {
    try {
        $stmt = $pdo->prepare("INSERT INTO supervisor (name) VALUES (:name)");
        return $stmt->execute([':name' => trim($name)]);
    } catch (PDOException $e) {
        error_log("Error adding supervisor: " . $e->getMessage());
        return false;
    }
}
