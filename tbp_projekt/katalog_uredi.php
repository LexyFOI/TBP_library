<?php
	include("radBP.php");
	include("header.php");
/*	otvoriBP();
	if ($aktivni_korisnik_tip != 3){
		header ("Location:index.php");
	}
*/	
	if(isset($_POST['novi'])) {
		
		$k_knjiga_id=$_POST['novi'];
		$k_broj_primjeraka=$_POST[''];
		$k_naslov=$_POST['naslov'];
		$k_pisac=$_POST['pisac'];
		$k_sazetak=$_POST['sazetak'];
		$p_primjerak_id=$_POST[''];
		$p_knjiga_id=$_POST[''];
		$p_status_id=$_POST['status'];
		$p_izdavac=$_POST['izdavac'];
		$p_god_izdanja=$_POST['god_izdanja'];
		$p_broj_rezervacija=$_POST['br_rez'];
		
		if(empty($k_knjiga_id) && !empty($k_naslov)&& !empty($k_pisac) && !empty($k_sazetak) ){
			$sql = "INSERT INTO knjige 
			(broj_primjeraka, naslov, pisac. sazetak)
			VALUES
			('$k_broj_primjeraka', '$k_naslov', '$k_pisac', '$k_sazetak');
			";
			izvrsiBP($sql);
			header("Location:katalog_uredi.php");
		}else if (!empty ($k_knjiga_id)){
			$sql = "UPDATE knjige SET 				 
				broj_primjeraka='$k_broj_primjeraka',
				naslov = '$k_naslov',
				pisac = '$k_pisac',
				sazetak='$k_sazetak',
				WHERE knjiga_id = '$k_knjiga_id'
			";
				izvrsiBP($sql);
				header("Location: katalog_uredi.php");
		}else{
			echo"Molim vas da unesete sve podatke.";
		}
	} 	
	
	if(isset($_GET['knjiga'])) {
		$id = $_GET['knjiga'];
		$sql = "SELECT k.knjiga_id, k.broj_primjeraka, k.naslov, k.pisac, k.sazetak, p.primjerak_id, p.knjiga_id, p.status_id, p.izdavac, p.god_izdanja, p.broj_rezervacija 
				FROM knjiga k, primjerak p 
				WHERE k.knjiga_id='$id' AND k.knjig_id = p.knjiga_id";
		$rs = izvrsiBP($sql);
		list($k_knjiga_id, $k_broj_primjeraka, $k_naslov, $k_pisac, $k_sazetak, $p_primjerak_id, $p_knjiga_id, $p_status_id, $p_izdavac, $p_god_izdanja, $p_broj_rezervacija ) = pg_fetch_array($rs);
		
		
	} else {
		$k_knjiga_id="";  d
		$k_broj_primjeraka=0; d
		$k_naslov=""; d
		$k_pisac=""; d
		$k_sazetak=""; d
		$p_primjerak_id=""; //???d
		$p_knjiga_id="";
		$p_status_id=0;d
		$p_izdavac="";d
		$p_god_izdanja="";d
		$p_broj_rezervacija=0;
	}
?>

	<form method="post" action="katalog_uredi.php">
		<div>
			<input type="hidden" name="novi" value="<?php echo $knjiga_id?>"/>
			<table class="table">
				<tr>
					<td><label for="naslov">Naslov:</label></td>
					<td><input type="text" name="naslov"
						<?php 
							if (!empty($k_naslov)) {
								echo "readonly='readonly'";
							}	
						?>value="<?php echo $k_naslov?>"/></td>
				</tr>
				<tr>
					<td><label for="pisac">Autor:</label></td>
					<td><input type="text" name="pisac"
						<?php 
							if (!empty($k_pisac)) {
								echo "readonly='readonly'";
							}	
						?>value="<?php echo $k_pisac?>"/></td>
				</tr>
				<tr>
					<td><label for="sazetak">Sažetak:</label></td>
					<td><input type="text" name="sazetak" value="<?php echo $k_sazetak?>"/></td>
				</tr>
				
				
				<tr>
					<td><label for="kor_ime">Broj primjeraka:</label></td>
					<td><input type="number" name="broj_primjeraka"
						<?php 
							if (!empty($k_broj_primjeraka)) {
								echo "readonly='readonly'";
							}	
						?>value="<?php echo $k_broj_primjeraka?>"/></td>
				</tr>
			</table>
		</div>
		
		
		<div>

		<?php
			$sql_primjerci = "SELECT primjerak_id, knjiga_id, status_id, izdavac, god_izdanja
				FROM primjerak
				WHERE knjiga_id='$id'";
			$rs_primjerci = izvrsiBP($sql_primjerci);
			while(list($p_primjerak_id, $p_knjiga_id, $p_status_id, $p_izdavac, $p_god_izdanja) = pg_fetch_array($rs_primjerci)){
			
			
			
			
			}
		?>
		
		
		
		
			<input type="hidden" name="novi2" value="<?php echo $p_primjerak_id?>"/>
			<table>	
				<tr>
					<td><label for="status">Status:</label></td>
					<td>
						<select name="status">
							<option value="0" <?php if ($status_id == 0) echo "selected='selected'";?>>Slobodna</option>
							<option value="1" <?php if ($status_id == 1) echo "selected='selected'";?>>Posuðena</option>
							<option value="2" <?php if ($status_id == 2) echo "selected='selected'";?>>Rezervirana</option>
						</select>
					</td>
				</tr>
						
				<tr>
					<td><label for="izdavac" >Izdavaè:</label></td>
					<td><input type="text" name="izdavac" value="<?php echo $p_izdavac?>"/></td>
				</tr>
						
				<tr>		
					<td><label for="godina">Godina izdavanja:</label></td>
					<td><input type="number" name="godina" value="<?php echo $p_godina?>"/></td>
				</tr>
				
				<tr>
					<td colspan="2"><input type="submit" value="Spremi primjerak"/></td>
				</tr>
			</table>
		</div>
				<tr>
					<td colspan="2"><input type="submit" value="Spremi i dodaj primjerak"/></td>
				</tr>
			
		
	</form>	

<?php
	zatvoriBP();
	include("footer.php");

?>