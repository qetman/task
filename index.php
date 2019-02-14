<?include 'header.php';?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>App</title>
</head>
<body>
<?
echo 'Hi, ' . ucfirst($_SESSION['login']) . '! ';
echo '<a href="?logout=1">Click to exit</a><br><br>';
echo '<a href="?gift=1&token=' . $_SESSION['token'] .'">Start wheel of fortune</a><br><br>';

if (isset($_GET["gift"]) && (int) $_GET["gift"] == 1) {
    
    $getTypeQuery = mysqli_query($con, "select * from category where count > 0 or count = -1 order by rand() limit 1");
    $getType      = mysqli_fetch_array($getTypeQuery);
    
    echo "YOUR PRESENT:<br><br>";
    
    switch ($getType['id']) {
        case 1:
            echo $getType['name'] . ": ";
            $amount = rand(MIN_MONEY, MAX_MONEY);
            echo $amount . " usd<br><br>";
            mysqli_query($con, "UPDATE category SET count = count - 1 where id = " . mysqli_real_escape_string($con, $getType['id']) . "");
            echo '<a href="?bank=1&amount=' . $amount . '&token=' . $_SESSION['token'] . '">Transfer money</a><br>';
            $amount_points = $amount * COEFFICIENT;
            echo '<a href="?balance=1&points=' . $amount_points . '&token=' . $_SESSION['token'] . '">Convert money to points</a> (Coefficient 1:' . COEFFICIENT . ')';
            $_SESSION['checking'] = 1;
            
            break;
        case 2:
            echo $getType['name'] . ": ";
            $points = rand(MIN_POINT, MAX_POINT);
            echo $points . " points<br>";
            echo '<a href="?balance=1&points=' . $points . '&token=' . $_SESSION['token'] . '">Add points to my balance</a>';
            $_SESSION['checking'] = 1;
            break;
        case 3:
            echo $getType['name'] . ": ";
            $getProductQuery = mysqli_query($con, "select * from products order by rand() limit 1");
            mysqli_query($con, "UPDATE category SET count = count - 1 where id = " . mysqli_real_escape_string($con, $getType['id']) . "");
            $getProduct = mysqli_fetch_array($getProductQuery);
            echo $getProduct['name'] . "<br>";
            echo '<a href="?product=1&product_id=' . $getProduct['id'] . '&token=' . $_SESSION['token'] . '">Send product by mail</a>';
            $_SESSION['checking'] = 1;
            
            break;
    }
    
    if ($_SESSION['checking'] == 1) {
        echo '<br><br><a href="?cancel=1&category_id=' . $getType['id'] . '&token=' . $_SESSION['token'] . '">Cancel the present</a>';
    }
    
} elseif (isset($_GET["bank"]) && (int) $_GET["bank"] == 1) {
    
    if (isset($_SESSION['checking']) && $_SESSION['checking'] == 1) {
        
        $trancastions_amount = sanitize($_GET["amount"]);
        
        echo "Your trancastion amount " . $trancastions_amount . " usd was added to our trancastion service system";
        
        $check_updated = mysqli_query($con, "INSERT INTO `trancastions`(`user_id`, `amount`) VALUES (" . mysqli_real_escape_string($con, $_SESSION['login_id']) . ", " . mysqli_real_escape_string($con, $trancastions_amount) . ")");
        
        unset($_SESSION['checking']);
    }
    
} elseif (isset($_GET["balance"]) && (int) $_GET["balance"] == 1) {
    
    $update_balance = sanitize($_GET["points"]);
    
    if (isset($_SESSION['checking']) && $_SESSION['checking'] == 1) {
        echo "Your balance was updated: +" . $update_balance . " points";

        $check_updated = mysqli_query($con, "UPDATE login SET balance = balance + " . mysqli_real_escape_string($con, $update_balance) . " where login = '" . mysqli_real_escape_string($con, $_SESSION['login']) . "'");

        unset($_SESSION['checking']);
    }
} elseif (isset($_GET["product"]) && (int) $_GET["product"] == 1) {
    
    $product_id = (int) sanitize($_GET["product_id"]);
    
    if (isset($_SESSION['checking']) && $_SESSION['checking'] == 1) {
        
        echo "Your product was added to our postal service";
        
        $check_updated = mysqli_query($con, "INSERT INTO `postal_service`(`user_id`, `product_id`) VALUES (" . mysqli_real_escape_string($con, $_SESSION['login_id']) . ", " . mysqli_real_escape_string($con, $product_id) . ")");

        unset($_SESSION['checking']);
    }
} elseif (isset($_GET["cancel"]) && (int) $_GET["cancel"] == 1) {
    
    $category_id = (int) sanitize($_GET["category_id"]);
    
    if (isset($_SESSION['checking']) && $_SESSION['checking'] == 1) {
        
        echo "Your present was canceled.";

        mysqli_query($con, "UPDATE category SET count = count + 1 where id = " . mysqli_real_escape_string($con, $category_id) . " and id != 2");

        unset($_SESSION['checking']);
    }
}
?>
</body>
</html>