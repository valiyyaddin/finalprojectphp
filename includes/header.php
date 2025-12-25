<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current page for navigation highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? h($page_title) . ' - ' : ''; ?>Supervised Driving Log</title>
    <link rel="stylesheet" href="css/style.css">
    <?php if (isset($use_chartjs) && $use_chartjs): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <?php endif; ?>
</head>
<body>
    <header class="site-header">
        <div class="container">
            <h1 class="site-title">🚗 Supervised Driving Experience Log</h1>
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php" class="<?php echo $current_page === 'index.php' ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="add_drive.php" class="<?php echo $current_page === 'add_drive.php' ? 'active' : ''; ?>">Add Drive</a></li>
                    <li><a href="summary.php" class="<?php echo $current_page === 'summary.php' ? 'active' : ''; ?>">Summary</a></li>
                    <li><a href="stats.php" class="<?php echo $current_page === 'stats.php' ? 'active' : ''; ?>">Statistics</a></li>
                    <li><a href="manage_variables.php" class="<?php echo $current_page === 'manage_variables.php' ? 'active' : ''; ?>">Manage</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <main class="main-content">
        <div class="container">
