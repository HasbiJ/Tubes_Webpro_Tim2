<?php
session_start();
include "config/koneksi.php";

// CEK LOGIN
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// READ DATA SISWA UNTUK TAMPILAN UTAMA
$q = mysqli_query($conn, "SELECT * FROM siswa WHERE id_user='$id_user'");
$data = mysqli_fetch_assoc($q);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFlex - Profile</title>
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="pengumpulan.css"> 
</head>
<body>
    <header class="header">
      <div class="brand">
        <img src="logo project website uid.png" alt="logo" class="logo">
        <div>
          <div>EduFlex</div>
          <div class="sub">Education Flexible</div>
        </div>
      </div>
      <nav>
          <ul>
              <li><a href="dashboard.php">Beranda</a></li>
              <li><a href="presensi.php">Presensi</a></li>
              <li><a href="jadwal.php">Jadwal</a></li>
              <li><a href="#">AI Helper</a></li>
              <li><a href="tampilanVideo.php">Video</a></li>
              <li><a href="tugas.php">Tugas</a></li>
          </ul>
      </nav>
      <section class="right-section">
        <div class="language">
          <img src="language_24dp_000000_FILL0_wght400_GRAD0_opsz24.png" alt="bahasa" class="logo-bahasa">
          <select class="select">
            <option>ID</option>
            <option>ENG</option>
          </select>
        </div>
        <div class="account">
          <a href="profile.php">
            <?php if(empty($data['foto'])){ ?>
                <img src="foto profile.jpg" class="avatar">
            <?php } else { ?>
                <img src="uploads/<?= $data['foto']; ?>" class="avatar">
            <?php } ?>
          </a>
          <a href="profile.php"><div><?= $data['nama']; ?></div></a>
        </div>
      </section>
    </header>

    <main class="konten-utama" style="padding: 50px 30px; max-width: 1000px; margin: 0 auto;">
        <div style="background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
            
            <h2 style="color:#0b1a3a; margin-top:0; margin-bottom:5px;">Kartu Pelajar</h2>
            <p style="color:#777; margin-bottom:30px; font-size:14px;">Informasi identitas pelajar Anda.</p>

            <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 25px; display: flex; gap: 30px; align-items: center; background: #fff; margin-bottom: 25px; position:relative;">
                
                <div style="text-align: center;">
                    <?php if(empty($data['foto'])): ?>
                        <img src="foto profile.jpg" style="width: 130px; height: 130px; border-radius: 50%; object-fit: cover; border: 4px solid #f1f5f9;">
                    <?php else: ?>
                        <img src="uploads/<?= $data['foto']; ?>" style="width: 130px; height: 130px; border-radius: 50%; object-fit: cover; border: 4px solid #f1f5f9;"><br>
                        <button type="button" onclick="hapusFotoProfil()" style="background:none; border:none; color:#e74c3c; font-size:12px; cursor:pointer; font-weight:bold; margin-top:8px;">Hapus Foto</button>
                    <?php endif; ?>
                </div>

                <div style="flex: 1; line-height: 1.8; color: #333;">
                    <h3 style="margin: 0 0 10px 0; font-size: 22px; color: #0b1a3a;"><?= $data['nama']; ?></h3>
                    <div style="font-size: 14px;"><strong>NISN:</strong> <?= $data['nisn'] ?: '-'; ?></div>
                    <div style="font-size: 14px;"><strong>Kelas:</strong> D3SI-49-01</div>
                    <div style="font-size: 14px;"><strong>Email:</strong> <?= $data['email'] ?: '-'; ?></div>
                    <div style="font-size: 14px;"><strong>No HP:</strong> <?= $data['no_hp'] ?: '-'; ?></div>
                    <div style="font-size: 14px;"><strong>Alamat:</strong> <?= $data['alamat'] ?: '-'; ?></div>
                </div>

                <div style="text-align: center; margin-left: auto;">
                    <img src="QR Code Pelajar" alt="QR Code Pelajar" style="width: 100px; height: 100px; opacity:0.8;">
                </div>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 25px;">
                <h4 style="margin: 0 0 20px 0; color:#0b1a3a;">Edit Biodata Profil</h4>
                
                <form id="formUpdateProfile" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 15px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="font-size:14px; font-weight:600; color:#4a5568;">Nama Lengkap</label>
                            <input type="text" name="nama" value="<?= $data['nama']; ?>" required style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:6px; margin-top:5px;">
                        </div>
                        <div>
                            <label style="font-size:14px; font-weight:600; color:#4a5568;">NISN</label>
                            <input type="text" name="nisn" value="<?= $data['nisn']; ?>" style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:6px; margin-top:5px;">
                        </div>
                        <div>
                            <label style="font-size:14px; font-weight:600; color:#4a5568;">Email</label>
                            <input type="email" name="email" value="<?= $data['email']; ?>" style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:6px; margin-top:5px;">
                        </div>
                        <div>
                            <label style="font-size:14px; font-weight:600; color:#4a5568;">No HP</label>
                            <input type="text" name="no_hp" value="<?= $data['no_hp']; ?>" style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:6px; margin-top:5px;">
                        </div>
                    </div>
                    
                    <div>
                        <label style="font-size:14px; font-weight:600; color:#4a5568;">Alamat Rumah</label>
                        <textarea name="alamat" rows="3" style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:6px; margin-top:5px; resize: none;"><?= $data['alamat']; ?></textarea>
                    </div>

                    <div>
                        <label style="font-size:14px; font-weight:600; color:#4a5568;">Ganti Foto Baru</label>
                        <input type="file" name="foto_baru" style="width:100%; padding:8px; border:1px dashed #cbd5e1; border-radius:6px; background:#fff; margin-top:5px;">
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <button type="submit" style="background: #b3541e; color: white; padding: 10px 25px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 14px;">Simpan Perubahan</button>
                        <button type="button" style="background: #fff; color: #4a5568; padding: 10px 20px; border: 1px solid #cbd5e1; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 14px;">Tampilkan Kode QR</button>
                    </div>
                </form>
            </div>

        </div>
    </main>

    <footer style="text-align: center; padding: 20px; font-size: 12px; color: #777; border-top: 1px solid #e2e8f0; margin-top: 50px;">
        <span style="margin: 0 10px;">Navigasi</span> | <span style="margin: 0 10px;">Sumber Daya</span> | <span style="margin: 0 10px;">Hubungi Kami</span>
    </footer>

    <script>
    // A. FETCH UPDATE PROFILE & UPLOAD FOTO
    document.getElementById('formUpdateProfile').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('api_profile.php?action=update', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if(data.status === 'success') {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            window.location.reload();
        });
    });

    // B. FETCH HAPUS FOTO PROFIL
    function hapusFotoProfil() {
        if(confirm('Apakah Anda yakin ingin menghapus foto profil ini?')) {
            fetch('api_profile.php?action=hapus_foto', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if(data.status === 'success') {
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.location.reload();
            });
        }
    }
    </script>
</body>
</html>