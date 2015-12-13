<?php
include("functions.php");
include("views/cities.php");

if(isset($_POST['getRegionChild']) and isset($regionsListChildren[$_POST['getRegionChild']])) :
	foreach($regionsListChildren[$_POST['getRegionChild']] as $children) :
		echo '<option value="'.$children.'">'.$citiesList[$children].'</option>';
	endforeach;
endif;

if(isset($_POST['getCatChild']) and isset($_POST['excludeCat'])) :
	$ops = listData('cats', 'deleted = 0 AND cat_level = 1 AND cat_parent = '.$_POST['getCatChild']);
	foreach($ops as $op) :
		if($_POST['excludeCat'] != $op[0]) echo '<option value="'.$op[0].'">'.$op['cat_name'].'</option>';
	endforeach;
elseif(isset($_POST['getCatChild'])) :
	$ops = listData('cats', 'deleted = 0 AND cat_level = 1 AND cat_parent = '.$_POST['getCatChild']);
	foreach($ops as $op) :
		echo '<option value="'.$op[0].'">'.$op['cat_name'].'</option>';
	endforeach;
endif;


?>