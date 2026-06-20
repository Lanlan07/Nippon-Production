<?php
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query("SELECT id, name, email, role, avatar, institution, phone, position FROM users");
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    sendJson(["success" => true, "data" => $users]);
}

sendJson(["success" => false, "message" => "Method tidak didukung."]);
