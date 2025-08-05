<!-- CONFIRM DELETE-->
<script type="text/javascript">
function confirmdelete(delUrl) {
    if (confirm("Anda yakin ingin menghapus?")) {
        document.location = delUrl;
    }
}
</script>

<?php
if(count(get_included_files())==1){
	echo "<meta http-equiv='refresh' content='0; url=http://$_SERVER[HTTP_HOST]'>";
	exit("Direct access not permitted.");
}
error_reporting(0);
session_start();

if (empty($_SESSION['namauser']) AND empty($_SESSION['passuser'])){
    header('location:../index.php');
}
else{
    if ($_SESSION['leveluser']=='admin' OR $_SESSION['leveluser']=='user_guru'){
        $id_ujian = $_GET['ujian'];
        $id_siswa = $_GET['siswa'];

        $sql_head = mysqli_query($koneksi,"SELECT * FROM topik_ujian WHERE id='$id_ujian'");
        $sql_data = mysqli_query($koneksi,"SELECT DISTINCT a.pertanyaan,a.kunci,b.jawaban 
                                           FROM soal_pilganda a, analisis b 
                                           WHERE a.id_soalpg=b.id_soal AND b.id_ujian='$id_ujian' AND b.id_siswa='$id_siswa'");
        $r = mysqli_fetch_array($sql_head);

        // Hitung jumlah benar, salah, dan kosong secara langsung
        $jumlah_benar = 0;
        $jumlah_salah = 0;
        $jumlah_kosong = 0;
        $analisis_rows = [];

        while($row = mysqli_fetch_array($sql_data)) {
            $analisis_rows[] = $row;

            if ($row['jawaban'] == "") {
                $jumlah_kosong++;
            } elseif ($row['jawaban'] == $row['kunci']) {
                $jumlah_benar++;
            } else {
                $jumlah_salah++;
            }
        }
?>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Jawaban Pilihan Ganda</h6>
                <div class="dropdown no-arrow">
                    <a href="?module=ug_ujian&act=detail_peserta&id=<?=$id_ujian;?>" class="btn-sm btn-warning">
                        <i class="fas fa-arrow-alt-circle-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="table_1" width="100%" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr align="center" class="bg-info" style="color: white;">
                                <th>NO</th>
                                <th>Soal</th>
                                <th>Kunci</th>
                                <th>Jawaban</th>
                                <th>Analisa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($analisis_rows as $row) {
                                $kunci = ['1' => 'A', '2' => 'B', '3' => 'C', '4' => 'D', '5' => 'E'][$row['kunci']] ?? '-';
                                $jawaban = ['1' => 'A', '2' => 'B', '3' => 'C', '4' => 'D', '5' => 'E'][$row['jawaban']] ?? '-';

                                $analisa = ($row['kunci'] == $row['jawaban']) 
                                            ? "<b class='text-success'> Benar</b>" 
                                            : "<b class='text-danger'> Salah</b>";
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= $row['pertanyaan']; ?></td>
                                <td align="center"><?= $kunci; ?></td>
                                <td align="center"><?= $jawaban; ?></td>
                                <td align="center"><?= $analisa; ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="7">
                                    <ul>
                                        <li>Jumlah Soal Benar : <?= $jumlah_benar; ?></li>
                                        <li>Jumlah Soal Salah : <?= $jumlah_salah; ?></li>
                                        <li>Tidak Dijawab : <?= $jumlah_kosong; ?></li>
                                    </ul>    
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer"></div>      
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card bg-deafult shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Data Siswa</h6>
            </div>
            <div class="card-body">
                <?php 
                $rs=mysqli_fetch_array(mysqli_query($koneksi,"SELECT a.*,b.nama_kelas,c.id_kelas 
                                                              FROM siswa a, m_kelas b, f_kelas c 
                                                              WHERE a.nis=c.nis AND b.id_kelas=c.id_kelas AND a.id='$id_siswa'"));
                ?>
                <table class="table table-bordered">
                    <tr>
                        <td colspan="2" align="center">
                            <img src="module/foto_siswa/medium_<?= $rs['foto'];?>" width="100%">
                        </td>
                    </tr>
                    <tr><td>NIS</td><td><?= $rs['nis'];?></td></tr>
                    <tr><td>Nama Lengkap</td><td><?= $rs['nama_lengkap'];?></td></tr>
                    <tr><td>Kelas</td><td><?= $rs['nama_kelas'];?></td></tr>
                </table>
            </div>
            <div class="card-footer"></div>
        </div>
    </div>
</div>

<?php
    } // end leveluser check
} // end session check
?>
