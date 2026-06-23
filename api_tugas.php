<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$conn = mysqli_connect("localhost", "root", "", "eduflex");

if (!$conn) {
    echo json_encode([
        "status" => "error",
        "message" => "Koneksi database gagal: " . mysqli_connect_error()
    ]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

/* Preflight */
if ($method == 'OPTIONS') {
    exit(0);
}

/* =========================
   GET -> Ambil data tugas
   ========================= */
if ($method == 'GET') {

    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $query = mysqli_query($conn, "SELECT * FROM tugas WHERE id_tugas='$id'");
        $data = mysqli_fetch_assoc($query);

        if ($data) {
            echo json_encode([
                "status" => "success",
                "data" => $data
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Data tugas tidak ditemukan"
            ]);
        }
    } else {
        $result = mysqli_query($conn, "SELECT * FROM tugas ORDER BY id_tugas DESC");

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        echo json_encode([
            "status" => "success",
            "data" => $data
        ]);
    }
}

/* =========================
   POST -> Tambah tugas
   ========================= */
elseif ($method == 'POST') {

    $id_mapel  = $_POST['id_mapel'] ?? '';
    $judul     = $_POST['judul'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    $deadline  = $_POST['deadline'] ?? '';

    if ($id_mapel == '' || $judul == '' || $deskripsi == '' || $deadline == '') {
        echo json_encode([
            "status" => "error",
            "message" => "Semua field wajib diisi"
        ]);
        exit;
    }

    $query = mysqli_query($conn, "
        INSERT INTO tugas (id_mapel, judul, deskripsi, deadline)
        VALUES ('$id_mapel', '$judul', '$deskripsi', '$deadline')
    ");

    if ($query) {
        echo json_encode([
            "status" => "success",
            "message" => "Tugas berhasil ditambahkan"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Gagal menambahkan tugas"
        ]);
    }
}

/* =========================
   PUT -> Update tugas
   ========================= */
elseif ($method == 'PUT') {

    parse_str(file_get_contents("php://input"), $putData);

    $id        = $putData['id_tugas'] ?? '';
    $id_mapel  = $putData['id_mapel'] ?? '';
    $judul     = $putData['judul'] ?? '';
    $deskripsi = $putData['deskripsi'] ?? '';
    $deadline  = $putData['deadline'] ?? '';

    if ($id == '' || $id_mapel == '' || $judul == '' || $deskripsi == '' || $deadline == '') {
        echo json_encode([
            "status" => "error",
            "message" => "Data update tidak lengkap"
        ]);
        exit;
    }

    $query = mysqli_query($conn, "
        UPDATE tugas SET
            id_mapel = '$id_mapel',
            judul = '$judul',
            deskripsi = '$deskripsi',
            deadline = '$deadline'
        WHERE id_tugas = '$id'
    ");

    if ($query) {
        echo json_encode([
            "status" => "success",
            "message" => "Tugas berhasil diupdate"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Gagal mengupdate tugas"
        ]);
    }
}

/* =========================
   DELETE -> Hapus tugas
   ========================= */
elseif ($method == 'DELETE') {

    parse_str(file_get_contents("php://input"), $deleteData);

    $id = $deleteData['id_tugas'] ?? '';

    if ($id == '') {
        echo json_encode([
            "status" => "error",
            "message" => "ID tugas tidak ditemukan"
        ]);
        exit;
    }

    $query = mysqli_query($conn, "DELETE FROM tugas WHERE id_tugas='$id'");

    if ($query) {
        echo json_encode([
            "status" => "success",
            "message" => "Tugas berhasil dihapus"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Gagal menghapus tugas"
        ]);
    }
}

/* =========================
   Method tidak dikenali
   ========================= */
else {
    echo json_encode([
        "status" => "error",
        "message" => "Method tidak didukung"
    ]);
}
?>