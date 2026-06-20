<?php
require '../config.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET' && $action === '') {
    $result = $conn->query("SELECT * FROM assignments ORDER BY id ASC");
    $data = [];
    while ($row = $result->fetch_assoc()) $data[] = $row;
    sendJson(["success" => true, "data" => $data]);
}

// POST - assign beberapa kru ke satu event
if ($method === 'POST' && $action === 'assign') {
    $d = getInput();
    $eventId = intval($d['event_id']);
    $crewIds = $d['crew_ids'] ?? [];

    $eventRes = $conn->query("SELECT * FROM events WHERE id=$eventId");
    $event = $eventRes->fetch_assoc();
    if (!$event) sendJson(["success" => false, "message" => "Event tidak ditemukan"]);

    $assigned = 0;
    foreach ($crewIds as $crewId) {
        $crewId = intval($crewId);
        $exists = $conn->query("SELECT id FROM assignments WHERE event_id=$eventId AND crew_id=$crewId");
        if ($exists->num_rows > 0) continue;

        $crewRes = $conn->query("SELECT * FROM crew WHERE id=$crewId");
        $crew = $crewRes->fetch_assoc();
        if (!$crew) continue;

        $conn->query("INSERT INTO assignments (crew_id, event_id, date, salary, status) VALUES ($crewId, $eventId, '{$event['date']}', {$crew['salary_per_event']}, 'Scheduled')");
        $conn->query("INSERT INTO attendance (crew_id, event_id, date, checkin, checkout, status) VALUES ($crewId, $eventId, '{$event['date']}', '-', '-', 'Scheduled')");
        $assigned++;
    }

    sendJson(["success" => true, "assigned" => $assigned]);
}

sendJson(["success" => false, "message" => "Aksi tidak dikenali."]);
