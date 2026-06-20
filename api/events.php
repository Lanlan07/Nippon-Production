<?php
require '../config.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// =============================================
// GET - List semua event
// =============================================
if ($method === 'GET' && $action === '') {
    $result = $conn->query("SELECT * FROM events ORDER BY id ASC");
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
    sendJson(["success" => true, "data" => $events]);
}

// =============================================
// POST - Buat order baru (customer)
// =============================================
if ($method === 'POST' && $action === 'create') {
    $d = getInput();
    $estimated = intval($d['estimated'] ?? 5000000);

    $stmt = $conn->prepare("INSERT INTO events (customer, event_name, type, date, location, status, kamera, gimbal, drone, led, operator, durasi, estimated, dp_paid, remaining, payment_status)
        VALUES (?, ?, ?, ?, ?, 'Menunggu Persetujuan', ?, ?, ?, ?, ?, ?, ?, 0, ?, 'Belum Bayar')");
    $stmt->bind_param(
        "sssssiiiiiiii",
        $d['customer'], $d['event_name'], $d['type'], $d['date'], $d['location'],
        $d['kamera'], $d['gimbal'], $d['drone'], $d['led'], $d['operator'], $d['durasi'],
        $estimated, $estimated
    );

    if ($stmt->execute()) {
        sendJson(["success" => true, "id" => $stmt->insert_id]);
    } else {
        sendJson(["success" => false, "message" => $stmt->error]);
    }
}

// =============================================
// POST - Approve order (admin) + auto jadwal kru default
// =============================================
if ($method === 'POST' && $action === 'approve') {
    $d = getInput();
    $id = intval($d['id']);

    $conn->query("UPDATE events SET status='Menunggu Pembayaran', dp_paid=0, remaining=estimated WHERE id=$id");

    // Jadwalkan kru default (Ahmad Fauzi) otomatis, seperti versi awal
    $eventRes = $conn->query("SELECT * FROM events WHERE id=$id");
    $event = $eventRes->fetch_assoc();
    $crewRes = $conn->query("SELECT * FROM crew WHERE name='Ahmad Fauzi' LIMIT 1");
    $crew = $crewRes->fetch_assoc();

    if ($event && $crew) {
        $exists = $conn->query("SELECT id FROM assignments WHERE event_id=$id AND crew_id={$crew['id']}");
        if ($exists->num_rows === 0) {
            $conn->query("INSERT INTO assignments (crew_id, event_id, date, salary, status) VALUES ({$crew['id']}, $id, '{$event['date']}', {$crew['salary_per_event']}, 'Scheduled')");
            $conn->query("INSERT INTO attendance (crew_id, event_id, date, checkin, checkout, status) VALUES ({$crew['id']}, $id, '{$event['date']}', '-', '-', 'Scheduled')");
        }
    }

    sendJson(["success" => true]);
}

// =============================================
// POST - Reject order (admin)
// =============================================
if ($method === 'POST' && $action === 'reject') {
    $d = getInput();
    $id = intval($d['id']);
    $conn->query("UPDATE events SET status='Dibatalkan' WHERE id=$id");
    sendJson(["success" => true]);
}

// =============================================
// POST - Konfirmasi pembayaran DP (admin)
// =============================================
if ($method === 'POST' && $action === 'confirm_payment') {
    $d = getInput();
    $id = intval($d['id']);

    $eventRes = $conn->query("SELECT * FROM events WHERE id=$id");
    $event = $eventRes->fetch_assoc();
    if (!$event) sendJson(["success" => false, "message" => "Event tidak ditemukan"]);

    $dp = round($event['estimated'] * 0.3);
    $remaining = $event['estimated'] - $dp;

    $conn->query("UPDATE events SET dp_paid=$dp, remaining=$remaining, status='DP Dibayar', payment_status='DP Dibayar' WHERE id=$id");

    $stmt = $conn->prepare("INSERT INTO payments (event_id, event_name, customer, amount, type, date, status) VALUES (?, ?, ?, ?, 'DP', CURDATE(), 'Diverifikasi')");
    $stmt->bind_param("issi", $id, $event['event_name'], $event['customer'], $dp);
    $stmt->execute();

    sendJson(["success" => true, "dp" => $dp, "remaining" => $remaining]);
}

// =============================================
// POST - Selesaikan event (admin)
// =============================================
if ($method === 'POST' && $action === 'complete') {
    $d = getInput();
    $id = intval($d['id']);

    $eventRes = $conn->query("SELECT * FROM events WHERE id=$id");
    $event = $eventRes->fetch_assoc();
    if (!$event) sendJson(["success" => false, "message" => "Event tidak ditemukan"]);
    if ($event['remaining'] > 0) {
        sendJson(["success" => false, "message" => "Event belum lunas!"]);
    }

    $conn->query("UPDATE events SET status='Selesai', payment_status='Lunas' WHERE id=$id");
    $conn->query("UPDATE attendance SET status='Hadir', checkin='08:00', checkout='17:00' WHERE event_id=$id AND status='Scheduled'");
    $conn->query("UPDATE assignments SET status='Completed' WHERE event_id=$id");

    sendJson(["success" => true]);
}

sendJson(["success" => false, "message" => "Aksi tidak dikenali."]);
