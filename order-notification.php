<?php include_once('includes/crud.php');
$db = new Database();
$db->connect();
$db->sql("SET NAMES 'utf8'");

include('includes/variables.php');
include_once('includes/custom-functions.php');
include_once('includes/functions.php');
$fn = new custom_functions;
$permissions = $fn->get_permissions($_SESSION['id']);
$config = $fn->get_configurations();
if (isset($config['system_timezone']) && isset($config['system_timezone_gmt'])) {
    date_default_timezone_set($config['system_timezone']);
    $db->sql("SET `time_zone` = '" . $config['system_timezone_gmt'] . "'");
} else {
    date_default_timezone_set('Asia/Kolkata');
    $db->sql("SET `time_zone` = '+05:30'");
}

    $now = date("Y-m-d h:i:sa");
    $query = "select * from orders";
    $db->sql($query);
    $result = $db->getResult();
    $count = $db->numRows($result);

/*echo "<h3>$count</h3>
<p>Total Orders</p> <audio controls autoplay hidden='true'>
<source src='http://www.w3schools.com/html/horse.ogg' type='audio/ogg'>
</audio>";exit;*/
echo "<script>alert('new order received');</script>";
 echo "<audio controls autoplay hidden='true'>
<source src='http://www.w3schools.com/html/horse.ogg' type='audio/ogg'>
</audio>";
exit;
?>