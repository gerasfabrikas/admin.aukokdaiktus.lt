<?php if($login->isUserLoggedIn() == false) return;  if(!isGridManager()) return;

if(isAction('salinti') and getParam()) :
	if(isManager() and countData('needy', "user_parent = ".CUSER." AND user_id = '".getParam()."'") == 0) {err('Neturite teisės keisti šio vartotojo duomenis', 'red'); return;}
	
	if(isGridManager()) :
		$children = array();
		foreach(listData('users', 'user_acctype = 1 AND user_active = 1 AND user_parent = '.CUSER) as $child) $children[] = 'user_parent = '.$child['user_id'];
		$parent = '('.implode(' OR ', $children).')';
		if(countData('needy', "(($parent) OR user_parent_ta = ".CUSER.") AND user_id = '".getParam()."'") == 0) {err('Neturite teisės keisti šio vartotojo duomenis', 'red'); return;}
	endif;
	
	$row = getRow('needy', 'user_id = '.getParam());
	$act = $row['deleted'];
	$act = !$act;
		
	if(isset($_POST['reason']) or $act == 0) :
		updateFieldWhere('needy', 'deleted', $act, 'user_id = '.getParam());
		
		if(isset($_POST['reason']) and ($_POST['reason'] == 1 or $_POST['reason'] == 2 or $_POST['reason'] == 3)) updateFieldWhere('needy', 'delreason', $_POST['reason'], 'user_id = '.getParam());

		redirect(0, getCurrentLink());
		return;
	endif;
	?>
	<form class="simpleform" action="" method="POST">
		Pažymėkite šalinimo priežastį:<br><br>
			<input checked="checked" name="reason" type="radio" value="1" />nebereikalinga parama/pagalba<br>
			<input name="reason" type="radio" value="2" />privatumo problema<br>
			<input name="reason" type="radio" value="3" />pastebėtas sukčiavimas<br>
		<br><input type="submit" value="Siųsti" />
	</form>
	<?php
	return;
	
endif;

$tablefields = array(
		'user_id',
		array('converter' => array('user_thumb', 'getThumb')),
		'user_fname',
		'user_lname',
		'user_orgname',
		'user_phone',
		array('converter' => array('user_city', 'getCountry')),
		'kuratorius',
		array('converter' => array('user_id', 'getNeedyEditLink')),
		array('action' => array('user_id', 'salinti', '<i title="Šalinti/atnaujinti" class="fa fa-times"></i>', false)),
		'null',
	);
	$tablehead = array(
		'titles' => array(
		'ID',
		'',
		'Vardas',
		'Pavardė',
		'Organizacija',
		'Telefonas',
		'Savivaldybė',
		'Kuratorius',
		'',
		'',
		'',
		),
		'columns' => array(
		'right fc-60',
		'left fc-30',
		'left fc-75',
		'left fc-100',
		'left fc-100',
		'left fc-90',
		'left fc-125',
		'left fc-125',
		'left fc-16',
		'left fc-16',
		'',
		),
		
	);

	
	$children = array();
	foreach(listData('users', 'user_acctype = 1 AND user_active = 1 AND user_parent = '.CUSER) as $child) $children[] = 'needy.user_parent = '.$child['user_id'];
	$parent = 'AND (('.implode(' OR ', $children).') OR user_parent_ta = '.CUSER.')';

	getSort('user_id');
	$where = 'SELECT needy.user_id, needy.user_thumb, needy.user_fname, needy.user_lname, needy.user_orgname, needy.user_phone, needy.user_city, needy.deleted, CONCAT(users.user_fname, " ", users.user_lname) as kuratorius FROM needy LEFT JOIN users ON needy.user_parent = users.user_id WHERE user_type=0 '.$parent.' ORDER BY '.$getsort.$getorder;
	
	$sortby = array(0 => 'user_id', 2 => 'user_fname', 3 => 'user_lname', 4 => 'user_orgname', 5 => 'user_phone', 6 => 'user_city', 7 => 'kuratorius');
	
	formatTable( listData(false, false, pageNum(), $where), $tablefields, $tablehead, $sortby, true, 'nomargin');
	pagination(countData(false, false, $where));
	
?>