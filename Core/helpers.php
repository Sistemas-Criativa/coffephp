<?php

<<<<<<< HEAD
include_once('Components/route.php');
include_once('Components/config.php');
include_once('Components/session.php');
include_once('Components/redirect.php');
include_once('Components/token.php');
include_once('Components/view.php');
include_once('Components/including.php');
=======
include_once('Helpers/route.php');
include_once('Helpers/config.php');
include_once('Helpers/session.php');
include_once('Helpers/redirect.php');
include_once('Helpers/token.php');
include_once('Helpers/view.php');
include_once('Helpers/including.php');
include_once('Helpers/request.php');
>>>>>>> 7ccd8bd... Add helper to response

function pre($param)
{
	echo '<pre>';
	var_dump($param);
	echo '</pre>';
	exit;
}
