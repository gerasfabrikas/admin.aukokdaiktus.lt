<?php if($login->isUserLoggedIn() == false) return; if(!isAdmin() and !isGridManager()) {err('Neturite teisės keisti šio vartotojo duomenis', 'red'); return;}

$legalStatuses = array('Akcinė bendrovė', 'Uždaroji akcinė bendrovė', 'Individuali įmonė', 'Mažoji bendrija', 'Kitos įmonės', 
'Valstybės/savivaldybės įmonė/įstaiga', 'Nevyriausybinė organizacija', 'Bendrija', 'Bendruomenė', 'Kitos organizacijos');

if(!isset($_GET['user']) or $_GET['user'] == 0) :

	if(isset($_POST['newuser'])) :
		$user_acctype = ( (isset($_POST['user_acctype']) ) ? $_POST['user_acctype'] : 0 );
		if(isGridManager()) : $user_acctype = 1; endif; // apsauga
		
		$user_person = (isset($_POST['user_person']) and $_POST['user_person'] == 1 and $user_acctype == '0' ? 1 : 0);
		
		$user_name = (isset($_POST['user_name']) ? mysqli_real_escape_string($con, $_POST['user_name']) : '');
		
		$user_fname = (isset($_POST['user_fname']) ? mysqli_real_escape_string($con, ucfirst($_POST['user_fname'])) : '');
		$user_lname = (isset($_POST['user_lname']) ? mysqli_real_escape_string($con, ucfirst($_POST['user_lname'])) : '');

		$user_orgname = (isset($_POST['user_orgname']) ? mysqli_real_escape_string($con, $_POST['user_orgname']) : '');
		$user_legalstatus = (isset($_POST['user_legalstatus']) ? mysqli_real_escape_string($con, $_POST['user_legalstatus']) : 0);
		$user_code1 = (isset($_POST['user_code1']) ? mysqli_real_escape_string($con, $_POST['user_code1']) : '');		
		$user_code2 = (isset($_POST['user_code2']) ? mysqli_real_escape_string($con, $_POST['user_code2']) : '');		
		$user_reg = (isset($_POST['user_reg']) ? mysqli_real_escape_string($con, $_POST['user_reg']) : '');
	
		$user_address = (isset($_POST['user_address']) ? mysqli_real_escape_string($con, $_POST['user_address']) : '');
		$user_region = (isset($_POST['user_region']) ? mysqli_real_escape_string($con, $_POST['user_region']) : '');
		$user_city = (isset($_POST['user_city']) ? mysqli_real_escape_string($con, $_POST['user_city']) : '');
		$user_phone = (isset($_POST['user_phone']) ? mysqli_real_escape_string($con, $_POST['user_phone']) : '');
		$user_email = (isset($_POST['user_email']) ? mysqli_real_escape_string($con, $_POST['user_email']) : '');
		$user_desc = (isset($_POST['user_desc']) ? mysqli_real_escape_string($con, $_POST['user_desc']) : '');
		
		$user_password_hash = (isset($_POST['user_password_hash']) ? password_hash($_POST['user_password_hash'], PASSWORD_DEFAULT, array('cost' => 10)) : '');
	

		if($user_name == '' or $user_address == '' or $user_region == '' or $user_city == '' or $user_phone == '' or $user_email == '' or ($user_fname == '' and $user_person == 0) or ($user_lname == '' and $user_person == 0) or ($user_orgname == '' and $user_person == 1) or ($user_code1 == '' and $user_person == 1) or ($user_legalstatus == '' and $user_person == 1) ) :
			err("Užpildykite visus reikalaujamus laukelius", 'red');
		elseif(strlen($user_phone) != 11) :
			err("Netinkamas telefono numerio formatas", 'red');
		elseif($user_name != '' and countData('users', "user_name = '$user_name'") > 0) :
			err("Vartotojas $user_name jau yra įvestas į registruotų vartotojų duomenų bazę", 'red');
		elseif($user_email != '' and countData('users', "user_email = '$user_email'") > 0) :
			err("El. paštas $user_email jau yra įvestas į registruotų vartotojų duomenų bazę", 'red');
		elseif($user_email != '' and countData('users', "user_phone = '$user_phone'") > 0) :
			err("Telefonas $user_phone jau yra įvestas į registruotų vartotojų duomenų bazę", 'red');
		elseif($user_email != '' and countData('needy', "user_email = '$user_email'") > 0) :
			err("El. paštas $user_email jau yra įvestas į neregistruotų vartotojų duomenų bazę", 'red');
		elseif($user_email != '' and countData('needy', "user_phone = '$user_phone'") > 0) :
			err("Telefonas $user_phone jau yra įvestas į neregistruotų vartotojų duomenų bazę", 'red');
		elseif($user_acctype == 1 and $user_desc == '') :
			err("Neįvestas aprašymas", 'red');
		else :
			$id = insertRow('users',
			'user_name, user_acctype, user_active, user_subscribed, user_password_hash, user_legalstatus, user_person, user_fname, user_lname, user_orgname, user_code1, user_code2, user_reg, user_address, user_region, user_city, user_phone, user_email, user_desc, user_registration_datetime',
			"'$user_name', $user_acctype, 1, 1, '$user_password_hash', $user_legalstatus, '$user_person', '$user_fname', '$user_lname', '$user_orgname', '$user_code1', '$user_code2', '$user_reg', '$user_address', '$user_region', '$user_city', '$user_phone', '$user_email', '$user_desc', '".date('Y-m-d H:i:s')."'"
			);
			
			if($id and isGridManager()) :
				updateFieldWhere('users', 'user_parent', CUSER, 'user_id='.$id);
			endif;
			
			if($id) :
				myMail($user_email,
				'Jūsų vartotojo paskyra sukurta',
				'Jūsų pagalbadaiktais.lt ir pagalbadarbais.lt vartotojo paskyra sukurta.<br><br>Paskyros vardas: '.$user_name.'<br>Slaptažodis: '.$_POST['user_password_hash']
				);

                // take care of uploads
                $Core = new Core();
                $result = $Core->uploadFile($id, null, ROOT_URL);
                if(!is_array($result)) {
                    $errExpl = $Core->get('uploadFileWriteToFilesystemErrors');
                    $uperr = $errExpl[$result];
                    $thumb = 0;
                } else {
                    $thumb = $result['fileThumb'];
                }

				if(isset($uperr)) : err($uperr, 'red', 'normal'); endif;
				redirect(0, '?p='.page().'&user='.$id);
			else : err('Vartotojas nesukurtas', 'red');
			endif;
		endif;
	endif;
	
	?>
		<?php $acctype = ( (isset($_GET['acctype']) ) ? $_GET['acctype'] : 0 ); ?>
		<?php $juridinis = (isset($_GET['juridinis']) and $acctype == '0' ? 1 : 0); ?>
		<?php if(isAdmin()) : ?>
		<label class="valign-top">Paskyros tipas<span class="reqfield">*</span></label> <div class="inline">
		<?php
		$atarr = array(0 => 'Dovanotojas arba geradarys', 1 => 'Kuratorius', 2 => 'Tinklo atstovas', 3 => 'Administratorius');
		foreach(listData('acctypes', 'acctype != 1 and acctype != 2 and acctype != 3') as $acc) $atarr[$acc['acctype']] = $acc['name'];
			foreach($atarr as $akey => $aname) :
				if($acctype == $akey) :
					echo '<i class="fa fa-dot-circle-o"></i> '.$aname;
				else :
					echo '<a class="uncolored" href="?p=edituser&acctype='.$akey.($juridinis == 1 ? '&juridinis' : '').'"><i class="fa fa-circle-o"></i> '.$aname.'</a>';
				endif;
				echo '<br>';
			endforeach;
		?>
		</div>
		<?php endif; ?>
		<br><br>
		<?php if($acctype == 0) : ?>
			<label>Asmuo<span class="reqfield">*</span></label>
			<?php if ($juridinis == 0) : ?>
				<i class="fa fa-dot-circle-o"></i> Fizinis <a class="uncolored" href="?p=edituser&juridinis"><i class="fa fa-circle-o"></i> Juridinis</a>
			<?php else : ?>
				<a  class="uncolored" href="?p=edituser"><i class="fa fa-circle-o"></i> Fizinis</a> <i class="fa fa-dot-circle-o"></i> Juridinis
			<?php endif; ?>
		<?php endif; ?>
		<br><br>
		
		<form action="" method="post" enctype="multipart/form-data">
			
			<input type="hidden" name="user_acctype" value="<?php echo $acctype; ?>" />
			<input type="hidden" name="user_person" value="<?php echo $juridinis; ?>" />
			
			<label>Paskyros vardas:<span class="reqfield">*</span></label> <input type="text" value="<?php echo (isset($_POST['user_name']) ? $_POST['user_name'] : ''); ?>" name="user_name" required /><br>
			<label>El. paštas<span class="reqfield">*</span></label> <input type="email" value="<?php echo (isset($_POST['user_email']) ? $_POST['user_email'] : ''); ?>" name="user_email" required /><br>
			
			<label>Slaptažodis<span class="reqfield">*</span></label> <input type="password" value="" name="user_password_hash" required /><br>
			
			<?php if ($juridinis == 0) : ?>
			<label>Vardas<span class="reqfield">*</span></label> <input type="text" value="<?php echo (isset($_POST['user_fname']) ? $_POST['user_fname'] : ''); ?>" name="user_fname" maxlength="32" required /><br>
			<label>Pavardė<span class="reqfield">*</span></label> <input type="text" value="<?php echo (isset($_POST['user_lname']) ? $_POST['user_lname'] : ''); ?>" name="user_lname" maxlength="32" required /><br>
			<?php else : ?>
			<label>Teisinis statusas<span class="reqfield">*</span></label> <select name="user_legalstatus">
				<?php foreach($legalStatuses as $okey => $op) echo '<option '.((isset($_POST['user_legalstatus']) and $_POST['user_legalstatus'] == $okey) ? 'selected="selected"' : '').' value='.$okey.'>'.$op.'</option>'; ?>
			</select><br>
			<label>Pavadinimas<span class="reqfield">*</span></label> <input type="text" value="<?php echo (isset($_POST['user_orgname']) ? $_POST['user_orgname'] : ''); ?>" name="user_orgname"  maxlength="128" required /><br>
			<label>Įmonės kodas<span class="reqfield">*</span></label> <input type="text" value="<?php echo (isset($_POST['user_code1']) ? $_POST['user_code1'] : ''); ?>" name="user_code1"  maxlength="9" required /><br>
			<label>PVM kodas</label> <input type="text" value="<?php echo (isset($_POST['user_code2']) ? $_POST['user_code2'] : ''); ?>" name="user_code2" maxlength="14" /><br>
			<label>Registras</label> <input type="text" value="<?php echo (isset($_POST['user_reg']) ? $_POST['user_reg'] : ''); ?>" name="user_reg"  maxlength="128" /><br>
			<?php endif; ?>
			
			<label>Adresas<span class="reqfield">*</span></label> <input type="text" value="<?php echo (isset($_POST['user_address']) ? $_POST['user_address'] : ''); ?>" name="user_address" required /><br>

			<label>Apskritis<span class="reqfield">*</span></label>
			<select name="user_region"><?php
				foreach($regionsList as $key => $op) echo '<option '.((isset($_POST['user_region']) and $_POST['user_region'] == $key) ? 'selected="selected"' : '').' value='.$key.'>'.$op.'</option>'; ?>
			</select>
			<br>

			<label>Savivaldybė<span class="reqfield">*</span></label>
			<select name="user_city"></select>
			<br>		
			
			<label>Telefonas<span class="reqfield">*</span><br><small>formatu 37012345678</small></label> <input type="text" value="<?php echo (isset($_POST['user_phone']) ? $_POST['user_phone'] : ''); ?>" name="user_phone" maxlength="11" required /><br>
			
			<?php if($acctype > 0) : ?>
				<label>Atstovaujama organizacija</label> <input type="text" value="<?php echo (isset($_POST['user_orgname']) ? $_POST['user_orgname'] : ''); ?>" name="user_orgname"  maxlength="128" /><br>
			<?php endif; ?>
			
			<label>Aprašymas<?php if($acctype == 1 or isGridManager()) : ?><span class="reqfield">*</span><?php endif; ?></label> <textarea name="user_desc" /><?php echo (isset($_POST['user_desc']) ? $_POST['user_desc'] : ''); ?></textarea><br>
			
			<label for="user_thumb"><?php echo ($juridinis == 1 ? 'Logotipas' : 'Nuotrauka'); ?><br><small>JPG, PNG, maks. 5 MB</small></label>
			<input type="file" name="user_thumb" id="user_thumb" />
			<br>
			
			<input type="submit" value="Sukurti" name="newuser" />
		</form>
	<?php

	return;
endif;

$us = $_GET['user'];

if(countData('users', "user_id = '$us'") == 0) {err('Toks vartotojas neegzistuoja', 'red'); return;}
if(isGridManager() and countData('users', "user_parent = ".CUSER." AND user_id = '$us'") == 0) {err('Neturite teisės keisti šio vartotojo duomenis', 'red'); return;}


// Edit

$usermeta = getRow('users', 'user_id = '.$us);

// Title
echo '<div class="edit_header_group">';
echo '<div class="gravatar">'.(strlen($usermeta['user_thumb']) > 16 ? '<img src="'.$usermeta['user_thumb'].'" onError="this.style.visibility=\'hidden\'" />' : '').'</div><div class="gname">';
echo '<div class="name">'.($usermeta['user_person'] == 0 ? $usermeta['user_fname'].' '.$usermeta['user_lname'] : $usermeta['user_orgname']).'</div>';
echo 'Paskyros vardas: <b>'.$usermeta['user_name'].'</b> &middot; Paskyros ID: <b>'.$usermeta['user_id'].'</b>';
echo '<br>Registravosi <b>'.$usermeta['user_registration_datetime'].'</b> iš <b>'.$usermeta['user_registration_ip'].'</b> &middot; ';
echo 'Paskutinį kartą jungėsi <b>'.$usermeta['user_lastlogin'].'</b> iš <b>'.$usermeta['user_lastlogin_ip'].'</b>';
echo '</div></div>';

$options = array();
if(isAdmin() and $usermeta['user_acctype'] == 0) :
	if(isset($_POST['user_acctype']) and $_POST['user_acctype'] != 0) updateField('users', 'user_person', 0, 'user_id', $us);
	$atarr = array(0 => 'Dovanotojas arba geradarys', 1 => 'Kuratorius', 2 => 'Tinklo atstovas', 3 => 'Administratorius');
		foreach(listData('acctypes', 'acctype != 1 and acctype != 2 and acctype != 3') as $acc) $atarr[$acc['acctype']] = $acc['name'];
	$options['fields']['user_acctype'] = array('Paskyros tipas', 'inputtype' => 'drop', 'drops' => $atarr, 'required' => true);
	updateUsermeta($options, $us, 'users', 'user_id');
	echo '<br>';

	$usermeta = getRow('users', 'user_id = '.$us);
endif;

if(isAdmin() and $usermeta['user_acctype'] == 1) :
	if(isset($_POST['user_parent']) and $_POST['user_parent'] > 0) :
		updateFieldWhere('needy', 'user_parent_ta', $_POST['user_parent'], "user_parent = $us");
	endif;
	// Tinklo atstovų sąrašas
	$kuratoriai = array(0 => '— Nepriskirtas —');
	$kuratoriaid = listData('users', 'user_acctype = 2 AND user_active = 1');
	foreach($kuratoriaid as $ku) :
		$kuratoriai[$ku['user_id']] = $ku['user_fname'].' '.$ku['user_lname'].' '.$ku['user_orgname'];
	endforeach;
	$options = array(
		'fields' => array(
			'user_parent'		=> array('Priskirtas tinklo atstovas', 'inputtype' => 'drop', 'drops' => $kuratoriai),
		),
	);
	updateUsermeta($options, $us, 'users', 'user_id');
	echo '<br>';
endif;

$usermeta = getRow('users', 'user_id = '.$us);

$options = array();

$options['fields']['user_active'] = array( 'Paskyra aktyvuota' , 'inputtype' => 'bool', 'required' => true);
$options['fields']['user_email'] = array('El. paštas', 'required' => true);
$options['fields']['user_password_hash'] = array('Slaptažodis', 'inputtype' => 'pass');

if($usermeta['user_person'] == 0) :

$options['fields']['user_fname'] = array('Vardas', 'required' => true);
$options['fields']['user_lname'] = array('Pavardė', 'required' => true);

elseif($usermeta['user_person'] == 1) :

$options['fields']['user_orgname'] =		array('Pavadinimas', 'required' => true);
$options['fields']['user_legalstatus'] =	array('Teisinis statusas', 'inputtype' => 'drop', 'drops' => $legalStatuses, 'required' => true);
$options['fields']['user_code1'] = array('Įmonės kodas', 'len' => 9, 'required' => true);
$options['fields']['user_code2'] = array('PVM mokėtojo kodas', 'len' => 14);
$options['fields']['user_reg'] = array('Registras', 'len' => 128);


endif;


$options['fields']['user_address'] = array('Adresas', 'required' => true);
$options['fields']['user_region'] = array('Apskritis', 'inputtype' => 'drop', 'drops' => $regionsList, 'required' => true);
$options['fields']['user_city'] = array('Savivaldybė', 'inputtype' => 'drop', 'drops' => $citiesList, 'required' => true);
$options['fields']['user_phone'] = array('Telefonas', 'len' => 11, 'required' => true);


if($usermeta['user_acctype'] > 0) :
	$options['fields']['user_orgname'] =		array('Atstovaujama organizacija');
endif;

$options['fields']['user_desc'] = array( ($usermeta['user_person'] == 0 ? 'Apie save' : 'Apie organizaciją') , 'inputtype' => 'textarea');
$options['fields']['user_thumb'] = array( ($usermeta['user_person'] == 0 ? 'Nuotrauka' : 'Logotipas') , 'inputtype' => 'photo');
$options['fields']['user_subscribed'] = array( 'Naujienų prenumerata' , 'inputtype' => 'bool' );

updateUsermeta($options, $us, 'users', 'user_id');
	
?>
