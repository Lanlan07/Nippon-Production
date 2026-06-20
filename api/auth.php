<?php
require '../config.php';

$action = $_GET['action'] ?? '';

// =============================================
// LOGIN
// =============================================
if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = getInput();
    $email = $conn->real_escape_string($input['email'] ?? '');
    $password = $input['password'] ?? '';

    $result = $conn->query("SELECT * FROM users WHERE email = '$email' LIMIT 1");

    if ($result && $row = $result->fetch_assoc()) {
        if ($password === $row['password']) {
            unset($row['password']);
            sendJson(["success" => true, "user" => $row]);
        } else {
            sendJson(["success" => false, "message" => "Email atau password salah!"]);
        }
    } else {
        sendJson(["success" => false, "message" => "Email atau password salah!"]);
    }
}

// =============================================
// REGISTER
// =============================================
if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = getInput();
    $name = $conn->real_escape_string($input['name'] ?? '');
    $email = $conn->real_escape_string($input['email'] ?? '');
    $phone = $conn->real_escape_string($input['phone'] ?? '');
    $password = $conn->real_escape_string($input['password'] ?? '');
    $instansi = $conn->real_escape_string($input['instansi'] ?? '-');

    $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($check && $check->num_rows > 0) {
        sendJson(["success" => false, "message" => "Email sudah terdaftar!"]);
    }

    $avatar = $conn->real_escape_string(implode('', array_map(fn($w) => $w[0] ?? '', explode(' ', $name))));

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, avatar, institution, phone) VALUES (?, ?, ?, 'customer', ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $email, $password, $avatar, $instansi, $phone);

    if ($stmt->execute()) {
        sendJson(["success" => true, "message" => "Akun berhasil didaftarkan."]);
    } else {
        sendJson(["success" => false, "message" => "Gagal mendaftar: " . $stmt->error]);
    }
}

sendJson(["success" => false, "message" => "Aksi tidak dikenali."]);
