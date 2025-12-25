<?php
/**
 * Statistics Page
 * 
 * Display charts and statistics using ChartJS
 * Multiple visualizations of driving data
 */

require_once '../config/db.php';
require_once '../includes/functions.php';

$page_title = 'Statistics';
$use_chartjs = true; // Load ChartJS in header

// Get database connection
$pdo = getDbConnection();

// Get statistics data
$kmByWeather = getKmByWeather($pdo);
$drivesByRoadType = getDrivesByRoadType($pdo);
$kmByMonth = getKmByMonth($pdo);

// Prepare data for ChartJS (JSON format)
$weatherLabels = array_map(function($item) { return $item['label']; }, $kmByWeather);
$weatherData = array_map(function($item) { return floatval($item['total_km']); }, $kmByWeather);

$roadTypeLabels = array_map(function($item) { return $item['label']; }, $drivesByRoadType);
$roadTypeData = array_map(function($item) { return intval($item['drive_count']); }, $drivesByRoadType);

$monthLabels = array_map(function($item) { return $item['month']; }, $kmByMonth);
$monthData = array_map(function($item) { return floatval($item['total_km']); }, $kmByMonth);

require_once '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h1 class="card-title">Driving Statistics</h1>
    </div>
    
    <p class="text-muted text-center mb-2">
        Analyze your driving patterns with interactive charts and visualizations.
    </p>
    
    <!-- Chart 1: Total KM by Weather -->
    <section class="mb-2">
        <h2 class="mb-1">Total Kilometers by Weather Condition</h2>
        <div class="chart-container">
            <canvas id="weatherChart"></canvas>
        </div>
    </section>
    
    <!-- Chart 2: Number of Drives by Road Type -->
    <section class="mb-2">
        <h2 class="mb-1">Number of Drives by Road Type</h2>
        <div class="chart-container">
            <canvas id="roadTypeChart"></canvas>
        </div>
    </section>
    
    <!-- Chart 3: KM per Month (Line Chart) -->
    <?php if (!empty($kmByMonth)): ?>
    <section class="mb-2">
        <h2 class="mb-1">Distance Traveled Over Time</h2>
        <div class="chart-container">
            <canvas id="monthChart"></canvas>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- Summary Stats Cards -->
    <section class="stats-grid mt-2">
        <div class="stat-card">
            <div class="stat-label">Most Common Weather</div>
            <div class="stat-value" style="font-size: 1.25rem;">
                <?php 
                if (!empty($kmByWeather)) {
                    $maxKm = max(array_column($kmByWeather, 'total_km'));
                    $mostCommon = array_filter($kmByWeather, function($item) use ($maxKm) {
                        return $item['total_km'] == $maxKm;
                    });
                    echo h(reset($mostCommon)['label']);
                }
                ?>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-label">Most Used Road Type</div>
            <div class="stat-value" style="font-size: 1.25rem;">
                <?php 
                if (!empty($drivesByRoadType)) {
                    $maxCount = max(array_column($drivesByRoadType, 'drive_count'));
                    $mostUsed = array_filter($drivesByRoadType, function($item) use ($maxCount) {
                        return $item['drive_count'] == $maxCount;
                    });
                    echo h(reset($mostUsed)['label']);
                }
                ?>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-label">Total Drives</div>
            <div class="stat-value" style="font-size: 1.25rem;">
                <?php 
                $totalDrives = $pdo->query("SELECT COUNT(*) as count FROM driving_experience")->fetch()['count'];
                echo number_format($totalDrives);
                ?>
            </div>
        </div>
    </section>
</div>

<div class="text-center mt-1">
    <a href="summary.php" class="btn btn-primary">View Summary Table</a>
    <a href="add_drive.php" class="btn btn-secondary">Add New Drive</a>
</div>

<script>
// Chart.js Configuration and Initialization

// Chart 1: Total KM by Weather (Bar Chart)
const weatherCtx = document.getElementById('weatherChart').getContext('2d');
const weatherChart = new Chart(weatherCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($weatherLabels); ?>,
        datasets: [{
            label: 'Total Kilometers',
            data: <?php echo json_encode($weatherData); ?>,
            backgroundColor: 'rgba(37, 99, 235, 0.7)',
            borderColor: 'rgba(37, 99, 235, 1)',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            title: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toFixed(1) + ' km';
                    }
                }
            }
        }
    }
});

// Chart 2: Drives by Road Type (Horizontal Bar Chart)
const roadTypeCtx = document.getElementById('roadTypeChart').getContext('2d');
const roadTypeChart = new Chart(roadTypeCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($roadTypeLabels); ?>,
        datasets: [{
            label: 'Number of Drives',
            data: <?php echo json_encode($roadTypeData); ?>,
            backgroundColor: 'rgba(16, 185, 129, 0.7)',
            borderColor: 'rgba(16, 185, 129, 1)',
            borderWidth: 2
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

<?php if (!empty($kmByMonth)): ?>
// Chart 3: KM per Month (Line Chart)
const monthCtx = document.getElementById('monthChart').getContext('2d');
const monthChart = new Chart(monthCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($monthLabels); ?>,
        datasets: [{
            label: 'Kilometers per Month',
            data: <?php echo json_encode($monthData); ?>,
            backgroundColor: 'rgba(245, 158, 11, 0.2)',
            borderColor: 'rgba(245, 158, 11, 1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toFixed(1) + ' km';
                    }
                }
            }
        }
    }
});
<?php endif; ?>
</script>

<?php require_once '../includes/footer.php'; ?>
