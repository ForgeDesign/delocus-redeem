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
// extract restaurant name from image url parameter

$search_character = 'M-';
$expiration_date ="";
    if (isset($_GET['expiration_date']))
         $expiration_date= htmlspecialchars($_GET['expiration_date']);
$email="";
    if (isset($_GET['email']))
         $email = htmlspecialchars($_GET['email']);
$image_url ="";
$restaurant_name = "";
    if (isset($_GET['image_url'])) {
         $image_url = htmlspecialchars($_GET['image_url']);
        $parts = explode($search_character, $image_url);
    }
$restaurant_name = $parts[1];
//echo $restaurant_name;
//$restaurant_name = $parts[1];
//$image_url = htmlspecialchars($_GET['image_url']);
//$restaurant_name = "";  

// echo $restaurant_name;


// check database list to see if we should display the PRINT ONLY message
$sql_check_print_only = "SELECT * FROM `print_only_restaurants` WHERE restaurant_name='" . $restaurant_name . "'";
// echo $sql_check_print_only;

$result_print_only = $link->query($sql_check_print_only);
// echo $result_print_only
$show_button = true;
$show_print_button = false;
$main_message = "Please have your waiter or cashier press to confirm your coupon.";
$sub_message = "Coupon expires: " . $expiration_date;

if ($result_print_only->num_rows > 0) {
	$main_message = "<b>This is a print only coupon!</b> Please print and show the coupon to the waiter/cashier.";
	$show_button = false;
    $show_print_button = true;
} else {
//    echo "<br> 0 results, show redeem digitally coupon stuff";
}

// var_dump($result_print_only);





// $expiration_date = htmlspecialchars($_GET['expiration_date']) ;
// echo $expiration_date;
date_default_timezone_set('America/Phoenix');

// 12/10/2018 Ivan reported an error where the expiration date being passed by wordpress had Eastern STandard Time as the timezone. in our time comparisons, the comparison expects EST as the timezone. so we are searching for occurrences of the string Eastern Standard Time and replacing it to be EST. the system now works like normal

$test =  str_replace("Eastern Standard Time", "EST", $expiration_date);
//$jsDateTS = strtotime($expiration_date);
$jsDateTS = strtotime($test);
//echo $jsDateTS;
$today = strtotime(date("Y-m-d H:i:s"));


// TODO: perform calulcation to set is_expired and is_redeemed
$is_expired = false;
$is_redeemed = false;



$sql = "SELECT * FROM coupon_history WHERE email='" . $email . "' AND image_url='" . $image_url . "' AND expiration_date='" . $expiration_date . "'";
// echo $sql;
// echo $sql;
$result = $link->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["expiration_date"]. " " . $row["cashier_redeemed_status"]. " " . $row["image_url"] .  "<br>";
        if($row["cashier_redeemed_status"] == 'true'){
            $is_redeemed = true;
        }
    }
} else {
    // echo "<br> 0 results";
}


// echo $restaurant_name;
// echo $sql_insert;
$showCoupon = true;

if ( (time() -  $jsDateTS) > 0) {
    $is_expired = true;
}
if ((time() -  $jsDateTS) < 0) {
    $is_expired = false;
}



if ($is_expired || $is_redeemed){
    $main_message = "This coupon has been redeemed, or it is expired! ";
    $sub_message = "Check out <a href=https://delocus.com> Delocus </a> for more great deals!";
    $show_button = false;
}



mysqli_close($link);


?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Delocus - Redeem Your Coupon</title>
  </head>
  <body>
  <div style="background:#FFFFFF !important" class="jumbotron jumbotron-fluid">
  <div class="container text-center">
    <h1 class="display-4">Redeem Coupon</h1>
    <p class="lead"><?php echo $main_message; ?></p>
    <p class="lead"><?php echo $sub_message; ?> </b></p>
     <?php if($show_button){
        echo '<button type="button" onclick="updateCoupon()" class="btn btn-success btn-lg">CASHIER </br> CLICK TO REDEEM</button> ';
} else {
    if($show_print_button){
        echo '<button type="button" onclick="printCoupon()" class="btn btn-success btn-lg">CLICK TO PRINT</button> ';
    }
}?>


  </div>
</div>
    <div class="container text-center">
    <br> 
    <?php if(!$is_expired && !$is_redeemed){
        echo "<img src=" . htmlspecialchars($_GET['image_url']) . " />";
    } 
    ?>
    
</div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script>
    function GetURLParameter(sParam) {
      var sPageURL = window.location.search.substring(1);
      var sURLVariables = sPageURL.split('&');
      for (var i = 0; i < sURLVariables.length; i++) {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam) {
          return sParameterName[1];
        }
      }
    }
    // find %20, %40 in a string and replaces with a ' ' and '@' respectively
    function CleanVariable(res) {
      var res = res.replace(/%20/g, " ");
      var res = res.replace(/%40/g, "@");
      return (res);

    }
  </script>

    <script>
        var email = CleanVariable(GetURLParameter("email"));
    // var name = CleanVariable(GetURLParameter("name"));
    var expiration_date = CleanVariable(GetURLParameter("expiration_date"));
    var image_url = CleanVariable(GetURLParameter("image_url"));
    // var address = CleanVariable(GetURLParameter("address"));

    var profileInformation = {
      "email": email,
    //   "name": name,
      "expiration_date": expiration_date,
      "image_url": image_url,
    }
    // alert(JSON.stringify(profileInformation))
    // set the friendly greeting in header
    // $("#greetingName").text(name);
    function printCoupon(){
     window.print();
   /* 
    $.ajax({
      type: "POST",
      url: "/delocus-redeem/test.php",
      // The key needs to match your method's input parameter (case-sensitive).
      data: JSON.stringify(
        profileInformation
      ),
      error: function(data) {
          console.log("error");
        console.log(data);
     window.print();

      },
      success: function(data) {
          console.log("success");
        console.log(data);
     window.print();

      },

      contentType: "application/json",
      dataType: "json"
          
        }
        );
    */
    }

    function updateCoupon(){
         //alert(JSON.stringify(
         //profileInformation
       //));
    $.ajax({
      type: "POST",
      url: "/delocus-redeem/test.php",
      // The key needs to match your method's input parameter (case-sensitive).
      data: JSON.stringify(
        profileInformation
      ),
      error: function(data) {
          console.log("error");
        console.log(data);
        window.location.reload();

      },
      success: function(data) {
          console.log("success");
        console.log(data);
        window.location.reload();

      },

      contentType: "application/json",
      dataType: "json"
          
        }
        );

    }
    </script>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>
