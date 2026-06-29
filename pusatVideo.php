<?php
// 1. Panggil file koneksi database milikmu
include 'koneksi.php'; 

/**
 * PENTING: Buka file koneksi.php milikmu. 
 * Jika di dalam file koneksi.php nama variabelnya adalah $conn atau $db,
 * silakan aktifkan salah satu baris di bawah ini dengan menghapus tanda //
 */
// $koneksi = $conn;
// $koneksi = $db;

// ==========================================
// LOGIKA BACKEND: PROSES CRUD KOMENTAR
// ==========================================

// A. PROSES CREATE (Tambah Komentar)
if (isset($_POST['tambah_komentar'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $isi_komentar = mysqli_real_escape_string($koneksi, $_POST['isi_komentar']);

    if (trim($isi_komentar) != '') {
        $query = "INSERT INTO komentar (nama, isi_komentar) VALUES ('$nama', '$isi_komentar')";
        mysqli_query($koneksi, $query);
    }
    
    // Amankan pemindahan halaman agar input kembali bersih
    header("Location: pusatVideo.php");
    exit();
}

// B. PROSES UPDATE (Simpan Perubahan Edit)
if (isset($_POST['update_komentar'])) {
    $id = intval($_POST['id']);
    $isi_komentar = mysqli_real_escape_string($koneksi, $_POST['isi_komentar']);

    if (trim($isi_komentar) != '') {
        $query = "UPDATE komentar SET isi_komentar = '$isi_komentar' WHERE id = '$id'";
        mysqli_query($koneksi, $query);
    }
    
    header("Location: pusatVideo.php");
    exit();
}

// C. PROSES DELETE (Hapus Komentar)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $query = "DELETE FROM komentar WHERE id = '$id'";
    mysqli_query($koneksi, $query);

    header("Location: pusatVideo.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusat Pembelajaran Video - EduFlex</title>
    <link rel="stylesheet" href="video.css">
    <link rel="stylesheet" href="pusatVideo.css">
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
                <li><a href="dashboard.php" class="active">Beranda</a></li>
                <li><a href="presensi.php">Presensi</a></li>
                <li><a href="jadwal.php">Jadwal</a></li>
                <li><a href="#">AI Helper</a></li>
                <li><a href="pusatVideo.php">Video</a></li>
                <li><a href="tugas.php">Tugas</a></li>
            </ul>
        </nav>
        <section class="right-section">
            <div>
                <form class="search" action="#">
                    <img src="search_24dp_000000_FILL0_wght400_GRAD0_opsz24.png" alt="search" class="logo-search">
                    <input placeholder="Cari... (tekan Enter)">
                </form>
            </div>
            <div class="language">
                <img src="language_24dp_000000_FILL0_wght400_GRAD0_opsz24.png" alt="bahasa" class="logo-bahasa">
                <select class="select">
                    <option>ID</option>
                    <option>ENG</option>
                </select>
            </div>
            <div class="account">
                <a href="assment2.html"><img src="foto profile.jpg" alt="Foto Asep" class="avatar"></a>
                <a href="assment2.html">
                    <div>Asep</div>
                </a>
            </div>
        </section>
    </header>

    <h1 class="page-title">Pusat Pembelajaran Video</h1>
    
    <main class="container">
        <section class="video-section">
            <div class="video-wrapper">
                <video controls>
                    <source src="https://www.youtube.com/watch?v=xoHt137BXJE" type="video/mp4">
                </video>
            </div>

            <h2 class="video-title">Memahami Dasar-Dasar Aljabar</h2>
            <p class="video-desc">Pada pertemuan kali ini, kita akan mempelajari Dasar-Dasar Aljabar</p>
            <div class="video-extra">
                <div class="teacher-info">
                    <img src="profileguru.jpg" class="teacher-foto">
                </div>
                <h3>Pak Budi Santoso</h3>
                <p>Pengajar Matematika</p>
            </div>
            <div class="video-actions">
                <button class="like-main">👍 Like</button>
                <a href="sample.mp4" download class="download-main">⬇️ Unduh Video</a>
            </div>

            <div class="comment-section">
                <h3>Komentar</h3>

                <div class="comment-box">
                    <form action="" method="POST" style="display: flex; width: 100%; gap: 10px;">
                        <input type="hidden" name="nama" value="Asep"> 
                        <input type="text" name="isi_komentar" placeholder="Tulis komentar..." required>
                        <button type="submit" name="tambah_komentar">Kirim</button>
                    </form>
                </div>

                <div class="comments-list">
                    <?php
                    // READ: Mengambil data komentar langsung dari database
                    $query = mysqli_query($koneksi, "SELECT * FROM komentar ORDER BY id DESC");
                    
                    if (!$query || mysqli_num_rows($query) == 0) {
                        echo "<p style='color: #6b7280; font-style: italic;'>Belum ada komentar. Jadilah yang pertama!</p>";
                    } else {
                        while ($row = mysqli_fetch_assoc($query)) {
                            // Jika parameter URL action=edit dan ID cocok, render form edit
                            if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id']) && $_GET['id'] == $row['id']) {
                                ?>
                                <div class="comment-item edit-mode">
                                    <form action="" method="POST" style="display: flex; flex-direction: column; gap: 8px; width: 100%;">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <strong><?php echo htmlspecialchars($row['nama']); ?> <span style="font-weight: normal; color: var(--accent);">(Mengedit...)</span></strong>
                                        <input type="text" name="isi_komentar" value="<?php echo htmlspecialchars($row['isi_komentar']); ?>" required style="padding: 8px; border-radius: 6px; border: 1px solid #ccc; width: 100%; box-sizing: border-box;">
                                        <div style="display: flex; gap: 10px;">
                                            <button type="submit" name="update_komentar" style="background: #10b981; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer;">Simpan</button>
                                            <a href="pusatVideo.php" class="btn-batal" style="text-decoration: none; color: #6b7280; align-self: center; font-size: 14px;">Batal</a>
                                        </div>
                                    </form>
                                </div>
                                <?php
                            } else {
                                ?>
                                <div class="comment-item">
                                    <div class="comment-header" style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                        <strong><?php echo htmlspecialchars($row['nama']); ?></strong>
                                        <span class="comment-date" style="font-size: 11px; color: #9ca3af;"><?php echo date('d M Y, H:i', strtotime($row['created_at'])); ?></span>
                                    </div>
                                    <p><?php echo htmlspecialchars($row['isi_komentar']); ?></p>
                                    
                                    <div class="comment-actions" style="display: flex; gap: 12px; margin-top: 8px; font-size: 12px;">
                                        <a href="pusatVideo.php?action=edit&id=<?php echo $row['id']; ?>" class="act-edit" style="text-decoration: none; color: #2563eb; font-weight: bold;">📝 Edit</a>
                                        <a href="pusatVideo.php?action=delete&id=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus komentar ini?')" class="act-delete" style="text-decoration: none; color: #dc2626; font-weight: bold;">❌ Hapus</a>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </section>

        <aside class="recommend-section">
            <h3>Video Rekomendasi</h3>
            <div class="rec-card">
                <img src="ipa.jpg" class="rec-thumb">
                <div>
                    <strong>Ilmu Pengetahuan Alam</strong>
                    <p class="rec-info">12:20 · 1.1K views</p>
                    <button class="like-btn">👍 Like</button>
                </div>
            </div>
            <div class="rec-card">
                <img src="one piece.jpeg" class="rec-thumb">
                <div>
                    <strong>Geografi Bumi</strong>
                    <p class="rec-info">10:45 · 980 views</p>
                    <button class="like-btn">👍 Like</button>
                </div>
            </div>
            <div class="rec-card">
                <img src="fisika.jpeg" class="rec-thumb">
                <div>
                    <strong>Fisika</strong>
                    <p class="rec-info">08:12 · 720 views</p>
                    <button class="like-btn">👍 Like</button>
                </div>
            </div>
            <div class="rec-card">
                <img src="1940.jpg_wh860.jpg" class="rec-thumb">
                <div>
                    <strong>Kimia</strong>
                    <p class="rec-info">05:12 · 720 views</p>
                    <button class="like-btn">👍 Like</button>
                </div>
            </div>
            <div class="rec-card">
                <img src="Sejarah.png" class="rec-thumb">
                <div>
                    <strong>Sejarah</strong>
                    <p class="rec-info">05:12 · 720 views</p>
                    <button class="like-btn">👍 Like</button>
                </div>
            </div>
        </aside>
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
</body>

</html>