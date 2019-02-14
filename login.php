<?
include 'db.php';

function doHackPrevent($reason)
{
    // here you can do some action for hacking prevent
}

$sqlatt = mysqli_query($con, "select id, balance, login, att,ip from login where id = 1");

if ((!$sqlatt) || (mysqli_num_rows($sqlatt) == '0')) {
    echo 'Error';
    exit;
} else {
    $rwatt = mysqli_fetch_array($sqlatt);
}

$userip = htmlspecialchars($_SERVER['REMOTE_ADDR']);

//check attempts;
if ($rwatt['att'] == '5') {
    $msg = '';
} elseif ($rwatt['att'] == '0') {
    doHackPrevent('No attempts, IP:' . $userip);
    exit;
} else {
    $msg = 'You have ' . $rwatt['att'] . ' attempts';
}

// check ips
if ($rwatt['ip'] != 'all') {
    $ips = explode(',', $ip);
    if (!in_array($userip, $ips)) {
        doHackPrevent('wrong IP:' . $userip);
        exit;
    }
}

if (isset($_POST['dologin']) && $_POST['dologin'] == '1') {
    $login = strip_tags(sanitize($_POST['login']));
    $pass  = strip_tags(sanitize($_POST['pass']));
    
    $passmd5 = md5($pass);
    
    $sqlLogin = mysqli_query($con, "select * from `login` where `login` = '" . mysqli_real_escape_string($con, $login) . "' and `pass` = '" . mysqli_real_escape_string($con, $passmd5) . "' and `id` = '1'");
    if (mysqli_num_rows($sqlLogin) == '0') {
        $att = intval($rwatt['att']);
        $att--;
        mysqli_query($con, "update `login` set `att` = '" . $att . "' where `id` = '1'");
        $msg = $msg . '<br>INCORRECT USERNAME or PASSWORD';
    } else {
        $_SESSION['start'] = '1';
        mysqli_query($con, "update `login` set `att` = '5' where `id` = '1'");
        $_SESSION['token']    = md5(uniqid(mt_rand()));
        $_SESSION['login']    = $rwatt['login'];
        $_SESSION['login_id'] = $rwatt['id'];
        header("Location: index.php?token=" . $_SESSION['token'] . "");
        exit;
    }
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>App</title>
</head>
<body>
	<form id="form1" name="form1" method="post">
    <span>
		Auth page 
		    <p><?=$msg?></p>
	</span>

    <span>
		Username
	</span>
    <div data-validate="Username is required">
        <input type="text" name="login" required id="login">
    </div>

    <span>
		Password
	</span>
    <div data-validate="Password is required">
        <input type="password" required name="pass" id="pass">
    </div>
    <input name="dologin" type="hidden" id="dologin" value="1">
    <div>
        <button name="submit" id="submit">
            Login
        </button>
    </div>
</form>
</body>
</html>

