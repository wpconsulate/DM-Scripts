<?php 

	$url = plugins_url()."/campaign-manager/app/public/DataTables/";
	$pluginJsurl = plugins_url()."/campaign-manager/app/public/js/";
	$pluginImagesDir = plugins_url().'/campaign-manager/images/';
	$pluginCSSDir = plugins_url().'/campaign-manager/css/';
	$incAbsPath = ABSPATH."wp-content/plugins/campaign-manager/app/public/DataTables/extras/Editor/";
        $blogurl=home_url()."/";
        

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<link rel="shortcut icon" type="image/ico" href="http://www.datatables.net/favicon.ico" />
		
		<title>DataTables example</title>
		<style type="text/css" title="currentStyle">

			@import "<?php echo $url; ?>media/css/demo_page.css";
			@import "<?php echo $url; ?>media/css/jquery.dataTables.css";
			@import "<?php echo $url; ?>extras/TableTools/media/css/TableTools.css";
			@import "<?php echo $url; ?>extras/Editor/media/css/dataTables.editor.css";
			@import "<?php echo $pluginCSSDir; ?>template.css";  <!-- Do not change -->
			@import "<?php echo $pluginCSSDir; ?>jquery-ui.css";  <!-- Do not change -->


		</style>
		  
		<script type="text/javascript" language="javascript" src="<?php echo $url; ?>media/js/jquery.js"></script>
		<script type="text/javascript" language="javascript" src="<?php echo $url; ?>media/js/jquery.dataTables.js"></script>
		<script type="text/javascript" charset="utf-8" src="<?php echo $url; ?>extras/TableTools/media/js/TableTools.js"></script>
		<script type="text/javascript" charset="utf-8" src="<?php echo $url; ?>extras/Editor/media/js/dataTables.editor.js"></script>
		<script type="text/javascript" charset="utf-8" src="<?php echo $pluginJsurl; ?>jquery.printElement.js"></script>
		<!--<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>-->
		<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>

<style>
	/* this style (ui-icon-closeButton) is for campaign add/edit dialog close button */
.ui-icon-closeButton{background-image:url(<?php echo $pluginImagesDir.'close.png';?>); background-color:#E9F4F5; color:#E9F4F5;}
</style>
		<script type="text/javascript" charset="utf-8">

		$(document).ready(function() {

			//delete_msg_board('propertyId','cProperties','s_search');
                        delete_msg_board(0,0,0,'0.5',1);
			$("#cProperties").live('change', function() {
				delete_msg_board('propertyId','cProperties','s_search');
			});

			$("#row_length").live('change', function() {
				delete_msg_board('propertyId','cProperties','s_search');
			});


			$("#customAddBtn").live('click', function() {
				loadProprtyAndCategory('categories', 'id','categoryId',0,1); //categories properties
				loadProprtyAndCategory('properties', 'id','propertyId',0,0); //categories properties
				Popup(); 
				//Placing the close button  ui-dialog-titlebar-close ui-corner-all	ui-icon ui-icon-closethick
				//$('.ui-icon-closethick').html('<img width="16" height="16" src="<?php echo $pluginImagesDir.'close.png'; ?>">'); 
				//$('.ui-dialog-titlebar-close ui-corner-all').html('<img width="16" height="16" src="<?php echo $pluginImagesDir.'close.png'; ?>">');

				$('.ui-icon-closethick').attr('class', 'ui-icon-closeButton');				
				$('.ui-icon-closeButton').text('.');

				//$('.ui-icon-closeButton').attr('title', '');
				//$('.ui-icon-closeButton').attr('alt', '');
				//$('.ui-icon-closeButton').height(18); $('#container').width(18);

			});

			$("#printButton").live('click', function() {
				printCampaign();
			});
 
			$(".editButton").live('click', function() {
				editThisCampaign(this.id);
			});

			$(function() {
				$( "#datepicker" ).datepicker({
					showOn: "button",
					buttonImage: "<?php echo $pluginImagesDir;?>calender.png",
					buttonImageOnly: true, dateFormat: 'yy-mm-dd'
				});
			});

			$(function() {
				$( "#datepicker2" ).datepicker({
					showOn: "button",
					buttonImage: "<?php echo $pluginImagesDir;?>calender.png",
					buttonImageOnly: true,dateFormat: 'yy-mm-dd',
					minDate : 0,
				});
				$( "#datepicker5,#datepicker6" ).datepicker({
					showOn: "both",
					beforeShow: customRange,
					dateFormat: "dd M yy",
                                        firstDay: 1, 
                                        changeFirstDay: false,

					buttonImage: "<?php echo $pluginImagesDir;?>calender.png",
					buttonImageOnly: true,dateFormat: 'yy-mm-dd'
				});
				function customRange(input) {
                                        if (input.id == 'datepicker6') {
                                                return {
                                                        minDate: jQuery('#datepicker5').datepicker("getDate")
                                                };
                                        } else if (input.id == 'datepicker5') {
                                                return {
                                                maxDate: jQuery('#datepicker6').datepicker("getDate")
                                                };
                                        }
                                }
                                
			});
			
                        $('#addCategoryButton').live('click', function (e){
                                if($("#propertyId").val() == 0){
                                        $('#propertyId').css("background-color",'#FFEDEF');
                                        $('#catresp').css("background-color",'#FFEDEF');
                                        $('#catresp').html('Select a property');
                                        e.preventDefault();
                                } else {
                                        $('#propertyId').css("background-color",'#FFF');
                                        SaveCat(1);
                                        e.preventDefault();
                                }
                        });			

			$("#AddCatBtn").live('click', function() {
				$('#catform').toggle(); ///SaveCat(1)
				html="<label for='CreateCat'>New Category Name</label><input id='CreateCat' type='text' >";
				html=html+"<button style='height: 23px;width:50px;' id='addCategoryButton' >add</button>		<span id='catresp' style='padding:5px;'></span>";
				/*html=html+"<button style='height: 23px;width:50px;' onclick='SaveCategoryCheck(); return false;'>add</button>";*/
				$('#catform').html(html);
			});



// *******************CHEK ALL UNCHECK ALL **************************
    // add multiple select / deselect functionality
    $("#selectall").live('change', function () {
          $('.case').attr('checked', this.checked);
    });
 
    // if all checkbox are selected, check the selectall checkbox
    // and viceversa
    $(".case").live('change', function(){
 
        if($(".case").length == $(".case:checked").length) {
            $("#selectall").attr("checked", "checked");
        } else {
            $("#selectall").removeAttr("checked");
        }
 
    });
// *****************************************************************

		} );

		function Popup()
		{
			$("#pop").load().dialog({ title: 'Add Campaign',
			height: 400,
			width: 700,
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
			var pageUrl = '<?php echo ABSPATH;?>wp-admin/admin.php?page=mvc_campaigns-edit&id='+cId;
			$.post("<?php echo plugins_url().'/campaign-manager/app/views/admin/campaigns/';?>test.php", { editid: cId,  blogurl: "<?php echo ABSPATH; ?>" },
				function(data) {
					$('#editForm').html(data);
			});

			$('#hidEditId').val(cId);
			$("#editDialog").load(pageUrl).dialog({
				height: 380,
				width: 708,
				modal:true,
				close: function(event,ui){
				$("editDialog").dialog('destroy');
				}
			});	
		}

	function showIt(imgId,classid){
		$('#row_'+imgId).css("background-color", '#DCEBFE');
		$('#editImg-'+imgId).attr('src', '<?php echo $pluginImagesDir."edit-hover.png"; ?>');
		$('#dropImg-'+imgId).attr('src', '<?php echo $pluginImagesDir."delete-hover.png"; ?>');
	}
	
	function hideIt(imgId,classid){
		$('#row_'+imgId).css("background-color", classid);
		$('#editImg-'+imgId).attr('src', '<?php echo $pluginImagesDir."edit.png"; ?>');
		$('#dropImg-'+imgId).attr('src', '<?php echo $pluginImagesDir."delete.png"; ?>');	
	}

	</script>
<style>
	#example_length img { position:relative; top:4px; }
	#example_filter { margin-right:5px; }
	#message { background: #DDFFDD; border: 1px solid #CCCC66; font-size:12px; font-weight:normal; line-height: 1.2em; margin: 10px 0; padding: 10px 10px 10px 35px; }
	#messageErr { background: #FF9EA5; border: 1px solid #CCCC66; font-size:12px; font-weight:normal; line-height: 1.2em; margin: 10px 0; padding: 10px 10px 10px 35px; }
	#ui-datepicker-div{font-size:12px !important; font-weight:normal !important;}
		label, #your-profile label + a {
		margin-left: 10px;
		vertical-align: middle;
	}
	label{
		margin-left: 0px !important;
	}
	#cProperties{
		height: 23px;
		position: absolute;
		top: 4px;
	}
	#searchQ{
		height: 23px !important;
		margin-top: 4px !important;
	}
 .ui-datepicker {
    padding: 0.2em 0.2em 0;
    width: 17em;
 display:none;
}
</style>
	</head>

<script type="text/javascript" >

	function edit_msg_board(rowId,prOPid) {
		var data = {
			action: 'admin_campaigns_ajaxedit', /* ctrlr_function */
			id : rowId,
			propertyId: prOPid
		};
		jQuery.post(ajaxurl, data, function(response) {
			$('#editForm').html(response);
			$( "#datepicker3" ).datepicker({
				showOn: "button",
				buttonImage: "<?php echo $pluginImagesDir;?>calender.png",
				buttonImageOnly: true,dateFormat: 'yy-mm-dd'
			});
			$("#editDialog").load().dialog({
				title: 'Edit Campaign',
				closeOnEscape: true, /*hide: 'slide',*/
				height: 380,
				width: 708,
				modal:true,
				close: function(event,ui){
					$("editDialog").dialog('destroy');
				}
			});
	
		});
		$('.ui-icon-closethick').attr('class', 'ui-icon-closeButton');
		$('.ui-icon-closethick').attr('class', 'ui-icon-closeButton');
		$('.ui-icon-closeButton').text('.');
	}

	// DELETE CAMPAIGN
	function delete_campaign(rowId,prOPid){
		var CampaignDataDel = {Campaign:{
			
			id : rowId,
			propertyId: prOPid,
		}};

		var data = {
			id : rowId,
			propertyId: prOPid,
			action: 'admin_campaigns_deletecampaign', /* [controller]_[function name] */
			data : CampaignDataDel,
		};

		var v=confirm("Are you really want to delete this campaign?");
		if(v){
			jQuery.post(ajaxurl, data, function(response) {
				$('#row_'+rowId).hide();
				$('#responceDiv').html(response);
				$('#responceDiv').show();
		
				alert(response);
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

			updateCampaign();
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
	///Creating Property Dropdown
	foreach($properties as $property):
		$propertiesArr[$property->id] = $property->property_name;
	endforeach;
	
	///Creating Categories Dropdown
	foreach($categories as $category):
		$categoriesArr[$category->id] = $category->category_name;
	endforeach; 

?>

<!-- Edit Campaign Dialogue Do not Alter/Change -->
	<div id="editDialog" style="display:none;">
		<input type="hidden" id="hidEditId" name="hidEditId" value="">
		<span id="editForm"></span>	
	</div>
<!-- Edit Campaign Dialogue Ends here -->

<div id="fw_container">
<h3>Campaign</h3>
<style>
	.loadingDiv{
		height: 150px;
		left: 40%;
		position: absolute;
		top: 21%;
		width: 150px;
		z-index: 1000;
	}
	
	.savebutton {
		background: url(<?php echo $pluginImagesDir.'button-bg.png';?>) no-repeat scroll 0 0 transparent;
		border: medium none;
		font-family: Verdana,Arial,sans-serif;
		font-size: 12px;
		height: 32px;
		padding-bottom: 5px;
		padding-top: 5px;
		width: 190px;
	}
	
	#dpic{ background-color:#fff; padding:3px;}
	#datepicker{border-color: #fff;}
	#colHead{background-image:url(<?php echo $pluginImagesDir.'grdnt.png';?>);}
	.ui-datepicker .ui-datepicker-title {
		line-height: 1.8em;
		margin: 0 2.3em;
		text-align: center;background-image:url(<?php echo $pluginImagesDir.'grdnt.png';?>);
	}
	.ui-datepicker .ui-datepicker-prev span, .ui-datepicker .ui-datepicker-next span {
		background-image:url(<?php echo $pluginImagesDir.'grdnt.png';?>);
		display: block;
		left: 50%;
		margin-left: -8px;
		margin-top: -8px;
		position: absolute;
		top: 50%; 
	}
	.ui-datepicker .ui-datepicker-prev {
		background-image:url(<?php echo $pluginImagesDir.'grdnt.png';?>);
		left: 2px;
	}
	.ui-datepicker .ui-datepicker-next {
		background-image:url(<?php echo $pluginImagesDir.'grdnt.png';?>);
		right: 2px;
	}
	.ui-helper-clearfix:after {
		background-image:url(<?php echo $pluginImagesDir.'grdnt.png';?>);
		clear: both;
		content: ".";
		display: block;
		height: 0;
		visibility: hidden;
	}
	.ui-datepicker-week-end{color:red;}
	.ui-datepicker-calendar thead:nth-child(1){background-image:url(<?php echo $pluginImagesDir.'grdnt.png';?>);}
	
	#addForm div{ width:100% !important; }
	form#addForm div:nth-child(even) {background: #F0F0F0;width: 700px; padding-left:10px; padding-right:20px;}
	form#addForm div:nth-child(odd) {background: #FFF;width: 700px; padding-left:10px; padding-right:20px;}
	form#form1 div:nth-child(even) {background: #F0F0F0;}
	form#form1 div:nth-child(odd) {background: #FFF;}
	
	.ui-dialog .ui-dialog-titlebar-close span {
		display: block;
		margin: 1px;
		background-image:url(<?php echo $pluginImagesDir.'close.png';?>);
	}

</style>
<!-- Loader Div -->
	<?php $loaderPath = plugins_url().'/campaign-manager/images/ajax-loader.gif'; ?>
	<div style="display:none" class="loadingDiv"><img src='<?php echo $loaderPath; ?>'></div>
<!-- Loader Div ends here -->
<div id="responceDiv" style="display:none"></div>
<div class="full_width">
<div role="grid" class="dataTables_wrapper" id="example_wrapper">
	<div class="fg-toolbar ui-toolbar ui-widget-header ui-corner-tl ui-corner-tr ui-helper-clearfix">
	<div id="example_length" class="dataTables_length">
	<label>Show </label> 
	<select id="row_length" name="row_length" size="1" style="height: 23px;" aria-controls="example">
		<option value="5">5</option>
		<option value="10">10</option>
		<option value="25">25</option>
		<option value="50">50</option>
		<option value="100">100</option>
	</select> entries.&nbsp;&nbsp;

	<span id="dpic" style="padding-bottom: 2px;">
  	<input type="text" id="datepicker" onChange="delete_msg_board('sent_date','ASC','sort')"></span>
  	<!--<img src="<?php echo $pluginImagesDir;?>datepicker.gif">-->&nbsp; 
	<img id="printButton" src="<?php echo $pluginImagesDir;?>print.png"> &nbsp;&nbsp; 
	<img id="customAddBtn" src="<?php echo $pluginImagesDir;?>add.png" > &nbsp;&nbsp;
<!-- aria-controls="example" -->
<?php
	if(is_super_admin()){
		echo $this->form->belongs_to_dropdown_nodiv('Property', $propertiesArr, array('empty' => 'Select Property','id'=>'cProperties','name'=>'cProperties'));
	}
	/* 	Original helper function is: $this->form->select()/ belongs_to_dropdown(). Tis will wrap select box with div
		We altered it to $this->form->select_nodiv() /belongs_to_dropdown_nodiv()
	*/
?>

<div class="dataTables_filter" id="example_filter" align="center" >
	<label><input type="text" id="searchQ" placeholder="Search" aria-controls="example" onkeyup="delete_msg_board('campaign_name','searchQ','s_search')"></label>
	</div></div></div>

<!-- GRID IS LOADED FROM configs/test.php -->
	<table border="0" cellspacing="0" cellpadding="0" style="width:980px" id="example" class="display dataTable" aria-describedby="example_info">
	<thead>
		<tr role="row">
	<th id="colHead" aria-label="Rendering engine: activate to sort column descending" aria-sort="ascending" style="width: 25px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper" >&nbsp;</div></th>
        <th id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1" style="width: 172px;" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending">
		<div class="DataTables_sort_wrapper" onClick="delete_msg_board('campaign_name','ASC','sort');">Name<!--<span class="DataTables_sort_icon css_right ui-icon ui-icon-triangle-1-n"></span>--></div></th>
        <th id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1" style="width: 248px;" aria-label="Browser: activate to sort column ascending">
		<div class="DataTables_sort_wrapper">Property
		<!--<span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span>--></div></th>
		        <th id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1" style="width: 248px;" aria-label="Browser: activate to sort column ascending">
		<div class="DataTables_sort_wrapper">Category
		<!--<span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span>--></div></th>
		
        <th id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1" style="width: 238px;" aria-label="Platform(s): activate to sort column ascending">
		<div class="DataTables_sort_wrapper" onClick="delete_msg_board('sent_date','ASC','sort');">Date<!--<span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span>--></div></th>
        <th id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1" style="width: 150px;" aria-label="Engine version: activate to sort column ascending">
		<div class="DataTables_sort_wrapper">Sent<!--<span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span>--></div></th>
        <th id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1" style="width: 150px;" aria-label="Engine version: activate to sort column ascending">
		<div class="DataTables_sort_wrapper">Opens<!--<span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span>--></div></th>
        <th id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1" style="width: 150px;" aria-label="Engine version: activate to sort column ascending">
		<div class="DataTables_sort_wrapper">Clicks<!--<span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span>--></div></th>
        <th id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1" style="width: 150px;" aria-label="Engine version: activate to sort column ascending">
		<div class="DataTables_sort_wrapper">Bounces<!--<span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span>--></div></th>
        <th id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1" style="width: 117px;" aria-label="CSS grade: activate to sort column ascending">
		<div class="DataTables_sort_wrapper">Action<!--<span class="DataTables_sort_icon css_right ui-icon ui-icon-carat-2-n-s"></span>--></div></th></tr>
	</thead>
	
<tbody role="alert" aria-live="polite" aria-relevant="all" style="display:none">
		<?php
			$class = 'odd';$a = 1;
			for($i=0;$i<count($objects);$i++){
			$cnt = $a%2; $class = ($cnt) ? 'odd' : 'even'; $a++; // Alternate row color
		?>
		<?php // $objects[$i]->propertyId .' : ' .?>
		<tr id="row_<?php echo $objects[$i]->id; ?>" class="gradeA <?php echo $class; ?>">

			<td>&nbsp;</td>
		<!-- Sujith sept 14 -->
			<td id="cname_<?php echo $objects[$i]->id; ?>" onclick="hili('<?php echo $objects[$i]->id; ?>','cname_<?php echo $objects[$i]->id; ?>');" class="  sorting_1"><?php echo $objects[$i]->campaign_name; ?></td>
			<td id="pname_<?php echo $objects[$i]->id; ?>" onclick="hili('<?php echo $objects[$i]->id; ?>','pname_<?php echo $objects[$i]->id; ?>','<?php echo $objects[$i]->propertyId; ?>');" class=" "><?php echo $objects[$i]->propertyId; ?></td>
		<!-- Sujith sept 14 -->
			<td id="sdate" class=" "><?php echo $objects[$i]->sent_date; ?></td>
			<td class="center ">1.8</td>
			<td class="center ">1.8</td>
			<td class="center ">1.8</td> 
			<td class="center ">1.8</td>
			<td class="center "> 
				<!-- onClick="editThisCampaign('<?php echo $objects[$i]->id; ?>')" -->
				<!-- class="editButton" id="<?php echo $objects[$i]->id; ?>" -->
				<img onClick="edit_msg_board(<?php echo $objects[$i]->id; ?>);" src="<?php echo $pluginImagesDir;?>edit.gif" /> / 
				<img onClick="delete_campaign(<?php echo $objects[$i]->id; ?>);" id="dropButton" src="<?php echo $pluginImagesDir;?>trash.gif" /></td>
		</tr>
		<?php } ?>
		</tbody>

		<thead style="display:none">
			<tr role="row">
				<!--<th class="ui-state-default" colspan="3"><?php  echo 'Showing 1 to '.$rowPerpage.' of '.$totalresult.' entries '; ?>Showing 1 to 10 of 18 entries &nbsp; </th>-->
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
								$prevButton .= '<a class="fg-button ui-button ui-state-default" style="width:33px; padding: 0 5px;" onclick="delete_msg_board(0,0,0,\''.$prevPg.'.'.$rowPerpage.'\');">Prev</a>';
							}

							if($i == 1){ $nextI = 0;} else { $nextI = ($i-1)*$rowPerpage; }
							$pageButtons .= '<a class="fg-button ui-button ui-state-default" onclick="delete_msg_board(0,0,0,\''.$nextI.'.'.$rowPerpage.'\');" id="'.$i.'" style="padding: 0 5px;"';
							//if($page == $i){ $pageButtons .= 'style="background-color:red; padding: 0 5px;"'; }
							$pageButtons .= '>';
							$pageButtons .= $i.'</a>';
						}
						$pagination = $prevButton;
						$pagination .= $pageButtons;

						if(($start+$showNum) < $total) {
							$nextPg = $start+$rowPerpage;
							//if($nextPg < $total) {
								$pagination .= '<a class="fg-button ui-button ui-state-default" style="width:33px; padding: 0 5px;" onclick="delete_msg_board(0,0,0,\''.$nextPg.'.'.$rowPerpage.'\');">Next</a>';
							//}
						}

					echo $pagination;
			?>
					</span>
				</th>
			</tr>
		</thead>

		</table>


		</div>
		</div>
	</div>

<!--<br clear="all">-->
<!---------------------------------------------Chart:start:sujith:20 Sept 2012----------------------------------------------------->
<?php $public = plugins_url()."/campaign-manager/app/public/"; ?>
<script>
//highlight row

idCamp=Array();
idProp=Array();
propId=Array();

function hili(id,rowid,propid){
row=document.getElementById(rowid);
if(!row.hilite){
        row.origColor=row.style.backgroundColor;
        row.style.backgroundColor='#BCD4EC';
        row.hilite = true;
} else{
row.style.backgroundColor=row.origColor;
row.hilite = false; 
}
check="cname_"+id;
if(rowid==check) hliteCamp(id);
else hliteProp(id,propid);
}
function hliteCamp(id){
if(idProp){
for(i=0;i<=idProp.length;i++){
        rowid="pname_"+idProp[0];
        row=document.getElementById(rowid); 
        if(row){ 
        row.style.backgroundColor="";
        row.hilite = false; 
        idProp.shift();
        } 
    }
}

        $("#datepicker5").val("");
        $("#datepicker6").val("");

f=false;
 for(var i=0; i<idCamp.length;i++ )
   { 
        if(idCamp[i]==id){
              idCamp.splice(i,1);
              f=true;
        }
  }
  if(!f) idCamp.push(id);
  if(idCamp.length>2){
  rowid="cname_"+idCamp[0];
  
        row=document.getElementById(rowid);  
        if(row){
        row.style.backgroundColor="";
        row.hilite = false; 
         } 
        idCamp.shift();

   }

  requestData(3,idCamp);
}
function hliteProp(id,propid){

if(idCamp){
for(i=0;i<=idCamp.length;i++){
        rowid="cname_"+idCamp[0];
        row=document.getElementById(rowid); 
        if(row){ 
        row.style.backgroundColor="";
        row.hilite = false; 
        idCamp.shift();
        } 
    }
}

        $("#datepicker5").val("");
        $("#datepicker6").val("");

f=false;

for(var i=0; i<idProp.length;i++ )
   { 
        if(idProp[i]==id){
              idProp.splice(i,1);
              f=true;
        }
  }
if(!f) idProp.push(id);
if(idProp.length>2){
  rowid="pname_"+idProp[0];
 row=document.getElementById(rowid);
 if(row){
 row.style.backgroundColor="";
 row.hilite = false;
 } 
 idProp.shift(); 
}
f2=false;
for(var i=0; i<propId.length;i++ )
   { 
        //if(propId[i]==propid){
         //     propId.splice(i,1);
          //    f2=true;
       // }
  }
if(!f2) propId.push(propid);
if(propId.length>2){
 propId.shift(); 
}

requestData(4,propId);
//alert("Property ids: "+idProp);
}

//end highlight row



var chart; // global
function requestData(type,val) {
switch(type){
case 1: 
        //val1=$("#combo_box").val();      
        //if(val !=="undefined" && val!= "" && val !="NaN"){
                val1=val;
                val2=0;
 
break;
case 1:
       val1=0;
       val2=0;
       break;
case 2:
        val1=$("#datepicker5").val();
        val2=$("#datepicker6").val();
break;
case 3:
        val1=val;
        val2=0;
break;
case 4:
        val1=val;
        val2=0;
break;
default:
        val1=0;
        val2=0;
}
  
    $.ajax({
        url: "<?php echo $blogurl; ?>external/chart/live-server-data.php?code=NxYjL&type="+type+"&val1="+val1+"&val2="+val2,
        success: function(point) {
        $("#stat").show();
       options.xAxis.categories=point[1];
       imax=parseInt(point[0][0]);
      for(i=0;i<8;i++){
         options.series[i].data=Array();
        } 
for(i=2;i<imax;i++){
 k=i-2;
            
   options.series[k].data=point[i];
           
}            

                var chart = new Highcharts.Chart(options);
            // call it again after one second 
 
        },
        cache: false
    });
}

$(document).ready(function() {
$("#datepicker5,#datepicker6").change(function(){
  date1=$("#datepicker5").val();
  date2=$("#datepicker6").val();
  if(date1 && date2){
    requestData(2);
  } 
});

    options = {
        chart: {
            renderTo: 'container',
            defaultSeriesType: 'spline',
            zoomType: 'x',
            events: {
               /* load: requestData(1) */
                            
            }
        },
        title: {
            text: 'Campaign'
        },
        xAxis: {
             categories:[],
        },
        yAxis: {
            minPadding: 0.2,
            maxPadding: 0.2,
            title: {
                text: 'Number Of Messages',
                margin: 80
            }
        },
        
        
        
            legend: {
             enabled: true,
             align: 'left',
             //backgroundColor: '#FCFFC5',
             borderColor: 'black',
             borderWidth: 2,
             layout: 'vertical',
             verticalAlign: 'top',
             y: 0,
             shadow: true
            },
    
            tooltip: {
                shared: true,
                crosshairs: true
            },
    
            plotOptions: {
                series: {
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function() {
                                hs.htmlExpand(null, {
                                    pageOrigin: {
                                        x: this.pageX,
                                        y: this.pageY
                                    },
                                    headingText: this.series.name,
                                    maincontentText: this.y +" " +this.series.name,
                                    width: 200
                                });
                            }
                        }
                    },
                    marker: {
                        lineWidth: 1
                    }
                }
            },
        series: [
        {
            name: 'Send 1',
            data: []
        },
        {
            name: 'Click 1',
            data: []
        },
        {
            name: 'Open 1',
            data: []
        },
        {
            name: 'Bounce 1',
            data: []
        },
        {
            name: 'Send 2',
            data: []
        },
        {
            name: 'Click 2',
            data: []
        },
        {
            name: 'Open 2',
            data: []
        },
        {
            name: 'Bounce 2',
            data: []
        }                     
        ]
    }       
});
</script>
	<body>
<script src="<?php echo $public; ?>chart/highcharts.js"></script>
<script src="<?php echo $public; ?>chart/exporting.js"></script>
<script type="text/javascript" src="http://www.highcharts.com/highslide/highslide-full.min.js"></script>
<script type="text/javascript" src="http://www.highcharts.com/highslide/highslide.config.js" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="http://www.highcharts.com/highslide/highslide.css" />
<style>
.text_style{
background: none repeat scroll 0 0 #D6D6D6;
    color: #666666;
    float: left;
    font-family: arial;
    font-size: 18px;
    margin: 0 0 10px;
    padding: 10px 0;
    text-align: center;
    text-transform: capitalize;
    width: 100%;
}
.date_pic input{
width:150px;
}
#stat_wrapper{
padding:10px;
}
.date_pic{
 margin-left: 200px;
}
.text_adj{
padding: 0 10px 0 0;
 font-family: arial;
    font-size: 14px;
}
</style>
<br clear="all" />
<div id="stat"  style="min-width: 400px; max-width: 980px; margin: 0 auto;border: 1px solid; display:none;" >
<div id="stat_wrapper">
<div class="text_style"><b>statistics</b></div><br />
<div class="date_pic">
<span class="text_adj">
From</span><input type="text" id="datepicker5" /> &nbsp &nbsp<span class="text_adj">To</span><input type="text" id="datepicker6" />
</div>
<div id="container" style="min-width: 400px; max-width: 980px; height: 400px; margin: 0 auto;"></div>
</div>
</div>
<br clear="all" />
<!----------------------------------------------chart end:sujith:20 Sept 2012------------------------------------------------->
	<div id="pop"  style="display:none;">

		<?php $this->render_view('_add', array('locals' => array($model))); ?>
	</div>
	<div id="printDiv"  style="display:none;">  
		<?php $this->render_view('_print', array('locals' => array($model))); ?>
	</div>

</body>
</body>
</html>	
