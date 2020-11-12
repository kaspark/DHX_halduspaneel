<?php
session_start();
include("conf.php");
include("func/base.php");

# AD Login
if (isset($_POST['user']) && isset($_POST['password']) && $AD_login === true) {
	$user = $_POST['user'];
	$password = $_POST['password'];

	$ad = ldap_connect("ldap://{$AD_host}.{$AD_domain}") or die('Mingi kala AD serveriga, proovi natukese aja pärast uuesti.');

	ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ad, LDAP_OPT_REFERRALS, 0);
	if (!ldap_start_tls($ad)) {
		$msg = "Could not start secure TLS connection";
	}

	@ldap_bind($ad, "{$user}@{$AD_domain}", $password) or die('Parool või kasutaja vale.');

	$userdn = getDN($ad, $user, $AD_basedn);

	if (checkGroupEx($ad, $userdn, getDN($ad, $AD_group, $AD_basedn))) {
		$_SESSION['auth']=1;
		$_SESSION['user']=getCN($userdn);
		header('Location: index.php');
		
	} else {
		$msg = 'Sul puudub siia ligipääs';
	}

	ldap_unbind($ad);
} 
# Local login
elseif (isset($_POST['user']) && isset($_POST['password']) && $AD_login === false) {
	$user = $_POST['user'];
	$password = $_POST['password'];
	if (($user == $LL_user) && ($password == $LL_pass)) {
		$_SESSION['auth']=1;
		$_SESSION['user']=$user;
		header('Location: index.php');
		
	} else {
		$msg = 'Sul puudub siia ligipääs';
	}



} elseif (isset($_GET['logout']) && $_GET['logout']==1)
{
	session_destroy();
	header('Location: login.php');	
}
?>


<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<title>DHX halduspaneel</title>
	<link href="css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
	<style>
		.bd-placeholder-img {
			font-size: 1.125rem;
			text-anchor: middle;
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
		}

		@media (min-width: 768px) {
			.bd-placeholder-img-lg {
				font-size: 3.5rem;
			}
		}
	</style>
	<!-- Custom styles for this template -->
	<link href="css/signin.css" rel="stylesheet">
</head>

<body class="text-center">
	<form class="form-signin" method="post" action="login.php">
		<h1 class="h3 mb-3 font-weight-normal">Logi sisse</h1>
		<?php
		if (!empty($msg)) {
			echo "<b>$msg</b>";
		}
		?>
		<label for="user" class="sr-only">Kasutajanimi</label>
		<input type="user" name="user" id="user" class="form-control" placeholder="Kasutajanimi" required autofocus>
		<label for="inputPassword" class="sr-only">Parool</label>
		<input type="password" name="password" id="inputPassword" class="form-control" placeholder="Parool" required>
		<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
		<p class="mt-5 mb-3 text-muted">&copy; 2020</p>
	</form>

	<!-- Placed at the end of the document so the pages load faster -->
	<script src="js/jquery-3.4.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</body>

</html>