<?php
date_default_timezone_set('America/Tegucigalpa');
$pluginImagesDir = 'http://hillplaceapts.com/wp-content/plugins/campaign-manager/images/';
	MvcConfiguration::set(array(
		'Debug' => false
	));
	
	MvcConfiguration::append(array(
		'AdminPages' => array(
			'campaigns' => array(
				'add',
				'delete',
				'Lists',
				'Settings',
				'templates','addTemplate','templateedit',
				'message','addmessage','editmessage','approveMessage','manage_categories'
			)
		)
	));

	add_action('admin_head', 'action_javascript');
	add_action('wp_ajax_delete', 'delete_callback');
	add_action('wp_ajax_loadPropCat', 'propCat_callback'); // Create properties and categories dropdown on the fly
	add_action('wp_ajax_loadPrintView', 'printView_callback'); // Create Print Preview
	
	add_action('wp_ajax_saveTemplate','SaveTemp'); //Vidhu - save Template
// 	add_action('wp_ajax_previewTemplate','PreviewTemp'); //Vidhu - Template Preview
	add_action('wp_ajax_editTemplate','editTemp');//Vidhu - Template edit
	add_action('wp_ajax_templatePages','paginateTmp');//Vidhu - Template Pagintaion
	add_action('wp_ajax_listPages','paginatelistPages');//Vidhu - List Pagintaion
	
	add_action('wp_ajax_SaveCategory', 'SaveCat_callback'); // Save New Category
	add_action('wp_ajax_SaveCampaign', 'SaveCampaign_callback'); // Save New Category
	add_action('wp_ajax_UpdateCampaign', 'UpdateCampaign_callback'); // Save New Category

	add_action('wp_ajax_SaveLists', 'SaveLists_callback'); // Save New Lists
	add_action('wp_ajax_editList','editList_callback');// List edit
	add_action('wp_ajax_loadListCat', 'listCat_callback');

	add_action('wp_ajax_LoadTemplate', 'LoadTemplate_callback');
	add_action('wp_ajax_LoadTemplateNotrans', 'LoadTemplateNotrans_callback');	
	add_action('wp_ajax_saveMessage', 'saveMessage_callback');
	add_action('wp_ajax_sendTempPreviewEmail', 'sendTempPreviewEmail_callback');
	add_action('wp_ajax_messagePages', 'messagePages_callback');
	add_action('wp_ajax_createTempPreview', 'createTempPreview_callback'); //Sam: template preview
	add_action('wp_ajax_setApprovePermission', 'setApprovePermission_callback'); //Sam: Set AdminnUser permission
	add_action('wp_ajax_setCampaignPermission', 'setCampaignPermission_callback');//sujith 17 jan 2013
	
	add_action('wp_ajax_SaveSendGrid',"SaveSendGrid_callback");	

	add_action('wp_ajax_loadPropCampaigns', 'loadPropCampaigns_callback'); //Sam: loadPropCampaigns
	add_action('wp_ajax_SaveEmailSet',"SaveEmailSet_callback"); //sujith 08 Oct
	add_action('wp_ajax_delete_category',"delete_category_callback"); // sujith oct 22
        add_action('wp_ajax_manage_category','manage_category_callback');  // sujith oct 23
        add_action('wp_ajax_saveSiteId','saveSiteId_callback');  // sujith Nov 06
	add_action('wp_ajax_delete_campaignGroup',"delete_campaignGroup_callback");
	add_action('wp_ajax_delete_templateGroup',"delete_templateGroup_callback"); //sujith 10dec 2012
	add_action('wp_ajax_delete_listGroup',"delete_listGroup_callback"); //sujith 10dec 2012	
        add_action('wp_ajax_updateSystemMode', 'updateSystemMode_callback'); //Sam: save system mode
	add_action('wp_ajax_delete_message_group',"delete_message_group_callback");

	add_action('wp_ajax_getPropertyBasedList',"getPropertyBasedList_callback");
	add_action('wp_ajax_getPropertyBasedCategories',"getPropertyBasedCategories_callback");
	add_action('wp_ajax_delete_categoryGroup',"delete_categoryGroup_callback");
	add_action('wp_ajax_loadplaceholders',"loadplaceholders_callback");
	add_action('wp_ajax_saveplaceholders',"saveplaceholders_callback");	
	
	function saveplaceholders_callback(){
		global $wpdb,$blog_id;
		//echo '<pre>'; print_r($_POST); echo '</pre>';

		$propid = $_POST['propid'];
		$txtproperty = $_POST['txtproperty'];
		$txtstreet = $_POST['txtstreet'];
		$txtcity = $_POST['txtcity'];
		$txtstate = $_POST['txtstate'];
		$txtzip = $_POST['txtzip'];
		$txtphone = $_POST['txtphone'];
		$txtemail = $_POST['txtemail'];
		$property_domain_url = $_POST['property_domain_url'];
		$txttwitter_url = $_POST['txttwitter_url'];
		$txtfacebook_url = $_POST['txtfacebook_url'];
		$txtpinterest_url = $_POST['txtpinterest_url'];
		$txtgoogle_url = $_POST['txtgoogle_url'];
		$txtyoutube_url = $_POST['txtyoutube_url'];


		$sql = "UPDATE ".$wpdb->base_prefix."properties 
		SET `property_name` = '$txtproperty',
		`streetaddress` = '$txtstreet',
		`city` = '$txtcity',
		`state` = '$txtstate',
		`zip` = '$txtzip',
		`phone` = '$txtphone',
		`email` = '$txtemail',
		`property_domain_url` = '$property_domain_url',
		`twitter_url` = '$txttwitter_url',
		`facebook_url` = '$txtfacebook_url',
		`pinterest_url` = '$txtpinterest_url',
		`google_url` = '$txtgoogle_url',
		`youtube_url` = '$txtyoutube_url' 
		WHERE `id` = '$propid' LIMIT 1" ;
		$res = $wpdb->query($sql);
		if($res){ echo "Placeholders saved sucessfully";} else { echo "Something went wrong! Please try again."; }	
		die();
		}
	
	function loadplaceholders_callback(){

		global $wpdb,$blog_id;
		$pid = $_POST['s_propid'];
		$psql = "SELECT * FROM ".$wpdb->base_prefix."properties WHERE `id` = $pid";
		$psqlres = $wpdb->get_results($psql);
		if(count($psqlres) > 0 ){
			//echo '<pre>'; print_r($psqlres); echo '</pre>';
/*
			$pid = $psqlres[0]['id'];
			$property_name = $psqlres[0]['property_name'];
			$streetaddress = $psqlres[0]['streetaddress'];
			$city = $psqlres[0]['city'];
			$state = $psqlres[0]['state'];
			$zip = $psqlres[0]['zip'];
			$phone = $psqlres[0]['phone'];
			$email = $psqlres[0]['email'];
			$property_domain_url = $psqlres[0]['property_domain_url'];
			$twitter_url = $psqlres[0]['twitter_url'];
			$facebook_url = $psqlres[0]['facebook_url'];
			$pinterest_url = $psqlres[0]['pinterest_url'];
			$google_url = $psqlres[0]['google_url'];
			$youtube_url = $psqlres[0]['youtube_url'];
			$prop_status = $psqlres[0]['prop_status'];
*/
			if( function_exists( "json_encode" ) ) { 
				$jsonGetData = json_encode( $psqlres[0] ); 
			} 
			echo $jsonGetData;


		} else { echo 'Invalid request';}
		die();
	}


        function getPropertyBasedList_callback(){
	        global $wpdb,$blog_id;
          	$selectdProp = $_POST['s_selectedProp'];

	        if($selectdProp == 1){
		        $prefix ="wp_";
	        } else { 
		        $prefix ="wp_".$selectdProp."_";
	        }

	        $listSql = "SELECT * FROM `".$prefix."lists` WHERE `propertyId` = ".$selectdProp." ";
	        $completeLists = $wpdb->get_results($listSql);
	        if(count($completeLists) > 0 ){
		        for($i=0;$i<count($completeLists);$i++){
			        echo '<option value="'.$completeLists[$i]->id.'">'.$completeLists[$i]->list_name.'</option>';
		        }
	        } else { echo '<option value="0">No Lists Available</option>'; }

	        die();
        }
        
        function getPropertyBasedCategories_callback(){

                global $wpdb,$blog_id;
          	$selectdProp = $_POST['s_selectedProp'];

	        if($selectdProp == 1){
		        $prefix ="wp_";
	        } else { 
		        $prefix ="wp_".$selectdProp."_";
	        }

	        $listSql = "SELECT * FROM `".$prefix."categories`WHERE category_type='1'";
	        //echo '<option value="0">'.$listSql.'</option>';
	        $completeCategories = $wpdb->get_results($listSql);
	        if(count($completeCategories) > 0 ){
		        for($i=0;$i<count($completeCategories);$i++){
			        echo '<option value="'.$completeCategories[$i]->id.'">'.$completeCategories[$i]->category_name.'</option>';
		        }
	        } else { echo '<option value="0">No Category Available</option>'; }

	        die();
        }


function updateSystemMode_callback(){
	global $wpdb,$blog_id;
	$s_mode = $_POST['s_mode'];
	$sql="UPDATE `wp_system_mode` SET system_mode ='$s_mode' ";
	$res = $wpdb->query($sql);
	if($res){ echo "Settings saved sucessfully";} else { echo "Some error occured!Please try again."; }

	die();
}


// sujith Nov 06
function saveSiteId_callback(){
	
	global $wpdb,$blog_id;
	$siteId=$_POST['siteId'];
	$res=$wpdb->get_results("SELECT * FROM `wp_generalSettings` WHERE `blogid`='$blog_id'");
	if($wpdb->num_rows > 0) $sql="UPDATE `wp_generalSettings` SET siteId='$siteId'WHERE `blogid`='$blog_id'";
	else $sql="INSERT INTO `wp_generalSettings` (siteId,`blogid`) VALUES ('$siteId','$blog_id')";
	$res1=$wpdb->query($sql);
	 if($res1) echo "Settings saved sucessfully";
	 else echo "Some error occured!Please try again.";
	 die();
	}
//sujith oct 22
function delete_category_callback(){
        global $wpdb;
        $id=$_POST['id'];
        $type=$_POST["type"];
        $prefix=(!empty($_POST["propId"]))?"wp_".$_POST["propId"]."_":$wpdb->prefix;
        switch($type){
                case 1 :$table=$prefix."campaigns";break;
                case 2 : $table=$prefix."lists";break;
	        case 3 : $table=$prefix."messages";break; 
	        default: $table=$prefix."campaigns";break;
        }
        $sql1="SELECT * FROM `".$table."` WHERE `categoryId`=$id";
        $res=$wpdb->get_results($sql1);
        if($wpdb->num_rows){
                echo "Category already assigned.Can't delete!";
        }else{
                $sql="DELETE FROM `".$prefix."categories` WHERE `id`='".$id."'";
                
                $res=$wpdb->query($sql);
                if($res){
                        echo "Category deleted sucessfully";
                }else{
                        echo mysql_error()." sql:".$sql;
                }
        }
         die();
}

///---Delete multiple campaign and its associated messages based on checkbox selection
function delete_campaignGroup_callback(){ //610

	global $wpdb;
	$s_selectedRows = $_POST['s_selectedRows'];

	for($c=0;$c<count($_POST['s_selectedRows']);$c++){ //Selected campaign Array
		list($campId, $propId) = explode('-',$_POST['s_selectedRows'][$c]); 
		if($propId == 1){
			$prefix ="wp_";
		} else { 
			$prefix ="wp_".$propId."_";
		}

		$sql = 'SELECT * FROM `'.$wpdb->base_prefix.'messages` WHERE `campaignID` = '.$campId.' AND `propertyID` = '.$propId.' ';
		$res=$wpdb->get_results($sql);
		if($wpdb->num_rows > 0){

			///$delSql = "DELETE FROM ".$wpdb->base_prefix."messages WHERE id  =  ".$res[0]->id." AND `status` !=3 "; //Retain already send messages

			//Remove every message associated with the campaign
			$delSql = "DELETE FROM ".$wpdb->base_prefix."messages WHERE id  =  ".$res[0]->id." ";
			$wpdb->query($delSql);
			$rows_deleted = $wpdb->rows_affected;
			if($rows_deleted){
				echo "<br><span style='color:#0F631E'>Success: Message has been deleted successfully </span>";
			}
		}
		//Remove campaigns
		$delCampSql = 'DELETE FROM `'.$prefix.'campaigns` WHERE `'.$prefix.'campaigns`.`id` = '.$campId.' LIMIT 1';
		$delCampRes = $wpdb->query($delCampSql);
		if($delCampRes){
			echo "<br><span style='color:#0F631E'>Campaign(s) has been deleted sucessfully</span>";
		} else {
			echo "<br><span style='color:#CF0000'>Something went wrong with campaign: ".$campId ."!! Please try again.</span>".mysql_error();
		}

	}//end 4 c

die();
}
// Delete multiple messages
///---------------------------
function delete_message_group_callback(){ //512

	global $wpdb;
	$s_selectedRows = $_POST['s_selectedRows'];
	for($c=0;$c<count($_POST['s_selectedRows']);$c++){ //Selected campaign Array

		$msgId = $_POST['s_selectedRows'][$c];

		//echo '<br>'.
		/// 	status 1=Pending:2=Approved:3=Sent
		$sql = "SELECT status FROM ".$wpdb->base_prefix."messages WHERE id = ".$msgId." ";
		$res = $wpdb->get_row($wpdb->prepare($sql));

		if($res->status != 3){
			$sql = "DELETE FROM ".$wpdb->base_prefix."messages WHERE id  =  ".$msgId."  LIMIT 1";
			$wpdb->query($sql);
			$rows_deleted = $wpdb->rows_affected;
			if($rows_deleted){
				echo "<br><span style='color:#0F631E'>Success: Message has been deleted successfully </span>";
			}else{ echo "Error: Unable to delete! Please contact system admin";}
		} else { echo '<span style="color:#CF0000">Error: Unable to Delete. This message has already been sent.';}




	}//end 4 c

die();
}

// Delete multiple category -------------

function delete_categoryGroup_callback(){

	global $wpdb,$blog_id;

	$s_selectedRows = $_POST['s_selectedRows'];
	for($c=0;$c<count($_POST['s_selectedRows']);$c++){ //Selected category Array - check boxes-
		list($id,$type,$property) = explode(',',$_POST['s_selectedRows'][$c]);

		if($property == 1){
			$prefix = 'wp_';
		} else {
			$prefix = 'wp_'.$property.'_';
		}

		switch($type){
			case 1 : $table = $prefix."campaigns"; break;
			case 2 : $table = $prefix."lists"; break;
			case 3 : $table = $prefix."messages"; break;
			default: $table = $prefix."campaigns"; break;
		}

		$sql1 = 'SELECT * FROM `'.$table.'` WHERE `categoryId` = '.$id.' ';
		$res=$wpdb->get_results($sql1);
		if($wpdb->num_rows){
			echo "Category (".$id.") already assigned. Can't delete!<br>";
		} else {
			$sql = 'DELETE FROM `'.$prefix.'categories` WHERE `id`= "'.$id.'"';
			$res = $wpdb->query($sql);
			if($res){
				echo "Category (".$id.") has been deleted sucessfully<br>";
			} else {
				echo mysql_error()." sql:".$sql;
			}
       		}

	}// 4 loop ends here

	die();
}

//-----------------template group delete sujith 10 dec 2012>>>>>>>>>>
function delete_templateGroup_callback(){ 

	global $wpdb,$blog_id;
	$err=0;
	$message="";
	$s_selectedRows = $_POST['s_selectedRows'];
	if($blog_id==1) $prefix="wp_";
	else $prefix="wp_{$blog_id}_";
	foreach($s_selectedRows as $id){
	$sql="DELETE FROM `{$prefix}templates` WHERE `id`='$id'"; //wp_templates
	 $res=$wpdb->query($sql);
	 if(!$res) $err=1;
	}
         
	 if($err) $message ="One or more templates can't be deleted!".mysql_error();
	 else $message="Template(s) deleted sucessfully.";
	 echo $message;
	//print_r($s_selectedRows);

die();
}
//-----------------template group delete sujith <<<<<<<<<<<<<<<<<<<<<

//-----------------list group delete sujith 10 dec 2012>>>>>>>>>>
function delete_listGroup_callback(){ 

	global $wpdb,$blog_id;
	
	$err=0;
	$message="";
	$s_selectedRows = $_POST['s_selectedRows'];
	/*
	$s_props=$_POST["s_props"];
	if($s_props){
           if($s_props=="1"){
                $prefix="wp_";
           }else{
                $prefix="wp_".$s_props."_";
           }
           $props=$s_props;
        }else{
                global $blog_id;
           $prefix=$wpdb->prefix;
           $props=$blog_id;
        }
 */       
	//if($blog_id==1) $prefix="wp_";
	//else $prefix="wp_{$blog_id}_";
	
	for($c=0;$c<count($_POST['s_selectedRows']);$c++){ 
		list($id, $propId) = explode('-',$_POST['s_selectedRows'][$c]); 
		if($propId == 1){
			$prefix ="wp_";
		} else { 
			$prefix ="wp_".$propId."_";
		}


		 $sql = "SELECT * FROM ".$prefix."lists WHERE id = '$id'";
		$res = $wpdb->get_row($wpdb->prepare($sql));
			if($res->import_type=="csv"){
				$res = $wpdb->query(
					$wpdb->prepare( 
						"
						DELETE FROM " .$prefix."contacts
						WHERE list_id='$id'"
					)
				);
				//$sql1="DELETE FROM " .$wpdb->prefix."contacts WHERE list_id='$id'";
				
			}elseif($res->import_type=="onesite"){
				$res = $wpdb->query(
					$wpdb->prepare( 
						"
						DELETE FROM " .$prefix."list_rel_onesite
						WHERE list_id='$id'"
					)
				); 
				//$sql1="DELETE FROM " .$wpdb->prefix."list_rel_onesite WHERE list_id='$id'";
			}

			$res= $wpdb->query(
				$wpdb->prepare( 
					"DELETE FROM " .$prefix."lists WHERE id='$id'"
				)
			);
			
			//$sql2="DELETE FROM " .$wpdb->prefix."lists WHERE id='$id'";
			 if(!$res) $err=1;
	}
	
         
	 if($err) $message ="One or more lists can't be deleted!".mysql_error();
	else $message="List(s) deleted sucessfully.";
	 echo $message;
//echo $sql1." _____ ".$sql2;

die();
}
//-----------------template group delete sujith <<<<<<<<<<<<<<<<<<<<<



///---------------------------
//sujith oct 22
//sujith oct 23
function manage_category_callback(){
include("manage_category.php");
	die();
}
//sujith oct 23 end
	function SaveTemp(){
		$TempName=$_POST['Name'];
		$TempPreview=$_POST['Preview'];
// 		$TempHeader=$_POST['Header'];
		$TempFooter=$_POST['Footer'];
		$Tempactive=$_POST['Active'];
		$Tempimg=$_POST['Tempimg'];
		
		include_once("saveTemplate.php");
		die();
	}
	function editTemp(){
		$TempName=$_POST['Name'];
		$TempPreview=$_POST['Preview'];
// 		$TempHeader=$_POST['Header'];
		$TempFooter=$_POST['Footer'];
		$Tempid=$_POST['id'];
		$Tempimg=$_POST['Tempimg'];
		include_once("saveTemplate.php");
		die();
	}
	function PreviewTemp(){;
// 		$TempHeader=$_POST['Header'];
		$TempFooter=$_POST['Footer'];
		include_once("previewTemplate.php");
		die();
	}
	function paginateTmp() {
		global $wpdb; // this is how you get access to the database
		$s_field = $_POST['s_field_key'];
		$s_order = $_POST['s_order_key']; 
		$s_type = $_POST['s_type_key'];
		$s_q = $_POST['s_q'];
		$s_cid=$_POST['s_cid'];
		$s_dtPicker = $_POST['s_dtPicker'];
		//$s_props = $_POST['s_props'];
		$s_searchq = $_POST['s_searchq'];
		$s_rowlen = $_POST['s_rowlen'];
		$s_page = $_POST['s_page'];

		include('TemplatePages.php');
		die(); // this is required to return a proper result
	}
	function paginatelistPages() {

		global $wpdb; // this is how you get access to the database
		$s_field = $_POST['s_field_key'];
		$s_order = $_POST['s_order_key']; 
		$s_type = $_POST['s_type_key'];
		$s_q = $_POST['s_q'];

		$s_dtPicker = $_POST['s_dtPicker'];
		$s_props = $_POST['s_props'];
		$s_searchq = $_POST['s_searchq'];
		$s_rowlen = $_POST['s_rowlen'];
		$s_page = $_POST['s_page'];
		$s_curId = $_POST['s_cid'];

		include('ListPages.php');
		die(); // this is required to return a proper result
	}
///	GLORY  *******************
	function SaveLists_callback(){
		$list_name=$_POST['l_list_name'];
		$list_description=$_POST['l_list_description'];
		$category=$_POST['l_category'];
		$category2=$_POST['l_category2'];
		$leftf1=$_POST['l_leftf1'];
		$leftf2=$_POST['l_leftf2'];
		$leftf3=$_POST['l_leftf3'];
		$rightf1=$_POST['l_rightf1'];	
		$rightf2=$_POST['l_rightf2'];
		$rightf3=$_POST['l_rightf3'];
		$csvname=rawurlencode($_POST['l_csvname']);
		$active=$_POST['l_active'];
		$tags=$_POST['l_tag'];
		$resident_values=$_POST['l_resident_values'];	
		$parent_values=$_POST['l_parent_values'];	
		$prospects_values=$_POST['l_prospects_values'];
		$chk_testlist=$_POST['l_chk_testlist'];
		include_once("saveLists.php");
		die();
	}
	function editList_callback(){
		$list_name=$_POST['Name'];
		$list_description=$_POST['Description'];
		$tags=$_POST['lTag'];
		$category=$_POST['categoryId'];
		$id=$_POST['id'];
		$s_props=$_POST['s_props'];
		include_once("UpdateLists.php");
		die();
	}
	function listCat_callback(){ // Create property and category drops
		$s_ctrlType = $_POST['s_ctrlType_key'];
		$s_order = $_POST['s_order'];
		include('createcategorylist.php');
		die();
	}
//sujith 08 Oct
function SaveEmailSet_callback(){
	
	global $wpdb;
	$res1 = FALSE;
	$user = $_POST['e_from'];
	$pass = $_POST["e_reply"];
	$res = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."emailsettings`");

	if($wpdb->num_rows > 0){
		if($user == $res[0]->from && $pass == $res[0]->reply){
			echo 'Same Data Found. So quiting';
		} else {
			$sql = "UPDATE `".$wpdb->prefix."emailsettings` SET `from`='$user',`reply`='$pass'";
			$res1 = $wpdb->query($sql);
			if($res1){ echo "Settings updated sucessfully."; } else { echo "Update error! Please try again.<br>".$sql.mysql_error(); }
		}
	} else {
		$sql="INSERT INTO `".$wpdb->prefix."emailsettings` (`from`,`reply`) VALUES ('$user','$pass')";
		$res1=$wpdb->query($sql);
		if($res1){ echo "Settings saved sucessfully"; } else { echo "Insert error! Please try again.<br>".$sql.mysql_error(); }
	}

	die();
}
	
///	SAM *******************

	function loadPropCampaigns_callback(){
		global $wpdb;
		$s_propid = $_POST['s_propid'];
		include('loadPropCampaigns.php');
		die();
	}

	//This function creates the template preview
	// add/remove a capability to the user and grant access based on that capability
	function setApprovePermission_callback(){
		$userPermission = $_POST['s_userPermission'];
		$adminUserId = $_POST['s_adminUserId'];
		$user = new WP_User( $adminUserId );
		global $wp_roles,$wpdb;
		
		$capability_name = 'approve_message';
	
		if($userPermission){
			$user->add_cap($capability_name);
			$res=$wpdb->query("INSERT INTO `".$wpdb->base_prefix."capabilities` (userId,capability,status) VALUES('$adminUserId ','$capability_name','1') ");
			$message = 'Capability to the user has been set.';
		} else {
		       $res=$wpdb->query("DELETE FROM `".$wpdb->base_prefix."capabilities` WHERE `userId`='$adminUserId' AND `capability`='$capability_name'");
			$user->remove_cap($capability_name);
			$message = 'Capability to the user has been removed.';
		}
		echo $message;
/*
// check whether the user has a certain capability or role name
if ( $user->has_cap( $cap_name ) ) {
    // do something
}
*/

		die();
	}

	function setCampaignPermission_callback(){
		$userPermission = $_POST['s_userPermission'];
		$adminUserId = $_POST['s_userId'];
		$user = new WP_User( $adminUserId );
		global $wp_roles,$wpdb;
		
		$capability_name = 'access_campainmanager';
	
		if($userPermission){
			$user->add_cap($capability_name);
			$res=$wpdb->query("INSERT INTO `".$wpdb->base_prefix."capabilities` (userId,capability,status) VALUES('$adminUserId ','$capability_name','1') ");
			$message = 'User granted access to campaingmanager.';
		} else {
		       $res=$wpdb->query("DELETE FROM `".$wpdb->base_prefix."capabilities` WHERE `userId`='$adminUserId' AND `capability`='$capability_name'");
			$user->remove_cap($capability_name);
			$message = 'User blocked access to campaingmanager.';
		}
		echo $message;
/*
// check whether the user has a certain capability or role name
if ( $user->has_cap( $cap_name ) ) {
    // do something
}
*/

		die();
	}
	//This function creates the template preview
	function createTempPreview_callback(){
		$s_temp_footer = stripslashes($_POST['s_temp_footer']);
		global $blog_id, $wpdb;
		$email_logo_path=get_site_url()."/wp-content/plugins/campaign-manager/images/logo/".$blog_id.".png";
		//$logo='<img src="'.$email_logo_path.'"/>';
		$logo = $email_logo_path;
		
		
		
		$sql = "SELECT  * 
			FROM ". $wpdb->base_prefix."properties 
			WHERE id = ".$blog_id ;
			$propObjects = $wpdb->get_results($sql);
			//echo '<pre>';print_r($propObjects); echo '</pre>';
		
			$propid = $propObjects[0]->id;
			$property_name = $propObjects[0]->property_name;
			$twitter_url = $propObjects[0]->twitter_url;
			$facebook_url = $propObjects[0]->facebook_url;
			$pinterest_url = $propObjects[0]->pinterest_url;
			$google_url = $propObjects[0]->google_url;
			$youtube_url = $propObjects[0]->youtube_url;
			$streetaddress = $propObjects[0]->streetaddress;
			$city = $propObjects[0]->city;
			$state = $propObjects[0]->state;
			$zip = $propObjects[0]->zip;
			$phone = $propObjects[0]->phone;
			$email = $propObjects[0]->email;
			$property_domain_url = $propObjects[0]->property_domain_url;
			
		        //$domain = '<a href="'.get_site_url().'" target="_blank">'.get_site_url().'</a>'; Jan 08 '13 sam
		        $domainUrl = get_site_url(); // Eg. http://somdev1.us
		        $domainName = explode('//', $domainUrl);
		        ///$s_temp_footer = stri_replace("{email_logo}",$logo,$s_temp_footer); // 07 Jan 13 sam
		        $s_temp_footer = stri_replace("{email_logo}",$email_logo_path,$s_temp_footer); //Translate just src of d email logo
		        $s_temp_footer = stri_replace("{domain}",$domainName[1],$s_temp_footer);
		        $s_temp_footer = stri_replace("{domain_url}",$domainUrl,$s_temp_footer);
		
			$s_temp_footer = str_replace("{twitter_url}", $twitter_url, $s_temp_footer);
			$s_temp_footer = str_replace("{facebook_url}", $facebook_url, $s_temp_footer);
			$s_temp_footer = str_replace("{pinterest_url}", $pinterest_url, $s_temp_footer);
			$s_temp_footer = str_replace("{google_url}", $google_url, $s_temp_footer);
			$s_temp_footer = str_replace("{youtube_url}", $youtube_url, $s_temp_footer);
			$s_temp_footer = str_replace("{property_name}", $property_name, $s_temp_footer);
			$s_temp_footer = str_replace("{streetaddress}", $streetaddress, $s_temp_footer);
			$s_temp_footer = str_replace("{city}", $city, $s_temp_footer);
			$s_temp_footer = str_replace("{state}", $state, $s_temp_footer);
			$s_temp_footer = str_replace("{zip}", $zip, $s_temp_footer);
			$s_temp_footer = str_replace("{phone}", $phone, $s_temp_footer);
			$s_temp_footer = str_replace("{email}", $email, $s_temp_footer);
		echo $s_temp_footer;
		die();
	}
	function stri_replace( $find, $replace, $string ) {
		$parts = explode( strtolower($find), strtolower($string) );
		$pos = 0;
		foreach( $parts as $key=>$part ){
			$parts[ $key ] = substr($string, $pos, strlen($part));
			$pos += strlen($part) + strlen($find);
		}
		return( join( $replace, $parts ) );
	}
	function messagePages_callback() {
		global $wpdb; // this is how you get access to the database
		$s_field = $_POST['s_field_key'];
		$s_order = $_POST['s_order_key']; 
		$s_type = $_POST['s_type_key'];
		$s_q = $_POST['s_q'];
		$s_curId = $_POST['s_curId'];

		$s_dtPicker = $_POST['s_dtPicker'];
		//$s_props = $_POST['s_props'];
		$s_searchq = $_POST['s_searchq'];
		$s_rowlen = $_POST['s_rowlen'];
		$s_page = $_POST['s_page'];
		$s_campName = $_POST['s_campName'];
		$s_cProperties = $_POST['s_cProperties'];

		include('messagePages.php');
		die(); // this is required to return a proper result
	}
/* end */


	function sendTempPreviewEmail_callback(){
		global $wpdb;
		$templateid = $_POST['s_templateid'];
		$content = $_POST['s_content'];

		include('sendTempPreviewEmail.php');
		die();
	}

	function saveMessage_callback(){
		global $wpdb;
		$template_list = $_POST['s_template_list'];
		$campaignID = $_POST['s_campaignID'];
		$message_name = $_POST['s_message_name'];
		$datepicker20 = $_POST['s_datepicker20'];
		$content = $_POST['s_content'];
		$s_hidMid = $_POST['s_hidMid'];
		$s_mode = $_POST['s_mode'];
		$s_textdata =$_POST['s_textdata'];
                $s_cProperties=$_POST['s_cProperties'];
		include('saveMessageData.php');
		die();
	}

	function LoadTemplate_callback(){ 
		global $wpdb;
		$getTtemplateId = $_POST['s_templateid'];
		$propertySelected = $_POST['s_cProperties'];	
		include('getTemplateData.php');
		die();
	}
	
	function LoadTemplateNotrans_callback(){ 
		global $wpdb;
		$getTtemplateId = $_POST['s_templateid'];
		$propertySelected = $_POST['s_cProperties'];	
		include('getTemplateDataNotrans.php');
		die();
	}	

	function SaveCampaign_callback(){
		global $wpdb;
		$campaign_name = $_POST['s_campaign_name'];
		$sent_date = $_POST['s_sent_date'];
		$categoryId = $_POST['s_categoryId'];
		$propertyId = $_POST['s_propertyId'];
		$list = $_POST['s_list'];
	
		include('saveCampaign.php');
		die(); // this is required to return a proper result
	}

	function UpdateCampaign_callback(){
		global $wpdb;
		$campaign_name = $_POST['s_campaign_name'];
		$sent_date = $_POST['s_sent_date'];
		$categoryId = $_POST['s_categoryId'];
		$propertyId = $_POST['s_propertyId'];
		$list = $_POST['s_list'];
		$hidCampID = $_POST['s_hidCamp'];
	
		include('updateCampaign.php');
		die(); // this is required to return a proper result
	}


	function printView_callback() {
		global $wpdb; // this is how you get access to the database
		$s_field = $_POST['s_field_key'];
		$s_order = $_POST['s_order_key']; 
		$s_type = $_POST['s_type_key'];
		$s_q = $_POST['s_q'];

		$s_dtPicker = $_POST['s_dtPicker'];
		$s_props = $_POST['s_props'];
		$s_searchq = $_POST['s_searchq'];
		$s_rowlen = $_POST['s_rowlen'];
		$s_page = $_POST['s_page'];
		$s_campidValues = $_POST['s_campidValues'];

		include('print.php');
		die(); // this is required to return a proper result
	}

	function delete_callback() {
		global $wpdb; // this is how you get access to the database
		$s_field = $_POST['s_field_key'];
		$s_order = $_POST['s_order_key']; 
		$s_type = $_POST['s_type_key'];
		$s_q = $_POST['s_q'];

		$s_dtPicker = $_POST['s_dtPicker'];
		$s_props = $_POST['s_props'];
		$s_searchq = $_POST['s_searchq'];
		$s_rowlen = $_POST['s_rowlen'];
		$s_page = $_POST['s_page'];
		$s_cid = $_POST['s_cid'];

		include('test.php');
		die(); // this is required to return a proper result
	}

	function propCat_callback(){ // Create property and category drops
		$s_ctrlType = $_POST['s_ctrlType_key'];
		$s_order = $_POST['s_order'];
		$s_ctype = $_POST['s_ctype'];
		include('createpropertylist.php');
		die();
	}
	##Sujith >>>>>>>>modified:01 Oct2012#

	function SaveCat_callback(){ // Save new category created
		$CatName=$_POST['NewCat'];
		$relType = $_POST['relTabName'];
		$propSelected = $_POST['propSelected'];
		include("savecategory.php");
		die();
	}
	function SaveSendGrid_callback(){
	
	global $wpdb;
	$res1=FALSE;
	$user=$_POST['username'];
	$pass=$_POST["password"];
	$res=mysql_query("SELECT * FROM `wp_sendgrids`");
	if(mysql_num_rows($res)>0) $sql="UPDATE `wp_sendgrids` SET Username='$user',password='$pass'";
	else $sql="INSERT INTO `wp_sendgrids` (Username,password) VALUES ('$user','$pass')";
	$res1=$wpdb->query($sql);
	 if($res1) echo "Settings saved sucessfully";
	 else echo "Some error occured!Please try again.";
	 die();
	}
	######<<<<<<<<<<<#


	function action_javascript() {
?>

<style>
	#toplevel_page_mvc_categories,#toplevel_page_mvc_properties,#toplevel_page_mvc_sendgrids,#toplevel_page_mvc_lists{
		display:none;
	}
</style>

	<script type="text/javascript" >


	function getPropertyBasedLists(selectedProp){
		var data = {
			action: 'getPropertyBasedList',
			s_selectedProp : selectedProp,

		};
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		jQuery.post(ajaxurl, data, function(response) {
			//alert(response);
			$('#list').html(response);
		});
	}
	function getPropertyBasedCategories(selectedProp){
		var data = {
			action: 'getPropertyBasedCategories',
			s_selectedProp : selectedProp,
		};
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		jQuery.post(ajaxurl, data, function(response) {
			$('#categoryId').html(response);
		});
	}
//delete_category_group // delete_campaign_group
	//Delete multiple category group
//----------------------------------------------------------------------------------------------------Delete category group

	function delete_category_group(id,type,property){ // line  111

		//var atLeastOneIsChecked = $("input:checkbox[name=case]:checked").length; // > 0;
		var msg = 'Do you really want to delete this category?';
		if($("input:checkbox[name=case]:checked").length > 1){
			msg = 'Do you really want to delete these categories?';
		}
		if($("input:checkbox[name=case]:checked").length > 0){
			var v = confirm(msg);
			if(v){
	
				var selectedRows = new Array();
				$("input:checkbox[name=case]:checked").each(function() {
					selectedRows.push($(this).val());
				});
				//alert(selectedRows.toSource()); // print array
	
				var data = {
					action: 'delete_categoryGroup',
					s_selectedRows : selectedRows,
					id : id,
					type:type,
					propId:property
		
				};
				ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
				jQuery.post(ajaxurl, data, function(response) {
					
					//$('.my-element').fadeIn();
					$('#responceDiv').show();
					$('#responceDiv').html(response);

					$('#responceDiv').fadeOut(6000, function() {
						category_pagination(0,0,0,'0.5',1);
					});
				});
			}
		} else { alert("You didn't choose any of the checkboxes!");}
	}
//---------- End category---------

	//Delete multiple campaigns
//----------------------------------------------------------------------------------------------------Delete campaign group
	function delete_campaign_group(){ // line  111

		//var atLeastOneIsChecked = $("input:checkbox[name=case]:checked").length; // > 0;
		if($("input:checkbox[name=case]:checked").length > 0){
			var v = confirm("Are you sure that you want to delete this campaign(s) and all related messages?");
			if(v){
	
				var selectedRows = new Array();
				$("input:checkbox[name=case]:checked").each(function() {
					selectedRows.push($(this).val());
				});
				//alert(selectedRows.toSource()); // print array
	
				var data = {
					action: 'delete_campaignGroup',
					s_selectedRows : selectedRows,
		
				};
				ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
				jQuery.post(ajaxurl, data, function(response) {
					
					//$('.my-element').fadeIn();
					$('#responceDiv').show();
					$('#responceDiv').html(response);

					$('#responceDiv').fadeOut(6000, function() {
						delete_msg_board(0,0,0,'0.5',1);
					});
				});
			}
		} else { alert("You didn't choose any of the checkboxes!");}
	}
	//Delete multiple messages
//----------------------------------------------------------------------------------------------------Delete messages group
	function delete_message_group(){ // line  111

		//var atLeastOneIsChecked = $("input:checkbox[name=case]:checked").length; // > 0;
		if($("input:checkbox[name=case]:checked").length > 0){
			var v = confirm("Are you really want to delete these messages?");
			if(v){
	
				var selectedRows = new Array();
				$("input:checkbox[name=case]:checked").each(function() {
					selectedRows.push($(this).val());
				});
				//alert(selectedRows.toSource()); // print array
	
				var data = {
					action: 'delete_message_group',
					s_selectedRows : selectedRows,
		
				};
				ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
				jQuery.post(ajaxurl, data, function(response) {
					
					//$('.my-element').fadeIn();
					$('#responceDiv').show();
					$('#responceDiv').html(response);

					$('#responceDiv').fadeOut(5000, function() {
						//delete_msg_board(0,0,0,'0.5',1);
						paginatMessage(0,0,0,'0.5',1);
					});
				});
			}
		} else { alert("You didn't choose any of the checkboxes!");}
	}

//-----------------delete template>>>>>>>>>>>>>>>>> Sujith 10 dec 2012
	function delete_template_group(){ // line  111

		//var atLeastOneIsChecked = $("input:checkbox[name=case]:checked").length; // > 0;
		if($("input:checkbox[name=case]:checked").length > 0){
			var v = confirm("Are you really want to delete these templates?");
			if(v){
	
				var selectedRows = new Array();
				$("input:checkbox[name=case]:checked").each(function() {
					selectedRows.push($(this).val());
				});
				//alert(selectedRows.toSource()); // print array
	
				var data = {
					action: 'delete_templateGroup',
					s_selectedRows : selectedRows,
		
				};
				ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
				jQuery.post(ajaxurl, data, function(response) {
					//alert(response);
					//$('.my-element').fadeIn();
					$('#responceDiv').show();
					$('#responceDiv').html(response);

					$('#responceDiv').fadeOut(5000, function() {
						paginateTemplate('id','DESC',0,'0.5',1);
					});
				});
			}
		} else { alert("You didn't choose any of the checkboxes!");}
	}

//<<---------------delete template<<<<<<<<<<<<<<<<<
	//sujith dec 3 >> 
		function showIt(imgId,classid){
		imgdir="http://somdev1.us/wp-content/plugins/campaign-manager/images/";
		//alert("<?php echo $pluginImagesDir; ?>");
		$('#row_'+imgId).css("background-color", '#DCEBFE');
		$('#editImg-'+imgId).attr('src', imgdir+'edit-hover.png');
		$('#dropImg-'+imgId).attr('src', imgdir+'delete-hover.png');
		
	}
	
	function hideIt(imgId,classid){
		$('#row_'+imgId).css("background-color", classid);
		$('#editImg-'+imgId).attr('src', imgdir+'edit.png');
		$('#dropImg-'+imgId).attr('src', imgdir+'delete.png');	
	}
	//sujith dec 3<<
	
	//sujith nov 06
	function updateSiteId(){
var s_siteId=$("#siteid").val();
		var data = {
			action: 'saveSiteId',
			siteId : s_siteId,
	
		};
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		jQuery.post(ajaxurl, data, function(response) {
			$('#siteIdRes').html(response);
		});
}
	
/*sujith oct 2*/

	function delete_cat(id,type,property){
		 var c=confirm("Do you really want to delete this category?");
		 if(c){
		 var data = {
			action: 'delete_category',
			id : id,
			type:type,
			propId:property

		};
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";

		jQuery.post(ajaxurl, data, function(response) {
			
 			alert(response);
 			if(response!="Category already assigned.Can't delete!")    
 			category_pagination('id','ASC','','');
		});
		  }
	}
/*sujith oct 22 */
/*sujith oct 23 */
function category_pagination(s_field,s_order,s_type,s_page,cid){

$('.loadingDiv').show();

		var prop = $('#cType').val();
		var sq = $('#searchQ').val();
		var rowlen = $('#row_length').val();

		if(s_type == 's_search'){
			var s_query = $('#'+s_order).val();
			s_order = '';
		}
		var data = {
			action: 'manage_category',
			s_field_key : s_field,
			s_order_key : s_order,
			s_type_key : s_type,
			s_q : s_query,
			s_props : prop,
			s_searchq : sq,
			s_rowlen : rowlen,
			s_page : s_page,
			s_cid : cid,
		};

		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		jQuery.post(ajaxurl, data, function(response) {
			if(response){
				//alert(response);
				$('#example').html(response);
				$('.loadingDiv').hide();
			}else{
				$('.full_width').prepend('<div id="messageErr">Unable to process your request at this moment</div>');
				$('#message').hide(2000, function () {
					$('#message').remove();
				});
			}
		});


}
/*sujith oct 23 */
/*     VIDHU       */


	function saveTemp(){

		TempName=jQuery("#template_name").val();
		TempPreview=jQuery("#preview").val();
// 		TempHeader=jQuery("#header").val();
		editor = tinymce.get('content');
		TempFooter=editor.getContent();
		Tempactive=jQuery("#active").val();
		var elem = document.getElementById('temp_thumb');
                    file = elem.files && elem.files[0];
		    if(file==undefined || !(file)   ){
		      Tempimg="";
		    }else{
		        Tempimg=jQuery("#temp_thumb").val();
		    }
		var data = {
			action: 'saveTemplate',
			Name : TempName,
			Preview : TempPreview,
// 			Header : TempHeader,
			Footer : TempFooter,
			Active : Tempactive,
			Tempimg :Tempimg
		};
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		document.getElementById("response").innerHTML = "Saving . . .";
		jQuery.post(ajaxurl, data, function(response) {/* alert(response);*/
			jQuery('#response').html(response);
			setTimeout(function() { 
				jQuery('#response').hide(); 
				jQuery("#pop").dialog('destroy');
				window.location.href = "<?php echo bloginfo('wpurl');?>/wp-admin/admin.php?page=mvc_campaigns-templates";
			}, 3000);
		});
	}
		function paginateTemplate(s_field,s_order,s_type,s_page,cid) {
		$('.loadingDiv').show();
		var datatep = $('#datepicker').val();
		var prop = $('#cProperties').val();
		var sq = $('#searchTemplate').val();
		var rowlen = $('#row_length').val();

		if(s_type == 's_search'){
			var s_query = $('#'+s_order).val();
			s_order = '';
		}
		var data = {
			action: 'templatePages',
			s_field_key : s_field,
			s_order_key : s_order,
			s_type_key : s_type,
			s_q : s_query,
			s_dtPicker : datatep,
			/*s_props : prop,*/
			s_searchq : sq,
			s_rowlen : rowlen,
			s_page : s_page,
			s_cid : cid,
		};

		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		jQuery.post(ajaxurl, data, function(response) {
			if(response){
				//alert(response);
				$('#example').html(response);
				$('.loadingDiv').hide();
			}else{
				$('.full_width').prepend('<div id="messageErr">Unable to process your request at this moment</div>');
				$('#message').hide(2000, function () {
					$('#message').remove();
				});
			}
		});
	}
	function ListPages(s_field,s_order,s_type,s_page,cid) {
// 	alert(cid);
		$('.loadingDiv').show();
		var datatep = $('#datepicker').val();
		var prop = $('#cProperties').val();
		var sq = $('#searchList').val();
		var rowlen = $('#row_length').val();

		if(s_type == 's_search'){
			var s_query = $('#'+s_order).val();
			s_order = '';
		} 
		var data = {
			action: 'listPages',
			s_field_key : s_field,
			s_order_key : s_order,
			s_type_key : s_type,
			s_q : s_query,
			s_dtPicker : datatep,
			s_searchq : sq,
			s_rowlen : rowlen,
			s_page : s_page,
			s_cid : cid,
			s_props : prop,
		};

		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		jQuery.post(ajaxurl, data, function(response) {
			if(response){
				//alert(response);
				$('#example').html(response);
				$('.loadingDiv').hide();
			}else{
				$('.full_width').prepend('<div id="messageErr">Unable to process your request at this moment</div>');
				$('#message').hide(2000, function () {
					$('#message').remove();
				});
			}
		});
	}
	
/*     GLORY       */
	function saveAddList(){
		list_name=$("#ListsListName").val();
		list_description=$("#ListsDescription").val();
		category=$("#categoryId").val();
		category2=$("#categoryId2").val();
		active=$("#active").val();
		list_tag=$("#ListsTags").val();
		
			leftf1=$("#fieldleft_1").val();
			leftf2=$("#fieldleft_2").val();
			leftf3=$("#fieldleft_3").val();
			rightf1=$("#fieldright_1").val();
			rightf2=$("#fieldright_2").val();
			rightf3=$("#fieldright_3").val();
			csvname=$("#csvname").val();
				var resident_values,parent_values,prospects_values;
				var term_types = [];
				$(':checkbox:checked').each(function(i){
					term_types[i] = $(this).val();
				});
				if (term_types.indexOf('Residents') != -1){
					resident_values="school_term:"+$("#school_term_res :selected").text()+";";
				}
				if (term_types.indexOf('Parents') != -1){
					parent_values="school_term:"+$("#school_term_par :selected").text()+";";
				}
				if (term_types.indexOf('Prospects') != -1){
					prospects_values="All";
				}
				if($('#chk_testlist').is(':checked') ){
					var chk_testlist=1;
				}else{
					var chk_testlist=0;
				}
		var data = {
			action: 'SaveLists',
			l_list_name : list_name,
			l_list_description : list_description,
			l_category : category,
			l_category2 : category2,
			l_leftf1 : leftf1,
			l_leftf2 : leftf2,
			l_leftf3 : leftf3,
			l_rightf1 : rightf1,
			l_rightf2 : rightf2,
			l_rightf3 : rightf3,
			l_csvname :csvname,
			l_active:active,
			l_tag:list_tag,
			l_resident_values:resident_values,
			l_parent_values:parent_values,
			l_prospects_values:prospects_values,
			l_chk_testlist:chk_testlist
		};
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		jQuery.post(ajaxurl, data, function(response) {
			$('#addLResponceDiv').html(response);
			$('#addLResponceDiv').css("color",'#008000');
			$('#addLResponceDiv').css("font-weight",'bold');
			$('#addLResponceDiv').show();
			setTimeout(function() { 
				$('#addLResponceDiv').hide(); 
				$("#pop").dialog('destroy');
				$("#listForm").reset();
				$("#response").html("");
			}, 3000); // Stay 3 seconds
			ListPages('list_name','ASC',0,'0.5','1');
			//setTimeout("window.location.reload()",3002);
		});
	}

		function loadListCategory(ctrlType, orderby, loadInId, perform,type){
		//alert(ctrlType+''+ orderby +''+ loadInId+''+perform);
		 if(typeof(type)==='undefined') type = 1;
		var data = {
			action: 'loadListCat',
			s_ctrlType_key : ctrlType,
			s_order : orderby,
			s_type  : type
		};

		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php

		jQuery.post(ajaxurl, data, function(response) {
			if(response){

				$('#'+loadInId).html(response);
			}else{
				if($('#messageErr').length == 0){
					$('.full_width').prepend('<div id="messageErr">An error has happened during processing. </div>');
				} else {
					$('#messageErr').replaceWith('<div id="messageErr">An error has happened during processing. </div>');
				}
				
				$('#message').hide(2000, function () {
					$('#message').remove();
				});
			}
		});
	}

/*     SAM & SUJITH  */

	function changeSystemMode(mode){
		var data = {
			action: 'updateSystemMode',
			s_mode : mode,
		};
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		jQuery.post(ajaxurl, data, function(response) {
			//$('#testCamp').html(response); 
			$('#systemModeRes').show();
			$('#systemModeRes').html(response);
			$('#systemModeRes').hide(2000, function () {
				//$('#systemModeRes').remove();
			});
		});
	}

	// Load Campaign of selected property
	function loadCampaigns(pid){
		var propid = pid
		var data = {
			action: 'loadPropCampaigns',
			s_propid : propid,
	
		};
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		jQuery.post(ajaxurl, data, function(response) {
			//$('#testCamp').html(response); 
			$('#campaignID').html(response);
		});
	}

	// Set user permission to approve message
	function setUserPermission(uid){
	
		var userPermission;
		var adminUserId = uid;
		if($('#admn_'+uid).is(':checked')){
			userPermission = 1;
		} else {
			userPermission = 0;
		}
		var data = {
			action: 'setApprovePermission',
			s_userPermission : userPermission,
			s_adminUserId : adminUserId,
	
		};
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		jQuery.post(ajaxurl, data, function(response) {
		        $('#permissionMsg').css('background-color','#CCFF99');
			$('#permissionMsg').html(response);
			$("#permissionMsg").show();
		});
		setTimeout(function() {
                        $("#permissionMsg").hide("slow");
                }, 4000);
	
	}
	function setCampaignPermission(uid){
	
		var setCampaignPermission;
		var userId = uid;
		if($('#acessCampaign_'+uid).is(':checked')){
			userPermission = 1;
		} else {
			userPermission = 0;
		}
		var data = {
			action: 'setCampaignPermission',
			s_userPermission : userPermission,
			s_userId : userId,
	
		};
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		jQuery.post(ajaxurl, data, function(response) {
		        $('#permissionMsg').css('background-color','#CCFF99');
			$('#permissionMsg').html(response);
			$("#permissionMsg").show();
		});
		setTimeout(function() {
                        $("#permissionMsg").hide("slow");
                }, 4000);
	
	}
/* paginate Message */
	function paginatMessage(s_field,s_order,s_type,s_page,id) {
		//alert(s_field+' : '+s_order+' : '+s_type+' : '+s_page);
		$('.loadingDiv').show();
		var datatep = $('#datepicker').val();
		var prop = $('#cProperties').val();
		var sq = $('#searchQ').val();
		var rowlen = $('#row_length').val();
		var campName = $('#campaignlst').val();
		var cProperties = $('#cProperties').val();

		if(s_type == 's_search'){
			var s_query = $('#'+s_order).val();
			s_order = '';
		}
		var data = {
			action: 'messagePages',
			s_field_key : s_field,
			s_order_key : s_order,
			s_type_key : s_type,
			s_q : s_query,
			s_dtPicker : datatep,
			s_searchq : sq,
			s_rowlen : rowlen,
			s_page : s_page,
			s_curId : id,
			s_campName : campName,
			s_cProperties : cProperties,
		};

		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		jQuery.post(ajaxurl, data, function(response) {
			if(response){
				//alert(response);
				$('#example').html(response);
				$('.loadingDiv').hide();
			}else{
				$('.full_width').prepend('<div id="messageErr">Unable to process your request at this moment</div>');
				$('#message').hide(2000, function () {
					$('#message').remove();
				});
			}
		});
	}
/* *******************  */

	function createPreview(){
		editor = tinymce.get('content');
		temp_footer=editor.getContent();
		var data = {
			action: 'createTempPreview',
			s_temp_footer : temp_footer,

		};
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		jQuery.post(ajaxurl, data, function(response) {
			jQuery('#pevPlace').html(response);
		});

	}

	function sendPreviewMessage(){
		//alert($("#previewMailId").val());
		var mailID = $("#previewMailId").val();
		var editor = tinymce.get('content');
		var content = editor.getContent();
		//console.log(content);
		var data = {
			action: 'sendTempPreviewEmail',
			s_templateid : mailID,
			s_content : content,

		};
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		jQuery.post(ajaxurl, data, function(response) {
			//alert(response); 
			$('#statmsg').html(response);
		});

	}


	function saveaddMessage(){
	        //alert($('#cProperties').val());
	
		var template_list = $("#template_list").val();
		var campaignID = $("#campaignID").val();
		var message_name = $("#message_name").val();
		var datepicker20 = $("#datepicker20").val();
		var textdata = $("#content2").val();
		var hidMid = $('#hidMid').val();
		var mode = $('#mode').val();
		var editor = tinymce.get('content');
		var content = editor.getContent();
		var cProperties = $('#cProperties').val();

		var data = {
			action: 'saveMessage',
			s_template_list : template_list,
			s_campaignID : campaignID,
			s_message_name : message_name,
			s_datepicker20 : datepicker20,
			s_content : content,
			s_hidMid : hidMid,
			s_mode : mode,
			s_textdata:textdata,
			s_cProperties : cProperties,

		};

		jQuery.post(ajaxurl, data, function(response) {

			var matches = response.split(/\b/);
			if(matches.length > 0) { var errorType = matches[0]; }
			if(errorType == 'Error'){
				$('#msgResponceDiv').css("background-color",'#FFBABA');
			} else { $('#msgResponceDiv').css("background-color",'#DDFFDD');}

			$('#msgResponceDiv').html(response);
			
			$('#msgResponceDiv').show();
			setTimeout(function() {
				$('#msgResponceDiv').hide();
			}, 30000); // Stay 3 seconds
			window.location = 'admin.php?page=mvc_campaigns-message';
		});
		
	}
	function loadTemplateData(templateid){

		cProperties = jQuery('#cProperties').val();
		if(cProperties==""){
		cProperties=jQuery('#cProperties2').val();
		}
		var data = {
			action: 'LoadTemplate',
			s_templateid : templateid,
			s_cProperties : cProperties,
		};
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		jQuery.post(ajaxurl, data, function(response) {
			doIt = confirm('If you select another template, the change that you made will be erased. Do you wish to proceed?');
 			if(doIt){
 			        jQuery("#template_list").val(templateid);
				var editor = tinymce.get('content');
				var content = editor.getContent();
				editor.setContent(response);
				
			}
		});
	}
	function loadTemplateDataNotrans(templateid){

		cProperties = jQuery('#cProperties').val();
		if(cProperties==""){
		cProperties=jQuery('#cProperties2').val();
		}
		var data = {
			action: 'LoadTemplateNotrans',
			s_templateid : templateid,
			s_cProperties : cProperties,
		};
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		jQuery.post(ajaxurl, data, function(response) {
			doIt = confirm('If you select another template, the change that you made will be erased. Do you wish to proceed?');
 			if(doIt){
 			        jQuery("#template_list").val(templateid);
				var editor = tinymce.get('content');
				var content = editor.getContent();
				editor.setContent(response);
				
			}
		});
	}	

	function saveAddCampaign(){

		campaign_name = $("#campaign_name").val();
		sent_date = $("#datepicker2").val();
		categoryId = $("#categoryId").val();
		propertyId = $("#propertyId").val();
		list = $("#list").val();

		var listSelected = []; // To get multiple selected values
		$('#list :selected').each(function(i, selected){
			listSelected[i] = $(selected).val();
		});

		var data = {
			action: 'SaveCampaign',
			s_campaign_name : campaign_name,
			s_sent_date : sent_date,
			s_categoryId : categoryId,
			s_propertyId : propertyId,
			s_list : listSelected,
		};
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		jQuery.post(ajaxurl, data, function(response) {
			$('#editResponceDiv').html(response);
			$('#editResponceDiv').css("background-color",'#DDFFDD');
			$('#editResponceDiv').show();
			setTimeout(function() { 
				$('#editResponceDiv').hide(); 
				$("#pop").dialog('destroy');
				document.getElementById("addForm").reset();
			}, 3000); // Stay 3 seconds
			delete_msg_board(0,0,0,'0.5',1);
		});
	}

	function updateCampaign(){
		campaign_name = $('#CampaignCampaignName').val();
		sent_date = $('#datepicker3').val();
		categoryId = $('#Campaign_Category_select').val();
		propertyId = $('#Campaign_Property_select').val();
		list = $('#listedit').val();
		cmpHidId = $('#CampaignHiddenId').val();
		propertyName = $('#Campaign_Property_select :selected').text();


		var listSelected = []; // To get multiple selected values
		$('#listedit :selected').each(function(i, selected){
			listSelected[i] = $(selected).val();
		});

		var data = {
			action: 'UpdateCampaign',
			s_campaign_name : campaign_name,
			s_sent_date : sent_date,
			s_categoryId : categoryId,
			s_propertyId : propertyId,
			s_list : listSelected,
			s_hidCamp : cmpHidId,
		};
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		jQuery.post(ajaxurl, data, function(response) {
			//window.location.href = "<?php echo bloginfo('wpurl');?>/wp-admin/admin.php?page=mvc_campaigns-templates";
			//alert(response);
			$('#editMsgDiv').html(response);
			$('#editMsgDiv').css("background-color",'#DDFFDD');
			$('#editMsgDiv').show();
			$('#cname_'+cmpHidId).html(campaign_name); 
			if(propertyName.length > 0){ $('#pname_'+cmpHidId).html(propertyName); } 
			$('#sdate_'+cmpHidId).html(sent_date);
			setTimeout(function() { 
				$('#editMsgDiv').hide();
				$("#editDialog").dialog('destroy');
			}, 3000); // Stay 3 seconds
                        delete_msg_board(0,0,0,'0.5',1);
						
			
			//alert($('#row_'+cmpHidId).html());

			
		});
	}

	function showPrintView(ctrlType, orderby, loadInId, perform){
		//alert(ctrlType+''+ orderby +''+ loadInId+''+perform);
		var campidValues = [];
		$('input[name="campid[]"]').each(function(){
			campidValues.push($(this).val());
		});

		var datatep = $('#datepicker').val();
		var prop = $('#cProperties').val();
		var sq = $('#searchQ').val();
		var rowlen = $('#row_length').val();

		var data = {
			action: 'loadPrintView',
			s_ctrlType_key : ctrlType,
			s_dtPicker : datatep,
			s_props : prop,
			s_searchq : sq,
			s_rowlen : rowlen,
			s_campidValues : campidValues,
		};

		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php

		jQuery.post(ajaxurl, data, function(response) {
			if(response){
				$('#'+loadInId).html(response);
			}else{
				if($('#messageErr').length == 0){
					$('.full_width').prepend('<div id="messageErr">An error has happened during processing. </div>');
				} else {
					$('#messageErr').replaceWith('<div id="messageErr">An error has happened during processing. </div>');
				}
				
				$('#message').hide(2000, function () {
					$('#message').remove();
				});
			}
		});
	}


	function loadProprtyAndCategory(ctrlType, orderby, loadInId, perform, ctype){
		//alert(ctrlType+''+ orderby +''+ loadInId+''+perform);
		var data = {
			action: 'loadPropCat',
			s_ctrlType_key : ctrlType,
			s_order : orderby,
			s_ctype : ctype,
		};

		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php

		jQuery.post(ajaxurl, data, function(response) {
			if(response){

				$('#'+loadInId).html(response);
			}else{
				if($('#messageErr').length == 0){
					$('.full_width').prepend('<div id="messageErr">An error has happened during processing. </div>');
				} else {
					$('#messageErr').replaceWith('<div id="messageErr">An error has happened during processing. </div>');
				}
				
				$('#message').hide(2000, function () {
					$('#message').remove();
				});
			}
		});
	}

	function delete_msg_board(s_field,s_order,s_type,s_page,cid) {
		//alert(s_field+' : '+s_order+' : '+s_type+' : '+s_page);
		$('.loadingDiv').show();
		var datatep = $('#datepicker').val();
		var prop = $('#cProperties').val();
		var sq = $('#searchQ').val();
		var rowlen = $('#row_length').val();

		if(s_type == 's_search'){
			var s_query = $('#'+s_order).val();
			s_order = '';
		}
		var data = {
			action: 'delete',
			s_field_key : s_field,
			s_order_key : s_order,
			s_type_key : s_type,
			s_q : s_query,
			s_dtPicker : datatep,
			s_props : prop,
			s_searchq : sq,
			s_rowlen : rowlen,
			s_page : s_page,
			s_cid : cid,
		};

		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		jQuery.post(ajaxurl, data, function(response) {
			if(response){
				//alert(response);
				$('#example').html(response);
				$('.loadingDiv').hide();
			}else{
				$('.full_width').prepend('<div id="messageErr">Unable to process your request at this moment</div>');
				$('#message').hide(2000, function () {
					$('#message').remove();
				});
			}
		});
	}

/* SAVE CATEGORY */

	function SaveCat(relationType){ //relationType 1 = campaign; relationType 2 = lists
		CatName= $("#CreateCat").val();
		var propSelected = $("#propertyId").val();
		var data = {
			action: 'SaveCategory',
			NewCat : CatName,
			relTabName : relationType,
			propSelected : propSelected,
		};
		
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		jQuery.post(ajaxurl, data, function(response) {
		       var parts = response.split(':');
                       if(parts[0] == 'Error'){
		                $('#catresp').css("background-color",'#FFEDEF');
		                $('#catresp').html(response);
		        } else {
		                $('#catresp').html(response);
			        setTimeout(function() { $('#catform').hide(); }, 3000);
			        setTimeout(function() { $('#catresp').hide(); }, 3000);
			        loadListCategory(propSelected, 'id','categoryId',0,1);
			}
			/*if(relationType == 2){ 
				loadListCategory('categories', 'id','categoryId',0);	
			}else{
			        loadProprtyAndCategory('categories', 'id','categoryId',0);// Populate Category dropdown
			}*/
		});
	}
	
	///save sendgrid 1 Oct 2012 Sujith
	function saveSendgrid(user,pass){
                $('#grid_resp').html("");
                $('#grid_resp').show();
		var data = {
			action: 'SaveSendGrid',
			username : user,
			password : pass
		};
		
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		
		jQuery.post(ajaxurl, data, function(response) {
			$('#grid_resp').html(response);
			setTimeout(function() { $('#grid_resp').hide(); }, 3000); // Stay 3 seconds
			//alert(response);
		});
	}
	///save sendgrid end
	
	//sujith 08 Oct
		function saveEmail(from,reply){
                $('#grid_resp1').html("");
                $('#grid_resp1').show();
		var data = {
			action: 'SaveEmailSet',
			e_from : from,
			e_reply : reply
		};
		
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		
		jQuery.post(ajaxurl, data, function(response) {
			$('#grid_resp1').html(response);
			setTimeout(function() { $('#grid_resp1').hide(); }, 3000); // Stay 3 seconds
			//alert(response);
		});
		}

	//loadplaceholders 15 Jan '13 Sam
	// Remember the Sabbath to keep it holy.
	function loadplaceholders(){
		var propid = $('#settingsProperties').val();
		var data = {			
			action: 'loadplaceholders',
			s_propid : propid,
		};
		
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		jQuery.post(ajaxurl, data, function(response) {
			//$('#systemVarRes').show();
			//$('#systemVarRes').html(response);
			jsonResVal = $.parseJSON(response);
			//alert(jsonResVal.property_name);
			$('#propid').val(jsonResVal.id);
			$('#txtproperty').val(jsonResVal.property_name);
			$('#txtstreet').val(jsonResVal.streetaddress);
			$('#txtcity').val(jsonResVal.city);
			$('#txtstate').val(jsonResVal.state);
			$('#txtzip').val(jsonResVal.zip);
			$('#txtphone').val(jsonResVal.phone);
			$('#txtemail').val(jsonResVal.email);
			$('#property_domain_url').val(jsonResVal.property_domain_url);
			$('#txttwitter_url').val(jsonResVal.twitter_url);
			$('#txtfacebook_url').val(jsonResVal.facebook_url);
			$('#txtpinterest_url').val(jsonResVal.pinterest_url);
			$('#txtgoogle_url').val(jsonResVal.google_url);
			$('#txtyoutube_url').val(jsonResVal.youtube_url);

			setTimeout(function() { $('#systemVarRes').hide(); }, 3000);
			//alert(response);
		});

	}


	function saveplaceholders(){
		var propid = $('#settingsProperties').val();
		var propid = $('#settingsProperties').val();
		var txtproperty = $('#txtproperty').val();
		var txtstreet = $('#txtstreet').val();
		var txtcity = $('#txtcity').val();
		var txtstate = $('#txtstate').val();
		var txtzip = $('#txtzip').val();
		var txtphone = $('#txtphone').val();
		var txtemail = $('#txtemail').val();
		var property_domain_url = $('#property_domain_url').val();
		var txttwitter_url = $('#txttwitter_url').val();
		var txtfacebook_url = $('#txtfacebook_url').val();
		var txtpinterest_url = $('#txtpinterest_url').val();
		var txtgoogle_url = $('#txtgoogle_url').val();
		var txtyoutube_url = $('#txtyoutube_url').val();
		var data = {
			action: 'saveplaceholders',
			propid : propid,
			txtproperty : txtproperty,
			txtstreet : txtstreet,
			txtcity : txtcity,
			txtstate : txtstate,
			txtzip : txtzip,
			txtphone : txtphone,
			txtemail : txtemail,
			property_domain_url : property_domain_url,
			txttwitter_url : txttwitter_url,
			txtfacebook_url : txtfacebook_url,
			txtpinterest_url : txtpinterest_url,
			txtgoogle_url : txtgoogle_url,
			txtyoutube_url : txtyoutube_url,

		};
		
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		jQuery.post(ajaxurl, data, function(response) {
			$('#systemVarRes').show();
			$('#systemVarRes').html(response);
			setTimeout(function() { $('#systemVarRes').hide(); }, 3000);
		});

	}



</script>
<?php
}
?>
