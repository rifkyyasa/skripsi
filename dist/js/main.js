$(function(){
   $('#isi').load('home.php');	
});

function show_detail(ujian){
   $('#isi').load('detail.php?ujian='+ujian);	
}
function show_nilai(ujian){
   $('#isi').load('nilai.php?ujian='+ujian);	
}

function show_petunjuk(ujian){
   $('#isi').load('petunjuk.php?ujian='+ujian);		
}

function show_ujian(ujian){
   $('#isi').load('ujian.php?ujian='+ujian);	
   return false;
}


