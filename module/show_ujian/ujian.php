

<?php


if (count(get_included_files()) == 1) {
    exit("Direct access not permitted.");
}

error_reporting(0);
session_start();
if (empty($_SESSION['namauser']) && empty($_SESSION['passuser'])) {
    header('location:../error_login.php');
    exit();
}

switch ($_GET['act']) {
    default:
        if ($_SESSION['leveluser'] == 'user_siswa') {

            include "style_menu.php";

            $id_ujian = $_GET['id'];
            $rujian = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM topik_ujian WHERE id='$id_ujian'"));

            // Ambil soal
            $pg = mysqli_fetch_array(mysqli_query($koneksi, "SELECT COUNT(id_soalpg) as jum FROM soal_pilganda WHERE id_tujian='$id_ujian'"));
            $qsoal = mysqli_query($koneksi, "SELECT * FROM soal_pilganda WHERE id_tujian='$id_ujian' ORDER BY rand() LIMIT {$pg['jum']}");
            $q2soal = mysqli_query($koneksi, "SELECT * FROM soal_pilganda WHERE id_tujian='$id_ujian' ORDER BY id_soalpg");

            if (mysqli_num_rows($qsoal) == 0) {
                die('<div class="alert alert-warning">Belum ada soal pada ujian ini</div>');
            }

            $arr_soal = [];
            $arr_jawaban = [];
            while ($rsoal = mysqli_fetch_array($qsoal)) {
                $arr_soal[] = $rsoal['id_soalpg'];
                $arr_jawaban[] = 0;
            }

            $soalid = [];
            while ($r2soal = mysqli_fetch_array($q2soal)) {
                $soalid[] = $r2soal['id_soalpg'];
            }

            $acak_soal = implode(",", $arr_soal);
            $jawaban = implode(",", $arr_jawaban);

            // Ambil kelas siswa
            $nis = mysqli_fetch_array(mysqli_query($koneksi, "SELECT nis FROM siswa WHERE id='{$_SESSION['id_user']}'"));
            $kelas_data = mysqli_fetch_array(mysqli_query(
                $koneksi,
                "SELECT id_kelas FROM f_kelas WHERE nis='{$nis['nis']}' AND tp='$tahun_p'"
            ));
            $kelas = $kelas_data['id_kelas'];

            // Cek nilai
            $qnilai = mysqli_query($koneksi, "SELECT * FROM nilai WHERE id_siswa='{$_SESSION['id_user']}' AND id_ujian='$id_ujian'");
            if (mysqli_num_rows($qnilai) < 1) {
                // Pastikan durasi ujian dalam detik
                $durasi_detik = intval($rujian['waktu_pengerjaan']);
                if ($durasi_detik <= 0) { $durasi_detik = 3600; } // default 1 jam jika kosong

                $sisa_waktu = gmdate("H:i:s", $durasi_detik);
                $waktu_selesai = date("H:i:s", time() + $durasi_detik);

                mysqli_query(
                    $koneksi,
                    "INSERT INTO nilai 
                     SET id_siswa='{$_SESSION['id_user']}',
                         id_ujian='$id_ujian',
                         kelas='$kelas',
                         acak_soal='$acak_soal',
                         jawaban='$jawaban',
                         sisa_waktu='$sisa_waktu',
                         waktu_selesai='$waktu_selesai',
                         status='mengerjakan',
                         jml_benar=0,
                         jml_kosong=0,
                         jml_salah=0,
                         nilai=0"
                ) or die(mysqli_error($koneksi));

                foreach ($soalid as $kelas_soal) {
                    mysqli_query(
                        $koneksi,
                        "INSERT INTO analisis 
                         SET id_siswa='{$_SESSION['id_user']}',
                             id_ujian='$id_ujian',
                             id_soal='$kelas_soal',
                             jawaban='0'"
                    );
                }
            } else {
                // Hitung ulang sisa waktu
                $nilai = mysqli_fetch_array($qnilai);
                $selesai = strtotime($nilai['waktu_selesai']);
                $sekarang = time();
                $sisa_detik = $selesai - $sekarang;
                if ($sisa_detik < 0) { $sisa_detik = 0; }
                $sisa_waktu = gmdate("H:i:s", $sisa_detik);

                mysqli_query($koneksi, "UPDATE nilai SET sisa_waktu='$sisa_waktu' WHERE id_siswa='{$_SESSION['id_user']}' AND id_ujian='$id_ujian'");
            }

            // Ambil sisa waktu terbaru
            $rnilai = mysqli_fetch_array(mysqli_query($koneksi, "SELECT sisa_waktu, acak_soal, jawaban FROM nilai WHERE id_siswa='{$_SESSION['id_user']}' AND id_ujian='$id_ujian'"));
            list($jam_awal, $menit_awal, $detik_awal) = explode(":", $rnilai['sisa_waktu']);
            $jam_awal = intval($jam_awal);
            $menit_awal = intval($menit_awal);
            $detik_awal = intval($detik_awal);
?>

<div class="row">
    <div class="col-lg-12">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">UJIAN : <?=$rujian['judul'];?> </h6>
            <div><b>Sisa Waktu : </b><span id="h_timer" class="badge bg-danger"></span></div>
        </div>
    </div>
</div>

<script>
var jam   = <?= $jam_awal ?>;
var menit = <?= $menit_awal ?>;
var detik = <?= $detik_awal ?>;

function updateTimer(){
    if (detik === 0) {
        if (menit === 0) {
            if (jam === 0) {
                document.getElementById("h_timer").innerText = "00:00:00";
                clearInterval(timerInterval);
                return;
            }
            jam--; menit = 59; detik = 59;
        } else {
            menit--; detik = 59;
        }
    } else {
        detik--;
    }
    document.getElementById("h_timer").innerText =
        String(jam).padStart(2, '0') + ":" +
        String(menit).padStart(2, '0') + ":" +
        String(detik).padStart(2, '0');
}

updateTimer();
let timerInterval = setInterval(updateTimer, 1000);
</script>


<?php
        }
    case "selesai_ujian":
        $id_ujian = $_POST['id_ujian'];
        mysqli_query($koneksi, "UPDATE nilai 
            SET status='selesai', sisa_waktu='00:00:00'
            WHERE id_siswa='{$_SESSION['id_user']}' AND id_ujian='$id_ujian'");
        header("Location: ?module=sis_ujian");
    break;
}
?>
