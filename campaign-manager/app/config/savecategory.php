<?php
	global $blog_id;
	if($relType == 2){
		if($blog_id == 1){
			$prefix = 'wp_';
		}else{
			$prefix = 'wp_'.$blog_id.'_';
		}
	} else {
		
		if($propSelected == 1) {
			$prefix = "wp_"; 
		} else {
			$prefix = "wp_".$propSelected."_";
		}
	}	

	if(is_admin()){
		if(!empty($CatName)){
			global $wpdb; 
			$tableName = 'categories';
			$checkSql = "SELECT * FROM ". $prefix."categories WHERE `category_name` = '$CatName'";
			$wpdb->get_results($checkSql);
			if($wpdb->num_rows){
				echo "Error: category name already exists";
			} else {
				$sql = "INSERT INTO  ". $prefix."categories (`category_name`, `category_type`, `active`) 
					VALUES ('$CatName',$relType, 1)";
	
				if($wpdb->query($sql)){
					echo "Success: Category has been saved sucessfully!";
				}else{
					echo "Error: Unable to save! Please Try again";
				}
			}

		} else {
			echo 'Error: Category name is required.';
		}
	} else {
		echo 'Error: You are not authorised to perform this action';
	}
?>
