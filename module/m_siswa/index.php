<script>

function confirmdelete(delUrl) {
if (confirm("Anda yakin ingin menghapus?")) {
document.location = delUrl;
}
}
</script>
<?php

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
      	if ($_SESSION['leveluser']=='admin'){
?>
<div class="row">
	<div class="col-md-12">
		<div class="card shadow">
			<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
		  		<h6 class="m-0 font-weight-bold text-primary">Data Seluruh Siswa Aktif</h6>
		  		 <div class="dropdown no-arrow">
                    <a href="module/m_siswa/form_siswa.xls" target="_blank" role="button">
                      <button class="btn-sm btn-success"><i class="fas fa-file-excel"></i> Form Siswa</button>
                    </a>
                    <a  href="#" role="button">
                      <button class="btn-sm btn-primary" data-toggle="modal" data-target="#upload_data"><i class="fas fa-upload"></i> Upload Data</button>
                    </a>
			  	</div>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-striped" id="data_siswa"  width="100%" cellspacing="0" cellpadding="0">
						<thead>
							<tr>
								<th>NISN</th>
								<th>Nama Siswa</th>
								<th>Alamat</th>
								<th>JK</th>
								<th>View</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>NISN</th>
								<th>Nama Siswa</th>
								<th>Alamat</th>
								<th>JK</th>
								<th>View</th>
							</tr>
						</tfoot>
					</table>
				</div>	
			</div>
		</div>
	</div>
</div>

<!-- Modal Tambah-->
<div class="modal fade" id="upload_data"  role="dialog" >
  	<div class="modal-dialog modal-md" role="document">
    	<form action="?module=m_siswa&act=save" method="POST" role="form" enctype="multipart/form-data">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<h4 class="modal-title" id="myModalLabel">Upload Data</h4>
	      		</div>
		      	<div class="modal-body">
			         <div class="form-group">
			          	<label for="">Upload File</label>
			          		<input type="file" class="form-control" name="fsiswa"  placeholder="NISN">
			         </div>
		      	</div>
		      	<div class="modal-footer">
		        	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		        	<button type="submit" name="upload_siswa" class="btn btn-primary">Simpan</button>
		      	</div>
    		</div>
    	</form>
  	</div>
</div>
<!--end modal-->


<?php }
break;
case 'view_data':
	$id_siswa = $_GET['id'];
	$d=mysqli_fetch_array(mysqli_query($koneksi,"SELECT * FROM siswa WHERE id='$id_siswa'"));
	$e=mysqli_fetch_array(mysqli_query($koneksi,"SELECT a.nama_kelas,b.id_kelas FROM m_kelas a, f_kelas b WHERE a.id_kelas = b.id_kelas AND b.nis='$d[nis]' AND b.tp='$tahun_p'"));

?>

<div class="row">
	<div class="col-lg-12">
		<div class="card shadow">
			<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
	  			<h6 class="m-0 font-weight-bold text-primary">Detail Profil Siswa</h6>
	  		 	<div class="dropdown no-arrow">
                	<a href="?module=m_siswa" class="btn-sm btn-warning"><i class="fas fa-arrow-alt-circle-left"></i> Back</a>
		  		</div>
			</div>
			<div class="card-body">
				<div class="table-responsive">
			        <div class="row">
			          	<div class="col-md-3">
			            	<!-- Profile Image -->
			            	<div class="card card-primary card-outline">
			              		<div class="card-body box-profile">
			                		<div class="text-center">
			                  		<img src="module/foto_siswa/medium_<?= $d['foto'];?>" width="100%" alt="User profile picture"><br><h5><?php echo $d['nama_lengkap'];?></h5>
			                		</div>
			                	</div>
			            	</div>
			           	</div>
			           	<div class="col-md-9">
			           		<div class="card card-primary">
				              	<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
				                	<h5>Detail Data</h5>
				              	</div><!-- /.card-header -->
				              	<div class="card-body">
				                  	<div class="form-group row">
				                        <div class="col-sm-3"><b>NISN </b></div>
				                        <div class="col-sm-6">: <?php echo $d['nis'];?></div>
				                    </div>
				                  	<div class="form-group row">
				                        <div class="col-sm-3"><b>Nama Lengkap </b></div>
				                        <div class="col-sm-6">: <?php echo $d['nama_lengkap'];?></div>
				                    </div>
				                    <div class="form-group row">
				                        <div class="col-sm-3"><b>Jenis Kelamin </b></div>
				                        <div class="col-sm-6">: <?php echo $d['jenis_kelamin'];?></div>
				                    </div>
				                    <div class="form-group row">
				                        <div class="col-sm-3"><b>TTL </b></div>
				                        <div class="col-sm-6">: <?php echo $d['tempat_lahir'].', '.tgl_indo($d['tgl_lahir']);?></div>
				                    </div>
				                    <div class="form-group row">
				                        <div class="col-sm-3"><b>Alamat </b></div>
				                        <div class="col-sm-6">: <?php echo $d['alamat'];?></div>
				                    </div>
				                    <div class="form-group row">
				                        <div class="col-sm-3"><b>No Telp/HP(WA)  </b></div>
				                        <div class="col-sm-6">: <?php echo $d['no_telp'];?></div>
				                    </div>
				                  	<div class="form-group row">
				                  		<div class="col-sm-3"><b>Tahun Masuk  </b></div>
				                        <div class="col-sm-6">: <?php echo $d['th_masuk'];?></div>
				                    </div>
				                    <div class="form-group row">
				                        <div class="col-sm-3"><b>Kelas  </b></div>
				                        <div class="col-sm-6">: <?php echo $e['nama_kelas'];?></div>
				                    </div>
				              	</div>
			              	</div>
		           		</div>
					</div>
				</div>
			</div>
  		</div>
  	</div>
</div>


<?php
case 'edit_data':
	$id_siswa = $_GET['id'];
	$d=mysqli_fetch_array(mysqli_query($koneksi,"SELECT * FROM siswa WHERE id='$id_siswa'"));
	
?>

<div class="row">
	<div class="col-lg-12">
		<div class="card shadow">
			<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
	  			<h6 class="m-0 font-weight-bold text-primary">Detail Profil Siswa</h6>
	  		 	<div class="dropdown no-arrow">
                	<a href="?module=m_siswa" class="btn-sm btn-warning"><i class="fas fa-arrow-alt-circle-left"></i> Back</a>
		  		</div>
			</div>
			<div class="card-body">
				<div class="table-responsive">
			        <div class="row">
			          	
			           	<div class="col-md-12">
			           		<div class="card card-primary">
				              	<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
				                	<h5>Edit Data</h5>
				              	</div><!-- /.card-header -->
				              	<div class="card-body">
				              		<form method="POST" action="?module=m_siswa&act=save" enctype="multipart/form-data">
					                  	<div class="form-group row">
					                        <div class="col-sm-3"><b>NISN </b></div>
					                        <input type="hidden" name="id" value="<?=$d['id'];?>">
					                        <div class="col-sm-6"><input type="text" name="nisn" class="form-control" value="<?php echo $d['nis'];?>" readonly="readonly"></div>
					                    </div>
					                  	<div class="form-group row">
					                        <div class="col-sm-3"><b>Nama Lengkap </b></div>
					                        <div class="col-sm-6"><input type="text" name="nama_lengkap" class="form-control" value="<?php echo $d['nama_lengkap'];?>"></div>
					                    </div>
					                    <div class="form-group row">
					                        <div class="col-sm-3"><b>Jenis Kelamin </b></div>
					                        <div class="col-sm-6">
					                        	<select name="jenis_kelamin" class="form-control">
					                        	<?php 
					                        		if($d['jenis_kelamin']=='L') {
					                        			$sel = 'selected';
					                        			$sep = '';
					                        		}
					                        		else {
					                        			$sel = '';
					                        			$sep = 'selected';
					                        		}
					                        	 ?>
					                        	 	<option value="L" <?=$sel;?>>Laki-laki</option>
					                        	 	<option value="P" <?=$seP;?>>Perempuan</option>
					                        	</select>
					                    	</div>
					                    </div>
					                    <div class="form-group row">
					                        <div class="col-sm-3"><b>Tempat Lahir</b></div>
					                        <div class="col-sm-6"><input type="text" name="tempat_lahir" class="form-control" value="<?php echo $d['tempat_lahir'];?>">
					                        </div>
					                    </div>
					                    <div class="form-group row">
					                        <div class="col-sm-3"><b>Tanggal Lahir</b></div>
					                        <div class="col-sm-6"><input type="date" name="tgl_lahir" class="form-control" value="<?php echo $d['tgl_lahir'];?>">
					                        </div>
					                    </div>
					                    <div class="form-group row">
					                        <div class="col-sm-3"><b>Alamat </b></div>
					                        <div class="col-sm-6"><input type="text" name="alamat" class="form-control" value="<?php echo $d['alamat'];?>"> </div>
					                    </div>
					                    <div class="form-group row">
					                        <div class="col-sm-3"><b>No Telp/HP(WA)  </b></div>
					                        <div class="col-sm-6"><input type="text" name="no_telp" class="form-control" value="<?php echo $d['no_telp'];?>"> </div>
					                    </div>
					                  	<div class="form-group row">
					                  		<div class="col-sm-3"><b>Tahun Masuk  </b></div>
					                        <div class="col-sm-6"><input type="number" name="th_masuk" class="form-control" value="<?php echo $d['th_masuk'];?>"> </div>
					                    </div>
					                    <div class="form-group row">
					                        <div class="col-sm-9 text-right"><input type="submit" name="edit_data" class="btn btn-sm btn-primary" value="Simpan"> </div>
					                    </div>
				                    </form>
				              	</div>
			              	</div>
		           		</div>
					</div>
				</div>
			</div>
  		</div>
  	</div>
</div>
<?php 
break;
case 'save':
include 'save.php';
break;
	}
}
?>
