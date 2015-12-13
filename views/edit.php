<!-- edit form for user's password / this form uses the HTML5 attribute "required" -->
<form method="post" action="" name="user_edit_form_password">
	<label for="user_password_old"><?php echo $phplogin_lang['Old password']; ?><span class="reqfield">*</span></label><input id="user_password_old" type="password" name="user_password_old" autocomplete="off" required />
	<br>
	<label for="user_password_new"><?php echo $phplogin_lang['New password']; ?><span class="reqfield">*</span></label><input id="user_password_new" type="password" name="user_password_new" autocomplete="off" required />
	<br>
	<label for="user_password_repeat"><?php echo $phplogin_lang['Repeat new password']; ?><span class="reqfield">*</span></label><input id="user_password_repeat" type="password" name="user_password_repeat" autocomplete="off" required />

	<br>
	<label></label><input type="submit" name="user_edit_submit_password" value="<?php echo $phplogin_lang['Change password']; ?>" />
</form>
<br>
<?php

$options = array(
	'fields' => array(
		'user_thumb'			=> array( ($usermeta['user_person'] == 0 ? 'Nuotrauka' : 'Logotipas') , 'inputtype' => 'photo'),
	),
);
updateUsermeta($options, CUSER, 'users', 'user_id');
echo '<br>';

if($usermeta['user_person'] == 0) :
	$options = array(
		'fields' => array(
			'user_fname'		=> array('Vardas', 'required' => true),
			'user_lname'		=> array('Pavardė', 'required' => true),
		),
	);
elseif($usermeta['user_person'] == 1) :
    $Core = new Core();
    $legalStatuses = $Core->get('legalStatuses');

	$options = array(
		'fields' => array(
			'user_orgname'		=> array('Pavadinimas', 'required' => true),
			'user_legalstatus'	=> array('Teisinis statusas', 'inputtype' => 'drop', 'drops' => $legalStatuses, 'required' => true),
			'user_code1'		=> array('Įmonės kodas', 'len' => 9, 'required' => true),
			'user_code2'		=> array('PVM mokėtojo kodas', 'len' => 14),
			'user_reg'			=> array('Registras', 'len' => 128),
		),
	);
endif;

$options['fields']['user_address'] = 	array('Adresas', 'required' => true);
$options['fields']['user_region'] = 		array('Miestas', 'inputtype' => 'drop', 'drops' => $regionsList, 'required' => true);
$options['fields']['user_city'] = 		array('Miestas', 'inputtype' => 'drop', 'drops' => $citiesList, 'required' => true);
$options['fields']['user_phone'] = 		array('Telefonas', 'len' => 11, 'required' => true);
$options['fields']['user_desc'] = 		array( ($usermeta['user_person'] == 0 ? 'Apie save' : 'Apie organizaciją') , 'inputtype' => 'textarea');
$options['fields']['user_subscribed'] = array( 'Naujienų prenumerata' , 'inputtype' => 'bool' );

updateUsermeta($options, CUSER, 'users', 'user_id');

?>

