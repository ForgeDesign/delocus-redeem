<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
// phpinfo();
$user = "root";
$password = 'root';
// $password = 'SNfGlu5tNdKfD5LM';
$db = 'delocus_redemption';
$host = 'localhost';
$port = 3307;
$link = mysqli_connect($host, $user, $password, $db, $port);

if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
// return json_encode($_POST);
// die();
// $expiration_date = htmlspecialchars($_POST['expiration_date']) ;

// get post variables
$data = json_decode(file_get_contents("php://input"));

// extract restaurant name from image url parameter
$restaurant_name =  substr($data->image_url, 47);
$restaurant_name =  substr($restaurant_name, 0, 5);

// $data = json_decode($data);
$file = 'people.txt';
// Open the file to get existing content
$current = file_get_contents($file);
// Append data to the file
$current .= $data->email;
// Write the contents back to the file
file_put_contents($file, $current);


// die();
$sql_insert = "INSERT INTO `coupon_history`(`expiration_date`, `cashier_redeemed_status`, `image_url`, `email`, `restaurant`) VALUES ('" . $data->expiration_date . "', 'true', '"   . $data->image_url . "', '" . $data->email . "', '" . $restaurant_name . "')";
$result = $link->query($sql_insert);



$dateStr = date('Y-m-d', strtotime("last Saturday"));
$company = "DeepHire";
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
