<style type="text/css">
/*Menyembunyikan semua blok soal*/
.blok-soal{
   display: block;
}
/*Menyembunyikan soal yang memiliki class active*/
.blok-soal.active{
	display: block;
}
/*Mengatur nomor soal dan huruf pada pilihan agar berbentuk lingkaran*/
.huruf{
   display: block;
   width: 35px;
   padding: 5px 0;
   text-align: center;
   border: 1px solid #ccc;
   border-radius: 50%;
   cursor: pointer;
}
/*Menyembunyikan input radio*/
input[type=radio]{
   display: none;
}
/*Mengganti warna background huruf ketika input radio dicentang*/
input[type=radio]:checked ~ .huruf{
   background: #336799;
   color: #fff;
}
.kotaksoal{
	width:97%;
	padding:20px;
	border:solid;
	top:30px;
	border-color:#CCC;
	height:100%;
}
.flex-next {
    background-color: #336898;
    width: 20px;
    height: 20px;
    margin: 10px;
    line-height: 20px;
    color: white;
    font-size: 18px;
    text-align: center;
	padding-left:12px;
	padding-right:12px;	
	padding-top:10px;
	padding-bottom:10px;

}
.flex-ragu {
    background-color:#FC0;
    width: 20px;
    height: 20px;
    margin: 10px;
    line-height: 20px;
    color: white;
    font-size: 18px;
    text-align: center;
	padding-left:12px;
	padding-right:12px;	
	padding-top:10px;
	padding-bottom:10px;
	text-decoration:none;
}
.flex-prev {
    background-color: #999;
    width: 25px;
    height: 25px;
    margin: 10px;
    line-height: 20px;
    color: white;
    font-size: 18px;
    text-align: center;
	padding-left:12px;
	padding-right:12px;	
	padding-top:10px;
	padding-bottom:10px;
}
.flex-container {
    height: 100%;
    padding: 0;
    margin: 0;
    display: -webkit-box;
    display: -moz-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
}

.flex-item {
    background-color: #336898;
	 width: 120px;
    height: 40px;
    margin-right: 0px;
	margin-top:-10px;
    line-height: 20px;
    color: white;
    font-size: 15px;
	font-weight:bold;
    text-align: center;
	padding-left:12px;
	padding-right:12px;	
	padding-top:7px;
	padding-bottom:6px;
}	
.flex-abu {
    background-color: #999;
    width: 120px;
    height: 40px;
    margin-right: 0px;
	margin-top:-10px;
    line-height: 20px;
    color: white;
    font-size: 15px;
    text-align: center;
	padding-left:12px;
	padding-right:12px;	
	padding-top:10px;
	padding-bottom:10px;
	float:right;
}	
.flex-biru {
    background-color: #000;
    width: 120px;
    height: 40px;
    margin-right: 0px;
	margin-top:-10px;
    line-height: 20px;
    color: white;
    font-size: 15px;
    text-align: center;
	padding-left:5px;
	padding-right:5px;	
	padding-top:10px;
	padding-bottom:10px;
	float:right;
}	
.flex-putih {
    background-color: #fff;
    width: 120px;
    height: 40px;
    margin-right: 0px;
	margin-top:-10px;
    line-height: 20px;
    color: black;
    font-size: 15px;
	font-weight:bold;
    text-align: center;
	padding-left:12px;
	padding-right:12px;	
	padding-top:10px;
	padding-bottom:10px;
	float:left;
}	
  .no-close .ui-dialog-titlebar-close {
        display: none;
    }

.fontlembarsoal{ padding-top:50px;}

    .left1{width: auto;    height: 80px;}
    .right1 {
        float: none;
        width: auto;
		margin-top:0px;
		height:60px;
		color:#FFFFFF;
    }
	.flex-putih,.flex-abu{ display:none}
	.flex-item{ margin-left:20px;}

</style>
<style>
#fontlembarsoal{
	margin-top:3px;
	margin-left:15px;
	margin-bottom:0px;
	margin-right:15px;
	background-color:#f0efef;
	font-size:12px;
	font-weight:bold;
	height:45px;
	left:40px;
	padding-top:10px;	
	padding-bottom:3px;	
	}

#tulisansoal{	
	background-color:#fff;
	height:90px;
	font-size:18px;
	font-weight:bold;
	vertical-align:middle;
	top:495px;
}
.tulisansoal{	
	background-color:#fff;
	height:90px;
	font-size:18px;
	font-weight:bold;
	vertical-align:middle;
	top:495px;
}
.nomersoal{	
	top:25px; width:100px;
	background-color:#336898;
	color:#fff;
	height:90px;
	font-size:18px;
	font-weight:bold;
	vertical-align:middle;	
	}	

#lembarsoal{
	margin-top:-8px;
	margin-left:15px;
	margin-bottom:2px;
	margin-right:15px;
	background-color:#fff;
	height:150%;
	    border-radius: 30px;
	border-style:solid;
	border-color:#999;
	}	
	
#hurufsoal{
    padding-left: 30px;
	padding-top:2px;
	padding-bottom:2px;
}

#tampilkan {
    background-color: #336898;
    width: 150px;
    height: 50px;
    margin-right: 20px;
	margin-top:-10px;
    line-height: 20px;
    color: white;
    font-size: 22px;
    text-align: center;
	padding-left:12px;
	padding-right:12px;	
	padding-top:14px;
	padding-bottom:14px;
	float:right;
}	
p{
	padding:20px;
	font-size: 16px;
	}
li{
	list-style:none;
	font-size:18px;
	}

	#lembaran{
	padding:20px;
	margin-left:12px;
	margin-right:12px;
	top:-30px;
	font-size: 12pt;
	background-color:#fff;
	border:solid;
	border-color:#ccc;
	}	
	#lembaransoal{
	padding:20px;
	font-size: 12pt;
	border:solid;
	border-color:#ccc;
	}	
.soal	{
	font-size: 16pt;
	}
.jawaban	{
	padding-bottom:10px;
	font-size: 10pt;
	border:solid;
	border-color:#CCC;
	}	
.pilihanjawaban	{
	font-size: 16pt;
	padding-bottom:15px;
	}	

.noti-jawab {
    position:absolute;
    background-color:white;
    color:#999;
    padding:4px;
    -webkit-border-radius: 30px;
    -moz-border-radius: 30px;
    border-radius: 30px;
	border-style:solid;
	border-color:#999;
    width:30px;
    height:30px;
    text-align:center;
}

	
    </style>
    
<style>
.jawaban	{
	padding-bottom:10px;
	font-size: 10pt;
	border:solid;
	border-color:#CCC;
	}	
.noti-jawab {
    position:absolute;
    background-color:white;
    color:#999;
    padding:4px;
    -webkit-border-radius: 30px;
    -moz-border-radius: 30px;
    border-radius: 30px;
	border-style:solid;
	border-color:#999;
    width:27px;
    height:27px;
    text-align:center;
}

.flatRoundedCheckbox
{
    width: 120px;
    height: 40px;
    margin: 20px 50px;
    position: relative;
}
.flatRoundedCheckbox div
{
    width: 100%;
    height:100%;
    background: #d3d3d3;
    border-radius: 50px;
    position: relative;
    top:-30px;
}  		

.piljwb{
	margin-left:0;    
	border-radius: 30px;
	border-style:solid;
	border-color:#999;
	list-style:none;}

</style>
<style>
#awal{
	color:#FFF;
	font-family:Arial, Helvetica, sans-serif;
	line-height: 90%;
	margin:0px auto;
	margin-top:20px;
}
#ahir{
	color:#FFF;
	font-family:Arial, Helvetica, sans-serif;
	line-height: 120%;
	margin:0px auto;
	margin-top:10px;
}
#noti-count {
    position:absolute;
    top:-12px;
    right:-15px;
    background-color:white;
    color:#313132;
    padding:5px;
    -webkit-border-radius: 30px;
    -moz-border-radius: 30px;
    border-radius: 30px;
	border-style:solid;
	border-color:#313132;
    width:30px;
    height:30px;
    text-align:center;
}
#noti-count div {
    margin-top:-5px;
}
</style>

<style>
        #container
        {
			height:300px;
        }
        
        .item
        {
            width: 50px;
            height: 50px;
			border:#313132;
			color:#fff;
			border-style:solid;
            margin-bottom: 17px;
			font-size:22px;
			line-height:normal;
			position: absolute; 
			left: 72px; 
			top: 0px;
			background-color: rgb(49, 49, 50);
			color: rgb(255, 255, 255); 
			border-color: rgb(49, 49, 50); 
        }
/*Mengatur warna tombol nomor soal*/
.ijo{
background-color: rgb(0, 128, 0);
border-color: rgb(0, 128, 0);
}
.yellow{
	background-color: rgb(234, 202, 8); 
	border-color: rgb(234, 202, 8);
}
.biru{
background-color: rgb(51, 104, 152); 
border-color: rgb(51, 104, 152);

}

/* === FIX RADIO BUTTON === */
/* Mengatur input radio agar sejajar dengan teks */
input[type=radio] {
    display: inline-block; /* tampil normal */
    margin-right: 8px; /* jarak ke teks */
    vertical-align: middle; /* sejajar dengan teks */
}

/* === FIX TIMER STYLE === */
#h_timer {
    font-weight: bold;
    font-size: 16px;
    padding: 5px 12px;
    background-color: #dc3545; /* merah Bootstrap */
    color: #fff;
    border-radius: 6px;
    display: inline-block;
    min-width: 90px;
    text-align: center;
}

/* === STYLE PILIHAN JAWABAN (opsional rapi) === */
.label-jawaban {
    display: flex;
    align-items: center;
    margin-bottom: 6px;
    font-size: 15px;
    cursor: pointer;
}

.label-jawaban:hover {
    background-color: #f5f5f5;
    border-radius: 4px;
}


    </style>