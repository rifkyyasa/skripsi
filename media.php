<?php
error_reporting(0);
session_start();
if (empty($_SESSION['namauser']) AND empty($_SESSION['passuser'])){
include "index.html";
}else{
  include "config/koneksi.php";
  include "config/fungsi_auto_id.php";
  include "config/fungsi_indotgl.php";
  // include "config/fungsi_combobox.php";
  include "config/library.php";
  include "config/fungsi.php";
  include "config/fungsi_upload_image.php";
  include "config/excel_reader2.php";
  include "config/class_paging.php";

  include "timeout.php";

  if($_SESSION['login']==1){
    if(!cek_login()){
    $_SESSION['login'] = 0;
    }
  }
  if($_SESSION['login']==0){
    header('location:logout_timeout.php');
  }
  
  ?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Elektronik Learning <?= $sis_singkat;?></title>

  <!-- Custom fonts for this template-->
  <link href="plugin/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <link rel="icon" type="image/png" sizes="32x32" href="https://smkmuh2plg.sch.id/public/img/ic_sekolah.png">

  <!-- Custom styles for this template-->
  <link href="dist/css/sb-admin-2.min.css" rel="stylesheet">
  <!-- Custom styles for this page -->
  <link href="plugin/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
  <!-- Select2 -->
  <link rel="stylesheet" href="plugin/select2/css/select2.min.css">
  <link rel="stylesheet" href="plugin/select2-bootstrap4-theme/select2-bootstrap4.min.css">

  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="plugin/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
  <!-- summernote -->
  <link rel="stylesheet" href="plugin/summernote/summernote-bs4.css">

  <!-- KaTeX -->
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.11.1/dist/katex.min.css" integrity="sha384-zB1R0rpPzHqg7Kpt0Aljp8JPLqbXI3bhnPWROx27a9N0Ll6ZP/+DiW/UqRcLbRjq" crossorigin="anonymous">

 

  <!-- CSS Ujian-->
  <!--<link href="dist/css/ujian.css" rel="stylesheet">-->
</head>
<body id="page-top" >

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-info sidebar sidebar-dark accordion" id="accordionSidebar">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
        <div class="sidebar-brand-icon rotate-n-5">
          <img src="dist/img/e-learning-white.png" width="100%">
        </div>
        <div class="sidebar-brand-text mx-3">Belajar <sup>Online</sup></div>
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <?php include 'sidebar.php';?>
      <!-- Divider -->
      <hr class="sidebar-divider d-none d-md-block">

      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>

    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->

        <!-- Sidebar Toggle (Topbar) -->
          <nav class="navbar navbar-expand navbar-light bg-gradient-white topbar mb-3 static-top shadow">
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
            <img src="dist/img/<?=$logo_nav;?>" width="3%">

          <!-- Sidebar Toggle (Topbar) -->

          <?php include 'navbar.php';?>

        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">

           <?php
            
            $module = $_GET['module'];
            $pathFile = 'module/'.$_GET['module'].'/index.php';
            if (file_exists($pathFile))
              {
                $modul=$_GET['module'];

                //Head Menu Guru
                if($modul=='ug_home'){$judul_head="Beranda";}
                elseif($modul=='ug_materi'){$judul_head="Menu Manajemen Materi";}
                elseif($modul=='ug_ujian'){$judul_head="Menu Manajemen Ujian / Tugas";}
                elseif($modul=='ug_video'){$judul_head="Menu Manajemen Video";}
                elseif($modul=='ug_forum'){$judul_head="Forum Diskusi ";}
                elseif($modul=='ug_chat'){$judul_head="Percakapan dengan siswa";}
                elseif($modul=='ug_pesan'){$judul_head="Manajemen Pesan";}
                elseif($modul=='ug_bank'){$judul_head="Manajemen Bank Soal";}

                //Head Menu Siswa
                elseif($modul=='sis_home'){$judul_head="Beranda";}
                elseif($modul=='sis_materi'){$judul_head="Modul / Materi ";}
                elseif($modul=='sis_ujian'){$judul_head="Menu Manajemen Ujian / Tugas";}
                elseif($modul=='ujian_online'){$judul_head="Informasi Ujian/Tugas";}
                elseif($modul=='show_ujian'){$judul_head="Ujian Pilihan Ganda";}
                elseif($modul=='show_esay'){$judul_head="Ujian Soal Essay";}
                elseif($modul=='sis_video'){$judul_head="Video Interaktif";}
                elseif($modul=='sis_forum'){$judul_head="Forum Diskusi ";}
                elseif($modul=='sis_chat'){$judul_head="Percakapan dengan Guru";}

                //MENU BERSAMA
                elseif($modul=='ug_profil'){$judul_head="Menu Profil";}
                elseif($modul=='ug_setting'){$judul_head="Menu Ganti Password";}
          ?>
             <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4 bg-gradient-info">
            <h4 class="h4 mb-0 text-white-800" style="color: white;"><?= $judul_head; ?></h4>
          </div>
          <?php
            include 'module/'.$_GET['module'].'/index.php';
            }
            else {
          ?>
          

          <!-- 404 Error Text -->
          <div class="text-center">
            <div class="error mx-auto" data-text="404">404</div>
            <p class="lead text-gray-800 mb-5">Modul Tidak ditemukan</p>
            <p class="text-gray-500 mb-0">Modul yang anda cari belum tersedia</p>
            <a href="?module=home">&larr; Kembali ke Dashboard</a>
          </div>
        <?php } ?>

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer shadow bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; <?= $sis_panjang;?></span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Yakin Akan Logout?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Pilih tombol <a class="btn btn-primary" href="#">Logout</a> dibawah untuk logout.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <a class="btn btn-primary" href="logout.php">Logout</a>
        </div>
      </div>
    </div>
  </div>

<!-- Bootstrap core JavaScript-->
  <script src="plugin/jquery/jquery.min.js"></script>
  <script src="plugin/bootstrap/js/bootstrap.bundle.min.js"></script>

  <script>
$(document).ready(function(){
 
 function load_unseen_notification(view = '')
 {
  $.ajax({
   url:"notif_pesan.php",
   method:"POST",
   data:{view:view},
   dataType:"json",
   success:function(data)
   {
    $('#notif_pesan').html(data.notification);
    if(data.unseen_notification > 0)
    {
     $('.count').html(data.unseen_notification);
    }
   }
  });
 }
 
 load_unseen_notification('');
 
 $(document).on('click', '.dropdown-pesan', function(){
  $('.count').html('');
  load_unseen_notification('yes');
 });

 
 setInterval(function(){ 
  load_unseen_notification();; 
 }, 1000);
 
 
});
</script>

  <!-- Core plugin JavaScript-->
  <script src="plugin/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="dist/js/sb-admin-2.min.js"></script>

  <!-- CHAR DATA -->
  <?php 
  $log_tgl = array();
  $isi_log = array();

  $sql_data = mysqli_query($koneksi,"SELECT COUNT(id_user) as user, tgl FROM log_user GROUP BY tgl  ORDER BY tgl DESC LIMIT 7");

  foreach ($sql_data as $r ) {
    $log_user[]=substr(tgl_indo($r['tgl']),0,6);
    $isi_log[]=$r['user'];
  }
  $tanggal=json_encode($log_user);
  $jumlah = json_encode($isi_log);
  //print_r($jumlah);
  ?>
  <!-- CHART PLUGIN-->
  <script src="plugin/chart.js/Chart.min.js"></script>
  <script type="text/javascript">
  // Set new default font family and font color to mimic Bootstrap's default styling
    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';

    function number_format(number, decimals, dec_point, thousands_sep) {
      // *     example: number_format(1234.56, 2, ',', ' ');
      // *     return: '1 234,56'
      number = (number + '').replace(',', '').replace(' ', '');
      var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
          var k = Math.pow(10, prec);
          return '' + Math.round(n * k) / k;
        };
      // Fix for IE parseFloat(0.55).toFixed(0) = 0;
      s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
      if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
      }
      if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
      }
      return s.join(dec);
    }

    // Area Chart Example
    var ctx = document.getElementById("log_user");
    var myLineChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?=$tanggal;?>,
        datasets: [{
          label: "Jumlah Login",
          lineTension: 0.3,
          backgroundColor: "rgba(78, 115, 223, 0.05)",
          borderColor: "rgba(78, 115, 223, 1)",
          pointRadius: 3,
          pointBackgroundColor: "rgba(78, 115, 223, 1)",
          pointBorderColor: "rgba(78, 115, 223, 1)",
          pointHoverRadius: 3,
          pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
          pointHoverBorderColor: "rgba(78, 115, 223, 1)",
          pointHitRadius: 10,
          pointBorderWidth: 2,
          data: <?= $jumlah;?>,
        }],
      },
      options: {
        maintainAspectRatio: false,
        layout: {
          padding: {
            left: 10,
            right: 25,
            top: 25,
            bottom: 0
          }
        },
        scales: {
          xAxes: [{
            time: {
              unit: 'date'
            },
            gridLines: {
              display: false,
              drawBorder: false
            },
            ticks: {
              maxTicksLimit: 7
            }
          }],
          yAxes: [{
            ticks: {
              maxTicksLimit: 5,
              padding: 10,
              // Include a dollar sign in the ticks
              callback: function(value, index, values) {
                return '' + number_format(value);
              }
            },
            gridLines: {
              color: "rgb(234, 236, 244)",
              zeroLineColor: "rgb(234, 236, 244)",
              drawBorder: false,
              borderDash: [2],
              zeroLineBorderDash: [2]
            }
          }],
        },
        legend: {
          display: false
        },
        tooltips: {
          backgroundColor: "rgb(255,255,255)",
          bodyFontColor: "#858796",
          titleMarginBottom: 10,
          titleFontColor: '#6e707e',
          titleFontSize: 14,
          borderColor: '#dddfeb',
          borderWidth: 1,
          xPadding: 15,
          yPadding: 15,
          displayColors: false,
          intersect: false,
          mode: 'index',
          caretPadding: 10,
          callbacks: {
            label: function(tooltipItem, chart) {
              var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
              return datasetLabel + ': ' + number_format(tooltipItem.yLabel);
            }
          }
        }
      }
    });

  </script>

  <!-- Page level custom scripts -->
  <!--<script src="dist/js/demo/chart-area-demo.js"></script>-->
  <!--<script src="dist/js/demo/chart-pie-demo.js"></script>-->
  
  <!-- Page level plugins -->
  <script src="plugin/datatables/jquery.dataTables.min.js"></script>
  <script src="plugin/datatables/dataTables.bootstrap4.min.js"></script>
  <!--IONICON-->
  <script src="https://unpkg.com/ionicons@5.0.0/dist/ionicons.js"></script>


  <script>
    $(document).ready(function(){
        var dataTable=$('#data_user').DataTable({
            "processing": true,
            "serverSide":true,
            "ajax":{
                url:"module/user/load_data.php",
                type:"post"
            }
        });
    });
  </script>

  <script>
    $(document).ready(function(){
        var dataTable=$('#data_siswa').DataTable({
            "processing": true,
            "serverSide":true,
            "ajax":{
                url:"module/m_siswa/load_siswa.php",
                type:"post"
            }
        });
    });
  </script>

  <script>
  $(document).ready(function() {
    $('#table_1').DataTable(
      {
        "language":{
                    "url":"plugin/datatables/indonesian.json",
                    "sEmptyTable":"Tidak Ada Data"
                  }
      });
    });
  </script>

  <script>
  $(document).ready(function() {
    $('#tab_bank1').DataTable(
      {
        "language":{
                    "url":"plugin/datatables/indonesian.json",
                    "sEmptyTable":"Tidak"
                  }
      });
    });
  </script>

  <script>
  $(document).ready(function() {
    $('#table_2').DataTable(
      {
        "language":{
                    "url":"plugin/datatables/indonesian.json",
                    "sEmptyTable":"Tidak ada data"
                  },
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": false,
        "info": false,
        "autoWidth": false
      });
    });
  </script>

<!-- Select2 -->
<script src="plugin/select2/js/select2.full.min.js"></script>
<!-- date-range-picker -->
<!-- <script src="plugin/daterangepicker/daterangepicker.js"></script>-->
<script type="text/javascript">
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2()

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })

    
    })
</script>

 
<!-- KaTeX -->
<!--
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.11.1/dist/katex.min.js" integrity="sha384-y23I5Q6l+B6vatafAwxRu/0oK/79VlbSz7Q9aiSZUvyWYIYsd+qj+o24G5ZU2zJz" crossorigin="anonymous"></script>
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.11.1/dist/contrib/auto-render.min.js" integrity="sha384-kWPLUVMOks5AQFrykwIup5lo0m3iMkkHrD0uJ4H5cjeGihAutqP0yW0J6dpFiVkI" crossorigin="anonymous"
    onload="renderMathInElement(document.body);"></script>-->

<!-- Mathjax -->
<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

<!-- CK Finder -->
<script src="plugin/ckfinder/ckfinder.js"></script>
<!-- CK Editor -->
<script src="plugin/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="plugin/ckeditor/adapters/jquery.js"></script> 
  <!-- EDIT JAWAB PIL GANDA -->
  <script type="text/javascript">                               
    $(function(){                                            
      $('.editor').ckeditor({
        filebrowserBrowseUrl: 'plugin/ckfinder/ckfinder.html',
        filebrowserUploadUrl: 'plugin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
        filebrowserWindowWidth: '1000',
        filebrowserWindowHeight: '700',
        height: 100
      });

      $('.editor_soal').ckeditor({
        filebrowserBrowseUrl: 'plugin/ckfinder/ckfinder.html',
        filebrowserUploadUrl: 'plugin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
        filebrowserWindowWidth: '1000',
        filebrowserWindowHeight: '700',
        height: 300
      });

      $('.editor_pesan').ckeditor({
        filebrowserBrowseUrl: 'plugin/ckfinder/ckfinder.html',
        filebrowserUploadUrl: 'plugin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
        filebrowserWindowWidth: '1000',
        filebrowserWindowHeight: '700',
        height: 400
      });

      $('.editor_jawab').ckeditor({
        filebrowserBrowseUrl: 'plugin/ckfinder/ckfinder.html',
        filebrowserUploadUrl: 'plugin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
        filebrowserWindowWidth: '1000',
        filebrowserWindowHeight: '700',
        height: 300,
        width : 850
      });  

      $('.forum_text').ckeditor({
        filebrowserBrowseUrl: 'plugin/ckfinder/ckfinder.html',
        filebrowserUploadUrl: 'plugin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
        filebrowserWindowWidth: '800',
        filebrowserWindowHeight: '700',
        height: 300,
        width : 750
      });   

    });             
  </script>
  

<!-- JS UJIAN ONLINE -->
<script type="text/javascript" src="dist/js/jquery.countdownTimer.js"></script>
<script type="text/javascript" src="dist/js/kakyusuf.js"></script>
<script type="text/javascript" src="dist/js/ujian.js"></script>
<script type="text/javascript" src="dist/js/main.js"></script>
<script type="text/javascript" src="dist/js/sidein_menu.js"></script>

<script type="text/javascript">
function AlertIt() {
$("#awal").css("display", "block");
$("#ahir").css("display", "none");  
if($("#slideMenu").hasClass('closed')){
        $("#slideMenu").animate({right:0}, 200, function(){
          $(this).removeClass('closed').addClass('opened');
          document.getElementById("kakisoal").style.width = '74%';          
          $("a#toggleLink").removeClass('toggleBtn').addClass('toggleBtnHighlight');
        });
$("#awal").css("display", "block");
$("#ahir").css("display", "none");          
//e.preventDefault();
    //return false;
      }//if close
      if($("#slideMenu").hasClass('opened')){
      
      if ( $(window).width() > 739) {      
        $("#slideMenu").animate({right:-400}, 200, function(){// jika screen kecil gunakan right:-240, untuk lebar right:-400
          $(this).removeClass('opened').addClass('closed');
          document.getElementById("kakisoal").style.width = '97.7%';
          $("a#toggleLink").removeClass('toggleBtnHighlight').addClass('toggleBtn');
        });
      } else if ( $(window).width() > 409) {      
        $("#slideMenu").animate({right:-200}, 200, function(){// jika screen kecil gunakan right:-240, untuk lebar right:-400
          $(this).removeClass('opened').addClass('closed');
          document.getElementById("kakisoal").style.width = '97.7%';
          $("a#toggleLink").removeClass('toggleBtnHighlight').addClass('toggleBtn');
        });
      
      } else {
        $("#slideMenu").animate({right:-240}, 200, function(){// jika screen kecil gunakan right:-240, untuk lebar right:-400
          $(this).removeClass('opened').addClass('closed');
          document.getElementById("kakisoal").style.width = '30%';
          $("a#toggleLink").removeClass('toggleBtnHighlight').addClass('toggleBtn');
        });
      }

$("#awal").css("display", "none");
$("#ahir").css("display", "block");         
//e.preventDefault();
      }//if close
}
</script>


<!-- audio -->
<!--<script src="js/jquery.min.audio.js"></script>-->   
<!--<link rel="stylesheet" href="css/style3.css">-->


</body>
</html>
<?php }
?>