<?php
require '../config.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET' && $action === '') {
    $result = $conn->query("SELECT * FROM inventory ORDER BY id ASC");
    $data = [];
    while ($row = $result->fetch_assoc()) $data[] = $row;
    sendJson(["success" => true, "data" => $data]);
}

if ($method === 'POST' && $action === 'create') {
    $d = getInput();
    $stmt = $conn->prepare("INSERT INTO inventory (name, type, status, price) VALUES (?, ?, ?, ?)");
    $price = intval($d['price']);
    $stmt->bind_param("sssi", $d['name'], $d['type'], $d['status'], $price);
    $stmt->execute();
    sendJson(["success" => true, "id" => $stmt->insert_id]);
}

if ($method === 'POST' && $action === 'update') {
    $d = getInput();
    $id = intval($d['id']);
    $price = intval($d['price']);
    $stmt = $conn->prepare("UPDATE inventory SET name=?, type=?, status=?, price=? WHERE id=?");
    $stmt->bind_param("sssii", $d['name'], $d['type'], $d['status'], $price, $id);
    $stmt->execute();
    sendJson(["success" => true]);
}

if ($method === 'POST' && $action === 'delete') {
    $d = getInput();
    $id = intval($d['id']);
    $conn->query("DELETE FROM inventory WHERE id=$id");
    sendJson(["success" => true]);
}

sendJson(["success" => false, "message" => "Aksi tidak dikenali."]);
