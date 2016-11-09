<?php if($login->isUserLoggedIn() == false) return;

// @todo: remove this errors part...
if(isset($_SERVER['APP_ENV']) && (strtolower($_SERVER['APP_ENV']) == 'dev')) {
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors','on');
} else {
    error_reporting(0);
    ini_set('display_errors',false);
}

if(isAction('deaktyvuoti') and getParam()) :
		
	if(isGridManager() and countData('users', "user_parent = ".CUSER." AND user_id = '".getParam()."'") == 0) {err('Neturite teisės keisti šio vartotojo duomenis', 'red'); return;}

	$row = getRow('users', 'user_id = '.getParam());
	$act = $row['user_active'] ? 0 : 1;
	updateFieldWhere('users', 'user_active', $act, 'user_id = '.getParam());
	
	redirect(0, getCurrentLink());
endif;

$rg = 0; if(haveRight($rg, 1)) {$tablefields[$rg] = 'user_id'; $tablehead['titles'][$rg] = 'ID'; $tablehead['columns'][$rg] = 'right fc-60'; $sortby[$rg] = 'user_id';}

$rg = 1; if(haveRight($rg, 1)) {$tablefields[$rg] = array('converter' => array('user_acctype', 'getGroupName')); $tablehead['titles'][$rg] = 'Grupė'; $tablehead['columns'][$rg] = 'left fc-75'; $sortby[$rg] = 'user_acctype';}

$rg = 2; if(haveRight($rg, 1)) {$tablefields[$rg] = array('converter' => array('user_active', 'nicerTF')); $tablehead['titles'][$rg] = '<span title="Aktyvacija">Akt</span>'; $tablehead['columns'][$rg] = 'left fc-16'; $sortby[$rg] = 'user_active';}

$rg = 11; if(haveRight($rg, 1)) {$tablefields[$rg] = array('converter' => array('user_thumb', 'getThumb')); $tablehead['titles'][$rg] = ''; $tablehead['columns'][$rg] = 'left fc-30';}

$rg = 3; if(haveRight($rg, 1)) {$tablefields[$rg] = 'user_fname'; $tablehead['titles'][$rg] = 'Vardas'; $tablehead['columns'][$rg] = 'left fc-100'; $sortby[$rg] = 'user_fname';}

$rg = 4; if(haveRight($rg, 1)) {$tablefields[$rg] = 'user_lname'; $tablehead['titles'][$rg] = 'Pavardė'; $tablehead['columns'][$rg] = 'left fc-100'; $sortby[$rg] = 'user_lname';}

$rg = 9; if(haveRight($rg, 1)) {$tablefields[$rg] = 'user_orgname'; $tablehead['titles'][$rg] = 'Organizacija'; $tablehead['columns'][$rg] = 'left fc-100'; $sortby[$rg] = 'user_orgname';}

$rg = 5; if(haveRight($rg, 1)) {$tablefields[$rg] = 'user_address'; $tablehead['titles'][$rg] = 'Adresas'; $tablehead['columns'][$rg] = 'left fc-100'; $sortby[$rg] = 'user_address';}

$rg = 6; if(haveRight($rg, 1)) {$tablefields[$rg] = array('converter' => array('user_region', 'getCounty')); $tablehead['titles'][$rg] = 'Apskritis'; $tablehead['columns'][$rg] = 'left fc-100'; $sortby[$rg] = 'user_region';}

$rg = 7; if(haveRight($rg, 1)) {$tablefields[$rg] = array('converter' => array('user_city', 'getCountry')); $tablehead['titles'][$rg] = 'Savivaldybė'; $tablehead['columns'][$rg] = 'left fc-100'; $sortby[$rg] = 'user_city';}

$rg = 8; if(haveRight($rg, 1)) {$tablefields[$rg] = 'user_phone'; $tablehead['titles'][$rg] = 'Telefonas'; $tablehead['columns'][$rg] = 'left fc-100'; $sortby[$rg] = 'user_phone';}

$rg = 10; if(haveRight($rg, 1)) {$tablefields[$rg] = 'user_desc'; $tablehead['titles'][$rg] = 'Apie'; $tablehead['columns'][$rg] = 'left fc-100'; $sortby[$rg] = 'user_desc';}

$rg = 12; if(haveRight($rg, 1)) {$tablefields[$rg] = array('converter' => array('user_subscribed', 'nicerTF')); $tablehead['titles'][$rg] = '<span title="Prenumerata">Pre</span>'; $tablehead['columns'][$rg] = 'left fc-16'; $sortby[$rg] = 'user_subscribed';}


$rg = 13; if(isAdmin()) {$tablefields[$rg] = array('converter' => array('user_id', 'getUserEditLink')); $tablehead['titles'][$rg] = ''; $tablehead['columns'][$rg] = 'left fc-16';}
$rg = 14; if(isAdmin()) {$tablefields[$rg] = array('action' => array('user_id', 'deaktyvuoti', '<i title="Aktyvuoti/deaktyvuoti" class="fa fa-times"></i>', false)); $tablehead['titles'][$rg] = ''; $tablehead['columns'][$rg] = 'left fc-16';}

	$parent = (isGridManager() ? 'AND user_parent = '.CUSER : '');
	
	getSort('user_id');
	$where = 'SELECT * FROM users WHERE 1 ORDER BY '.$getsort.$getorder;
	
	formatTable( listData(false, false, pageNum(), $where), $tablefields, $tablehead, $sortby, true, 'nomargin');
	pagination(countData(false, false, $where));
	
?>

<br><br>
<a href="?p=edituser"><i class="fa fa-asterisk"></i> Sukurti vartotoją</a>