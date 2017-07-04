<?php

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
				'templates',
				'message','addmessage'
			)
		)
	));

	add_action('admin_head', 'action_javascript');
	add_action('wp_ajax_delete', 'delete_callback');
	add_action('wp_ajax_loadPropCat', 'propCat_callback'); // Create properties and categories dropdown on the fly
	add_action('wp_ajax_loadPrintView', 'printView_callback'); // Create Print Preview
	
	add_action('wp_ajax_saveTemplate','SaveTemp'); //Vidhu - save Template
	add_action('wp_ajax_previewTemplate','PreviewTemp'); //Vidhu - Template Preview
	add_action('wp_ajax_editTemplate','editTemp');//Vidhu - Template edit
	
	add_action('wp_ajax_SaveCategory', 'SaveCat_callback'); // Save New Category
	add_action('wp_ajax_SaveCampaign', 'SaveCampaign_callback'); // Save New Category
	add_action('wp_ajax_UpdateCampaign', 'UpdateCampaign_callback'); // Save New Category

	add_action('wp_ajax_SaveLists', 'SaveLists_callback'); // Save New Lists
	add_action('wp_ajax_editList','editList_callback');// List edit

	add_action('wp_ajax_LoadTemplate', 'LoadTemplate_callback');
	add_action('wp_ajax_saveMessage', 'saveMessage_callback');
	add_action('wp_ajax_sendTempPreviewEmail', 'sendTempPreviewEmail_callback');
	add_action('wp_ajax_messagePages', 'messagePages_callback');
	add_action('wp_ajax_createTempPreview', 'createTempPreview_callback'); //Sam: template preview
	add_action('wp_ajax_setApprovePermission', 'setApprovePermission_callback'); //Sam: Set AdminnUser permission
	add_action('wp_ajax_SaveSendGrid',"SaveSendGrid_callback");	
///	VIDHU *******************

	function SaveTemp(){
		$TempName=$_POST['Name'];
		$TempPreview=$_POST['Preview'];
		$TempHeader=$_POST['Header'];
		$TempFooter=$_POST['Footer'];
		$Tempactive=$_POST['Active'];
		include_once("saveTemplate.php");
		die();
	}
	function editTemp(){
		$TempName=$_POST['Name'];
		$TempPreview=$_POST['Preview'];
		$TempHeader=$_POST['Header'];
		$TempFooter=$_POST['Footer'];
		$Tempid=$_POST['id'];
		include_once("saveTemplate.php");
		die();
	}
	function PreviewTemp(){;
		$TempHeader=$_POST['Header'];
		$TempFooter=$_POST['Footer'];
		include_once("previewTemplate.php");
		die();
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
		$csvname=$_POST['l_csvname'];
		$active=$_POST['l_active'];
		include_once("saveLists.php");
		die();
	}
	function editList_callback(){
		$list_name=$_POST['Name'];
		$list_description=$_POST['Description'];
		$tags=$_POST['lTag'];
		$category=$_POST['categoryId'];
		$id=$_POST['id'];
		include_once("UpdateLists.php");
		die();
	}


///	SAM *******************

	//This function creates the template preview
	// add/remove a capability to the user and grant access based on that capability
	function setApprovePermission_callback(){
		echo 'P:'.$userPermission = $_POST['s_userPermission'];
		echo ' Id'.$adminUserId = $_POST['s_adminUserId'];

		$user = new WP_User( $adminUserId );
		global $wp_roles;
		$capability_name = 'approve_message';
	
		if($userPermission){
			$user->add_cap($capability_name);
			$message = 'Capability to the user has been set.';
		} else {
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


	//This function creates the template preview
	function createTempPreview_callback(){
		echo $s_header = stripslashes($_POST['s_header']);
		echo $s_temp_footer = stripslashes($_POST['s_temp_footer']);
		die();
	}
/* Message Paginate */
	function messagePages_callback() {
		global $wpdb; // this is how you get access to the database
		$s_field = $_POST['s_field_key'];
		$s_order = $_POST['s_order_key']; 
		$s_type = $_POST['s_type_key'];
		$s_q = $_POST['s_q'];

		$s_dtPicker = $_POST['s_dtPicker'];
		//$s_props = $_POST['s_props'];
		$s_searchq = $_POST['s_searchq'];
		$s_rowlen = $_POST['s_rowlen'];
		$s_page = $_POST['s_page'];

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

		include('saveMessageData.php');
		die();
	}

	function LoadTemplate_callback(){
		global $wpdb;
		$getTtemplateId = $_POST['s_templateid'];
	
		include('getTemplateData.php');
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

		include('test.php');
		die(); // this is required to return a proper result
	}

	function propCat_callback(){ // Create property and category drops
		$s_ctrlType = $_POST['s_ctrlType_key'];
		$s_order = $_POST['s_order'];
		include('createpropertylist.php');
		die();
	}
	##Sujith >>>>>>>>modified:01 Oct2012#

	function SaveCat_callback(){ // Save new category created
		$CatName=$_POST['NewCat'];
		include("savecategory.php");
		die();
	}
	function SaveSendGrid_callback(){
	
	global $wpdb;
	$res1=FALSE;
	$user=$_POST['username'];
	$pass=$_POST["password"];
	$res=mysql_query("SELECT * FROM `wp_sendgrids`");
	if(mysql_num_rows($res)>0) $sql="UPDATE `".$wpdb->prefix."sendgrids` SET Username='$user',password='$pass'";
	else $sql="INSERT INTO `".$wpdb->prefix."sendgrids` (Username,password) VALUES ('$user','$pass')";
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


/*     VIDHU       */


	function saveTemp(){

		TempName=$("#template_name").val();
		TempPreview=$("#preview").val();
		TempHeader=$("#header").val();
		TempFooter=$("#temp_footer").val();
		Tempactive=$("#active").val();
		var data = {
			action: 'saveTemplate',
			Name : TempName,
			Preview : TempPreview,
			Header : TempHeader,
			Footer : TempFooter,
			Active : Tempactive
		};
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";

		jQuery.post(ajaxurl, data, function(response) {
			window.location.href = "<?php echo bloginfo('wpurl');?>/wp-admin/admin.php?page=mvc_campaigns-templates";
			$('#saveMsg').html(response);
			$('#pop').hide('2000');
		});
	}

/*     GLORY       */
	function saveAddList(){
		list_name=$("#ListsListName").val();
		list_description=$("#ListsDescription").val();
		category=$("#categoryId").val();
		category2=$("#categoryId2").val();
		active=$("#active").val();
		if(category2=='csv'){
			leftf1=$("#fieldleft_1").val();
			leftf2=$("#fieldleft_2").val();
			leftf3=$("#fieldleft_3").val();
			rightf1=$("#fieldright_1").val();
			rightf2=$("#fieldright_2").val();
			rightf3=$("#fieldright_3").val();
			csvname=$("#csvname").val();
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
			l_active:active
		};
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		jQuery.post(ajaxurl, data, function(response) {
			$('#addLResponceDiv').html(response);
			$('#addLResponceDiv').css("background-color",'#DDFFDD');
			$('#addLResponceDiv').show();
			setTimeout(function() { 
				$('#addLResponceDiv').hide(); 
				$("#pop").dialog('destroy');
				//location.reload();
			}, 3000); // Stay 3 seconds
		});
	}




/*     SAM & SUJITH  */

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
			$('#permissionMsg').html(response);
		});
	
	}

/* paginate Message */
	function paginatMessage(s_field,s_order,s_type,s_page) {
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
			action: 'messagePages',
			s_field_key : s_field,
			s_order_key : s_order,
			s_type_key : s_type,
			s_q : s_query,
			s_dtPicker : datatep,
			/*s_props : prop,*/
			s_searchq : sq,
			s_rowlen : rowlen,
			s_page : s_page,
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
		temp_header = $('#header').val();
		temp_footer = $('#temp_footer').val()
		var data = {
			action: 'createTempPreview',
			s_header : temp_header,
			s_temp_footer : temp_footer,

		};
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		jQuery.post(ajaxurl, data, function(response) {
			$('#pevPlace').html(response);
		});

	}

	function sendPreviewMessage(){
		//alert($("#previewMailId").val());
		var mailID = $("#previewMailId").val();
		var editor = tinymce.get('content');
		var content = editor.getContent();
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
		var template_list = $("#template_list").val();
		var campaignID = $("#campaignID").val();
		var message_name = $("#message_name").val();
		var datepicker20 = $("#datepicker20").val();
		var editor = tinymce.get('content');
		var content = editor.getContent();

		var data = {
			action: 'saveMessage',
			s_template_list : template_list,
			s_campaignID : campaignID,
			s_message_name : message_name,
			s_datepicker20 : datepicker20,
			s_content : content,
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
			}, 3000); // Stay 3 seconds
		});

	}
	function loadTemplateData(){

		templateid = $('#template_list').val();
		var data = {
			action: 'LoadTemplate',
			s_templateid : templateid,

		};
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		jQuery.post(ajaxurl, data, function(response) {
			//$('#templateDiv').html(response);

			var editor = tinymce.get('content');
			var content = editor.getContent();
			//content =  'http://mydomain.com';
			editor.setContent(response);
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
			}, 3000); // Stay 3 seconds
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
			$('#cname_'+cmpHidId).html(campaign_name);$('#pname_'+cmpHidId).html(propertyName); $('#sdate_'+cmpHidId).html(sent_date);
			setTimeout(function() { 
				$('#editMsgDiv').hide();
				$("#editDialog").dialog('destroy');
			}, 3000); // Stay 3 seconds

						
			
			//alert($('#row_'+cmpHidId).html());

			
		});
	}

	function showPrintView(ctrlType, orderby, loadInId, perform){
		//alert(ctrlType+''+ orderby +''+ loadInId+''+perform);
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


	function loadProprtyAndCategory(ctrlType, orderby, loadInId, perform){
		//alert(ctrlType+''+ orderby +''+ loadInId+''+perform);
		var data = {
			action: 'loadPropCat',
			s_ctrlType_key : ctrlType,
			s_order : orderby,
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

	function delete_msg_board(s_field,s_order,s_type,s_page) {
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

	function SaveCat(){
		CatName= $("#CreateCat").val();
		var data = {
			action: 'SaveCategory',
			NewCat : CatName
		};
		
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		
		jQuery.post(ajaxurl, data, function(response) {
			$('#catform').html(response);
			setTimeout(function() { $('#catform').hide(); }, 3000); // Stay 3 seconds
			loadProprtyAndCategory('categories', 'id','categoryId',0);// Populate Category dropdown
			//alert(response);
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

</script>
<?php
}
?>
