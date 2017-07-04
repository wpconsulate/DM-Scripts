<?php 

	$url = plugins_url()."/campaign-manager/app/public/DataTables/";
	$pluginJsurl = plugins_url()."/campaign-manager/app/public/js/";
	$pluginImagesDir = plugins_url().'/campaign-manager/images/';
	$pluginCSSDir = plugins_url().'/campaign-manager/css/';
	$incAbsPath = ABSPATH."wp-content/plugins/campaign-manager/app/public/DataTables/extras/Editor/";
        $blogurl=home_url()."/";
?>
<head>
		<link rel='stylesheet' id='colors-css'  href="<?php echo $pluginCSSDir; ?>newstyle.css" type='text/css'  />
<style type="text/css" title="currentStyle">

			@import "<?php echo $url; ?>media/css/demo_page.css";
			@import "<?php echo $url; ?>media/css/jquery.dataTables.css";
			@import "<?php echo $url; ?>extras/TableTools/media/css/TableTools.css";
			@import "<?php echo $url; ?>extras/Editor/media/css/dataTables.editor.css";
			@import "<?php echo $pluginCSSDir; ?>template.css";  <!-- Do not change -->
			@import "<?php echo $pluginCSSDir; ?>newstyle.css";
			@import "<?php echo $pluginCSSDir; ?>jquery-ui.css";  <!-- Do not change -->

		</style>
		  
		<script type="text/javascript" language="javascript" src="<?php echo $url; ?>media/js/jquery.js"></script>
		<script type="text/javascript" language="javascript" src="<?php echo $url; ?>media/js/jquery.dataTables.js"></script>
		<script type="text/javascript" charset="utf-8" src="<?php echo $url; ?>extras/TableTools/media/js/TableTools.js"></script>
		<script type="text/javascript" charset="utf-8" src="<?php echo $url; ?>extras/Editor/media/js/dataTables.editor.js"></script>
		<script type="text/javascript" charset="utf-8" src="<?php echo $pluginJsurl; ?>jquery.printElement.js"></script>
		<!--<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>-->
		<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
		<script type="text/javascript" language="javascript" >
		$(document).ready(function() {
                        //category_pagination('id','ASC','','');
                        category_pagination(0,0,0,'0.5',1);
                        $("#row_length").live('change', function() {
                                var type=$("#row_length").val();
				category_pagination('id','ASC','','');
			});
			$("#cType").live('change', function() {
				category_pagination('id','cProperties','s_search');
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

		</script>
		<style>
/*			#wrapper{
		 margin-top: 10px;
		}
	
#example_wrapper {
    width: 784px !important;
     margin: auto;
}

#example_length{
float:left;
width:59%;
}
*/
.loadingDiv{
	height: 150px;
	left: 40%;
	position: absolute;
	top: 21%;
	width: 150px;
	z-index: 1000;
}
#example_filter{
margin-right:5px;
text-align:right;
}

#searchQ {
margin-top:2px !important;
}
/*
.dataTables_filter {
    width: 40% !important;
}

#example_length {
    width: 55% !important;
}
*/
table.display td{
font-size: 0.8em;
    padding: 3px 10px;
    
}
		</style>
		
</head>

<div id="fw_container">

<h3>Categories</h3>
<div id="responceDiv" style="display:none"></div>
<!-- Loader Div -->
	<?php $loaderPath = plugins_url().'/campaign-manager/images/ajax-loader.gif'; ?>
	<div style="display:none" class="loadingDiv"><img src='<?php echo $loaderPath; ?>'></div>
<!-- Loader Div ends here -->
<div class="full_width">
<div role="grid" class="dataTables_wrapper" id="example_wrapper">
	<div class="fg-toolbar ui-toolbar ui-widget-header ui-corner-tl ui-corner-tr ui-helper-clearfix" style="background-color: rgb(233, 245, 246) ! important;"><div id="example_length" class="dataTables_length">
	<label>Show 
	<select id="row_length" name="row_length" size="1" aria-controls="example">
		<option value="5">5</option>
		<option value="10">10</option>
		<option value="25">25</option>
		<option value="50">50</option>
		<option value="100">100</option>
	</select> entries.&nbsp;&nbsp;
<!-- 	<img id="customAddBtn" src="<?php echo $pluginImagesDir;?>add.png" height="16" width="16"> &nbsp;&nbsp;
 aria-controls="example" -->
<select name='type' id="cType">
<option value="0">Select type</option>
<option value="1">Campaign</option>
<option value="2">List</option>
</select>
</label> 
<div class="dataTables_filter" id="example_filter" align="center" >
	<label><input type="text" id="searchQ" placeholder="Search" aria-controls="example" onkeyup="category_pagination('category_name','searchQ','s_search')"></label>
	</div></div></div>



<table id="example" class="display dataTable" cellspacing="0" cellpadding="0" border="0" aria-describedby="example_info" style="width:100%">
</table>
<!--
		<div class="fg-toolbar ui-toolbar ui-widget-header ui-corner-bl ui-corner-br ui-helper-clearfix" >
			<div class="dataTables_info" id="example_info"><!--Showing 1 to 10 of 58 entries--6> &nbsp;</div>
			
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

			
