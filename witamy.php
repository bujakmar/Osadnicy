<?php
session_start();

if (!isset($_SESSION['udanarejestracja']))
{
	header('Location: index.php');
	exit();
}
else
{
unset($_SESSION['udanarejestracja']);
}
?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" conten="IE=edge,chrome=1" />
	<title>Osadnicy - Witamy</title>
</head>
<body>
Dziękujemy za rejestrację! Możesz się zalogować <br /><br />

<a href="index.php">Zaloguj sie na swoje konto</a>
<br /><br />

</body>
</html>