<?php
/**
 * Delete Driving Experience Handler
 * 
 * Handles deletion of driving experiences with confirmation
 */

require_once '../config/db.php';
require_once '../includes/functions.php';

// Get database connection
$pdo = getDbConnection();

// Check if ID is provided
$encodedId = $_GET['id'] ?? $_POST['id'] ?? null;
$experienceId = decodeId($encodedId);

if (!$experienceId) {
    $_SESSION['error_message'] = "Invalid driving experience ID.";
    redirect('summary.php');
}

// Handle POST request (actual deletion)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $experience = DrivingExperience::findById($pdo, $experienceId);
    
    if (!$experience) {
        $_SESSION['error_message'] = "Driving experience not found.";
        redirect('summary.php');
    }
    
    if ($experience->delete($pdo)) {
        $_SESSION['success_message'] = "Driving experience deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to delete driving experience. Please try again.";
    }
    
    redirect('summary.php');
}

// Show confirmation page
$page_title = 'Delete Drive';
$experience = DrivingExperience::findById($pdo, $experienceId);

if (!$experience) {
    $_SESSION['error_message'] = "Driving experience not found.";
    redirect('summary.php');
}

// Get related data for display
$stmt = $pdo->prepare("
    SELECT 
        w.label as weather,
        t.label as traffic,
        s.name as supervisor
    FROM driving_experience de
    JOIN weather w ON de.weather_id = w.id
    JOIN traffic t ON de.traffic_id = t.id
    JOIN supervisor s ON de.supervisor_id = s.id
    WHERE de.id = :id
");
$stmt->execute([':id' => $experienceId]);
$details = $stmt->fetch();

require_once '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h1 class="card-title" style="color: var(--danger-color);">⚠️ Delete Driving Experience</h1>
    </div>
    
    <div class="alert alert-error">
        <strong>Warning:</strong> This action cannot be undone. Are you sure you want to delete this driving experience?
    </div>
    
    <div class="card" style="background: var(--bg-light); padding: 1.5rem; margin-bottom: 1.5rem;">
        <h3>Experience Details:</h3>
        <table style="width: 100%; margin-top: 1rem;">
            <tr>
                <td style="padding: 0.5rem; font-weight: 600;">Date & Time:</td>
                <td style="padding: 0.5rem;"><?php echo formatDateTime($experience->getDriveDatetime(), 'F d, Y \a\t H:i'); ?></td>
            </tr>
            <tr>
                <td style="padding: 0.5rem; font-weight: 600;">Distance:</td>
                <td style="padding: 0.5rem;"><?php echo formatNumber($experience->getKm()); ?> km</td>
            </tr>
            <tr>
                <td style="padding: 0.5rem; font-weight: 600;">Weather:</td>
                <td style="padding: 0.5rem;"><?php echo h($details['weather']); ?></td>
            </tr>
            <tr>
                <td style="padding: 0.5rem; font-weight: 600;">Traffic:</td>
                <td style="padding: 0.5rem;"><?php echo h($details['traffic']); ?></td>
            </tr>
            <tr>
                <td style="padding: 0.5rem; font-weight: 600;">Supervisor:</td>
                <td style="padding: 0.5rem;"><?php echo h($details['supervisor']); ?></td>
            </tr>
            <?php if ($experience->getNotes()): ?>
            <tr>
                <td style="padding: 0.5rem; font-weight: 600;">Notes:</td>
                <td style="padding: 0.5rem;"><?php echo h($experience->getNotes()); ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
    
    <form method="POST" action="delete_drive.php?id=<?php echo h($encodedId); ?>">
        <input type="hidden" name="id" value="<?php echo h($encodedId); ?>">
        <input type="hidden" name="confirm_delete" value="1">
        
        <div style="display: flex; gap: 1rem; justify-content: center;">
            <button type="submit" class="btn" style="background-color: var(--danger-color); color: white;">
                ✓ Yes, Delete This Experience
            </button>
            <a href="summary.php" class="btn btn-secondary">
                ✗ Cancel, Keep This Experience
            </a>
        </div>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
