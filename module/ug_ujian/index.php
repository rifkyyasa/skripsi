<script>
function confirmdelete(delUrl) {
if (confirm("Anda yakin ingin menghapus?")) {
document.location = delUrl;
}
}
</script>
<?php
//Deteksi hanya bisa diinclude, tidak bisa langsung dibuka (direct open)
if(count(get_included_files())==1){
  echo "<meta http-equiv='refresh' content='0; url=http://$_SERVER[HTTP_HOST]'>";
  exit("Direct access not permitted.");
  }
error_reporting(0);
session_start();
if (empty($_SESSION['namauser']) AND empty($_SESSION['passuser'])){
header('location:../error_login.php');
  }
else{
  switch($_GET['act']){
    default:
        if ($_SESSION['leveluser']=='user_guru'){
?>
<div class="row">
  <div class="col-md-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">Daftar Tugas dan Ujian</h6>
           <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <button class="btn-sm btn-primary" data-toggle="modal" data-target="#tambah_data"><i class="fas fa-plus"></i> Topik Tugas/Ujian</button>
                    </a>
          </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped" id="table_1"  width="100%" cellspacing="0" cellpadding="0">
            <thead>
              <tr>
                <th>NO</th>
                <th>Judul/Nama Ujian</th>
                <th>Ket</th>
                <th>Lihat Soal</th>
                <th>Telah Ujian</th>
                <th>Edit</th>
                <th>Hapus</th>
              </tr>
            </thead>
            <tbody style="font-size: 12px;">

              <?php 
                $thn_lalu = '2024-01-01';
$thn_skrg = '2025-12-31';

$sql_data = mysqli_query($koneksi,"
SELECT a.*, b.nama_mapel 
FROM topik_ujian a 
JOIN m_mapel b ON a.id_mapel = b.id_mapel 
WHERE a.pembuat = '" . mysqli_real_escape_string($koneksi, $_SESSION['id_user']) . "' 
AND a.tgl_buat BETWEEN '$thn_lalu' AND '$thn_skrg' 
ORDER BY a.id DESC
");

                $no=1;
                while($r=mysqli_fetch_array($sql_data)){
              ?>
                <tr>
                  <td><?= $no;?></td>
                  <td><?= $r['judul'];?></td>
                  <td>
                        Tgl_Post: <?= tgl_indo($r['tgl_buat']);?><br>
                        Waktu: <?= $r['waktu_pengerjaan']/60;?> Menit<br>
                        Soal: <?= $r['info'];?><br>
                        Terbit: <?= $r['terbit'];?><br>
                        Bobot PG: <?= $r['bobot_pg'];?> %<br>
                        Bobot Essay: <?= $r['bobot_esay'];?> %<br>
                      </td>
                  <td align="center"><a href="?module=ug_ujian&act=detail_soal&id=<?=$r['id'];?>" class="btn-sm btn-info"><i class="fas fa-search"></i></a></td>
                  <td align="center"><a href="?module=ug_ujian&act=detail_peserta&id=<?=$r['id'];?>" class="btn-sm btn-success"><i class="fas fa-graduation-cap"></i></a></td>
                  <td align="center"><a href="#" class="btn-sm btn-warning" data-toggle="modal" data-target="#edit<?php echo $r['id'];?>"><i class="fas fa-edit"></i></a></td>
                  <td align="center"><a href="javascript:confirmdelete('?module=ug_ujian&act=hapus_topik&id=<?php echo $r['id'];?>')" class="btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                  </tr>

                  <!-- Modal Edit-->
                  <div class="modal fade" id="edit<?php echo $r['id'];?>"  role="dialog" >
                    <div class="modal-dialog modal-lg" role="document">
                      <div class="modal-content">
                          <div class="modal-header">
                          <h4 class="modal-title" id="myModalLabel">Edit Topik Ujian</h4>
                          </div>
                        <form action="?module=ug_ujian&act=save" method="POST" role="form" enctype="multipart/form-data">
                          <?php 
                          $sql_edit=mysqli_query($koneksi,"SELECT * FROM topik_ujian WHERE id='$r[id]'");
                          $e=mysqli_fetch_array($sql_edit);
                           ?>
                        <div class="modal-body">
                          <div class="form-group">
                              <input type="text" class="form-control" name="id_tujian" value="<?= $e['id'];?>" placeholder="Judul Ujian">
                           </div>
                          <div class="form-group">
                              <label for="">Judul Ujian</label>
                              <input type="text" class="form-control" name="judul" value="<?= $e['judul'];?>" placeholder="Judul Ujian">
                           </div>

                           <div class="form-group">
                              <label for="">Mata Pelajaran </label>
                              <select name="id_mapel" class="form-control select2bs4" required="required">
                                <?php 
                                $mp=mysqli_fetch_array(mysqli_query($koneksi,"SELECT nama_mapel FROM m_mapel WHERE id_mapel ='$e[id_mapel]'"));
                                 ?>
                                <option value="<?= $e['id_mapel'];?>"><?= $mp['nama_mapel'];?></option>
                                <?php
                                $kl=mysqli_query($koneksi,"SELECT DISTINCT a.nama_mapel,b.id_mapel FROM m_mapel a, f_mapel b  WHERE a.id_mapel = b.id_mapel AND b.nip='$_SESSION[id_user]' ORDER BY b.id_mapel ASC");
                                while($dkl=mysqli_fetch_array($kl)) {
                                ?>
                                <option value="<?php echo $dkl['id_mapel'];?>"><?php echo $dkl['nama_mapel'];?></option>
                                <?php } ?>
                              </select>
                           </div>
                           <div class="form-group">
                              <label for="">Kelas</label>
                              <?php 
                                  
                                  $jumn=mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM kelas_ujian WHERE id ='$e[id]'"));
                                  $psql=mysqli_query($koneksi,"SELECT * FROM kelas_ujian WHERE id ='$e[id]'");
                                  // echo "SELECT * FROM kelas_ujian WHERE id ='$e[id]'";
                                ?>
                                
                                <select name="id_kelas[]" class="form-control select2bs4" multiple="multiple">
                                    <?php
                                    
                                    while($p=mysqli_fetch_array($psql))
                                    {
                                        $ng=mysqli_fetch_array(mysqli_query($koneksi,"SELECT nama_kelas FROM m_kelas WHERE id_kelas='$p[id_kelas]'"));
                                       //echo "$ng[nama_lengkap] </br>";
                                    ?>
                                    <option value="<?= $p['id_kelas']; ?>" selected> <?= $ng['nama_kelas']; }?></option>
                                    <?php
                                    $kls=mysqli_query($koneksi,"SELECT a.nama_kelas,b.id_kelas FROM m_kelas a, f_mapel b  WHERE a.id_kelas = b.id_kelas AND b.nip='$_SESSION[id_user]' ORDER BY b.id_kelas ASC");
                                    while($dkls=mysqli_fetch_array($kls)) {
                                    ?>
                                    <option value="<?php echo $dkls['id_kelas'];?>"><?php echo $dkls['nama_kelas'];?></option>
                                    <?php } ?>
                                </select>
                           </div>

                           <div class="form-group">
                              <label for="">Waktu Pengerjaan</label>
                              <input type="number" class="form-control" name="waktu_pengerjaan" value="<?= $e['waktu_pengerjaan']/60;?>" placeholder="Waktu Pengerjaan (Dalam Menit)"><p><small>Dalam Menit</small>
                           </div>

                           <div class="form-group">
                              <label for="">Info Ujian</label>
                              <input type="text" class="form-control" name="info" value="<?= $e['info']?>" placeholder="Info Ujian">
                           </div>

                           <div class="form-group">
                              <label for="">Bobot Pilihan Ganda (%)</label>
                              <input type="number" class="form-control" name="bobot_pg" value="<?= $e['bobot_pg']?>" placeholder="Bobot Pilihan Ganda (%)">
                           </div>

                           <div class="form-group">
                              <label for="">Bobot Essaay (%)</label>
                              <input type="number" class="form-control" name="bobot_esay" value="<?= $e['bobot_esay']?>" placeholder="Bobot Essay (%)">
                           </div>

                           <div class="form-group">
                              <label for="">Terbit..?</label><p>
                              <?php  
                              if($e['terbit']=='Y'){
                                    echo"<label><input type=radio name='terbit' value=Y checked>Y</input></label>
                                         <label><input type=radio name='terbit' value=N>N</input></label>";
                                      }
                              else{
                                    echo"<label><input type=radio name='terbit' value=Y >Y</input></label>
                                         <label><input type=radio name='terbit' value=N checked>N</input></label>";
                                      }
                                  ?>
                           </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                          <button type="submit" name="update_tu" class="btn btn-primary">Simpan</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div><!--end modal-->
              <?php
                $no++;
                }

               ?>
            </tbody>
            <tfoot>
              
            </tfoot>
          </table>
        </div>  
      </div>
    </div>
  </div>
</div>

<!-- Modal Add Topik Uzian-->
<div class="modal fade" id="tambah_data"  role="dialog" >
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Buat Ujian Baru</h4>
        </div>
      <form action="?module=ug_ujian&act=save" method="POST" role="form" enctype="multipart/form-data">
      <div class="modal-body">
        <div class="form-group">
            <label for="">Judul Ujian</label>
            <input type="text" class="form-control" name="judul" value="" placeholder="Judul Ujian">
         </div>

         <div class="form-group">
            <label for="">Mata Pelajaran </label>
            <select name="id_mapel" class="form-control select2bs4" required="required">
              <option value="">--Pilih Mapel--</option>
              <?php
              $kl=mysqli_query($koneksi,"SELECT DISTINCT a.nama_mapel,b.id_mapel FROM m_mapel a, f_mapel b  WHERE a.id_mapel = b.id_mapel AND b.nip='$_SESSION[id_user]' ORDER BY b.id_mapel ASC");
              while($dkl=mysqli_fetch_array($kl)) {
              ?>
              <option value="<?php echo $dkl['id_mapel'];?>"><?php echo $dkl['nama_mapel'];?></option>
              <?php } ?>
            </select>
         </div>
         <div class="form-group">
            <label for="">Kelas</label>
            <select name="id_kelas[]" class="form-control select2bs4" required="required" multiple="multiple">
              <option value="">--Pilih Kelas--</option>
              <?php
              $kls=mysqli_query($koneksi,"SELECT DISTINCT a.nama_kelas,b.id_kelas FROM m_kelas a, f_mapel b  WHERE a.id_kelas = b.id_kelas AND b.nip='$_SESSION[id_user]' ORDER BY b.id_kelas ASC");
              while($dkls=mysqli_fetch_array($kls)) {
              ?>
              <option value="<?php echo $dkls['id_kelas'];?>"><?php echo $dkls['nama_kelas'];?></option>
              <?php } ?>
            </select>
         </div>

         <div class="form-group">
            <label for="">Waktu Pengerjaan</label>
            <input type="number" class="form-control" name="waktu_pengerjaan" value="" placeholder="Waktu Pengerjaan (Dalam Menit)"><p><small>Dalam Menit</small>
         </div>

         <div class="form-group">
            <label for="">Info Ujian</label>
            <input type="text" class="form-control" name="info" value="" placeholder="Info Ujian">
         </div>

         <div class="form-group">
            <label for="">Bobot Pilihan Ganda (%)</label>
            <input type="number" class="form-control" name="bobot_pg" value="" placeholder="Bobot Pilihan Ganda (%)">
         </div>

         <div class="form-group">
            <label for="">Bobot Essaay (%)</label>
            <input type="number" class="form-control" name="bobot_esay" value="" placeholder="Bobot Essay (%)">
         </div>

         <div class="form-group">
            <label for="">Terbit..?</label>
            <select name="terbit" class="form-control">
              <option>Y</option>
              <option>N</option>
            </select>
         </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        <button type="submit" name="simpan_tu" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>
</div><!--end modal-->

<?php }
break;
case "detail_soal":
?>
<div class="row">
  <div class="col-md-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">Detail Soal Ujian</h6>
           <div class="dropdown no-arrow">
            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></a>
            <a href="?module=ug_ujian" class="btn-sm btn-warning"><i class="fas fa-arrow-alt-circle-left"></i> Back</a>
          </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <?php
              $sql_data = mysqli_query($koneksi,"SELECT a.*,b.nama_mapel FROM topik_ujian a, m_mapel b WHERE a.id_mapel = b.id_mapel AND a.id='$_GET[id]' ORDER BY a.judul ASC");
              $s=mysqli_fetch_array($sql_data);
              $waktu = $s['waktu_pengerjaan']/60;

              $sql_2=mysqli_query($koneksi,"SELECT a.nama_kelas, b.id_kelas FROM m_kelas a, kelas_ujian b WHERE a.id_kelas=b.id_kelas AND b.id = '$s[id]'");
              //echo "SELECT a.nama_kelas, b.id_kelas FROM m_kelas a, kelas_ujian b WHERE a.id_kelas=b.id_kelas AND b.id = '$s[id]'";
            ?>
        <table class="table table-bordered table-striped" cellpadding="-1" cellspacing="-1">
          <thead>
          </thead>
          <tbody>
            <tr><td>Judul Tugas/Ujian</td><td>: <?= $s['judul'];?></td></tr>
            <tr><td>Kelas Yang di Tugaskan</td><td>: <ol><?php 
                    while ($t=mysqli_fetch_array($sql_2)) {
                    echo "<li>$t[nama_kelas]</li>";

                    
                  } ?>
                    
                  </ol>
                </td>
            </tr>
            <tr><td>Mata Pelajaran</td><td>: <?= $s['nama_mapel'];?></td></tr>
            <tr><td>Jenis Soal</td><td>: <?= $s['info'];?></td></tr>
            <tr><td>Waktu Pengerjaan</td><td>: <?= $waktu;?> Menit</td></tr>
          </tbody>
          <tfoot>
            <tr><td style="text-align: right;">
                    <a href="?module=ug_ujian&act=essay&id=<?=$s['id'];?>" class="btn btn-success btn-icon-split"><span class="icon text-white-50"><i class="fas fa-search"></i></span><span class="text">Soal Essay</span></a>
                  </td>
                  <td>
                    <a href="?module=ug_ujian&act=pil_ganda&id=<?=$s['id'];?>" class="btn btn-primary btn-icon-split"><span class="text">Pilihan Ganda</span><span class="icon text-white-50"><i class="fas fa-search"></i></span></a>
                  </td></tr>
          </tfoot>
        </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
break;
case "essay":
?>
<div class="row">
  <div class="col-md-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">List Soal Essay</h6>
           <div class="dropdown no-arrow">
            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></a>
            <a href="?module=ug_ujian&act=detail_soal&id=<?= $_GET['id'];?>" class="btn-sm btn-warning"><i class="fas fa-arrow-alt-circle-left"></i> Back</a>
            <a href="#" class="btn-sm btn-primary" data-toggle="modal" data-target="#add_essay"><i class="fas fa-plus"></i> Essay</a>
            <a href="#" class="btn-sm btn-success" data-toggle="modal" data-target="#bank_esay"><i class="fas fa-copy"></i> Bank Soal </a>
          </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <?php
              $sql_1 = mysqli_query($koneksi,"SELECT a.*,b.nama_mapel FROM topik_ujian a, m_mapel b WHERE a.id_mapel = b.id_mapel AND a.id='$_GET[id]' ORDER BY a.judul ASC");
              $s=mysqli_fetch_array($sql_1);
              $waktu = $s['waktu_pengerjaan']/60;
              $sql_2 = mysqli_query($koneksi,"SELECT * FROM soal_esay WHERE id_tujian = '$s[id]' ORDER BY pertanyaan ASC");
              $no=1;
            ?>
          <form>
            <fieldset>
              <dl class='inline'>
              <dt><label>Judul</label>           : <?=$s['judul'];?></dt>
              <dt><label>Mata Pelajaran</label>  : <?=$s['nama_mapel'];?></dt>
              <dt><label>Waktu</label>           : <?=$waktu;?> Menit</dt>
              </dl>
            </fieldset>
          </form>
          <table class="table table-bordered table-striped" id="table_1">
            <thead>
              <tr>
                <th>No</th><th>Pertanyaan</th><th>Tgl Buat</th><th>Edit</th><th>Hapus</th>
              </tr>
            </thead>
            <tbody>
              <?php while($t=mysqli_fetch_array($sql_2))
              { ?>
              <tr>
                <td><?= $no;?></td>
                <td><?= $t['pertanyaan'];?></td>
                <td><?= tgl_indo($t['tgl_buat']);?></td>
                <td align="center"><a href="#" class="btn-sm btn-warning" data-toggle="modal" data-target="#edit_<?= $t['id_soal'];?>"><i class="fas fa-edit"></i></a></td> 
                <td align="center"> <a href="javascript:confirmdelete('?module=ug_ujian&act=hapus&id=<?= $t['id_soal'];?>&id_tujian=<?= $t['id_tujian'];?>')" class="btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
              </tr>

              <!-- Modal Edit Soal-->
              <div class="modal fade" id="edit_<?= $t['id_soal'];?>"  role="dialog" >
                <div class="modal-dialog modal-lg" role="document">
                  <div class="modal-content">
                      <div class="modal-header">
                      <h4 class="modal-title" id="myModalLabel">Edit Soal Essay</h4>
                      </div>
                    <form action="?module=ug_ujian&act=save" method="POST" role="form" enctype="multipart/form-data">
                      
                      <div class="modal-body">
                      <?php 
                        $ed = mysqli_query($koneksi,"SELECT * FROM soal_esay WHERE id_soal = '$t[id_soal]'");
                        $dt = mysqli_fetch_array($ed);
                      ?>
                        <div class="form-group">
                          <input type="hidden" class="form-control" name="id_soal" value="<?= $dt['id_soal'];?>">
                          <input type="hidden" class="form-control" name="id_tujian" value="<?= $dt['id_tujian'];?>">
                        </div>
                        <div class="form-group">
                          <label for="pertanyaan">Pertanyaan</label>
                          <textarea name="pertanyaan" cols="75" rows="3" class="editor_soal"> <?= $dt['pertanyaan'];?></textarea>
                            
                        </div>
                      </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                      <button type="submit" name="update_essay" class="btn btn-primary">Update</button>
                    </div>
                  </form>
                </div>
              </div>
              </div><!--end modal-->

              <?php 
              $no++;
              } ?>
            </tbody>
            <tfoot>
              <tr><td colspan="5"></td></tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Add Soal Essay-->
<div class="modal fade" id="add_essay"  role="dialog" >
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Tambah Soal Essay</h4>
        </div>
      <form action="?module=ug_ujian&act=save" method="POST" role="form" enctype="multipart/form-data">
      <div class="modal-body">
      <?php 
        $jum = mysqli_query($koneksi,"SELECT COUNT(soal_esay.id_soal) as jml FROM soal_esay WHERE id_tujian = '$_GET[id]'");
        $j = mysqli_fetch_array($jum);
        $jumlah = $j['jml'] + 1;
        //echo $jumlah;
      ?>
        <div class="form-group">
          <label for="">Pertanyaan ke <?= $jumlah;?></label>
            <input type="hidden" class="form-control" name="id_tujian" value="<?= $_GET['id'];?>">
            <textarea name="pertanyaan" cols="75" rows="3" class="editor"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        <button type="submit" name="simpan_essay" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>
</div><!--end modal-->

<!-- Modal Add Bank Soal Essay-->
<div class="modal fade" id="bank_esay"  role="dialog" >
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Tambah Soal Essay</h4>
        </div>
      <form action="?module=ug_ujian&act=save" method="POST" role="form" enctype="multipart/form-data">
      <div class="modal-body">
        <input type="hidden" class="form-control" name="id_tujian" value="<?= $_GET['id'];?>">
      <?php 
        $sql_bank_essay = mysqli_query($koneksi,"SELECT * FROM bank_esay WHERE pembuat ='$_SESSION[id_user]'");
        
        $no = 1;
      ?>
      <table class="table table-striped" id="tab_bank1" >
        <thead>
          <tr><th width="5%">No</th><th width="90%">Pertanyaan</th><th>Mapel</th><th width="5%">Aksi</th></tr>
        </thead>
        <tbody>
          <?php 
          foreach ($sql_bank_essay as $rb) {
            $mpl = mysqli_fetch_array(mysqli_query($koneksi,"SELECT nama_mapel FROM m_mapel WHERE id_mapel = '$rb[id_mapel]'")); 
           
          ?>
          <tr><td><?=$no;?></td><td><?=$rb['pertanyaan'];?></td><td><?=$mpl['nama_mapel'];?></td><td><input type="checkbox" name="id[]" value="<?=$rb['id'];?>" class="form" ></td></tr>
          <?php 
          $no++;
          }
           ?>
        </tbody>
        <tfoot>
          <tr><th width="5%">No</th><th width="90%">Pertanyaan</th><th width="5%">Aksi</th></tr>
        </tfoot>
      </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        <button type="submit" name="essay_bank" class="btn btn-primary"><i class="fas fa-copy"></i> Proses</button>
      </div>
    </form>
  </div>
</div>
</div><!--end modal-->

<?php
break;
case "pil_ganda":
?>

<div class="row">
  <div class="col-md-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">List Soal Pilihan Ganda</h6>
           <div class="dropdown no-arrow">
            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></a>
            <a href="?module=ug_ujian&act=detail_soal&id=<?= $_GET['id'];?>" class="btn-sm btn-warning"><i class="fas fa-arrow-alt-circle-left"></i> Back</a>
            <a href="#" class="btn-sm btn-primary" data-toggle="modal" data-target="#add_pilganda"><i class="fas fa-plus"></i> Pil Ganda</a>
            <a href="#" class="btn-sm btn-success" data-toggle="modal" data-target="#bank_pg"><i class="fas fa-copy"></i> Bank Soal </a>
          </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <?php
              $sql_1 = mysqli_query($koneksi,"SELECT a.*,b.nama_mapel FROM topik_ujian a, m_mapel b WHERE a.id_mapel = b.id_mapel AND a.id='$_GET[id]' ORDER BY a.judul ASC");
              $s=mysqli_fetch_array($sql_1);
              $waktu = $s['waktu_pengerjaan']/60;
              $sql_2 = mysqli_query($koneksi,"SELECT * FROM soal_pilganda WHERE id_tujian = '$s[id]' ORDER BY id_soalpg DESC");
              $no=1;
            ?>
          <form>
            <fieldset>
              <dl class='inline'>
              <dt><label>Judul</label>           : <?=$s['judul'];?></dt>
              <dt><label>Mata Pelajaran</label>  : <?=$s['nama_mapel'];?></dt>
              <dt><label>Waktu</label>           : <?=$waktu;?> Menit</dt>
              </dl>
            </fieldset>
          </form>
          <table class="table table-bordered table-striped" id="table_1" style="font-size: 13px;">
            <thead>
              <tr>
                <th>No</th><th>Soal</th><th>Pil A</th><th>Pil B</th><th>Pil C</th><th>Pil D</th><th>Pil E</th><th>KUNCI</th><th>Edit</th><th>Hapus</th>
            </thead>
            <tbody>
              <?php while($t=mysqli_fetch_array($sql_2))
              { 
                if($t['kunci']==1){
                  $kunci = 'A';
                } 
                elseif($t['kunci']==2){
                  $kunci = 'B';
                } 
                elseif($t['kunci']==3){
                  $kunci = 'C';
                } 
                elseif($t['kunci']==4){
                  $kunci = 'D';
                } 
                else {
                  $kunci = 'E';
                } 
              ?>
              <tr>
                <td><?= $no;?></td>
                <td><?= $t['pertanyaan'];?></td>
                <td><?= $t['pil_a'];?></td>
                <td><?= $t['pil_b'];?></td>
                <td><?= $t['pil_c'];?></td>
                <td><?= $t['pil_d'];?></td>
                <td><?= $t['pil_e'];?></td>
                <td><?= $kunci;?></td>
                <td align="center"><a href="#" class="btn-sm btn-warning" data-toggle="modal" data-target="#edit_<?= $t['id_soalpg'];?>"><i class="fas fa-edit"></i></a></td>
                <td align="center"><a href="javascript:confirmdelete('?module=ug_ujian&act=hapuspg&id=<?= $t['id_soalpg'];?>&id_tujian=<?= $t['id_tujian'];?>')" class="btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
              </tr>

              <!-- Modal Edit Soal-->
              <div class="modal fade" id="edit_<?= $t['id_soalpg'];?>"  role="dialog" >
                <div class="modal-dialog modal-lg" role="document">
                  <div class="modal-content">
                      <div class="modal-header">
                      <h4 class="modal-title" id="myModalLabel">Edit Soal Pilihan Ganda</h4>
                      </div>
                    <form action="?module=ug_ujian&act=save" method="POST" role="form" enctype="multipart/form-data">
                      
                      <div class="modal-body">
                      <?php 
                        $ed = mysqli_query($koneksi,"SELECT * FROM soal_pilganda WHERE id_soalpg = '$t[id_soalpg]'");
                        $dt = mysqli_fetch_array($ed);
                      ?>
                        <div class="form-group">
                          <input type="hidden" class="form-control" name="id_soalpg" value="<?= $dt['id_soalpg'];?>">
                          <input type="hidden" class="form-control" name="id_tujian" value="<?= $dt['id_tujian'];?>">
                        </div>
                        <div class="form-group">
                          <label for="pertanyaan">Pertanyaan</label>
                            
                            <textarea name="pertanyaan"  cols="75" rows="3" class="editor_soal"><?= $dt['pertanyaan'];?></textarea>
                        </div>

                        <div class="form-group">
                          <label for="">Jawaban A</label>
                          <textarea name="pil_a"  cols="75" rows="3" class ="editor"><?= $dt['pil_a'];?></textarea>
                        </div>

                        <div class="form-group">
                          <label for="">Jawaban B</label>
                          <textarea name="pil_b"  cols="75" rows="3" class ="editor"><?= $dt['pil_b'];?></textarea>
                        </div>

                        <div class="form-group">
                          <label for="">Jawaban C</label>
                          <textarea name="pil_c"  cols="75" rows="3" class ="editor"><?= $dt['pil_c'];?></textarea>
                        </div>

                        <div class="form-group">
                          <label for="">Jawaban D</label>
                          <textarea name="pil_d"  cols="75" rows="3" class ="editor"><?= $dt['pil_d'];?></textarea>
                        </div>

                        <div class="form-group">
                          <label for="">Jawaban E</label>
                          <textarea name="pil_e"  cols="75" rows="3" class ="editor"><?= $dt['pil_e'];?></textarea>
                        </div>
                        <div class="form-group">
                          <label for="">Kunci Jawaban</label>
                          <select name="kunci" class="form-control">
                            <?php 
                              if($dt['kunci']==1){$kunci = 'A';} elseif($dt['kunci']==2){$kunci = 'B';} elseif($dt['kunci']==3){$kunci = 'C';} elseif($dt['kunci']==4){$kunci = 'D';} else{$kunci = 'E';}   
                            ?>
                            <option value="<?= $dt['kunci'];?>">Pilihan <?=$kunci;?></option>
                            <option value="1">Pilihan A</option>
                            <option value="2">Pilihan B</option>
                            <option value="3">Pilihan C</option>
                            <option value="4">Pilihan D</option>
                            <option value="5">Pilihan E</option>
                          </select>
                        </div>
                      </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                      <button type="submit" name="update_pilganda" class="btn btn-primary">Update</button>
                    </div>
                  </form>
                </div>
              </div>
              </div><!--end modal-->

              <?php 
              $no++;
              } ?>
            </tbody>
            <tfoot>
              <tr><td colspan="10"></td></tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal Add Soal Pilihan Ganda-->
<div class="modal fade" id="add_pilganda"  role="dialog" >
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Tambah Soal Pilihan Ganda</h4>
        </div>
      <form action="?module=ug_ujian&act=save" method="POST" role="form" enctype="multipart/form-data">
      <div class="modal-body">
      <?php 
        $jum = mysqli_query($koneksi,"SELECT COUNT(soal_pilganda.id_soalpg) as jml FROM soal_pilganda WHERE id_tujian = '$_GET[id]'");
        $j = mysqli_fetch_array($jum);
        $jumlah = $j['jml'] + 1;
        //echo $jumlah;
      ?>
        <div class="form-group">
          <label for="">Pertanyaan ke <?= $jumlah;?></label>
            <input type="hidden" class="form-control" name="id_tujian" value="<?= $_GET['id'];?>">
            <textarea name="pertanyaan"  cols="75" rows="3" class="editor_soal"></textarea>
        </div>

        <div class="form-group">
          <label for="">Jawaban A</label>
          <textarea name="pil_a"  cols="75" rows="3" class="editor"></textarea>
          
        </div>

        <div class="form-group">
          <label for="">Jawaban B</label>
          <textarea name="pil_b"  cols="75" rows="3" class="editor"></textarea>
        </div>

        <div class="form-group">
          <label for="">Jawaban C</label>
          <textarea name="pil_c"  cols="75" rows="3" class="editor"></textarea>
        </div>

        <div class="form-group">
          <label for="">Jawaban D</label>
          <textarea name="pil_d"  cols="75" rows="3" class="editor"></textarea>
        </div>

        <div class="form-group">
          <label for="">Jawaban E</label>
          <textarea name="pil_e"  cols="75" rows="3" class="editor"></textarea>
        </div>

        <div class="form-group">
          <label for="">Kunci Jawaban</label><p>
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="pil_1" name="kunci" class="custom-control-input" value="1">
            <label class="custom-control-label" for="pil_1">Pilihan A</label>
          </div>

          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="pil_2" name="kunci" class="custom-control-input" value="2">
            <label class="custom-control-label" for="pil_2">Pilihan B</label>
          </div>

          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="pil_3" name="kunci" class="custom-control-input" value="3">
            <label class="custom-control-label" for="pil_3">Pilihan C</label>
          </div>

          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="pil_4" name="kunci" class="custom-control-input" value="4">
            <label class="custom-control-label" for="pil_4">Pilihan D</label>
          </div>

          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="pil_5" name="kunci" class="custom-control-input" value="5">
            <label class="custom-control-label" for="pil_5">Pilihan E</label>
          </div>
        </div>  

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        <button type="submit" name="simpan_pilganda" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>
</div><!--end modal-->

<!-- Modal Add Bank Soal PG-->
<div class="modal fade" id="bank_pg"  role="dialog" >
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
        <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Tambah Soal PG dari Bank Soal</h4>
        </div>
      <form action="?module=ug_ujian&act=save" method="POST" role="form" enctype="multipart/form-data">
      <div class="modal-body">
        <input type="hidden" class="form-control" name="id_tujian" value="<?= $_GET['id'];?>">
      <?php 
        $sql_bank_pg = mysqli_query($koneksi,"SELECT * FROM bank_pilganda WHERE pembuat ='$_SESSION[id_user]'");
        $no = 1;
      ?>
      <table class="table table-striped" id="tab_bank1" >
        <thead>
          <tr><th width="5%">No</th><th width="50%">Pertanyaan</th><th width="20%">Mapel</th><th>Pil A</th><th>Pil B</th><th>Pil C</th><th>Pil D</th><th>Pil E</th><th>Kunci</th><th width="5%">Aksi</th></tr>
        </thead>
        <tbody>
          <?php 
          foreach ($sql_bank_pg as $rp) {
            if($rp['kunci']==1){$kunci = 'A';} elseif($rp['kunci']==2){$kunci = 'B';} elseif($rp['kunci']==3){$kunci = 'C';} elseif($rp['kunci']==4){$kunci = 'D';} elseif($rp['kunci']==5){$kunci = 'E';} else{$kunci = 'kosong';}
                $d=mysqli_fetch_array(mysqli_query($koneksi,"SELECT nama_mapel FROM m_mapel WHERE id_mapel = '$r[id_mapel]'"));
            $mpl = mysqli_fetch_array(mysqli_query($koneksi,"SELECT nama_mapel FROM m_mapel WHERE id_mapel = '$rp[id_mapel]'")); 
          ?>
          <tr>
              <td><?=$no;?></td>
              <td><?=$rp['pertanyaan'];?></td>
              <td><?=$mpl['nama_mapel'];?></td>
              <td><?=$rp['pil_a'];?></td>
              <td><?=$rp['pil_b'];?></td>
              <td><?=$rp['pil_c'];?></td>
              <td><?=$rp['pil_d'];?></td>
              <td><?=$rp['pil_e'];?></td>
              <td><?=$kunci;?></td>
              <td><input type="checkbox" name="id[]" value="<?=$rp['id'];?>" class="form" ></td></tr>
          <?php 
          $no++;
          }
           ?>
        </tbody>
        <tfoot>
          <tr><th width="5%">No</th><th width="90%">Pertanyaan</th><th width="5%">Aksi</th></tr>
        </tfoot>
      </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        <button type="submit" name="pg_bank" class="btn btn-primary"><i class="fas fa-copy"></i> Proses</button>
      </div>
    </form>
  </div>
</div>
</div><!--end modal-->

<?php   
break;
case "save":
include "save.php";

break;
case "hapus":
    $cek = mysqli_query($koneksi,"SELECT * FROM soal_esay WHERE id_soal = '$_GET[id]'");
      $r = mysqli_fetch_array($cek);
      if(empty($r['gambar'])) {
        mysqli_query($koneksi,"DELETE FROM soal_esay WHERE id_soal = '$_GET[id]'");
        save_alert('save','Soal di Hapus');
      htmlRedirect('media.php?module='.$module.'&act=essay&id='.$_GET['id_tujian'],1);
       }
      else{
          $img = "module/foto_soal/$r[gambar]";
          unlink($img);
          $img2 = "module/foto_soal/medium_$r[gambar]";
          unlink($img2);
          mysqli_query($koneksi,"DELETE FROM soal_esay WHERE id_soal = '$_GET[id]'");
          save_alert('save','Soal di Hapus');
          htmlRedirect('media.php?module='.$module.'&act=essay&id='.$_GET['id_tujian'],1);
       }
break;
case "hapuspg":
    $cek = mysqli_query($koneksi,"SELECT * FROM soal_pilganda WHERE id_soalpg = '$_GET[id]'");
      $r = mysqli_fetch_array($cek);
      if(empty($r['gambar'])) {
        mysqli_query($koneksi,"DELETE FROM soal_pilganda WHERE id_soalpg = '$_GET[id]'");
        save_alert('save','Soal di Hapus');
      htmlRedirect('media.php?module='.$module.'&act=pil_ganda&id='.$_GET['id_tujian'],1);
       }
      else{
          $img = "module/foto_soal_pilganda/$r[gambar]";
          unlink($img);
          $img2 = "module/foto_soal_pilganda/medium_$r[gambar]";
          unlink($img2);
          mysqli_query($koneksi,"DELETE FROM soal_pilganda WHERE id_soalpg = '$_GET[id]'");
          save_alert('save','Soal di Hapus');
          htmlRedirect('media.php?module='.$module.'&act=pil_ganda&id='.$_GET['id_tujian'],1);
       }
break;
case "hapus_topik":
  $c="DELETE FROM kelas_ujian WHERE id='$_GET[id]'";
  $c1="DELETE FROM topik_ujian WHERE id ='$_GET[id]'";
  mysqli_query($koneksi,$c);
  mysqli_query($koneksi,$c1);          
  save_alert('save','Soal di Hapus');
  htmlRedirect('media.php?module='.$module.'&act=ug_ujian');
break;

case "reset_pg":
  $id_siswa = $_POST['id_siswa'];
  $id_ujian   = $_POST['id_ujian'];
  
  $c1 ="DELETE FROM nilai WHERE id_siswa ='$id_siswa' AND id_ujian='$id_ujian'";
  $c2 ="DELETE FROM analisis WHERE id_siswa ='$id_siswa' AND id_ujian='$id_ujian'";
  
  mysqli_query($koneksi,$c1);
  mysqli_query($koneksi,$c2); 
           
  save_alert('save','Reset Soal PG Berhasil');
  htmlRedirect('media.php?module='.$module.'&act=detail_peserta&id='.$id_ujian);
break;

case "reset_essay":
  $id_siswa = $_POST['id_siswa'];
  $id_ujian   = $_POST['id_ujian'];
  
  $c3="DELETE FROM nilai_esay WHERE id_siswa ='$id_siswa' AND id_ujian='$id_ujian'";
 
  mysqli_query($koneksi,$c3);          
  save_alert('save','Reset Essay Berhasil');
  htmlRedirect('media.php?module='.$module.'&act=detail_peserta&id='.$id_ujian);
break;


case "detail_peserta":
include "detail_peserta.php";
break;  

case "koreksi":
include "koreksi.php";
break;      

case "analisa_pg":
include "analisa_pg.php";
break; 

case "export_excel":
include "export_excel.php";
break; 
  }
} 
?>
