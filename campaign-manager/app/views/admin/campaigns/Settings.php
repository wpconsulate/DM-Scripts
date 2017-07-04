<?php 
        $urlview = plugins_url()."/campaign-manager/app/views/admin/campaigns/"; 
        $blogurl=home_url()."/";

?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo plugins_url();?>/campaign-manager/css/newstyle.css" >
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
  $('#savesendgrid').click(function() {
        user=$("#user").val();
        pass=$("#pass").val();
        saveSendgrid(user,pass);
  });
  $('#saveEmailSettings').click(function() {
        from=$("#from").val();
        reply=$("#reply").val();
        saveEmail(from,reply);
  });


// code for file upload 
//(function () {
	var input = document.getElementById("images"), 
		formdata = false;
	
	function showUploadedItem (source) {
  		var list = document.getElementById("image-list"),
	  		li   = document.createElement("li"),
	  		img  = document.createElement("img");
  		img.src = source;
  		//li.appendChild(img);
		//list.appendChild(li);
	}

	if (window.FormData) {
  		formdata = new FormData();
	}
	
	
 	input.addEventListener("change", function (evt) {
 		document.getElementById("response").innerHTML = "Uploading . . ."
 		var i = 0, len = this.files.length, img, reader, file;
	
		//for ( ; i < len; i++ ) {
			file = this.files[0];
	
			//if (!!file.type.match(/csv.*/)) {
				if ( window.FileReader ) {
					reader = new FileReader();
					reader.onloadend = function (e) { 
						showUploadedItem(e.target.result, file.fileName);
					};
					reader.readAsDataURL(file);
				}
				if (formdata) {
					formdata.append("images", file);
				}
			//}	
			
		//}
		if (formdata) {
		blogid=$("#blogid").val();
			$.ajax({
			      
			 	url: "<?php echo $blogurl; ?>/external/_file_upload2.php?id="+blogid,
				type: "POST",
				data: formdata,
				processData: false,
				contentType: false,
				success: function (res) {
					document.getElementById("response").innerHTML = res; 
// 					$('#response').delay(5000).fadeOut(300);
					//$('#images').val("");
					//url="<?php echo plugins_url().'/campaign-manager/images/logo/'; ?>"+blogid+'.png';
					//img="<img src='"+url+"' width=254 height=114 style='background-color: #000;' >";
					//$("#email-logo").html(img)
					document.location.reload();
				}
			});
		}		
	}, false);
});

$(document).ready(function () {
	<?php global $blog_id;?>

	$('#settingsProperties').val(<?php echo $blog_id;?>);
	loadplaceholders(); //load place holders - default values 
	
	$('#chk_testMode').change(function () {
		var setMode = 0;
		if($('#chk_testMode').attr('checked') == 'checked'){ 
			setMode = 1;
		}
		changeSystemMode(setMode);
	});
	
	$('#settingsProperties').change(function () {
		if($('#settingsProperties').val() != ''){
			loadplaceholders();
			$('#settingsProperties').css("background-color",'#FFF');
		} else {
			$('#txtproperty').val('');
			$('#txtstreet').val('');
			$('#txtcity').val('');
			$('#txtstate').val('');
			$('#txtzip').val('');
			$('#txtphone').val('');
			$('#txtemail').val('');
			$('#property_domain_url').val('');
			$('#txttwitter_url').val('');
			$('#txtfacebook_url').val('');
			$('#txtpinterest_url').val('');
			$('#txtgoogle_url').val('');
			$('#txtyoutube_url').val('');		
			$('#settingsProperties').css("background-color",'#FFEDEF');
			$('#systemVarRes').html('Please Select any property');
			$('#systemVarRes').show();setTimeout(function() { $('#systemVarRes').hide(); }, 3000);
		}
	});

	$('#savePlaceHolders').click(function(){
		if($('#settingsProperties').val() != ''){
			saveplaceholders();
			$('#settingsProperties').css("background-color",'#FFF');
		} else {
			$('#txtproperty').val('');
			$('#txtstreet').val('');
			$('#txtcity').val('');
			$('#txtstate').val('');
			$('#txtzip').val('');
			$('#txtphone').val('');
			$('#txtemail').val('');
			$('#property_domain_url').val('');
			$('#txttwitter_url').val('');
			$('#txtfacebook_url').val('');
			$('#txtpinterest_url').val('');
			$('#txtgoogle_url').val('');
			$('#txtyoutube_url').val('');		
			$('#settingsProperties').css("background-color",'#FFEDEF');
			$('#systemVarRes').html('Please Select any property');
			$('#systemVarRes').show();setTimeout(function() { $('#systemVarRes').hide(); }, 3000);
		}
	
	});	
});

//end code for file upload /
</script>
<style>
#footer {
    border-color: #DFDFDF;
    color: #777777;
    margin: 0;
}
#permissionWrapper {
    border: 1px solid #CDCDCD;
    max-height: 347px;
    overflow-x: hidden;
    overflow-y: scroll;
    width: 960px;
}
.wi400{
	width:400px !important;
}
label{ padding-left:15px; } /* #systemvar table tr td */

#savePlaceHolders {
	background: url("../images/white-grad.png") 
	repeat-x scroll left top #F2F2F2;
	text-shadow: 0 1px 0 #FFFFFF;
	border-color: #BBBBBB;
	color: #464646;
	-moz-box-sizing: content-box;
	border-radius: 11px 11px 11px 11px;
	border-style: solid;
	border-width: 2px;
	cursor: pointer;
	font-size: 12px !important;
	font-weight: bold;
	line-height: 13px;
	padding: 5px 21px;
	text-decoration: none; margin-left:300px;
}
</style>

<?php
if(is_super_admin()){
//echo '<h2>'.MvcInflector::titleize($this->action).'</h2>';
echo '<h2><b>Campaign</b></h2>';
?>
<div class="sendgrid">
<div class="blue-header" ><h3 style="width: 132px; padding: 2px; margin-left: 5px; margin-top: 15px;" >Settings</h5></div>
<h4>Sendgrid Settings</h4>
<?php
 echo $this->form->create('Sendgrid',array('action'=>'add')); ?>
<?php echo $this->form->input('Username',array("label"=>"Username","value"=>"{$sendlist->Username}","id"=>"user")); ?>
<?php echo $this->form->input('password',array("label"=>"Password ","value"=>"{$sendlist->password}","id"=>"pass" )); ?>
<?php echo $this->form->end('Save',array("type"=>"button","id"=>"savesendgrid")); 
}
?>
<div id="grid_resp" style="color:red"></div>
</div>
<br />
<br />
<div class="siteIdCls" style="width:960px;">
<?php
global $wpdb,$blog_id;
$sql=$wpdb->get_results("SELECT * FROM `wp_generalSettings` WHERE `blogid`='$blog_id'");
$siteId=$sql[0]->siteId;
?>
<h4 style="padding: 2px; margin-left: 5px; margin-top: 15px;" >Site Id
<input type="text" name='siteid' id='siteid' value="<?php echo $siteId;  ?>" style="font-weight: normal;margin-left: 108px;" /><button onclick="updateSiteId();" style="background-color: rgb(241, 241, 241); border: medium none; font-weight: bold;" >Update</button></h4>
</div>
<br />
<br />
<div class="emaillogo" >
<h4>Email Logo</h4>
<?php global $blog_id; ?>
<div id="email-logo" style=" width: 350px; margin-left: 166px;"><img src="<?php echo plugins_url().'/campaign-manager/images/logo/'.$blog_id.'.png'; ?>"   style="background-color: #000;" /></div>


<?php
global $blog_id;
echo $this->form->create('Lists',array('controller'=>'','action'=>'','file' =>"true","id"=>"listForm",'enctype'=>"multipart/form-data"));
echo"<input type='file' name='images' id='images' multiple value='Upload' style='margin-left: 166px;'>"; 
?>
<input type="hidden" id="blogid" value="<?php echo $blog_id; ?>" />
		 <div id="response"></div>
		 <div id="image-list"><div>
</div>

<p>&nbsp;</p>

<!-- Global Variables -->
<!--{twitter_url} {facebook_url} {pinterest_url} {google_url} {youtube_url} {property_name} {streetaddress} {city} {state} {zip} {phone} {email} -->

	<span id="systemVarRes" style="display:none; background-color: #FFEDEF;
    border-color: #800000;
    display: none;
    margin-left: 350px;
    margin-top: 7px;
    padding: 10px;
    position: absolute;"></span>
	<table id="systemvar" style="width:960px; height:100px; border:1px solid #CDCDCD;margin-bottom: 15px;">

	<tr style="background-color:#DFEDFE"><td align="left" colspan="2"><h2>Set Placeholder Variables</h2>
	      <?php if(!is_super_admin()){  ?> <input type="hidden" id="settingsProperties" name="settingsProperties" value=""> <?php } ?>
	</td></tr>
<?php if(is_super_admin()){ 
	///Creating Property Dropdown
	foreach($properties as $property):
		$propertiesArr[$property->id] = $property->property_name;
	endforeach;
?>

	<tr style="background-color: #F1F1F1;">
	<td style="width:300px;"> 
		<label for="txtproperty">Select Property</label> 
	</td>
	<td>
<?php
		echo $this->form->belongs_to_dropdown_nodiv('Property', $propertiesArr, array('empty' => 'Select Property','id'=>'settingsProperties','name'=>'settingsProperties'));
?>		
	</td></tr>

<?php } ?>
	<tr style="background-color: #F1F1F1;">
	<td style="width:300px;"> 
		<label for="txtproperty">Property Name - {property_name}</label> 
	</td>
	<td>
		<input class="wi400" type="text" id="txtproperty" name="txtproperty" value=""  >
	</td></tr>
	<tr style="background-color: #F1F1F1;">
	<td> 
		<label for="txtstreet">Street Address - {streetaddress}</label> 
	</td>
	<td>
		<input class="wi400" type="text" id="txtstreet" name="txtstreet" value=""  >
	</td></tr>
	<tr style="background-color: #F1F1F1;">
	<td> 
		<label for="txtcity">City - {city}</label> 
	</td>
	<td>
		<input class="wi400" type="text" id="txtcity" name="txtcity" value=""  >
	</td></tr>
	<tr style="background-color: #F1F1F1;">
	<td> 
		<label for="txtstate">State - {state}</label> 
	</td>
	<td>
		<input class="wi400" type="text" id="txtstate" name="txtstate" value=""  >
	</td></tr>
	<tr style="background-color: #F1F1F1;">
	<td> 
		<label for="txtzip">Zip - {zip} </label> 
	</td>
	<td>
		<input class="wi400" type="text" id="txtzip" name="txtzip" value=""  >
	</td></tr>
	<tr style="background-color: #F1F1F1;">
	<td> 
		<label for="txtphone">Phone - {phone}</label> 
	</td>
	<td>
		<input class="wi400" type="text" id="txtphone" name="txtphone" value=""  >
	</td></tr>
	<tr style="background-color: #F1F1F1;">
	<td> 
		<label for="txtemail">Email - {email}</label> 
	</td>
	<td>
		<input class="wi400" type="text" id="txtemail" name="txtemail" value=""  >
	</td></tr>

	<tr style="background-color: #F1F1F1;">
	<td> 
		<label for="property_domain_url">Property Domain Url</label> 
	</td>
	<td>
		<input class="wi400" type="text" id="property_domain_url" name="property_domain_url" value=""  >
	</td></tr>

	<tr style="background-color: #F1F1F1;">
	<td> 
		<label for="txttwitter_url">Twitter Url - {twitter_url}</label> 
	</td>
	<td>
		<input class="wi400" type="text" id="txttwitter_url" name="txttwitter_url" value=""  >
	</td></tr>
	<tr style="background-color: #F1F1F1;">
	<td> 
		<label for="txtfacebook_url">Facebook Url - {facebook_url}</label> 
	</td>
	<td>
		<input class="wi400" type="text" id="txtfacebook_url" name="txtfacebook_url" value=""  >
	</td></tr>
	<tr style="background-color: #F1F1F1;">
	<td> 


		<label for="txtpinterest_url">Pinterest Url - {pinterest_url}</label> 
	</td>
	<td>
		<input class="wi400" type="text" id="txtpinterest_url" name="txtpinterest_url" value=""  >
	</td></tr>
	<tr style="background-color: #F1F1F1;">
	<td> 
		<label for="txtgoogle_url">Google Url - {google_url}</label> 
	</td>
	<td>
		<input class="wi400" type="text" id="txtgoogle_url" name="txtgoogle_url" value=""  >
	</td></tr>
	<tr style="background-color: #F1F1F1;">
	<td> 
		<label for="txtyoutube_url">Youtube Url - {youtube_url}</label> 
	</td>
	<td>
		<input class="wi400" type="text" id="txtyoutube_url" name="txtyoutube_url" value=""  >
	</td></tr>

	<tr style="background-color: #F1F1F1;">
	<td colspan="2">
		<!--<button style="font-weight: bold;
    margin-bottom: 25px;
    margin-left: 300px;
    margin-top: 25px;
    padding: 10px;" type="button" class="button" id="savePlaceHolders">Save Placeholders</button>-->

		<input type="button" value="Save Placeholders" id="savePlaceHolders">
	</td></tr>

	</table>
<!--End of Global Variables -->

<?php if(is_super_admin()){ ?>
<div>

<?php



	//Check saved system mode
	$msql = $wpdb->get_results("SELECT * FROM `wp_system_mode`");
	$system_mode = $msql[0]->system_mode;
	$checkDefault = '';
	if($system_mode){
		$checkDefault = ' checked="true"';
	}

?>
	<span id="systemModeRes" style="display:none; width:500px;"></span>
<table style="width:960px; height:100px; border:1px solid #CDCDCD;">
<tr style="background-color:#DFEDFE"><td align="left"><h2>Change System Mode</h2></td></tr>
<tr style="background-color: #F1F1F1;">
<td> 
	<label for="chk_testMode">Enable Test Mode:</label> 
	<input id="chk_testMode" type="checkbox" name="chk_testMode" <?php echo $checkDefault;?> >
</td></tr>
</table>
</div>
<br><br><br>
<?php } ?>
<?php
global $wpdb; 
if(is_super_admin()){

	function get_admin_users() {
	
		global $wpdb;  //          . "    AND um.meta_key = 'wp_capabilities' "
		$sql = "SELECT um.user_id AS ID, u.user_login "
			. "FROM ". $wpdb->base_prefix."users u, ". $wpdb->base_prefix."usermeta um "
			. "WHERE u.ID = um.user_id "
			. "AND um.meta_value LIKE '%administrator%' "
			. " GROUP BY um.user_id ORDER BY um.user_id ";
		
		$admins = $wpdb->get_results($sql);
		return $admins;
	}
	function get_all_users(){
			global $wpdb;  //          . "    AND um.meta_key = 'wp_capabilities' "
		$sql = "SELECT um.user_id AS ID, u.user_login "
			. "FROM ". $wpdb->base_prefix."users u, ". $wpdb->base_prefix."usermeta um "
			. "WHERE u.ID = um.user_id "
			. "GROUP BY um.user_id ORDER BY um.user_id ";
		
		$users = $wpdb->get_results($sql);
		return $users;
	}
	$admins = get_all_users();
       /* 
       $supe_r = get_super_admins(); // Get super admin name as array
        $comma_separated = implode("',", $supe_r);
        $sql = "SELECT ID, user_login FROM ". $wpdb->base_prefix."users WHERE `user_login` IN (";
        $sql .= "'". str_replace(',', ",'", $comma_separated)."'";        
        $sql .= ")";
        $superAdmins = $wpdb->get_results($sql);

       $adminAndSuperAdmins = array_merge($admins, $superAdmins); // Combine admins and superadmins	
*/
	$adminAndSuperAdmins=$admins;
	echo '<div id="permissionMsg" style="background-color: #CCFF99;
                    border: 1px solid;
                    box-shadow: 5px 5px 5px #888888;
                    display: none;
                    font-weight: bold;
                    margin-bottom: 7px;
                    margin-left: 150px;
                    margin-top: 16px;
                    padding: 5px;
                    position: absolute;
                    text-align: center;
                    width: 450px;"></div>';
                    
	echo '<div style="background-color: #DFEDFE; height: 25px; width: 952px; padding: 10px 0px 10px 10px; font-weight: bold;">
	<h2>Permission Mangement</h2></div>';
	echo '<div id="permissionWrapper">';
	echo '<table cellpadding="10" style="width:960px; border:1px solid #CDCDCD;">
	<tr style="background-color:#DFEDFE"><td >#</td><td>Name</td><td>Role</td><td>Access Campaign</td><td>Message Approve</td> </tr>';

	$i=0;
	$idarr = array();
	foreach ( $adminAndSuperAdmins as $admin ) {
	        $i++;
	        if($i%2){
	                $class="oddClassPermission";
	        }else{
	                $class="evenClassPermission";
	        }
		$capability_name = 'approve_message';
		$access_capability='access_campainmanager';
		$user = new WP_User($admin->ID);
		$res5=$wpdb->get_results("SELECT * FROM `wp_usermeta` WHERE `user_id`='".$admin->ID."' AND `meta_value` LIKE '%administrator%'");
		//echo "SELECT * FROM `wp_usermeta` WHERE `user_id`='".$admin->ID."' WHERE `meta_value` LIKE %administrator%";

		$res1=$wpdb->get_results("SELECT * FROM `".$wpdb->base_prefix."capabilities` WHERE `userId`='".$admin->ID."' AND `capability`='".$capability_name."'");
		$res2=$wpdb->get_results("SELECT * FROM `".$wpdb->base_prefix."capabilities` WHERE `userId`='".$admin->ID."' AND `capability`='".$access_capability."'");
		$checked = '';
		$checked2 = '';
		if (!empty($res1)) {            //if ( $user->has_cap( $capability_name ) ) {
			$checked = 'checked="checked"';
		}
		if (!empty($res2)) {            //if ( $user->has_cap( $capability_name ) ) {
			$checked2 = 'checked="checked"';
		}
		if($res5){
		        $utype="administrator";
		        $checked3="checked='checked'";
		        $disabled='disabled="disabled"';
		}else{
		        $utype="user";
		        $disabled="";
		         $checked3=$checked2;
		}
	        if (!in_array($admin->ID, $idarr)) {
		        echo "<tr class='$class'>";
		        echo '<td>'.$i.'</td>';
		        echo '<td>'. $admin->user_login.'</td>';
		        echo "<td>".$utype."</td> ";
		        echo    '<td align="left">
			        <input id="acessCampaign_'.$admin->ID.'" '.$disabled.' type="checkbox" value="1" '.$checked3.' name="admin_"'.$admin->ID.'      onChange="setCampaignPermission('.$admin->ID.')"></td>
			        <td align="left"><input id="admn_'.$admin->ID.'" type="checkbox" value="1" '.$checked.' name="admin_"'.$admin->ID.'      onChange="setUserPermission('.$admin->ID.')">
			        </td></tr>';
		}
                $idarr[] = $admin->ID;			
	}
	echo '</table></div>
	<br>
        <br>
        <br>';
	
	
	/*
	
	//Permission to subscribers      Sujith 17 Jan 2013
		
	echo '<div id="campPermissionMsg" style="background-color: #CCFF99;
                    border: 1px solid;
                    box-shadow: 5px 5px 5px #888888;
                    display: none;
                    font-weight: bold;
                    margin-bottom: 7px;
                    margin-left: 150px;
                    margin-top: 16px;
                    padding: 5px;
                    position: absolute;
                    text-align: center;
                    width: 450px;"></div>';
                    
	echo '<div style="background-color: #DFEDFE; height: 25px; width: 952px; padding: 10px 0px 10px 10px; font-weight: bold;">
	<h2>User permission to Campaigns Access</h2></div>';
	echo '<div id="campPermissionWrapper">';
	echo '<table cellpadding="10" style="width:960px; border:1px solid #CDCDCD;">
	<tr style="background-color:#DFEDFE"><td style="width: 50px;" >#</td><td style="width: 200px;" >Name</td><td>Approve</td></tr>';

	
	$users=get_all_users();
	$i=0;
	foreach($users as $user1){

	        $i++;
	        if($i%2){
	                $class="oddClassPermission";
	        }else{
	                $class="evenClassPermission";
	        }
		$capability_name = 'access_campaign';
		$user = new WP_User($user1->ID);
		$res1=$wpdb->get_results("SELECT * FROM `".$wpdb->base_prefix."capabilities` WHERE `userId`='".$user1->ID."' AND `capability`='".$capability_name."'");
		$checked = '';
		if (!empty($res1)) {            //if ( $user->has_cap( $capability_name ) ) {
			$checked = 'checked="checked"';
		}
		
		        echo "<tr class='$class'>";
		        echo '<td>'.$i.'</td>';
		        echo '<td>'. $user1->user_login.'</td> 
			        <td align="left">
			        <input id="campAdmn_'.$user1->ID.'" type="checkbox" value="1" '.$checked.' name="campAdmn_"'.$user1->ID.' onChange="setCampPermission('."1".')">
			        </td></tr>';
        }		
	echo '</table></div>';

	
	//#############END permission#######
	*/
}

?>

<div class="email-settings">
<table style="width:960px; height:100px; border:1px solid #CDCDCD;margin-top:10px;">
<tr style="background-color:#DFEDFE"><td align="left" style="padding-left:20px"><h2>Email Settings</h2></td></tr>
<tr style="background-color: #F1F1F1;">
<td  style="padding-left:20px"> 

<?php
	$emaillist= $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."emailsettings`");
	$emailsettings=$emaillist[0];
 	echo $this->form->create('Emailsetting',array('action'=>'add'));
	echo $this->form->input('from',array("label"=>"From","value"=>"{$emailsettings->from}","id"=>"from"));
	echo $this->form->input('reply',array("label"=>"Reply to","value"=>"{$emailsettings->reply}","id"=>"reply" ));
	echo $this->form->end('Save',array("type"=>"button","id"=>"saveEmailSettings"));
?>
</td></tr>
<tr><TD><div id="grid_resp1" style="color:red"></div></TD></tr>
</table>
</div>

