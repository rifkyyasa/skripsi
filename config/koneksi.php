<?php
// panggil fungsi validasi xss dan injection
require_once('fungsi_validasi.php');

$db['host'] = "localhost"; //host 127.0.0.1:3309 localhost
$db['user'] = "root"; //username database root u8864067_elearning
$db['pass'] = ""; //password database XzM2aEViJaZPZ6z
$db['name'] = "kohk_7173_e-learning"; //nama database db_e_learning u8864067_db_elearning
 
$koneksi = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
// buat variabel untuk validasi dari file fungsi_validasi.php
//$val = new validasi;
// Check connection
if (mysqli_connect_errno()){
	echo "Koneksi database gagal : " . mysqli_connect_error();
}

?>
