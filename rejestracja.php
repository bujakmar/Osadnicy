<?php
session_start();

if (isset($_POST['email']))
{
	//Udana walidacja? Zaużmy, że TAK!
	$wszystko_OK=true;
	
	//Sprawdź poprawość nicka
	$nick = $_POST['nick'];
	
	//Sprawdzenie długości nicka
	if ((strlen($nick)<3) || (strlen($nick)>20))
	{
	$wszystko_OK=false;
	$_SESSION['e_nick']="Nick musi posiadać 3 do 20 znaków!";
	}
	
	if (ctype_alnum($nick)==false)
	{
	$wszystko_OK=false;
	$_SESSION['e_nick']="Nick musi składać sie tylko z liter i cyfr (bez polskich znaków)";
	}
	
	//Sprawdz poprawność e-mail
	$email= $_POST['email'];
	$emailB= filter_var($email, FILTER_SANITIZE_EMAIL);
	
	if ((filter_var($emailB, FILTER_VALIDATE_EMAIL)==false) || ($emailB!=$email))
	{
		$wszystko_OK=false;
		$_SESSION['e_email']="Podaj poprawny adres email";
	}
	
	//sprawdz poprawność hasła
	$haslo1=$_POST['haslo1'];
	$haslo2=$_POST['haslo2'];
	
	if ((strlen($haslo1)<8) || (strlen($haslo1)>20))
	{
		$wszystko_OK=false;
		$_SESSION['e_haslo']="Hasło musi posiadać od 8 do 20 znaków!!!";
	}
	
	if ($haslo1!=$haslo2)
	{
		$wszystko_OK=false;
		$_SESSION['e_haslo']="Podane hasła nie są identyczne!";
	}
	
	$haslo_hash = password_hash($haslo1, PASSWORD_DEFAULT);
	// echo $haslo_hash; exit(); //tu było sprawdzenie jak wygląda zahashowane hasło do uzupełnienie w mysql
	
	// czy zakceptowano regulamin
	
	if (!isset ($_POST['regulamin']))
	{
		$wszystko_OK=false;
		$_SESSION['e_regulamin']="Zaakceptuj regulamin!";
	}
	
	// Sprawdzanie czy nie bot
/*	$sekret = "6LdoP0AUAAAAAPNbHT2copcCSJ5Zc1lqUWJLeHNb";
	
	$sprawdz = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$sekret.'&response='.$_POST['g-recaptcha-response']);
	$odpowiedz = json_decode($sprawdz);	
	
	
	if ($odpowiedz->success==false)
	{
		$wszystko_OK=false;
		$_SESSION['e_bot']="Potwierdz ze nie jestes botem";
	}
	
	*/
	require_once "connect.php";
	mysqli_report(MYSQLI_REPORT_STRICT);
	try
	{
	$polaczenie = new mysqli($host,$db_user,$db_password,$db_name);
	if ($polaczenie->connect_errno!=0)
{
	throw new Exception(mysqli_Connect_error());
}
	else
	{
	//czy email juz istnieje?
	$rezultat = $polaczenie->query("SELECT id FROM uzytkownicy WHERE email='$email'");
	
	if(!$rezultat) throw new Exception($polaczenie->error);
	
	$ile_takich_maili = $rezultat->num_rows;
	if($ile_takich_maili>0)
	{
		$wszystko_OK=false;
		$_SESSION['e_email']="Email juz isteniej";
	}
	
	//czy nick juz istnieje?
	$rezultat = $polaczenie->query("SELECT id FROM uzytkownicy WHERE user='$nick'");
	
	if(!$rezultat) throw new Exception($polaczenie->error);
	
	$ile_takich_nickow = $rezultat->num_rows;
	if($ile_takich_nickow>0)
	{
		$wszystko_OK=false;
		$_SESSION['e_nick']="Nick już zajęty";
	}
	
	if ($wszystko_OK==true)
	
	{
		//HURA wszystkie test zaliczone, dodajemy gracza do bazy

		if ($polaczenie->query("INSERT INTO uzytkownicy VALUES (NULL, '$nick', '$haslo_hash', '$email', 100, 100, 100, now() + INTERVAL 14 DAY)"))
		{
			$_SESSION['udanarejestracja']=true;
			header('Location: witamy.php');
		}
		else
		{
		throw new Exception($polaczenie->error);
		}
	}
	
	
		$polaczenie->close();
	}
	}
	catch(Exception $e)
	{
	echo '<span style="color:red;">Błąd serwera! Przepraszamy spróbuj innym razem!</span>';
	echo '<br />Informacja developerska: '.$e;
	}
	
}

?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" conten="IE=edge,chrome=1" />
	<title>Osadnicy - załóż darmowe konto</title>
	<script src='https://www.google.com/recaptcha/api.js'></script>
	
	<style>
		.error
		{
		color:red;
		margin-top: 10px;
		margin-bottom: 10px;
		}
	</style>
</head>
<body>
<form method="post">
	Nickname: <br /> <input type="text" name="nick" /><br />
	<?php
	if (isset($_SESSION['e_nick']))
	{
	echo '<div class="error">'.$_SESSION['e_nick'].'</div>';
	unset($_SESSION['e_nick']);
	}
	?>
	
	E-mail: <br /> <input type="text" name="email" /><br />
	<?php
	if (isset($_SESSION['e_email']))
	{
	echo '<div class="error">'.$_SESSION['e_email'].'</div>';
	unset($_SESSION['e_email']);
	}
	?>
	
	Twoje Hasło: <br /> <input type="password" name="haslo1" /><br />
	<?php
	if (isset($_SESSION['e_haslo']))
	{
	echo '<div class="error">'.$_SESSION['e_haslo'].'</div>';
	unset($_SESSION['e_haslo']);
	}
	?>
	
	Powtórz Hasło: <br /> <input type="password" name="haslo2" /><br />

	<label>
	<input type="checkbox" name="regulamin" /> Akceptuję regulamin
	</label>
	
	<?php
	if (isset($_SESSION['e_regulamin']))
	{
	echo '<div class="error">'.$_SESSION['e_regulamin'].'</div>';
	unset($_SESSION['e_regulamin']);
	}
	?>

<!--	<div class="g-recaptcha" data-sitekey="6LdoP0AUAAAAALUc4PVjiD5_1l1up5s69h3DbtYP"></div>
	
-->	
	<br />
	<input type="submit" value="zarejestruj się" />
	
</form>

</body>
</html>