<?php
if(count(get_included_files())==1){
   echo "<meta http-equiv='refresh' content='0; url=http://$_SERVER[HTTP_HOST]'>";
   exit("Direct access not permitted.");
}
error_reporting(0);
session_start();

if (empty($_SESSION['namauser']) AND empty($_SESSION['passuser'])){
    header('location:../error_login.php');
}

// Ganti semua ke koneksi yang benar
// Pastikan $koneksi sudah didefinisikan dari file koneksi.php

if($_GET['action']=="kirim_jawaban"){
    $rnilai = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM nilai WHERE id_ujian='$_POST[ujian]' AND id_siswa='$_SESSION[id_user]'"));
    $arr_soal = explode(",", $rnilai['acak_soal']);
    $jawaban = explode(",", $rnilai['jawaban']);
    $index = $_POST['index'];	
    $jawaban[$index] = $_POST['jawab'];
	
    $jawabanfix = implode(",", $jawaban);
    mysqli_query($koneksi, "UPDATE nilai SET jawaban='$jawabanfix' WHERE id_ujian='$_POST[ujian]' AND id_siswa='$_SESSION[id_user]'");
    mysqli_query($koneksi, "UPDATE analisis SET jawaban='$jawaban[$index]' WHERE id_ujian='$_POST[ujian]' AND id_soal='$arr_soal[$index]' AND id_siswa='$_SESSION[id_user]'");
    echo "ok";
}

elseif($_GET['action']=="selesai_ujian"){
    $rnilai = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM nilai WHERE id_ujian='$_POST[ujian]' AND nis='$_SESSION[nis]'"));
    $arr_soal = explode(",", $rnilai['acak_soal']);
    $jawaban = explode(",", $rnilai['jawaban']);
    $jbenar = 0;
    $jkosong = 0;
    $jsoal = count($arr_soal);

    for($i = 0; $i < $jsoal; $i++){
        $rsoal = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM soal WHERE id_ujian='$_POST[ujian]' AND id_soal='$arr_soal[$i]'"));
        if($rsoal['kunci'] == $jawaban[$i]) $jbenar++;
        if($jawaban[$i] == 0) $jkosong++;
    }
    $jsalah = $jsoal - $jbenar - $jkosong;
    $nilai = ($jbenar / $jsoal) * 100;

    mysqli_query($koneksi, "UPDATE nilai SET jml_benar='$jbenar', jml_kosong='$jkosong', jml_salah='$jsalah', nilai='$nilai', status='selesai' WHERE id_ujian='$_POST[ujian]' AND nis='$_SESSION[nis]'");
    mysqli_query($koneksi, "UPDATE siswa SET status='Selesai' WHERE nis='$_SESSION[nis]'");
    echo "ok";
}
?>
