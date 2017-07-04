<?php
include("../../../../../wp-config.php");
global $wpdb;

$data= implode("--",$_POST);
$data.="___";
$data.=implode(array_keys($_POST));
$data.="___";
$data .= implode("--",$_POST["category"]);
$data.="___";
$data.=implode(array_keys($_POST["category"]));

$event          =       $_POST["event"];
$email          =       $_POST["email"];
$category       =       $_POST["category"][0];
$list           =       $_POST["category"][1];
$campaign       =       $_POST["category"][2];
$property       =       $_POST["category"][3];
$date           =       date("Y-m-d",$_POST["timestamp"]);
$time           =       date("G:i:s",$_POST["timestamp"]);
$sql            =       "INSERT INTO ".$wpdb->prefix."sendgrid_status (`event`,`email`,`category`,`list`,`campaign`,`property`,`date`,`time`) VALUES ('$event','$email','$category','$list','$campaign','$property','$date','$time')";
echo $sql;
echo $wpdb->query($sql);

?>
