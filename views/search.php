<?php if($login->isUserLoggedIn() == false) return; if(!isAdmin() and !isManager() and !isGridManager()) return;



$tablefields = array(
		'obj_type',
		'obj_name',
		'obj_desc',
		array('convArray' => array(array('obj_type', 'obj_id'), 'getSearchLink')),
		'null',
	);
	$tablehead = array(
		'titles' => array(
		'Tipas',
		'Rezultatas',
		'Kita informacija',
		'',
		'',
		),
		'columns' => array(
		'left fc-150',
		'left fc-150',
		'left fc-300',
		'left fc-16',
		'',
		),
		
	);
	
	$term = (isset($_GET['srch']) ? $_GET['srch'] : '');
	if($term == '' or strlen($term) < 3) {err('Per trumpa paieškos užklausa'); return;}
	
	getSort('obj_id');
	$where = "
	SELECT user_id AS obj_id, 'Vartotojas' AS obj_type, CONCAT(user_fname, ' ', user_lname, ' ', user_orgname) AS obj_name, CONCAT('Paskyros vardas: ', user_name,', el. paštas: ', user_email, ', telefonas: ', user_phone) AS obj_desc FROM users WHERE user_name LIKE ('%$term%') OR user_fname LIKE ('%$term%') OR user_lname LIKE ('%$term%') OR user_orgname LIKE ('%$term%') OR user_phone LIKE ('%$term%') OR user_email LIKE ('%$term%')
	UNION
	SELECT user_id AS obj_id, 'Stokojantysis' AS obj_type, CONCAT(user_fname, ' ', user_lname, ' ', user_orgname) AS obj_name, CONCAT('El. paštas: ', user_email, ', telefonas: ', user_phone) AS obj_desc FROM needy WHERE user_fname LIKE ('%$term%') OR user_lname LIKE ('%$term%') OR user_orgname LIKE ('%$term%') OR user_phone LIKE ('%$term%') OR user_email LIKE ('%$term%')
	UNION
	SELECT need_id AS obj_id, 'Dovanotojas/geradarys' AS obj_type, CONCAT(user_fname, ' ', user_lname) AS obj_name, CONCAT('El. paštas: ', user_email, ', telefonas: ', user_phone) AS obj_desc FROM needs WHERE user_fname LIKE ('%$term%') OR user_lname LIKE ('%$term%') OR user_phone LIKE ('%$term%') OR user_email LIKE ('%$term%')
	UNION
	SELECT need_id AS obj_id, 'Daiktas/darbas' AS obj_type, need_name AS obj_name, need_desc AS obj_desc FROM needs WHERE need_name LIKE ('%$term%')
	";
	$sortby = false;
	
	formatTable( listData(false, false, pageNum(), $where), $tablefields, $tablehead, $sortby, true, 'nomargin');
	pagination(countData(false, false, $where));
	
?>
