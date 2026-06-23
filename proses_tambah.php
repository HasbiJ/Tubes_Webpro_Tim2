<?php
include "koneksi.php";

$mapel=$_POST['mapel'];
$guru=$_POST['guru'];
$judul=$_POST['judul'];
$deadline=$_POST['deadline'];
$status=$_POST['status'];

mysqli_query($conn,"INSERT INTO tugas
(mata_pelajaran,guru,judul,deadline,status)
VALUES
('$mapel','$guru','$judul','$deadline','$status')");

header("Location:guru.php");
?>