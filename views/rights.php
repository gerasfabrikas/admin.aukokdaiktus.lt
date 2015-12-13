<?php if($login->isUserLoggedIn() == false) return; if(!isAdmin()) return;

$rights = array(
	0 => 'Poreikiai',
	1 => 'Darbai',
	2 => 'Geradariai',
	3 => 'Stokojantieji',
	4 => 'Statistika',
	5 => 'Atsarg. kopija',
	6 => 'Puslapiai',
	7 => 'Antraštės',
	8 => 'Naujienos',
);



?>
<table class="admintbl nomargin ritable">

<thead>
<tr>
<th class="fc-100">Grupė</th>
<th>Teisės</th>
</tr>
</thead>

<?php
if( isset($_POST['acctype']) ) :
	$code = '';
	foreach($rights as $key => $ri) $code .= (isset($_POST['ri'.$key]) ? 1 : 0);
	updateFieldWhere('acctypes', 'rights', $code, "acctype = ".$_POST['acctype']);
	redirect();
endif;

foreach(listData('acctypes', 'acctype != 1 and acctype != 2 and acctype != 3') as $data) :
	echo '<tr>';
	echo '<td>'.$data['name'].'</td>';
	$ristr = $data['rights'];
	echo '<td><form action="" method="POST">';
	echo '<input type="hidden" name="acctype" value="'.$data['acctype'].'" />';
	foreach($rights as $key => $ri) echo '<span class="label"><input style="width: auto;" '.($ristr[$key] == 1 ? 'checked="checked"' : '').' type="checkbox" name="ri'.$key.'" />'.$ri.'</span>&nbsp;&nbsp;';
	echo '<br><input type="submit" name="upd" value="Nustatyti"></form></td>';
	echo '</tr>';
endforeach;
?>
</table>

<?php
if(isset($_POST['gr'])) :
	mysqli_query($con, "INSERT INTO acctypes (name) VALUES ('".$_POST['gr']."')");
	$aid = mysqli_insert_id($con);
	updateFieldWhere('acctypes', 'rights', '000000000', "acctype = ".$aid);
	updateFieldWhere('acctypes', 'rights1', '0000000000000', "acctype = ".$aid);
	updateFieldWhere('acctypes', 'rights2', '0000000', "acctype = ".$aid);
	redirect();
endif;
?>
<br></br>
<form action="" method="POST">
Nauja grupė <input type="text" name="gr" value="" maxlength="16" />
<input type="submit" value="Sukurti" />
</form>

<br><hr><br>
<?php
if(isset($_POST['setacctype'])) :
	updateFieldWhere('users', 'user_temp_acctype', $_POST['setacctype'], "user_id = ".CUSER);
	redirect(0, '/');
endif;
?>
<form action="" method="POST">
Įgauti teises 
<select name="setacctype">
<?php
$atarr = array(1 => 'Kuratorius', 2 => 'Tinklo atstovas', 3 => 'Administratorius');
		foreach(listData('acctypes', 'acctype != 1 and acctype != 2 and acctype != 3') as $acc) $atarr[$acc['acctype']] = $acc['name'];
foreach ($atarr as $key => $at) echo '<option value="'.$key.'">'.$at.'</option>';
?>
</select>
<input type="submit" value="Įgauti" />
</form>