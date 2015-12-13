<?php
// include html header and display php-login message/error
$regmenu = 'registration';

include('header.php');

// show negative messages
if ($registration->errors) {
    foreach ($registration->errors as $error) {
         err($error, 'red');
    }
}

// show positive messages
if ($registration->messages) {
    foreach ($registration->messages as $message) {
        echo err($message);
    }
}

// show register form
// - the user name input field uses a HTML5 pattern check
// - the email input field uses a HTML5 email type check
if (!$registration->registration_successful && !$registration->verification_successful) { ?>

<form method="post" action="register.php" name="registerform" enctype="multipart/form-data">   
	<label for="user_name">Paskyros vardas<span class="reqfield">*</span></label>
	<input id="user_name" type="text" pattern="[a-zA-Z0-9]{2,64}" name="user_name" required />
	<br>
	<label for="user_email">El. paštas<span class="reqfield">*</span></label>
	<input id="user_email" type="email" name="user_email" required />
	<br>
	<label for="user_password_new">Slaptažodis<span class="reqfield">*</span></label>
	<input id="user_password_new" type="password" name="user_password_new" pattern=".{6,}" required autocomplete="off" />  
	<br>
	<label for="user_password_repeat">Pakartokite slaptažodį<span class="reqfield">*</span></label>
	<input id="user_password_repeat" type="password" name="user_password_repeat" pattern=".{6,}" required autocomplete="off" />
	<br>
	<br>
	<?php if(isset($_GET['juridinis'])) : ?>
		<label for="user_orgname">Pavadinimas<span class="reqfield">*</span></label>
		<input id="user_orgname" type="text" maxlength="128" name="user_orgname" required />
		<br>
		<label for="user_legalstatus">Teisinė forma<span class="reqfield">*</span></label>
        <select id="user_legalstatus" name="user_legalstatus"><?php

            $Core = new Core();
            $legalStatuses = $Core->get('legalStatuses');

            foreach($legalStatuses as $key => $legalStatus) {
                echo '<option value="' . $key . '">' . htmlspecialchars($legalStatus, ENT_QUOTES, CHARSET) . '</option>';
            }

            ?>
		</select>
		<br>
		<label for="user_code1">Įmonės kodas<span class="reqfield">*</span></label>
		<input id="user_code1" type="text" maxlength="9" name="user_code1" required />
		<br>
		<label for="user_code2">PVM mokėtojo kodas</label>
		<input id="user_code2" type="text" maxlength="14" name="user_code2" />
		<br>
		<label for="user_reg">Registras</label>
		<input id="user_reg" type="text" maxlength="128" name="user_reg" />
		
		<input type="hidden" name="user_person" value="1" />
		<input type="hidden" name="user_fname" value="" />
		<input type="hidden" name="user_lname" value="" />
	<?php else : ?>
		<label for="user_fname">Vardas<span class="reqfield">*</span></label>
		<input id="user_fname" type="text" maxlength="32" name="user_fname" required />
		<br>
		<label for="user_lname">Pavardė<span class="reqfield">*</span></label>
		<input id="user_lname" type="text" maxlength="32" name="user_lname" required />
						
		<input type="hidden" name="user_person" value="0" />
		<input type="hidden" name="user_orgname" value="" />
		<input type="hidden" name="user_legalstatus" value="" />
		<input type="hidden" name="user_code1" value="" />
		<input type="hidden" name="user_code2" value="" />
		<input type="hidden" name="user_reg" value="" />
	<?php endif; ?>
	<br>
	<br>
	<label for="user_address">Adresas<span class="reqfield">*</span></label>
	<input id="user_address" type="text" maxlength="256" name="user_address" required />
	<br>
	<label for="user_city">Miestas arba rajonas<span class="reqfield">*</span></label>
	<select id="user_city" name="user_city">
		<?php foreach($citiesList as $key => $city) echo '<option value="'.$key.'">'.$city.'</option>'; ?>
	</select>
	<br>
	<label for="user_phone">Telefonas<span class="reqfield">*</span><br><small>formatu 37012345678</small></label>
	<input id="user_phone" type="text" maxlength="11" name="user_phone" value="370" required />
	<br>
	<br>
	
	<label for="user_desc"><?php echo (isset($_GET['juridinis']) ? 'Apie organizaciją' : 'Apie save'); ?></label>
	<textarea id="user_desc" name="user_desc"></textarea>
	<br>
	<br>
	
	<label for="user_thumb"><?php echo (isset($_GET['juridinis']) ? 'Logotipas' : 'Nuotrauka'); ?><br><small>JPG, PNG, maks. 5 MB</small></label>
	<input type="file" name="user_thumb" id="user_thumb" />
	<br><br>
	
	<label for="user_subscribed">Prenumeruoti naujienas</label>
	<input id="user_subscribed" type="checkbox" name="user_subscribed" value="1" />


	
	<br>
	<img class="captcha" src="tools/showCaptcha.php" alt="captcha" />
	<br>
	<label>Įveskite kodą, kurį matote paveikslėlyje<span class="reqfield">*</span></label>
	<input type="text" name="captcha" required />
	<br>
	<br>
	<label>Su <a href="/taisykles" target="_blank">taisyklėmis</a> sutinku</label>
	<input id="ag" type="checkbox" name="ag" value="1" />	
	<br>
	<br>
	<input type="submit" name="register" value="Registruotis" />
</form>
<?php } ?>

<?php
// include html footer
include('footer.php');
