<?php if($login->isUserLoggedIn() == false) return; if(!isAdmin() and !isGridManager()) return;

if(isAction('deaktyvuoti') and getParam()) :
		
	if(isGridManager() and countData('users', "user_parent = ".CUSER." AND user_id = '".getParam()."'") == 0) {err('Neturite teisės keisti šio vartotojo duomenis', 'red'); return;}

	$row = getRow('users', 'user_id = '.getParam());
	$act = $row['user_active'] ? 0 : 1;
	updateFieldWhere('users', 'user_active', $act, 'user_id = '.getParam());
	
	redirect(0, getCurrentLink());
endif;

$tablefields = array(
		'user_id',
		array('converter' => array('user_thumb', 'getThumb')),
		'user_name',
		'user_fname',
		'user_lname',
		'user_orgname',
		'user_phone',
		'user_email',
		array('converter' => array('user_city', 'getCountry')),
		'user_lastlogin',
		array('converter' => array('user_id', 'getUserEditLink')),
		array('action' => array('user_id', 'deaktyvuoti', '<i title="Aktyvuoti/deaktyvuoti" class="fa fa-times"></i>', false)),
		'null',
	);
	$tablehead = array(
		'titles' => array(
		'ID',
		'',
		'<span title="Paskyros vardas">P. vardas</span>',
		'Vardas',
		'Pavardė',
		'Organizacija',
		'Telefonas',
		'El. paštas',
		'Savivaldybė',
		'Pask. k. prisijungė',
		'',
		'',
		'',
		),
		'columns' => array(
		'right fc-60',
		'left fc-30',
		'left fc-75',
		'left fc-75',
		'left fc-100',
		'left fc-100',
		'left fc-90',
		'left',
		'left fc-125',
		'left fc-dates',
		'left fc-16',
		'left fc-16',
		'',
		),
		
	);

	$parent = (isGridManager() ? 'AND user_parent = '.CUSER : '');
	
	getSort('user_id');
	$where = 'SELECT * FROM users WHERE user_acctype='.subpage().' '.$parent.' ORDER BY '.$getsort.$getorder;
	$sortby = array(
		0 => 'user_id',
		2 => 'user_name',
		3 => 'user_fname',
		4 => 'user_lname',
		5 => 'user_orgname',
		6 => 'user_email',
		7 => 'user_phone',
		8 => 'user_city',
		9 => 'user_lastlogin',
		);
	
	formatTable( listData(false, false, pageNum(), $where), $tablefields, $tablehead, $sortby, true, 'nomargin');
	pagination(countData(false, false, $where));
	
?>

<br><br>
<a href="?p=edituser"><i class="fa fa-asterisk"></i> Sukurti vartotoją</a>