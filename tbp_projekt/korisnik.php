<?php
	include("radBP.php");
	include("header.php");
	otvoriBP();
	if ($aktivni_korisnik_tip != 3){
		header ("Location:index.php");
	}
	
	if(isset($_POST['kor_ime'])) {
		
		$id = $_POST['novi'];
		$korisnicko_ime=$_POST['kor_ime'];
		$ime=$_POST['ime'];
		$prezime=$_POST['prezime'];
		$email=$_POST['email'];
		$lozinka=$_POST['lozinka'];
		$tip_id=$_POST['tip'];
		
		if(empty($id) && !empty($korisnicko_ime)&& !empty($ime) && !empty($lozinka) ){
			$sql = "INSERT INTO korisnik 
			(tip_id, korisnicko_ime, lozinka, ime, prezime, email, slika)
			VALUES
			('$tip_id', '$korisnicko_ime', '$lozinka', '$ime', '$prezime', '$email', '$slika');
			";
			izvrsiBP($sql);
			header("Location:korisnici.php");
		}else if (!empty ($id)){
			$sql = "UPDATE korisnik SET 				 
				ime='$ime',
				prezime='$prezime',
				tip_id = '$tip_id',
				lozinka = '$lozinka',
				email='$email',
				slika='$slika'
				WHERE korisnik_id = '$id'
			";
				izvrsiBP($sql);
				header("Location: korisnici.php");
		}else{
			echo"Molim vas da unesete korisničko ime, lozinku i ime.";
		}
	} 
	
	
	if(isset($_GET['korisnik'])) {
		$id = $_GET['korisnik'];
		$sql = "SELECT kor_id, tip_id, kor_ime, lozinka, clanarina_do, ime, prezime, oib, email, adresa, mjesto_id FROM korisnik WHERE kor_id='$id'";
		$rs = izvrsiBP($sql);
		list($kor_id, $tip_id, $kor_ime, $lozinka, $clanarina_do, $ime, $prezime, $oib, $email, $adresa, $mjesto) = pg_fetch_array($rs);
		
		
	} else {
		$kor_id="";
		$tip_id=1;
		$kor_ime="";
		$lozinka="";
		$clanarina_do="";
		$ime="";
		$prezime="";
		$oib="";
		$email="";
		$adresa=""; 
		$mjesto="";
	}
?>


		<form method="post" action="korisnik.php">
	
			<input type="hidden" name="novi" value="<?php echo $kor_id?>"/>
			<table class="table">
				<tr>
					<td><label for="kor_ime">Korisničko ime:</label></td>
					<td><input type="text" name="kor_ime"
						<?php 
							if (!empty($kor_id)) {
								echo "readonly='readonly'";
							}	
						?>value="<?php echo $kor_ime?>"/></td>
				</tr>
				
				<tr>
					<td><label for="ime">Ime:</label></td>
					<td><input type="text" name="ime" value="<?php echo $ime?>"/></td>
				</tr>
				
				<tr>
					<td><label for="prezime">Prezime:</label></td>
					<td><input type="text" name="prezime" value="<?php echo $prezime?>"/></td>
				</tr>
				
				<tr>
					<td><label for="lozinka" >Lozinka:</label></td>
					<td><input type="text" name="lozinka" value="<?php echo $lozinka?>"/></td>
				</tr>
				
				<?php 
					if($aktivni_korisnik_tip == 3) {
						?>
							<tr>
								<td>Tip korisnika:</td>
								<td><select name="tip">
									<option value="3" <?php if ($tip_id == 3) echo "selected='selected'";?>>Administrator</option>
									<option value="2" <?php if ($tip_id == 2) echo "selected='selected'";?>>Moderator</option>
									<option value="1" <?php if ($tip_id == 1) echo "selected='selected'";?>>Korisnik</option>
								</select></td>
							</tr>
						<?php
					}
					?>
				<tr>
					<td><label for="oib">OIB:</label></td>
					<td><input type="number" name="oib" value="<?php echo $oib?>"/></td>
				</tr>
				
				<tr>
					<td><label for="email">E-mail:</label></td>
					<td><input type="text" name="email" value="<?php echo $email?>"/></td>
				</tr>
				
				<tr>
					<td><label for="clanarina_do">Članarina do:</label></td>
					<td><input type="date" name="clanarina_do" value="<?php echo $clanarina_do?>" /></td>
				</tr>
				
				<tr>
					<td><label for="email">Adresa:</label></td>
					<td><input type="text" name="adresa" value="<?php echo $adresa?>"/></td>
				</tr>
				<!-- drop box za odabir mjesta -->
				<tr>
					<td><label for="mjesto">Mjesto:</label></td>
					<td><input type="text" name="mjesto" value="<?php echo $mjesto?>"/></td>
				</tr>
				
				<tr>
					<td colspan="2"><input type="submit" value="Pošalji"/></td>
				</tr>
			</table>
	
		</form>	
<?php
	zatvoriBP();
	include("footer.php");

?>