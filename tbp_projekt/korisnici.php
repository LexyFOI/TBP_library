<?php
	include("radBP.php");
	include("header.php");
	
	
/*	otvoriBP();
	if ($aktivni_korisnik_tip != 3){
		header ("Location:index.php");
	}
*/			
		$sql = "SELECT kor_id, tip_id, kor_ime, lozinka, clanarina_do, ime, prezime, oib, email, adresa, k.mjesto_id, m.mjesto_id, m.naziv FROM korisnik k, mjesto m WHERE k.mjesto_id = m.mjesto_id";
		$rs = izvrsiBP($sql);
		if(pg_num_rows($rs) == 0) {
			echo "Nema postojeæih korisnika.";
		}
		
	echo "<table cellspacing='3' cellpadding='1' class='table'>
		<tr>
			<th>Korisnik_id</th>
			<th>Tip korisnika</th>
			<th>Korisnièko ime</th>
			<th>Lozinka</th>
			<th>Ælanarina vrijedi do</th>
			<th>Ime</th>
			<th>Prezime</th>
			<th>OIB</th>
			<th>E-mail</th>
			<th>Adresa</th>
			<th>Mjesto</th>";
		if($aktivni_korisnik_tip == 3){
			echo"<th></th>";
		}
	echo"</tr>";
	
	while(list($kor_id, $tip_id, $kor_ime, $lozinka, $clanarina_do, $ime, $prezime, $oib, $email, $adresa, $k_mjesto_id, $m_mjesto_id, $m_naziv) = pg_fetch_array($rs));{
		echo "<tr>
			<td>".$kor_id."</td>
			<td>".$tip_id."</td>
			<td>".$lozinka."</td>
			<td>".$clanarina_do."</td>
			<td>".$ime."</td>
			<td>".$prezime."</td>
			<td>".$oib."</td>
			<td>".$email."</td>
			<td>".$adresa."</td>
			<td>".$m_naziv."</td>
			<td><a class='link' href='korisnik.php?korisnik=$kor_id'>UREDI</a></td>
		</tr>";
	} 
	echo"</table>";

//pg_query($connection, "select function_name()");
//primjer pokretanja funkcije u bazi 
	
	
	include("footer.php");
?>