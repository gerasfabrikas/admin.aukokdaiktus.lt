<?php if($login->isUserLoggedIn() == false) return; if(!isAdmin() and !isManager() and !isGridManager()) return;

if(!isset($_GET['need']) and !isset($_GET['type'])) return;

$need_type = ( (isset($_GET['type']) and ($_GET['type'] == 1 or $_GET['type'] == 2))  ? $_GET['type'] : 1);
if($need_type == 1) $need_cat_type = 3;
if($need_type == 2) $need_cat_type = 4;

if(!isset($_GET['need']) or $_GET['need'] == 0) :
	return;
endif;

$us = $_GET['need'];

if(countData('needs', "need_id = '$us'") == 0) {err('Toks poreikis neegzistuoja', 'red'); return;}
if(isManager() and countData(false, false, "SELECT * FROM needs INNER JOIN needy ON needs.need_needy = needy.user_id WHERE needs.need_id = $us AND needy.user_parent = ".CUSER) == 0) {err('Neturite teisės keisti šio poreikio duomenis', 'red'); return;}

if(isGridManager()) :
	$children = array();
	foreach(listData('users', 'user_acctype = 1 AND user_active = 1 AND user_parent = '.CUSER) as $child) $children[] = 'needy.user_parent = '.$child['user_id'];
	$parent2 = '(('.implode(' OR ', $children).') OR needy.user_parent_ta = '.CUSER.')';
	if(countData(false, false, "SELECT * FROM needs INNER JOIN needy ON needs.need_needy = needy.user_id WHERE needs.need_id = $us AND ".$parent2) == 0) {err('Neturite teisės keisti šio poreikio duomenis', 'red'); return;}
endif;

// Edit

$usermeta = getRow('needs', 'need_id = '.$us);


$need_type = $usermeta['need_type'];
if($need_type == 1) $need_cat_type = 3;
if($need_type == 2) $need_cat_type = 4;

// Title
echo '<div class="edit_header_group">';
echo '<div class="name single">'.($need_type == 1 ? 'Darbas': 'Daiktas').': '.$usermeta['need_name'].'</div>';
echo 'Modifikavimo data: '.$usermeta['need_regdate'];
echo '</div>';

$options = array();


$needy = getRow('needy', 'user_id = '.$usermeta['need_needy']);
if(empty($needy)) err('Informacinė sistema duomenų bazėje nerado nurodyto stokojančiojo. Skelbimas nebus publikuojamas svetainėje. Prašome nurodyti stokojantįjį iš naujo', 'red');

if(isAdmin()) : $parent = '';
elseif(isManager() or isGridManager()) : $parent = 'AND user_parent = '.CUSER;
/*elseif(isGridManager()) :
	$children = array();
	foreach(listData('users', 'user_acctype = 1 AND user_active = 1 AND user_parent = '.CUSER) as $child) $children[] = 'user_parent = '.$child['user_id'];
	$parent = 'AND (('.implode(' OR ', $children).') OR user_parent_ta = '.CUSER.')';*/
endif;

$stokojantys = array();
$ops = listData('needy', "deleted = 0 $parent ORDER BY user_lname");
foreach($ops as $op) $stokojantys[$op[0]] = $op['user_lname'].' '.$op['user_fname'].' '.$op['user_orgname'];

$options['fields']['need_needy'] = array('Stokojantis', 'inputtype' => 'drop', 'drops' => $stokojantys, 'required' => true);

$kats = array();
$ops = listData('cats', 'cat_type = '.$need_cat_type.' AND deleted = 0 AND cat_level = 0');
foreach($ops as $op) $kats[$op[0]] = $op['cat_name'];

$options['fields']['need_cat'] = array('Kategorija', 'inputtype' => 'drop', 'drops' => $kats, 'required' => true);

if($usermeta['need_type'] == 2) :
	$subkats = array();
	$ops = listData('cats', 'cat_type = '.$need_cat_type.' AND deleted = 0 AND cat_level = 1 AND cat_parent = '.$usermeta['need_subcat']);
	foreach($ops as $op) $subkats[$op[0]] = $op['cat_name'];
	$options['fields']['need_subcat'] = array('Subkategorija', 'inputtype' => 'drop', 'drops' => $subkats, 'required' => true);
endif;

$options['fields']['need_name'] = array('Pavadinimas', 'required' => true);
$options['fields']['need_desc'] = array('Aprašymas', 'inputtype' => 'textarea', 'required' => true);
$options['fields']['need_expires'] = array('Galiojimo laikas', 'required' => true);

updateUsermeta($options, $us, 'needs', 'need_id');

echo '<br><hr><br>';

$options = array();
$options['fields']['need_full'] = array('Poreikis patenkintas', 'inputtype' => 'drop', 'drops' => array('Ne', 'Taip'));

updateUsermeta($options, $us, 'needs', 'need_id');

if($usermeta['need_full'] == 1) :
	echo '<br><hr><br>';
	
	echo '<b>Aprašymas</b><br><br>';
	if($usermeta['need_full_desc'] != '') echo $usermeta['need_full_desc'].'<br><br>';
	else echo '<i>Poreikį patenkinęs vartotojas neįvedė aprašymo</i><br></br>';
	
	if($usermeta['need_full_photo'] != '') echo '<img style="max-height: 200px; max-width: 640px;" src="'.$usermeta['need_full_photo'].'" alt="" onError="this.style.visibility=\'hidden\'" /><br><br>';
	
	echo '<br><hr><br>';

	echo '<b>Informacija apie '.($need_type == 1 ? 'darbo': 'daikto').' poreikį patenkinusį vartotoją</b><br><br>';

	if($usermeta['need_full_user'] > 0) :
		$usermeta2 = getRow('users', 'user_id = '.$usermeta['need_full_user']);
		if(!empty($usermeta2)) :
			echo '<label>Registracija</label>Registruotas vartotojas. <a href="?p=edituser&user='.$usermeta['need_full_user'].'">Detalesnė informacija</a><br>';
		else :
			err('Poreikį patenkino registruotas vartotojas, tačiau informacinė sistema nerado duomenų apie šį vartotoją. Galbūt įrašas apie vartotoją pašalintas iš duomenų bazės?', 'red');
			return;
		endif;
	elseif($usermeta['need_full_user'] == 0) :
		$usermeta2 = $usermeta;
		echo '<label>Registracija</label>Neregistruotas vartotojas<br>';
	endif;

	echo '<label>Vardas</label>'.$usermeta2['user_fname'].'<br>';
	echo '<label>Pavardė</label>'.$usermeta2['user_lname'].'<br>';
	echo '<label>Adresas</label>'.$usermeta2['user_address'].'<br>';
	echo '<label>Apskritis</label>'.$regionsList[$usermeta2['user_region']].'<br>';
	echo '<label>Savivaldybė</label>'.$citiesList[$usermeta2['user_city']].'<br>';
	echo '<label>Telefonas</label>'.$usermeta2['user_phone'].'<br>';
	echo '<label>El. paštas</label>'.$usermeta2['user_email'].'<br>';
endif;

if(isset($_POST['updateUsermeta'])) updateFieldWhere('needs', 'need_regdate', date('Y-m-d H:i:s'), 'need_id = '.$us);
?>
