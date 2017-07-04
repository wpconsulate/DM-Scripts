<?php
//echo '<pre>'; print_r($_POST); echo '</pre>';
	if(is_admin()){
		$pid = $_POST['s_propertyId'];
		if($pid == 1){
			$prefix =" wp_";
		} else { 
			$prefix =" wp_".$pid."_";
		}

		if(!empty($campaign_name)){
			global $wpdb; 
			$tableName = 'campaigns';
			$campaign_name = mysql_real_escape_string($campaign_name);

			if(!empty($hidCampID)){

				$sql = "UPDATE ". $prefix."campaigns 
					SET `campaign_name` = '$campaign_name',
						`sent_date` = '$sent_date',
						`categoryId` = '$categoryId',
						`propertyId` = '$propertyId' 
					WHERE `id` = '$hidCampID' LIMIT 1 ";
				$wpdb->query($sql);

				//if($wpdb->query($sql)){

				$delSql = "DELETE FROM ". $prefix."campaign_list_rel WHERE `campaign_id` = $hidCampID";
				$wpdb->query($delSql);
				if(count($list)){
					for($i=0;$i<count($list);$i++){
						$campListSql = "INSERT INTO ". $prefix."campaign_list_rel (
								`campaign_id` ,
								`list_id`
							)
							VALUES (
								'$hidCampID', '$list[$i]'
							)";
						$wpdb->query($campListSql);
					}
				}
				echo "Success: Campaign has been updated sucessfully!";
				//}else{ //echo "Error: Unable to save! Please Try again"; }

			} else { echo 'Error: Invalid request!';}

		} else {
			echo 'Error: Campaign name is required';
		}
	} else {
		echo 'Error: You are not authorised to perform this action';
	}
?>
