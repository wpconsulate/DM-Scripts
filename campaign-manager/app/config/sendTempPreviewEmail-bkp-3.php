<?php

$content = str_replace('\"', '', $content);
$serverName = 'http://somdev1.us/';
if($_SERVER['SERVER_NAME'] == 'somdev1.us'){
	$serverName = 'http://somdev2.us/';
}
echo 'SN: '.$serverName;

//$content = str_replace('../"', $serverName, $content);


	if(is_admin()){
		if(!empty($templateid)){
			global $wpdb; 
			$to = $templateid;

			global $blog_id; 
			//$blog_id = '901_Place-01';
			$firstname = 'Fname place holder';

$blog_id = 'basketball'; //'Huntsville_Place-01';
			//$email_logo_path = '<img src="'.$serverName.'wp-content/plugins/campaign-manager/images/logo/'.$blog_id.'.png" width="254" height="114" />';
			$content = str_replace("../", $serverName, $content);
			$content = str_replace("{first_name}", $firstname, $content);


			$subject = 'Message Preview Email';
			$message = 'This email is being sent to you because you are listed as an approval manager within the Peak campus Campaign Manager. Please review the email below and choose to approve or modify it.<br><br>'.$content;
			
/*

<div ><a style="background: none repeat scroll 0 0 #84B0DC; color: black; font-family: verdana; font-size: 10pt; font-weight: bold; padding: 6px; text-decoration: none !important; margin:10px;" href=""> Approve </a> <a style="background: none repeat scroll 0 0 #84B0DC; color: black; font-family: verdana; font-size: 10pt; font-weight: bold; padding: 6px; text-decoration: none !important; margin:10px;" href=""> Modify </a></div>
*/
			$message .= '<br><br> <div ><a href=""> Approve </a> <a href=""> Modify </a></div>';
			
			// Always set content-type when sending HTML email
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
			
			// More headers
			$headers .= 'From: <mailer@somdev1.us>' . "\r\n";
			//$headers .= 'Cc: myboss@example.com' . "\r\n";
			
			if(mail($to,$subject,$message,$headers)){
				echo 'Message has been send successfully';
			}else{ echo 'Unable to send message';}

		}

	} else {
		echo 'Error: You are not authorised to perform this action';
	}

?>