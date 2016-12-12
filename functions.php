<?php
require_once('config/config.php');

// Web core class
require_once('classes' . DIRECTORY_SEPARATOR . 'Core.php');


$con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (mysqli_connect_errno($con)) {echo "Failed to connect to MySQL: " . mysqli_connect_error();}
mysqli_set_charset($con, "utf8");

function err($text, $color = 'yellow', $class = '') {
	echo '<div class="err '.$color.' '.$class.'">'.$text.'</div>';
}

function redirect($time = 0, $url = false) {
	echo '<meta http-equiv="refresh" content="'.$time.';'.($url ? 'URL='.$url : NULL).'">';
}

function getCurrentUser() {
	global $login;
	global $con;
	if (isset($login) and $login->isUserLoggedIn() == true and !empty($_SESSION['user_name'])) :
		$row = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM users WHERE user_name = '".$_SESSION['user_name']."'"));
		return $row;
	endif;
	return false;
}
$user = getCurrentUser();
define("CUSER", $user['user_id']);

function isAction($action) {
	if(isset($_GET['veiksmas']) && $_GET['veiksmas'] == $action) return true;
	return false;
}

function page() {
	if(isset($_GET['p'])) return $_GET['p'];
	return 'home';
}

function subpage() {
	if(isset($_GET['subp'])) return $_GET['subp'];
	return false;
}

function psl() {
	if(isset($_GET['page'])) return $_GET['page'];
	return false;
}

function getAction() {
	if(isset($_GET['action'])) return $_GET['action'];
	return false;
}

function getParam() {
	if(isset($_GET['param'])) return $_GET['param'];
	return false;
}

function isHome() {
	if(!isset($_GET['p'])) return true;
	return false;
}

function doLevelsMatch($level) {
	$id = CUSER;
	global $con;
	if($id) :
		$q = "SELECT user_acctype, user_temp_acctype FROM users WHERE user_id = $id";
		$res = mysqli_query($con, $q);
		if($res) :
			$res = mysqli_fetch_array($res);
			if($res['user_acctype'] == $level and $res['user_temp_acctype'] == 0) return true;
			if($res['user_acctype'] == 3 and $res['user_temp_acctype'] == $level) return true;
		endif;
	endif;
	return false;
}

function isSponsor()	{return doLevelsMatch(0);}
function isManager()	{return doLevelsMatch(1);}
function isGridManager()	{return doLevelsMatch(2);}
function isAdmin()	{return doLevelsMatch(3);}

function isCustom() {
	$id = CUSER;
	global $con;
	if($id) :
		$q = "SELECT user_acctype, user_temp_acctype FROM users WHERE user_id = $id";
		$res = mysqli_query($con, $q);
		if($res) :
			$res = mysqli_fetch_array($res);
			if($res['user_acctype'] > 3 and $res['user_temp_acctype'] == 0) return true;
			if($res['user_acctype'] == 3 and $res['user_temp_acctype'] > 3) return true;
		endif;
	endif;
	return false;
}

function haveRight($right, $rtype = '') {
	$id = CUSER;
	global $con;
	if($id) :
		$q = "SELECT user_acctype, user_temp_acctype FROM users WHERE user_id = $id";
		$res = mysqli_query($con, $q);
		if($res) :
			$res = mysqli_fetch_array($res);
			$acctype = $res['user_acctype'];
			$tempacctype = $res['user_temp_acctype'];
			$q = "SELECT rights".$rtype." FROM acctypes WHERE acctype = $acctype";
			$res = mysqli_query($con, $q);
			if($tempacctype == 0) :
				if($res) :
					$res = mysqli_fetch_array($res);
					$code = $res['rights'.$rtype];
					if($code[$right] == 1) return true;
				endif;
			endif;
			if($acctype == 3 and $tempacctype > 0) :
				$q = "SELECT rights".$rtype." FROM acctypes WHERE acctype = $tempacctype";
				$res = mysqli_query($con, $q);
				if($res) :
					$res = mysqli_fetch_array($res);
					$code = $res['rights'.$rtype];
					if($code[$right] == 1) return true;
				endif;
			endif;
		endif;
	endif;
	return false;
}

function updateField($table, $field, $post, $wherefield, $wherenum) {
	global $con;
	$q = "UPDATE $table SET $field = '$post' WHERE $wherefield = $wherenum";
	mysqli_query($con, $q);
}

function updateFieldWhere($table, $field, $post, $where) {
	global $con;
	$q = "UPDATE $table SET $field = '$post' WHERE $where";
	mysqli_query($con, $q);
}

function getRow($table, $where) {
	global $con;
	$getfi = mysqli_query($con, "SELECT * FROM $table WHERE $where");
	$row = mysqli_fetch_array($getfi);
	if(!empty($row)) :
		return $row;
	endif;
	return false;
}

function insertRow($table, $fields, $values, $errors = false) {
	global $con;
	$q = "INSERT INTO $table ($fields) VALUES ($values)";
	mysqli_query($con, $q);
	if($errors) {echo '<b>'.$q.'</b><br>'; echo mysqli_error($con);}
	return mysqli_insert_id($con);
}

function getField($table, $field, $idfield, $id) {
	global $con;
	$q = "SELECT $field FROM $table WHERE $idfield = $id";
	$getfi = mysqli_query($con, $q);
	if($getfi) $row = mysqli_fetch_array($getfi);
	if(!empty($row)) :
		if(isset($row[$field])) return $row[$field];
	endif;
	return false;
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function thumb($source, $destination, $new_w = 60, $new_h = 60, $ext) {
	$cropfile=$source;
	$ext = strtolower($ext);
	if($ext == 'jpg' or $ext == 'jpeg') $source_img = @imagecreatefromjpeg($cropfile);
	if($ext == 'png') $source_img = @imagecreatefrompng($cropfile);

	if (!$source_img) {
		echo "could not create image handle";
		exit(0);
	}

	$orig_w = imagesx($source_img);
	$orig_h = imagesy($source_img);

	$w_ratio = ($new_w / $orig_w);
	$h_ratio = ($new_h / $orig_h);
	if ($orig_w > $orig_h ) {//landscape from here new
		$crop_w = round($orig_w * $h_ratio);
		$crop_h = $new_h;
		$src_x = ceil( ( $orig_w - $orig_h ) / 2 );
		$src_y = 0;
	} elseif ($orig_w < $orig_h ) {//portrait
		$crop_h = round($orig_h * $w_ratio);
		$crop_w = $new_w;
		$src_x = 0;
		$src_y = ceil( ( $orig_h - $orig_w ) / 2 );
	} else {//square
		$crop_w = $new_w;
		$crop_h = $new_h;
		$src_x = 0;
		$src_y = 0;
	}

	$dest_img = imagecreatetruecolor($new_w,$new_h);
	imagecopyresampled($dest_img, $source_img, 0 , 0 , $src_x, $src_y, $crop_w, $crop_h, $orig_w, $orig_h); //till here
	if($ext == 'jpg' or $ext == 'jpeg') {
		if(imagejpeg($dest_img, $destination)) {
			imagedestroy($dest_img);
			imagedestroy($source_img);
			return true;
		}
		else {return false;}
	}
	if($ext == 'png') {
		if(imagepng($dest_img, $destination)) {
			imagedestroy($dest_img);
			imagedestroy($source_img);
			return true;
		}
		else {return false;}
	}
	return false;
}

function getRegChildren($region) {
	global $regionsListChildren, $citiesList;
	$regionChildren = array();
	foreach($regionsListChildren[$region] as $children) :
		$regionChildren[$children] = $citiesList[$children];
	endforeach;
	return $regionChildren;
}

function getCatChildren($parent) {
	$children = array();
	$ops = listData('cats', 'deleted = 0 AND cat_level = 1 AND cat_parent = '.$parent);
	foreach($ops as $op) :
		$children[$op[0]] = $op['cat_name'];
	endforeach;
	return $children;
}

function updateUsermeta($options, $user, $table, $flname) {
	global $con;

	if(isset($_POST['updateUsermeta'])) :
		foreach($_POST as $key => $data) :
			if(array_key_exists ($key, $options['fields'])) :
				if($data != '') :
					if($key == 'user_password_hash') : $data = password_hash($data, PASSWORD_DEFAULT, array('cost' => 10)); endif;
					updateField($table, $key, $data, $flname, $user);
				endif;
			endif;
		endforeach;
		//redirect();
	endif;

	$usermeta = getRow($table, "$flname = '$user'");

	echo '<form action="" method="post" enctype="multipart/form-data">';
		foreach($options['fields'] as $keyn => $fi) :
			if( $keyn == 'user_city' ) $fi['drops'] = getRegChildren($usermeta['user_region']);
			if( $keyn == 'need_subcat' ) $fi['drops'] = getCatChildren($usermeta['need_cat']);
		
			if(isset($fi['required']) and $fi['required'] == true) :
				$spanreq = '<span class="reqfield">*</span>';
				$req = 'required';
			else :
				$spanreq = '';
				$req = '';
			endif;

			echo '<label>' . (isset($fi[0]) ? $fi[0] : '') . $spanreq . '</label>';
			if(isset($fi['inputtype']) and $fi['inputtype'] == 'radio') :
				foreach($fi['radios'] as $keyradio => $radioname) :
					echo '<input type ="radio" name="'.$keyn.'" value="'.$keyradio.'" '.(($keyradio == $usermeta[$keyn]) ? 'checked="checked"' : '').' /> '.$radioname.'&nbsp;&nbsp;';
				endforeach;

			elseif(isset($fi['inputtype']) and $fi['inputtype'] == 'number') :
				echo '<input type="number" min="'.$fi['min'].'" max="'.$fi['max'].'" name="'.$keyn.'" value="'.$usermeta[$keyn].'" '.$req.' />';

			elseif(isset($fi['inputtype']) and $fi['inputtype'] == 'drop') :
				echo '<select name="'.$keyn.'">';
					foreach($fi['drops'] as $keyn2 => $drop) :
						echo '<option '.(($keyn2 == $usermeta[$keyn]) ? 'selected="selected"' : '').' value="'.$keyn2.'">'.$drop.'</option>';
					endforeach;
				echo '</select>';

			elseif(isset($fi['inputtype']) and $fi['inputtype'] == 'bool') :
				$checked = ($usermeta[$keyn] == 1 ? 'checked="checked"' : '');

				echo '<input type="hidden" value="0" name="'.$keyn.'">';
				echo '<input '.$checked.' type="checkbox" value="1" name="'.$keyn.'">';

			elseif(isset($fi['inputtype']) and $fi['inputtype'] == 'photo') :

                echo '<div style="display: inline-block;">';

                    if(isset($_FILES[$keyn]) && !$_FILES[$keyn]['error'] && !empty($_FILES[$keyn]['tmp_name'])) {
                        // take care of uploads
                        $Core = new Core();
                        $basenameToRemove = pathinfo($usermeta['user_thumb'], PATHINFO_BASENAME);
                        $result = $Core->uploadFile($usermeta['user_id'], $basenameToRemove, ROOT_URL, $table);
                        if(!is_array($result)) {
                            $errExpl = $Core->get('uploadFileWriteToFilesystemErrors');
                            $uperr = $errExpl[$result];
                        }
                        $usermeta = getRow($table, "$flname = '$user'");
                    }

                    if(isset($usermeta['user_thumb']) && (strlen($usermeta['user_thumb']) > 3)) { // if this is not code, if this is an link to image...
                        echo '<img src="' . $usermeta['user_thumb'] . '" alt="Nuotrauka" onError="this.style.visibility=\'hidden\'" style="margin:10px; border: 1px solid #c4c4c4;" /><br />';
                    }

                    echo '<input type="file" name="'.$keyn.'" />';

                echo '</div>';

				if(isset($uperr)) : err($uperr, 'red', 'normal'); endif;

			elseif(isset($fi['inputtype']) and $fi['inputtype'] == 'textarea') :
				echo '<textarea name="'.$keyn.'">'.$usermeta[$keyn].'</textarea>';
			elseif(isset($fi['inputtype']) and $fi['inputtype'] == 'pass') :
				echo '<input '.((isset($fi['class'])) ? 'class="'.$fi['class'].'"' : '').' type="text" name="'.$keyn.'" '.((isset($fi['len'])) ? 'maxlength="'.$fi['len'].'"' : '').' value="" />';
			else :
				echo '<input '.((isset($fi['class'])) ? 'class="'.$fi['class'].'"' : '').' type="text" name="'.$keyn.'" '.((isset($fi['len'])) ? 'maxlength="'.$fi['len'].'"' : '').' value="'.$usermeta[$keyn].'" '.$req.' />';
			endif;
			echo '<br>';
		endforeach;

		echo '<label class="noprint"></label><input type="submit" name="updateUsermeta" value="Išsaugoti" />';

	echo '</form>';
}

function theMenu() {
	$menu = array();
	if(isCustom()) :
		$menu = array();
		if(haveRight(0)) $menu[] = array('need', 'Poreikiai', '1');
		if(haveRight(1)) $menu[] = array('thing', 'Darbai', '1');
		if(haveRight(2)) $menu[] = array('thingd', 'Geradariai', '1');
		if(haveRight(3)) $menu[] = array('needy', 'Stokojantieji / pagalbos gavėjai');
		if(haveRight(4)) $menu[] = array('stats', 'Statistika');
		if(haveRight(5)) $menu[] = array('atsarg', 'Atsarginė duomenų kopija');
		if(haveRight(7)) $menu[] = array('hefo', 'Antraštės ir poraštės');
		if(haveRight(6)) $menu[] = array('pages', 'Statiniai puslapiai');
		if(haveRight(8)) $menu[] = array('blogpages', 'Naujienos');
	endif;
	if(isManager()) 		$menu = array(
								array('home', 'Pradžia'),
								array('head' => 'AUKOKDAIKTUS.LT'),
								array('need', 'Poreikiai', '2'),
								array('thing', 'Daiktai', '2'),
								array('head' => 'AUKOKLAIKA.LT'),
								array('need', 'Poreikiai', '1'),
								array('thing', 'Darbai', '1'),
								array('head' => 'Bendri duomenys'),
								array('needy', 'Stokojantieji ir paslaugų gavėjai'),
								array('colleagues', 'Kolegų kontaktai'),
								);
	if(isGridManager())		$menu = array(
								array('home', 'Pradžia'),
								array('head' => 'AUKOKDAIKTUS.LT'),
								array('need', 'Poreikiai', '2'),
								array('thing', 'Daiktai', '2'),
								array('head' => 'AUKOKLAIKA.LT'),
								array('need', 'Poreikiai', '1'),
								array('thing', 'Darbai', '1'),
								array('head' => 'Bendri duomenys'),
								array('needy', 'Stokojantieji ir paslaugų gavėjai'),
								array('users', 'Kuratoriai', '1'),
								array('colleagues', 'Kolegų kontaktai'),
								array('head' => 'MANO KURATORIŲ DUOMENYS'),
								array('needyman', 'Stokojantieji'),
								array('needman', 'aukokdaiktus.lt poreikiai', '2'),
								array('thingman', 'Daiktai', '2'),
								array('needman', 'aukoklaika.lt poreikiai', '1'),
								array('thingman', 'Darbai', '1'),
								);
	
	if(isAdmin())			$menu = array(
								array('home', 'Pradžia'),
								array('head' => 'AUKOKDAIKTUS.LT'),
								array('need', 'Poreikiai', '2'),
								array('thing', 'Daiktai', '2'),
								array('thingd', 'Dovanotojai', '2'),
								array('head' => 'AUKOKLAIKA.LT'),
								array('need', 'Poreikiai', '1'),
								array('thing', 'Darbai', '1'),
								array('thingd', 'Geradariai', '1'),
								array('head' => 'Bendri duomenys'),
								array('edituser', 'Sukurti / keisti vartotojo paskyrą'),
								array('users', 'Dovanotojai / geradariai', '0'),
								array('users', 'Kuratoriai', '1'),
								array('users', 'Tinklo atstovai', '2'),
								array('users', 'Administratoriai', '3'),
								array('needy', 'Stokojantieji / pagalbos gavėjai'),
								array('rights', 'Vartotojų grupės ir teisės'),
								array('fields', 'Matomi laukai'),
								array('allusers', 'Visi vartotojai'),
								array('stats', 'Statistika'),
								array('atsarg', 'Atsarginė duomenų kopija'),
								array('laiskai', 'Laiškų siuntimas'),
								array('head' => 'SVETAINIŲ TURINYS'),
								array('hefo', 'Antraštės ir poraštės'),
								array('pages', 'Statiniai puslapiai'),
								array('blogpages', 'Naujienos'),
								);
	if(CUSER == 1)			$menu = array(
								array('home', 'Pradžia'),
								array('head' => 'INFORMACINĖ SISTEMA'),
								array('need', 'Poreikiai', '1'),
								array('thing', 'Darbai', '1'),
								array('thingd', 'Geradariai', '1'),
								array('edituser', 'Sukurti / keisti vartotojo paskyrą'),
								array('users', 'Dovanotojai / geradariai', '0'),
								array('users', 'Kuratoriai', '1'),
								array('users', 'Tinklo atstovai', '2'),
								array('users', 'Administratoriai', '3'),
								array('needy', 'Stokojantieji / pagalbos gavėjai'),
								array('rights', 'Vartotojų grupės ir teisės'),
								array('fields', 'Matomi laukai'),
								array('allusers', 'Visi vartotojai'),
								array('stats', 'Statistika'),
								array('atsarg', 'Atsarginė duomenų kopija'),
								array('laiskai', 'Laiškų siuntimas'),
								array('head' => 'SVETAINĖS TURINYS'),
								array('hefo', 'Antraštės ir poraštės'),
								array('pages', 'Statiniai puslapiai'),
								array('blogpages', 'Naujienos'),
								);
	foreach($menu as $menuit) :
		if(isset($menuit['head'])) :
			echo '<li class="menuhead">'.$menuit['head'].'</li>';
		else :
			$class = '';
			$allowed = array(
				$menuit[0],
				'edit'.$menuit[0],
				'edit'.$menuit[0].'s',
				'cat'.$menuit[0],
				'cat'.$menuit[0].'1',
				'cat'.$menuit[0].'2',
				'cat'.$menuit[0].'s',
				);
			$allowed2 = array(
				rtrim($menuit[0], 's'),
				'edit'.rtrim($menuit[0], 's'),
				'edit'.$menuit[0],
			);
			if((in_array(page(), $allowed) or in_array(page(), $allowed2)) and !isset($menuit[2])) $class='class="current"';
			if(in_array(page(), $allowed) and isset($menuit[2]) and ($menuit[2] == subpage() or (isset($_GET['type']) and $menuit[2] == $_GET['type']))) $class='class="current"';
			echo '<li><a href="?p='.$menuit[0].(isset($menuit[2]) ? '&subp='.$menuit[2]: '').'"'.$class.'>'.$menuit[1].'</a></li>';
		endif;
	endforeach;
}

// Admin functions

function listData($what, $where, $page = false, $advanced = false) {
	global $con;
	if(isset($_GET['pp']) and $_GET['pp'] > 10 and $_GET['pp'] <= 1000) :
		$page = ($page - 1) * (int)$_GET['pp'];
		$limit = 'LIMIT '.$page.','.(int)$_GET['pp'];
	elseif($page) :
		$page = ($page - 1) * 10;
		$limit = 'LIMIT '.$page.',10';
	else :
		$limit = '';
	endif;

	$ret = array();
	if($advanced) $q = $advanced." ".$limit;
	else $q = "SELECT * FROM $what WHERE $where $limit";
	$res = mysqli_query($con, $q);
	if($res) while($row = mysqli_fetch_array($res)) $ret[] = $row;
	return $ret;
}

function countData($what, $where, $advanced = false) {
	global $con;
	$ret = array();
	if($advanced) $q = $advanced;
	else $q = "SELECT * FROM $what WHERE $where";
	$res = mysqli_query($con, $q);
	if($res) return mysqli_num_rows($res);
	return 0;
}

function getSort($default, $defaultadv = false) {
	global $getsort, $getorder;
	if(!isset($_GET['rikiuoti']) && !isset($_GET['tvarka']) && $defaultadv) :
		$getsort = $defaultadv;
		$getorder = '';
	else :
		$getsort = (isset($_GET['rikiuoti']) ? $_GET['rikiuoti'] : $default);
		$gettvarka = (isset($_GET['tvarka']) ? $_GET['tvarka'] : '');
		switch($gettvarka) :
			case 'asc' : $getorder = ' ASC'; break;
			case 'desc' : $getorder = ' DESC'; break;
			default : $getorder = ' DESC';
		endswitch;
	endif;
}

function getCurrentLink() {
	return '?p='.page().(isset($_GET['subp']) ? '&subp='.$_GET['subp'] : '').(isset($_GET['rikiuoti']) ? '&rikiuoti='.$_GET['rikiuoti'] : '').(isset($_GET['tvarka']) ? '&tvarka='.$_GET['tvarka'] : '').(isset($_GET['pp']) ? '&pp='.$_GET['pp'] : '').(psl() ? '&page='.psl(): '');
}

function formatTable($object, $cols, $head = false, $sort = false, $viewed = false, $class = false) {
	if(!empty($object)) :
		echo '<table class="admintbl '.$class.'">';
		if($head) :
			echo '<thead><tr>';
			foreach($head['titles'] as $key => $h) :
				$finaltitle = $h;
				$colspan = (isset($head['merge']) && (array_key_exists($key, $head['merge'])) ? ' colspan="'.$head['merge'][$key].'"' : '');
				$columns = (isset($head['columns']) && (array_key_exists($key, $head['columns'])) ? ' class="'.$head['columns'][$key].'"' : '');
				if($sort && array_key_exists($key, $sort)) :
					$parameters = preg_replace("/([?&])rikiuoti=\w+(&|$)/", "$2", $_SERVER['QUERY_STRING']);
					$parameters = preg_replace("/([?&])tvarka=\w+(&|$)/", "$2", $parameters);
					$parameters = preg_replace("/([?&])page=\w+(&|$)/", "$2", $parameters);
					$sortvalue = $sort[$key];
					$gettvarka = (isset($_GET['tvarka']) ? $_GET['tvarka'] : '');
					switch($gettvarka) :
						case 'asc' : $ordervalue = 'desc'; $arr ='fa fa-angle-up'; break;
						case 'desc' : $ordervalue = 'asc'; $arr ='fa fa-angle-down'; break;
						default : $ordervalue = 'desc'; $arr ='fa fa-angle-up';
					endswitch;
					$getrikiuoti = (isset($_GET['rikiuoti']) ? $_GET['rikiuoti'] : '');
					$arrow = (($sort[$key] == $getrikiuoti) ? $arr : '');
					echo '<th'.$colspan.$columns.'><a href="index.php?'.$parameters.'&rikiuoti='.$sortvalue.'&tvarka='.$ordervalue.'&page='.pageNum().'">'.$finaltitle.' <i class="'.$arrow.'"></i></a></th>';
				else : 
					echo '<th'.$colspan.$columns.'>'.$finaltitle.'</th>';
				endif;
			endforeach;
			echo '</tr></thead>';
		endif;
		foreach($object as $o) :
			if($viewed && ((isset($o['user_active']) and $o['user_active'] == 0) or (isset($o['deleted']) and $o['deleted'] == 1))) echo '<tr class="unmarked">';
			else echo '<tr>';
			$count = 0;
			foreach($cols as $key => $col) :
				$columns = (isset($head['columns']) && (array_key_exists($key, $head['columns'])) ? ' '.$head['columns'][$key] : '');
				$title = (isset($head['title']) && (array_key_exists($key, $head['title'])) ? $head['title'][$key] : '');
				echo '<td class="col'.$count.$columns.'" title="'.($title == true ? rtrim(str_replace('<br>', "\n", $o[$col]), ', ') : NULL).'">';
				$count = $count + 1;
				if(is_array($col)) :
					if(array_key_exists('action', $col)) :
						if(array_key_exists(4, $col['action']) && $col['action'][4] != false) :
							$func = $col['action'][4];
							$color = $func($o[$col['action'][0]]);
						else : $color = $col['action'][3];
						endif;
						echo '<a class="'.((array_key_exists(5, $col['action'])) ? 'responsive-normal ' : '').'actionbutton '.$color.'" href="'.getCurrentLink().'&veiksmas='.$col['action'][1].'&param='.$o[$col['action'][0]].'">'.$col['action'][2].'</a>';
						if(array_key_exists(5, $col['action'])) :
							echo '<a class="responsive-small responsive-verysmall actionbutton '.$color.'" href="'.getCurrentLink().'&veiksmas='.$col['action'][1].'&param='.$o[$col['action'][0]].'" title="'.$col['action'][2].'"></a>';
						endif;
					elseif(array_key_exists('converter', $col)) :
						$value = $o[$col['converter'][0]];
						$fun = $col['converter'][1];
						$arg = ( isset($col['converter'][2]) ? $col['converter'][2] : false);
						echo $fun($value, $arg);
					elseif(array_key_exists('convArray', $col)) :
						$value = $col['convArray'][0];
						$values = array();
						foreach($value as $vl) :
							$values[] = $o[$vl];
						endforeach;
						$fun = $col['convArray'][1];
						$arg = ( isset($col['convArray'][2]) ? $col['convArray'][2] : false);
						echo $fun($values, $arg);
					elseif(array_key_exists('editable', $col)) :
						$value = $o[$col['editable'][0]];
						$field = $col['editable'][0];
						$valueid = $o[$col['editable'][1]];
						$class = $col['editable'][2];
						$len = $col['editable'][3];
						echo '<form action="" method="post"><input type="hidden" name="editableid" value="'.$valueid.'"><input type="hidden" name="editablefield" value="'.$field.'"><input title="Įvedę duomenis paspauskite ENTER" name="editable" type="text" value="'.$value.'" class="'.$class.'" maxlength="'.$len.'"></form>';
					endif;
				else :
					if($col == 'null') : echo '';
					else :
						if(isset($_GET['srch'])) :
							echo str_replace(ucfirst($_GET['srch']), '<span class="srch_marker">'.ucfirst($_GET['srch']).'</span>', str_replace($_GET['srch'], '<span class="srch_marker">'.$_GET['srch'].'</span>', $o[$col]));
						else :
							echo $o[$col];
						endif;
					endif;
				endif;
				echo '</td>';
			endforeach;
			echo '</tr>';
		endforeach;
		echo '</table>';
	else : echo '<i class="">Nėra duomenų</i>';
	endif;
}

function pageNum() {
	if(isset($_GET['page'])) return $_GET['page'];
	else return 1;
}

function pagination($count, $perpage = 10) {
	if(isset($_GET['pp']) and $_GET['pp'] > 10 and $_GET['pp'] <= 1000) $perpage = (int)$_GET['pp'];
	
	if($count > $perpage) :

		if(isset($_POST['smsearch']) or isset($_GET['s'])) : $getsm = '&s='.(!empty($_POST['smsearch']) ? $_POST['smsearch'] : (!empty($_GET['s']) ? $_GET['s'] : NULL));
		else : $getsm = '';
		endif;

		$pages = ceil($count/$perpage);
		$adj = 3;
		$pbl = $pages - 1;

		$parameters = preg_replace("/([?&])page=\w+(&|$)/", "$2", $_SERVER['QUERY_STRING']);
		$parameters = preg_replace('/([?&])s=[^&]+(&|$)/','$2', $parameters);
		
		$parp = (isset($_GET['p']) ? '<input type="hidden" name="p" value="'.$_GET['p'].'">' : '');
		$parsubp = (isset($_GET['subp']) ? '<input type="hidden" name="subp" value="'.$_GET['subp'].'">' : '');
		$parsrch = (isset($_GET['srch']) ? '<input type="hidden" name="srch" value="'.$_GET['srch'].'">' : '');
		$parrikiuoti = (isset($_GET['rikiuoti']) ? '<input type="hidden" name="rikiuoti" value="'.$_GET['rikiuoti'].'">' : '');
		$partvarka = (isset($_GET['tvarka']) ? '<input type="hidden" name="tvarka" value="'.$_GET['tvarka'].'">' : '');
		
		$ppops = array(10, 20, 50, 100, 200, 500, 1000, 2000);
		$ppoptions = '';
		foreach($ppops as $ppop) :
			$ppoptions .= '<option '.((isset($_GET['pp']) and $_GET['pp'] == $ppop) ? 'selected="selected"' : '').' value='.$ppop.'>'.$ppop.'</option>';
		endforeach;
		
		
		$pag = '<div class="pagination-div">Puslapis <i><strong>'.pageNum().'</strong></i> iš <i>'.$pages.'</i>, rodoma po <form class="inline" action="" method="GET">'.$parp.$parsubp.$parsrch.$parrikiuoti.$partvarka.'<select style="width: 65px;" name="pp">'.$ppoptions.'</select> <input type="submit" value="Pakeisti" /></form> </div>';
		$pag .= '<ul class="pagination">';

		if ($pages < 7 + ($adj * 2))
		{
			for ($i = 1; $i <= $pages; $i++) :
				if($i == pageNum()) $class = ' class="current" '; else $class = '';

				$pag .= '<li '.$class.' style="margin: 5px 2px;"><a href="/index.php?'.$parameters.$getsm.'&page='.$i.'">'.$i.'</a></li>';
			endfor;
		}
		else if ($pages > 5 + ($adj * 2))
		{
			if (pageNum() < 1 + ($adj * 2))
			{
				for ($i = 1; $i < 4 + ($adj * 2); $i++) :
					if($i == pageNum()) $class = ' class="current" '; else $class = '';

					$pag .= '<li '.$class.' style="margin: 5px 2px;"><a href="/index.php?'.$parameters.$getsm.'&page='.$i.'">'.$i.'</a></li>';
				endfor;

				$pag .= '<li style="margin: 5px 2px;" class="dots"><a>...</a></li>';
				$pag .= '<li style="margin: 5px 2px;"><a href="/index.php?'.$parameters.$getsm.'&page='.$pbl.'">'.$pbl.'</a></li>';
				$pag .= '<li style="margin: 5px 2px;"><a href="/index.php?'.$parameters.$getsm.'&page='.$pages.'">'.$pages.'</a></li>';
			}
			else if ($pages - ($adj * 2) > pageNum() && pageNum() > ($adj * 2))
			{
				$pag .= '<li style="margin: 5px 2px;"><a href="/index.php?'.$parameters.$getsm.'&page=1">1</a></li>';
				$pag .= '<li style="margin: 5px 2px;"><a href="/index.php?'.$parameters.$getsm.'&page=2">2</a></li>';
				$pag .= '<li style="margin: 5px 2px;" class="dots"><a>...</a></li>';

				for ($i = pageNum() - $adj; $i <= pageNum() + $adj; $i++) :
					if($i == pageNum()) $class = ' class="current" '; else $class = '';

					$pag .= '<li '.$class.' style="margin: 5px 2px;"><a href="/index.php?'.$parameters.$getsm.'&page='.$i.'">'.$i.'</a></li>';
				endfor;

				$pag .= '<li style="margin: 5px 2px;" class="dots"><a>...</a></li>';
				$pag .= '<li style="margin: 5px 2px;"><a href="/index.php?'.$parameters.$getsm.'&page='.$pbl.'">'.$pbl.'</a></li>';
				$pag .= '<li style="margin: 5px 2px;"><a href="/index.php?'.$parameters.$getsm.'&page='.$pages.'">'.$pages.'</a></li>';
			}
			else
			{
				$pag .= '<li style="margin: 5px 2px;"><a href="/index.php?'.$parameters.$getsm.'&page=1">1</a></li>';
				$pag .= '<li style="margin: 5px 2px;"><a href="/index.php?'.$parameters.$getsm.'&page=2">2</a></li>';
				$pag .= '<li style="margin: 5px 2px;" class="dots"><a>...</a></li>';

				for ($i = $pages - (2 + ($adj * 2)); $i <= $pages; $i++) :
					if($i == pageNum()) $class = ' class="current" '; else $class = '';

					$pag .= '<li '.$class.' style="margin: 5px 2px;"><a href="/index.php?'.$parameters.$getsm.'&page='.$i.'">'.$i.'</a></li>';
				endfor;
			}
		}

		$pag .= '</ul>';

		echo $pag;
	endif;


	return false;
}

function prettyslug($url) {
   $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
   $url = trim($url, "-");
   $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
   $url = strtolower($url);
   $url = preg_replace('~[^-a-z0-9_]+~', '', $url);
   return $url;
}

// Converters
function getThumb($thumbLink) {return (strlen($thumbLink) > 16 ? '<img class="thumb" src="'.$thumbLink.'" onError="this.style.visibility=\'hidden\'" />' : '');}
function getUserEditLink($id) {return '<a title="Paskyros nustatymai" href="?p=edituser&user='.$id.'"><i class="fa fa-cog"></i></a>';}
function getNeedyEditLink($id) {return '<a title="Nustatymai" href="?p=editneedy&user='.$id.'"><i class="fa fa-cog"></i></a>';}
function getNeedEditLink($id) {return '<a title="Nustatymai" href="?p=editneed&type='.subpage().'&need='.$id.'"><i class="fa fa-cog"></i></a>';}
function getThingEditLink($id) {return '<a title="Nustatymai" href="?p=editthing&type='.subpage().'&need='.$id.'"><i class="fa fa-cog"></i></a>';}
function getPageEditLink($id) {return '<a title="Redaguoti" href="?p=editpage&page='.$id.'"><i class="fa fa-pencil"></i></a>';}
function getBlogPageEditLink($id) {return '<a title="Redaguoti" href="?p=editblogpage&page='.$id.'"><i class="fa fa-pencil"></i></a>';}
function getCountry($id) {global $citiesList; return $citiesList[$id];}
function getCounty($id) {global $regionsList; return $regionsList[$id];}
function getNeedyName($id) {return getField('needy', 'user_fname', 'user_id', $id).' '.getField('needy', 'user_lname', 'user_id', $id);}
function getCatName($id) {return getField('cats', 'cat_name', 'cat_id', $id);}
function getNeedEditLinkGift($id) {return '<a title="Darbas/daiktas" href="?p=editthing&type='.subpage().'&need='.$id.'"><i class="fa fa-gift"></i></a>';}
function nicerTF($val) {return ($val == 1 ? '<i class="fa fa-check"></i>' : '');}
function getGroupName($acctype) {if($acctype == 0) return 'Geradarys'; return getField('acctypes', 'name', 'acctype', $acctype);}

function getBlogLinks($params = array(0, 0)) {
	switch($params[1]) :
		case 0 : echo '<a target="_blank" href="http://www.aukoklaika.lt/naujienos/'.$params[0].'"><i class="fa fa-globe"></i></a>'; break;
		case 1 : echo '<a target="_blank" href="http://www.aukokdaiktus.lt/naujienos/'.$params[0].'"><i class="fa fa-globe"></i></a>'; break;
		case 2 : echo '<a target="_blank" href="http://www.aukoklaika.lt/naujienos/'.$params[0].'"><i class="fa fa-globe"></i></a> <a target="_blank" href="http://www.aukokdaiktus.lt/naujienos/'.$params[0].'"><i class="fa fa-globe"></i></a>'; break;
		default :
	endswitch;
}

function getPageLinks($params = array('', 0)) {
	switch($params[1]) :
		case 0 : echo '<a target="_blank" href="http://www.aukoklaika.lt/'.$params[0].'"><i class="fa fa-globe"></i></a>'; break;
		case 1 : echo '<a target="_blank" href="http://www.aukokdaiktus.lt/'.$params[0].'"><i class="fa fa-globe"></i></a>'; break;
		case 2 : echo '<a target="_blank" href="http://www.aukoklaika.lt/'.$params[0].'"><i class="fa fa-globe"></i></a> <a target="_blank" href="http://www.aukokdaiktus.lt/'.$params[0].'"><i class="fa fa-globe"></i></a>'; break;
		default :
	endswitch;
}

function getThingdName($params = array('', '', '', '', '', 0)) {
	if($params[0] != '' and $params[1] != '') return $params[0].' '.$params[1];
	if($params[2] != '' and $params[3] != '') :
		if($params[5] > 0) return '<a href="/index.php?p=edituser&user='.$params[5].'" title="Šis vartotojas yra registruotas, todėl galite peržiūrėti jo profilį">'.$params[2].' '.$params[3].'</a>';
		return $params[2].' '.$params[3];
	endif;
	if($params[4] != '') return $params[4];
	return '<i>Nenurodytas vardas</i>';
}

function getDoubleString($params = array('', '')) {
	if($params[0] != '') return $params[0];
	if($params[1] != '') return $params[1];
	return '<i>N/D</i>';
}

function getSearchLink($params = array('', '')) {
	$lnk = '';
	if($params[0] == 'Vartotojas') $lnk = '/index.php?p=edituser&user='.$params[1];
	if($params[0] == 'Stokojantysis') $lnk = '/index.php?p=editneedy&user='.$params[1];
	if($params[0] == 'Dovanotojas/geradarys' or $params[0] == 'Daiktas/darbas') $lnk = '/index.php?p=editthing&need='.$params[1];
	return '<a href="'.$lnk.'"><i class="fa fa-info-circle"></i></a>';
}

function getManagerParent($user) {
	$acctype = getField('users', 'user_acctype', 'user_id', $user);
	if($acctype == 1) :
		$parent = getField('users', 'user_parent', 'user_id', $user);
		if($parent) : return $parent; endif;
	elseif($acctype == 2 or $acctype == 3) : return $user;
	endif;
	return 0;
}

function acctypeBack() {
	$id = CUSER;
	global $con;
	if($id) :
		$q = "SELECT user_acctype, user_temp_acctype FROM users WHERE user_id = $id";
		$res = mysqli_query($con, $q);
		if($res) :
			$res = mysqli_fetch_array($res);
			if($res['user_acctype'] == 3 and $res['user_temp_acctype'] > 0) :
				if(isset($_GET['grizti'])) :
					updateFieldWhere('users', 'user_temp_acctype', 0, "user_id = $id");
					redirect(0, '/');
				endif;
			?>
				<div class="grizti">
					<a href="/?grizti">Grįžti į admin. paskyrą</a>
				</div>
			<?php
			endif;
		endif;
	endif;
	return false;
}

function isDevelopmentEnvironment()
{
    return (strpos($_SERVER['HTTP_HOST'], '.local') || strpos($_SERVER['HTTP_HOST'], '.dev'));
}

function logEmailMessage($from, $to, $subject, $message, $replyTo)
{
    global $con;

    $query = sprintf(
        "INSERT INTO `email_log` (`from`, `to`, `reply_to`, `subject`, `message`, `created`) 
		 VALUES ('%s', '%s', '%s', '%s', '%s', NOW())",
        mysqli_real_escape_string($con, $from),
        mysqli_real_escape_string($con, $to),
        mysqli_real_escape_string($con, $replyTo),
        mysqli_real_escape_string($con, $subject),
        mysqli_real_escape_string($con, $message)
    );

    return mysqli_query($con, $query);
}

// Mail
function myMail($to, $subject, $message, $from = '', $fromName = '')
{
    $replyTo = $from;
    if ($from !== EMAIL_DEFAULT_FROM) {
        $from = EMAIL_DEFAULT_FROM;
    }

    logEmailMessage($from, $to, $subject, $message, $replyTo);
    if (isDevelopmentEnvironment()){
        return true;
    }

    $mail = new PHPMailer;

    // please look into the config/config.php for much more info on how to use this!
    // use SMTP or use mail()
    if (EMAIL_USE_SMTP) {
        // Set mailer to use SMTP
        $mail->IsSMTP();
        //useful for debugging, shows full SMTP errors
        //$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
        // Enable SMTP authentication
        $mail->SMTPAuth = EMAIL_SMTP_AUTH;
        // Enable encryption, usually SSL/TLS
        if (defined(EMAIL_SMTP_ENCRYPTION)) {
            $mail->SMTPSecure = EMAIL_SMTP_ENCRYPTION;
        }
        // Specify host server
        $mail->Host = EMAIL_SMTP_HOST;
        $mail->Username = EMAIL_SMTP_USERNAME;
        $mail->Password = EMAIL_SMTP_PASSWORD;
        $mail->Port = EMAIL_SMTP_PORT;
    } else {
        $mail->IsMail();
    }

    $mail->From = $from;
    $mail->FromName = $fromName ?: substr(strrchr($from, "@"), 1); // if no from name then use email domain of 'from' email
    if(is_array($to)) {
        foreach($to as $emailTo) {
            $mail->AddBCC($emailTo, '');
        }
    } else {
        $mail->AddAddress($to);
    }

    $mail->AddReplyTo($replyTo);
    $mail->Subject = $subject;

    // Sender - Must be set, because it is required as security flag or so..
    $mail->set('Sender', $from);

    // Encoding
    $mail->set('CharSet', CHARSET);

    // the link to your register.php, please set this value in config/email_verification.php
    $mail->Body = $message;
    $mail->IsHTML(true);

    if(!$mail->Send()) {
        //$this->errors[] = 'Mail not sent' . $mail->ErrorInfo;
        return false;
    } else {
        return true;
    }
}

?>