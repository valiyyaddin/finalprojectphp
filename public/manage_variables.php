<?php
/**
 * Manage Variables Page
 * 
 * Add new weather conditions, traffic levels, road types, and supervisors
 * Optional administrative functionality
 */

require_once '../config/db.php';
require_once '../includes/functions.php';

$page_title = 'Manage Variables';

// Get database connection
$pdo = getDbConnection();

// Initialize variables
$errors = [];
$successMessage = null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_weather':
            $label = trim($_POST['weather_label'] ?? '');
            if (empty($label)) {
                $errors[] = "Weather label cannot be empty.";
            } elseif (addVariable($pdo, 'weather', $label)) {
                $successMessage = "Weather condition '$label' added successfully!";
            } else {
                $errors[] = "Failed to add weather condition. It may already exist.";
            }
            break;
            
        case 'add_traffic':
            $label = trim($_POST['traffic_label'] ?? '');
            if (empty($label)) {
                $errors[] = "Traffic label cannot be empty.";
            } elseif (addVariable($pdo, 'traffic', $label)) {
                $successMessage = "Traffic condition '$label' added successfully!";
            } else {
                $errors[] = "Failed to add traffic condition. It may already exist.";
            }
            break;
            
        case 'add_road_type':
            $label = trim($_POST['road_type_label'] ?? '');
            if (empty($label)) {
                $errors[] = "Road type label cannot be empty.";
            } elseif (addVariable($pdo, 'road_type', $label)) {
                $successMessage = "Road type '$label' added successfully!";
            } else {
                $errors[] = "Failed to add road type. It may already exist.";
            }
            break;
            
        case 'add_supervisor':
            $name = trim($_POST['supervisor_name'] ?? '');
            if (empty($name)) {
                $errors[] = "Supervisor name cannot be empty.";
            } elseif (addSupervisor($pdo, $name)) {
                $successMessage = "Supervisor '$name' added successfully!";
            } else {
                $errors[] = "Failed to add supervisor.";
            }
            break;
    }
}

// Get current data
$weatherList = getAllWeather($pdo);
$trafficList = getAllTraffic($pdo);
$roadTypeList = getAllRoadTypes($pdo);
$supervisorList = getAllSupervisors($pdo);

require_once '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h1 class="card-title">Manage Variables</h1>
    </div>
    
    <p class="text-muted mb-2">
        Add custom options for weather conditions, traffic levels, road types, and supervisors.
        These options will be available when logging driving experiences.
    </p>
    
    <?php if ($successMessage): ?>
        <div class="alert alert-success">
            <?php echo h($successMessage); ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul class="error-list">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo h($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <!-- Weather Conditions -->
    <section class="mb-2">
        <h2 class="mb-1">Weather Conditions</h2>
        
        <div class="stats-grid mb-1">
            <?php foreach ($weatherList as $weather): ?>
                <div class="card" style="background: var(--bg-light); padding: 0.75rem;">
                    <strong><?php echo h($weather['label']); ?></strong>
                </div>
            <?php endforeach; ?>
        </div>
        
        <form method="POST" action="manage_variables.php" class="filters">
            <input type="hidden" name="action" value="add_weather">
            <div class="filter-grid">
                <div class="form-group">
                    <label for="weather_label">New Weather Condition</label>
                    <input 
                        type="text" 
                        id="weather_label" 
                        name="weather_label" 
                        class="form-control"
                        placeholder="e.g., Hail, Sleet"
                        required
                    >
                </div>
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary">Add Weather</button>
                </div>
            </div>
        </form>
    </section>
    
    <!-- Traffic Conditions -->
    <section class="mb-2">
        <h2 class="mb-1">Traffic Conditions</h2>
        
        <div class="stats-grid mb-1">
            <?php foreach ($trafficList as $traffic): ?>
                <div class="card" style="background: var(--bg-light); padding: 0.75rem;">
                    <strong><?php echo h($traffic['label']); ?></strong>
                </div>
            <?php endforeach; ?>
        </div>
        
        <form method="POST" action="manage_variables.php" class="filters">
            <input type="hidden" name="action" value="add_traffic">
            <div class="filter-grid">
                <div class="form-group">
                    <label for="traffic_label">New Traffic Condition</label>
                    <input 
                        type="text" 
                        id="traffic_label" 
                        name="traffic_label" 
                        class="form-control"
                        placeholder="e.g., Congested, Free-flowing"
                        required
                    >
                </div>
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary">Add Traffic</button>
                </div>
            </div>
        </form>
    </section>
    
    <!-- Road Types -->
    <section class="mb-2">
        <h2 class="mb-1">Road Types</h2>
        
        <div class="stats-grid mb-1">
            <?php foreach ($roadTypeList as $roadType): ?>
                <div class="card" style="background: var(--bg-light); padding: 0.75rem;">
                    <strong><?php echo h($roadType['label']); ?></strong>
                </div>
            <?php endforeach; ?>
        </div>
        
        <form method="POST" action="manage_variables.php" class="filters">
            <input type="hidden" name="action" value="add_road_type">
            <div class="filter-grid">
                <div class="form-group">
                    <label for="road_type_label">New Road Type</label>
                    <input 
                        type="text" 
                        id="road_type_label" 
                        name="road_type_label" 
                        class="form-control"
                        placeholder="e.g., Expressway, Dirt Road"
                        required
                    >
                </div>
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary">Add Road Type</button>
                </div>
            </div>
        </form>
    </section>
    
    <!-- Supervisors -->
    <section class="mb-2">
        <h2 class="mb-1">Supervisors</h2>
        
        <div class="stats-grid mb-1">
            <?php foreach ($supervisorList as $supervisor): ?>
                <div class="card" style="background: var(--bg-light); padding: 0.75rem;">
                    <strong><?php echo h($supervisor['name']); ?></strong>
                </div>
            <?php endforeach; ?>
        </div>
        
        <form method="POST" action="manage_variables.php" class="filters">
            <input type="hidden" name="action" value="add_supervisor">
            <div class="filter-grid">
                <div class="form-group">
                    <label for="supervisor_name">New Supervisor</label>
                    <input 
                        type="text" 
                        id="supervisor_name" 
                        name="supervisor_name" 
                        class="form-control"
                        placeholder="e.g., John Doe, Instructor Smith"
                        required
                    >
                </div>
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary">Add Supervisor</button>
                </div>
            </div>
        </form>
    </section>
</div>

<div class="text-center mt-1">
    <a href="index.php" class="btn btn-secondary">Back to Home</a>
</div>

<?php require_once '../includes/footer.php'; ?>
