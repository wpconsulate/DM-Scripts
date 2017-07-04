<?php
global $wpdb; 
$tableName = 'templates';
if($Tempid!=""){
	$userfile_extn = end(explode('.', $Tempimg));
	$old_name=ABSPATH."wp-content/plugins/campaign-manager/images/templates/thumb/".$Tempimg;
	$new_img="temp".$Tempid;
	$new_img=$new_img.".".$userfile_extn;
	$new_name=ABSPATH."wp-content/plugins/campaign-manager/images/templates/thumb/".$new_img;
	$mylink = $wpdb->get_row("SELECT * FROM ". $wpdb->base_prefix.$tableName." WHERE id = '".$Tempid."'");
	$name=$mylink->template_name;
	$head=$mylink->header;
	$foot=$mylink->footer;
	$img=$mylink->previewImgName;
	$delfile=ABSPATH."wp-content/plugins/campaign-manager/images/templates/thumb/".$img;
	if($TempName==$name && $TempHeader==$head && $TempFooter==$foot && $Tempimg==""){
		echo "No fields are changed for the existing template, so update is not required.";
	}else{
		if($Tempimg!=""){
			if (file_exists($delfile)) {
				unlink($delfile);
			}
			rename ($old_name, $new_name);
			$sql="UPDATE ". $wpdb->base_prefix.$tableName." SET template_name='".$TempName."', header='".$TempHeader."', footer='".$TempFooter."',previewImgName='".$new_img."' WHERE id='".$Tempid."'";
			$wpdb->query($sql);
			echo "Template has been updated.";
		}
		else{
		$sql="UPDATE ". $wpdb->base_prefix.$tableName." SET template_name='".$TempName."', header='".$TempHeader."', footer='".$TempFooter."' WHERE id='".$Tempid."'";
			$wpdb->query($sql);
			echo "Template has been updated.";
		}
	}
}
else{
	$TempHeader=$TempHeader;
	$TempFooter=$TempFooter;
	$curdate=date("Y-m-d");
	global $blog_id;
	$date = current_time('mysql');
	$search_duplicate="SELECT count(*) as count FROM ". $wpdb->base_prefix.$tableName." WHERE `template_name`='$TempName'";
	$count=$wpdb->get_var( $wpdb->prepare($search_duplicate));
	if($count==0){
		$sql = "INSERT INTO  ". $wpdb->base_prefix.$tableName." (`template_name`,`preview`,`header`,`footer`,`created_date`,`active`,`propertyId`) VALUES ('$TempName','$TempPreview','$TempHeader','$TempFooter','$date','1','$blog_id')";
		if($wpdb->query($sql)){
			echo "New Template has been created";
			$Tempid = $wpdb->insert_id;
		}else{
			echo "Some error occured!Please Try again";
		}
	}
	if($Tempimg!=""){
		$userfile_extn = end(explode('.', $Tempimg));
		$old_name=ABSPATH."wp-content/plugins/campaign-manager/images/templates/thumb/".$Tempimg;
		$new_img="temp".$Tempid;
		$new_img=$new_img.".".$userfile_extn;
		$new_name=ABSPATH."wp-content/plugins/campaign-manager/images/templates/thumb/".$new_img;
		rename ($old_name, $new_name);
		$wpdb->update( 
			$wpdb->base_prefix.$tableName, 
			array( 
				'previewImgName' => $new_img
			), 
			array( 'id' => $Tempid )
		);
	}
}
?>

