<?php
header("Content-Type: application/json");
session_start();
include "config/koneksi.php";

// Validasi Otentikasi & Session
if(!isset($_SESSION['id_user'])){
    echo json_encode(['status' => 'error', 'message' => 'Anda harus login terlebih dahulu']);
    exit;
}

$id_user = $_SESSION['id_user'];
$query_user = mysqli_query($conn, "SELECT id_siswa FROM siswa WHERE id_user='$id_user'");
$data_user = mysqli_fetch_assoc($query_user);
$id_siswa = $data_user['id_siswa'];

// MENGAMBIL METHOD REQUEST (GET / POST)
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// ====================================================================
// 1. METHOD GET: UNTUK SELECT/READ DATA (Membaca Status Tugas)
// ====================================================================
if ($method == 'GET') {
    $id_tugas = $_GET['id_tugas'] ?? '';
    
    if($id_tugas == "") {
        echo json_encode(['status' => 'error', 'message' => 'ID Tugas tidak disertakan']);
        exit;
    }
    
    // Query SELECT CRUD
    $cek = mysqli_query($conn, "SELECT file_tugas, tgl_kumpul FROM pengumpulan_tugas WHERE id_tugas='$id_tugas' AND id_siswa='$id_siswa'");
    
    if(mysqli_num_rows($cek) > 0){
        $data = mysqli_fetch_assoc($cek);
        echo json_encode([
            'status' => 'success', 
            'sudah_kumpul' => true, 
            'data' => $data
        ]);
    } else {
        echo json_encode([
            'status' => 'success', 
            'sudah_kumpul' => false
        ]);
    }
    exit;
}

// ====================================================================
// 2. METHOD POST: UNTUK CREATE, UPDATE, & DELETE DATA
// ====================================================================
if ($method == 'POST') {
    $id_tugas = $_POST['id_tugas'] ?? '';
    
    if($id_tugas == "") {
        echo json_encode(['status' => 'error', 'message' => 'ID Tugas tidak disertakan']);
        exit;
    }

    // ----------------================================================
    // LOGIKA BARU: JIKA ACTION ADALAH DELETE (Membatalakan Tugas)
    // ----------------================================================
    if ($action == 'delete') {
        // Ambil nama file lama terlebih dahulu untuk dihapus dari folder server
        $cek_file = mysqli_query($conn, "SELECT file_tugas FROM pengumpulan_tugas WHERE id_tugas='$id_tugas' AND id_siswa='$id_siswa'");
        
        if (mysqli_num_rows($cek_file) > 0) {
            $data_file = mysqli_fetch_assoc($cek_file);
            $nama_file_lama = $data_file['file_tugas'];
            $path_file = "uploads_tugas/" . $nama_file_lama;
            
            // Hapus file fisik dari folder uploads_tugas jika filenya ada
            if (file_exists($path_file) && !empty($nama_file_lama)) {
                unlink($path_file);
            }
            
            // Hapus data dari database (DELETE CRUD)
            $query_delete = mysqli_query($conn, "DELETE FROM pengumpulan_tugas WHERE id_tugas='$id_tugas' AND id_siswa='$id_siswa'");
            
            if ($query_delete) {
                echo json_encode(['status' => 'success', 'message' => 'Pengumpulan tugas berhasil dibatalkan dan berkas dihapus!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data pengumpulan dari database']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data pengumpulan tugas tidak ditemukan']);
        }
        exit;
    }

    // ----------------================================================
    // LOGIKA UPLOAD / UPDATE FILE (LOGIKA ASLI KAMU)
    // ----------------================================================
    $nama_file = $_FILES['file_resmi']['name'] ?? '';
    $tmp_file = $_FILES['file_resmi']['tmp_name'] ?? '';
    
    if($nama_file == "") {
        echo json_encode(['status' => 'error', 'message' => 'Data input tugas atau berkas tidak lengkap']);
        exit;
    }
    
    $ekstensi = pathinfo($nama_file, PATHINFO_EXTENSION);
    $nama_file_baru = "Tugas_" . $id_tugas . "_" . $id_siswa . "." . $ekstensi;
    $folder_tujuan = "uploads_tugas/" . $nama_file_baru;
    
    if (!is_dir('uploads_tugas')) {
        mkdir('uploads_tugas', 0777, true);
    }
    
    if(move_uploaded_file($tmp_file, $folder_tujuan)){
        // Cek apakah data sudah ada sebelumnya di database
        $cek = mysqli_query($conn, "SELECT * FROM pengumpulan_tugas WHERE id_tugas='$id_tugas' AND id_siswa='$id_siswa'");
        
        if(mysqli_num_rows($cek) > 0){
            // UPDATE CRUD
            $query = mysqli_query($conn, "UPDATE pengumpulan_tugas SET file_tugas='$nama_file_baru', tgl_kumpul=NOW() WHERE id_tugas='$id_tugas' AND id_siswa='$id_siswa'");
            $msg = "Berkas tugas berhasil diperbarui!";
        } else {
            // CREATE CRUD
            $query = mysqli_query($conn, "INSERT INTO pengumpulan_tugas (id_tugas, id_siswa, file_tugas, tgl_kumpul) VALUES ('$id_tugas', '$id_siswa', '$nama_file_baru', NOW())");
            $msg = "Tugas berhasil dikirim!";
        }
        
        if($query) {
            echo json_encode(['status' => 'success', 'message' => $msg]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data ke database']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengunggah berkas ke server']);
    }
    exit;
}
?>