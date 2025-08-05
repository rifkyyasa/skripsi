<!-- CONFIRM DELETE-->
<script type="text/javascript">
function confirmdelete(delUrl) {
if (confirm("Anda yakin ingin menghapus?")) {
document.location = delUrl;
	}
}
</script>
<?php
// oke
//Deteksi hanya bisa diinclude, tidak bisa langsung dibuka (direct open)
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
      $id_ujian = $_GET['id'];
      $sql_head=mysqli_query($koneksi,"SELECT * FROM topik_ujian WHERE id='$id_ujian'");
      $r=mysqli_fetch_array($sql_head);

      //CARI ID SOAL PG DAN ESSAY
      $pg=mysqli_num_rows(mysqli_query($koneksi,"SELECT DISTINCT id_nilai FROM nilai WHERE id_ujian='$id_ujian'"));
      $es=mysqli_num_rows(mysqli_query($koneksi,"SELECT DISTINCT id_nesay FROM nilai_esay WHERE id_ujian='$id_ujian'"));
      
      if($pg>0 AND $es>0) {
            $sql_data=mysqli_query($koneksi,"SELECT DISTINCT id_ujian,id_siswa,nilai FROM nilai WHERE id_ujian='$id_ujian' AND nilai !=''");
      }
      elseif($pg>0 AND $es==0) {
            $sql_data=mysqli_query($koneksi,"SELECT DISTINCT id_ujian,id_siswa,nilai FROM nilai WHERE id_ujian='$id_ujian' AND nilai !=''");

      }
      elseif($pg==0 AND $es>0) {
            $sql_data=mysqli_query($koneksi,"SELECT DISTINCT id_ujian,id_siswa FROM nilai_esay WHERE id_ujian='$id_ujian'");
      }
      
      //$sql_data=mysqli_query($koneksi,"SELECT a.$sql_data=mysqli_query($koneksi,"SELECT id_siswa FROM nilai WHERE id_ujian='$id_ujian' ");gkap, b.id_mapel, c.nis FROM siswa a, f_mapel b, f_kelas c WHERE a.nis=c.nis AND b.id_kelas = c.id_kelas AND b.nip='$_SESSION[id_user]'");
      //echo "SELECT a.nama_lengkap, b.id_mapel, c.nis FROM siswa a, f_mapel b, f_kelas c WHERE a.nis=c.nis AND b.id_kelas = c.id_kelas AND b.nip='$_SESSION[id_user]'";
      //echo "SELECT DISTINCT id_ujian,id_siswa,nilai FROM nilai WHERE id_ujian='$id_ujian'";
?>
<div class="row">
  <div class="col-md-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Siswa Yang Melaksanakan Ujian<b>  <?=$r['judul'];?></b></h6>
        <div class="dropdown no-arrow">
          <a href="?module=ug_ujian" class="btn-sm btn-warning"><i class="fas fa-arrow-alt-circle-left"></i> Back</a>
          <a href="#" class="btn-sm btn-primary" data-toggle="modal" data-target="#cetak"><i class="fas fa-print"></i> Cetak</a>
          <a href="#" class="btn-sm btn-success" data-toggle="modal" data-target="#export"><i class="fas fa-file-excel"></i> Export Excel</a>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped" id="table_1"  width="100%" cellspacing="0" cellpadding="0">
            <thead>
              <tr align="center" class="bg-info" style="color: white;">
                <th rowspan="2">NO</th>
                <th rowspan="2">NISN</th>
                <th rowspan="2">Nama Siswa</th>
                <th rowspan="2">Kelas</th>
                <th rowspan="2">Nilai PG</th>
                <th rowspan="2">Nilai Essay</th>
                <th rowspan="2">Jumlah</th>
                <th colspan="2">Reset</th>
              </tr>
              <tr align="center" class="bg-info" style="color: white;">
                <th>PG</th>
                <th>Essay</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $no = 1;
              while($row=mysqli_fetch_array($sql_data)) {

                $row2 = mysqli_fetch_array(mysqli_query($koneksi,"SELECT a.nama_lengkap,a.id, b.id_mapel, c.nis FROM siswa a, f_mapel b, f_kelas c WHERE a.nis=c.nis AND b.id_kelas = c.id_kelas AND a.id='$row[id_siswa]' AND c.tp='$tahun_p' AND b.id_mapel='$r[id_mapel]'"));
                //echo "SELECT a.nama_lengkap,a.id, b.id_mapel, c.nis FROM siswa a, f_mapel b, f_kelas c WHERE a.nis=c.nis AND b.id_kelas = c.id_kelas AND a.id='$row[id_siswa]' AND c.tp='$tahun_p'";
                $row3 = mysqli_fetch_array(mysqli_query($koneksi,"SELECT a.nama_kelas,b.id_kelas FROM m_kelas a,f_kelas b WHERE a.id_kelas=b.id_kelas AND b.nis='$row2[nis]' AND b.tp='$tahun_p'"));
                $row4 = mysqli_fetch_array(mysqli_query($koneksi,"SELECT sum(nilai) as jum FROM nilai_esay WHERE id_ujian='$id_ujian' AND id_siswa='$row[id_siswa]' "));
                  
                  if($row4['jum']==0){
                        $n_esay = 'Belum Koreksi <br> <a href="?module=ug_ujian&act=koreksi&ujian='.$id_ujian.'&siswa='.$row2['id'].'" class="btn-sm btn-primary"><i class="fas fa-exclamation-circle"></i>Koreksi</a>';
                  }
                  else{
                        $n_esay = $r['bobot_esay']/100*$row4['jum'].'<br><a href="?module=ug_ujian&act=koreksi&ujian='.$id_ujian.'&siswa='.$row2['id'].'" class="btn-sm btn-primary"><i class="fas fa-exclamation-circle"></i><small>Recorect</small></a>';
                  }
                
                $n_pg = $r['bobot_pg']/100 * $row['nilai'];
                $jumlah = $n_pg+ ($r['bobot_esay']/100*$row4['jum']);
               ?>
                  
              <tr>
                <td><?=$no;?></td>
                <td><?=$row2['nis'];?></td>
                <td><?=$row2['nama_lengkap'];?></td>
                <td><?=$row3['nama_kelas'];?></td>
                <td align="center"><?=$n_pg;?><br> <a href="?module=ug_ujian&act=analisa_pg&ujian=<?=$id_ujian;?>&siswa=<?=$row2['id'];?>" class="btn-sm btn-primary"><i class="fas fa-check"></i><small>Analisa</small></a></td>
                <td align="center"><?=$n_esay;?> </td>
                <td align="center"><?=$jumlah;?></td>
                <td align="center"><a href="#" data-toggle="modal" data-target="#reset_pg<?= $row['id_siswa'];?>" class="btn-sm btn-warning"><i class="fas fa-exclamation-circle"></i></a></td>
                <td align="center"><a href="#" data-toggle="modal" data-target="#reset_es<?= $row['id_siswa'];?>" class="btn-sm btn-danger"><i class="fas fa-exclamation-circle"></i></a></td>
              </tr>

              <!-- Modal Reset PG-->
              <div class="modal fade " id="reset_pg<?= $row['id_siswa'];?>"  role="dialog" >
                <div class="modal-dialog modal-sm" role="document">
                  <div class="modal-content bg-gradient-warning">
                  <form method="POST" action="?module=ug_ujian&act=reset_pg" enctype="multipart/form-data">
                        <div class="modal-header">
                          <h5 class="modal-title text-white">Reset Soal Pilihan Ganda</h5>
                        </div>
                        <?php 
                        $data=mysqli_fetch_array(mysqli_query($koneksi,"SELECT nama_lengkap FROM siswa WHERE id='$row[id_siswa]'"));
                         ?>
                        <div class="modal-body" align="justify-content-between">
                              <h5 class="text-white"><b><?=$data['nama_lengkap'];?></b></h5>
                              <input type="hidden" name="id_siswa" value="<?= $row['id_siswa'];?>">
                              <input type="hidden" name="id_ujian" value="<?= $row['id_ujian'];?>">
                        </div>
                        <div class="modal-footer">
                              
                              <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                              <button type="submit" name="proses_koreksi" class="btn btn-primary">Proses</button>
                        </div>
                    </form>
                    </div>
                </div>
              </div><!-- END MODAL-->

              <!-- Modal Reset ES-->
              <div class="modal fade " id="reset_es<?= $row['id_siswa'];?>"  role="dialog" >
                <div class="modal-dialog modal-sm" role="document">
                  <div class="modal-content bg-gradient-warning">
                  <form method="POST" action="?module=ug_ujian&act=reset_essay" enctype="multipart/form-data">
                        <div class="modal-header">
                          <h5 class="modal-title text-white">Reset Soal Essay</h5>
                        </div>
                        <?php 
                        $data=mysqli_fetch_array(mysqli_query($koneksi,"SELECT nama_lengkap FROM siswa WHERE id='$row[id_siswa]'"));
                         ?>
                        <div class="modal-body" align="justify-content-between">
                              <h5 class="text-white"><b><?=$data['nama_lengkap'];?></b></h5>
                              <input type="hidden" name="id_siswa" value="<?= $row['id_siswa'];?>">
                              <input type="hidden" name="id_ujian" value="<?= $row['id_ujian'];?>">
                        </div>
                        <div class="modal-footer">
                              
                              <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                              <button type="submit" name="proses" class="btn btn-primary">Proses</button>
                        </div>
                    </form>
                    </div>
                </div>
              </div><!-- END MODAL-->


              <?php 
                $no++; 
                } 
              ?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="7"></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="card-footer">
        <ul style="font-size: 13px;">
          <li>Pilih Aksi Reset  jika ingin mereset Siswa yang telah mengikuti ujian.</li>
          <li>Hanya jawaban soal Essay yang bisa di koreksi.</li>
          <li>Penilaian Soal Pilihan Ganda Sistem yang mengerjakan.</li>
        </ul>
      </div>      
    </div>
  </div>
</div>

<!-- Modal Cetak-->
<div class="modal fade" id="cetak"  role="dialog" >
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
    <form method="POST" action="module/ug_ujian/cetak_laporan.php" enctype="multipart/form-data" target="_blank">
      <div class="modal-header">
        <h4 class="modal-title" >Cetak Laporan Nilai</h4>
      </div>
      <?php 
        $data=mysqli_fetch_array(mysqli_query($koneksi,"SELECT nama_lengkap FROM siswa WHERE id='$row[id_siswa]'"));
       ?>
      
      <div class="modal-body" align="justify-content-between">
        <input type="hidden" name="id_ujian" value="<?= $id_ujian;?>">
        <select name="id_kelas" class="select2 form-control" required="required">
          <option value="">--Pilih Kelas--</option>
          
          <?php 
            $sq_kelas=mysqli_query($koneksi,"
            SELECT a.nama_kelas, b.id_kelas 
            FROM m_kelas a
            JOIN kelas_ujian b ON a.id_kelas = b.id_kelas 
            WHERE b.id_topik = '$id_ujian'
        ");
        while($rk=mysqli_fetch_array($sq_kelas)){
            echo '<option value="'.$rk['id_kelas'].'">'.$rk['nama_kelas'].'</option>';
        }
        
          ?>
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        <button type="submit" name="cetak" class="btn btn-primary"><i class="fas fa-print"></i> Cetak</button>
      </div>
    </form>
    </div>
  </div>
</div>


<!-- Modal Export -->
<form method="POST" action="module/ug_ujian/export_excel.php" target="_blank">
    <input type="hidden" name="id_ujian" value="<?= $_GET['id']; ?>">
    <select name="id_kelas" class="select2 form-control" required>
        <option value="">--Pilih Kelas--</option>
        <?php 
        $sq_kelas = mysqli_query($koneksi,"
            SELECT a.nama_kelas, b.id_kelas
            FROM m_kelas a
            JOIN kelas_ujian b ON a.id_kelas = b.id_kelas
            WHERE b.id_topik = '{$_GET['id']}'
        ");
        while($rk = mysqli_fetch_array($sq_kelas)){
            echo '<option value="'.$rk['id_kelas'].'">'.$rk['nama_kelas'].'</option>';
        }
        ?>
    </select>
    <button type="submit" class="btn btn-primary">Export</button>
</form>


<?php
      }
}
?>