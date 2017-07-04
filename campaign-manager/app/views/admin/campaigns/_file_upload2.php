<?php
if(!empty($_FILES) && !empty($_GET["id"])){
if ($_FILES["images"]["error"] > 0) echo "Error: " . $_FILES["file"]["error"] . "<br />";
$extension = end(explode(".", $_FILES["images"]["name"]));

  if (($_FILES["images"]["type"] == "image/png")){
                $extension = end(explode(".", $_FILES["images"]["name"]));
	        $filename=$_GET["id"].".png";
	    
               $filename=$filename;

   $res=  move_uploaded_file($_FILES["images"]["tmp_name"],"../../../../images/logo/" . $filename);
 if($res) echo "Email Logo changed";
 
  }else{
  
   echo "Invalid file! only PNG files are allowed";
   }
}else{

echo "Empty id or file";
}
