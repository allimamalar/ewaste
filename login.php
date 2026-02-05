<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once 'database.php';

$data = json_decode(file_get_contents("php://input"));

if (!$data->email || !$data->password || !$data->role) {
    echo json_encode(["success" => false, "message" => "Missing data"]);
    exit;
}

$stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
$stmt->execute([$data->email, $data->role]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($data->password, $user['password'])) {
    unset($user['password']); // Don't send password back
    echo json_encode(["success" => true, "user" => $user]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid credentials or role"]);
}
?>
