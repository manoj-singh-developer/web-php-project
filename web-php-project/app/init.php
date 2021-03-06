<?php 
session_start();

$GLOBALS['config'] = array(
	'mysql' => array(
		'host' => '127.0.0.1',
		'username' => 'root',
		'password' => '',
		'db' => 'sistema_seguridad_administracion'
	),
	'session' => array(
		'session_name' => 'user'
	),
	'SITE_URL' => 'http://localhost/github/web-php-project/web-php-project/public/'
);

spl_autoload_register(function($class) {
	require_once '../app/core/utils/' . $class . '.php';
});

require_once '../app/config/Config.php';
require_once '../app/core/db/DB.php';
require_once '../app/core/utils/Sanitize.php';
require_once '../app/core/validators/Validation.php';

require_once '../app/models/User.php';

require_once 'core/App.php';
require_once 'core/Controller.php';

?>