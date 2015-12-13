<?php if($login->isUserLoggedIn() == false) return; if(!isAdmin()) return;

$rights1 = array(
	0 => 'ID',
	1 => 'Grupė',
	2 => 'Aktyvacija',
	3 => 'Vardas',
	4 => 'Pavardė',
	5 => 'Adresas',
	6 => 'Apskritis',
	7 => 'Savivaldybė',
	8 => 'Telefonas',
	9 => 'Organizacija',
	10 => 'Apie',
	11 => 'Miniatiūra',
	12 => 'Prenumerata',
);

$rights2 = array(
	0 => 'ID',
	1 => 'Stokojantysis',
	2 => 'Kategorija',
	3 => 'Pavadinimas',
	4 => 'Aprašymas',
	5 => 'Galioja',
	6 => 'Modifikuotas',
);

?>
<table class="admintbl nomargin ritable">

<thead>
<tr>
<th class="fc-100">Grupė</td>
<th>Matomi laukai</th>
</tr>
</thead>

<?php
if( isset($_POST['acctype']) ) :
	$code1 = $code2 = '';
	foreach($rights1 as $key => $ri) $code1 .= (isset($_POST['ri1'.$key]) ? 1 : 0);
	foreach($rights2 as $key => $ri) $code2 .= (isset($_POST['ri2'.$key]) ? 1 : 0);
	updateFieldWhere('acctypes', 'rights1', $code1, "acctype = ".$_POST['acctype']);
	updateFieldWhere('acctypes', 'rights2', $code2, "acctype = ".$_POST['acctype']);
	redirect();
endif;

foreach(listData('acctypes', '1') as $data) :
	echo '<tr>';
	echo '<td>'.$data['name'].'</td>';
	$ristr = $data['rights1'];
	echo '<td><form action="" method="POST">';
	echo '<input type="hidden" name="acctype" value="'.$data['acctype'].'" />';
	echo 'Vartotojų lentelėje<br>';
	$ristr = $data['rights1'];
	foreach($rights1 as $key => $ri) echo '<span class="label"><input style="width: auto;" '.($ristr[$key] == 1 ? 'checked="checked"' : '').' type="checkbox" name="ri1'.$key.'" />'.$ri.'</span>&nbsp;&nbsp;';
	echo '<br>';
	echo 'Poreikių lentelėje<br>';
	$ristr = $data['rights2'];
	foreach($rights2 as $key => $ri) echo '<span class="label"><input style="width: auto;" '.($ristr[$key] == 1 ? 'checked="checked"' : '').' type="checkbox" name="ri2'.$key.'" />'.$ri.'</span>&nbsp;&nbsp;';
	echo '<br><input type="submit" name="upd" value="Nustatyti"></form></td>';
	echo '</tr>';
endforeach;
?>
</table>