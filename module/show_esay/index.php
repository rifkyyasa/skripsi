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
	//echo "<meta http-equiv='refresh' content='0; url=http://$_SERVER[HTTP_HOST]'>";
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
      	$id_ujian = $_GET['id'];
      	$uj	= mysqli_fetch_array(mysqli_query($koneksi,"SELECT a.judul,a.pembuat,b.nama_mapel FROM topik_ujian a, m_mapel b WHERE a.id_mapel = b.id_mapel AND  a.id='$id_ujian'"));
?>
<div class="row">
	<div class="col-lg-8">
		<div class="card bg-deafult shadow">
			<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
		  		<h6 class="m-0 font-weight-bold text-primary">Daftar Soal Esay </h6>
		  		 <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    </a>
			  	</div>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered "  width="100%" cellspacing="0" cellpadding="0">
						<thead>
							<tr>
								<td>No</td>
								<td>Soal</td>
								<td>Jawab</td>
								<td>Edit Jawaban</td>
							</tr>
						</thead>
						<tbody>
							<?php
							 $no 		= 1;
							 
							 $sql_data	= mysqli_query($koneksi,"SELECT * FROM soal_esay WHERE id_tujian ='$id_ujian' ORDER BY id_soal ASC");
							 
							 while($s=mysqli_fetch_array($sql_data)) {
							 
							 $r=mysqli_fetch_array(mysqli_query($koneksi,"SELECT id_nesay,jawaban_esay FROM nilai_esay WHERE id_ujian='$id_ujian' AND id_soalesay='$s[id_soal]' AND id_siswa ='$_SESSION[id_user]'"));
								 if(empty($r['jawaban_esay'])) {
								 	$isi = "<a href='?module=show_esay&act=jawab&id_soal=$s[id_soal]&id_ujian=$id_ujian' class='btn-sm btn-primary'>Jawab</a>";
								 	$edit = "<i class='btn-sm btn-warning'>Belum di jawab</i>";
								 }
								 else {
								 	$isi = substr($r['jawaban_esay'],0,5).'... '.'<a href="#" data-toggle="modal" data-target="#view_'.$r['id_nesay'].'"> more...</a>';
								 	$edit = "<a href='?module=show_esay&act=edit&id_soal=$s[id_soal]&id_ujian=$id_ujian&id=$r[id_nesay]' class='btn-sm btn-warning'><i class='fas fa-edit'></i> Edit</a>";
								 }
							 ?>
							 <tr>
							 	<td><?= $no;?></td>
							 	<td><?= $s['pertanyaan'];?></td>
							 	<td align="center"><?= $isi;?></td>
							 	<td align="center"><?= $edit;?></td>
							 </tr>

							 <!-- Modal Edit-->
			                  <div class="modal fade" id="view_<?php echo $r['id_nesay'];?>"  role="dialog" >
			                    <div class="modal-dialog modal-lg" role="document">
			                      <div class="modal-content">
			                          <div class="modal-header">
			                          <h4 class="modal-title" id="myModalLabel">Detail Jawaban Anda</h4>
			                          </div>
			                          <?php 
			                          $sql=mysqli_query($koneksi,"SELECT jawaban_esay FROM nilai_esay WHERE id_nesay='$r[id_nesay]'");
			                          $e=mysqli_fetch_array($sql);
			                           ?>
			                        <div class="modal-body">
			                          <?= $e['jawaban_esay'];?>
			                        </div>
			                        <div class="modal-footer">
			                          <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
			                        </div>
			                    </div>
			                  </div>
			                </div><!--end modal-->
							 <?php
							 $no++;
							 }
							 
							 ?>
						</tbody>
					</table>
				</div>	
			</div>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="card bg-deafult shadow">
			<div class="card-header">
				<h6 class="m-0 font-weight-bold text-primary">Data Ujian</h6>
			</div>
			<div class="card-body">
				<b>Ujian  <?= $uj['judul'];?></b><br>Pelajaran  <?= $uj['nama_mapel'];?><br>
				
			</div>
			<div class="card-footer">
				<a href="?module=ujian_online&pmb=<?= $uj['pembuat'];?>&topik=<?= $id_ujian;?>"><button class="btn-sm btn-warning"><i class="fas fa-arrow-alt-circle-left"></i> Back</button></a>

				<a href="#" data-toggle="modal" data-target="#info_selesai"><button class="btn-sm btn-primary"><i class="fas fa-check"></i> Selesai Ujian</button></a>

				  <!-- Modal Alert Selesai-->
	              <div class="modal fade" id="info_selesai"  role="dialog" >
	                <div class="modal-dialog modal-md" role="document">
	                  <div class="modal-content">
	                      <div class="modal-header">
	                      <h4 class="modal-title" id="myModalLabel"></h4>
	                      </div>
	                    <form action="?module=show_esay&act=selesai_ujian" method="POST" role="form" enctype="multipart/form-data">
	                    <div class="modal-body">
	                      <center>Terima Kasih Telah Melaksanakan Ujian Soal Essay Ini<br>
	                      Klik <b>Proses</b> Untuk Menyimpan dan <i>Close</i> Untuk Membatalkan</center>
	                      <input type="hidden" name="id_ujian" value="<?= $id_ujian;?>">
	                      <input type="hidden" name="id_siswa" value="<?= $_SESSION['id_user'];?>">
	                    </div>
	                    <div class="modal-footer">
	                      <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
	                      <input type="submit" name="proses" value="Proses" class="btn btn-primary">
	                    </div>
	                  </form>
	                </div>
	              </div>
	            </div><!--end modal-->

			</div>
		</div>
	</div>
</div>

<?php }
break;
case "jawab":
	
	$id_soal 	= $_GET['id_soal'];
	$id_ujian 	= $_GET['id_ujian'];
	//echo $id_soal.' '.$id_ujian;
	$rs=mysqli_fetch_array(mysqli_query($koneksi,"SELECT pertanyaan,gambar FROM soal_esay WHERE id_soal='$id_soal' AND id_tujian='$id_ujian' "));
?>
<div class="row">
	<div class="col-lg-10">
		<div class="card bg-deafult shadow">
			<div class="card-header bg-info py-3 d-flex flex-row align-items-center justify-content-between">
				<h6 class="m-0 font-weight-bold text-danger">Soal Essay</h6>
				<div class="dropdown no-arrow">
					<a href="?module=show_esay&id=<?= $id_ujian;?>"><button class="btn-sm btn-warning"><i class="fas fa-arrow-alt-circle-left"></i> Back</button></a>
				</div>
			</div>
			<form role="form" method="POST" action="?module=show_esay&act=save" enctype="multipart/form-data">
			
			<div class="card-body">
				<h6 class="m-0 font-weight-bold text-primary"><?= $rs['pertanyaan'];?></h6>
				<br>
				<!--<img src="module/foto_soal/<?= $rs['gambar']; ?>" width="80%">-->
				<br>
				<label class="form-group">
					<input type="hidden" name="id_siswa" value="<?= $_SESSION['id_user'];?>">
					<input type="hidden" name="id_ujian" value="<?= $id_ujian;?>">
					<input type="hidden" name="id_soalesay" value="<?= $id_soal;?>">
					Jawab<br>
					<textarea cols="100" class="editor_jawab" name="jawaban_esay" placeholder="Place some text here" required="required"></textarea>
				</label>
			</div>
			<div class="card-footer">
				<input type="submit" name="simpan" class="btn-sm btn-primary" value="Simpan">
			</div>

			</form>
		</div>
	</div>
</div>
<?php 
break;
case "edit":
	
	$id_soal 	= $_GET['id_soal'];
	$id_ujian 	= $_GET['id_ujian'];
	$id_nesay 	= $_GET['id'];
	//echo $id_soal.' '.$id_ujian;
	$rs=mysqli_fetch_array(mysqli_query($koneksi,"SELECT pertanyaan,gambar FROM soal_esay WHERE id_soal='$id_soal' AND id_tujian='$id_ujian' "));
	$re=mysqli_fetch_array(mysqli_query($koneksi,"SELECT jawaban_esay FROM nilai_esay WHERE id_nesay='$id_nesay'"));
?>
<div class="row">
	<div class="col-lg-8">
		<div class="card bg-deafult shadow">
			<div class="card-header bg-info py-3 d-flex flex-row align-items-center justify-content-between">
				<h6 class="m-0 font-weight-bold text-danger">Edit Soal Essay</h6>
				<div class="dropdown no-arrow">
					<a href="?module=show_esay&id=<?= $id_ujian;?>"><button class="btn-sm btn-warning"><i class="fas fa-arrow-alt-circle-left"></i> Back</button></a>
				</div>
			</div>
			<form role="form" method="POST" action="?module=show_esay&act=save_edit" enctype="multipart/form-data">
			
			<div class="card-body">
				<h6 class="m-0 font-weight-bold text-primary"><?= $rs['pertanyaan'];?></h6>
				<br>
				<img src="module/foto_soal/<?= $rs['gambar']; ?>" width="80%">
				<br>
				<label class="form-group">
					<input type="hidden" name="id_nesay" value="<?= $id_nesay;?>">
					<input type="hidden" name="id_ujian" value="<?= $id_ujian;?>">
					<br>Jawab<br>
					<textarea class="textarea" name="jawaban_esay" required="required"><?= $re['jawaban_esay'];?></textarea>
				</label>
			</div>
			<div class="card-footer">
				<input type="submit" name="simpan" class="btn-sm btn-primary" value="Simpan">
			</div>

			</form>
		</div>
	</div>
</div>

<?php 
break;
case "save":
	
	$cek_jawaban = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM nilai_esay WHERE id_ujian='$_POST[id_ujian]' AND id_siswa = '$_POST[id_siswa]' AND id_soalesay='$_POST[id_soalesay]'"));
	if($cek_jawaban== 0){

		$sql_save="INSERT INTO nilai_esay(id_siswa,id_ujian,id_soalesay,jawaban_esay,nilai,status) VALUES('$_POST[id_siswa]','$_POST[id_ujian]','$_POST[id_soalesay]','$_POST[jawaban_esay]','0','mengerjakan')";

		if(mysqli_query($koneksi,$sql_save)) {
			save_alert('save','Jawaban di simpan');
			htmlRedirect('media.php?module='.$module.'&act=show_esay&id='.$_POST[id_ujian],1);
		}
		else {
			save_alert('error','Gagal di simpan');
			htmlRedirect('media.php?module='.$module.'&act=show_esay&id='.$_POST[id_ujian],1);
		}
	}
	else {
		save_alert('error','Soal Sudah di Jawab');
		htmlRedirect('media.php?module='.$module.'&act=show_esay&id='.$_POST[id_ujian],1);
	}

	//echo "INSERT INTO nilai_esay(id_siswa,id_ujian,id_soalesay,jawaban_esay,nilai,status) VALUES('$_POST[id_siswa]','$_POST[id_ujian]','$_POST[id_soalesay]','$_POST[jawaban_esay]','0','mengerjakan')";
break;

case "save_edit":
	
	$sql_save="UPDATE nilai_esay SET jawaban_esay = '$_POST[jawaban_esay]' WHERE id_nesay='$_POST[id_nesay]'";

	if(mysqli_query($koneksi,$sql_save)) {
		save_alert('save','Jawaban di simpan');
		htmlRedirect('media.php?module='.$module.'&act=show_esay&id='.$_POST[id_ujian],1);
	}
	else {
		save_alert('error','Gagal di simpan');
		htmlRedirect('media.php?module='.$module.'&act=show_esay&id='.$_POST[id_ujian],1);
	}
	//echo "INSERT INTO nilai_esay(id_siswa,id_ujian,id_soalesay,jawaban_esay,nilai,status) VALUES('$_POST[id_siswa]','$_POST[id_ujian]','$_POST[id_soalesay]','$_POST[jawaban_esay]','0','mengerjakan')";
break;

case "selesai_ujian":
	
	$sql_save="UPDATE nilai_esay SET status = 'selesai' WHERE id_ujian='$_POST[id_ujian]' AND id_siswa='$_POST[id_siswa]'";
	$row=mysqli_fetch_array(mysqli_query($koneksi,"SELECT pembuat FROM topik_ujian WHERE id='$_POST[id_ujian]'"));

	if(mysqli_query($koneksi,$sql_save)) {
		save_alert('save','Ujian Soal Essay Telah Selesai');
		htmlRedirect('media.php?module=ujian_online&act=ujian_online&pmb='.$row[pembuat].'&topik='.$_POST[id_ujian],1);
	}
	else {
		save_alert('error','Gagal di simpan');
		htmlRedirect('media.php?module='.$module.'&act=show_esay&id='.$_POST[id_ujian],1);
	}
	//echo "INSERT INTO nilai_esay(id_siswa,id_ujian,id_soalesay,jawaban_esay,nilai,status) VALUES('$_POST[id_siswa]','$_POST[id_ujian]','$_POST[id_soalesay]','$_POST[jawaban_esay]','0','mengerjakan')";
break;
	}
}
?>
