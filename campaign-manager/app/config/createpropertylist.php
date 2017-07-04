<?php
	global $wpdb;
	global $blog_id;
	if($blog_id == 1) {
		$prefix="wp_"; 
	} else {
		$prefix="wp_".$blog_id."_";
	}

	if(is_admin()){
	
		$requestOption = $_POST['s_ctrlType_key'];
	
		if($requestOption == 'properties'){
			$tableName = 'properties';
			$sortField = $_POST['s_order'];
			$sql = "SELECT  id, property_name AS name 
				FROM ". $prefix.$tableName." 
				WHERE prop_status = 1 
				ORDER BY ". $sortField ." ASC" ;
		}
		if($requestOption == 'categories'){
			$tableName = 'categories';
			$sortField = $_POST['s_order'];
			if(!is_super_admin()){
				if(!empty($s_ctype)){ $field = ' AND category_type = '.$s_ctype; } else { $field = ''; }
			}
			$sql = "SELECT  id, category_name  AS name,category_type 
				FROM ". $prefix.$tableName." 
				WHERE active = 1 $field
				ORDER BY ". $sortField ." ASC" ;
		}
	
		$objects = $wpdb->get_results($sql);
			echo '<option value="0">Select One.-</option>';
		for($i=0;$i<count($objects);$i++){
			echo '<option value="'.$objects[$i]->id.'">'.$objects[$i]->name.'</option>';
	
		}
	}
?>