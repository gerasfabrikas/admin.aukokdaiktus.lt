<?php
$usermeta = getCurrentUser();
include('cities.php');

global $login;

if(isset($_POST['login']) and $login->isUserLoggedIn() and CUSER > 0) :
	updateField('users', 'user_lastlogin', date('Y-m-d H:i:s'), 'user_id', CUSER);
	updateField('users', 'user_lastlogin_ip', $_SERVER['REMOTE_ADDR'], 'user_id', CUSER);
endif;

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>pagalbadarbais.lt</title>
		<link rel="stylesheet" type="text/css" href="normalize.css" />
		<link rel="stylesheet" type="text/css" href="style.css" />
		<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/i18n/jquery-ui-i18n.min.js"></script>
		<script src="script.js"></script>
    </head>
    <body>

	<div class="menu">
		<div class="auth">
		<?php if (isset($login) and $login->isUserLoggedIn() == true) : ?>
			<div class="gravatar">
				<?php echo (strlen($usermeta['user_thumb']) > 16 ? '<img src="'.$usermeta['user_thumb'].'" onError="this.style.visibility=\'hidden\'" />' : ''); ?>
			</div><div class="gname">
				<div class="name"><?php echo $usermeta['user_fname'].' '.$usermeta['user_lname']; ?></div>
				<a href="?p=edit"><i class="fa fa-cog"></i> Paskyros nustatymai</a><br>
				<a href="index.php?logout"><i class="fa fa-sign-out"></i> Atsijungti</a>
			</div>
		<?php else : ?>
			<form method="post" action="index.php" name="loginform">
				<div class="inpwrap inline">
					<small>El. paštas arba paskyros vardas</small><br>
					<input id="user_name" type="text" name="user_name" required />
				</div><div class="inpwrap inline">
					<small>Slaptažodis</small><br>
					<input id="user_password" type="password" name="user_password" autocomplete="off" required />
				</div>
				<input type="hidden" id="user_rememberme" name="user_rememberme" value="1" />
				<input type="submit" name="login" value="Prisijungti" />
			</form>
		<?php endif; ?>
		</div>
		
		<?php if (isset($login) and $login->isUserLoggedIn() == true) : ?>
		<?php if(isAdmin() or isManager() or isGridManager()) : ?>
		<form class="srchform" action="" method="GET">
			Paieška <input type="text" value="<?php if(isset($_GET['srch'])) echo $_GET['srch']; ?>" name="srch" />
			<input type="hidden" value="search" name="p" />
		</form>
		<?php endif; ?>
		<ul class="menulist">
			<?php theMenu(); ?>
		</ul>
		<?php endif; ?>
	</div>
	
	<div class="main">
