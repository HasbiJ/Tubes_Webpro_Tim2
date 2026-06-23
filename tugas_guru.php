<?php

$conn = mysqli_connect("localhost","root","","eduflex");

if(!$conn){
    die("Koneksi gagal : " . mysqli_connect_error());
}

/* =========================
   CREATE
========================= */
if(isset($_POST['tambah'])){

    $id_mapel  = $_POST['id_mapel'];
    $judul     = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $deadline  = $_POST['deadline'];

    if($id_mapel == "" || $judul == "" || $deskripsi == "" || $deadline == ""){
        echo "<script>alert('Semua field wajib diisi!');</script>";
    } else {

        $query = mysqli_query($conn,"
            INSERT INTO tugas (id_mapel, judul, deskripsi, deadline)
            VALUES ('$id_mapel','$judul','$deskripsi','$deadline')
        ");

        if($query){
            header("Location: tugas_guru.php");
            exit;
        } else {
            echo "<script>alert('Gagal menambah data: " . mysqli_error($conn) . "');</script>";
        }
    }
}

/* =========================
   DELETE
========================= */
if(isset($_GET['hapus'])){

    $id = $_GET['hapus'];

    $query = mysqli_query($conn,"
        DELETE FROM tugas
        WHERE id_tugas='$id'
    ");

    if($query){
        header("Location: tugas_guru.php");
        exit;
    } else {
        echo "<script>alert('Gagal menghapus data: " . mysqli_error($conn) . "');</script>";
    }
}

/* =========================
   UPDATE
========================= */
if(isset($_POST['update'])){

    $id        = $_POST['id_tugas'];
    $id_mapel  = $_POST['id_mapel'];
    $judul     = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $deadline  = $_POST['deadline'];

    if($id_mapel == "" || $judul == "" || $deskripsi == "" || $deadline == ""){
        echo "<script>alert('Semua field wajib diisi!');</script>";
    } else {

        $query = mysqli_query($conn,"
            UPDATE tugas SET
                id_mapel='$id_mapel',
                judul='$judul',
                deskripsi='$deskripsi',
                deadline='$deadline'
            WHERE id_tugas='$id'
        ");

        if($query){
            header("Location: tugas_guru.php");
            exit;
        } else {
            echo "<script>alert('Gagal update data: " . mysqli_error($conn) . "');</script>";
        }
    }
}

/* =========================
   AMBIL DATA EDIT
========================= */
$dataEdit = null;

if(isset($_GET['edit'])){

    $id = $_GET['edit'];

    $query = mysqli_query($conn,"
        SELECT * FROM tugas
        WHERE id_tugas='$id'
    ");

    $dataEdit = mysqli_fetch_assoc($query);
}

/* =========================
   AMBIL DATA MAPEL UNTUK DROPDOWN
========================= */
$dataMapel = mysqli_query($conn,"
    SELECT id_mapel, nama_mapel
    FROM mata_pelajaran
    ORDER BY nama_mapel ASC
");

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>CRUD Tugas Guru</title>

<style>
body{
    font-family: Arial, sans-serif;
    background:#f4f4f4;
    padding:20px;
}

.container{
    width:90%;
    margin:auto;
}

h2{
    color:#ff6600;
}

form{
    background:white;
    padding:20px;
    border-radius:10px;
    margin-bottom:20px;
}

input,
select,
textarea{
    width:100%;
    padding:10px;
    margin-bottom:10px;
    box-sizing:border-box;
}

textarea{
    resize:vertical;
}

button{
    background:#ff6600;
    color:white;
    border:none;
    padding:10px 15px;
    cursor:pointer;
    border-radius:5px;
}

table{
    width:100%;
    border-collapse:collapse;
    background:white;
}

th{
    background:#ff6600;
    color:white;
}

th,td{
    border:1px solid #ddd;
    padding:10px;
    text-align:center;
    vertical-align:top;
}

.edit{
    background:green;
    color:white;
    padding:5px 10px;
    text-decoration:none;
    border-radius:5px;
}

.hapus{
    background:red;
    color:white;
    padding:5px 10px;
    text-decoration:none;
    border-radius:5px;
}

.kosong{
    color:red;
    font-weight:bold;
    margin-bottom:15px;
}
</style>
</head>

<body>

<div class="container">

<h2>Kelola Tugas Guru</h2>

<?php
$cekMapel = mysqli_query($conn, "SELECT COUNT(*) as total FROM mata_pelajaran");
$rowMapel = mysqli_fetch_assoc($cekMapel);

if($rowMapel['total'] == 0){
    echo "<p class='kosong'>Data mata pelajaran masih kosong. Isi tabel mata_pelajaran dulu sebelum menambah tugas.</p>";
}
?>

<form method="POST">

<?php if($dataEdit){ ?>
    <input type="hidden" name="id_tugas" value="<?= $dataEdit['id_tugas']; ?>">
<?php } ?>

<label>Mata Pelajaran</label>
<select name="id_mapel" required>
    <option value="">-- Pilih Mata Pelajaran --</option>
    <?php
    mysqli_data_seek($dataMapel, 0);
    while($mapel = mysqli_fetch_assoc($dataMapel)){
        $selected = ($dataEdit && $dataEdit['id_mapel'] == $mapel['id_mapel']) ? "selected" : "";
        echo "<option value='".$mapel['id_mapel']."' $selected>".$mapel['nama_mapel']." (ID: ".$mapel['id_mapel'].")</option>";
    }
    ?>
</select>

<label>Judul Tugas</label>
<input type="text" name="judul" required value="<?= $dataEdit['judul'] ?? ''; ?>">

<label>Deskripsi</label>
<textarea name="deskripsi" rows="4" required><?= $dataEdit['deskripsi'] ?? ''; ?></textarea>

<label>Deadline</label>
<input type="date" name="deadline" required value="<?= $dataEdit['deadline'] ?? ''; ?>">

<?php if($dataEdit){ ?>
    <button type="submit" name="update">Update Tugas</button>
<?php } else { ?>
    <button type="submit" name="tambah">Tambah Tugas</button>
<?php } ?>

</form>

<table>
<tr>
    <th>ID Tugas</th>
    <th>Mata Pelajaran</th>
    <th>Judul</th>
    <th>Deskripsi</th>
    <th>Deadline</th>
    <th>Aksi</th>
</tr>

<?php

$data = mysqli_query($conn,"
    SELECT 
        tugas.id_tugas,
        tugas.judul,
        tugas.deskripsi,
        tugas.deadline,
        mata_pelajaran.nama_mapel
    FROM tugas
    JOIN mata_pelajaran ON tugas.id_mapel = mata_pelajaran.id_mapel
    ORDER BY tugas.id_tugas DESC
");

if(mysqli_num_rows($data) > 0){
    while($row = mysqli_fetch_assoc($data)){
?>

<tr>
    <td><?= $row['id_tugas']; ?></td>
    <td><?= $row['nama_mapel']; ?></td>
    <td><?= $row['judul']; ?></td>
    <td><?= $row['deskripsi']; ?></td>
    <td><?= $row['deadline']; ?></td>
    <td>
        <a class="edit" href="tugas_guru.php?edit=<?= $row['id_tugas']; ?>">Edit</a>
        <a class="hapus" href="tugas_guru.php?hapus=<?= $row['id_tugas']; ?>" onclick="return confirm('Yakin ingin menghapus data?')">Hapus</a>
    </td>
</tr>

<?php
    }
} else {
?>
<tr>
    <td colspan="6">Belum ada data tugas</td>
</tr>
<?php } ?>

</table>

</div>

</body>
</html>