<?php if($login->isUserLoggedIn() == false) return;

if(isCustom() and haveRight(0)) :

$tablefields = $sortby = array();
$tablehead = array( 'titles' => array(), 'columns' => array() );

$rg = 0; if(haveRight($rg, 2)) {$tablefields[$rg] = 'need_id'; $tablehead['titles'][$rg] = 'ID'; $tablehead['columns'][$rg] = 'right fc-60'; $sortby[$rg] = 'need_id';}

$rg = 1; if(haveRight($rg, 2)) {$tablefields[11] = 'user_fname'; $tablehead['titles'][11] = '<span title="Stokojančiojo vardas">Stokoj. vardas</span>'; $tablehead['columns'][11] = 'left fc-100'; $sortby[11] = 'user_fname';}

$rg = 1; if(haveRight($rg, 2)) {$tablefields[12] = 'user_lname'; $tablehead['titles'][12] = '<span title="Stokojančiojo pavardė">Pavardė</span>'; $tablehead['columns'][12] = 'left fc-100'; $sortby[12] = 'user_lname';}

$rg = 1; if(haveRight($rg, 2)) {$tablefields[13] = 'user_orgname'; $tablehead['titles'][13] = '<span title="Stokojančioji organizacija">Organizacija</span>'; $tablehead['columns'][13] = 'left fc-100'; $sortby[13] = 'user_orgname';}

$rg = 2; if(haveRight($rg, 2)) {$tablefields[$rg] = 'cat_name'; $tablehead['titles'][$rg] = 'Kategorija'; $tablehead['columns'][$rg] = 'left fc-100'; $sortby[$rg] = 'cat_name';}

$rg = 3; if(haveRight($rg, 2)) {$tablefields[$rg] = 'need_name'; $tablehead['titles'][$rg] = 'Pavadinimas'; $tablehead['columns'][$rg] = 'left fc-100'; $sortby[$rg] = 'need_name';}

$rg = 4; if(haveRight($rg, 2)) {$tablefields[$rg] = 'need_desc'; $tablehead['titles'][$rg] = 'Aprašymas'; $tablehead['columns'][$rg] = 'left fc-100'; $sortby[$rg] = 'need_desc';}

$rg = 5; if(haveRight($rg, 2)) {$tablefields[$rg] = 'need_regdate'; $tablehead['titles'][$rg] = 'Modifikuotas'; $tablehead['columns'][$rg] = 'left fc-dates'; $sortby[$rg] = 'need_regdate';}

$rg = 6; if(haveRight($rg, 2)) {$tablefields[$rg] = 'need_expires'; $tablehead['titles'][$rg] = 'Galioja iki'; $tablehead['columns'][$rg] = 'left fc-dates'; $sortby[$rg] = 'need_expires';}

	$parent = '';	
	
	getSort('need_id');
	$where = 'SELECT need_id, need_name, cat_name, user_fname, user_lname, user_orgname, need_expires, need_desc, need_regdate, a.deleted AS deleted FROM (SELECT need_id, need_name, cat_name, need_type, need_needy, need_expires, need_desc, need_regdate, needs.deleted AS deleted FROM needs LEFT JOIN cats ON needs.need_cat = cats.cat_id WHERE needs.need_type='.subpage().' AND needs.need_full=0) a LEFT JOIN needy ON a.need_needy = needy.user_id WHERE a.need_type='.subpage().' '.$parent.' ORDER BY '.$getsort.$getorder;
	
	formatTable( listData(false, false, pageNum(), $where), $tablefields, $tablehead, $sortby, true, 'nomargin');
	pagination(countData(false, false, $where));

endif;

// customs end

if(!isAdmin() and !isManager() and !isGridManager()) return;

if(isAdmin() and isset($_GET['regenerate'])) :
	
	foreach(listData('cats', '1') as $key => $data) :
		updateField('cats', 'cat_slug', prettyslug($data['cat_name']), 'cat_id', $data['cat_id']);
	endforeach;

	redirect(0, '/index.php?p=need&subp=1');
endif;

if(!subpage()) return;

if(isAction('salinti') and getParam()) :
	if(isManager() and countData(false, false, "SELECT * FROM needs INNER JOIN needy ON needs.need_needy = needy.user_id WHERE needs.need_id = ".getParam()." AND needy.user_parent = ".CUSER) == 0) {err('Neturite teisės keisti šio poreikio duomenis', 'red'); return;}
	
	if(isGridManager()) :
		$children = array();
		foreach(listData('users', 'user_acctype = 1 AND user_active = 1 AND user_parent = '.CUSER) as $child) $children[] = 'needy.user_parent = '.$child['user_id'];
		$parent = '(('.implode(' OR ', $children).') OR needy.user_parent_ta = '.CUSER.')';
		if(countData(false, false, "SELECT * FROM needs INNER JOIN needy ON needs.need_needy = needy.user_id WHERE needs.need_id = ".getParam()." AND ".$parent) == 0) {err('Neturite teisės keisti šio poreikio duomenis', 'red'); return;}
	endif;

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

$tablefields = $sortby = array();
$tablehead = array( 'titles' => array(), 'columns' => array() );

$rg = 0; if(haveRight($rg, 2)) {$tablefields[$rg] = 'need_id'; $tablehead['titles'][$rg] = 'ID'; $tablehead['columns'][$rg] = 'right fc-60'; $sortby[$rg] = 'need_id';}

$rg = 1; if(haveRight($rg, 2)) {$tablefields[11] = 'user_fname'; $tablehead['titles'][11] = '<span title="Stokojančiojo vardas">Stokoj. vardas</span>'; $tablehead['columns'][11] = 'left fc-100'; $sortby[11] = 'user_fname';}

$rg = 1; if(haveRight($rg, 2)) {$tablefields[12] = 'user_lname'; $tablehead['titles'][12] = '<span title="Stokojančiojo pavardė">Pavardė</span>'; $tablehead['columns'][12] = 'left fc-100'; $sortby[12] = 'user_lname';}

$rg = 1; if(haveRight($rg, 2)) {$tablefields[13] = 'user_orgname'; $tablehead['titles'][13] = '<span title="Stokojančioji organizacija">Organizacija</span>'; $tablehead['columns'][13] = 'left fc-100'; $sortby[13] = 'user_orgname';}

$rg = 2; if(haveRight($rg, 2)) {$tablefields[$rg] = 'cat_name'; $tablehead['titles'][$rg] = 'Kategorija'; $tablehead['columns'][$rg] = 'left fc-100'; $sortby[$rg] = 'cat_name';}

$rg = 3; if(haveRight($rg, 2)) {$tablefields[$rg] = 'need_name'; $tablehead['titles'][$rg] = 'Pavadinimas'; $tablehead['columns'][$rg] = 'left fc-100'; $sortby[$rg] = 'need_name';}

$rg = 4; if(haveRight($rg, 2)) {$tablefields[$rg] = 'need_desc'; $tablehead['titles'][$rg] = 'Aprašymas'; $tablehead['columns'][$rg] = 'left fc-100'; $sortby[$rg] = 'need_desc';}

$rg = 5; if(haveRight($rg, 2)) {$tablefields[$rg] = 'need_regdate'; $tablehead['titles'][$rg] = 'Modifikuotas'; $tablehead['columns'][$rg] = 'left fc-dates'; $sortby[$rg] = 'need_regdate';}

$rg = 6; if(haveRight($rg, 2)) {$tablefields[$rg] = 'need_expires'; $tablehead['titles'][$rg] = 'Galioja iki'; $tablehead['columns'][$rg] = 'left fc-dates'; $sortby[$rg] = 'need_expires';}

/* Admin specific */

$tablefields[7] = array('converter' => array('need_id', 'getNeedEditLink')); $tablehead['titles'][7] = ''; $tablehead['columns'][7] = 'left fc-16';

$tablefields[8] = array('action' => array('need_id', 'salinti', '<i title="Šalinti/atnaujinti" class="fa fa-times"></i>', false)); $tablehead['titles'][8] = ''; $tablehead['columns'][8] = 'left fc-16';
/*
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
*/
	if(isAdmin()) : $parent = '';
	elseif(isManager() or isGridManager()) : $parent = 'AND needy.user_parent = '.CUSER;
	/*elseif(isGridManager()) :
		$children = array();
		foreach(listData('users', 'user_acctype = 1 AND user_active = 1 AND user_parent = '.CUSER) as $child) $children[] = 'needy.user_parent = '.$child['user_id'];
		$parent = 'AND (('.implode(' OR ', $children).') OR needy.user_parent_ta = '.CUSER.')';*/
	endif;
	
	
	getSort('need_id');
	$where = 'SELECT need_id, need_name, cat_name, user_fname, user_lname, user_orgname, need_expires, need_desc, need_regdate, a.deleted AS deleted FROM (SELECT need_id, need_name, cat_name, need_type, need_needy, need_expires, need_desc, need_regdate, needs.deleted AS deleted FROM needs LEFT JOIN cats ON needs.need_cat = cats.cat_id WHERE needs.need_type='.subpage().' AND needs.need_full=0) a LEFT JOIN needy ON a.need_needy = needy.user_id WHERE a.need_type='.subpage().' '.$parent.' ORDER BY '.$getsort.$getorder;

	
	/*
	$sortby = array(0 => 'need_id', 1 => 'need_name', 2 => 'cat_name', 3 => 'user_fname', 4 => 'user_lname', 5 => 'user_orgname', 6 => 'need_regdate');*/
	
	formatTable( listData(false, false, pageNum(), $where), $tablefields, $tablehead, $sortby, true, 'nomargin');
	pagination(countData(false, false, $where));

	$poreikis = array(1 => 'darbo', 2 => 'daikto');
?>

<br><br>
<a href="?p=editneed&type=<?php echo subpage(); ?>&subp=<?php echo subpage(); ?>"><i class="fa fa-asterisk"></i> Sukurti <?php echo $poreikis[subpage()]; ?> poreikį</a>
<?php if(isAdmin()) : ?> &middot;
<a href="?p=catneed<?php echo subpage(); ?>&subp=<?php echo subpage(); ?>"><i class="fa fa-tags"></i> <?php echo ucfirst($poreikis[subpage()]); ?> poreikio kategorijos</a> &middot;
<a href="?p=need&subp=<?php echo subpage(); ?>&regenerate"><i class="fa fa-cogs"></i> Regeneruoti santrumpas</a>
<?php endif; ?>
