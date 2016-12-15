<?php
// include html header and display php-login message/error
$regmenu = 'reset';

include('header.php');

// show negative messages
if ($login->errors) {
    foreach ($login->errors as $error) {
        err($error, 'red');
    }
}

// show positive messages
if ($login->messages) {
    foreach ($login->messages as $message) {
        echo err($message);
    }
}

// the user just came to our page by the URL provided in the password-reset-mail
// and all data is valid, so we show the type-your-new-password form
if ($login->passwordResetLinkIsValid() == true) {
?>             
<form method="post" action="password_reset.php" name="new_password_form">
	<input type='hidden' name='user_name' value='<?php echo $_GET['user_name']; ?>' />
	<input type='hidden' name='user_password_reset_hash' value='<?php echo $_GET['verification_code']; ?>' />

	<label for="user_password_new">Naujas slaptažodis</label>
	<input id="user_password_new" type="password" name="user_password_new" pattern=".{6,}" required="required" autocomplete="off" />
	<br>
	<label for="user_password_repeat">Pakartokite naują slaptažodį</label>
	<input id="user_password_repeat" type="password" name="user_password_repeat" pattern=".{6,}" required="required" autocomplete="off" />
	<br><br>
	<input type="submit" name="submit_new_password" value="Nustatyti naują slaptažodį" />
</form>
<?php
// no data from a password-reset-mail has been provided, so we simply show the request-a-password-reset form
} else {
?>
<form method="post" action="password_reset.php" name="password_reset_form">
	Atkurkite pamirštą slaptažodį įvesdami savo el. pašto adresą, kurį nurodėte registruodamiesi sistemoje. Instrukcijas gausite el. pašto adresu:<br><br>
	<input id="user_email" type="text" name="user_email" required="required" />
	<input type="submit" name="request_password_reset" value="Atkurti" />
</form>
<?php
}
?>

<?php
// include html footer
include('footer.php');
