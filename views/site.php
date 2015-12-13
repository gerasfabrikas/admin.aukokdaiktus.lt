<?php
// include html header and display php-login message/error
include('header.php');

// show negative messages
if ($login->errors) {
    foreach ($login->errors as $error) {
        err($error, 'red');
    }
}

// show positive messages
if ($login->messages) {
    foreach ($login->messages as $message) {
        echo err($message);
    }
}

?>   

	<?php
	if(page() == 'edit' and file_exists(page().".php")) include page().".php";
	elseif(file_exists("views/".page().".php")) include "views/".page().".php";
	?>
	
<?php
// include html footer
include('footer.php');
