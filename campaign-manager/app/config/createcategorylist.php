<?php
	global $wpdb;

	if(is_admin()){
	
		$requestOption = $_POST['s_ctrlType_key'];
	        if($requestOption == 1) {
		        $prefix = "wp_"; 
	        } else {
		        $prefix = "wp_".$requestOption."_";
	        }		
	        $type=$_POST['s_type']?$_POST['s_type']:1;
		//if($requestOption == 'categories'){


                        $maxidSql = "SELECT * FROM ". $prefix."categories ORDER BY id DESC";
                        $myrows = $wpdb->get_results($maxidSql);
                      
                        $maxId = $myrows[0]->id;


			$sortField = $_POST['s_order'];
			$sqlIn = "SELECT * FROM ". $prefix."categories  
			 WHERE `category_type` = '$type' AND active = 1 ORDER BY ". $sortField ." DESC" ;
			$catList = $wpdb->get_results($sqlIn);
		//}
		
		$objects=$catList;
		echo '<option value="0">Select One</option>';
		//echo '<option value="0">'.$sqlIn.'</option>';
		for($i=0;$i<count($objects);$i++){
		
		        if($maxId == $objects[$i]->id){ $selected = ' selected=selected'; } else { $selected = ''; }
		
			echo '<option value="'.$objects[$i]->id.'" '.$selected.'>'.$objects[$i]->category_name.'</option>';
		}
	}
?>
