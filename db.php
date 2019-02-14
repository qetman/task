<? 
$con = mysqli_connect('localhost','qetman_task','qetman_task');
mysqli_select_db($con, 'qetman_task');
mysqli_query($con,"set names `utf8`");

define('MIN_MONEY', 1);
define('MAX_MONEY', 10);
define('MIN_POINT', 1);
define('MAX_POINT', 100);
define('COEFFICIENT', 10);
?>