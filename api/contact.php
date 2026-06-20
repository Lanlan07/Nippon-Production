<?php
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d = getInput();
    $stmt = $conn->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $d['name'], $d['email'], $d['message']);
    if ($stmt->execute()) {
        sendJson(["success" => true]);
    } else {
        sendJson(["success" => false, "message" => $stmt->error]);
    }
}

sendJson(["success" => false, "message" => "Method tidak didukung."]);
