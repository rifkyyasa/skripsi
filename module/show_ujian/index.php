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

            $qnilai = mysqli_query($koneksi, "SELECT * FROM nilai WHERE id_siswa='{$_SESSION['id_user']}' AND id_ujian='$id_ujian'");
            if (mysqli_num_rows($qnilai) < 1) {
                $jam  = date("H:i:s");
                $jm1  = substr($jam, 0, 2);
                $mn1  = substr($jam, 3, 2);
                $dt1  = substr($jam, 6, 2);

                $w_a  = $rujian['waktu_pengerjaan'];
                $j_w = floor($w_a / 3600);
                $m_w = floor(($w_a % 3600) / 60);
                $d_w = 0;
                $wak = sprintf("%02d:%02d:%02d", $j_w, $m_w, $d_w);

                $jm2   = substr($wak, 0, 2);
                $mn2   = substr($wak, 3, 2);
                $jam12 = $jm2 + $jm1;
                $menit = $mn2 + $mn1;

                if ($menit > 59) {
                    $hr = $jam12 + 1;
                    $mn = $menit - 60;
                } else {
                    $hr = $jam12;
                    $mn = $menit;
                }

                $waktuselesai = sprintf("%02d:%02d:%02d", $hr, $mn, $dt1);

                mysqli_query(
                    $koneksi,
                    "INSERT INTO nilai 
                     SET id_siswa='{$_SESSION['id_user']}',
                         id_ujian='$id_ujian',
                         kelas='$kelas',
                         acak_soal='$acak_soal',
                         jawaban='$jawaban',
                         sisa_waktu='$wak',
                         waktu_selesai='$waktuselesai',
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
            }

            $qnilai = mysqli_query($koneksi, "SELECT * FROM nilai WHERE id_siswa='{$_SESSION['id_user']}' AND id_ujian='$id_ujian'");
            $rnilai = mysqli_fetch_array($qnilai);

            // Ambil waktu dari database dan pastikan angka
            $sisa_waktu_str = !empty($rnilai['sisa_waktu']) ? $rnilai['sisa_waktu'] : "00:00:00";
            list($jam_awal, $menit_awal, $detik_awal) = explode(":", $sisa_waktu_str);
            echo "<!-- DEBUG: sisa_waktu_str = {$sisa_waktu_str}, jam={$jam_awal}, menit={$menit_awal}, detik={$detik_awal} -->";
?>

<div class="row">
    <div class="col-lg-12">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">UJIAN : <?=$rujian['judul'];?> </h6>
            <div><b>Sisa Waktu : </b><span id="h_timer" class="badge bg-danger"></span></div>
        </div>
    </div>
</div>

<div class="row">
<?php 
$arr_soal = explode(",", $rnilai['acak_soal']);
$arr_jawaban = explode(",", $rnilai['jawaban']);
for($s=0; $s<count($arr_soal); $s++) {
    $rsoal = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM soal_pilganda WHERE id_soalpg='$arr_soal[$s]'"));
    $no = $s+1;
    $soal = $rsoal['pertanyaan'];

    echo '<div class="col-lg-12">
        <div class="card mb-3">
            <div class="card-body">
                <p><b>Soal Nomor '.$no.'</b></p>
                <p>'.$soal.'</p>
                <table>';
    $arr_pilihan = [
        ["no"=>1,"pilihan"=>$rsoal['pil_a']],
        ["no"=>2,"pilihan"=>$rsoal['pil_b']],
        ["no"=>3,"pilihan"=>$rsoal['pil_c']],
        ["no"=>4,"pilihan"=>$rsoal['pil_d']],
        ["no"=>5,"pilihan"=>$rsoal['pil_e']]
    ];
    $arr_huruf = ["A","B","C","D","E"];

    for($i=0; $i<=4; $i++){
        $checked = ($arr_jawaban[$s] == $arr_pilihan[$i]['no']) ? "checked" : "";
        echo '<tr>
            <td>
                <label style="cursor:pointer;">
                    <input type="radio" name="jawab-'.$no.'" 
                           value="'.$arr_pilihan[$i]['no'].'" '.$checked.'
                           onclick="kirim_jawaban('.$s.', '.$arr_pilihan[$i]['no'].')">
                    '.$arr_huruf[$i].'. '.$arr_pilihan[$i]['pilihan'].'
                </label>
            </td>
        </tr>';
    }
    
    echo '</table>
            </div>
        </div>
    </div>';
}
?>
</div>

<!-- Tombol Selesai -->
<div class="text-center mb-4">
    <button class="btn btn-danger" onclick="selesai()">SELESAI</button>
</div>

<!-- Modal Selesai -->
<div class="modal fade" id="modal-selesai" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="?module=sis_ujian&act=selesai_ujian">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Selesai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_ujian" value="<?=$id_ujian;?>">
                    Apakah Anda yakin ingin menyelesaikan ujian ini?
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Ya, Selesai</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function selesai(){
    var modal = new bootstrap.Modal(document.getElementById('modal-selesai'));
    modal.show();
}

// Timer countdown - nilai dari PHP langsung integer
let jam   = <?= intval($jam_awal) ?>;
let menit = <?= intval($menit_awal) ?>;
let detik = <?= intval($detik_awal) ?>;
console.log("DEBUG - jam:", jam, "menit:", menit, "detik:", detik);

function updateTimer(){
    if (detik === 0) {
        if (menit === 0) {
            if (jam === 0) {
                document.getElementById("h_timer").innerText = "00:00:00";
                clearInterval(timerInterval);
                return;
            }
            jam--;
            menit = 59;
            detik = 59;
        } else {
            menit--;
            detik = 59;
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
    break;

    // PROSES SELESAI UJIAN
    case "selesai_ujian":
        $id_ujian = $_POST['id_ujian'];
        mysqli_query($koneksi, "UPDATE nilai 
            SET status='selesai', sisa_waktu='00:00:00'
            WHERE id_siswa='{$_SESSION['id_user']}' AND id_ujian='$id_ujian'");
        header("Location: ?module=sis_ujian");
    break;
}
?>
