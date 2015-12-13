<?php if($login->isUserLoggedIn() == false) return; if(!isAdmin()  and !(isCustom() and haveRight(5)) ) return;

?>
Atsisiųskite atsarginę informacinės sistemos duomenų kopiją:<br>
<a href="/uploads/backup/pdbackup.sql" download>pdbackup.sql</a> <?php echo round(filesize(ROOT_PATH . 'uploads/backup/pdbackup.sql')/1024/1024, 3); ?> MB, paskutinį kartą atnaujinta <?php echo date('Y-m-d H:i:s', filemtime(ROOT_PATH . 'uploads/backup/pdbackup.sql')); ?>