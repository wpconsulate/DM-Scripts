<style>
#msg-wrap{
    border: 1px solid;
    height: auto;
    margin:10px auto;
    margin-left: 14px;
    text-align: center;
    vertical-align: middle;
    width: 99%;
    font-family:arial helvetica;
    font-size:14px;
    color:#CC0000;
    border-radius:5px;
    background:#FFEBE8;
    font-weight:bold;
   }
#msg-wrap2{
    border: 1px solid;
    height: auto;
    margin:10px auto;
    margin-left: 14px;
    text-align: center;
    vertical-align: middle;
    width: 99%;
    font-family:arial helvetica;
    font-size:14px;
    color:#333;
    border-radius:5px;
    background:#EEFFEE;
     font-weight:bold;
    color: #499049;
   }

</style>
<?php
global $blog_id;
	if(!empty($err_message)){

		echo "<div id='msg-wrap'>";
		echo "<p>$err_message</p>";
		echo "</div>";

	}else if(!empty($message)){

		echo "<div id='msg-wrap2'>";
		echo "<p>$message</p>";
		echo "</div>";

	}
?>

<div>
<div align="center"> Redirecting to messages...</div>
<meta http-equiv="refresh" content="2;url=<?php echo get_site_url().'/wp-admin/admin.php?page=mvc_campaigns-message'; ?>">
