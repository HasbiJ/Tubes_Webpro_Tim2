<?php

$conn = mysqli_connect("localhost","root","","eduflex");

if(!$conn){
    die("Koneksi gagal : ".mysqli_connect_error());
}

/* CREATE */
if(isset($_POST['tambah'])){

    $id_siswa = $_POST['id_siswa'];
    $id_mapel = $_POST['id_mapel'];
    $nilai = $_POST['nilai'];

    mysqli_query($conn,"
        INSERT INTO nilai
        (id_siswa,id_mapel,nilai)
        VALUES
        ('$id_siswa','$id_mapel','$nilai')
    ");

    header("Location:nilai.php");
    exit;
}

/* DELETE */
if(isset($_GET['hapus'])){

    $id = $_GET['hapus'];

    mysqli_query($conn,"
        DELETE FROM nilai
        WHERE id_nilai='$id'
    ");

    header("Location:nilai.php");
    exit;
}

/* UPDATE */
if(isset($_POST['update'])){

    $id = $_POST['id_nilai'];

    $id_siswa = $_POST['id_siswa'];
    $id_mapel = $_POST['id_mapel'];
    $nilai = $_POST['nilai'];

    mysqli_query($conn,"
        UPDATE nilai SET
        id_siswa='$id_siswa',
        id_mapel='$id_mapel',
        nilai='$nilai'
        WHERE id_nilai='$id'
    ");

    header("Location:nilai.php");
    exit;
}

/* EDIT DATA */
$dataEdit = null;

if(isset($_GET['edit'])){

    $id = $_GET['edit'];

    $query = mysqli_query($conn,"
        SELECT *
        FROM nilai
        WHERE id_nilai='$id'
    ");

    $dataEdit = mysqli_fetch_assoc($query);
}

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>CRUD Nilai</title>

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

input{
    width:100%;
    padding:10px;
    margin-bottom:10px;
    box-sizing:border-box;
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

</style>
</head>

<body>

<div class="container">

<h2>Kelola Nilai Siswa</h2>

<form method="POST">

<?php if($dataEdit){ ?>
<input type="hidden"
       name="id_nilai"
       value="<?= $dataEdit['id_nilai']; ?>">
<?php } ?>

<label>ID Siswa</label>
<input type="number"
       name="id_siswa"
       required
       value="<?= $dataEdit['id_siswa'] ?? ''; ?>">

<label>ID Mapel</label>
<input type="number"
       name="id_mapel"
       required
       value="<?= $dataEdit['id_mapel'] ?? ''; ?>">

<label>Nilai</label>
<input type="number"
       step="0.01"
       min="0"
       max="100"
       name="nilai"
       required
       value="<?= $dataEdit['nilai'] ?? ''; ?>">

<?php if($dataEdit){ ?>

<button type="submit" name="update">
    Update Nilai
</button>

<?php } else { ?>

<button type="submit" name="tambah">
    Tambah Nilai
</button>

<?php } ?>

</form>

<table>

<tr>
    <th>ID Nilai</th>
    <th>ID Siswa</th>
    <th>ID Mapel</th>
    <th>Nilai</th>
    <th>Aksi</th>
</tr>

<?php

$data = mysqli_query($conn,"
    SELECT *
    FROM nilai
    ORDER BY id_nilai DESC
");

while($row = mysqli_fetch_assoc($data)){
?>

<tr>

    <td><?= $row['id_nilai']; ?></td>
    <td><?= $row['id_siswa']; ?></td>
    <td><?= $row['id_mapel']; ?></td>
    <td><?= $row['nilai']; ?></td>

    <td>

        <a class="edit"
           href="nilai.php?edit=<?= $row['id_nilai']; ?>">
           Edit
        </a>

        <a class="hapus"
           href="nilai.php?hapus=<?= $row['id_nilai']; ?>"
           onclick="return confirm('Yakin ingin menghapus data?')">
           Hapus
        </a>

    </td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>