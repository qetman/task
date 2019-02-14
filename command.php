<?include 'db.php';


if (isset($argv[1]) && is_numeric($argv[1])) {
	$count = (int)$argv[1];
} else {
	$count = 1;
}

  echo "Starting...\n";
  echo "Try to send ".$count. " trancasction(s) if exist with status 0\n";

  $gettrancastions = mysqli_query($con, "select * from trancastions where status = 0  order by id asc LIMIT " . mysqli_real_escape_string($con,$count) . "");

  $y = 0;
  while ($result = mysqli_fetch_array($gettrancastions,MYSQLI_ASSOC)) {
	$y++;
	// here API request 
	$answer_from_api = true;
	// end API request
	if ($answer_from_api == true) {
   		 mysqli_query($con, "UPDATE trancastions SET status = 1 where id = " . mysqli_real_escape_string($con, $result["id"]) . "");
   		 echo $result["amount"]. " usd was send to user# ".$result["user_id"] ." bank account\n";
	}
    
  }

  if ($y > 0) {
  	echo $y." trancastions was sent.\n";
  } else {
	echo "No trancastions to sent.\n";
  }
  echo "End.\n";

?>