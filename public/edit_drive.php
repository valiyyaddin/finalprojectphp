<?php
/**
 * Edit Driving Experience Page
 * 
 * Form to edit an existing driving experience with validation
 */

require_once '../config/db.php';
require_once '../includes/functions.php';

$page_title = 'Edit Drive';

// Get database connection
$pdo = getDbConnection();

// Get encoded ID from URL
$encodedId = $_GET['id'] ?? null;
$experienceId = decodeId($encodedId);

if (!$experienceId) {
    $_SESSION['error_message'] = "Invalid driving experience ID.";
    redirect('summary.php');
}

// Load experience using OOP
$experience = DrivingExperience::findById($pdo, $experienceId);

if (!$experience) {
    $_SESSION['error_message'] = "Driving experience not found.";
    redirect('summary.php');
}

// Initialize variables
$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update experience object with form data
    $experience->setDriveDatetime($_POST['drive_datetime'] ?? '');
    $experience->setKm($_POST['km'] ?? '');
    $experience->setWeatherId($_POST['weather_id'] ?? '');
    $experience->setTrafficId($_POST['traffic_id'] ?? '');
    $experience->setSupervisorId($_POST['supervisor_id'] ?? '');
    $experience->setRoadTypeIds($_POST['road_types'] ?? []);
    $experience->setNotes($_POST['notes'] ?? '');
    
    // Validate
    $errors = $experience->validate();
    
    // If no errors, save to database
    if (empty($errors)) {
        if ($experience->save($pdo)) {
            // Success - redirect to summary page
            $_SESSION['success_message'] = "Driving experience updated successfully!";
            redirect('summary.php');
        } else {
            $errors[] = "Failed to update driving experience. Please try again.";
        }
    }
}

// Get dropdown data using OOP
$weatherOptions = Weather::getAll($pdo);
$trafficOptions = Traffic::getAll($pdo);
$supervisorOptions = Supervisor::getAll($pdo);
$roadTypeOptions = RoadType::getAll($pdo);

require_once '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h1 class="card-title">Edit Driving Experience</h1>
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
    
    <form method="POST" action="edit_drive.php?id=<?php echo h($encodedId); ?>" novalidate>
        <!-- Date and Time -->
        <div class="form-group">
            <label for="drive_datetime" class="required">Date & Time</label>
            <input 
                type="datetime-local" 
                id="drive_datetime" 
                name="drive_datetime" 
                class="form-control"
                value="<?php echo h(date('Y-m-d\TH:i', strtotime($experience->getDriveDatetime()))); ?>"
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
                value="<?php echo h($experience->getKm()); ?>"
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
                        <?php echo ($experience->getWeatherId() == $weather['id']) ? 'selected' : ''; ?>
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
                        <?php echo ($experience->getTrafficId() == $traffic['id']) ? 'selected' : ''; ?>
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
                        <?php echo ($experience->getSupervisorId() == $supervisor['id']) ? 'selected' : ''; ?>
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
                <?php 
                $selectedRoadTypes = $experience->getRoadTypeIds();
                foreach ($roadTypeOptions as $roadType): 
                ?>
                    <div class="checkbox-item">
                        <input 
                            type="checkbox" 
                            id="road_type_<?php echo h($roadType['id']); ?>" 
                            name="road_types[]" 
                            value="<?php echo h($roadType['id']); ?>"
                            <?php echo in_array($roadType['id'], $selectedRoadTypes) ? 'checked' : ''; ?>
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
            ><?php echo h($experience->getNotes()); ?></textarea>
        </div>
        
        <!-- Submit Buttons -->
        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block">
                Update Driving Experience
            </button>
        </div>
    </form>
</div>

<div class="text-center mt-1">
    <a href="summary.php" class="btn btn-secondary">Cancel & Return to Summary</a>
</div>

<?php require_once '../includes/footer.php'; ?>
