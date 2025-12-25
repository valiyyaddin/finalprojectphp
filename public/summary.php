<?php
/**
 * Summary Page
 * 
 * Displays all driving experiences in a table with total kilometers
 * Includes optional date range filtering
 */

require_once '../config/db.php';
require_once '../includes/functions.php';

$page_title = 'Driving Summary';

// Get database connection
$pdo = getDbConnection();

// Check for success message from add page
$successMessage = $_SESSION['success_message'] ?? null;
unset($_SESSION['success_message']);

// Get filter parameters
$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;

// Get all experiences with optional filtering
$experiences = getAllExperiences($pdo, $startDate, $endDate);

// Get total kilometers with same filters
$totalKm = getTotalKm($pdo, $startDate, $endDate);

require_once '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h1 class="card-title">Driving Experience Summary</h1>
    </div>
    
    <?php if ($successMessage): ?>
        <div class="alert alert-success">
            <?php echo h($successMessage); ?>
        </div>
    <?php endif; ?>
    
    <!-- Filters -->
    <form method="GET" action="summary.php" class="filters">
        <div class="filter-grid">
            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input 
                    type="date" 
                    id="start_date" 
                    name="start_date" 
                    class="form-control"
                    value="<?php echo h($startDate ?? ''); ?>"
                >
            </div>
            
            <div class="form-group">
                <label for="end_date">End Date</label>
                <input 
                    type="date" 
                    id="end_date" 
                    name="end_date" 
                    class="form-control"
                    value="<?php echo h($endDate ?? ''); ?>"
                >
            </div>
            
            <div class="form-group">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary">Filter</button>
                <?php if ($startDate || $endDate): ?>
                    <a href="summary.php" class="btn btn-secondary">Clear</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
    
    <!-- Total KM Display -->
    <div class="stat-card text-center mb-2">
        <div class="stat-label">Total Kilometers Traveled</div>
        <div class="stat-value"><?php echo formatNumber($totalKm); ?> km</div>
        <?php if ($startDate || $endDate): ?>
            <div class="stat-label">
                (Filtered: 
                <?php echo $startDate ? formatDateTime($startDate, 'M d, Y') : 'Start'; ?> 
                to 
                <?php echo $endDate ? formatDateTime($endDate, 'M d, Y') : 'End'; ?>)
            </div>
        <?php endif; ?>
    </div>
    
    <?php if (empty($experiences)): ?>
        <div class="alert alert-info">
            No driving experiences found. <a href="add_drive.php">Add your first drive!</a>
        </div>
    <?php else: ?>
        <!-- Experiences Table -->
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Distance (km)</th>
                        <th>Weather</th>
                        <th>Traffic</th>
                        <th>Supervisor</th>
                        <th>Road Types</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($experiences as $exp): ?>
                        <tr>
                            <td><?php echo formatDateTime($exp['drive_datetime'], 'M d, Y H:i'); ?></td>
                            <td><?php echo formatNumber($exp['km']); ?></td>
                            <td><?php echo h($exp['weather']); ?></td>
                            <td><?php echo h($exp['traffic']); ?></td>
                            <td><?php echo h($exp['supervisor']); ?></td>
                            <td><?php echo h($exp['road_types']); ?></td>
                            <td><?php echo h(substr($exp['notes'] ?? '', 0, 100)); ?><?php echo strlen($exp['notes'] ?? '') > 100 ? '...' : ''; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <p class="text-center text-muted mt-1">
            Showing <?php echo count($experiences); ?> driving experience<?php echo count($experiences) !== 1 ? 's' : ''; ?>
        </p>
    <?php endif; ?>
</div>

<div class="text-center mt-1">
    <a href="add_drive.php" class="btn btn-primary">Add New Drive</a>
    <a href="stats.php" class="btn btn-secondary">View Statistics</a>
</div>

<?php require_once '../includes/footer.php'; ?>
