<?php
session_start();
if (empty($_SESSION['namauser']) && empty($_SESSION['passuser'])) {
    header('location:../index.php');
    exit();
} else {
    

    // SIMPAN UJIAN BARU
    if (isset($_POST['simpan_tu'])) {

        // Ambil id terbaru lalu increment
        $id = mysqli_fetch_array(mysqli_query($koneksi, "SELECT MAX(id) as jum FROM topik_ujian"));
        $idmax = $id['jum'] + 1;

        // Konversi menit ke detik
        $waktu = $_POST['waktu_pengerjaan'] * 60;

        // Insert data ujian ke topik_ujian
        $data = "INSERT INTO topik_ujian(
                    id, judul, id_mapel, tgl_buat, pembuat, waktu_pengerjaan, info, 
                    bobot_pg, bobot_esay, terbit
                 ) VALUES (
                    '$idmax', 
                    '{$_POST['judul']}', 
                    '{$_POST['id_mapel']}', 
                    '$tgl_sekarang', 
                    '{$_SESSION['id_user']}', 
                    '$waktu', 
                    '{$_POST['info']}', 
                    '{$_POST['bobot_pg']}', 
                    '{$_POST['bobot_esay']}', 
                    '{$_POST['terbit']}'
                 )";

        if (mysqli_query($koneksi, $data)) {

            // Pastikan id_kelas diambil dari form dan benar-benar ada
            if (!empty($_POST['id_kelas']) && is_array($_POST['id_kelas'])) {
                foreach ($_POST['id_kelas'] as $kelas) {
                    $kelas = trim($kelas);
                    if (!empty($kelas)) {
                        // Ambil ID terakhir di kelas_ujian
                        $last_id_row = mysqli_fetch_array(mysqli_query($koneksi, "SELECT MAX(id) as max_id FROM kelas_ujian"));
                        $new_id = $last_id_row['max_id'] + 1;

                        // Insert hubungan kelas-ujian
                        mysqli_query($koneksi, "INSERT INTO kelas_ujian (id, id_kelas, id_topik) VALUES ('$new_id', '$kelas', '$idmax')");
                    }
                }
            }

            save_alert('save', 'Ujian Berhasil di Tambahkan');
            htmlRedirect('media.php?module=' . $module . '&act=ug_ujian');

        } else {
            save_alert('error', 'Gagal Tersimpan');
            htmlRedirect('media.php?module=' . $module . '&act=ug_ujian');
        }
    }

    // UPDATE UJIAN
    elseif (isset($_POST['update_tu'])) {

        $waktu = $_POST['waktu_pengerjaan'] * 60;

        $data  = "UPDATE topik_ujian 
                  SET judul = '{$_POST['judul']}', 
                      id_mapel = '{$_POST['id_mapel']}', 
                      waktu_pengerjaan = '$waktu', 
                      info = '{$_POST['info']}', 
                      bobot_pg = '{$_POST['bobot_pg']}', 
                      bobot_esay = '{$_POST['bobot_esay']}', 
                      terbit = '{$_POST['terbit']}' 
                  WHERE id='{$_POST['id_tujian']}'";

        if (mysqli_query($koneksi, $data)) {

            // Hapus semua kelas lama
            mysqli_query($koneksi, "DELETE FROM kelas_ujian WHERE id_topik='{$_POST['id_tujian']}'");

            // Masukkan kelas baru
            if (!empty($_POST['id_kelas']) && is_array($_POST['id_kelas'])) {
                foreach ($_POST['id_kelas'] as $kelas) {
                    $kelas = trim($kelas);
                    if (!empty($kelas)) {
                        $last_id_row = mysqli_fetch_array(mysqli_query($koneksi, "SELECT MAX(id) as max_id FROM kelas_ujian"));
                        $new_id = $last_id_row['max_id'] + 1;

                        mysqli_query($koneksi, "INSERT INTO kelas_ujian (id, id_kelas, id_topik) VALUES ('$new_id', '$kelas', '{$_POST['id_tujian']}')");
                    }
                }
            }

            save_alert('save', 'Ujian Berhasil di Update');
            htmlRedirect('media.php?module=' . $module . '&act=ug_ujian');

        } else {
            save_alert('error', 'Gagal Tersimpan');
            htmlRedirect('media.php?module=' . $module . '&act=ug_ujian');
        }
    }

    // PROSES KOREKSI
    elseif (isset($_POST['proses_koreksi'])) {
        $data = "UPDATE nilai_esay SET nilai = '{$_POST['nilai']}' WHERE id_nesay='{$_POST['id_nesay']}'";
        if (mysqli_query($koneksi, $data)) {
            save_alert('save', 'Soal dikoreksi');
        } else {
            save_alert('error', 'Gagal dikoreksi');
        }
        htmlRedirect('media.php?module=' . $module . '&act=koreksi&ujian=' . $_POST['ujian'] . '&siswa=' . $_POST['siswa']);
    }

    // SIMPAN PILIHAN GANDA
    elseif (isset($_POST['simpan_pilganda'])) {
        $lokasi_file = $_FILES['fupload']['tmp_name'];
        $nama_file = $_FILES['fupload']['name'];
        $tipe_file = $_FILES['fupload']['type'];

        if (!empty($lokasi_file)) {
            if ($tipe_file != "image/jpeg" && $tipe_file != "image/jpg") {
                save_alert('error', 'Type File Tidak diizinkan');
                htmlRedirect('media.php?module=' . $module . '&act=pil_ganda&id=' . $_POST['id_tujian'], 1);
            } else {
                UploadImage_soal_pilganda($nama_file);
                mysqli_query($koneksi, "INSERT INTO soal_pilganda (
                    id_tujian, pertanyaan, gambar, pil_a, pil_b, pil_c, pil_d, pil_e, kunci, tgl_buat, jenis_soal
                ) VALUES (
                    '{$_POST['id_tujian']}', '{$_POST['pertanyaan']}', '$nama_file',
                    '{$_POST['pil_a']}', '{$_POST['pil_b']}', '{$_POST['pil_c']}',
                    '{$_POST['pil_d']}', '{$_POST['pil_e']}', '{$_POST['kunci']}',
                    '$tgl_sekarang', 'pil_ganda'
                )");
                save_alert('save', 'Soal Tersimpan');
                htmlRedirect('media.php?module=' . $module . '&act=pil_ganda&id=' . $_POST['id_tujian'], 1);
            }
        } else {
            mysqli_query($koneksi, "INSERT INTO soal_pilganda (
                id_tujian, pertanyaan, gambar, pil_a, pil_b, pil_c, pil_d, pil_e, kunci, tgl_buat, jenis_soal
            ) VALUES (
                '{$_POST['id_tujian']}', '{$_POST['pertanyaan']}', '',
                '{$_POST['pil_a']}', '{$_POST['pil_b']}', '{$_POST['pil_c']}',
                '{$_POST['pil_d']}', '{$_POST['pil_e']}', '{$_POST['kunci']}',
                '$tgl_sekarang', 'pil_ganda'
            )");
            save_alert('save', 'Soal Tersimpan');
            htmlRedirect('media.php?module=' . $module . '&act=pil_ganda&id=' . $_POST['id_tujian'], 1);
        }
    }

    // SIMPAN ESSAY
    elseif (isset($_POST['simpan_essay'])) {
        if (!isset($tgl_sekarang) || empty($tgl_sekarang)) {
            $tgl_sekarang = date('Y-m-d');
        }
    
        $id_tujian  = intval($_POST['id_tujian']);
        $pertanyaan = mysqli_real_escape_string($koneksi, $_POST['pertanyaan']);
    
        $lokasi_file = $_FILES['fupload']['tmp_name'];
        $nama_file   = $_FILES['fupload']['name'];
        $tipe_file   = $_FILES['fupload']['type'];
    
        if (!empty($lokasi_file)) {
            if ($tipe_file != "image/jpeg" && $tipe_file != "image/jpg" && $tipe_file != "image/png") {
                save_alert('error', 'Type file tidak diizinkan');
                htmlRedirect('media.php?module='.$module.'&act=essay&id='.$id_tujian, 1);
                exit();
            }
            // Upload gambar
            UploadImage_soal($nama_file);
            $gambar = $nama_file;
        } else {
            $gambar = ''; 
        }
    
        $query = "INSERT INTO soal_esay (id_tujian, pertanyaan, gambar, tgl_buat, jenis_soal)
                  VALUES ('$id_tujian', '$pertanyaan', '$gambar', '$tgl_sekarang', 'essay')";
    
        if (mysqli_query($koneksi, $query)) {
            save_alert('save', 'Soal essay tersimpan');
        } else {
            save_alert('error', 'Gagal menyimpan soal essay: '.mysqli_error($koneksi));
        }
    
        htmlRedirect('media.php?module='.$module.'&act=essay&id='.$id_tujian, 1);
    }

    // AMBIL ESSAY DARI BANK SOAL
elseif (isset($_POST['essay_bank'])) {
    if (!isset($tgl_sekarang) || empty($tgl_sekarang)) {
        $tgl_sekarang = date('Y-m-d');
    }
    // Debug log

 // Hentikan agar kita bisa baca hasil debug


    $id_tujian = intval($_POST['id_tujian']);
    $id_soal   = isset($_POST['id']) ? $_POST['id'] : [];


    $gagal_id = [];

    if (!empty($id_soal) && is_array($id_soal)) {
        foreach ($id_soal as $soal_id) {
            $soal_id = intval($soal_id);
           
$sql = mysqli_query($koneksi, "SELECT pertanyaan FROM bank_esay WHERE id='$soal_id'");
  
          if ($cd = mysqli_fetch_array($sql)) {
   
    $pertanyaan = mysqli_real_escape_string($koneksi, $cd['pertanyaan']);
    $gambar = mysqli_real_escape_string($koneksi, $cd['gambar']);

    if (trim($pertanyaan) === '') {
        echo "<div style='color:red'>[Debug] Soal ID $soal_id ditemukan tapi kolom `pertanyaan` kosong!</div>";
    } else {
        echo "<div style='color:green'>[Debug] Soal ID $soal_id: " . htmlentities($pertanyaan) . "</div>";
    }

    mysqli_query(
        $koneksi,
        "INSERT INTO soal_esay (id_tujian, pertanyaan, gambar, tgl_buat, jenis_soal)
        VALUES ('$id_tujian', '$pertanyaan', '$gambar', '$tgl_sekarang', 'essay')"
    ) or die(mysqli_error($koneksi));
}else {
                $gagal_id[] = $soal_id;
            }
        }


        if (count($gagal_id) > 0) {
            echo "<div style='color:red'>Soal ID " . implode(', ', $gagal_id) . " tidak ditemukan di bank soal.</div>";
        }

        save_alert('save', 'Soal dari bank soal berhasil ditambahkan');
    } else {
        save_alert('error', 'Tidak ada soal yang dipilih');
    }
     

    htmlRedirect('media.php?module='.$module.'&act=essay&id='.$id_tujian, 1);
}


// UPDATE ESSAY
elseif (isset($_POST['update_essay'])) {
    if (!isset($tgl_sekarang) || empty($tgl_sekarang)) {
        $tgl_sekarang = date('Y-m-d');
    }

    $id_soal    = intval($_POST['id_soal']);
    $id_tujian  = intval($_POST['id_tujian']);
    $pertanyaan = mysqli_real_escape_string($koneksi, $_POST['pertanyaan']);

    $lokasi_file = $_FILES['fupload']['tmp_name'];
    $nama_file   = $_FILES['fupload']['name'];
    $tipe_file   = $_FILES['fupload']['type'];

    if (!empty($lokasi_file)) {
        if ($tipe_file != "image/jpeg" && $tipe_file != "image/jpg" && $tipe_file != "image/png") {
            save_alert('error', 'Type file tidak diizinkan');
            htmlRedirect('media.php?module='.$module.'&act=essay&id='.$id_tujian, 1);
            exit();
        }

        // Ambil gambar lama untuk dihapus
        $old = mysqli_fetch_array(mysqli_query($koneksi, "SELECT gambar FROM soal_esay WHERE id_soal='$id_soal'"));
        if (!empty($old['gambar'])) {
            @unlink("module/foto_soal/".$old['gambar']);
            @unlink("module/foto_soal/medium_".$old['gambar']);
        }

        // Upload gambar baru
        UploadImage_soal($nama_file);
        $gambar_sql = ", gambar='$nama_file'";
    } else {
        $gambar_sql = ""; // Tidak ubah gambar
    }

    $query = "UPDATE soal_esay 
              SET pertanyaan='$pertanyaan', tgl_buat='$tgl_sekarang' $gambar_sql
              WHERE id_soal='$id_soal'";

    if (mysqli_query($koneksi, $query)) {
        save_alert('save', 'Soal essay berhasil diupdate');
    } else {
        save_alert('error', 'Gagal update soal essay: '.mysqli_error($koneksi));
    }

    htmlRedirect('media.php?module='.$module.'&act=essay&id='.$id_tujian, 1);
}

// SIMPAN PILIHAN GANDA DARI BANK SOAL
elseif (isset($_POST['pg_bank'])) {
    if (!isset($tgl_sekarang) || empty($tgl_sekarang)) {
        $tgl_sekarang = date('Y-m-d');
    }

    $id_soal_list = $_POST['id']; // array id bank soal
    $id_tujian    = intval($_POST['id_tujian']);
    $n = count($id_soal_list);

    if ($n > 0) {
        foreach ($id_soal_list as $id_bank) {
            $id_bank = intval($id_bank);
            $cd = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM bank_pilganda WHERE id='$id_bank'"));

            if ($cd) {
                $pertanyaan = mysqli_real_escape_string($koneksi, $cd['pertanyaan']);
                $pil_a = mysqli_real_escape_string($koneksi, $cd['pil_a']);
                $pil_b = mysqli_real_escape_string($koneksi, $cd['pil_b']);
                $pil_c = mysqli_real_escape_string($koneksi, $cd['pil_c']);
                $pil_d = mysqli_real_escape_string($koneksi, $cd['pil_d']);
                $pil_e = mysqli_real_escape_string($koneksi, $cd['pil_e']);
                $kunci = mysqli_real_escape_string($koneksi, $cd['kunci']);
                $gambar = !empty($cd['gambar']) ? $cd['gambar'] : '';

                mysqli_query($koneksi, "INSERT INTO soal_pilganda (
                    id_tujian, pertanyaan, pil_a, pil_b, pil_c, pil_d, pil_e, kunci, gambar, tgl_buat, jenis_soal
                ) VALUES (
                    '$id_tujian', '$pertanyaan', '$pil_a', '$pil_b', '$pil_c', '$pil_d', '$pil_e', '$kunci', '$gambar', '$tgl_sekarang', 'pil_ganda'
                )");
            }
        }
        save_alert('save', 'Soal dari bank PG berhasil disalin');
    } else {
        save_alert('error', 'Tidak ada soal yang dipilih dari bank');
    }

    htmlRedirect('media.php?module='.$module.'&act=pil_ganda&id='.$id_tujian, 1);
}

// UPDATE PILIHAN GANDA
elseif (isset($_POST['update_pilganda'])) {
    if (!isset($tgl_sekarang) || empty($tgl_sekarang)) {
        $tgl_sekarang = date('Y-m-d');
    }

    $id_soalpg  = intval($_POST['id_soalpg']);
    $pertanyaan = mysqli_real_escape_string($koneksi, $_POST['pertanyaan']);
    $pil_a      = mysqli_real_escape_string($koneksi, $_POST['pil_a']);
    $pil_b      = mysqli_real_escape_string($koneksi, $_POST['pil_b']);
    $pil_c      = mysqli_real_escape_string($koneksi, $_POST['pil_c']);
    $pil_d      = mysqli_real_escape_string($koneksi, $_POST['pil_d']);
    $pil_e      = mysqli_real_escape_string($koneksi, $_POST['pil_e']);
    $kunci      = mysqli_real_escape_string($koneksi, $_POST['kunci']);

    $lokasi_file = $_FILES['fupload']['tmp_name'];
    $nama_file   = $_FILES['fupload']['name'];
    $tipe_file   = $_FILES['fupload']['type'];

    if (!empty($lokasi_file)) {
        if (!in_array($tipe_file, ["image/jpeg", "image/jpg", "image/png"])) {
            save_alert('error', 'Type file tidak diizinkan');
            htmlRedirect('media.php?module='.$module.'&act=pil_ganda&id='.$_POST['id_tujian'], 1);
            exit();
        }
        $cek = mysqli_fetch_array(mysqli_query($koneksi, "SELECT gambar FROM soal_pilganda WHERE id_soalpg='$id_soalpg'"));
        if (!empty($cek['gambar'])) {
            @unlink("module/foto_soal_pilganda/".$cek['gambar']);
            @unlink("module/foto_soal_pilganda/medium_".$cek['gambar']);
        }
        UploadImage_soal_pilganda($nama_file);
        $gambar = $nama_file;
    } else {
        $cek = mysqli_fetch_array(mysqli_query($koneksi, "SELECT gambar FROM soal_pilganda WHERE id_soalpg='$id_soalpg'"));
        $gambar = $cek['gambar'];
    }

    $query = "UPDATE soal_pilganda 
              SET pertanyaan='$pertanyaan', 
                  pil_a='$pil_a', pil_b='$pil_b', pil_c='$pil_c', pil_d='$pil_d', pil_e='$pil_e', 
                  kunci='$kunci', 
                  gambar='$gambar', 
                  tgl_buat='$tgl_sekarang' 
              WHERE id_soalpg='$id_soalpg'";

    if (mysqli_query($koneksi, $query)) {
        save_alert('save', 'Soal pilihan ganda berhasil diperbarui');
    } else {
        save_alert('error', 'Gagal update: '.mysqli_error($koneksi));
    }

    htmlRedirect('media.php?module='.$module.'&act=pil_ganda&id='.$_POST['id_tujian'], 1);
}




    

}
?>
