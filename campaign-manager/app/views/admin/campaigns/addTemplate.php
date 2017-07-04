<?php
if(!is_super_admin()){
	global $blog_id;
	echo '<div align="center" style="background: none repeat scroll 0 0 #FFCCCC;
		border: 1px solid #FF8080;
		font-size: 12px;
		line-height: 1.3em;
		margin-bottom: 22px;
		padding: 11px;width:80%"><b>You don\'t have permission to create or edit Templates but you can use templates for creating messages.</b>
		</div>';
	echo '<br>Redirecting....';
	echo '<meta http-equiv="refresh" content="1;url=http://somdev'.$blog_id.'.us/wp-admin/admin.php?page=mvc_campaigns-templates" />';
exit();
}
	$pluginImagesDir = plugins_url().'/campaign-manager/images/';

$urlview = get_site_url()."/external/";
$pluginPublicUrl = plugins_url()."/campaign-manager/app/public/";
$url = plugins_url()."/campaign-manager/app/public/DataTables/";
?>
<style>
#pop label {margin-right: 104px;vertical-align: top;}
#pop {font-size:12px; font-weight:normal;}
#content {height: 150px;margin-left: -38px;width: 575px;}
#header {height:200px;margin-left: -42px;width: 575px;}
#template_name {margin-bottom:15px;margin-top:15px;margin-left: 90px;width: 575px;}
#preview {height: 100px;width: 350px;margin-left: 20px;}
textarea {width: 650px;border: 1px solid #cccccc;padding: 5px;font-family: Tahoma, sans-serif;}
.prevButtonPos{clear: both;float: right;margin-bottom: 10px;margin-right: 60px;}
#fileUpDiv{margin-bottom:15px;margin-top:15px;clear:both;margin-bottom: 10px;}
#temp_thumb{margin-left:15px;}
.mceEditor{width:765px !important;height:480px !important;}
legend {border: 1px solid #CCCCCC;font-size: 17px !important;margin-left: 15px;text-align: left;}
fieldset{padding:5px;}
label{font-weight:bold;}
.ui-dialog-titlebar-close:hover {background-image:url("http://somdev1.us/wp-content/plugins/campaign-manager/images/close.png");
    background-repeat: no-repeat;display: block;}
.ui-dialog-titlebar-close  {background-image:url("http://somdev1.us/wp-content/plugins/campaign-manager/images/close.png");background-repeat: no-repeat;display: block;margin: 1px;}
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

form#form1 div:nth-child(even) {background: #F0F0F0;}
form#form1 div:nth-child(odd) {background: #FFF;}

</style>

<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
<script type="text/javascript">
	function Popup(divname,popTitle)
	{
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
	jQuery(document).ready(function(){
		jQuery('#save').click(function() {
			error = 0;
			if(jQuery('#template_name').val() == '') {
				error = 1;
				jQuery('#template_name').css("background-color",'#FFEDEF');
				jQuery('#error_add').html("Add Name For The Template");
				jQuery('#template_name').focus();
				return false;
			} else { jQuery('#template_name').css("background-color",'#fff'); }
			editor = tinymce.get('content');
			var data = editor.getContent();
			if(data == '') {
				error = 1;
				jQuery('#content').css("background-color",'#FFEDEF');
				jQuery('#error_add').html("Add Template");
				jQuery('#content').focus();
				return false;
			} else {
				jQuery('#content').css("background-color",'#fff'); 
			}
			if(error==0){
				jQuery('#error_add').html("");
				if (window.FormData) {
					formdata = new FormData();
				}
				img=jQuery('#temp_thumb').val();
				if(img!=""){
					document.getElementById("response").innerHTML = "Thumbnail Image Uploading . . .";
					//file = document.getElementById("temp_thumb").files[0];
					var elem = document.getElementById('temp_thumb'),
                                        file = elem.files && elem.files[0];
					//alert(file);
					if(file==undefined || !(file)   ){
				         alert("Your browser version doesnot support this file uploading.Please upgrade your browser");
					document.getElementById("response").innerHTML ="Your browser version doesnot support this.Please upgrade your browser";
					saveTemp();
					return true;
					}else{
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
							
								saveTemp();
							}
						});
					}
					}
				}
				else{
				
					saveTemp();
				}
			}
		});
		jQuery('#show_preview_btn1315').click(function() {
			error = 0;
			if(jQuery('#content').val() == '') {
				error = 1;
				jQuery('#content').css("background-color",'#FFEDEF');
				jQuery('#error_add').html("Add Template");
				jQuery('#content').focus();
				return false;
			} else { jQuery('#content').css("background-color",'#fff'); }
			if(error == 0){
				jQuery('#error_add').html("");
				createPreview();
				jQuery('#previewTemplate').show();
				Popup('previewTemplate','Template Preview');
			}
		});
		
			jQuery('#template_list').change( function() {
			var templistval = jQuery('#template_list').val();
			var propdropval = jQuery('#cProperties').val();
			loadTemplateData(templistval);
				
			
		});
	});
	

    </script>
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

	// Theme options
	theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,jbimages",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
});
</script>
    <div id="error_add" style="color:#FF0000;"></div>
<?php
	$options['file'] = 1; 
	$options['controller']='campaigns';
	$options['enctype']="multipart/form-data";
	echo $this->form->create('Templates',$options);
	?>
<?php 	$options['id'] = 'template_name';
	$options['class'] = 'padLeft';
	echo $this->form->input('template_name', $options); ?>
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
		<option value="">Select Template</option><option value="142">Event</option>
		<?php
		foreach($templatedropArr as $key=>$value){
		echo "<option value='$key'>$value</option>";
                }
                ?>
		</select>
		<span style="display:none" id="tempSelErr"></span>
		<br>
		</div> 
	
	<div><textarea id="content" name="content" class="mceEditor" style="" cols="40" rows="20"> </textarea></div>
<?php
	$options = array();
	$options['id'] = 'temp_thumb';
	$options['label'] = 'Upload Thumbnail image';
	$options['before'] = '<div id="fileUpDiv">';
	$options['after'] = '</div>';
	echo $this->form->file_input('temp_thumb',$options);
	
?>
<!--<input id="file" type="file" multiple />-->
<?php
	echo '<div><label>&nbsp;</label>
	<input type="button" value="Save Template" id="save" class="savebutton">
	<input type="button" name="show_preview_btn" class="savebutton" id="show_preview_btn1315" value="Show Preview">
	</div>';
	echo '</form>';
?>
<div id="show_preview"></div>
<br>
<div id="response" style="color:#006400;"></div>
<?php //} ?>
<div id="previewTemplate" style="display:none">Preview <hr>
	<div id="pevPlace" style="height:100%;height:100%; font-weight:normal;"></div>
</div>
