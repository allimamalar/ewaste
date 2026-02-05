<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once 'database.php';

$email = $_GET['email'] ?? '';
$role = $_GET['role'] ?? '';

try {
    if ($role === 'user') {
        $stmt = $db->prepare("SELECT * FROM submissions WHERE user_email = ? ORDER BY timestamp DESC");
        $stmt->execute([$email]);
    } else if ($role === 'collector') {
        $stmt = $db->prepare("SELECT * FROM submissions WHERE status = 'Submitted' ORDER BY timestamp DESC");
        $stmt->execute();
    } else if ($role === 'authority') {
        $stmt = $db->prepare("SELECT * FROM submissions WHERE status IN ('Verified', 'Submitted') ORDER BY timestamp DESC");
        $stmt->execute();
    } else if ($role === 'recycler') {
        $stmt = $db->prepare("SELECT * FROM submissions WHERE status = 'Approved' ORDER BY timestamp DESC");
        $stmt->execute();
    } else {
        $stmt = $db->prepare("SELECT * FROM submissions ORDER BY timestamp DESC");
        $stmt->execute();
    }
    
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(["success" => true, "submissions" => $submissions]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Fetch failed: " . $e->getMessage()]);
}
?>
