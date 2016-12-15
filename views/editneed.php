<?php if($login->isUserLoggedIn() == false) return; if(!isAdmin() and !isManager() and !isGridManager()) return;

if(!isset($_GET['need']) and !isset($_GET['type'])) return;

$need_type = ( (isset($_GET['type']) and ($_GET['type'] == 1 or $_GET['type'] == 2))  ? $_GET['type'] : 1);
if($need_type == 1) $need_cat_type = 3;
if($need_type == 2) $need_cat_type = 4;

if(isAdmin()) : $parent = '';
elseif(isManager() or isGridManager()) : $parent = 'AND user_parent = '.CUSER;
/*elseif(isGridManager()) :
	$children = array();
	foreach(listData('users', 'user_acctype = 1 AND user_active = 1 AND user_parent = '.CUSER) as $child) $children[] = 'user_parent = '.$child['user_id'];
	$parent = 'AND (('.implode(' OR ', $children).') OR user_parent_ta = '.CUSER.')';*/
endif;

if(!isset($_GET['need']) or $_GET['need'] == 0) :

	if(isset($_POST['newneed'])) :
			$need_needy = (isset($_POST['need_needy']) ? mysqli_real_escape_string($con, $_POST['need_needy']) : 0);
			$need_cat = (isset($_POST['need_cat']) ? mysqli_real_escape_string($con, $_POST['need_cat']) : 0);
			$need_subcat = (isset($_POST['need_subcat']) ? mysqli_real_escape_string($con, $_POST['need_subcat']) : 0);
			$need_name = (isset($_POST['need_name']) ? mysqli_real_escape_string($con, $_POST['need_name']) : '');
			$need_desc = (isset($_POST['need_desc']) ? mysqli_real_escape_string($con, $_POST['need_desc']) : '');
			$need_expires = (isset($_POST['need_expires']) ? mysqli_real_escape_string($con, $_POST['need_expires']) : 0);
			$need_regdate = date('Y-m-d H:i:s');
			$need_author = CUSER;
		
			if(
				$need_needy == '' or 
				$need_needy <= 0 or
				$need_cat == '' or
				$need_cat <= 0 or
				($need_cat_type == 4 and $need_subcat == '') or
				($need_cat_type == 4 and $need_subcat <= 0) or
				$need_name == '' or
				$need_desc == '' or
				$need_expires == ''
			) :
				err("Užpildykite visus reikalaujamus laukelius", 'red');
			elseif(strtotime($need_expires) === false) :
				err("Netinkamas galiojimo laikas", 'red');
			elseif(strtotime($need_expires) < strtotime('+1 minute')) :
				err("Šis galiojimo laikas jau praėjo", 'red');
			else :
				$id = insertRow('needs',
				'need_needy, need_cat, need_subcat, need_name, need_desc, need_regdate, need_expires, need_type, need_author',
				"$need_needy, $need_cat, $need_subcat, '$need_name', '$need_desc', '$need_regdate', '$need_expires', $need_type, $need_author", false
				);
				
				if($id) :
					redirect(0, '?p='.page().'&type='.$need_type.'&subp='.subpage().'&need='.$id);
				else : err('Poreikis nesukurtas', 'red');
				endif;
			endif;
	endif;
	
	?>
	<form action="" method="post">
		<label>Stokojantis<span class="reqfield">*</span></label>
		<select name="need_needy">
            <option value="">- pasirinkti -</option>
            <?php
			    $ops = listData('needy', "deleted = 0 $parent ORDER BY user_lname, user_orgname");
			    foreach($ops as $op) {
			        echo '<option value='.$op[0].'>'.$op['user_lname'].' '.$op['user_fname'].' '.$op['user_orgname'].'</option>';
                }
            ?>
		</select>
		<br>
		
		<label>Kategorija<span class="reqfield">*</span></label>
		<select name="need_cat">
            <option value="0">- pasirinkti -</option>
            <?php
			    $ops = listData('cats', 'cat_type = '.$need_cat_type.' AND deleted = 0 AND cat_level = 0');
    			foreach($ops as $op) {
                    echo '<option value='.$op[0].'>'.$op[1].'</option>';
                }
            ?>
		</select>
		<br>
		
		<?php if($need_type == 2) : ?>
		<label>Subkategorija<span class="reqfield">*</span></label>
		<select name="need_subcat"></select>
		<br>
		<?php endif; ?>
		
		<label>Pavadinimas<span class="reqfield">*</span></label>
        <input type="text" value="" name="need_name" maxlength="256" required="required" /><br>
		
		<label>Aprašymas<span class="reqfield">*</span></label>
        <textarea name="need_desc" required="required"></textarea><br>
		
		<label>Galiojimo laikas<span class="reqfield">*</span></label> 
		<select name="need_expires">
			<option value="<?php echo date('Y-m-d H:i:s', strtotime('+2 weeks')); ?>">2 savaitės</option>
			<option value="<?php echo date('Y-m-d H:i:s', strtotime('+1 month')); ?>">1 mėnuo</option>
			<option value="<?php echo date('Y-m-d H:i:s', strtotime('+3 months')); ?>">3 mėnesiai</option>
			<option value="<?php echo date('Y-m-d H:i:s', strtotime('+6 months')); ?>">6 mėnesiai</option>
			<option value="<?php echo date('Y-m-d H:i:s', strtotime('+1 year')); ?>">1 metai</option>
		</select>
		<br>
		
		<input type="submit" value="Sukurti" name="newneed" />
	</form>
	<?php

	return;
endif;

$us = $_GET['need'];

if(countData('needs', "need_id = '$us'") == 0) {err('Toks poreikis neegzistuoja', 'red'); return;}
if(isManager() and countData(false, false, "SELECT * FROM needs INNER JOIN needy ON needs.need_needy = needy.user_id WHERE needs.need_id = $us AND needy.user_parent = ".CUSER) == 0) {err('Neturite teisės keisti šio poreikio duomenis', 'red'); return;}

if(isGridManager()) :
	$children = array();
	foreach(listData('users', 'user_acctype = 1 AND user_active = 1 AND user_parent = '.CUSER) as $child) $children[] = 'needy.user_parent = '.$child['user_id'];
	$parent2 = '(('.implode(' OR ', $children).') OR needy.user_parent_ta = '.CUSER.')';
	if(countData(false, false, "SELECT * FROM needs INNER JOIN needy ON needs.need_needy = needy.user_id WHERE needs.need_id = $us AND ".$parent2) == 0) {err('Neturite teisės keisti šio poreikio duomenis', 'red'); return;}
endif;

// Edit

$usermeta = getRow('needs', 'need_id = '.$us);

// Title
echo '<div class="edit_header_group">';
echo '<div class="name single">Poreikis: '.$usermeta['need_name'].'</div>';
echo 'Modifikavimo data: '.$usermeta['need_regdate'];
echo '</div>';

$need_type = $usermeta['need_type'];
if($need_type == 1) $need_cat_type = 3;
if($need_type == 2) $need_cat_type = 4;

$needy = getRow('needy', 'user_id = '.$usermeta['need_needy']);
if(empty($needy)) err('Informacinė sistema duomenų bazėje nerado nurodyto stokojančiojo. Skelbimas nebus publikuojamas svetainėje. Prašome nurodyti stokojantįjį iš naujo', 'red');


$options = array();

$stokojantys = array();
$ops = listData('needy', "deleted = 0 $parent ORDER BY user_lname");

foreach($ops as $op) $stokojantys[$op[0]] = $op['user_lname'].' '.$op['user_fname'].' '.$op['user_orgname'];

$options['fields']['need_needy'] = array('Stokojantis', 'inputtype' => 'drop', 'drops' => $stokojantys, 'required' => true);

$kats = array();
$ops = listData('cats', 'cat_type = '.$need_cat_type.' AND deleted = 0 AND cat_level = 0');
foreach($ops as $op) $kats[$op[0]] = $op['cat_name'];

$options['fields']['need_cat'] = array('Kategorija', 'inputtype' => 'drop', 'drops' => $kats, 'required' => true);

if($usermeta['need_type'] == 2) :
	$subkats = array();
	$ops = listData('cats', 'cat_type = '.$need_cat_type.' AND deleted = 0 AND cat_level = 1 AND cat_parent = '.$usermeta['need_subcat']);
	foreach($ops as $op) $subkats[$op[0]] = $op['cat_name'];
	$options['fields']['need_subcat'] = array('Subkategorija', 'inputtype' => 'drop', 'drops' => $subkats, 'required' => true);
endif;

$options['fields']['need_name'] = array('Pavadinimas', 'required' => true);
$options['fields']['need_desc'] = array('Aprašymas', 'inputtype' => 'textarea', 'required' => true);
$options['fields']['need_expires'] = array('Galiojimo laikas', 'required' => true);

updateUsermeta($options, $us, 'needs', 'need_id');

if(isset($_POST['updateUsermeta'])) updateFieldWhere('needs', 'need_regdate', date('Y-m-d H:i:s'), 'need_id = '.$us);
?>
