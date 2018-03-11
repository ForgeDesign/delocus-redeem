<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
// phpinfo();
$user = "root";
$password = 'lolipop123';
// $password = 'SNfGlu5tNdKfD5LM';
$db = 'delocus_redemption';
$host = 'localhost';
$port = 8889;
$link = mysqli_connect($host, $user, $password, $db, $port);

if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
// $expiration_date = htmlspecialchars($_POST['expiration_date']) ;

// get post variables
$data = json_decode(file_get_contents("php://input"));

$file = 'people.txt';
// Open the file to get existing content
$current = file_get_contents($file);
// Append data to the file
$current .= "hello";
// $current .= $data->$result;
// Write the contents back to the file
file_put_contents($file, $current);
// return json_encode($_POST);
 //die();
// extract restaurant name from image url parameter

$image_url =  $data->image_url;

$search_character = '*';
$parts = explode($search_character, $image_url);
$restaurant_name = $parts[1];
// $data = json_decode($data);


// die();
$sql_insert = "INSERT INTO `coupon_history`(`expiration_date`, `cashier_redeemed_status`, `image_url`, `email`, `restaurant`) VALUES ('" . $data->expiration_date . "', 'true', '"   . $data->image_url . "', '" . $data->email . "', '" . $restaurant_name . "')";

$result = $link->query($sql_insert);



$dateStr = date('Y-m-d', strtotime("last Saturday"));
$company = $restaurant_name;
$sql_check_exists = "SELECT id FROM weekly_redeems WHERE Company_Name ='".$company."' AND Week ='".$dateStr."'";




$result2 = $link->query($sql_check_exists);

if ($result2->num_rows > 0) {

     $id = ($result2->fetch_assoc()['id']);
     echo($id);
  $sql_increment_coupon = "UPDATE weekly_redeems 
  SET Coupons_Redeemed = Coupons_Redeemed + 1 
  WHERE id = '".$id."'";
  $result3 = $link->query($sql_increment_coupon);


    
}
else {
    echo("0");
    $insert_new_date_or_company = "INSERT INTO `weekly_redeems` (`Week`, `Coupons_Redeemed`, `Company_Name`) VALUES ('".$dateStr."', 1, '".$company."')";

$result4 = $link->query($insert_new_date_or_company);

}

// echo($dateStr);
return("success");

?>
