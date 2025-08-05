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
} else {
	switch($_GET['act']){
		default:
		  if ($_SESSION['leveluser']=='user_siswa'){

			include "style_menu.php";
			$id_ujian = $_GET['id'];
			$rujian = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM topik_ujian WHERE id='$id_ujian'"));

			$pg = mysqli_fetch_array(mysqli_query($koneksi,"SELECT COUNT(id_soalpg) as jum FROM soal_pilganda WHERE id_tujian='$id_ujian'"));
			$qsoal = mysqli_query($koneksi, "SELECT * FROM soal_pilganda WHERE id_tujian='$id_ujian' ORDER BY rand() LIMIT $pg[jum]");
			$q2soal = mysqli_query($koneksi, "SELECT * FROM soal_pilganda WHERE id_tujian='$id_ujian' ORDER BY id_soalpg");

			if(mysqli_num_rows($qsoal)==0) die('<div class="alert alert-warning">Belum ada soal pada ujian ini</div>');

			$arr_soal = array();
			$arr_jawaban = array();
			while($rsoal = mysqli_fetch_array($qsoal)){
				$arr_soal[] = $rsoal['id_soalpg'];
				$arr_jawaban[] = 0;
			}
			$soalid = array();
			while($r2soal = mysqli_fetch_array($q2soal)){
				$soalid[] = $r2soal['id_soalpg'];
			}

			$acak_soal = implode(",", $arr_soal);
			$jawaban = implode(",", $arr_jawaban);

			$qnilai = mysqli_query($koneksi, "SELECT * FROM nilai WHERE id_siswa='$_SESSION[id_user]' AND id_ujian='$id_ujian'");
			if (mysqli_num_rows($qnilai) < 1) {
				$now = time();
				$durasi = intval($rujian['waktu_pengerjaan']);
				$waktu_selesai_timestamp = $now + $durasi;
				$waktu_selesai = date("H:i:s", $waktu_selesai_timestamp);
				$sisa_waktu = gmdate("H:i:s", $durasi);

				mysqli_query($koneksi, "INSERT INTO nilai SET 
					id_siswa='{$_SESSION['id_user']}',
					id_ujian='$id_ujian',
					acak_soal='$acak_soal',
					jawaban='$jawaban',
					sisa_waktu='$sisa_waktu',
					waktu_selesai='$waktu_selesai',
					status='mengerjakan',
					jml_benar=0,
					jml_kosong=0,
					jml_salah=0,
					nilai=0
				") or die(mysqli_error($koneksi));

				foreach ($soalid as $kelas) {
					mysqli_query($koneksi, "INSERT INTO analisis SET 
						id_siswa='{$_SESSION['id_user']}',
						id_ujian='$id_ujian',
						id_soal='$kelas',
						jawaban='0'");
				}
			}

			$qnilai = mysqli_query($koneksi, "SELECT * FROM nilai WHERE id_siswa='$_SESSION[id_user]' AND id_ujian='$id_ujian'");
			$rnilai = mysqli_fetch_array($qnilai);
			$sisa_waktu = explode(":", $rnilai['sisa_waktu']);

			echo '<div class="row"><div class="col-lg-12"><div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">';
			echo '<h6 class="m-0 font-weight-bold text-primary">UJIAN : '.$rujian['judul'].'</h6>';
			echo '<input type="hidden" id="ujian" value="'.$id_ujian.'">';
            echo '<b>Sisa Waktu : </b><button id="h_timer" class="btn-sm btn-danger"></button>';

			echo '<input type="hidden" id="jam" value="'.$sisa_waktu[0].'">';
			echo '<input type="hidden" id="menit" value="'.$sisa_waktu[1].'">';
			echo '<input type="hidden" id="detik" value="'.$sisa_waktu[2].'">';
			echo '</div></div></div>';

			echo '<div class="row">';

			$arr_soal = explode(",", $rnilai['acak_soal']);
			$arr_jawaban = explode(",", $rnilai['jawaban']);

			for($s=0; $s<count($arr_soal); $s++) {
				$rsoal = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM soal_pilganda WHERE id_soalpg='$arr_soal[$s]'"));
				$no = $s+1;
				$soal = str_replace("../media", "../media", $rsoal['pertanyaan']);
				echo '<div class="col-lg-12"><div class="blok-soal soal-'.$no.' active">';
				echo '<div class="card shadow"><div class="card-body">';
				echo '<p class="soal">'.$soal.'</p><br><table>'; 

				$arr_pilihan = [
					["no" => 1, "pilihan" => $rsoal['pil_a']],
					["no" => 2, "pilihan" => $rsoal['pil_b']],
					["no" => 3, "pilihan" => $rsoal['pil_c']],
					["no" => 4, "pilihan" => $rsoal['pil_d']],
					["no" => 5, "pilihan" => $rsoal['pil_e']]
				];
				$arr_huruf = ["A","B","C","D","E"];

				foreach($arr_pilihan as $i => $pil){
                    $checked = ($arr_jawaban[$s] == $arr_pilihan[$i]['no']) ? "checked" : "";
                    $pilihan = str_replace("../media", "../media", $arr_pilihan[$i]['pilihan']);
                    $pilihan = str_replace("p>", "b>", $pilihan);
                  
                    echo '
                    <tr>
                      <td valign="top" colspan="2">
                        <label class="label-jawaban">
                          <input type="radio" name="jawab-'.$no.'" id="pilihan-'.$no.'-'.$i.'" '.$checked.' 
                                 onclick="kirim_jawaban('.$s.', '.$arr_pilihan[$i]['no'].')">
                          <b>'.$arr_huruf[$i].'.</b> &nbsp; '.$pilihan.'
                        </label>
                      </td>
                    </tr>';
				}

				echo '</table></div><div class="card-footer"><div class="kakisoal" style="width: 97.7%;">';
				echo '<a onclick="selesai()"><button class="btn-sm btn-danger btn-next activebutton">SELESAI</button></a>';
				echo '</div></div></div></div></div>';
			}

			echo '</div>';
			echo '<div class="modal fade" id="modal-selesai" role="dialog"><form method="POST" action="?module=sis_ujian&act=selesai_ujian" enctype="multipart/form-data" role="form">';
			echo '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">Konfirmasi Tes</h4></div><div class="modal-body">';
			echo '<input type="hidden" name="ujian" value="'.$id_ujian.'">';
			echo '<p>Terimakasih telah berpartisipasi dalam tes ini.<br>Silahkan klik tombol SELESAI untuk mengakhiri test.</p></div>';
			echo '<div class="modal-footer"><button type="submit" class="btn btn-success" name="simpan">SELESAI</button><button type="button" class="btn btn-danger" data-dismiss="modal">TIDAK</button></div></div></div></form></div>';
		  }
		  break;
		case "selesai_ujian":
			$id_ujian = $_POST['ujian'];
			$id_siswa = $_SESSION['id_user'];
			mysqli_query($koneksi, "UPDATE nilai SET status='selesai', sisa_waktu='00:00:00' WHERE id_siswa='$id_siswa' AND id_ujian='$id_ujian'");
			header("Location: ?module=sis_ujian");
			break;
	}
}
?>
