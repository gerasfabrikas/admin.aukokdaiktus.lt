<?php if($login->isUserLoggedIn() == false) return; if(!isGridManager()) return;

if(!subpage()) return;

if(isAction('salinti') and getParam()) :
	
	$children = array();
	foreach(listData('users', 'user_acctype = 1 AND user_active = 1 AND user_parent = '.CUSER) as $child) $children[] = 'needy.user_parent = '.$child['user_id'];
	$parent = '(('.implode(' OR ', $children).') OR needy.user_parent_ta = '.CUSER.')';
	if(countData(false, false, "SELECT * FROM needs INNER JOIN needy ON needs.need_needy = needy.user_id WHERE needs.need_id = ".getParam()." AND ".$parent) == 0) {err('Neturite teisės keisti šio poreikio duomenis', 'red'); return;}

	$row = getRow('needs', 'need_id = '.getParam());
	$act = $row['deleted'];
	$act = !$act;
		
	if(isset($_POST['reason']) or $act == 0) :
		updateFieldWhere('needs', 'deleted', $act, 'need_id = '.getParam());
		
		if(isset($_POST['reason']) and ($_POST['reason'] == 1 or $_POST['reason'] == 2)) updateFieldWhere('needs', 'delreason', $_POST['reason'], 'need_id = '.getParam());

		if(isset($_POST['reason']) and ($_POST['reason'] == 1)) {
			updateFieldWhere('needs', 'need_full', 1, 'need_id = '.getParam());
			updateFieldWhere('needs', 'deleted', 0, 'need_id = '.getParam());
		}
		
		redirect(0, getCurrentLink());
		return;
	endif;
	?>
	<form class="simpleform" action="" method="POST">
		Pažymėkite šalinimo priežastį:<br><br> 
			<input checked="checked" name="reason" type="radio" value="1" />padovanota / padėta ne per sistemą<br>
			<input name="reason" type="radio" value="2" />nebereikalinga pagalba / nebereikalingas daiktas<br>
		<br><input type="submit" value="Siųsti" />
	</form>
	<?php
	return;
	
endif;

$tablefields = array(
		'need_id',
		'need_name',
		'cat_name',
		'user_fname',
		'user_lname',
		'user_orgname',
		'need_regdate',
		array('converter' => array('need_id', 'getNeedEditLink')),
		array('action' => array('need_id', 'salinti', '<i title="Šalinti/atnaujinti" class="fa fa-times"></i>', false)),
		'null',
	);
	$tablehead = array(
		'titles' => array(
		'ID',
		'Pavadinimas',
		'Kategorija',
		'<span title="Stokojančiojo vardas">Stokoj. vardas</span>',
		'<span title="Stokojančiojo pavardė">Pavardė</span>',
		'<span title="Stokojančioji organizacija">Organizacija</span>',
		'Modifikuotas',
		'',
		'',
		'',
		),
		'columns' => array(
		'right fc-60',
		'left fc-150',
		'left fc-150',
		'left fc-100',
		'left fc-100',
		'left fc-120',
		'left fc-dates',
		'left fc-16',
		'left fc-16',
		'',
		),
		
	);


	$children = array();
	foreach(listData('users', 'user_acctype = 1 AND user_active = 1 AND user_parent = '.CUSER) as $child) $children[] = 'needy.user_parent = '.$child['user_id'];
	$parent = 'AND (('.implode(' OR ', $children).') OR needy.user_parent_ta = '.CUSER.')';
	
	
	getSort('need_id');
	$where = 'SELECT need_id, need_name, cat_name, user_fname, user_lname, user_orgname, need_regdate, a.deleted AS deleted FROM (SELECT need_id, need_name, cat_name, need_type, need_needy, need_regdate, needs.deleted AS deleted FROM needs INNER JOIN cats ON needs.need_cat = cats.cat_id WHERE needs.need_type='.subpage().' AND needs.need_full=0) a INNER JOIN needy ON a.need_needy = needy.user_id WHERE a.need_type='.subpage().' '.$parent.' ORDER BY '.$getsort.$getorder;

	
	
	$sortby = array(0 => 'need_id', 1 => 'need_name', 2 => 'cat_name', 3 => 'user_fname', 4 => 'user_lname', 5 => 'user_orgname', 6 => 'need_regdate');
	
	formatTable( listData(false, false, pageNum(), $where), $tablefields, $tablehead, $sortby, true, 'nomargin');
	pagination(countData(false, false, $where));
?>