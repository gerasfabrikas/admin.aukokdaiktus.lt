<?php if($login->isUserLoggedIn() == false) return;  if(!isAdmin() and !(isCustom() and haveRight(8))) return;

if(isAction('salinti') and getParam()) :
	$row = getRow('pages', 'page_id = '.getParam());
	$act = $row['deleted'];
	$act = !$act;
	updateFieldWhere('pages', 'deleted', $act, 'page_id = '.getParam());

	redirect(0, getCurrentLink());
endif;

$tablefields = array(
		'page_id',
		'page_name',
		'page_published',
		array('converter' => array('page_id', 'getBlogPageEditLink')),
		array('convArray' => array(array('page_id', 'page_site'), 'getBlogLinks')),
		array('action' => array('page_id', 'salinti', '<i title="Šalinti/atnaujinti" class="fa fa-times"></i>', false)),
		'null',
	);
	$tablehead = array(
		'titles' => array(
		'ID',
		'Pavadinimas',
		'Publikuotas',
		'',
		'',
		'',
		'',
		),
		'columns' => array(
		'right fc-60',
		'left fc-verylong',
		'left fc-150',
		'left fc-18',
		'left fc-30',
		'left fc-16',
		'',
		),
		
	);


	getSort('page_id');
	$where = 'SELECT * FROM pages WHERE page_type = 1 ORDER BY '.$getsort.$getorder;

	$sortby = array(0 => 'page_id', 1 => 'page_name', 2 => 'page_modified');
	
	formatTable( listData(false, false, pageNum(), $where), $tablefields, $tablehead, $sortby, true, 'nomargin');
	pagination(countData(false, false, $where));

?>

<br><br>
<a href="?p=editblogpage"><i class="fa fa-asterisk"></i> Sukurti naujieną</a>
