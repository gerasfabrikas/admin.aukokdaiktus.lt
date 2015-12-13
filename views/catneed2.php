<?php if($login->isUserLoggedIn() == false) return;  if(!isAdmin()) return;

$catType = 4;

if( isset($_POST['editable']) && isset($_POST['cat']) && isset($_POST['editableid']) ) :
	$name = mysqli_real_escape_string($con, $_POST['editable']);
	$par = mysqli_real_escape_string($con, $_POST['cat']);
	$id = mysqli_real_escape_string($con, $_POST['editableid']);
	updateField('cats', 'cat_name', $name, 'cat_id', $id);
	updateField('cats', 'cat_parent', $par, 'cat_id', $id);
	redirect(0, $url = '?p='.page().(subpage() ? '&subp='.subpage() : '').(psl() ? '&page='.psl(): ''));
endif;

if(isAction('salinti') and getParam()) :

	$row = getRow('cats', 'cat_id = '.getParam());
	$act = $row['deleted'];
	$act = !$act;
	$level = $row['cat_level'];
	
	if($act == 1) : 
		if(isset($_POST['need_cat2']) and isset($_POST['need_subcat']) and $_POST['need_subcat'] > 0 and $_POST['need_cat2'] > 0) :
			$cat = mysqli_real_escape_string($con, $_POST['need_cat2']);
			$subcat = mysqli_real_escape_string($con, $_POST['need_subcat']);
			if($level == 1) :
				mysqli_query($con, "UPDATE needs SET need_cat = $cat, need_subcat = $subcat WHERE need_subcat = ".getParam());
			elseif($level == 0) :
				mysqli_query($con, "UPDATE needs SET need_cat = $cat, need_subcat = $subcat WHERE need_cat = ".getParam());
			endif;
			updateFieldWhere('cats', 'deleted', $act, 'cat_id = '.getParam());
			redirect(0, $url = '?p='.page().(subpage() ? '&subp='.subpage() : '').(psl() ? '&page='.psl(): ''));
		else : ?>
		<form action="" method="post">
			<i class="fa fa-exclamation-triangle "></i> Pasirinkite subkategoriją, kuri bus priskirta ištrintos kategorijos įrašams
			<select name="need_cat2" data-exclude="<?php echo getParam(); ?>">
				<option value="0"></option>
				<?php foreach(listData('cats', 'cat_type = '.$catType.' AND cat_level = 0 AND deleted = 0') as $op) if(getParam() != $op[0]) echo '<option value='.$op[0].'>'.$op[1].'</option>'; ?>
			</select>
			<select name="need_subcat"></select>
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
	$cat_level = ( (isset($_POST['option2']) and $_POST['option2'] > 0) ? 1 : 0);
	$cat_parent = ( isset($_POST['option2']) ? $_POST['option2'] : 0);
	insertRow('cats', 'cat_name, cat_type, cat_level, cat_parent', "'".$_POST['option1']."', ".$catType.", $cat_level, $cat_parent");
	redirect(0, $url = '?p='.page().(subpage() ? '&subp='.subpage() : '').(psl() ? '&page='.psl(): ''));
	else : err('Įveskite kategorijos vardą', 'red');
	endif;
endif;

?>
<form action="" method="post">
Įtraukti naują kategoriją: kategorijos vardas <input type="text" name="option1" value="" />
Tėvinė kategorija <select name="option2"><option value="0">— Neturi —</option><?php foreach(listData('cats', 'cat_type = '.$catType.' AND deleted = 0 AND cat_level = 0') as $op) echo '<option value='.$op[0].'>'.$op[1].'</option>'; ?></select>
<input type="submit" name="addoption" value="Įtraukti" />
</form>

<form action="" method="post" id="edit_subc" class="hide">
	<input name="editableid" type="hidden" value="" maxlength="128">
	<input name="editable" type="text" value="" maxlength="128">
	Tėvinė kategorija
	<select name="cat">
		<option value="0">— Neturi —</option>
		<?php foreach(listData('cats', 'cat_type = '.$catType.' AND deleted = 0 AND cat_level = 0') as $op) echo '<option value='.$op[0].'>'.$op[1].'</option>'; ?>
	</select>
<input type="submit" name="editoption" value="Išsaugoti" />
</form>
<?php



$where = 'SELECT * FROM cats WHERE cat_type = '.$catType.' AND cat_level = 0 ORDER BY cat_name ASC';
$parents = listData(false, false, false, $where);

echo '<ul>';

foreach($parents as $parent) :
	echo '<li class="'.($parent['deleted'] == 1 ? 'unmarked' : '').'">';
	echo '<span class="editablelistitem" data-parent="0" data-editsubcid="'.$parent['cat_id'].'">'.$parent['cat_name'].'</span> <a href="?p='.page().'&veiksmas=salinti&param='.$parent['cat_id'].'"><i title="Trinti" class="fa fa-times"></i></a>';
	
	$where = 'SELECT * FROM cats WHERE cat_type = '.$catType.' AND cat_level = 1 AND cat_parent='.$parent['cat_id'].' ORDER BY cat_name ASC';
	$children = listData(false, false, false, $where);
	echo '<ul>';
	foreach($children as $child) :
		echo '<li class="'.($child['deleted'] == 1 ? 'unmarked' : '').'">';
		echo '<span class="editablelistitem" data-parent="'.$child['cat_parent'].'" data-editsubcid="'.$child['cat_id'].'">'.$child['cat_name'].'</span> <a href="?p='.page().'&veiksmas=salinti&param='.$child['cat_id'].'"><i title="Trinti" class="fa fa-times"></i></a>';
		echo '</li>';
	endforeach;
	echo '</ul>';
	
	echo '</li>';
endforeach;
echo '</ul>';
	
?>