<?php 
if(is_super_admin()){
	$url = plugins_url()."/campaign-manager/app/public/DataTables/";
	$pluginJsurl = plugins_url()."/campaign-manager/app/public/js/";
	$pluginImagesDir = plugins_url().'/campaign-manager/images/';
	$pluginCSSDir = plugins_url().'/campaign-manager/css/';
	$incAbsPath = ABSPATH."wp-content/plugins/campaign-manager/app/public/DataTables/extras/Editor/";
        $blogurl=home_url()."/";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<div>
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
		<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
		<script type="text/javascript" charset="utf-8">
		
			$(document).ready(function() {
			paginateTemplate('id','DESC',0,'0.5',1);
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

			$(".error").html("");
			document.getElementById("row_length").value="5";
				$("#row_length").live('change', function() {
					paginateTemplate('0','0','s_search');
				});
// 				$("#customAddBtn").live('click', function() {
// 					Popup('pop','Add Template');
// 				});
	
				$("#customAddBtn").live('click', function() {
					Popup('preview','Preview');
				});
	
				$(function() {
					$( "#datepicker" ).datepicker({
						showOn: "button",
						buttonImage: "<?php echo $pluginImagesDir;?>calender.png",
						buttonImageOnly: true, dateFormat: 'yy-mm-dd'
					});
				});
			} );
			function Popup(divname,popTitle)
			{	
				$('#'+divname).load().dialog({
				height: 600,
				width: 800,
				title: popTitle,
				modal:true,
				close: function(event,ui){
				$('#'+divname).dialog('destroy');
					}
				});
			}
		</script>
<style>

	/* this style (ui-icon-closeButton) is for campaign add/edit dialog close button */
	.ui-icon-closeButton{background-image:url(<?php echo $pluginImagesDir.'close.png';?>); background-color:#E9F4F5; color:#E9F4F5;}
	#example_length img { position:relative; top:4px; }
	#example_filter { margin-right:5px; }
	#messageErr {
		background: #FF9EA5;
		border: 1px solid #CCCC66;
		font-size:12px; font-weight:normal;
		line-height: 1.2em;
		margin: 10px 0;
		padding: 10px 10px 10px 35px;
	}
	.error{opacity:0;}
	.dataTables_length {
		
	}
	#example_filter {

	}
	#message {opacity:0;}
	#datepicker{ width:110px; }
	#searchTemplate{ width:110px !important;}
	hr {
		background-color: #D4D4D4;
		border: 0 none;
		clear: both;
		color: #D4D4D4;
		height: 1px;
	}

#ui-datepicker-div{display:none;}
.ui-widget-header {
    background: none repeat scroll 0 0 #E9F5F6 !important;
    }
		</style>
		</head>
<script type="text/javascript" >

	// EDIT Template
	function edit_msg_board(rowId,URL) {

		var data = {
			action: 'admin_campaigns_templateedit',
			id : rowId
		};
			jQuery.post(ajaxurl, data, function(response) {
				$('#editForm').html(response);
				$("#editDialog").load().dialog({
					closeOnEscape: true, 
					height: 675,
					width: 780,
					modal:true,
					close: function(event,ui){
					
						$("#editDialog").dialog('destroy');
						location.reload();
					}
				});
		
			});
	}

	// DELETE Template
	function delete_template(rowId){
		var TemplateDataDel = {Template:{
			id : rowId,
		}};
		var data = {
			id : rowId,
			action: 'admin_campaigns_deleteTemplates',
			data : TemplateDataDel,
		};
		var v=confirm("Are you really want to delete this Template?");
		if(v){
			$('#row_'+rowId).hide();
			jQuery.post(ajaxurl, data, function(response) {
				$('#row_'+rowId).hide();
				paginateTemplate('id','DESC',0,'0.5',1);
				//window.location.href = "<?php echo bloginfo('wpurl');?>/wp-admin/admin.php?page=mvc_campaigns-templates";
			});
		}
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
/*form#addForm div:nth-child(even) {background: #F0F0F0;width: 700px; padding-left:10px; padding-right:20px;}
form#addForm div:nth-child(odd) {background: #FFF;width: 700px; padding-left:10px; padding-right:20px;}*/
form#form1 div:nth-child(even) {background: #F0F0F0;}
form#form1 div:nth-child(odd) {background: #FFF;}

.ui-dialog .ui-dialog-titlebar-close span {
    display: block;
    margin: 1px;
background-image:url(<?php echo $pluginImagesDir.'close.png';?>);
}
#searchTemplate {
    height: 21px;
    margin-top: 7px;
    width: 145px !important;
}
#template_name {
    height: 22px;
    margin-bottom: 8px;
    margin-left: -90px;
    margin-top: 5px;
    width: 575px;
}
label, #your-profile label + a {
    margin-left: 10px;
    vertical-align: middle;
}
label{
    margin-left: 0px !important;
}
</style>
<body id="index" class="grid_2_3">
	<div id="editDialog" style="display:none">	
		<input type="hidden" id="hidEditId" name="hidEditId" value="">
		<span id="editForm"></span>	
	</div>

<div id="fw_container">
<h3>Templates</h3>
<style>
.loadingDiv{
	  height: 150px;
    left: 40%;
    position: absolute;
    top: 21%;
    width: 150px;
    z-index: 1000;
}
</style>
<!-- Loader Div -->
	<?php $loaderPath = plugins_url().'/campaign-manager/images/ajax-loader.gif'; ?>
	<div style="display:none" class="loadingDiv"><img src='<?php echo $loaderPath; ?>'></div>
	<div id="responceDiv" style="display:none"></div>
<div class="full_width">
	<div role="grid" class="dataTables_wrapper" id="example_wrapper">
		<div class="fg-toolbar ui-toolbar ui-widget-header ui-corner-tl ui-corner-tr ui-helper-clearfix" >
			<div id="example_length" class="dataTables_length" style="padding-left: 1px;">
				<label>Show</label>
				<select id="row_length" name="row_length" size="1" aria-controls="example">
					<option value="5">5</option>
					<option value="10">10</option>
					<option value="25">25</option>
					<option value="50">50</option>
					<option value="100">100</option>
				</select> entries.&nbsp;&nbsp;
				<span id="dpic">
				<input type="text" id="datepicker" onchange="paginateTemplate('created_date','ASC','sort')">
				</span>
				&nbsp;&nbsp;<a href="<?php echo $_SERVER['PHP_SELF'].'?page=mvc_campaigns-addTemplate';?>"><img id="customAddBtn" src="<?php echo $pluginImagesDir;?>add.png"></a> &nbsp;&nbsp;
				
			
			<div class="dataTables_filter" id="example_filter"><label>
			<input placeholder="Search" type="text" aria-controls="example" id="searchTemplate" onkeyup="paginateTemplate('template_name','searchTemplate','s_search')"></label>
			</div>
		</div></div>
		<table border="0" cellspacing="0" cellpadding="0"  id="example" class="display dataTable" aria-describedby="example_info">
<!-- style="width:980px"-->
		<thead>
			<tr role="row">
		<th style="width:580px;" id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1"  aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending"><!-- style="width: 172px;"-->
			<div class="DataTables_sort_wrapper" onClick="paginateTemplate('template_name','ASC','sort');">Name</div></th>
		<th style="text-align: center; width: 150px;" id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1"  aria-label="Browser: activate to sort column ascending"><!-- style="width: 248px;"-->
			<div class="DataTables_sort_wrapper" onClick="paginateTemplate('created_date','ASC','sort');">Created On</div></th>
		<th style="text-align: center; width: 150px;" id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1"  aria-label="CSS grade: activate to sort column ascending">
			<div class="DataTables_sort_wrapper">Action</div></th></tr><!-- style="width: 117px;"-->
		</thead>
	
		<tbody role="alert" aria-live="polite" aria-relevant="all">
			
		</tbody>
		
		<thead>
			<tr role="row">
				<th class="infooter" colspan="8" style="border:0px;">
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
							if($start > 0) {
								
								$prevPg = $start-$rowPerpage;
								$prevButton .= '<a class="fg-button ui-button ui-state-default" style="width:33px; padding: 5px 5px; font-weight:bold;" onclick="paginateTemplate(0,0,0,\''.$prevPg.'.'.$rowPerpage.'\');">Prev</a>';
							}

							if($i == 1){ $nextI = 0;} else { $nextI = ($i-1)*$rowPerpage; }
							$pageButtons .= '<a class="fg-button ui-button ui-state-default" onclick="paginateTemplate(0,0,0,\''.$nextI.'.'.$rowPerpage.'\',\''.$i.'\');" id="'.$i.'"';
							if($i == 1){ $pageButtons .= 'style="color:#000000 !important;font-weight:bold; padding: 5px 5px;"'; } else { $pageButtons .= ' style="padding: 5px 5px; "'; }
							$pageButtons .= '>';
							$pageButtons .= $i.'</a>';
						}
						$pagination = $prevButton;
						$pagination .= $pageButtons;

						if(($start+$showNum) < $total) {
							$nextPg = $start+$rowPerpage;
								$pagination .= '<a class="fg-button ui-button ui-state-default" style="width:33px; padding: 5px 5px; font-weight:bold;" onclick="paginateTemplate(0,0,0,\''.$nextPg.'.'.$rowPerpage.'\',2);">Next</a>';
							//}
						}

					echo $pagination;
			?>
					</span>
				</th>
			</tr>
		</thead>

		</table>

<!--		<div class="fg-toolbar ui-toolbar ui-widget-header ui-corner-bl ui-corner-br ui-helper-clearfix">
			<div class="dataTables_info" id="example_info">&nbsp;</div>
			
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
		</div>-->

	</div>
</div>
</div>
<div id="pop"  style="display:none;">
	<?php
		$this->render_view('addTemplate', array('locals' => array($model)));
	?>
	<div id="saveMsg"> </div>
</div>
<div id="previewTemplate" style="display:none">Preview <hr>
	<div id="pevPlace" style="height:100%;height:100%; font-weight:normal;"></div>
</div>
</body>
</div>
</html>
<?php
}
else{
?>
<div style="background: none repeat scroll 0 0 #FFCCCC;
    border: 1px solid #FF8080;
    font-size: 12px;
    line-height: 1.3em;
    margin-bottom: 22px;
    padding: 11px;width:80%" align="center"><b>You don't have permission to create or edit Templates but you can use templates for creating messages.</b>
</div>
<?php
}
?>
