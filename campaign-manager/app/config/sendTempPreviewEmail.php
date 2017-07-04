<?php

        $serverName =  get_site_url()."/";
        //$serverName = 'http://hillplaceapts.com/';



	if(1){         //if(is_admin()){
		if(!empty($templateid)){
			global $wpdb; 
			$to = $templateid;

			global $blog_id; 
			//$blog_id = '901_Place-01';

			$content = str_replace("../", $serverName, $content);
			//$content = str_replace("{first_name}", $firstname, $content);



			$subject = 'Message Preview Email';
			$resFrm=$wpdb->get_results("SELECT * FROM `".$wpdb->prefix."emailsettings` ");
                        $fromName=$resFrm[0]->from;
                        $fromEmail=$resFrm[0]->reply;
			$message = $content;
			
			$message .= '<br><br> ';
			$message = stripslashes(html_entity_decode($message));
			
			// Always set content-type when sending HTML email
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
			
			// More headers
			$headers .= "From:$fromName <$fromEmail>" . "\r\n";
			//$headers .= 'Cc: myboss@example.com' . "\r\n";
			
			if(mail($to,$subject,$message,$headers)){
				echo 'Preview Message Sent Successfully..';
			}else{ echo 'Unable to send preview message';}

//**************************************
			        /*$sql = "INSERT INTO  wp_messages  (
				        message_name ,
				        message ,
				        date ,
				        campaignID ,
				        propertyID ,
				        status ,
				        active
			        ) VALUES ('TestPrev', '$content', '2002-10-12', '12', '14', '1', '1')";
			        $wpdb->query($sql);*/

//********************************************
		}

	} else {
		echo 'Error: You are not authorised to perform this action';
	}

?>
