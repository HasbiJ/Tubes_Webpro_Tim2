<?php
header('Content-Type: application/json');
include "../config/koneksi.php";

// 1. Otorisasi API Key
$headers = getallheaders();
if (!isset($headers['API-KEY']) || $headers['API-KEY'] !== 'EduFlexKey2026') {
    die(json_encode(['error' => 'Unauthorized']));
}

// 2. Ambil input JSON
$input = json_decode(file_get_contents('php://input'), true);

// 3. Tentukan Method (Ambil dari URL jika ada, atau dari server)
$method = $_GET['method'] ?? $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET': // Read
        $res = mysqli_query($conn, "SELECT * FROM jadwal");
        echo json_encode(mysqli_fetch_all($res, MYSQLI_ASSOC));
        break;

    case 'POST': // Create
        $m = $input['nama_mapel'];
        $w = $input['waktu'];
        // Pastikan nama kolom sesuai database Anda
        $query = mysqli_query($conn, "INSERT INTO jadwal (id_kelas, id_mapel, hari, jam_mulai, jam_selesai, ruang) 
                                      VALUES ('{$input['id_kelas']}', '{$input['id_mapel']}', '{$input['hari']}', 
                                      '{$input['jam_mulai']}', '{$input['jam_selesai']}', '{$input['ruang']}')");
        echo json_encode(['status' => $query ? 'sukses' : 'failed']);
        break;

    case 'DELETE': // Delete
        $id = $input['id_jadwal']; 
        $query = mysqli_query($conn, "DELETE FROM jadwal WHERE id_jadwal = '$id'");
        echo json_encode(['status' => $query ? 'sukses' : 'failed']);
        break;

    case 'PUT': // Update
        $id = $input['id_jadwal'];
        // Tambahkan logic update sesuai field yang dikirim
        $query = mysqli_query($conn, "UPDATE jadwal SET id_kelas='{$input['id_kelas']}', id_mapel='{$input['id_mapel']}' WHERE id_jadwal='$id'");
        echo json_encode(['status' => $query ? 'sukses' : 'failed']);
        break;
}
?>