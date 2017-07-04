<?php
if(1){
	if(!empty($id)){
		global $wpdb; 
		$tableName1 = 'lists';
		$date=  date("Y-m-d");
		
		if($s_props){
                   if($s_props=="1"){
                        $prefix="wp_";
                   }else{
                        $prefix="wp_".$s_props."_";
                   }
                }else{
                   $prefix=$wpdb->prefix;
                }

		// IF already exists?
		$checkSql = "SELECT * FROM ". $prefix."".$tableName1." WHERE `id` = $id";
		$vals=$wpdb->get_results($checkSql);
		if($wpdb->num_rows >0){
			$checkSql2 = "SELECT * FROM ". $prefix."".$tableName1." WHERE `list_name` = '$list_name' AND  `id` !=$id";
			$res=$wpdb->get_row($checkSql2);
			if($res  == null){
				$sql ="UPDATE ". $prefix."".$tableName1." SET `list_name` = '$list_name',`categoryId` = '$category',`description` = '$list_description',`tags` = '$tags' WHERE `id` = '$id' LIMIT 1";
				if($wpdb->query($sql)){
					echo "Success: List has been Updated sucessfully!";
				
				}else{
					echo "Error: Unable to Update! Please Try again";
					
				}

		} else { echo 'Error: List name already exists.Please Choose Another Name';
			
		}

	} else {
			echo 'Error..No Records Found';
			
	}
} else {
	echo 'Error: You are not authorised to perform this action';
}
}
?>
