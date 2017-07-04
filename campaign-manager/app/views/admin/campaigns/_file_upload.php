<?php
// file used for ajax csv file upload 
if(!empty($_FILES)){

$dbfileds=array('1'=>'first_name','2'=>'last_name','3'=>'email');
	foreach ($_FILES["images"]["error"] as $key => $error){
		if ($error == UPLOAD_ERR_OK) {
			$name = $_FILES["images"]["name"][$key];
			$randfn =  chr(rand(97, 122)). chr(rand(97, 122)). chr(rand(97, 122));
    		if(mkdir('../../../public/uploads/'.$randfn)){
			if(move_uploaded_file( $_FILES["images"]["tmp_name"][$key], "../../../public/uploads/" .$randfn."/".$_FILES['images']['name'][$key])){
			$source_file="../../../public/uploads/".$randfn."/".$name;
			$hname=$randfn."/".$name;
				if (($handle = fopen($source_file, "r")) !== FALSE) { 
					while($data = fgetcsv($handle,'\n')){
						$csvalues[]=$data;	
					}
					$fieldnames=$csvalues[0];
				}
?>
			<div id="csvflds">
			<input type="hidden" name="csvname" id="csvname" value="<?php echo $hname;?>">	
			<?php 
				foreach($dbfileds as $key => $value){ 
			?>
					<div style="width:500px;">
					<div style="width:250px;float:left;">
					<select name="fieldleft_<?php echo $key;?>" id="fieldleft_<?php echo $key;?>" style="padding:0px;">
					<option value="">Select Field</option>
					<?php 
					  foreach($fieldnames as $d=>$csvfl){
// 						$strin=strtolower($csvfl);
						echo "<option value=".$d.">".$csvfl."</option>";
					  }
					?>
					</select></div>
					<div style="width:250px;float:left;">
						<select name="fieldright_<?php echo $key;?>" id="fieldright_<?php echo $key;?>" style="padding:0px;">
					<?php 
					  foreach($dbfileds as $dbcv){
						echo "<option value=".$dbcv.">".$dbcv."</option>";
					  }
					?>
						</select>
					</div>
					</div>
					<?php
				}
			?>
			</div>
			<?php

			}else{
				echo "Error. Please contact the system administrator";
			}
		}else{
			echo "Error.Cannot Create a folder.Please contact the system administrator";	
		}
		}
	}
}else{
	echo "Please Upload a file in CSV Format!";
}

