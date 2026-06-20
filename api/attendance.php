<?php
require '../config.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET' && $action === '') {
    $result = $conn->query("SELECT * FROM attendance ORDER BY id ASC");
    $data = [];
    while ($row = $result->fetch_assoc()) $data[] = $row;
    sendJson(["success" => true, "data" => $data]);
}

// POST - crew check-in (absen)
if ($method === 'POST' && $action === 'checkin') {
    $d = getInput();
    $id = intval($d['id']);
    $now = date("H:i");

    $conn->query("UPDATE attendance SET status='Hadir', checkin='$now', checkout='17:00' WHERE id=$id");
    sendJson(["success" => true]);
}

sendJson(["success" => false, "message" => "Aksi tidak dikenali."]);
