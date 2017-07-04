<?php
	global $blog_id;
	$pluginImagesDir = plugins_url().'/campaign-manager/images/';

if(is_super_admin()){
$urlview = get_site_url()."/external/";
$pluginPublicUrl = plugins_url()."/campaign-manager/app/public/";
?>
<style>
#editDialog label {
	margin-right: 104px;
	vertical-align: top;
}
#editDialog {
	font-size:12px; font-weight:normal;
}
label{ 
	font-weight:bold;
}
#edit_template_name{
	margin-bottom:15px;
	margin-top:15px;
	margin-left: 90px;
	width: 563px;
}
#content {
	height: 150px;
	margin-left: 90px;
	width: 575px;
}
.mceEditor{
	width:760px !important;
	height:480px !important;
}
legend {
	border: 1px solid #CCCCCC;
	font-size: 17px !important;
	margin-left: 15px;
	text-align: left;
}
.edit_prevButtonPos{
	clear: both;
	float: right;
	margin-bottom: 10px;
	margin-right: 35px;
}
#preview_image{
	clear: both;
	float: left;
	margin-bottom: -5px;
	margin-left: 5px;
}
#temp_thumb_edit{
	margin-bottom:15px;
	margin-top:15px;
}
.ui-dialog-titlebar-close:hover {
	background-image: url("http://hillplaceapts.com/wp-content/plugins/campaign-manager/images/close.png");
	background-repeat: no-repeat;
	display: block;
}
.ui-dialog-titlebar-close  {
	background-image: url("http://hillplaceapts.com/wp-content/plugins/campaign-manager/images/close.png");
	background-repeat: no-repeat;
	display: block;
	margin: 1px;
}
#show_preview_btn1315{
    background: url(<?php echo $pluginImagesDir.'button-bg.png';?>) no-repeat scroll 0 0 transparent;
    border: medium none;
    font-family: Verdana,Arial,sans-serif;
    font-size: 12px;
    height: 32px;
    padding-bottom: 5px;
    padding-top: 5px;
    width: 190px;
}
#updateButton{
    background: url(<?php echo $pluginImagesDir.'button-bg.png';?>) no-repeat scroll 0 0 transparent;
    border: medium none;
    font-family: Verdana,Arial,sans-serif;
    font-size: 12px;
    height: 32px;
    padding-bottom: 5px;
    padding-top: 5px;
    width: 190px;
}
</style>

<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#updateButton').click(function() {
	editTemp();
	});
	jQuery('#show_preview_btn1315').click(function() {
	var errorEdit=0;
	editor = tinymce.get('content');
	var data = editor.getContent();
		if(data == '') {
			errorEdit = 1;
			jQuery('#content').css("background-color",'#FFEDEF');
			jQuery('#error').html("Add Template");
			jQuery('#content').focus();
			return false;
		} else { jQuery('#content').css("background-color",'#fff'); }
		if(errorEdit==0){
			jQuery('#error').html("");
			createEditPreview();
			jQuery('#previewTemplate').show();
			Popup('previewTemplate');
		}
	});
	
			jQuery('#template_list').change( function() {
			var templistval = jQuery('#template_list').val();
			var propdropval = jQuery('#cProperties').val();
			//loadTemplateData(templistval);
			loadTemplateDataNotrans(templistval); //No translation
				
			
		});
});
function Popup(divname,popTitle){
	jQuery('#'+divname).load().dialog({
	height: 600,
	width: 800,
	title: popTitle,
	modal:true,
	close: function(event,ui){
		jQuery('#'+divname).dialog('destroy');
	}
	});
}
function createEditPreview(){
	temp_footer = editor.getContent();
	var data = {
		action: 'createTempPreview',
		s_temp_footer : temp_footer,
	};
	ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
	jQuery.post(ajaxurl, data, function(response) {
		jQuery('#pevPlace').html(response);
	});
}
function editTemp(){
	var errorEdit = 0;
	var TempName=jQuery('#edit_template_name').val();
	editor = tinymce.get('content');
	var TempFooter=editor.getContent();
	var Tempid=jQuery('#edit_id').val();
	if(TempName == ''){
		jQuery('#error').html("Enter a name for the template.");
		jQuery('#edit_template_name').css("background-color",'#FFEDEF');
		jQuery('#edit_template_name').focus();
		errorEdit = 1;
		return false;
	}else {
		jQuery('#edit_template_name').css("background-color",'#FFF');
	}
	if(jQuery('#content').val() == '') {
		errorEdit = 1;
		jQuery('#content').css("background-color",'#FFEDEF');
		jQuery('#error').html("Add Footer For The Template");
		jQuery('#content').focus();
		return false;
	} else { jQuery('#content').css("background-color",'#fff'); }
	if(errorEdit==0){
		jQuery('#error').html("");
		if (window.FormData) {
			formdata = new FormData();
		}
		img=jQuery('#temp_thumb_edit').val();
		if(img!=""){
		file = document.getElementById("temp_thumb_edit").files[0];
			jQuery('#update').html("Uploading.....");
			if ( window.FileReader ) {
				reader = new FileReader();
				reader.onloadend = function (e) {
				};
				reader.readAsDataURL(file);
			}
			if (formdata) {
				formdata.append("thumb_nail[]", file);
			}
			if (formdata) {
				jQuery.ajax({
				
					url: "<?php echo $urlview?>tempPreview_upload.php?code=NxYjL",
					type: "POST",
					data: formdata,
					processData: false,
					contentType: false,
					async:false,
					success: function (res) {
						jQuery('#update').html("Updating.....");
						var data = {
							action: 'editTemplate',
							Name : TempName,
							Footer : TempFooter,
							id : Tempid,
							Tempimg: img
						};
						ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
					
						jQuery.post(ajaxurl, data, function(response) {
							window.location.href = "<?php echo bloginfo('wpurl');?>/wp-admin/admin.php?page=mvc_campaigns-templates";
						});
					}
				});
			}
		}
		else{
			jQuery('#update').html("Updating.....");
			var data = {
				action: 'editTemplate',
				Name : TempName,
				Footer : TempFooter,
				id : Tempid,
				Tempimg: img
			};
			ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";
		
			jQuery.post(ajaxurl, data, function(response) {
				window.location.href = "<?php echo bloginfo('wpurl');?>/wp-admin/admin.php?page=mvc_campaigns-templates";
			});
		}
	}
}
</script>
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $pluginPublicUrl; ?>tinymce/jscripts/tiny_mce/tiny_mce.js"></script> 
<script type="text/javascript" language="javascript" src="<?php echo $pluginPublicUrl; ?>tinymce/jscripts/tiny_mce/langs/en.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $pluginPublicUrl; ?>tinymce/jscripts/tiny_mce/themes/advanced/editor_template.js"></script>
<script type="text/javascript">
tinyMCE.init({
	editor_selector : "mceEditor",
	mode : "specific_textareas",
	editor_selector : "mceEditor",
	theme : "advanced",
	plugins : "jbimages,pagebreak,style,layer,table,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
	theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,jbimages",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
});
</script>
<?php
	$options['onfocus'] = 'alertthis()';
?>
<?php echo $this->form->create('Templates',$options); ?>
<?php
	$options = array(
		'value' => $Templates->template_name,
		'id' => 'edit_template_name',
		'class' => 'padLeft'
		);
	echo $this->form->input('template_name', $options); 
?>
<?php
	/// This code will replace images/imgname.ext into relative path to the template image dir.
	///Images for templates are located @ wp-content/plugins/campaign-manager/images/template_images/
	//$templateImagePath = $pluginImagesDir.'template_images/';
	//$Templates_footer = str_replace("{template_image_path}", $templateImagePath, $Templates->footer);
	//$domain = "somdev".$blog_id.".us";
	//$Templates_footer = str_replace("{domain}", $domain, $Templates_footer);
	/*$Templates->footer*/
        $domainUrl = get_site_url();
	$Templates_footer = $Templates->footer; // str_replace("{domain}", $domainUrl, $Templates->footer);
	
?>
<?php
	global $blog_id; 
	foreach($templatedrops as $templatedrop):
	$templatedropArr[$templatedrop->id] = $templatedrop->template_name;
	endforeach;
	//print_r($templatedropArr);
	?>
<input id="cProperties" value="<?php echo $blog_id;?>" type="hidden">	
		<div id="templateDiv"><label for="template_list">Load Template</label>
		<select name="template" class="padLeft" id="template_list" style="margin-left: 95px;">
		<option value="">Select Template</option>
		<?php
		foreach($templatedropArr as $key=>$value){
		echo "<option value='$key'>$value</option>";
                }
                ?>
		</select>
		<span style="display:none" id="tempSelErr"></span>
		<br>
		</div> 
<textarea id="content" name="content" class="mceEditor"><?php echo $Templates_footer;?></textarea>
<?php
	echo '<div><table><tr><td style="padding-left: 10px;  padding-top: 10px;">';
	if($Templates->previewImgName!=""||$Templates->previewImgName!=NULL){
		$loaderPath = plugins_url().'/campaign-manager/images/templates/thumb/'.$Templates->previewImgName;
		echo '<img src="'. $loaderPath.'" height="50" width="50" alt="preview image"/>';
	}
	echo '</td><td>';
	$options = array();
	$options['id'] = 'temp_thumb_edit';
	$options['label'] = 'Change Thumbnail image';
	$options['before'] = '<span id="fileUpDiv">';
	$options['after'] = '</span>';
	echo $this->form->file_input('temp_thumb_edit',$options);
	echo '</td></tr></table>
		<input type="hidden" value="'.$Templates->id.'" name="data[Templates][id]" id="edit_id">
	</div>';
	echo '<div style="background: none repeat scroll 0 0 transparent; margin-top:5px;"><label>&nbsp;</label>
			<input type="button" class="savebutton" id="updateButton" name="Update" value="Update">
			<input type="button" class="savebutton" id="show_preview_btn1315" name="show_preview_btn" value="Show Preview">
		</div>';
	echo '</form>';
?>
<div id="error" style="color:#FF0000;"></div>
<div id="update" style="color:#006400;"></div>
<?php }?>
<div id="previewTemplate" style="display:none">Preview <hr>
	<div id="pevPlace" style="height:100%;height:100%; font-weight:normal;"></div>
</div>
