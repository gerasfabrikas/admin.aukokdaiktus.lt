<?php if($login->isUserLoggedIn() == false) return;  if(!isAdmin()) return;

$catType = 3;

if( isset($_POST['editable']) && isset($_POST['editablefield']) && isset($_POST['editableid']) ) :
	$value = mysqli_real_escape_string($con, $_POST['editable']);
	$field = mysqli_real_escape_string($con, $_POST['editablefield']);
	$id = mysqli_real_escape_string($con, $_POST['editableid']);
	updateField('cats', $field, $value, 'cat_id', $id);
	redirect(0, $url = '?p='.page().(subpage() ? '&subp='.subpage() : '').(psl() ? '&page='.psl(): ''));
endif;

if(isAction('salinti') and getParam()) :
	$row = getRow('cats', 'cat_id = '.getParam());
	$act = $row['deleted'];
	$act = !$act;
	if($act == 1) : 
		if(isset($_POST['updatecats'])) :
			$updcat = mysqli_real_escape_string($con, $_POST['updatecats']);
			updateFieldWhere('needs', 'need_cat', $updcat, 'need_cat = '.getParam());
			updateFieldWhere('cats', 'deleted', $act, 'cat_id = '.getParam());
			redirect(0, $url = '?p='.page().(subpage() ? '&subp='.subpage() : '').(psl() ? '&page='.psl(): ''));
		else : ?>
		<form action="" method="post">
			<i class="fa fa-exclamation-triangle "></i> Pasirinkite kategoriją, kuri bus priskirta ištrintos kategorijos įrašams
			<select name="updatecats">
				<?php foreach(listData('cats', 'cat_type = '.$catType.' AND deleted = 0') as $op) if(getParam() != $op[0]) echo '<option value='.$op[0].'>'.$op[1].'</option>'; ?>
			</select>
			<input type="submit" value="Priskirti" />
		</form><br><br><?php
		endif;
	else :
		updateFieldWhere('cats', 'deleted', $act, 'cat_id = '.getParam());
		redirect(0, $url = '?p='.page().(subpage() ? '&subp='.subpage() : '').(psl() ? '&page='.psl(): ''));
	endif;
endif;

if(isset($_POST['addoption'])) :
	if(strlen($_POST['option1']) > 2) :
	insertRow('cats', 'cat_name, cat_type', "'".$_POST['option1']."', ".$catType);
	redirect(0, $url = '?p='.page().(subpage() ? '&subp='.subpage() : '').(psl() ? '&page='.psl(): ''));
	else : err('Įveskite kategorijos vardą', 'red');
	endif;
endif;

?>
<form action="" method="post">
Įtraukti naują kategoriją: kategorijos vardas <input type="text" name="option1" value="" />
<input type="submit" name="addoption" value="Įtraukti" />
</form>
<?php


$tablefields = array(
		'cat_id',
		array('editable' => array('cat_name', 'cat_id', 'fc-verylong', '')), 
		array('action' => array('cat_id', 'salinti', '<i title="Šalinti" class="fa fa-times"></i>', false)),
		'null',
	);
	$tablehead = array(
		'titles' => array(
		'ID',
		'Kategorijos vardas',
		'',
		'',
		),
		'columns' => array(
		'right fc-75',
		'left',
		'left fc-16',
		'',
		),
		
	);

	$sortby = false;
	$where = 'SELECT * FROM cats WHERE cat_type = '.$catType.' ORDER BY cat_id DESC';
	
	formatTable( listData(false, false, pageNum(), $where), $tablefields, $tablehead, $sortby, true, 'nomargin');
	pagination(countData(false, false, $where));
	
?>