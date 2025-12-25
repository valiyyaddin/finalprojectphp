<?php
/**
 * Home Page / Dashboard
 * 
 * Main landing page with navigation to all features
 */

require_once '../config/db.php';
require_once '../includes/functions.php';

$page_title = 'Home';

// Get database connection
$pdo = getDbConnection();

// Get some quick stats for the dashboard
$totalKm = getTotalKm($pdo);
$totalDrives = $pdo->query("SELECT COUNT(*) as count FROM driving_experience")->fetch()['count'];

require_once '../includes/header.php';
?>

<section class="card">
    <div class="card-header">
        <h1 class="card-title">Welcome to Your Driving Log</h1>
    </div>
    
    <p class="text-center text-muted mb-2">
        Track your supervised driving experiences, view statistics, and monitor your progress toward your driver's license.
    </p>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Drives Logged</div>
            <div class="stat-value"><?php echo number_format($totalDrives); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Distance Traveled</div>
            <div class="stat-value"><?php echo formatNumber($totalKm); ?> km</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Average per Drive</div>
            <div class="stat-value">
                <?php echo $totalDrives > 0 ? formatNumber($totalKm / $totalDrives) : '0.00'; ?> km
            </div>
        </div>
    </div>
</section>

<section class="dashboard-grid">
    <a href="add_drive.php" class="dashboard-card">
        <h2>➕ Add New Drive</h2>
        <p>Log a new supervised driving experience with all details.</p>
    </a>
    
    <a href="summary.php" class="dashboard-card">
        <h2>📋 View Summary</h2>
        <p>See all your driving experiences in a detailed table view.</p>
    </a>
    
    <a href="stats.php" class="dashboard-card">
        <h2>📊 Statistics</h2>
        <p>Analyze your driving data with interactive charts and graphs.</p>
    </a>
    
    <a href="manage_variables.php" class="dashboard-card">
        <h2>⚙️ Manage Options</h2>
        <p>Add custom weather conditions, road types, and supervisors.</p>
    </a>
</section>

<section class="card mt-2">
    <h2 class="mb-1">About This Application</h2>
    <p class="text-muted">
        This driving log helps you track and analyze your supervised driving experiences.
        Record details like weather conditions, traffic levels, road types (using many-to-many relationships),
        and supervisors. View comprehensive statistics with ChartJS visualizations and export-ready summary tables.
    </p>
</section>

<?php require_once '../includes/footer.php'; ?>
