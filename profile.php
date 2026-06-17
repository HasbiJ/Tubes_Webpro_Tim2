<?php
session_start();
include "config/koneksi.php";

// Proteksi Akun Siswa
if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// Ambil data siswa asli hasil JOIN dari database (READ)
$query = mysqli_query($conn, "
    SELECT siswa.*, users.username, kelas.nama_kelas 
    FROM siswa 
    JOIN users ON siswa.id_user = users.id_user
    LEFT JOIN kelas ON siswa.id_kelas = kelas.id_kelas
    WHERE siswa.id_user='$id_user'
");
$data = mysqli_fetch_assoc($query);

// Fallback jika nama kelas kosong di database database
$kelas_siswa = isset($data['nama_kelas']) ? $data['nama_kelas'] : 'Belum Ditentukan';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFlex - Profil Siswa</title>
    <link rel="stylesheet" href="profile.css">
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

  <main class="main-container">
    <div class="main-card">
      <h2 class="title">Kartu Pelajar</h2>
      <p class="desc">Informasi identitas pelajar Anda.</p>

      <section class="student-card">
        <?php if(empty($data['foto'])){ ?>
            <img src="foto profile.jpg" alt="Foto Pelajar" class="student-photo">
        <?php } else { ?>
            <img src="uploads/<?= $data['foto']; ?>" alt="Foto Pelajar" class="student-photo">
        <?php } ?>

        <div class="student-info">
          <h3 class="student-name"><?= $data['nama']; ?></h3>
          <p><strong>NISN:</strong> <?= $data['nisn'] ? $data['nisn'] : '-'; ?></p>
          <p><strong>Kelas:</strong> <?= $kelas_siswa; ?></p>
          <p><strong>Email:</strong> <?= $data['email'] ? $data['email'] : '-'; ?></p>
          <p><strong>No HP:</strong> <?= $data['no_hp'] ? $data['no_hp'] : '-'; ?></p>
          <p><strong>Alamat:</strong> <?= $data['alamat'] ? $data['alamat'] : '-'; ?></p>
        </div>

        <!-- BLOK QR CODE LOKAL (SUDAH BERSIH TIDAK DOUBLE & ANTI-GAGAL) -->
        <div class="qr">
            <img src="qrcode_default.png" alt="QR Code Pelajar" style="width: 100px; height: 100px; display: block;">
        </div>
      </section>

      <div class="button-area">
        <button class="download-btn" onclick="toggleEditForm()">Edit Biodata Profil</button>
        <button class="qr-btn">Tampilkan Kode QR</button>
      </div>

      <div id="editProfileSection" style="display: none; margin-top: 35px; padding-top: 25px; border-top: 1px solid #eee;">
          <h3 style="color: #0b1a3a; margin-bottom: 15px;">Formulir Perbarui Data Diri</h3>
          
          <form id="formUpdateProfil" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 15px; max-width: 600px;">
              <div>
                  <label style="display: block; margin-bottom: 5px; font-weight: 600;">Nama Lengkap:</label>
                  <input type="text" name="nama" value="<?= $data['nama']; ?>" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px;">
              </div>

              <div>
                  <label style="display: block; margin-bottom: 5px; font-weight: 600;">NISN Pelajar:</label>
                  <input type="text" name="nisn" value="<?= $data['nisn']; ?>" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px;" placeholder="Masukkan 10 digit NISN Anda">
              </div>
              <div>
                  <label style="display: block; margin-bottom: 5px; font-weight: 600;">Email Aktif:</label>
                  <input type="email" name="email" value="<?= $data['email']; ?>" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px;">
              </div>
              <div>
                  <label style="display: block; margin-bottom: 5px; font-weight: 600;">Nomor Handphone:</label>
                  <input type="text" name="no_hp" value="<?= $data['no_hp']; ?>" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px;">
              </div>
              <div>
                  <label style="display: block; margin-bottom: 5px; font-weight: 600;">Alamat Rumah:</label>
                  <textarea name="alamat" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px; font-family: sans-serif;"><?= $data['alamat']; ?></textarea>
              </div>
              <div>
                  <label style="display: block; margin-bottom: 5px; font-weight: 600;">Ganti Foto Profil (Opsional):</label>
                  <input type="file" name="foto_baru" accept="image/*">
                  
                  <?php if(!empty($data['foto'])){ ?>
                      <button type="button" id="btnHapusFoto" style="margin-top: 10px; padding: 6px 12px; background: #e74c3c; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 0.85em; display: block; font-weight: 600;">
                          Hapus Foto Profil Saat Ini
                      </button>
                  <?php } ?>
              </div>
              
              <div style="display: flex; gap: 10px; margin-top: 10px;">
                  <button type="submit" style="padding: 10px 20px; background: #10cd30; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Simpan Perubahan</button>
                  <button type="button" onclick="toggleEditForm()" style="padding: 10px 20px; background: #ccc; color: #333; border: none; border-radius: 6px; cursor: pointer;">Batal</button>
              </div>
          </form>
      </div>

    </div>
  </main>

  <footer class="gridfooter">
    <div class="leftfooter">
      <div class="textfooter">
        <div>Navigasi</div>
        <div>Sumber Daya</div>
        <div>Hubungi Kami</div>
      </div>
    </div>
    <div class="rightfooter">
      <div class="logososmed">
        <img src="instagram.png" alt="ig">
        <img src="facebook (1).png" alt="fb">
        <img src="youtube.png" alt="yt">
        <img src="twitter.png" alt="x">
        <img src="linkedin.png" alt="linkedin">
      </div>
    </div>
  </footer>

  <script>
    function toggleEditForm() {
        const formSection = document.getElementById('editProfileSection');
        if(formSection.style.display === 'none') {
            formSection.style.display = 'block';
            formSection.scrollIntoView({ behavior: 'smooth' });
        } else {
            formSection.style.display = 'none';
        }
    }

    const formProfil = document.getElementById('formUpdateProfil') || document.getElementById('formUpdateProfile');

    // AJAX FETCH API UNTUK PROSES UPDATE PROFIL (UPDATE CRUD)
    if(formProfil) {
        formProfil.addEventListener('submit', function(e) {
            e.preventDefault(); 
            
            let formData = new FormData(this);
            
            fetch('api_profil.php?action=update_profile', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text()) 
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    alert(data.message);
                    if(data.status === 'success') {
                        location.reload(); 
                    }
                } catch(err) {
                    console.error("Respon Server Bukan JSON:", text);
                    alert("Terjadi kesalahan sistem pada backend API.");
                }
            })
            .catch(error => {
                console.error('Error Fetch:', error);
                alert("Gagal terhubung ke server.");
            });
        });
    } else {
        console.error("Elemen form profil tidak ditemukan di halaman HTML ini!");
    }

    // AJAX FETCH API UNTUK PROSES DELETE FOTO PROFIL (DELETE CRUD)
    const btnHapus = document.getElementById('btnHapusFoto');
    if(btnHapus) {
        btnHapus.addEventListener('click', function() {
            if(confirm('Apakah Anda yakin ingin menghapus foto profil saat ini?')) {
                fetch('api_profil.php?action=delete_foto', {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if(data.status === 'success') {
                        location.reload(); 
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("Gagal terhubung ke server.");
                });
            }
        });
    }

    // Daftarkan aksi klik untuk tombol QR
    const btnQR = document.querySelector('.qr-btn');
    if(btnQR) {
        btnQR.addEventListener('click', function() {
            alert("Pindai QR Code di sebelah kanan kartu menggunakan kamera ponsel Anda untuk melihat Kartu Tanda Pelajar Digital!");
        });
    }
  </script>
</body>
</html>