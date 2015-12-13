<?php if($login->isUserLoggedIn() == false) return; if(!isGridManager() and !isManager()) return;

$tablefields = array(
		array('converter' => array('user_thumb', 'getThumb')),
		'user_fname',
		'user_lname',
		'user_email',
		'user_phone',
		array('converter' => array('user_city', 'getCountry')),
		'null',
	);
	$tablehead = array(
		'titles' => array(
		'',
		'Vardas',
		'Pavardė',
		'El. paštas',
		'Telefonas',
		'Savivaldybė',
		'',
		),
		'columns' => array(
		'left fc-30',
		'left fc-75',
		'left fc-100',
		'left fc-200',
		'left fc-100',
		'left fc-125',
		'',
		),
		
	);
	
	$subpage = ((subpage() == 1 or subpage() == 2) ? "user_acctype =".subpage() : "(user_acctype = 1 OR user_acctype = 2)");
	
	getSort('user_id');
	$where = "SELECT * FROM users WHERE $subpage AND user_active = 1 ORDER BY ".$getsort.$getorder;

	$sortby = array(
		1 => 'user_fname',
		2 => 'user_lname',
		3 => 'user_email',
		4 => 'user_phone',
		5 => 'user_city',
		);
	
	formatTable( listData(false, false, pageNum(), $where), $tablefields, $tablehead, $sortby, true, 'nomargin');
	pagination(countData(false, false, $where));
	
?>
<br><br>
<form action="" method="GET">
<input type="hidden" name="p" value="colleagues" />
<select name="subp">
	<option value="0"<?php if(subpage() != 2 and subpage() != 1) echo 'selected="selected"';?>>Tinklo atstovai ir kuratoriai</option>
	<option value="2"<?php if(subpage() == 2) echo 'selected="selected"';?>>Tinklo atstovai</option>
	<option value="1"<?php if(subpage() == 1) echo 'selected="selected"';?>>Kuratoriai</option>
	<input type="submit" value="Rodyti" />
</select>
</form>