<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once 'database.php';

$data = json_decode(file_get_contents("php://input"));

if (!$data->user_email || !$data->device_name) {
    echo json_encode(["success" => false, "message" => "Missing data"]);
    exit;
}

$submissionId = 'EW' . rand(100000, 999999);
$blockchainHash = '0x' . bin2hex(random_bytes(16));

try {
    $db->beginTransaction();

    $stmt = $db->prepare("INSERT INTO submissions (id, user_email, device_name, category, ownership, location, image, blockchain_hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $submissionId,
        $data->user_email,
        $data->device_name,
        $data->category,
        $data->ownership,
        $data->location,
        $data->image,
        $blockchainHash
    ]);

    $stmtLedger = $db->prepare("INSERT INTO ledger (submission_id, action, status, blockchain_hash) VALUES (?, ?, ?, ?)");
    $stmtLedger->execute([$submissionId, 'Submission', 'Submitted', $blockchainHash]);

    $db->commit();
    echo json_encode(["success" => true, "id" => $submissionId, "hash" => $blockchainHash]);
} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(["success" => false, "message" => "Submission failed: " . $e->getMessage()]);
}
?>
