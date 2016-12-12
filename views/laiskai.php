<?php if($login->isUserLoggedIn() == false) return; if(!isAdmin()) return;
?>
<script src="/ckeditor/ckeditor.js"></script>
<?php
if(isset($_POST['send'])) :
	if( !isset($_POST['recip']) or !isset($_POST['from']) or !isset($_POST['subject']) or !isset($_POST['message']) ) :
		err('Užpildykite visus laukelius');
	elseif($_POST['from'] == '') :
		err('Užpildykite laukelį <i>Nuo</i>');
	elseif($_POST['subject'] == '') :
		err('Užpildykite laukelį <i>Tema</i>');
	elseif($_POST['message'] == '') :
		err('Įveskite laiško tekstą');
	else :
		$q = '';
		switch($_POST['recip']) :
			case 0 : $q = 'SELECT user_email FROM users WHERE user_acctype = 3 AND user_active = 1 AND user_subscribed = 1'; break;
			case 1 : $q = 'SELECT user_email FROM users WHERE user_acctype = 3 AND user_active = 1'; break;
			case 2 : $q = 'SELECT user_email FROM users WHERE user_acctype = 2 AND user_active = 1 AND user_subscribed = 1'; break;
			case 3 : $q = 'SELECT user_email FROM users WHERE user_acctype = 2 AND user_active = 1'; break;
			case 4 : $q = 'SELECT user_email FROM users WHERE user_acctype = 1 AND user_active = 1 AND user_subscribed = 1'; break;
			case 5 : $q = 'SELECT user_email FROM users WHERE user_acctype = 1 AND user_active = 1'; break;
			case 6 : $q = 'SELECT user_email FROM users WHERE user_acctype = 0 AND user_active = 1 AND user_subscribed = 1'; break;
			case 7 : $q = 'SELECT user_email FROM users WHERE user_acctype = 0 AND user_active = 1'; break;
			case 8 : $q = 'SELECT user_email FROM needy WHERE deleted = 0'; break;
			case 9 : $q = '(SELECT user_email FROM needs WHERE need_type = 1 AND need_full = 1 AND user_subscribed = 1) UNION (SELECT users.user_email AS user_email FROM needs LEFT JOIN users ON needs.need_full_user = users.user_id WHERE need_type = 1 AND users.user_subscribed = 1 AND users.user_active = 1)'; break;
			case 10 : $q = '(SELECT user_email FROM needs WHERE need_type = 2 AND need_full = 1 AND user_subscribed = 1) UNION (SELECT users.user_email AS user_email FROM needs LEFT JOIN users ON needs.need_full_user = users.user_id WHERE need_type = 2 AND users.user_subscribed = 1 AND users.user_active = 1)'; break;
		endswitch;

		$recips = array();
		foreach(listData(false, false, false, $q) as $rec) if($rec['user_email'] != '') $recips[] = $rec['user_email'];
		$amount = count($recips);
        $report = array('success' => array(), 'fail' => array());
        if ($amount > 0) {
            foreach($recips as $receip) {
                $successfullySent = myMail($receip, $_POST['subject'], $_POST['message'], $_POST['from']);
                if($successfullySent) {
                    $report['success'][$receip] = $receip;
                } else {
                    $report['fail'][$receip] = $receip;
                }
            }
        }


        if(!empty($report['fail'])) {
            $notice = 'Laiškas neišsiųstas šiems ' . count($report['fail']) . ' gavėjams:<br />' . implode(', ', $report['fail']);
            err($notice, 'red');
        }
        if(!empty($report['success'])) {
            $notice = 'Laiškas išsiųstas šiems ' . count($report['success']) . ' gavėjams:<br />' . implode(', ', $report['success']);
            err($notice, 'green');
        }

        if(empty($report['fail']) && empty($report['success'])) {
            $notice = 'Nėra gavėjų';
            err($notice, 'red');
        }

	endif;
endif;

?>
<form action="" method="POST">
	<label>Gavėjai</label>
	<select name="recip">
		<option value="0">Administratoriai (visi)</option>
		<option value="1">Administratoriai (prenumeratoriai)</option>
		<option value="2">Tinklo atstovai (visi)</option>
		<option value="3">Tinklo atstovai (prenumeratoriai)</option>
		<option value="4">Kuratoriai (visi)</option>
		<option value="5">Kuratoriai (prenumeratoriai)</option>
		<option value="6">Reg. dovanotojai / geradariai (visi)</option>
		<option value="7">Reg. dovanotojai / geradariai (prenumeratoriai)</option>
		<option value="8">Stokojantieji</option>
		<option value="9">Reg. + neregistruoti geradariai (prenumeratoriai)</option>
		<option value="10">Reg. + neregistruoti dovanotojai (prenumeratoriai)</option>
	</select> <i>Laiškai nesiunčiami neaktyviems vartotojams</i>
	<br>
	<label>Nuo</label> <input type="text" name="from" value="<?php if(isset($_POST['from'])) echo $_POST['from']; else echo 'noreply@aukokdaiktus.lt'; ?>" />
	<br>
	<label>Tema</label> <input type="text" name="subject" value="<?php if(isset($_POST['subject'])) echo $_POST['subject']; ?>" />
	<br>
	<br>
	<textarea id="editor1" name="message"><?php if(isset($_POST['message'])) echo $_POST['message']; ?></textarea>
	<script>
		CKEDITOR.replace( 'editor1', {
		uiColor: '#ffffff', language: 'lt',
		filebrowserUploadUrl: '/ckupload.php',
		height: '270px',
		});

	</script>
	<br>
	<input type="submit" name="send" value="Siųsti" />
</form>