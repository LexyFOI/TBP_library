<?php
	include("radBP.php");
	if(isset($_GET['logout'] )) {
		session_start();
		unset($_SESSION["aktivni_korisnik"]);
		unset($_SESSION["aktivni_korisnik_tip"]);
		unset($_SESSION["aktivni_korisnik_id"]);
		session_destroy();
		header("Location:index.php");
	}
	include("header.php");
	
	if (isset($_POST['korisnicko_ime'])) {
		
		otvoriBP();
		$kor_ime=pg_escape_string($_POST['korisnicko_ime']);
		$lozinka =pg_escape_string($_POST['lozinka']);
		if (!empty($kor_ime) && !empty($lozinka)) {
			
			$sql = "SELECT kor_id, tip_id, ime, prezime FROM korisnik WHERE kor_ime='$kor_ime' and lozinka = '$lozinka'";
			$rs = izvrsiBP($sql);
			if(pg_num_rows($rs) == 0) {
				echo "Ne postoji korisnik s navedenim korisnièkim imenom i lozinkom";
			} else {	
				session_start();			
				list($id, $tip, $ime, $prezime) = pg_fetch_array($rs);
				$_SESSION['aktivni_korisnik'] = $kor_ime;
				$_SESSION['aktivni_korisnik_ime'] = $ime . " " . $prezime;
				$_SESSION['aktivni_korisnik_id'] = $id;
				$_SESSION['aktivni_korisnik_tip'] = $tip;
				header("Location:katalog.php");
			}
			pg_close();
		} else {
			echo "Molim unesite korisnièko ime i lozinku";
		}
	}
	

?>

<form method="post" action="login.php">
	<table class="table">
		<tr>
			<td>Korisnièko ime</td>
			<td><input type="text" name="korisnicko_ime"/></td>
		</tr>
		<tr>
			<td>Lozinka</td>
			<td><input type="password" name="lozinka"/></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Prijavi se"/></td>
		</tr>
	</table>	
</form>


<?php	
include("footer.php");
?>