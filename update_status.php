<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once 'database.php';

$data = json_decode(file_get_contents("php://input"));

if (!$data->id || !$data->status) {
    echo json_encode(["success" => false, "message" => "Missing data"]);
    exit;
}

$newHash = '0x' . bin2hex(random_bytes(16));

try {
    $db->beginTransaction();

    $stmt = $db->prepare("UPDATE submissions SET status = ?, blockchain_hash = ? WHERE id = ?");
    $stmt->execute([$data->status, $newHash, $data->id]);

    $stmtLedger = $db->prepare("INSERT INTO ledger (submission_id, action, status, blockchain_hash) VALUES (?, ?, ?, ?)");
    $stmtLedger->execute([$data->id, 'Status Update', $data->status, $newHash]);

    $db->commit();
    echo json_encode(["success" => true, "hash" => $newHash]);
} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(["success" => false, "message" => "Update failed: " . $e->getMessage()]);
}
?>
