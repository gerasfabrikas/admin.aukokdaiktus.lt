<?php if($login->isUserLoggedIn() == false) return; if(!isAdmin() and !(isCustom() and haveRight(7))) return;

if(isset($_FILES["file"]) and $_FILES["file"]["tmp_name"] != '') :
$allowedExts = array("jpeg", "jpg");
$temp = explode(".", $_FILES["file"]["name"]);
$extension = end($temp);
if ( in_array($extension, $allowedExts))
  {
  if ($_FILES["file"]["error"] > 0)
    {
    echo "Klaida: " . $_FILES["file"]["error"] . "<br>";
    }
  else
    {
      move_uploaded_file($_FILES["file"]["tmp_name"], (ROOT_PATH . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . "head" . $_POST['htype'] . ".jpg"));
    }
  }
else {err('Klaida');}
endif;

?>
<table style="width: 100%;">
<!-- Užkomentuojam, nes šiuo metu negalim uploadint`i į kitą tinklapį (pvz. iš admin.pagalbadaiktais.lt į pagalbadarbais.lt)
<tr>

<td style="vertical-align: top;">
<form action="" method="post"
enctype="multipart/form-data">
<input type="hidden" name="htype" value="1" />
<label for="file">Antraštinis failas 1 (JPG):</label><br>
<input type="file" name="file" id="file"><br>
<input type="submit" name="submit" value="Siųsti">
</form>
<br>
<img width="300" src="/uploads/head1.jpg?dummy=<?php echo date('His'); ?>" />
</td>

<td style="vertical-align: top;">
<form action="" method="post"
enctype="multipart/form-data">
<input type="hidden" name="htype" value="2" />
<label for="file">Antraštinis failas 2 (JPG):</label><br>
<input type="file" name="file" id="file"><br>
<input type="submit" name="submit" value="Siųsti">
</form>
<br>
<img width="300" src="/uploads/head2.jpg?dummy=<?php echo date('His'); ?>" />
</td>

</tr>
-->
<?php
if(isset($_POST['foot1'])) :
	updateFieldWhere('settings', 'set_val', $_POST['foot1'], "set_name = 'footer".$_POST['htype']."'");
endif;
?>
<tr>
<td style="vertical-align: top; padding-top: 30px;">
	<form action="" method="post">
		Poraštė 1:<br>
		<input type="hidden" name="htype" value="1" />
		<textarea name="foot1"><?php echo getField('settings', 'set_val', 'set_id', '1'); ?></textarea><br>
		<input type="submit" value="Išsaugoti" />
	</form>
</td>
<td style="vertical-align: top; padding-top: 30px;">
		Poraštė 2:<br>
	<form action="" method="post">
		<input type="hidden" name="htype" value="2" />
		<textarea name="foot1"><?php echo getField('settings', 'set_val', 'set_id', '2'); ?></textarea><br>
		<input type="submit" value="Išsaugoti" />
	</form>
</td>

</tr>
</table>