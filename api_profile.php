<?php
header("Content-Type: application/json");
session_start();
include "config/koneksi.php";

if(!isset($_SESSION['id_user'])){
    echo json_encode(['status'=>'error','message'=>'Belum login']);
    exit;
}

$id_user = $_SESSION['id_user'];
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// =====================
// READ DATA (GET)
// =====================
if($method == 'GET'){
    $q = mysqli_query($conn, "SELECT * FROM siswa WHERE id_user='$id_user'");
    
    if(mysqli_num_rows($q) > 0){
        $data = mysqli_fetch_assoc($q);
        echo json_encode(['status'=>'success','data'=>$data]);
    } else {
        echo json_encode(['status'=>'success','data'=>null]);
    }
    exit;
}

// =====================
// UPDATE PROFILE
// =====================
if($method == 'POST' && $action == 'update'){
    $nama   = $_POST['nama'] ?? '';
    $nisn   = $_POST['nisn'] ?? '';
    $email  = $_POST['email'] ?? '';
    $no_hp  = $_POST['no_hp'] ?? '';
    $alamat = $_POST['alamat'] ?? '';

    // ambil foto lama
    $q = mysqli_query($conn, "SELECT foto FROM siswa WHERE id_user='$id_user'");
    $data = mysqli_fetch_assoc($q);
    $foto_lama = $data['foto'] ?? '';

    $nama_foto = $foto_lama;

    // upload foto
    if(!empty($_FILES['foto']['name'])){
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nama_foto = "foto_".time().".".$ext;

        if(!is_dir("uploads")){
            mkdir("uploads",0777,true);
        }

        move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/".$nama_foto);

        if(!empty($foto_lama) && file_exists("uploads/".$foto_lama)){
            unlink("uploads/".$foto_lama);
        }
    }

    // cek data sudah ada atau belum
    $cek = mysqli_query($conn, "SELECT * FROM siswa WHERE id_user='$id_user'");

    if(mysqli_num_rows($cek) > 0){
        $query = "UPDATE siswa SET 
            nama='$nama',
            nisn='$nisn',
            email='$email',
            no_hp='$no_hp',
            alamat='$alamat',
            foto='$nama_foto'
            WHERE id_user='$id_user'";
    } else {
        $query = "INSERT INTO siswa (id_user,nama,nisn,email,no_hp,alamat,foto)
            VALUES ('$id_user','$nama','$nisn','$email','$no_hp','$alamat','$nama_foto')";
    }

    if(mysqli_query($conn,$query)){
        echo json_encode(['status'=>'success','message'=>'Berhasil update']);
    } else {
        echo json_encode(['status'=>'error','message'=>'Gagal']);
    }
    exit;
}

// =====================
// DELETE FOTO
// =====================
if($method == 'POST' && $action == 'delete_foto'){
    $q = mysqli_query($conn, "SELECT foto FROM siswa WHERE id_user='$id_user'");
    $data = mysqli_fetch_assoc($q);

    if(!empty($data['foto'])){
        if(file_exists("uploads/".$data['foto'])){
            unlink("uploads/".$data['foto']);
        }

        mysqli_query($conn, "UPDATE siswa SET foto='' WHERE id_user='$id_user'");
    }

    echo json_encode(['status'=>'success','message'=>'Foto dihapus']);
    exit;
}