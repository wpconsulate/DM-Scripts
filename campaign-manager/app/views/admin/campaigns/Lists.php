<?php 
	$url = plugins_url()."/campaign-manager/app/public/DataTables/";
	$pluginImagesDir = plugins_url().'/campaign-manager/images/'; 
	//http://somdev1.us/wp-content/plugins/campaign-manager/images/
	$pluginCSSDir = plugins_url().'/campaign-manager/css/';
	$incAbsPath = ABSPATH."wp-content/plugins/campaign-manager/app/public/DataTables/extras/Editor/";
	global $blog_id,$wpdb;
	if($s_props){
        if($s_props=="1"){
                $prefix="wp_";
        }else{
                $prefix="wp_".$s_props."_";
        }
}else{
        $prefix=$wpdb->prefix;
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<link rel="shortcut icon" type="image/ico" href="http://www.datatables.net/favicon.ico" />
		
		<title>Campaign Manger-Lists</title>
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
        <script type="text/javascript" charset="utf-8" src="<?php echo $url; ?>media/js/jquery.form.js"></script>
  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
  


	<script type="text/javascript" charset="utf-8">

		$(document).ready(function() {
		ListPages('list_name','ASC',0,'0.5','1');
			$('#row_length').val(5);
			$("#row_length").live('change', function() {
				ListPages('0','0','0');
			});
			$("#cProperties").live('change', function() {
				ListPages('0','0','0'); // sam Jan 14
			});			
			
			$("#row_length").live('change', function() {
				ListPages('0','0','0');
			});			

			$("#customAddBtn").live('click', function() {
				PopupAddPage(); //Popup(); changed : sam 3.12.12
			});

			$("#printButton").live('click', function() {
				printCampaign();
			});
 
			$("#editButton").live('click', function() {
				Popup();
			});

			$("#dropButton").live('click', function() {

				//printCampaign();
			});


			$(function() {
				$( "#datepicker" ).datepicker({
					showOn: "button",
					buttonImage: "<?php echo $pluginImagesDir;?>calender.png",
					buttonImageOnly: true, dateFormat: 'yy-mm-dd'
				});
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


//-----------------delete template>>>>>>>>>>>>>>>>> Sujith 10 dec 2012
	function delete_list_group(){ // line  111

		//var atLeastOneIsChecked = $("input:checkbox[name=case]:checked").length; // > 0;
		if($("input:checkbox[name=case]:checked").length > 0){
			var v = confirm("Are you really want to delete these Lists & related contacts?");
			if(v){
	                        var propertyID =$("#cProperties").val();
				var selectedRows = new Array();
				$("input:checkbox[name=case]:checked").each(function() {
					selectedRows.push($(this).val());
				});
				//alert(selectedRows.toSource()); // print array
	
				var data = {
					action: 'delete_listGroup',
					s_selectedRows : selectedRows,
					s_props:propertyID
		
				};
				ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
				jQuery.post(ajaxurl, data, function(response) {
					alert(response);
					//$('.my-element').fadeIn();
					$('#responceDiv').show();
					$('#responceDiv').html(response);

					$('#responceDiv').fadeOut(5000, function() {
						ListPages('list_name','ASC',0,'0.5','1');
					});
				});
			}
		} else { alert("You didn't choose any of the checkboxes!");}
	}

//<<---------------delete lists<<<<<<<<<<<<<<<<< //sujith dec 10 >> 
	
		function Popup(){

			$("#pop").load().dialog({
			height: 500,
			width: 600,
			title: 'Edit List',
			modal:true,
			close: function(event,ui){
			$("pop").dialog('destroy');
			
				}
			});	
		}

		function PopupAddPage(){

			$("#pop").load().dialog({
			height: 500,
			width: 600,
			title:'Import List',
			modal:true,
			close: function(event,ui){
			$("pop").dialog('destroy');
			
				}
			});	
		}

	// EDIT List
	function edit_msg_board(rowId,prop) {
	//propertyID =$("#cProperties").val();
	var propertyID=prop;
		var data = {
			action: 'admin_campaigns_listedit',
			id : rowId,
			s_props:propertyID
		};
			jQuery.post(ajaxurl, data, function(response) {
				$('#editForm').html(response);
				$("#editDialog").load().dialog({
					closeOnEscape: true, 
					height: 450,
					width: 600,
					title:'Edit List',
					modal:true,
					close: function(event,ui){
						$("#editDialog").dialog('destroy');
					}
				});
		
			});
	}

	function download_list(id,prop){

// 		alert("Please download the list");
                //sprops =$("#cProperties").val();
                sprops=prop;
		window.open("<?php echo site_url(); ?>/external/csv_download.php?codein=NxYjL&idi="+id+"&sprops="+sprops,"mywindow");
		return false;
	}
// DELETE LIST
	function delete_list(rowId,prop){
		//alert(rowId);
		//propertyID =$("#cProperties").val();
		var propertyID=prop;
		var ListDataDel = {Lists:{			
			id : rowId,
		}};

		var data = {
			id : rowId,
			action: 'admin_campaigns_deletelist', /* [controller]_[function name] */
			data : ListDataDel,
			s_props : propertyID
		};

		var v=confirm("Are you really want to delete this List & Related contacts?");
		if(v){
			$('#row_'+rowId).hide();
			jQuery.post(ajaxurl, data, function(response) {
			$('#row_'+rowId).hide();
				
//  				window.location.href = "<?php //echo bloginfo('wpurl');?>/wp-admin/admin.php?page=mvc_campaigns-lists";
				ListPages('list_name','ASC',0,'0.5','1');
			});
		}
	}
		
	function showIt(imgId,classid){
		$('#row_'+imgId).css("background-color", '#DCEBFE');
		$('#editImg-'+imgId).attr('src', '<?php echo $pluginImagesDir."edit-hover.png"; ?>');
		$('#dropImg-'+imgId).attr('src', '<?php echo $pluginImagesDir."delete-hover.png"; ?>');
		$('#download-'+imgId).attr('src', '<?php echo $pluginImagesDir."download-hr.png"; ?>');
	}
	
	function hideIt(imgId,classid){
		$('#row_'+imgId).css("background-color", classid);
		$('#editImg-'+imgId).attr('src', '<?php echo $pluginImagesDir."edit.png"; ?>');
		$('#dropImg-'+imgId).attr('src', '<?php echo $pluginImagesDir."delete.png"; ?>');
		$('#download-'+imgId).attr('src', '<?php echo $pluginImagesDir."download.png"; ?>');
	}

	


	</script>
<style type="text/css">

.dataTables_length {
    /*margin: 2px 0px 0px 1px;*/
    }
/*
    height: 30px;
    margin-left: 10px;
    width: 70%;
}*/
#row_length{
height:23px;
margin-top:3px;
}
#example_length img { position:relative; top:4px; }
#example_filter { margin-right:5px;margin-top: 5px; }
 #message { 
	opacity:0;
}
#searchList{ width:110px !important;}
#datepicker{ width:110px; }

#ui-datepicker-div {
    font-size: 12px !important;
    font-weight: normal !important;
}
/* #messageErr { */
    /*background: url("../../../css/images/24-message-info.png") no-repeat scroll 7px 7px #DDFFDD;*/
	/*background: #FF9EA5;
    border: 1px solid #CCCC66;
	font-size:12px; font-weight:normal;
    line-height: 1.2em;
    margin: 10px 0;
    padding: 10px 10px 10px 35px;
z-index:-1;
opacity:0;
}*/

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
   /* height: 10px; */
    visibility: hidden;
}
.ui-datepicker-week-end{color:red;}
.ui-datepicker-calendar thead:nth-child(1){background-image:url(<?php echo $pluginImagesDir.'grdnt.png';?>);}
#searchList {
    height: 21px;
    margin-top: 7px;
    width: 145px !important;
}

 .ui-datepicker {
    padding: 0.2em 0.2em 0;
    width: 17em;
 display:none;
}

.ui-dialog .ui-dialog-titlebar-close span {
    display: block;
    margin: 1px;
background-image:url(<?php echo $pluginImagesDir.'close.png';?>);
}
.ui-dialog .ui-dialog-titlebar-close .ui-state-hover{
	background: #E9F4F5; border:0; 
	background-position:left;
	background-image:url(<?php echo $pluginImagesDir.'close.png';?>);
	height: 16px;
	width: 16px;
	margin: -10px -3px 0 !important;
}
.ui-dialog .ui-dialog-titlebar-close:hover {
    background: url("http://somdev1.us/wp-content/plugins/campaign-manager/images/close.png") repeat scroll 0 0 #E9F4F5 !important;
    border: 0 none !important;
    height: 16px !important;
    margin: -10px 2px 0 !important;
    position: absolute !important;
    width: 16px !important;
    z-index: 1 !important;
}
</style>
	</head>
<?php

	///Creating Property Dropdown
	foreach($properties as $property):
		$propertiesArr[$property->id] = $property->property_name;
	endforeach;
?>	
<div id="index" class="grid_2_3">
<div id="editDialog" style="display:none">	
		<input type="hidden" id="hidEditId" name="hidEditId" value="">
		<span id="editForm"></span>	
</div>
<div id="fw_container">
	<h3>List</h3>
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
			<div class="fg-toolbar ui-toolbar ui-widget-header ui-corner-tl ui-corner-tr ui-helper-clearfix">
				<div id="example_length" class="dataTables_length">
					<label>Show</label>
					<select id="row_length" name="row_length" size="1" aria-controls="example">
						<option value="5">5</option>
						<option value="10">10</option>
						<option value="25">25</option>
						<option value="50">50</option>
						<option value="100">100</option>
					</select> entries.&nbsp;&nbsp;
					<span id="dpic">
  					<input type="text" id="datepicker" onchange="ListPages('date','ASC','sort')"></span>
					<img alt="print" id="printButton" src="<?php echo $pluginImagesDir;?>print.png" >&nbsp;&nbsp;&nbsp;
					<img alt="add" id="customAddBtn" src="<?php echo $pluginImagesDir;?>add.png" > &nbsp;&nbsp;
					
				<?php
	if(is_super_admin()){
		echo $this->form->belongs_to_dropdown_nodiv('Property', $propertiesArr, array('empty' => 'Select Property','id'=>'cProperties','name'=>'cProperties'));
	}else{
	global $blog_id;
	echo '<input type="hidden" id="cProperties" value="'.$blog_id.'" />';
	}					
?>
				<div class="dataTables_filter" id="example_filter" style="margin-top: 0px;"><label>
				<input placeholder="Search" type="text" style="margin-top: 5px;" aria-controls="example" id="searchList" onkeyup="ListPages('list_name','searchList','s_search')"></label>
				</div>
			</div></div>
			<table border="0" cellspacing="0" cellpadding="0" text-align: left;" id="example" class="display dataTable" aria-describedby="example_info"  >
			<thead>
				<tr role="row">
				<th id="colHead" aria-label="Rendering engine: activate to sort column descending" aria-sort="ascending" style="width: 25px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper" ><input name="sdf" type="checkbox" id="selectall"/></div></th>
        			<th id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" style="width: 504px;" rowspan="1" colspan="1"  aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending"><!--style="width: 310px;"-->
				<div class="DataTables_sort_wrapper"  onClick="ListPages('list_name','DESC','sort');">Name</div>
				</th>
        <th id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1" style="width: 269px;"  aria-label="Platform(s): activate to sort column ascending"><!--style="width: 85px;"-->
		<div class="DataTables_sort_wrapper" onClick="ListPages('date','ASC','sort');">Date</div></th>
<th id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" style="width: 504px;" rowspan="1" colspan="1"  aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending"><!--style="width: 310px;"-->
	<div class="DataTables_sort_wrapper"  onClick="ListPages('list_name','DESC','sort');">Category</div>
				</th>
        <th id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1" style="width: 269px;"  aria-label="Engine version: activate to sort column ascending"><!--style="width: 75px;"-->
		<div class="DataTables_sort_wrapper">Total Contacts</div></th>
        <th id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1" style="width: 294px;"  aria-label="CSS grade: activate to sort column ascending"><!--style="width: 67px;"-->
		<div class="DataTables_sort_wrapper">Action</div></th></tr>
	</thead>
		
<tbody role="alert" aria-live="polite" aria-relevant="all">
		<?php
 			$class = 'odd';
		if(count($objects) > 0){			
			$a = 1;
			for($i=0;$i<count($objects);$i++){
			//$cnt = $a%2; $class = ($cnt) ? 'odd' : 'even'; $a++; // Alternate row color

				$cnt = $a%2; //$class = ($cnt) ? 'odd' : 'even'; $a++; // Alternate row color
				if($cnt) {
					$class = 'odd';
					$rcolor = '#F0F0F0';
				} else { 
					$class = 'even';
					$rcolor = '#FFFFFF';
				}
			$a++; 
		?>
		<tr onmouseover="showIt('<?php echo $objects[$i]->id; ?>','<?php echo $rcolor;?>')" onmouseout="hideIt('<?php echo $objects[$i]->id; ?>','<?php echo $rcolor;?>')" class="gradeA <?php echo $class; ?>" id="row_<?php echo $objects[$i]->id; ?>">
		<td><input type="checkbox" class="case" name="case" value="<?php echo $objects[$i]->id; ?>"></td>
			<td style="width:290px;"><?php echo $objects[$i]->list_name; ?></td>
			<td class=" " style="width:85px;"><?php echo $objects[$i]->date; ?></td>
			<?php
			 $prefix=$wpdb->prefix;
	 $resCat=mysql_query("SELECT category_name FROM {$prefix}categories WHERE id='".$objects[$i]->categoryId."'"); 
	 $rowcat=mysql_fetch_array($resCat);
	 ?>
			<td style="width:290px;"><?php echo $rowcat['category_name']; ?></td>
			<td class=" " style="width:75px;"><?php echo $objects[$i]->num_contacts; ?></td>
			<td>
				<!-- onClick="editThisCampaign('<?php echo $objects[$i]->id; ?>')" -->
			<img id="editImg-<?php echo $objects[$i]->id; ?>" alt="edit" class="editButton" onClick="edit_msg_board(<?php echo $objects[$i]->id; ?>);" src="<?php echo $pluginImagesDir;?>edit.png" />
			<img src="<?php echo $pluginImagesDir.'seperator-18.png'; ?>">
			<img id="dropImg-<?php echo $objects[$i]->id; ?>"  alt="delete" id="dropButton" onClick="delete_list(<?php echo $objects[$i]->id; ?>);" src="<?php echo $pluginImagesDir;?>delete.png" />
			<img src="<?php echo $pluginImagesDir.'seperator-18.png'; ?>">
			<img id="download-<?php echo $objects[$i]->id; ?>" alt="download csv" id="dropButton" onClick="download_list(<?php echo $objects[$i]->id; ?>);" src="<?php echo $pluginImagesDir;?>download.png" height="18" width="18" /></td>
		</tr>
		<?php } }else{ ?>
			<tr class="gradeA <?php echo $class; ?>">
					<td class="center" colspan="6" height="50"><b>No lists available</b><br/></td></tr>
		<?php } ?>
		</tbody>
		<thead>
			<tr role="row">
				<!--<th class="ui-state-default" colspan="3"><?php  echo 'Showing 1 to '.$rowPerpage.' of '.$totalresult.' entries '; ?>Showing 1 to 10 of 18 entries &nbsp; </th>-->
				<td colspan="2" align="left">
					<img id="dropImg" src="'<?php echo $pluginImagesDir; ?>arrow_ltr.png'" width="38" height="22" style=" margin-left: 0.3em; margin-right: 0.3em;" />
					<img id="dropImg" onClick="delete_list_group('delSelected');" id="dropButton" src="'<?php echo $pluginImagesDir; ?>delete.png'" /> 
				</td>
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
							if($start > 0) { //if($num_of_pages > $rowPerpage){
								
								$prevPg = $start-$rowPerpage;
								$prevButton .= '<a class="fg-button ui-button ui-state-default" style="width:33px; padding: 5px 5px; font-weight:bold;" onclick="ListPages(0,0,0,\''.$prevPg.'.'.$rowPerpage.'\');">Prev</a>';
							}

							if($i == 1){ $nextI = 0;} else { $nextI = ($i-1)*$rowPerpage; }
							$pageButtons .= '<a class="fg-button ui-button ui-state-default" onclick="ListPages(0,0,0,\''.$nextI.'.'.$rowPerpage.'\',\''.$i.'\');" id="'.$i.'"';
							if($i == 1){ $pageButtons .= 'style="color:#000000 !important;font-weight:bold; padding: 5px 5px;"'; } else { $pageButtons .= ' style="padding: 5px 5px; "'; }
							$pageButtons .= '>';
							$pageButtons .= $i.'</a>';
						}
						$pagination = $prevButton;
						$pagination .= $pageButtons;

						if(($start+$showNum) < $total) {
							$nextPg = $start+$rowPerpage;
							//if($nextPg < $total) {
								$pagination .= '<a class="fg-button ui-button ui-state-default" style="width:33px; padding: 5px 5px; font-weight:bold;" onclick="ListPages(0,0,0,\''.$nextPg.'.'.$rowPerpage.'\',2);">Next</a>';
							//}
						}

					echo $pagination;
					
			?>
					</span>
				</th>
			</tr>
		</thead>
</table>
<!--<div class="fg-toolbar ui-toolbar ui-widget-header ui-corner-bl ui-corner-br ui-helper-clearfix">
		<div class="dataTables_info" id="example_info">&nbsp;</div>
			
		<div style="display:none" class="dataTables_paginate fg-buttonset ui-buttonset fg-buttonset-multi ui-buttonset-multi paging_full_numbers" id="example_paginate"><a class="first ui-corner-tl ui-corner-bl fg-button ui-button ui-state-default ui-state-disabled" tabindex="0" id="example_first">First</a><a class="previous fg-button ui-button ui-state-default ui-state-disabled" tabindex="0" id="example_previous">Previous</a><span><a class="fg-button ui-button ui-state-default ui-state-disabled" tabindex="0">1</a><a class="fg-button ui-button ui-state-default" tabindex="0">2</a><a class="fg-button ui-button ui-state-default" tabindex="0">3</a><a class="fg-button ui-button ui-state-default" tabindex="0">4</a><a class="fg-button ui-button ui-state-default" tabindex="0">5</a></span><a class="next fg-button ui-button ui-state-default" tabindex="0" id="example_next">Next</a><a class="last ui-corner-tr ui-corner-br fg-button ui-button ui-state-default" tabindex="0" id="example_last">Last</a></div>
</div>-->
</div>
<div id="pop"  style="display:none;">
<?php
	$this->render_view('_add_lists', array('locals' => array($model)));
?>
</div>
</div>
</div>
</div>
</body>
</html>	
