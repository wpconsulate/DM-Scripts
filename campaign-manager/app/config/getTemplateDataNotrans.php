<?php
	global $wpdb;

	if(is_admin()){
	
		if(!empty($getTtemplateId)){
			//Fetch data for the property selected.



			$tableName = 'templates';
			$sql = "SELECT  * 
				FROM ". $wpdb->base_prefix."".$tableName." 
				WHERE active = 1 AND id = ".$getTtemplateId ;
			$objects = $wpdb->get_results($sql);
			$footer = $objects[0]->footer;
			$footer = stripslashes($footer);



			echo $footer;
		}
	}
?>
