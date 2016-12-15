<?php if($login->isUserLoggedIn() == false) return;  if(!isAdmin() and !(isCustom() and haveRight(6))) return;

?>
<script src="/ckeditor/ckeditor.js"></script>
<?php

if(isset($_POST['newpage']) or isset($_POST['savepage'])) :
		$page_name = (isset($_POST['page_name']) ? mysqli_real_escape_string($con, $_POST['page_name']) : '');
		$page_content = (isset($_POST['page_content']) ? mysqli_real_escape_string($con, $_POST['page_content']) : '');
		$page_slug = (isset($_POST['page_slug']) ? mysqli_real_escape_string($con, $_POST['page_slug']) : '');
		$page_site = (isset($_POST['page_site']) ? mysqli_real_escape_string($con, $_POST['page_site']) : 0);
		$page_modified = date('Y-m-d H:i:s');
		$page_author = CUSER;

		$page_slug = prettyslug($page_slug);
		
		if(
			$page_name == '' or
			$page_slug == ''
		) :
			err("Užpildykite visus reikalaujamus laukelius", 'red');
		elseif(isset($_POST['newpage']) and countData('pages', "page_slug = '$page_slug' AND page_site = $page_site") > 0) :
			err("Tokia svetainės ir santrumpos kombinacija jau yra", 'red');
		elseif(isset($_POST['savepage']) and countData('pages', "page_slug = '$page_slug' AND page_site = $page_site AND page_id != ".$_GET['page']) > 0) :
			err("Tokią svetainės ir santrumpos kombinaciją jau turi kitas puslapis", 'red');
		else :
			if(isset($_POST['newpage'])) :
				$id = insertRow('pages',
				'page_type, page_name, page_content, page_modified, page_author, page_slug, page_site',
				"0, '$page_name', '$page_content', '$page_modified', $page_author, '$page_slug', $page_site", false
				);
				
				if($id) :
					redirect(0, '?p='.page().'&page='.$id);
				else : err('Puslapis nesukurtas', 'red');
				endif;
			elseif(isset($_POST['savepage']) and isset($_GET['page'])) :
				
				updateFieldWhere('pages', 'page_name', $page_name, 'page_id = '.$_GET['page']);
				updateFieldWhere('pages', 'page_content', $page_content, 'page_id = '.$_GET['page']);
				updateFieldWhere('pages', 'page_modified', $page_modified, 'page_id = '.$_GET['page']);
				updateFieldWhere('pages', 'page_author', $page_author, 'page_id = '.$_GET['page']);
				updateFieldWhere('pages', 'page_slug', $page_slug, 'page_id = '.$_GET['page']);
				updateFieldWhere('pages', 'page_site', $page_site, 'page_id = '.$_GET['page']);
			else :
				
			endif;
			
		endif;
		
endif;

if(!isset($_GET['page']) or $_GET['page'] == 0) :	
	?>
	<form action="" method="post">	
		<label>Pavadinimas<span class="reqfield">*</span></label> <input type="text" value="" name="page_name" maxlength="256" required="required" /><br>
		<label>Santrumpa<span class="reqfield">*</span></label> <input type="text" value="" name="page_slug" maxlength="256" required="required" />
		<label>Svetainė<span class="reqfield">*</span></label> <select name="page_site">
			<option value="0">aukoklaika.lt</option>
			<option value="1">aukokdaiktus.lt</option>
			<option value="2">abi</option>
		</select><br>
		<br>
		<textarea id="editor1" name="page_content"></textarea>
		<script>
			CKEDITOR.replace( 'editor1', {
			uiColor: '#ffffff', language: 'lt',
			filebrowserUploadUrl: '/ckupload.php',
			height: '350px',
			});

		</script>
		<br>
		<input type="submit" value="Sukurti" name="newpage" />
	</form>
	<?php

	return;
endif;

$us = $_GET['page'];

if(countData('pages', "page_id = '$us'") == 0) {err('Toks poreikis neegzistuoja', 'red'); return;}

// Edit

$usermeta = getRow('pages', 'page_id = '.$us); ?>
<form action="" method="post">
<?php

// Title
echo '<div class="edit_header_group noborder">';
echo '<div class="name single">Puslapis: <input type="text" value="'.$usermeta['page_name'].'" name="page_name" maxlength="256" required="required" /></div>';
echo 'Modifikavimo data: '.$usermeta['page_modified'];
echo ' &middot; Santrumpa <input type="text" value="'.$usermeta['page_slug'].'" name="page_slug" maxlength="256" required="required" />
&middot; Svetainė<span class="reqfield"> <select name="page_site">
			<option value="0"'.($usermeta['page_site'] == 0 ? ' selected="selected"' : '').'>aukoklaika.lt</option>
			<option value="1"'.($usermeta['page_site'] == 1 ? ' selected="selected"' : '').'>aukokdaiktus.lt</option>
			<option value="2"'.($usermeta['page_site'] == 2 ? ' selected="selected"' : '').'>abi</option>
		</select>';
echo '</div>';

?>
	<textarea id="editor1" name="page_content"><?php echo $usermeta['page_content']; ?></textarea>
	<script>
		CKEDITOR.stylesSet.add('my_custom_style', [
			{ name: 'Antraštė 2 be virš. p.', element: 'h2', styles: { 'margin-top': '0'} }
		]);
	
		CKEDITOR.replace( 'editor1', {
			uiColor: '#ffffff', language: 'lt',
			filebrowserUploadUrl: '/ckupload.php',
			height: '350px',
			stylesSet: 'my_custom_style'
		});

	</script>
	<br>
	<input type="submit" value="Išsaugoti" name="savepage" />
</form>
	<?php
?>
