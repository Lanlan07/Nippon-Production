<?php
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query("SELECT * FROM crew ORDER BY id ASC");
    $data = [];
    while ($row = $result->fetch_assoc()) $data[] = $row;
    sendJson(["success" => true, "data" => $data]);
}

sendJson(["success" => false, "message" => "Method tidak didukung."]);
