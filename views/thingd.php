<?php if($login->isUserLoggedIn() == false) return;

if(isCustom() and haveRight(2)) :
	$thingName = (subpage() == 1 ? 'Geradarys' : 'Dovanotojas');
	$thingLabel = (subpage() == 1 ? 'Darbas' : 'Daiktas');

	$tablefields = array(
		array('convArray' => array(array('fname', 'lname', 'fnamer', 'lnamer', 'orgname', 'uid'), 'getThingdName')),
		array('convArray' => array(array('phone', 'phoner'), 'getDoubleString')),
		array('convArray' => array(array('email', 'emailr'), 'getDoubleString')),
		'descr',
		//array('converter' => array('nid', 'getNeedEditLinkGift')),
		'null',
	);
	$tablehead = array(
		'titles' => array(
		$thingName,
		'Telefonas',
		'El. paštas',
		$thingLabel,
		//'',
		'',
		),
		'columns' => array(
		'left fc-150',
		'left fc-150',
		'left fc-150',
		'left fc-300',
		//'left fc-16',
		'',
		),
		
	);


	getSort('need_fulldate');
	$where = 'SELECT
				needs.user_fname AS fname,
				needs.user_lname AS lname,
				users.user_fname AS fnamer,
				users.user_lname AS lnamer,
				users.user_orgname AS orgname,
				needs.need_full_user AS uid,
				needs.user_email AS email,
				users.user_email AS emailr,
				needs.user_phone AS phone,
				users.user_phone AS phoner,
				needs.need_full_desc AS descr,
				needs.need_id AS nid
				FROM needs LEFT JOIN users ON needs.need_full_user = users.user_id WHERE needs.need_type='.subpage().' AND needs.need_full = 1 ORDER BY '.$getsort.$getorder;
				
	
	$sortby = array(/*0 => 'need_id', 1 => 'need_name', 2 => 'cat_name', 3 => 'user_fname', 4 => 'user_lname', 5 => 'need_regdate'*/);
	
	formatTable( listData(false, false, pageNum(), $where), $tablefields, $tablehead, $sortby, true, 'nomargin');
	pagination(countData(false, false, $where));

endif;

if(!isAdmin()) return;

if(!subpage()) return;

$thingName = (subpage() == 1 ? 'Geradarys' : 'Dovanotojas');
$thingLabel = (subpage() == 1 ? 'Darbas' : 'Daiktas');

$tablefields = array(
		array('convArray' => array(array('fname', 'lname', 'fnamer', 'lnamer', 'orgname', 'uid'), 'getThingdName')),
		array('convArray' => array(array('phone', 'phoner'), 'getDoubleString')),
		array('convArray' => array(array('email', 'emailr'), 'getDoubleString')),
		'descr',
		array('converter' => array('nid', 'getNeedEditLinkGift')),
		'null',
	);
	$tablehead = array(
		'titles' => array(
		$thingName,
		'Telefonas',
		'El. paštas',
		$thingLabel,
		'',
		'',
		),
		'columns' => array(
		'left fc-150',
		'left fc-150',
		'left fc-150',
		'left fc-300',
		'left fc-16',
		'',
		),
		
	);


	getSort('need_fulldate');
	$where = 'SELECT
				needs.user_fname AS fname,
				needs.user_lname AS lname,
				users.user_fname AS fnamer,
				users.user_lname AS lnamer,
				users.user_orgname AS orgname,
				needs.need_full_user AS uid,
				needs.user_email AS email,
				users.user_email AS emailr,
				needs.user_phone AS phone,
				users.user_phone AS phoner,
				needs.need_full_desc AS descr,
				needs.need_id AS nid
				FROM needs LEFT JOIN users ON needs.need_full_user = users.user_id WHERE needs.need_type='.subpage().' AND needs.need_full = 1 ORDER BY '.$getsort.$getorder;
				
	
	$sortby = array(/*0 => 'need_id', 1 => 'need_name', 2 => 'cat_name', 3 => 'user_fname', 4 => 'user_lname', 5 => 'need_regdate'*/);
	
	formatTable( listData(false, false, pageNum(), $where), $tablefields, $tablehead, $sortby, true, 'nomargin');
	pagination(countData(false, false, $where));

	$poreikis = array(1 => 'darbų', 2 => 'daiktų');
?>
