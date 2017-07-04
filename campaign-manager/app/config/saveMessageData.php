<?php

	if(is_admin()){
		if(!empty($message_name)){
			global $wpdb; 
			$tableName = 'messages';

				$message_name = mysql_real_escape_string($message_name);

if(!$s_cProperties){
global $blog_id;
$s_cProperties=$blog_id;
}
                                $psql = "SELECT * FROM ". $wpdb->base_prefix."properties WHERE `id` = '$s_cProperties' ";
                                $myprop = $wpdb->get_row( $psql);
                                $serverName = $myprop->property_domain_url."/";
                                
			        /// PREPARE eMAIL MESSAGE
				//$content = str_replace('\"', '', $content);
				//$content = html_entity_decode($content);
				/*$serverName = 'http://somdev1.us/';
				if($_SERVER['SERVER_NAME'] == 'somdev1.us'){
					$serverName = 'http://somdev2.us/';
				}*/
				$content = str_replace("../", $serverName, $content);
	
	
				$message = 'This email is being sent to you because you are listed as an approval manager within the Peak campus Campaign Manager. Please review the email below and choose to approve or modify it.<br><br>'.$content;



				global $blog_id;
				
				if(!$s_cProperties){
                                  global $blog_id;
                                  $s_cProperties=$blog_id;
                                 }else{
                                        $propertyID = $s_cProperties;
                                 }
                                 echo "ppid".$s_cProperties;
				$approveCode = md5('approveMessage');
				$modifyCode = md5('modifyMessage');

				if($s_hidMid){

					$sql = "UPDATE ". $wpdb->base_prefix."messages SET message_name = '$message_name',
					message = '$content',
					date = '$datepicker20',
					campaignID = '$campaignID',
					text_message = '$s_textdata',
					`status`='1' 
					 WHERE id = '$s_hidMid' LIMIT 1";
					if($wpdb->query($sql)){
					
						echo "Success: Message has been updated successfully ";
					}else{ echo "Error: Unable to update! Please Try again";}

				} else {


					// IF already exists?
					$checkSql = "SELECT *
							FROM ". $wpdb->base_prefix.$tableName."
							WHERE `message_name` = '$message_name' AND active = 1";
					$wpdb->query($checkSql);
					//echo  $wpdb->num_rows . 'Rows Found<br>';
		
					if($wpdb->num_rows == 0){
			
						$sql = "INSERT INTO  ". $wpdb->base_prefix."".$tableName." (
							message_name ,
							message ,
							date ,
							campaignID ,
							propertyID ,
							status ,
							active,
							text_message
						) VALUES ('$message_name', '$content', '$datepicker20', '$campaignID', '$propertyID', '1', '1','$s_textdata')";
						$wpdb->query($sql);
						$lastId = $wpdb->insert_id;
					
						$verificationCode = '1';
						if($lastId){
							echo "Success: Message has been saved successfully ";
						}else{ echo "Error: Unable to save! Please Try again";}

					} else { echo 'Error: Message name already exists'; }
				}

//------------------
			//This set of code is to send authourise/approve email
			//If message is in modify mode, no authourise/approve email required
			if($s_mode != 'modify'){
				// Find user with authorise permission
				function get_admin_users() {
					global $wpdb;  //          . "    AND um.meta_key = 'wp_capabilities' "
					$sql = "SELECT um.user_id AS ID, u.user_login, u.user_email "
						. "FROM ". $wpdb->prefix."users u, ". $wpdb->prefix."usermeta um "
						. "WHERE u.ID = um.user_id "
						. "AND um.meta_value LIKE '%administrator%' "
						. " GROUP BY um.user_id ORDER BY um.user_id ";
					
					$admins = $wpdb->get_results($sql);
					return $admins;
				}
			

				$admins = get_admin_users();
				foreach ( $admins as $admin ) {
					$capability_name = 'approve_message';
					$user = new WP_User($admin->ID);
					$checked = '';
					if ( $user->has_cap( $capability_name ) ) {
						//reate ann array of emailId of admin user with capability to authourise.
						$authouriseEmail[] = $admin->user_email;
					}
				}
				if(!empty($authouriseEmail)){
					$comma_separatedEmailIds = implode(",", $authouriseEmail);
				}
			}
				if(empty($lastId)){ $lastId = $s_hidMid; }

				$message .= '<br><br><table cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;width:50%">
				<tbody>
				<tr>
				<td style="font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding:7px 20px;background-color:#f2f2f2;border-left:none;border-right:none;border-top:1px solid #ccc;border-bottom:1px solid #ccc">
				<table cellpadding="20">
				<tr>
				<td style="font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding:3px 10px;background-color:#fff9d7;border-left:1px solid #e2c822;border-right:1px solid #e2c822;border-top:1px solid #e2c822;border-bottom:1px solid #e2c822"><a target="_blank" style="color:#3b5998;text-decoration:none" href="'.get_site_url().'/wp-admin/admin.php?page=mvc_campaigns-approveMessage&mid='.$lastId.'&vc='.$approveCode.'">Approve</a></td>
				<td style="font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding:3px  10px;background-color:#fff9d7;border-left:1px solid #e2c822;border-right:1px solid #e2c822;border-top:1px solid #e2c822;border-bottom:1px solid #e2c822"><a target="_blank" style="color:#3b5998;text-decoration:none" href="'.get_site_url().'/wp-admin/admin.php?page=mvc_campaigns-editmessage&mid='.$lastId.'&vc='.$modifyCode.'&m=modify">Modify</a></td>				
				</tr></table></td></tr></table>';


				$message = stripslashes(html_entity_decode($message));

				// To send HTML mail, the Content-type header must be set
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$comma_separatedEmailIds .= ',gr8sammundiyani@gmail.com,vn.sujithayur@gmail.com';
				// Additional headers
				$to = "$comma_separatedEmailIds" . "\r\n";
				//$headers .= 'To: '.$comma_separatedEmailIds. "\r\n";
				$headers .= 'From: Peak Campaigns <info@peakcampaigns.us>' . "\r\n";
				$subject = $message_name; //'Peak Campaigns message approval system';
				//$headers .= 'Cc: asd@ahu.com' . "\r\n";
				//$headers .= 'Bcc: bcc@yahu.com' . "\r\n";
				
				// Mail it
				mail($comma_separatedEmailIds, $subject, $message, $headers);

//------------------

			

		} else {
			echo 'Error: Message name is required';
		}
	} else {
		echo 'Error: You are not authorised to perform this action';
	}

?>
