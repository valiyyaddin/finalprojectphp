<?php
/**
 * Add Driving Experience Page
 * 
 * Form to enter a new driving experience with validation
 * Mobile-friendly responsive design
 */

require_once '../config/db.php';
require_once '../includes/functions.php';

$page_title = 'Add New Drive';

// Get database connection
$pdo = getDbConnection();

// Initialize variables
$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $data = [
        'drive_datetime' => $_POST['drive_datetime'] ?? '',
        'km' => $_POST['km'] ?? '',
        'weather_id' => $_POST['weather_id'] ?? '',
        'traffic_id' => $_POST['traffic_id'] ?? '',
        'supervisor_id' => $_POST['supervisor_id'] ?? '',
        'road_types' => $_POST['road_types'] ?? [],
        'notes' => $_POST['notes'] ?? ''
    ];
    
    // Validate input
    $errors = validateDrivingExperience($data);
    
    // If no errors, save to database
    if (empty($errors)) {
        $experienceId = saveDrivingExperience($pdo, $data);
        
        if ($experienceId) {
            // Success - redirect to summary page
            $_SESSION['success_message'] = "Driving experience added successfully!";
            redirect('summary.php');
        } else {
            $errors[] = "Failed to save driving experience. Please try again.";
        }
    }
}

// Get dropdown data
$weatherOptions = getAllWeather($pdo);
$trafficOptions = getAllTraffic($pdo);
$supervisorOptions = getAllSupervisors($pdo);
$roadTypeOptions = getAllRoadTypes($pdo);

// Get current datetime for default value
$currentDatetime = date('Y-m-d\TH:i');

require_once '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h1 class="card-title">Add New Driving Experience</h1>
    </div>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <strong>Please correct the following errors:</strong>
            <ul class="error-list">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo h($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="add_drive.php" novalidate>
        <!-- Date and Time -->
        <div class="form-group">
            <label for="drive_datetime" class="required">Date & Time</label>
            <input 
                type="datetime-local" 
                id="drive_datetime" 
                name="drive_datetime" 
                class="form-control"
                value="<?php echo isset($_POST['drive_datetime']) ? h($_POST['drive_datetime']) : $currentDatetime; ?>"
                required
            >
        </div>
        
        <!-- Distance in Kilometers -->
        <div class="form-group">
            <label for="km" class="required">Distance (Kilometers)</label>
            <input 
                type="number" 
                id="km" 
                name="km" 
                class="form-control"
                inputmode="decimal"
                min="0" 
                step="0.1"
                placeholder="e.g., 25.5"
                value="<?php echo isset($_POST['km']) ? h($_POST['km']) : ''; ?>"
                required
            >
        </div>
        
        <!-- Weather Condition -->
        <div class="form-group">
            <label for="weather_id" class="required">Weather Condition</label>
            <select id="weather_id" name="weather_id" class="form-control" required>
                <option value="">-- Select Weather --</option>
                <?php foreach ($weatherOptions as $weather): ?>
                    <option 
                        value="<?php echo h($weather['id']); ?>"
                        <?php echo (isset($_POST['weather_id']) && $_POST['weather_id'] == $weather['id']) ? 'selected' : ''; ?>
                    >
                        <?php echo h($weather['label']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- Traffic Condition -->
        <div class="form-group">
            <label for="traffic_id" class="required">Traffic Condition</label>
            <select id="traffic_id" name="traffic_id" class="form-control" required>
                <option value="">-- Select Traffic --</option>
                <?php foreach ($trafficOptions as $traffic): ?>
                    <option 
                        value="<?php echo h($traffic['id']); ?>"
                        <?php echo (isset($_POST['traffic_id']) && $_POST['traffic_id'] == $traffic['id']) ? 'selected' : ''; ?>
                    >
                        <?php echo h($traffic['label']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- Supervisor -->
        <div class="form-group">
            <label for="supervisor_id" class="required">Supervisor</label>
            <select id="supervisor_id" name="supervisor_id" class="form-control" required>
                <option value="">-- Select Supervisor --</option>
                <?php foreach ($supervisorOptions as $supervisor): ?>
                    <option 
                        value="<?php echo h($supervisor['id']); ?>"
                        <?php echo (isset($_POST['supervisor_id']) && $_POST['supervisor_id'] == $supervisor['id']) ? 'selected' : ''; ?>
                    >
                        <?php echo h($supervisor['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- Road Types (Many-to-Many) -->
        <div class="form-group">
            <label class="required">Road Types (Select all that apply)</label>
            <div class="checkbox-group">
                <?php foreach ($roadTypeOptions as $roadType): ?>
                    <div class="checkbox-item">
                        <input 
                            type="checkbox" 
                            id="road_type_<?php echo h($roadType['id']); ?>" 
                            name="road_types[]" 
                            value="<?php echo h($roadType['id']); ?>"
                            <?php echo (isset($_POST['road_types']) && in_array($roadType['id'], $_POST['road_types'])) ? 'checked' : ''; ?>
                        >
                        <label for="road_type_<?php echo h($roadType['id']); ?>">
                            <?php echo h($roadType['label']); ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Notes -->
        <div class="form-group">
            <label for="notes">Notes (Optional)</label>
            <textarea 
                id="notes" 
                name="notes" 
                class="form-control" 
                rows="4"
                placeholder="Add any additional notes about this driving experience..."
            ><?php echo isset($_POST['notes']) ? h($_POST['notes']) : ''; ?></textarea>
        </div>
        
        <!-- Submit Button -->
        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block">
                Save Driving Experience
            </button>
        </div>
    </form>
</div>

<div class="text-center mt-1">
    <a href="summary.php" class="btn btn-secondary">View All Experiences</a>
</div>

<?php require_once '../includes/footer.php'; ?>
