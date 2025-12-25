<?php
/**
 * DrivingExperience Class
 * 
 * Represents a single driving experience with all related data
 */
class DrivingExperience {
    private $id;
    private $drive_datetime;
    private $km;
    private $notes;
    private $weather_id;
    private $traffic_id;
    private $supervisor_id;
    private $road_type_ids = [];
    
    // Related objects
    private $weather;
    private $traffic;
    private $supervisor;
    private $road_types = [];
    
    /**
     * Constructor
     */
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }
    
    /**
     * Populate object from array
     */
    public function hydrate($data) {
        if (isset($data['id'])) $this->id = $data['id'];
        if (isset($data['drive_datetime'])) $this->drive_datetime = $data['drive_datetime'];
        if (isset($data['km'])) $this->km = $data['km'];
        if (isset($data['notes'])) $this->notes = $data['notes'];
        if (isset($data['weather_id'])) $this->weather_id = $data['weather_id'];
        if (isset($data['traffic_id'])) $this->traffic_id = $data['traffic_id'];
        if (isset($data['supervisor_id'])) $this->supervisor_id = $data['supervisor_id'];
        if (isset($data['road_type_ids'])) $this->road_type_ids = $data['road_type_ids'];
    }
    
    /**
     * Save to database (insert or update)
     */
    public function save(PDO $pdo) {
        if ($this->id) {
            return $this->update($pdo);
        } else {
            return $this->insert($pdo);
        }
    }
    
    /**
     * Insert new experience
     */
    private function insert(PDO $pdo) {
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("
                INSERT INTO driving_experience 
                (drive_datetime, km, notes, weather_id, traffic_id, supervisor_id)
                VALUES (:drive_datetime, :km, :notes, :weather_id, :traffic_id, :supervisor_id)
            ");
            
            $stmt->execute([
                ':drive_datetime' => $this->drive_datetime,
                ':km' => $this->km,
                ':notes' => $this->notes,
                ':weather_id' => $this->weather_id,
                ':traffic_id' => $this->traffic_id,
                ':supervisor_id' => $this->supervisor_id
            ]);
            
            $this->id = $pdo->lastInsertId();
            
            // Insert road types
            $this->saveRoadTypes($pdo);
            
            $pdo->commit();
            return $this->id;
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Error inserting driving experience: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update existing experience
     */
    private function update(PDO $pdo) {
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("
                UPDATE driving_experience 
                SET drive_datetime = :drive_datetime,
                    km = :km,
                    notes = :notes,
                    weather_id = :weather_id,
                    traffic_id = :traffic_id,
                    supervisor_id = :supervisor_id
                WHERE id = :id
            ");
            
            $stmt->execute([
                ':drive_datetime' => $this->drive_datetime,
                ':km' => $this->km,
                ':notes' => $this->notes,
                ':weather_id' => $this->weather_id,
                ':traffic_id' => $this->traffic_id,
                ':supervisor_id' => $this->supervisor_id,
                ':id' => $this->id
            ]);
            
            // Delete and re-insert road types
            $stmt = $pdo->prepare("DELETE FROM experience_road_type WHERE experience_id = :id");
            $stmt->execute([':id' => $this->id]);
            
            $this->saveRoadTypes($pdo);
            
            $pdo->commit();
            return true;
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Error updating driving experience: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Save road types (many-to-many)
     */
    private function saveRoadTypes(PDO $pdo) {
        if (empty($this->road_type_ids)) return;
        
        $stmt = $pdo->prepare("
            INSERT INTO experience_road_type (experience_id, road_type_id)
            VALUES (:experience_id, :road_type_id)
        ");
        
        foreach ($this->road_type_ids as $roadTypeId) {
            $stmt->execute([
                ':experience_id' => $this->id,
                ':road_type_id' => $roadTypeId
            ]);
        }
    }
    
    /**
     * Delete experience from database
     */
    public function delete(PDO $pdo) {
        try {
            $stmt = $pdo->prepare("DELETE FROM driving_experience WHERE id = :id");
            return $stmt->execute([':id' => $this->id]);
        } catch (PDOException $e) {
            error_log("Error deleting driving experience: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find experience by ID
     */
    public static function findById(PDO $pdo, $id) {
        $stmt = $pdo->prepare("SELECT * FROM driving_experience WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        
        if (!$data) return null;
        
        $experience = new self($data);
        
        // Load road types
        $stmt = $pdo->prepare("
            SELECT road_type_id FROM experience_road_type WHERE experience_id = :id
        ");
        $stmt->execute([':id' => $id]);
        $experience->road_type_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        return $experience;
    }
    
    /**
     * Validate data
     */
    public function validate() {
        $errors = [];
        
        if (empty($this->drive_datetime)) {
            $errors[] = "Date and time is required.";
        }
        
        if (empty($this->km) || !is_numeric($this->km) || $this->km <= 0) {
            $errors[] = "Please enter a valid distance (km > 0).";
        }
        
        if (empty($this->weather_id) || !is_numeric($this->weather_id)) {
            $errors[] = "Please select a weather condition.";
        }
        
        if (empty($this->traffic_id) || !is_numeric($this->traffic_id)) {
            $errors[] = "Please select a traffic condition.";
        }
        
        if (empty($this->supervisor_id) || !is_numeric($this->supervisor_id)) {
            $errors[] = "Please select a supervisor.";
        }
        
        if (empty($this->road_type_ids)) {
            $errors[] = "Please select at least one road type.";
        }
        
        return $errors;
    }
    
    // Getters and Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    
    public function getDriveDatetime() { return $this->drive_datetime; }
    public function setDriveDatetime($datetime) { $this->drive_datetime = $datetime; }
    
    public function getKm() { return $this->km; }
    public function setKm($km) { $this->km = $km; }
    
    public function getNotes() { return $this->notes; }
    public function setNotes($notes) { $this->notes = $notes; }
    
    public function getWeatherId() { return $this->weather_id; }
    public function setWeatherId($id) { $this->weather_id = $id; }
    
    public function getTrafficId() { return $this->traffic_id; }
    public function setTrafficId($id) { $this->traffic_id = $id; }
    
    public function getSupervisorId() { return $this->supervisor_id; }
    public function setSupervisorId($id) { $this->supervisor_id = $id; }
    
    public function getRoadTypeIds() { return $this->road_type_ids; }
    public function setRoadTypeIds($ids) { $this->road_type_ids = $ids; }
}

/**
 * Weather Class
 */
class Weather {
    private $id;
    private $label;
    
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->label = $data['label'] ?? '';
        }
    }
    
    public static function getAll(PDO $pdo) {
        $stmt = $pdo->query("SELECT id, label FROM weather ORDER BY label");
        return $stmt->fetchAll();
    }
    
    public function getId() { return $this->id; }
    public function getLabel() { return $this->label; }
}

/**
 * Traffic Class
 */
class Traffic {
    private $id;
    private $label;
    
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->label = $data['label'] ?? '';
        }
    }
    
    public static function getAll(PDO $pdo) {
        $stmt = $pdo->query("SELECT id, label FROM traffic ORDER BY label");
        return $stmt->fetchAll();
    }
    
    public function getId() { return $this->id; }
    public function getLabel() { return $this->label; }
}

/**
 * Supervisor Class
 */
class Supervisor {
    private $id;
    private $name;
    
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->name = $data['name'] ?? '';
        }
    }
    
    public static function getAll(PDO $pdo) {
        $stmt = $pdo->query("SELECT id, name FROM supervisor ORDER BY name");
        return $stmt->fetchAll();
    }
    
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
}

/**
 * RoadType Class
 */
class RoadType {
    private $id;
    private $label;
    
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->label = $data['label'] ?? '';
        }
    }
    
    public static function getAll(PDO $pdo) {
        $stmt = $pdo->query("SELECT id, label FROM road_type ORDER BY label");
        return $stmt->fetchAll();
    }
    
    public function getId() { return $this->id; }
    public function getLabel() { return $this->label; }
}
