<?php
	
	if (session_id() == "") 
		session_start(); 

	$aktivni_korisnik=0;
	$aktivni_korisnik_tip=-1;
	if(isset($_SESSION['aktivni_korisnik'])) { 
		$aktivni_korisnik=$_SESSION['aktivni_korisnik']; 
		$aktivni_korisnik_ime=$_SESSION['aktivni_korisnik_ime'];
		$aktivni_korisnik_tip=$_SESSION['aktivni_korisnik_tip'];
		$aktivni_korisnik_id =$_SESSION['aktivni_korisnik_id'];
	}
	
?>


<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" type="text/css" href="knjiznica.css"/>
	<title>upravljanje_korisnickim_racunima</title>
</head>
<body>
<div class ="content"> 
		<div class="header">
		
		<span>
			<?php
				if ($aktivni_korisnik===0) {
					echo "Neprijavljeni korisnik <br/>";
					echo "<a class='link' href='login.php'>Prijava</a>";
				} else {
					echo "Dobrodo�li, $aktivni_korisnik_ime <br/>";
					echo "<a class='link' href='login.php?logout=1'>Odjava</a>";
				}
			?>
			<h1 align="center">Online knji�nica</h1>
			<p align="center">Pregled kataloga knji�nice i rezervacija knjiga</p>
			<br/>
		</span>
		
		</div>
	<div class="menu">
	<a href="index.php">Po�etna</a>
	<a href="katalog.php">Katalog </a> <!--neprijavljeni korisnik po�etna-->
 
 <!-- 0. neprijavljenji, 1.�lan, 2. moderator, 3. admin-->
 <?php
		//if ($aktivni_korisnik_tip >=0 ) {
			echo "<a href='rezervacije.php'> Moje rezervacije</a>"; //prijavljeni korisnik po�etna//
			echo "<a href='posudba.php'>Posu�ene knjige</a>";
			//echo "<a href='zahtjev.php'>Novi zahtjev</a>";
		//}
		//if ($aktivni_korisnik_tip == 2){
			echo"<a href='rezervacija_o.php'>Zahtjevi za obradu</a>";
		//}
		//if ($aktivni_korisnik_tip == 3) {
			echo "<a href='korisnici.php'>  Korisnici</a>";
			echo "<a href='knjige.php'> Dopuna kataloga </a>";
		//}
	?>
	</div>

