<?php if($login->isUserLoggedIn() == false) return; if(!isAdmin() and !(isCustom() and haveRight(4)) ) return;

// @TODO: Add the following statistics query:

/*
SELECT

    n.need_id as 'Poreikio ID', n.need_name as 'Poreikis', n.need_regdate as 'Poreikis uzregistruotas', n.need_expires as 'Poreikio galiojimo laikas', IF(n.need_type = 1, 'Darbas', 'Daiktas') as 'Projektas', n.need_author, n.need_full as 'Poreikis igyvendintas (1-taip, 0-ne)', n.deleted as 'Poreikis pasalintas (1-taip, 0-ne)'
    , c.cat_name as 'Kategorija', c.cat_type as 'Kategorijos tipas', c.cat_level as 'Kategorijos lygis', c.cat_parent as 'Tevine kategorija'
    , c_sub.cat_name as 'Subkategorija', c_sub.cat_type as 'Subkategorijos tipas', c_sub.cat_level as 'Subkategorijos lygis', c_sub.cat_parent as 'Tevine subkategorija'
    , n.need_full_user as 'Geradario ID', u.user_name AS 'Geradario pavadinimas', u.user_fname as 'Geradario vardas', u.user_lname as 'Geradario pavarde', u.user_region as 'Geradario regionas', u.user_city as 'Geradario miestas'
    , ny.user_person as 'Stokojanciojo tipas', ny.user_fname as 'Stokojanciojo vardas', ny.user_lname as 'Stokojanciojo pavarde', ny.user_orgname as 'Stokojanti organizacija', ny.user_city as 'Stokojanciojo miesto ID', ny.user_region as 'Stokojanciojo regiono ID'
    , ucity.id as 'Geradario miesto ID', ucity.`name` as 'Geradario miestas'
    , uregion.id as 'Geradario regiono ID', uregion.`name` as 'Geradario regionas'
    , ncity.id as 'Stokojanciojo miesto ID', ncity.`name` as 'Stokojanciojo miestas'
    , nregion.id as 'Stokojanciojo regiono ID', nregion.`name` as 'Stokojanciojo regionas'

FROM needs as n
LEFT JOIN cats as c ON (n.need_cat = c.cat_id)
LEFT JOIN cats as c_sub ON (n.need_cat = c_sub.cat_id)
LEFT JOIN users as u ON (n.need_full_user = u.user_id)
LEFT JOIN needy as ny ON (n.need_needy = ny.user_id)
LEFT JOIN city as ucity ON (u.user_city = ucity.id)
LEFT JOIN region as uregion ON (ucity.region_id = uregion.id)
LEFT JOIN city as ncity ON (ny.user_city = ncity.id)
LEFT JOIN region as nregion ON (ncity.region_id = nregion.id)

WHERE
    n.need_fulldate BETWEEN '2017-01-01 00:00:00' AND '2017-12-31 23:59:59'
    -- AND
    -- n.need_full = 1
    -- AND
    -- n.deleted != 1
*/

?>
<form class="inline" action="" method="GET">
		<input type="hidden" name="p" value="stats" />
	Per metus
	<select class="short" name="year">
		<?php for($i = 2013; $i <= date('Y'); $i++) echo '<option '.((isset($_GET['year']) and $_GET['year'] == $i) ? 'selected="selected"' : '').' value="'.$i.'">'.$i.'</option>'; ?>
		
	</select><input class="short" type="submit" value="Rodyti" />
</form>

<form class="inline" action="" method="GET">
		<input type="hidden" name="p" value="stats" />
	Per mėnesį
	<select class="shorter" name="month">
	<?php
	$months = array('', 'sausis', 'vasaris', 'kovas', 'balandis', 'gegužė', 'birželis', 'liepa', 'rugpjūtis', 'rugsėjis', 'spalis', 'lapkritis', 'gruodis');
	for($i = 2013; $i <= date('Y'); $i++) for($m = 1; $m <= 12; $m++) echo '<option '.((isset($_GET['month']) and $_GET['month'] == $i.'-'.$m) ? 'selected="selected"' : '').' value="'.$i.'-'.$m.'">'.$i.' m. '.$months[$m].'</option>';
	?>
	</select><input class="short" type="submit" value="Rodyti" />
</form>

<form class="inline" action="" method="GET">
		<input type="hidden" name="p" value="stats" />
	Per dieną
	<input class="short" type="text" name="day" value="<?php echo ((isset($_GET['day'])) ? $_GET['day'] : date('Y-m-d')); ?>" />
	<input class="short" type="submit" value="Rodyti" />
</form>
<br>
<form class="inline" action="" method="GET">
		<input type="hidden" name="p" value="stats" />
	Per laiko tarpą
	<input class="short" type="text" name="day1" value="<?php echo ((isset($_GET['day1'])) ? $_GET['day1'] : date('Y-m-d')); ?>" /> — 
	<input class="short" type="text" name="day2" value="<?php echo ((isset($_GET['day2'])) ? $_GET['day2'] : date('Y-m-d')); ?>"  />
	<input class="short" type="submit" value="Rodyti" />
</form>
<br>
<hr>

<?php

if( isset($_GET['year']) or isset($_GET['month']) or isset($_GET['day']) or isset($_GET['day1']) or isset($_GET['day2']) ) :
	
	if( isset($_GET['year']) ) :
		$filter = " AND YEAR(user_registration_datetime) = '".$_GET['year']."'";
		$filter2 = " YEAR(need_regdate) = '".$_GET['year']."'";
		$filter3 = " YEAR(need_fulldate) = '".$_GET['year']."'";
	elseif( isset($_GET['month']) ) :
		$month = explode('-', $_GET['month']);
		$filter = " AND YEAR(user_registration_datetime) = '".$month[0]."' AND MONTH(user_registration_datetime) = '".$month[1]."'";
		$filter2 = " YEAR(need_regdate) = '".$month[0]."' AND MONTH(need_regdate) = '".$month[1]."'";
		$filter3 = " YEAR(need_fulldate) = '".$month[0]."' AND MONTH(need_fulldate) = '".$month[1]."'";
	elseif( isset($_GET['day']) ) :
		$explode = explode('-', $_GET['day']);
		if(count($explode) != 3) {err('Netinkamai suformuota data'); return;}
		if(!checkdate((int)$explode[1], (int)$explode[2], (int)$explode[0])) {err('Ši data negalioja'); return;}
		$filter = " AND user_registration_datetime > '".$_GET['day']." 00:00:00' AND user_registration_datetime < '".$_GET['day']." 23:59:59'";
		$filter2 = " need_regdate > '".$_GET['day']." 00:00:00' AND need_regdate < '".$_GET['day']." 23:59:59'";
		$filter3 = " need_fulldate > '".$_GET['day']." 00:00:00' AND need_fulldate < '".$_GET['day']." 23:59:59'";
	elseif( isset($_GET['day1']) and isset($_GET['day2']) ) :
		$explode1 = explode('-', $_GET['day1']);
		$explode2 = explode('-', $_GET['day2']);
		if(count($explode1) != 3 or count($explode2) != 3) {err('Netinkamai suformuota data'); return;}
		if(!checkdate((int)$explode1[1], (int)$explode1[2], (int)$explode1[0])) {err('Ši data negalioja (intervalo pradžia)'); return;}
		if(!checkdate((int)$explode2[1], (int)$explode2[2], (int)$explode2[0])) {err('Ši data negalioja (intervalo pabaiga)'); return;}
	
		if( strtotime($_GET['day1']) > strtotime($_GET['day2']) ) {err('Intervalo pradžia didesnė už pabaigą'); return;}
		$filter = " AND user_registration_datetime > '".$_GET['day1']." 00:00:00' AND user_registration_datetime < '".$_GET['day2']." 23:59:59'";
		$filter2 = " need_regdate > '".$_GET['day1']." 00:00:00' AND need_regdate < '".$_GET['day2']." 23:59:59'";
		$filter3 = " need_fulldate > '".$_GET['day1']." 00:00:00' AND need_fulldate < '".$_GET['day2']." 23:59:59'";
	endif;
	
	$stats = array(
		array( 'Administratoriai', countData('users', 'user_acctype= 3 AND user_active = 1'.$filter) ),
		array( 'Tinklo atstovai', countData('users', 'user_acctype= 2 AND user_active = 1'.$filter) ),
		array( 'Kuratoriai', countData('users', 'user_acctype= 1 AND user_active = 1'.$filter) ),
		array( 'Dovanotojai / geradariai', countData('users', 'user_acctype= 0 AND user_active = 1'.$filter) ),
	);
	
	$stats2 = array(
		array( 'Registruota poreikių', countData('needs', $filter2) ),
		array( 'Įgyvendinta poreikių', countData('needs', $filter3) ),
	);
	?>
	<br><b>Informacinėje sistemoje registruoti vartotojai</b><br><br>
	<table>
	<?php
	foreach($stats as $st) :
		echo '<tr><td style="width: 375px;">'.$st[0].'</td><td>'.$st[1].'</td></tr>';
	endforeach;
	?>
	</table>
	<br>
	<br><b>Informacija apie poreikius</b><br><br>
	<table>
	<?php
	foreach($stats2 as $st) :
		echo '<tr><td style="width: 375px;">'.$st[0].'</td><td>'.$st[1].'</td></tr>';
	endforeach;
	?>
	</table>
	<?php
	return;
endif;

$stats = array(
	array( 'Administratoriai', countData('users', 'user_acctype= 3 AND user_active = 1') ),
	array( 'Tinklo atstovai', countData('users', 'user_acctype= 2 AND user_active = 1') ),
	array( 'Kuratoriai', countData('users', 'user_acctype= 1 AND user_active = 1') ),
	array( 'Dovanotojai / geradariai', countData('users', 'user_acctype= 0 AND user_active = 1') ),
);
?>
<br><b>Informacinėje sistemoje registruoti vartotojai</b><br><br>
<table>
<?php
foreach($stats as $st) :
	echo '<tr><td style="width: 375px;">'.$st[0].'</td><td>'.$st[1].'</td></tr>';
endforeach;
?>
</table>

<?php
$pa = countData('needs', 'need_type = 1 AND need_full = 0 AND deleted = 0');
$pb = countData('needs', 'need_type = 2 AND need_full = 0 AND deleted = 0');
$da = countData('needs', 'need_type = 1 AND need_full = 1 AND deleted = 0');
$db = countData('needs', 'need_type = 2 AND need_full = 1 AND deleted = 0');

$dova = countData(false, false, 'SELECT DISTINCT need_full_user FROM needs WHERE need_type = 1 AND need_full = 1 AND need_full_user > 0 AND deleted = 0');

$dovb = countData(false, false, 'SELECT DISTINCT need_full_user FROM needs WHERE need_type = 2 AND need_full = 1 AND need_full_user > 0 AND deleted = 0');

$dova2 = countData(false, false, 'SELECT DISTINCT CONCAT(user_fname, user_lname) FROM needs WHERE need_type = 1 AND need_full = 1 AND need_full_user = 0 AND deleted = 0');

$dovb2 = countData(false, false, 'SELECT DISTINCT CONCAT(user_fname, user_lname) FROM needs WHERE need_type = 2 AND need_full = 1 AND need_full_user = 0 AND deleted = 0');

$dovv = countData(false, false, 'SELECT DISTINCT need_full_user FROM needs WHERE need_full = 1 AND need_full_user > 0 AND deleted = 0');

$dovv2 = countData(false, false, 'SELECT DISTINCT CONCAT(user_fname, user_lname) FROM needs WHERE need_full = 1 AND need_full_user = 0 AND deleted = 0');

$stats = array(

	array( 'Stokojantieji', countData('needy', 'deleted = 0') ),
	array( 'Poreikiai (darbų)', $pa ),
	array( 'Poreikiai (daiktų)', $pb ),
	array( 'Poreikiai (suma)', $pa + $pb ),
	array( 'Darbai', $da ),
	array( 'Daiktai', $db ),
	array( 'Darbai / daiktai (suma)', $da + $db ),
	array( 'Geradariai', ($dova + $dova2). " ($dova reg. vart. ir apytiksliai $dova2 nereg. vart.)" ),
	array( 'Dovanotojai', ($dovb + $dovb2). " ($dovb reg. vart. ir apytiksliai $dovb2 nereg. vart.)" ),
	array( 'Geradariai / dovanotojai (aibių sąjunga)', ($dovv + $dovv2). " ($dovv reg. vart. ir apytiksliai $dovv2 nereg. vart.)" ),
);

?>
<br><b>Informacija apie stokojančiuosius</b><br><br>
<table>
<?php
foreach($stats as $st) :
	echo '<tr><td style="width: 375px;">'.$st[0].'</td><td>'.$st[1].'</td></tr>';
endforeach;
?>
</table>
<?php
$stats = array(

	array( 'Stokojantis pašalintas, nes nebereikalinga parama/pagalba', countData('needy', 'delreason = 1 AND deleted = 1') ),
	array( 'Stokojantis pašalintas dėl privatumo problemos', countData('needy', 'delreason = 2 AND deleted = 1') ),
	array( 'Stokojantis pašalintas dėl sukčiavimo', countData('needy', 'delreason = 3 AND deleted = 1') ),

	array( 'Poreikis pašalintas, nes padovanota/padėta ne per sistemą', countData('needs', 'delreason = 1') ),
	array( 'Poreikis pašalintas, nes nebereikalingas daiktas/darbas', countData('needs', 'delreason = 2 AND deleted = 1') ),
);

?>
<br>
<b>Duomenų šalinimo statistika</b><br><br>
<table>
<?php
foreach($stats as $st) :
	echo '<tr><td style="width: 375px;">'.$st[0].'</td><td>'.$st[1].'</td></tr>';
endforeach;
?>
</table>