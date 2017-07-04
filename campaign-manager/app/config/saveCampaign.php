<?php
	if(is_admin()){
		if(!empty($campaign_name)){
			global $wpdb; 

			if($propertyId == 1){
				$prefix=" wp_";
			} else {
				$prefix=" wp_".$propertyId."_";
			}

			$tableName = 'campaigns';
			$campaign_name = mysql_real_escape_string($campaign_name);

			// IF already exists?
			$checkSql = "SELECT *
					FROM ". $prefix."campaigns
					WHERE `campaign_name` LIKE '$campaign_name' ";
			$wpdb->query($checkSql);
			//echo  $wpdb->num_rows . 'Rows Found<br>';

			if($wpdb->num_rows == 0){
				$sql = "INSERT INTO  ". $prefix."".$tableName." (
						`campaign_name` ,
						`sent_date` ,
						`categoryId` ,
						`propertyId` ,
						`active`
					) VALUES ('$campaign_name','$sent_date',$categoryId,$propertyId,1)";
				if($wpdb->query($sql)){
					$campaignInsertID = $wpdb->insert_id;
					echo "Success: Campaign has been saved sucessfully!";

					for($i=0;$i<count($list);$i++){
						$campListSql = "INSERT INTO ". $prefix."campaign_list_rel (
								`campaign_id` ,
								`list_id`
							)
							VALUES (
								'$campaignInsertID', '$list[$i]'
							)";
						$wpdb->query($campListSql);
					}

				}else{
					echo "Error: Unable to save! Please Try again";
				}

			} else { echo 'Error: Campaign name already exists'; }

		} else {
			echo 'Error: Campaign name is required';
		}
	} else {
		echo 'Error: You are not authorised to perform this action';
	}
?>
