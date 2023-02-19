<?php
	include("radBP.php");
	include("header.php");
	
	
	otvoriBP();
	
	// odabrano rezerviranje knjige
	if(isset($_GET['rezervaacija'])){
		
		//code
		
		
		
		
		
		
	}
	
	
	
	
			
		$sql_knjige = "SELECT knjiga_id, broj_primjeraka, naslov, pisac, sazetak,broj_rezervacija
				FROM knjige k";
		$rs_knjige = izvrsiBP($sql_knjige);
		
		if(pg_num_rows($rs_knjige) == 0) {
			echo "Nema dohvaæenih podataka iz kataloga.";
		}
		
	echo "<table cellspacing='3' cellpadding='1' class='table'>
		<tr>
			<th>Knjigs_id</th>
			<th>Naslov</th>
			<th>Autor</th>
			<th>Sažetak</th>
			<th>Broj primjeraka</th>";
		//rezervacija knjige
		if($aktivni_korisnik_tip > 0){ 
			echo"<th>Slobodne knjige</th>
				<th></th>";
		}	
		// ureðivanje podataka o knjizi
		if($aktivni_korisnik_tip == 3){ 
			echo"<th></th>";
		}
	echo"</tr>";
	
	while(list($knjiga_id, $br_prim, $naslov, $pisac, $sazetak) = pg_fetch_array($rs_knjige));{
		echo "<tr>
				<td>".$knjiga_id."</td>
				<td>".$br_prim."</td>
				<td>".$naslov."</td>
				<td>".$pisac."</td>
				<td>".$sazetak."</td>
			";
	
		$sql_posudeno = "SELECT COUNT(status_id) FROM primjerak WHERE status_id = 1 AND knjiga_id = ".$knjiga_id;
		$rs_posudeno = izvrsiBP($sql_posudeno);
		$sql_rezervirano = "SELECT broj_rezervacija FROM knjige WHERE knjiga_id = ".$knjiga_id;
		$rs_rezervirano = izvrsiBP($sql_rezervirano);
		
		if($aktivni_korisnik_tip > 0){ 
			echo"<td>".$rs_posudeno."</td>
				<td>".$rs_rezervirano."</td>
				<td>
					<a type= 'button' class='button' href='katalog.php?rezervacija=$knjiga_id'>REZERVIRAJ</a>
				</td>";
		}		
			//ureðivanje knjige
			
		if($aktivni_korisnik_tip == 3){ 
			echo"<td>
					<a type= 'button' class='button' href='katalog_uredi.php?knjiga=$knjiga_id'>UREDI</a> 
				</td>
			</tr>";
		}
	} 
	echo"</table>";

//pg_query($connection, "select function_name()");
//primjer pokretanja funkcije u bazi 
	
	
	include("footer.php");
?>