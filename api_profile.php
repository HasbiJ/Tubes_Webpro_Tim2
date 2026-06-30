<?php
header("Content-Type: application/json");
session_start();
include "config/koneksi.php";

// Validasi Otentikasi & Session Login
if (!isset($_SESSION['id_user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Anda harus login terlebih dahulu']);
    exit;
}

$id_user = $_SESSION['id_user'];
$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// ====================================================================
// 1. API: HAPUS FOTO PROFIL (POST)
// ====================================================================
if ($method == 'POST' && $action == 'hapus_foto') {
    $q = mysqli_query($conn, "SELECT foto FROM siswa WHERE id_user='$id_user'");
    $data = mysqli_fetch_assoc($q);
    $foto_lama = $data['foto'] ?? '';

    // Lenyapkan file fisik dari folder uploads jika ada
    if (!empty($foto_lama) && file_exists("uploads/" . $foto_lama)) {
        unlink("uploads/" . $foto_lama);
    }

    // Update kolom database jadi kosong (DELETE/UPDATE CRUD)
    $query_update = mysqli_query($conn, "UPDATE siswa SET foto='' WHERE id_user='$id_user'");

    if ($query_update) {
        echo json_encode(['status' => 'success', 'message' => 'Foto profil berhasil dihapus!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data foto di database']);
    }
    exit;
}

// ====================================================================
// 2. API: UPDATE BIODATA PROFILE & UPLOAD FOTO BARU (POST)
// ====================================================================
if ($method == 'POST' && $action == 'update') {
    $nama   = mysqli_real_escape_string($conn, $_POST['nama'] ?? '');
    $nisn   = mysqli_real_escape_string($conn, $_POST['nisn'] ?? '');
    $email  = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $no_hp  = mysqli_real_escape_string($conn, $_POST['no_hp'] ?? '');
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat'] ?? '');

    if (empty($nama)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama lengkap tidak boleh kosong']);
        exit;
    }

    // Ambil data foto lama untuk cadangan pencegahan tindihan
    $q = mysqli_query($conn, "SELECT foto FROM siswa WHERE id_user='$id_user'");
    $data = mysqli_fetch_assoc($q);
    $foto_lama = $data['foto'] ?? '';
    $nama_foto_final = $foto_lama;

    // Logika upload foto baru jika user memilih file baru
    if (!empty($_FILES['foto_baru']['name'])) {
        $nama_file_asal = $_FILES['foto_baru']['name'];
        $tmp_file       = $_FILES['foto_baru']['tmp_name'];

        $ext = pathinfo($nama_file_asal, PATHINFO_EXTENSION);
        $nama_foto_final = "foto_" . time() . "." . $ext;

        if (!is_dir("uploads")) {
            mkdir("uploads", 0777, true);
        }

        if (move_uploaded_file($tmp_file, "uploads/" . $nama_foto_final)) {
            // Jika upload sukses, hapus file foto lama dari folder server
            if (!empty($foto_lama) && file_exists("uploads/" . $foto_lama)) {
                unlink("uploads/" . $foto_lama);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengunggah berkas foto baru ke server']);
            exit;
        }
    }

    // Eksekusi Update ke Database (UPDATE CRUD)
    $query_save = mysqli_query($conn, "
        UPDATE siswa SET
        nama='$nama',
        nisn='$nisn',
        email='$email',
        no_hp='$no_hp',
        alamat='$alamat',
        foto='$nama_foto_final'
        WHERE id_user='$id_user'
    ");

    if ($query_save) {
        echo json_encode(['status' => 'success', 'message' => 'Profil dan biodata Anda berhasil diperbarui!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan perubahan biodata ke database']);
    }
    exit;
}

// Jika request action tidak dikenali
echo json_encode(['status' => 'error', 'message' => 'Aksi atau metode request tidak valid']);
exit;
?>