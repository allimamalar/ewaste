<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once 'database.php';

$data = json_decode(file_get_contents("php://input"));

if (!$data->name || !$data->email || !$data->role || !$data->password) {
    echo json_encode(["success" => false, "message" => "Missing data"]);
    exit;
}

$hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);

try {
    $stmt = $db->prepare("INSERT INTO users (name, email, role, wallet, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$data->name, $data->email, $data->role, $data->wallet, $hashedPassword]);
    echo json_encode(["success" => true, "message" => "User registered successfully"]);
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo json_encode(["success" => false, "message" => "Email already exists"]);
    } else {
        echo json_encode(["success" => false, "message" => "Registration failed: " . $e->getMessage()]);
    }
}
?>
