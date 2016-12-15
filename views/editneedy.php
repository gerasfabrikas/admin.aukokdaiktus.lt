<?php if($login->isUserLoggedIn() == false) return;  if(!isAdmin() and !isManager() and !isGridManager()) return;

if(!isset($_GET['user']) or $_GET['user'] == 0) :

	if(isset($_POST['newuser'])) :
			$user_person = (isset($_POST['user_person']) ? mysqli_real_escape_string($con, $_POST['user_person']) : '');

			$user_fname = (isset($_POST['user_fname']) ? mysqli_real_escape_string($con, ucfirst($_POST['user_fname'])) : '');
			$user_lname = (isset($_POST['user_lname']) ? mysqli_real_escape_string($con, ucfirst($_POST['user_lname'])) : '');

			$user_orgname = (isset($_POST['user_orgname']) ? mysqli_real_escape_string($con, $_POST['user_orgname']) : '');
			$user_code1 = (isset($_POST['user_code1']) ? mysqli_real_escape_string($con, $_POST['user_code1']) : '');		
			$user_code2 = (isset($_POST['user_code2']) ? mysqli_real_escape_string($con, $_POST['user_code2']) : '');		
			$user_reg = (isset($_POST['user_reg']) ? mysqli_real_escape_string($con, $_POST['user_reg']) : '');
		
			$user_cat = (isset($_POST['user_cat']) ? mysqli_real_escape_string($con, $_POST['user_cat']) : '');
			$user_address = (isset($_POST['user_address']) ? mysqli_real_escape_string($con, $_POST['user_address']) : '');
			$user_region = (isset($_POST['user_region']) ? mysqli_real_escape_string($con, $_POST['user_region']) : '');
			$user_city = (isset($_POST['user_city']) ? mysqli_real_escape_string($con, $_POST['user_city']) : '');
			$user_phone = (isset($_POST['user_phone']) ? mysqli_real_escape_string($con, $_POST['user_phone']) : '');
			$user_email = (isset($_POST['user_email']) ? mysqli_real_escape_string($con, $_POST['user_email']) : '');
			$user_desc = (isset($_POST['user_desc']) ? mysqli_real_escape_string($con, $_POST['user_desc']) : '');
		
			if($user_person == '' or $user_cat == '' or $user_address == '' or $user_region == '' or $user_city == '' or $user_desc == '' or $user_phone == '' or ($user_email == '' and $user_person == 1) or ($user_fname == '' and $user_person == 0) or ($user_lname == '' and $user_person == 0) or ($user_orgname == '' and $user_person == 1)  or ($user_code1 == '' and $user_person == 1) ) :
				err("Užpildykite visus reikalaujamus laukelius", 'red');
			elseif(strlen($user_phone) != 11) :
				err("Netinkamas telefono numerio formatas", 'red');
			elseif(strlen($user_desc) < 5) :
				err("Aprašymas per trumpas", 'red');
			elseif($user_email != '' and countData('users', "user_email = '$user_email'") > 0) :
				err("El. paštas $user_email jau yra įvestas į registruotų vartotojų duomenų bazę", 'red');
			elseif($user_email != '' and countData('users', "user_phone = '$user_phone'") > 0) :
				err("Telefonas $user_phone jau yra įvestas į registruotų vartotojų duomenų bazę", 'red');
			elseif($user_email != '' and countData('needy', "user_email = '$user_email'") > 0) :
				err("El. paštas $user_email jau yra įvestas į neregistruotų vartotojų duomenų bazę", 'red');
			elseif($user_email != '' and countData('needy', "user_phone = '$user_phone'") > 0) :
				err("Telefonas $user_phone jau yra įvestas į neregistruotų vartotojų duomenų bazę", 'red');
			else :
				$id = insertRow('needy',
				'user_person, user_fname, user_lname, user_orgname, user_code1, user_code2, user_reg, user_cat, user_address, user_region, user_city, user_phone, user_email, user_desc, user_registration_datetime',
				"'$user_person', '$user_fname', '$user_lname', '$user_orgname', '$user_code1', '$user_code2', '$user_reg', '$user_cat', '$user_address', '$user_region', '$user_city', '$user_phone', '$user_email', '$user_desc', '".date('Y-m-d H:i:s')."'"
				);
				
				if($id and isManager()) :
					updateFieldWhere('needy', 'user_parent', CUSER, 'user_id='.$id);
					updateFieldWhere('needy', 'user_parent_ta', getManagerParent(CUSER), 'user_id='.$id);
				elseif($id and (isAdmin() or isGridManager()) ) :
					$user_parent = (isset($_POST['user_parent']) ? mysqli_real_escape_string($con, $_POST['user_parent']) : 0);
					updateFieldWhere('needy', 'user_parent', $user_parent, 'user_id='.$id);
					updateFieldWhere('needy', 'user_parent_ta', getManagerParent($user_parent), 'user_id='.$id);
				endif;
				
				if($id) :
					if($_FILES and isset($_FILES['user_thumb']) and $_FILES['user_thumb']["tmp_name"] != '') :
                        $upfilename = date('YmdHis') . str_replace('.', '_', uniqid('_', true)) . '.' . pathinfo($_FILES["user_thumb"]["name"], PATHINFO_EXTENSION);
                        $upDirNative = ROOT_PATH . 'uploads' . DIRECTORY_SEPARATOR . 'needy' . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATO . 'native' . DIRECTORY_SEPARATORR;
                        $upDirThumb = ROOT_PATH . 'uploads' . DIRECTORY_SEPARATOR . 'needy' . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . 'thumb' . DIRECTORY_SEPARATORR;
                        $upDirNativeUrl = ROOT_URL . 'uploads/needy/' . $id . '/native/';
                        $upDirThumbUrl = ROOT_URL . 'uploads/needy/' . $id . '/thumb/';

                        if(!is_dir($upDirNative)) {
                            mkdir($upDirNative, 0777, true);
                        }
                        if(!is_dir($upDirThumb)) {
                            mkdir($upDirThumb, 0777, true);
                        }
						$uptempname = $_FILES['user_thumb']["tmp_name"];
						$mm = mime_content_type($uptempname);
						$upfilesize = (int)$_FILES['user_thumb']["size"]/(1024*1024);
						if($upfilesize < 5) :
							if(move_uploaded_file($uptempname, ($upDirNative . $upfilename))) :
								$pathParts = pathinfo($path);
								if($mm == 'image/jpeg' or $mm == 'image/png') :
									if(thumb(($upDirNative . $upfilename), ($upDirThumb . $upfilename), 60, 60, $pathParts['extension']) == true) :
										$thumb = $upDirThumbUrl . $upfilename;
										updateField('needy', 'user_thumb', $thumb, 'user_id', $id);
									else : $uperr = 'Failo įkėlimo klaida: nepavyko sukurti miniatiūros';
									endif;
								else : $uperr = 'Failo tipas netinkamas. Galima įkelti tik JPEG ir PNG failus';
								endif;
							else : $uperr = 'Failo įkėlimo klaida';
							endif;
						else: $uperr = 'Failas per didelis. Galima įkelti failus iki 5 MB';
						endif;
					else : $thumb = 0;
					endif;
					if(isset($uperr)) : err($uperr, 'red', 'normal'); endif;
					redirect(0, '?p='.page().'&user='.$id);
				else : err('Vartotojas nesukurtas', 'red');
				endif;
			endif;
	endif;
	
	$kuratoriai = array();
	$parent = '';
	if(isAdmin()) $parent = '';
	if(isGridManager()) $parent = 'AND user_parent = '.CUSER;
	$kuratoriaid = listData('users', "user_acctype = 1 AND user_active = 1 $parent");
	if(isAdmin()) $kuratoriaid = listData('users', "user_active = 1 AND (user_acctype = 1 OR user_acctype = 2 OR user_acctype = 3)");
	foreach($kuratoriaid as $ku) :
		$kuratoriai[$ku['user_id']] = $ku['user_fname'].' '.$ku['user_lname'].' '.$ku['user_orgname'];
	endforeach;
	$kuratoriai[CUSER] = '— Aš —';
	
	?>
	<label>Asmuo<span class="reqfield">*</span></label>
	<?php if (!isset($_GET['juridinis'])) : ?>
		<i class="fa fa-dot-circle-o"></i> Fizinis <a class="uncolored" href="?p=editneedy&juridinis"><i class="fa fa-circle-o"></i> Juridinis</a>
	<?php else : ?>
		<a  class="uncolored" href="?p=editneedy"><i class="fa fa-circle-o"></i> Fizinis</a> <i class="fa fa-dot-circle-o"></i> Juridinis
	<?php endif; ?>
	<form action="" method="post" enctype="multipart/form-data">
		<?php if (!isset($_GET['juridinis'])) : ?>
		<input type="hidden" name="user_person" value="0" />
		<label>Vardas<span class="reqfield">*</span></label> <input type="text" value="<?php echo (isset($_POST['user_fname']) ? $_POST['user_fname'] : ''); ?>" name="user_fname" maxlength="32" required="required" /><br>
		<label>Pavardė<span class="reqfield">*</span></label> <input type="text" value="<?php echo (isset($_POST['user_lname']) ? $_POST['user_lname'] : ''); ?>" name="user_lname" maxlength="32" required="required" /><br>
		<?php else : ?>
		<input type="hidden" name="user_person" value="1" />
		<label>Pavadinimas<span class="reqfield">*</span></label> <input type="text" value="<?php echo (isset($_POST['user_orgname']) ? $_POST['user_orgname'] : ''); ?>" name="user_orgname"  maxlength="128" required="required" /><br>
		<label>Įmonės kodas<span class="reqfield">*</span></label> <input type="text" value="<?php echo (isset($_POST['user_code1']) ? $_POST['user_code1'] : ''); ?>" name="user_code1"  maxlength="9" required="required" /><br>
		<label>PVM kodas</label> <input type="text" value="<?php echo (isset($_POST['user_code2']) ? $_POST['user_code2'] : ''); ?>" name="user_code2" maxlength="14" /><br>
		<label>Registras</label> <input type="text" value="<?php echo (isset($_POST['user_reg']) ? $_POST['user_reg'] : ''); ?>" name="user_reg"  maxlength="128" /><br>
		<?php endif; ?>
		
		<label>Kategorija<span class="reqfield">*</span></label>
		<select name="user_cat"><?php
			$ct = (isset($_GET['juridinis']) ? 2 : 1);
			$ops = listData('cats', 'cat_type = '.$ct.' AND deleted = 0');
			foreach($ops as $op) echo '<option '.((isset($_POST['user_cat']) and $_POST['user_cat'] == $op[0]) ? 'selected="selected"' : '').' value='.$op[0].'>'.$op[1].'</option>'; ?>
		</select>
		<br>
		
		<label>Adresas<span class="reqfield">*</span></label>
        <input type="text" value="<?php echo (isset($_POST['user_address']) ? $_POST['user_address'] : ''); ?>" name="user_address" required="required" /><br>

		<label>Apskritis<span class="reqfield">*</span></label>
		<select name="user_region"><?php
			foreach($regionsList as $key => $op) echo '<option '.((isset($_POST['user_region']) and $_POST['user_region'] == $key) ? 'selected="selected"' : '').' value='.$key.'>'.$op.'</option>'; ?>
		</select>
		<br>

		<label>Savivaldybė<span class="reqfield">*</span></label>
		<select name="user_city"></select>
		<br>		
		
		<label>Telefonas<span class="reqfield">*</span><br><small>formatu 37012345678</small></label> <input type="text" value="<?php echo (isset($_POST['user_phone']) ? $_POST['user_phone'] : ''); ?>" name="user_phone" maxlength="11" required="required" /><br>
		
		<label>El. paštas<?php if(isset($_GET['juridinis'])) : ?><span class="reqfield">*</span><?php endif; ?></label> <input type="email" value="<?php echo (isset($_POST['user_email']) ? $_POST['user_email'] : ''); ?>" name="user_email" <?php if(isset($_GET['juridinis'])) : ?>required<?php endif; ?> /><br>
		
		<label>Aprašymas<span class="reqfield">*</span></label> <textarea name="user_desc" required="required" /><?php echo (isset($_POST['user_desc']) ? $_POST['user_desc'] : ''); ?></textarea><br>
		
		<label for="user_thumb"><?php echo (isset($_GET['juridinis']) ? 'Logotipas' : 'Nuotrauka'); ?><br><small>JPG, PNG, maks. 5 MB</small></label>
		<input type="file" name="user_thumb" id="user_thumb" />
		<br>
		<?php if(isAdmin() or isGridManager()) : ?>
		<label>Kuratorius<span class="reqfield">*</span></label> <select name="user_parent">
			<?php foreach($kuratoriai as $key => $kur) echo '<option '.($key == CUSER ? 'selected="selected"' : '').' value="'.$key.'">'.$kur.'</option>'; ?>
		</select><br>
		<?php endif; ?>
		<input type="submit" value="Sukurti" name="newuser" />
	</form>
	<?php

	return;
endif;

$us = $_GET['user'];

if(countData('needy', "user_id = '$us'") == 0) {err('Toks vartotojas neegzistuoja', 'red'); return;}
if(isManager() and countData('needy', "user_parent = ".CUSER." AND user_id = '$us'") == 0) {err('Neturite teisės keisti šio vartotojo duomenis', 'red'); return;}

if(isGridManager()) :
	$children = array();
	foreach(listData('users', 'user_acctype = 1 AND user_active = 1 AND user_parent = '.CUSER) as $child) $children[] = 'user_parent = '.$child['user_id'];
	$parent = '('.implode(' OR ', $children).')';
	if(countData('needy', "(($parent) OR user_parent = ".CUSER." OR user_parent_ta = ".CUSER.") AND user_id = '$us'") == 0) {err('Neturite teisės keisti šio vartotojo duomenis', 'red'); return;}
endif;

// Edit

$usermeta = getRow('needy', 'user_id = '.$us);

// Title
echo '<div class="edit_header_group">';
echo '<div class="gravatar">'.(strlen($usermeta['user_thumb']) > 16 ? '<img src="'.$usermeta['user_thumb'].'" onError="this.style.visibility=\'hidden\'" />' : '').'</div><div class="gname">';
echo '<div class="name">'.($usermeta['user_person'] == 0 ? $usermeta['user_fname'].' '.$usermeta['user_lname'] : $usermeta['user_orgname']).'</div>';
echo 'Registracijos data: '.$usermeta['user_registration_datetime'];
echo '</div></div>';


/*$options = array(
	'fields' => array(
		'user_person'		=> array('Asmuo', 'inputtype' => 'drop', 'drops' => array(0 => 'Fizinis', 1=> 'Juridinis'), 'required' => true),
	),
);
updateUsermeta($options, $us, 'needy', 'user_id');
echo '<br>';

$usermeta = getRow('needy', 'user_id = '.$us);*/

$kuratoriai = array();
$parent = '';
if(isAdmin()) $parent = '';
if(isGridManager()) $parent = 'AND user_parent = '.CUSER;
$kuratoriaid = listData('users', "user_acctype = 1 AND user_active = 1 $parent");
if(isAdmin()) $kuratoriaid = listData('users', "user_active = 1 AND (user_acctype = 1 OR user_acctype = 2 OR user_acctype = 3)");
foreach($kuratoriaid as $ku) :
	$kuratoriai[$ku['user_id']] = $ku['user_fname'].' '.$ku['user_lname'].' '.$ku['user_orgname'];
endforeach;
$kuratoriai[CUSER] = '— Aš —';

$options = array();

if($usermeta['user_person'] == 0) :

$options['fields']['user_fname'] = array('Vardas', 'required' => true);
$options['fields']['user_lname'] = array('Pavardė', 'required' => true);

elseif($usermeta['user_person'] == 1) :

$options['fields']['user_orgname'] =		array('Pavadinimas', 'required' => true);
$options['fields']['user_code1'] = array('Įmonės kodas', 'len' => 9, 'required' => true);
$options['fields']['user_code2'] = array('PVM mokėtojo kodas', 'len' => 14);
$options['fields']['user_reg'] = array('Registras', 'len' => 128);

endif;

$ct = ($usermeta['user_person'] == 1 ? 2 : 1);
$cats = array();
foreach(listData('cats', 'cat_type = '.$ct.' AND deleted = 0') as $cat) $cats[$cat[0]] = $cat[1];

$getReg = $usermeta['user_region'];
$regionChildren = array();
foreach($regionsListChildren[$getReg] as $children) :
	$regionChildren[$children] = $citiesList[$children];
endforeach;

if(isset($_POST['user_parent'])) {updateFieldWhere('needy', 'user_parent_ta', getManagerParent($_POST['user_parent']), 'user_id='.$us);}

$options['fields']['user_address'] = array('Adresas', 'required' => true);
$options['fields']['user_cat'] = array('Kategorija', 'required' => true, 'inputtype' => 'drop', 'drops' => $cats);
$options['fields']['user_region'] = array('Apskritis', 'inputtype' => 'drop', 'drops' => $regionsList, 'required' => true);
$options['fields']['user_city'] = array('Miestas', 'inputtype' => 'drop', 'drops' => $regionChildren, 'required' => true);
$options['fields']['user_phone'] = array('Telefonas', 'len' => 11, 'required' => true);
if($usermeta['user_person'] == 0) :
	$options['fields']['user_email'] = array('El. paštas');
elseif($usermeta['user_person'] == 1) :
	$options['fields']['user_email'] = array('El. paštas', 'required' => true);
endif;
$options['fields']['user_desc'] = array( 'Aprašymas' , 'inputtype' => 'textarea');
$options['fields']['user_thumb'] = array( ($usermeta['user_person'] == 0 ? 'Nuotrauka' : 'Logotipas') , 'inputtype' => 'photo');
if(isAdmin() or isGridManager()) $options['fields']['user_parent'] = array( 'Priskirtas kuratorius' , 'inputtype' => 'drop', 'drops' => $kuratoriai);
updateUsermeta($options, $us, 'needy', 'user_id');

$getTA = getRow('needy', 'user_id = '.$us);
if($getTA['user_parent_ta'] > 0) :
	$getTA = getRow('users', 'user_id = '.$getTA['user_parent_ta']);
	echo '<br><label>Priskirtas tinklo atstovas</label> '.$getTA['user_fname'].' '.$getTA['user_lname'].' '.$getTA['user_orgname'];
endif;
?>
