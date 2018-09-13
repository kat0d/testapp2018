<?php
function authenticate() {
	header('WWW-Authenticate: Basic realm="Any login. No password required"');
	header('HTTP/1.0 401 Unauthorized');
	echo "Вы должны ввести корректный логин и пароль для получения доступа к ресурсу \n";
	exit;
}

if (!isset($_SERVER['PHP_AUTH_USER'])|| $_SERVER['PHP_AUTH_USER'] == '' || ($_POST['SeenBefore'] == 1 && $_POST['OldAuth'] == $_SERVER['PHP_AUTH_USER'])) {
	authenticate();
} else {

	define('APP_CONST', '1');
	session_start();


	include 'db.php'; // database credentials and initialize $db as a class
	include 'loader.php'; // load other classes

	// initialize classes
	$user = new UserController();


	if($result = $db->query("SELECT login FROM Users")){
		$user_check = 0;
		foreach ($result as $value) {
			if(htmlspecialchars($_SERVER['PHP_AUTH_USER']) === $value[login]){
				$user_check .= 1;
				break;
			}
		}
		if (!$user_check) {
			$db->addUser(htmlspecialchars($_SERVER['PHP_AUTH_USER']));
		}

	}

	if( (isset($_POST['get-prize']) || isset($_POST['prize-action'])) && $_SERVER['REQUEST_METHOD'] == 'POST' ){
		include 'ajax-requests.php';
		exit;
	}

	include 'template.php';

}
?>