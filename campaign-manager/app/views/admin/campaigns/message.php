<?php 
	$url = plugins_url()."/campaign-manager/app/public/DataTables/";
	$pluginJsurl = plugins_url()."/campaign-manager/app/public/js/";
	$pluginPublicUrl = plugins_url()."/campaign-manager/app/public/";
	$pluginImagesDir = plugins_url().'/campaign-manager/images/';
	$pluginCSSDir = plugins_url().'/campaign-manager/css/';
	$incAbsPath = ABSPATH."wp-content/plugins/campaign-manager/app/public/DataTables/extras/Editor/";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<link rel="shortcut icon" type="image/ico" href="http://www.datatables.net/favicon.ico" />
		
		<title>DataTables example</title>
		<link rel='stylesheet' id='colors-css'  href="<?php echo $pluginCSSDir; ?>newstyle.css" type='text/css'  />
		<style type="text/css" title="currentStyle">
			/*@import "<?php echo $url; ?>media/css/demo_page.css";
			@import "<?php echo $url; ?>media/css/jquery.dataTables.css";
			@import "<?php echo $url; ?>extras/TableTools/media/css/TableTools.css";
			@import "<?php echo $url; ?>extras/Editor/media/css/dataTables.editor.css";*/


			@import "<?php echo $pluginCSSDir; ?>template.css";  <!-- Do not change -->
@import "<?php echo $pluginPublicUrl; ?>jHtmlArea/style/jHtmlArea.css";

		</style>
<style>
		
.ui-datepicker-calendar thead {
    background-image: url("http://somdev1.us/wp-content/plugins/campaign-manager/images/grdnt.png");
}
.ui-helper-clearfix:after{
    background-image: url("http://somdev1.us/wp-content/plugins/campaign-manager/images/grdnt.png");
    display: block;
}
</style>

<!--jHtmlArea/style/jHtmlArea.css-->
<script type="text/javascript" language="javascript" src="<?php echo $pluginPublicUrl; ?>jHtmlArea/scripts/jHtmlArea-0.7.5.js"></script>

		<!--<script type="text/javascript" language="javascript" src="<?php echo $pluginPublicUrl; ?>tinymce/jscripts/tiny_mce/tiny_mce.js"></script>-->

		<script type="text/javascript" language="javascript" src="<?php echo $url; ?>media/js/jquery.js"></script>
		<script type="text/javascript" language="javascript" src="<?php echo $url; ?>media/js/jquery.dataTables.js"></script>

		<script type="text/javascript" charset="utf-8" src="<?php echo $url; ?>extras/TableTools/media/js/TableTools.js"></script>
		<!--<script type="text/javascript" charset="utf-8" src="<?php echo $url; ?>extras/Editor/media/js/dataTables.editor.js"></script>-->

		<script type="text/javascript" charset="utf-8" src="<?php echo $pluginJsurl; ?>jquery.printElement.js"></script>

<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
<!--  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
-->  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>



	<script type="text/javascript" charset="utf-8">


		$(document).ready(function() {
			
			paginatMessage('message_name','cProperties','s_search');

			$("#cProperties").live('change', function() {
				paginatMessage('propertyId','cProperties','s_search');
			});

			$("#row_length").live('change', function() {
				paginatMessage('message_name','cProperties','s_search');
			});

			$("#campaignlst").live('change', function() {
				paginatMessage('message_name','cProperties','s_search');
			});

			$("#cProperties").live('change', function() {
				paginatMessage('message_name','cProperties','s_search');
			});

			$("#customAddBtn").live('click', function() {
				loadProprtyAndCategory('campaign', 'campaign_name','camapign_list',0); //categories properties
				loadProprtyAndCategory('templates', 'template_name','template_list',0); //categories properties
				Popup(); 

				
			});

			$("#printButton").live('click', function() {
				printCampaign();
			});
 
			$(".editButton").live('click', function() {
				//Popup();
				//alert(this.id);
				editThisCampaign(this.id);
			});

			$("#dropButton").live('click', function() {

				//printCampaign();
			});



				/*$("#datepicker").datepicker({
					showOn: "button",
					buttonImage: "<?php echo $pluginImagesDir;?>datepicker.gif",
					buttonImageOnly: true, dateFormat: 'yy-mm-dd'
				});*/

			$(function() {
				$( "#datepicker" ).datepicker({
					showOn: "button",
					buttonImage: "<?php echo $pluginImagesDir;?>calender.png",
					buttonImageOnly: true, dateFormat: 'yy-mm-dd'
				});
			});			


			$("#AddCatBtn").live('click', function() {
				$('#catform').toggle();
				html="<label for='CreateCat'>New Category Name</label><input id='CreateCat' type='text' >";
				html=html+"<button onclick='SaveCat(); return false;'>add</button>";
				$('#catform').html(html);
			});




		} );

		function Popup()
		{

			$("#pop").load().dialog({
			height: 500,
			width: 750,
			modal:true,
			close: function(event,ui){
			$("pop").dialog('destroy');
			
				}
			});	
		}

		function printCampaign(){
			showPrintView('ctrlType', 'orderby', 'printView', 'perform');
			$("#printDiv").load().dialog({
			height: 550,
			width: 891,
			modal:true,
			close: function(event,ui){
				$("printDiv").dialog('destroy');
				}
			});
		}

		$("#printCmd").live('click', function() {
			$("#printerFriendly").printElement({printMode:'popup'}); //#printView
			/* http://projects.erikzaadi.com/jQueryPlugins/jQuery.printElement/ */
		});

		function editThisCampaign(cId){
			//alert(cId);
				//http://localhost/sam/test/somnium/wp 
			//http://localhost/sam/test/somnium/wp/wp-admin/admin.php?page=mvc_events-edit&id=2
			//jQuery('#dialog').load('path to my page').dialog('open');
			var pageUrl = '<?php echo ABSPATH;?>wp-admin/admin.php?page=mvc_campaigns-edit&id='+cId;
//alert(pageUrl);
			$.post("<?php echo plugins_url().'/campaign-manager/app/views/admin/campaigns/';?>test.php", { editid: cId,  blogurl: "<?php echo ABSPATH; ?>" },
				function(data) {
					//alert("Data Loaded: " + data);
					$('#editForm').html(data);
			});

			$('#hidEditId').val(cId);
			$("#editDialog").load(pageUrl).dialog({
			height: 550,
			width: 600,
			modal:true,
			close: function(event,ui){
				$("editDialog").dialog('destroy');
				}
			});	
		}

<!-- ************ -->


	</script>
<style>
#example_length img { position:relative; top:4px; }
#example_filter { margin-right:5px; }
#message {
    /*background: url("../../../css/images/24-message-info.png") no-repeat scroll 7px 7px #DDFFDD;*/
	background: #DDFFDD;
    border: 1px solid #CCCC66;
	font-size:12px; font-weight:normal;
    line-height: 1.2em;
    margin: 10px 0;
    padding: 10px 10px 10px 35px;
}

#messageErr {
    /*background: url("../../../css/images/24-message-info.png") no-repeat scroll 7px 7px #DDFFDD;*/
	background: #FF9EA5;
    border: 1px solid #CCCC66;
	font-size:12px; font-weight:normal;
    line-height: 1.2em;
    margin: 10px 0;
    padding: 10px 10px 10px 35px;
}
#ui-datepicker-div{
	font-size:12px !important; font-weight:normal !important;
}
.footer-no-bg{
background:none !important;
}
</style>
	</head>

<script type="text/javascript" >

	function edit_msg_board(rowId,URL) {
		var data = {
			action: 'admin_campaigns_ajaxedit', /* ctrlr_function */
			id : rowId
		};
		//ajaxurl= "http://localhost/sam/test/somnium/wp/wp-admin/admin-ajax.php";

		jQuery.post(ajaxurl, data, function(response) {
			$('#editForm').html(response);
			$( "#datepicker3" ).datepicker({
				showOn: "button",
				buttonImage: "<?php echo $pluginImagesDir;?>datepicker.gif",
				buttonImageOnly: true,dateFormat: 'yy-mm-dd'
			});
			$("#editDialog").load().dialog({
				closeOnEscape: true, /*hide: 'slide',*/
				height: 380,
				width: 580,
				modal:true,
				close: function(event,ui){
					$("editDialog").dialog('destroy');
				}
			});
	
		});
		//loadProprtyAndCategory('categories', 'id','editCategoryId',0); //propertyId categoryId
		//loadProprtyAndCategory('properties', 'id','editPropertyId',0); //categories properties

	}

	// DELETE CAMPAIGN
	function delete_message(rowId){
		var MessageDataDel = {Message:{
			
			id : rowId,
		}};

		var data = {
			id : rowId,
			action: 'admin_campaigns_deletemessage', /* [controller]_[function name] */
			data : MessageDataDel,
		};

		var v=confirm("Are you really want to delete this message?");
		if(v){
			jQuery.post(ajaxurl, data, function(response) {
				
				$('#row_'+rowId).hide();
				//alert(response);
				var matches = response.split(/\b/);
				if(matches.length > 0) { var errorType = matches[0]; }
				if(errorType == 'Error'){
					$('#responceDiv').css("background-color",'#FFBABA');
				} else { $('#responceDiv').css("background-color",'#DDFFDD');}
	
				$('#responceDiv').html(response);	
				$('#responceDiv').show();
				setTimeout(function() {
					$('#responceDiv').hide();
				}, 3000); // Stay 3 seconds

				//location.reload();
			});
		}
	}

	$("#cProperties").live('change', function() {
		var propertyId = $('#cProperties').val();
		var data = {
			action: 'admin_campaigns_index',
			propertyId : propertyId
		};

		jQuery.post(ajaxurl, data, function(response) {
			//alert(response); 
			//$('#editDialog').dialog('destroy');
			//location.reload();
		});

	});

	$("#updateButton").live('click', function() {
		var error = 0;
		if($('#CampaignCampaignName').val() == ''){
			$('#CampaignCampaignName').css("background-color",'#FFEDEF');
			error = 1;
		} else { $('#CampaignCampaignName').css("background-color",'#FFF'); }

		if($('#datepicker3').val() == ''){
			$('#datepicker3').css("background-color",'#FFEDEF');
			error = 1;
		} else { $('#datepicker3').css("background-color",'#FFF'); }

		if($("#Campaign_Category_select").val() == ''){
			$('#Campaign_Category_select').css("background-color",'#FFEDEF');
			error = 1;
		} else { $('#Campaign_Category_select').css("background-color",'#FFF'); }
		if($("#Campaign_Property_select").val() == ''){
			$('#Campaign_Property_select').css("background-color",'#FFEDEF');
			error = 1;
		} else { $('#Campaign_Property_select').css("background-color",'#FFF'); }
		if($("#listedit option:selected").length == 0){
			$('#listedit').css("background-color",'#FFEDEF');
			error = 1;
		} else { $('#listedit').css("background-color",'#FFF'); }

		if(error == 0){

			//saveAddCampaign();
			//return false;
		}
		return false;

/* ---------------  */
		var editId = $('#CampaignHiddenId').val();
		//var postData = $('#Campaign_Edit').serialize();
		var campaign_name = $('#CampaignCampaignName').val();
		var sent_date = $('#datepicker3').val();
		var categoryId = $('#CampaignCategoryId').val();
		var propertyId = $('#CampaignPropertyId').val();
		var active = $('#CampaignActive').val();
		var model = $('#CampaignModel').val();

		var CampaignData = {Campaign:{
			
			id : editId,
			campaign_name : campaign_name,
			sent_date : sent_date,
			categoryId : categoryId,
			propertyId : propertyId,
			active : active	

		}};

		//var CampaignData1 = {Campaign:CampaignData};

		var data = {
			id : editId,
			action: 'admin_campaigns_ajaxedit',
			data : CampaignData,
			};

		jQuery.post(ajaxurl, data, function(response) {
			$('#editDialog').dialog('destroy');
			$('.full_width').prepend('<div id="message"> Campaign has been updated </div>');
			location.reload();
		});
	}); 
	
</script>

<body id="index" class="grid_2_3">
<?php
/**
	//echo 'l:312. POST CNT: '.$post_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM ".$wpdb->prefix."posts"));
	///Creating Property Dropdown
	foreach($properties as $property):
		$propertiesArr[$property->id] = $property->property_name;
	endforeach;
	
	///Creating Categories Dropdown
	foreach($categories as $category):
		//echo $category->id .' - '. $category->category_name.' - '. $category->active.' <br> ';
		$categoriesArr[$category->id] = $category->category_name;
	endforeach; 
*/

	///Creating Categories Dropdown
	foreach($campaignsList as $campaign):
		$campaignArr[$campaign->id] = $campaign->campaign_name;
	endforeach;
?>

<!-- Edit Campaign Dialogue Do not Alter/Change -->
	<div id="editDialog" style="display:none">	
		<input type="hidden" id="hidEditId" name="hidEditId" value="">
		<span id="editForm"></span>	
	</div>
<!-- Edit Campaign Dialogue Ends here -->
<?php
	//echo '<pre>'; print_r($objects);echo '</pre>';
?>

<div id="fw_container">
<h3>Messages</h3>
<style>
.loadingDiv{
	  height: 150px;
    left: 40%;
    position: absolute;
    top: 21%;
    width: 150px;
    z-index: 1000;
}
#campaignlst{ font-weight:normal !important;}
</style>
<!-- Loader Div -->
	<?php $loaderPath = plugins_url().'/campaign-manager/images/ajax-loader.gif'; ?>
	<div style="display:none" class="loadingDiv"><img src='<?php echo $loaderPath; ?>'></div>
<!-- Loader Div ends here -->
<div id="responceDiv" style="display:none"></div>
<div class="full_width">
<div role="grid" class="dataTables_wrapper" id="example_wrapper">
	<div class="fg-toolbar ui-toolbar ui-widget-header ui-corner-tl ui-corner-tr ui-helper-clearfix" style="background-color: rgb(233, 245, 246) ! important;" ><div id="example_length" class="dataTables_length" style="padding-left: 1px;">
	<label>Show</label>
	<select id="row_length" name="row_length" size="1" aria-controls="example">
		<option value="5">5</option>
		<option value="10">10</option>
		<option value="25">25</option>
		<option value="50">50</option>
		<option value="100">100</option>
	</select> entries.&nbsp;&nbsp;
  	<span id="dpic" style="background-color: #FFF; padding: 4px;" ><input id="datepicker" type="text" onChange="paginatMessage('date','ASC','sort')" style="border:none"></span>
	<!--<img id="printButton" src="<?php echo $pluginImagesDir;?>print.png">--> &nbsp;&nbsp; 
	<a href="<?php echo $_SERVER['PHP_SELF'].'?page=mvc_campaigns-addmessage';?>"><img id="customAddBtn1315" src="<?php echo $pluginImagesDir;?>add.png"></a> &nbsp;&nbsp;
<!-- aria-controls="example" -->
<?php
	//echo $this->form->belongs_to_dropdown_nodiv('Property', $properties, array('empty' => 'Select Property','id'=>'cProperties','name'=>'cProperties'));

/* 	Original helper function is: $this->form->select()/ belongs_to_dropdown(). Tis will wrap select box with div
	We altered it to $this->form->select_nodiv() /belongs_to_dropdown_nodiv()
*/



	global $wpdb;
	global $blog_id;
	
	//network admins to be able to search messages by property.
	if(is_super_admin()){

		///Creating Property Dropdown
		foreach($properties as $property):
			$propertiesArr[$property->id] = $property->property_name; //$properties
		endforeach;

		echo $this->form->belongs_to_dropdown_nodiv('Property', $propertiesArr, array('empty' => 'Select Property','id'=>'cProperties','name'=>'cProperties'));
	}

/*
// Filter message based on campaign.
// If network admin, campaigns from all props. Otherwise, from props over which he has admin control.

	if(is_super_admin()){

		/// IF network admin, all properties
		$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM ".$wpdb->base_prefix."blogs"));
		foreach ($blogids as $bloGid) { // loop through all blogs
			if($bloGid == 1){ $prefix = "wp_";} else {	$prefix = "wp_{$bloGid}_"; }

			$sql1 = "SELECT * FROM `". $prefix ."campaigns` WHERE `propertyId` = $bloGid ";
			$results = $wpdb->get_results($sql1);

			foreach($results as $result):
				$campArKey = $result->id.'-'.$result->propertyId; // <<<-  campaignId-propertyId
				$campaign_Arr[$campArKey] = $result->campaign_name; // getall campaign names
			endforeach;
			
		}
		

	} else {
		/// Get SITEIDs on which logged user has admin permission
		$user_ID = get_current_user_id();

		$userSql = "SELECT umeta_id, user_id, meta_key
			FROM ".$wpdb->base_prefix."usermeta
			WHERE meta_value like '%administrator%' and user_id={$user_ID}";
		$whichSites = $wpdb->get_results($userSql);

		foreach ( $whichSites as $whichSite ){
			list($pre,$sid,$cap) = explode('_',$whichSite->meta_key); // wp_3_capabilities 
			if($sid == 'capabilities') { $sid = 1; }
			$applicableSites[] = $sid;
			//echo "$pre : $sid : $cap <br>";	
		}
		$applicableSites = implode(",", $applicableSites);


			if($blog_id == 1){ $prefix = "wp_";} else {	$prefix = "wp_{$blog_id}_"; }

			$sql1 = "SELECT * FROM `". $prefix ."campaigns` WHERE `propertyId` IN ( $applicableSites ) ";
			$results = $wpdb->get_results($sql1);

			foreach($results as $result):
				$campaign_Arr[$result->id] = $result->campaign_name;
			endforeach;
	}
	//echo '<pre>'; print_r($campaign_Arr);echo '</pre>';

	$options = array();
	$options['options'] = $campaign_Arr; //$campaignArr
	$options['value'] = 'category2';
	$options['empty'] = 'Select Campaign';
	$options['label'] = '';
	$options['id'] = 'campaignlst';
	$options['name'] = 'data[Campaign][propertyId]';
	$options['before'] = ''; $options['after'] = ''; 

	echo $this->form->select('data[Campaign][propertyId]', $options); 
*/

?>

<div class="dataTables_filter" id="example_filter" align="center">
	<input type="text" id="searchQ" aria-controls="example" placeholder="search" onkeyup="paginatMessage('message_name','searchQ','s_search')">
	</div></div></div>


	<table border="0" cellspacing="0" cellpadding="0" style="width:980px" id="example" class="display dataTable" aria-describedby="example_info">
	<thead>
		<tr role="row">
        <th class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1" style="width: 172px;" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending">
		<div class="DataTables_sort_wrapper" onClick="paginatMessage('message_name','ASC','sort');">Name<!--<span class="DataTables_sort_icon css_right ui-icon ui-icon-triangle-1-n"></span>--></div></th>
        <th class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1" style="width: 248px;" aria-label="Browser: activate to sort column ascending">
		<div class="DataTables_sort_wrapper">Campaign
		<!--<span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span>--></div></th>
        <th class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1" style="width: 238px;" aria-label="Platform(s): activate to sort column ascending">
		<div class="DataTables_sort_wrapper" onClick="paginatMessage('date','ASC','sort');">Date<!--<span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span>--></div></th>
        <th class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1" style="width: 150px;" aria-label="Engine version: activate to sort column ascending">
		<div class="DataTables_sort_wrapper">Status<!--<span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span>--></div></th>

        <th class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1" style="width: 117px;" aria-label="CSS grade: activate to sort column ascending">
		<div class="DataTables_sort_wrapper">Action<!--<span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span>--></div></th></tr>
	</thead>
	


<style>
#actionTable td{ float:left; height:18px !important; width:13px; }

td.editbg{
	background-image: url(<?php echo $pluginImagesDir."edit.gif"; ?>);
	/*width: 132px;  use you own image size; */
	/*height: 125px;  use you own image size; */
	background-repeat: no-repeat;
	background-position: center;
	text-align: center;
	vertical-align: top;
}
td.delbg{
	background-image: url(<?php echo $pluginImagesDir."trash.gif"; ?>); 
	/*width: 132px;  use you own image size; */
	/*height: 125px;  use you own image size; */
	background-repeat: no-repeat;
	background-position: left top;
	text-align: center;
	vertical-align: top;
}

</style>
<tbody role="alert" aria-live="polite" aria-relevant="all">
		</tbody>

		<thead>
			<tr role="row">
				<th class="ui-state-default" colspan="3">
<?php
					echo '<img id="dropImg" src="'. $pluginImagesDir.'arrow_ltr.png" width="38" height="22" style=" margin-left: 0.3em; margin-right: 0.3em;" />
					<img id="dropImg" onClick="delete_campaign_group(\'delSelected\');" id="dropButton" src="'. $pluginImagesDir.'delete.png" />';
?>
				</th>
				<th class="infooter" colspan="8">
					<span style="text-align:right">
					<?php
						$total = $num_of_pages;
						$pageButtons = '';
						$prevButton = '';
						for($i=1; $i<=$num_of_pages; $i++)
						{
							$nextPage=0;
							if($page > 0){
								$prevPage = $page - 1;
							}
							if($page < $num_of_pages){
								$nextPage = $page + 1;
							}
							if($start > 0) { //if($num_of_pages > $rowPerpage){
								
								$prevPg = $start-$rowPerpage;
								$prevButton .= '<a class="footer-no-bg fg-button ui-button ui-state-default" style="width:33px; padding: 0 5px;" onclick="paginatMessage(0,0,0,\''.$prevPg.'.'.$rowPerpage.'\');">Prev</a>';
							}

							if($i == 1){ $nextI = 0;} else { $nextI = ($i-1)*$rowPerpage; }
							$pageButtons .= '<a class="footer-no-bg fg-button ui-button ui-state-default" onclick="paginatMessage(0,0,0,\''.$nextI.'.'.$rowPerpage.'\');" id="'.$i.'" style="padding: 0 5px;"';
							//if($page == $i){ $pageButtons .= 'style="background-color:red; padding: 0 5px;"'; }
							$pageButtons .= '>';
							$pageButtons .= $i.'</a>';
						}
						$pagination = $prevButton;
						$pagination .= $pageButtons;

						if(($start+$showNum) < $total) {
							$nextPg = $start+$rowPerpage;
							//if($nextPg < $total) {
								$pagination .= '<a class="footer-no-bg fg-button ui-button ui-state-default" style="width:33px; padding: 0 5px;" onclick="paginatMessage(0,0,0,\''.$nextPg.'.'.$rowPerpage.'\');">Next</a>';
							//}
						}

					echo $pagination;
			?>
					</span>
				</th>
			</tr>
		</thead>

		</table>
<!--
		<div class="fg-toolbar ui-toolbar ui-widget-header ui-corner-bl ui-corner-br ui-helper-clearfix">
			<div class="dataTables_info" id="example_info"><!--Showing 1 to 10 of 58 entries--=> &nbsp;</div>
			
			<div style="display:none" class="dataTables_paginate fg-buttonset ui-buttonset fg-buttonset-multi ui-buttonset-multi paging_full_numbers" id="example_paginate">
				<a class="first ui-corner-tl ui-corner-bl ui-state-disabled" tabindex="0" id="example_first">First</a>
				<a class="previous" tabindex="0" id="example_previous">Previous</a>
				<span>
					<a class="fg-button ui-button ui-state-default ui-state-disabled" tabindex="0">1</a>
					<a class="fg-button ui-button ui-state-default" tabindex="0">2</a>
					<a class="fg-button ui-button ui-state-default" tabindex="0">3</a>
					<a class="fg-button ui-button ui-state-default" tabindex="0">4</a>
					<a class="fg-button ui-button ui-state-default" tabindex="0">5</a>
				</span>
				<a class="next fg-button ui-button ui-state-default" tabindex="0" id="example_next">Next</a>
				<a class="last ui-corner-tr ui-corner-br fg-button ui-button ui-state-default" tabindex="0" id="example_last">Last</a>
			</div>
		</div>
-->

		</div>
		</div>
	</div>

<!--<br clear="all">-->

	<div id="pop"  style="display:none;">
		<?php $this->render_view('_addmessage', array('locals' => array($model))); ?>
	</div>
	<div id="printDiv"  style="display:none;">
		<?php $this->render_view('_print', array('locals' => array($model))); ?>
	</div>

</body>
</body>
</html>	
