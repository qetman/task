<?
session_start();
include 'functions.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$domain = "http://".htmlspecialchars($_SERVER['SERVER_NAME'])."/";

if((isset($_GET['logout'])) && ($_GET['logout'] == '1')){
	unset($_SESSION['start']);
	unset($_SESSION['token']);
	session_destroy();
	header("Location: /");
	exit;
}
if(empty($_SESSION['start'])){
		include 'login.php';
		exit;
}

$tk = $_REQUEST['token'];
if($tk != $_SESSION['token']){
	unset($_SESSION['start']);
	unset($_SESSION['login']);
	unset($_SESSION['login_id']);
	unset($_SESSION['token']);
	unset($_SESSION['checking']);
	session_destroy();
	header("Location: /");
	exit;
}else{
	include 'db.php';
}
?>