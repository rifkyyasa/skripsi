<script>
function confirmdelete(delUrl) {
if (confirm("Anda yakin ingin menghapus?")) {
document.location = delUrl;
}
}
</script>
<?php 
 ?>
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
        if ($_SESSION['leveluser']=='user_siswa'){


?>
<div class="row">
  <div class="col-md-12">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">Daftar Video Pada Pelajaran Anda</h6>
    </div>
  </div>
</div>

<div class="row">
<?php
  $p      = new Paging;
  $batas  = 6;
  $posisi = $p->cariPosisi($batas); 
  $sq_kls=mysqli_fetch_array(mysqli_query($koneksi,"SELECT id_kelas FROM f_kelas a, siswa b WHERE a.nis=b.nis AND b.id='$_SESSION[id_user]'"));
  $id_kelas= $sq_kls['id_kelas'];
  
  $sql_data="SELECT DISTINCT a.*, b.id_mapel,c.id_kelas FROM file_video a, f_mapel b, file_video_det c WHERE a.id_mapel=b.id_mapel AND a.id_video=c.id_video AND c.id_kelas='$id_kelas' AND b.tp='$tahun_p' AND a.tgl_posting BETWEEN '$thn_lalu' AND '$thn_skrg' ORDER BY a.id_video DESC LIMIT $posisi,$batas";
  //$sql_data="SELECT * FROM file_video ORDER BY id_video DESC LIMIT $posisi,$batas";

  $video=mysqli_query($koneksi,$sql_data);

  $jmldata     = mysqli_num_rows(mysqli_query($koneksi, "SELECT DISTINCT a.*, b.id_mapel,c.id_kelas FROM file_video a, f_mapel b, file_video_det c WHERE a.id_mapel=b.id_mapel AND a.id_video=c.id_video AND c.id_kelas='$id_kelas' ORDER BY a.id_video"));

  $jmlhalaman  = $p->jumlahHalaman($jmldata, $batas);
  $linkHalaman = $p->navHalaman(anti_injection($_GET['hal']), $jmlhalaman);

  //$j=mysqli_num_rows(mysqli_query($koneksi,$sql_data));
  $video=mysqli_query($koneksi,$sql_data);
  //echo $j;
  foreach ($video as $r ) 
    { 
    
    if(empty($r['nama_video'])){
      $video = $r['youtube'];
    }
    else{
      $video = 'module/files_video/'.$r['nama_video'];
    }

  $mapel = mysqli_fetch_array(mysqli_query($koneksi,"SELECT nama_mapel FROM m_mapel WHERE id_mapel='$r[id_mapel]'"));
  $guru = mysqli_fetch_array(mysqli_query($koneksi,"SELECT nama_lengkap FROM guru WHERE id='$r[pembuat]'"));
    
 ?>
  <div class="col-md-4 mt-4">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary"><?= $r['judul'];?></h6>
      </div>
      <div class="card-body">
        <div class="embed-responsive embed-responsive-16by9">
          <iframe class="embed-responsive-item" src="<?= $video;?>" allowfullscreen></iframe>
        </div>
        <div class="flex-row align-items-center justify-content-between">
          <table class="mt-2">
            <tr><td>Mapel</td><td> : <?= $mapel['nama_mapel'];?></td></tr> 
            <tr><td> Uploader</td><td> : <?= $guru['nama_lengkap'];?></td></tr>
          </table>
        </div>
      </div>
      <div class="card-footer">
        <a href="?module=sis_video&act=lihat&id=<?=$r['id_video'];?>" class="btn-sm btn-primary"> View</a>
      </div>
    </div>
  </div>

<?php } ?>

</div>
<hr class="divider">
<?= $linkHalaman;?>

<?php 
    }
break;

case 'save':
include 'save.php';

break;
  }
} 
?>
