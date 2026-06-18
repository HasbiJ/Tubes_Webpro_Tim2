<?php
include "config/koneksi.php";

if(!isset($_GET['id_siswa'])){
    echo "<h3>Akses Ditolak: ID Siswa tidak ditemukan.</h3>";
    exit;
}

$id_siswa = mysqli_real_escape_string($conn, $_GET['id_siswa']);

// Ambil data siswa untuk tampilan publik scan
$query = mysqli_query($conn, "
    SELECT siswa.*, kelas.nama_kelas 
    FROM siswa 
    LEFT JOIN kelas ON siswa.id_kelas = kelas.id_kelas
    WHERE siswa.id_siswa='$id_siswa'
");
$data = mysqli_fetch_assoc($query);

if(!$data){
    echo "<h3>Data Pelajar Tidak Ditemukan!</h3>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFlex - Verifikasi Kartu Pelajar Digital</title>
    <style>
        body { font-family: sans-serif; background: #f5f5f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; max-width: 350px; width: 90%; }
        .avatar { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #0b1a3a; margin-bottom: 15px; }
        h2 { color: #0b1a3a; margin: 5px 0; font-size: 22px; }
        .status-badge { background: #2ecc71; color: white; padding: 5px 15px; border-radius: 20px; display: inline-block; font-size: 12px; font-weight: 600; margin-top: 5px; margin-bottom: 15px;}
        .info { text-align: left; background: #f9f9f9; padding: 15px; border-radius: 8px; font-size: 14px; }
        .info p { margin: 8px 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .footer-logo { margin-top: 20px; font-size: 12px; color: #888; font-weight: bold; }
    </style>
</head>
<body>

<div class="card">
    <?php if(empty($data['foto'])){ ?>
        <img src="foto profile.jpg" class="avatar">
    <?php } else { ?>
        <img src="uploads/<?= $data['foto']; ?>" class="avatar">
    <?php } ?>
    
    <h2><?= $data['nama']; ?></h2>
    <div class="status-badge">✓ PELAJAR AKTIF</div>
    
    <div class="info">
        <p><strong>NISN:</strong> <?= $data['nisn'] ? $data['nisn'] : '-'; ?></p>
        <p><strong>Kelas:</strong> <?= $data['nama_kelas'] ? $data['nama_kelas'] : 'Belum Ditentukan'; ?></p>
        <p><strong>Sekolah:</strong> SMA Negeri 1 Jakarta</p>
        <p><strong>Email:</strong> <?= $data['email'] ? $data['email'] : '-'; ?></p>
    </div>
    
    <div class="footer-logo">EduFlex - Education Flexible</div>
</div>

</body>
</html>