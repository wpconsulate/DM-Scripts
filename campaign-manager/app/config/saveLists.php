<?php
set_time_limit(0);

$debug = false;

if(is_admin()){
	$csvurl= plugins_url()."/campaign-manager/app/public/uploads/";
	$fileuploaddir=ABSPATH."wp-content/plugins/campaign-manager/app/public/uploads/";
	if(!empty($list_name)){
		global $wpdb; 
		global $blog_id;
		$inlis_contact=0;
		$tableName1 = 'lists';
		$tableName2 = 'contacts';
		$tableName3 = 'list_rel_onesite';
		$csvFile=$csvurl.$csvname;
		$csvdel=$fileuploaddir.$csvname;
		$date=  date("Y-m-d");
		$list_name = $list_name;
// 		echo "Listname: ".$list_name."<br/>";
		// IF already exists?
		$checkSql = "SELECT * FROM ". $wpdb->prefix."".$tableName1." WHERE `list_name` = '$list_name' AND `propertyId`='$blog_id'";
		$wpdb->query($checkSql);
// 		echo  $wpdb->num_rows . 'Rows Found<br>';
// 		echo "Adding to database....<br/>";
		if($wpdb->num_rows == 0){
			$wpdb->query('START TRANSACTION');
			$wpdb->query('BEGIN');
			$sql = "INSERT INTO  ". $wpdb->prefix."".$tableName1."(`list_name` , `date` , `categoryId` ,`import_type` , `active` , `description` , `tags`,`propertyId`,`num_contacts`,`testMod`)VALUES('".mysql_real_escape_string($list_name)."','$date','$category','$category2','$active','".mysql_real_escape_string($list_description)."','$tags','$blog_id', 0, '$chk_testlist')";
            
			if($wpdb->query($sql)){
				$listInsertID = $wpdb->insert_id;
                
                if($debug) echo "$sql - Added to database".$listInsertID."<br/>";
                
				if($category2=='csv'){
					$line_of_text=array_map("str_getcsv", file($csvFile));
					unset($line_of_text[0]);
					foreach($line_of_text as $ltx){
						$sql2 = "INSERT INTO  ".$wpdb->prefix."".$tableName2."(`list_id` , `$rightf1` , `$rightf2` , `$rightf3`)VALUES( '$listInsertID', '".mysql_real_escape_string($ltx[$leftf1])."', '".mysql_real_escape_string($ltx[$leftf2])."', '".mysql_real_escape_string($ltx[$leftf3])."')";
                        
                        if($debug) echo $sql2;
                        
						if($wpdb->query($sql2)){
							$inlis_contact++;
							$errflag=true;
						}else{
                            
                            if($debug) echo "<br>ROLLBACK<br>";
                            
							$wpdb->query('ROLLBACK');
							$errflag=false;
                            
                            echo "Error: Unable to save Contacts! Please Try again"; die();
						}
					}
					if($errflag==true){
                        
                        //Update Counter to the List
                        $_sql_9 = "UPDATE ". $wpdb->prefix."".$tableName1." SET num_contacts=".$inlis_contact." WHERE id = ".$listInsertID;
                        $wpdb->query($_sql_9);
                        
                        if($debug) echo $_sql_9;
                        
						$wpdb->query('COMMIT');
						if(unlink(rawurldecode($csvdel))){
						   echo "Success: List has been saved sucessfully!";
        				}else{
							echo "Success: List has been saved sucessfully, but unable to delete the csv file";
						}
                        $successflag=true;
					}else{
						$successflag=false;
						unlink(rawurldecode($csvdel));
						echo "Error: Unable to save Contacts! Please Try again";
					}
				}
                elseif($category2=='onesite'){
					$sql3 = "INSERT INTO ".$wpdb->prefix."".$tableName3." (`residents`, `parents`, `prospects`,`list_id` ,`status` )VALUES ('$resident_values','$parent_values','$prospects_values','$listInsertID', '1')";
						if($wpdb->query($sql3)){
							$wpdb->query('COMMIT');
							 echo "Success: List has been saved sucessfully!";
							 $successflag=true;
						}else{
							$wpdb->query('ROLLBACK');
							echo "Error: Unable to Update Onesite relation! Please Try again";	
							 $successflag=false;
						}
				}
				
			}else{
				$wpdb->query('ROLLBACK');
				unlink($csvdel);
				echo "Error: Unable to save Lists! Please Try again";
			}

		} else { 
				unlink(rawurldecode($csvdel));
				echo 'Error: List name already exists'; }
// 
	} 
    else {	
			unlink(rawurldecode($csvdel));
			echo 'Error: List name is required';
	}

// ////for number of contacts
if($successflag==true && $category2=='csv'){
	$sqllis = "SELECT count(*) as count FROM ".$wpdb->prefix."".$tableName2." WHERE list_id  =".$listInsertID;
	$res=$wpdb->get_row($sqllis);
		if($res != null){
			$ccount=$res->count;
			$sqluplist ="UPDATE ". $wpdb->prefix."".$tableName1." SET `num_contacts` = '$ccount' WHERE `id` = '$listInsertID' LIMIT 1";
			$wpdb->query($sqluplist);
		}
	}elseif($successflag==true && $category2=='onesite'){
		include_once("count_list.php");
// 		echo "User Count: ".$user_count."<br/>";
		$sqluplist ="UPDATE ". $wpdb->prefix."".$tableName1." SET `num_contacts` = '$user_count' WHERE `id` = '$listInsertID' LIMIT 1";
		$wpdb->query($sqluplist);
	}

} 
else {
	if($category2=='csv'){
	    unlink(rawurldecode($csvdel));
	}
	echo 'Error: You are not authorised to perform this action';
}

?>
