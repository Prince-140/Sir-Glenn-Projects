<?php
// remove_appliance.php - Handles appliance removal
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Database configuration
$host = 'localhost';
$dbname = 'sensors';
$username = 'Prince';
$password = '';
$port = 3307;

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle the removal request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove'])) {
    $applianceId = intval($_POST['remove']);
    
    // Get appliance name for logging/message
    $stmt = $pdo->prepare("SELECT Name FROM appliances WHERE Id = ?");
    $stmt->execute([$applianceId]);
    $appliance = $stmt->fetch();
    
    if ($appliance) {
        // Delete the appliance
        $deleteStmt = $pdo->prepare("DELETE FROM appliances WHERE Id = ?");
        
        if ($deleteStmt->execute([$applianceId])) {
            // Log the activity
            $logStmt = $pdo->prepare("INSERT INTO activity_logs (user, action, appliance, timestamp) VALUES (?, ?, ?, NOW())");
            $logStmt->execute([
                $_SESSION['user'],
                'removed',
                $appliance['Name']
            ]);
            
            $_SESSION['message'] = "Appliance '{$appliance['Name']}' removed successfully!";
        } else {
            $_SESSION['message'] = "Failed to remove appliance!";
        }
    } else {
        $_SESSION['message'] = "Appliance not found!";
    }
    
    // Redirect back to the appliances page
    header("Location: dashboard.php#appliances");
    exit();
}

// If accessed directly without POST data, redirect to dashboard
header("Location: dashboard.php");
exit();
?>