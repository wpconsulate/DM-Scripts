<?php 

$taleName = 'wp_2_campaigns';
$parts = explode('_',$taleName);
//print_r($parts);

	$pluginPublicUrl = plugins_url()."/campaign-manager/app/public/";
	$url = plugins_url()."/campaign-manager/app/public/DataTables/";
	$pluginJsurl = plugins_url()."/campaign-manager/app/public/js/";
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
		<style type="text/css" title="currentStyle">

			@import "<?php echo $url; ?>media/css/demo_page.css";
			@import "<?php echo $url; ?>media/css/jquery.dataTables.css";
			@import "<?php echo $url; ?>extras/TableTools/media/css/TableTools.css";
			@import "<?php echo $url; ?>extras/Editor/media/css/dataTables.editor.css";


			@import "<?php echo $pluginCSSDir; ?>template.css";  <!-- Do not change -->
		#campaignID,#cProperties {
                        height: 25px;
                        width: 190px;
                }
                #message_name{
                        width: 285px;
                        height:25px;
                }
                #datepicker20,#template_list,#addCampaignpop{
                        width:188px;
                        height:25px;
                }
		</style>
		  
		<script type="text/javascript" language="javascript" src="<?php echo $url; ?>media/js/jquery.js"></script>
		<script type="text/javascript" language="javascript" src="<?php echo $url; ?>media/js/jquery.dataTables.js"></script>

		<script type="text/javascript" charset="utf-8" src="<?php echo $url; ?>extras/TableTools/media/js/TableTools.js"></script>
		<script type="text/javascript" charset="utf-8" src="<?php echo $url; ?>extras/Editor/media/js/dataTables.editor.js"></script>

		<script type="text/javascript" charset="utf-8" src="<?php echo $pluginJsurl; ?>jquery.printElement.js"></script>

<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
<!--  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
-->  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $pluginPublicUrl; ?>tinymce/jscripts/tiny_mce/tiny_mce.js"></script>

<script>
	function selecttemp(id){
		loadTemplateData(id);
		$("#templateLoader").dialog('destroy');
	}
</script>

<!--<h2>Add Message</h2>-->

<?php
	//echo "TempSuji:".print_r($templateList);
	//Formattimg rop downs
	foreach($campaignsList as $campaignsl):
		$campaignslArr[$campaignsl->id] = $campaignsl->campaign_name;
	endforeach;
	foreach($templatedrops as $templatedrop):
		$templatedropArr[$templatedrop->id] = $templatedrop->template_name;
	endforeach;

	foreach($properties as $property):
		$propertiesArr[$property->id] = $property->property_name;
	endforeach;

	foreach($templateList as $tempList):
		//echo '<pre>';print_r($tempList); echo '</pre>';
		$tempListArr[$tempList->id] = array("id"=>$tempList->id,'tname'=>$tempList->template_name, 'thumb'=>$tempList->previewImgName);
	endforeach;

//echo 'Templates: '. count($tempListArr); echo '<pre>';print_r($tempListArr); echo '</pre>'; //[1] => testTemplate1
?>
<?php 
	$pluginImagesDir = plugins_url().'/campaign-manager/images/';

?>
<div style="background-color: #E9F5F6; padding: 5px 5px 5px 10px; height: 30px; font-weight: bolder;">Add Message</div>

<style>
.wrap {
    font-size: 12px !important;
}
#pop label {
	margin-right: 104px;/*104px;*/
}
#datepicker { width:110px; }
#pop {
	font-size:12px; font-weight:normal;
}
#list {
	height: 100px;
   /* margin-left: 200px;*/
    width: 350px;
}
.padLeft{
}
#message_name{
margin-left: 88px; width:350px;
}
#campaignID {
margin-left: 38px;
}

#template_list {
margin-left: 44px;
}
#datepicker20{
margin-left:68px;
}
#CreateCat{
  	height: 22px;
	margin-left: -38px;
	margin-right: 10px;
}

#previewMailId{
	height: 25px;
    margin-left: 10px;
    width: 275px;
}
#openTemplateBtn{ /* Toposition folder image*/
	 left: 6px;
    position: relative;
    top: 8px;
}
.info, .success, .warning, .error, .validation {
background-position: 10px center;
    background-repeat: no-repeat;
    border: 1px solid;
    margin-left: 5px;
    padding: 5px 5px 5px 10px;
}
.error {
    color: #D8000C;
    background-color: #FFBABA;
    background-image: url('error.png');
}

#statmsg{	
	background-color: #F1F1F1;
	margin-left: 5px;
	padding: 5px;
}
#ui-datepicker-div{font-size:12px; }
</style>

<script type="text/javascript">

			$(function() {
				$( "#datepicker20" ).datepicker({
					showOn: "button",
					buttonImage: "<?php echo $pluginImagesDir;?>calender.png",
					buttonImageOnly: true,dateFormat: 'yy-mm-dd',
					minDate: 0,
				});
			});

function initTinyMce(){

	tinyMCE.init({
		// General options
		/*mode : "textareas",*/
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


}
initTinyMce(); //********************* Do not remove dis line 
//alert(tinyMCE.getInstanceById('mceEditor'));

function myCustomSetupContent(editor_id, body, doc) {
        body.innerHTML = "my new content" + body.innerHTML;
}

function initTinyMceNew(){
 	var editor = tinymce.get('content');
	var content = editor.getContent();
	content =  'http://mydomain.com';
	editor.setContent(content);
}
</script>


<script>
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
function validateEmail(email) {
	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\ ".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA -Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(email);
}
	$(document).ready(function() {
		jQuery('#showEmailPreview').click(function() {
		var errorEdit=0;
		editor = tinymce.get('content'); //content edit_temp_footer
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
				setTimeout(function() {
					$('#statmsg').fadeOut('slow');
				}, 3000);			
		});

		$('#tempSelErr').click( function() {
			$('#tempSelErr').hide();
		});
		
		// //$('#openTemplateBtn').click( function() {
		$('#template_list').change( function() {
			var templistval = $('#template_list').val();
			var propdropval = $('#cProperties').val();
			if(propdropval == 0 || propdropval == null){
				$('#cProperties').css("background-color",'#FFEDEF');
				$('#errspan').html('Please Select any property');
				$('#errspan').show();
				$('#errspan').hide(3000, function () {
				});
				$('#template_list').val('');
			} else {
			        $('#cProperties').css("background-color",'#FFF');
				if(templistval == 0 || templistval == null){
					$('#tempSelErr').html('Please Select any template');
					$('#tempSelErr').addClass('error');
					$('#tempSelErr').show();
				} else {
					$('#tempSelErr').hide();
					//initTinyMceNew();
					loadTemplateData(templistval);
				}
			}
		});


		//Save message:
		$("#addCampaignpop").click( function() {

			var error = 0;
			if($("#message_name").val() == ''){
				$('#message_name').css("background-color",'#FFEDEF');
				error = 1;
			} else { $('#message_name').css("background-color",'#FFF'); }
			if($("#datepicker20").val() == ''){
				$('#datepicker20').css("background-color",'#FFEDEF');
				error = 1;
			} else { $('#datepicker20').css("background-color",'#FFF'); }
	
			if($("#campaignID").val() == 0){
				$('#campaignID').css("background-color",'#FFEDEF');
				error = 1;
			} else { $('#campaignID').css("background-color",'#FFF'); }
			if($("#template_list").val() == 0){
				$('#template_list').css("background-color",'#FFEDEF');
				error = 1;
			} else { $('#template_list').css("background-color",'#FFF');}
			if(error == 1){
				$('#addCampaignpop').css("border", '2px solid #FFBABA');
				$('#addCampaignpop').css("color", '#FF0000');
			}

			if(error == 0){
				$('#addCampaignpop').css("border", '1px solid #BBBBBB');
				$('#addCampaignpop').css("color", '#464646');

				saveaddMessage();
			}
			return false;
		});


		// Send Preview Email:
		// sendPreviewEmail previewMailId statmsg
		$("#sendPreviewEmail").click( function() {
			if($("#previewMailId").val() == ''){
				$('#statmsg').css("background-color",'#FFBABA');
				$('#statmsg').html('Email ID is required!');
			} else if (!validateEmail($("#previewMailId").val())) {
				$('#statmsg').html('Invalid Email ID'); 
				$('#statmsg').css("background-color",'#FFBABA');
			}else{ $('#statmsg').html('');  $('#statmsg').css("background-color",'#fff'); 
				sendPreviewMessage();
			}
			setTimeout(function() {
				$('#statmsg').fadeOut('fast');
			}, 3000);			
		});

		$("#openTemplateBtn").click( function() {
			var propdropval = $('#cProperties').val();
			if(propdropval == 0 || propdropval == null){
				$('#cProperties').css("background-color",'#FFEDEF');
				$('#errspan').html('Please Select any property');
				$('#errspan').show();
				$('#errspan').hide(3000, function () {
				});
				$('#template_list').val('');
			} else {
				$('#cProperties').css("background-color",'#FFF');
			        $("#templateLoader").load().dialog({
				        height: 460,
				        width: 628,
				        modal:true,
				        close: function(event,ui){
					        $("templateLoader").dialog('destroy');
				        }
			        });
                        }
		});
		$("#cProperties").change( function() {
			
			loadCampaigns($("#cProperties").val());
		});
	});


</script>
<style>
#cProperties{ margin-left:88px;}

#errspan{
	background-color: #FFEDEF;
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
	width: 450px;
}
</style>

<div id="errspan"></div>

<?php

	// echo is_admin(); echo is_super_admin();

	$options['action'] = 'mvc_campaigns-addmessage';
	$options['id'] = 'addForm';
	echo $this->form->create('Message',$options); 
?>

<?php 
	//Proprty select option for super admin

	if ( is_super_admin() ) {
	echo '<div style="background-color: #FFF; padding: 5px; height: 30px;">';
		echo $this->form->belongs_to_dropdown('Property', $propertiesArr, array('empty' => 'Select Property','id'=>'cProperties','name'=>'cProperties'));
		
		echo "</div>";
	}else{
	global $blog_id;
	
	echo "<input id='cProperties' type='hidden' value='$blog_id'>";
	}
	global $blog_id;
	
	echo "<input id='cProperties2' type='hidden' value='$blog_id'>";
	
	$options['options'] = $campaignslArr;
	$options['label'] = 'Select Campaign';
	$options['id'] = 'campaignID';
 	$options['class'] = 'padLeft';
	$options['required'] = 'required';
	$options['empty'] = 'Select Campaign';
echo '<div style="background-color: #F1F1F1; padding: 5px; height: 30px;">';
	echo $this->form->select('data[Campaign][campaignID]', $options);
	echo '</div>';
?>

<?php 	
	$options = array();
	$options['id'] = 'message_name';
 	$options['class'] = 'padLeft';
	//$options['required'] = 'required';
	//$options['name'] = 'data[Campaign][categoryId]';
	echo '<div style="background-color: #FFF; padding: 5px; height: 30px;">';
	//echo $this->form->input('message_name', $options); 
	echo '<div>
                <label for="message_name">Subject</label>
                <input id="message_name" class="padLeft" type="text" value="" name="data[Message][message_name]" placeholder="Message name goes here">
              </div>';
	echo '</div>';
?>
<?php 
        
	echo '<div style="background-color: rgb(241, 241, 241); padding: 5px; height: 30px;">
	<div><label style="min-width:150px; " for="sent_date">Send Date: </label>';
	echo '<input type="text" id="datepicker20">'; // id="datepicker" class="dpClass"
	echo '</div></div>';
?>

<?php
	$options = array();
	$options['options'] = $templatedropArr;
	$options['label'] = 'Select Template';
	$options['id'] = 'template_list';
 	$options['class'] = 'padLeft';
	$options['name'] = 'data[Template][templateList]';
	$options['required'] = 'required';
	$options['empty'] = 'Select Template';
	$options['before'] = '<div id="templateDiv">';
	$addImg = '<img id="openTemplateBtn" src="'.$pluginImagesDir.'folder.png" >';
	$options['after'] = "$addImg <span id='tempSelErr' style='display:none'></span></div>";
	echo '<div style="background-color: #FFF; padding: 5px; height: 30px;">';
	echo $this->form->select('template', $options); 
	echo "</div>";
?>

<?php
/*
$id = 'content';
$content = 'This is some content that will be editable with TinyMCE.This is some content that will be editable with TinyMCE.';
wp_editor( $content, $id );
*/
?>
<div>
 <textarea id="content" name="content" class="mceEditor" style="width:100%; height:400px"> </textarea>

</div>
<div id="textarea2" style="font-size: larger; font-family: Times New Roman; font-weight: bold;"><h3>For non HTML email services</h3></div>
<div>
 <textarea id="content2" name="textcontent" class="texteditor" style="width:100%; height:195px"> </textarea>

</div>
<!--<textarea id="content" name="content" class="mceEditor"> </textarea>-->


<?php 
	$options['id'] = 'mode';
	$options['name'] = 'data[Message][active]';
 	echo $this->form->hidden_input('active', $options); 
?>
<?php 
	$options['id'] = 'active';
	$options['name'] = 'data[Campaign][active]';
	$options['value'] = '1';
 	echo $this->form->hidden_input('active', $options); ?>
<?php  
	$options = array(); 
	$options['id'] = 'sendPreviewEmail';
	$options['type'] = 'button';
	$options['class'] = 'button';
	//$options['before'] = '<div id="previewDiv">';


	//echo $this->form->before_input('Send Preview Email',$options=array('label'=>''));
	echo '<div style="background-color: #F1F1F1; padding: 5px; height: 30px; margin-top: 20px;">';
	echo '<div>';
	//echo $this->form->button('Send Preview Email',$options);
	echo '<button id="sendPreviewEmail" class="button" type="button" style="font-weight: bold;">Send Preview Email</button>';
	echo $addMailBox = '<input type="text" id="previewMailId" value="" >';
        echo '<button id="showEmailPreview" class="button" type="button" style="font-weight: bold;margin-left:15px;">Show Email Preview</button>';	
	echo '<span id="statmsg"></span></div></div>';
	//echo after_input('Send Preview Email');
?>
<style>
#addCampaignpop {
	background: url("../images/white-grad.png") 
	repeat-x scroll left top #F2F2F2;
   	text-shadow: 0 1px 0 #FFFFFF;
	border-color: #BBBBBB;
    	color: #464646;
    -moz-box-sizing: content-box;
    border-radius: 11px 11px 11px 11px;
    border-style: solid;
    border-width: 1px;
    cursor: pointer;
    font-size: 12px !important;
    line-height: 13px;
    padding: 3px 8px;
    text-decoration: none;

}
#addCampaignpop:hover {
	border-color:#666666; color:#000;
}
</style>
<?php  
	$options = array(); 
	$options['id'] = 'addCampaignpop';
	$options['type'] = 'button';
	$options['class'] = 'button';
	echo '<div style="background-color: #FFF; padding: 5px; height: 30px; margin-top: 20px;">';
	echo $this->form->end('Submit For Approval',$options);
	echo '</div>';
?>
<div id="msgResponceDiv" style="background-color:#99FF66;" ></div>
<script type="text/javascript">
$(function(){
    var width=960,sign="-";
    $("#news-back").click(function(){
        sign='+';
        scroll_me();
        return false;
    });
    $("#news-forward").click(function(){
        sign='-';
        scroll_me();
        return false;
    });

    function scroll_me(){
        if(!$("#inner-wrapper").is(':animated')){
            switch(sign){
                case('-'):
                    $("#inner-wrapper").stop(false,true).animate({left:"-="+width+"px"},500,function(){
                        //$(this).css({left:0}).append(jQuery(".item",jQuery(this)).eq(0));
                    });
                break;
                default:
                    $("#inner-wrapper").css({left:'-'+width+'px'});
                    $("#inner-wrapper .item:first").before(jQuery("#inner-wrapper .item:last"));
                    $("#inner-wrapper").stop(false,true).animate({left:"+="+width+"px"},500,function(){
                        $(this).css({left:0});
                    });
                break;
            }
        }
    }

});
</script>

<style>/* 960px width:683px; */
#outer-wrapper{width:auto;height:365px;display:block;overflow-x:hidden;}
#inner-wrapper{width:3840px;height:220px;position:relative;}
.item{width:960px;height:200px;display:block;float:left;position:relative;}
.item li { float:left;}
.item ul { clear:both;}
.item li img{ border: 1px solid #CCCCCC;
    margin: 2px;
    padding: 5px; }
#tableSlider td{ border: thin solid #CCCCCC;
    border-collapse: collapse; padding: 5px;}
.item{ font-size:10px; }
</style>
<?php
				$rowsPerPage = 3;
				$perRow = 5;
				$totalTemplates = count($tempListArr);
				//$pages = floor($totalTemplates/$perRow);
				$num_of_pages = ceil($totalTemplates/$rowsPerPage);

				$perPageTotal = $perRow*$rowsPerPage;
				for($r=1;$r<=$num_of_pages;$r++){
					$rows[] = $perRow*$r;
				}
			for($dv=0;$dv<$pages;$dv++){
				echo '<div class="item">';
				echo '<ul>';
				for($i=1;$i<=count($tempListArr);$i++){
					$rowInPg = 1;
					echo '<li><img id="'.$i.'" src="'.$pluginImagesDir.'msg_templates/thumb/'.$tempListArr[$i]['thumb'].'" width="100" height="100"><br>'.$i.':'.$tempListArr[$i]['thumb'].'</li>';
						
					if (in_array($i,$rows)){
						echo '</ul><ul>';
					}

				}
				echo '</div>';
			}


/*$firstFive = array_slice($templateList, 0, 5);
for($t=0;$t<count($firstFive);$t++){
echo $firstFive[$t]->id;
}*/
//print_r($firstFive);
?>
<div id="templateLoader" style="display:none">


	<a id="news-back" href="#"><img src="<?php echo$pluginImagesDir.'left.png'; ?>"></a>
	<a id="news-forward" href="#"><img src="<?php echo$pluginImagesDir.'right.png'; ?>"></a>

	<div id="outer-wrapper">
	<div id="inner-wrapper">
		<!--<div class="item"><ul>-->

			<?php
			$imgPerRow=5;
			$totalRows=3;
			        $template=1;
			        //$newtemp=array_merge(array(),$tempListArr);
				if(is_array($tempListArr)){
			       		$newtemp = array_merge(array(),$tempListArr);
				} else { echo 'Templates are not available!';}
			        $j=0;
                              while($j<count($newtemp) ){
				echo '<div class="item">';
				$l=0;
				while($j<count($newtemp) ){
				echo '<ul>';
    $k=0;
				 for($i=$j;$i<count($newtemp);$i++){
				
					//echo $templateList[$t]->template_name .''.$templateList[$t]->previewImgName;
					$img=!empty($newtemp[$i]['thumb'])?$newtemp[$i]['thumb']:"no_preview.jpg";
					echo '<li><img title="'.$newtemp[$i]["tname"].'" id="'.$newtemp[$i]['id'].'" src="'.$pluginImagesDir.'templates/thumb/'.$img.'" width="100"'."onclick=selecttemp('".$newtemp[$i]['id']."');".' height="100"></li>';
					if(($i+1)%($imgPerRow) ==0) echo "<br/>";
					$j++;
					$k++;
					if($k==$imgPerRow) break;
				} 
				//$j++;
				//if (in_array($i,$rows)){ echo '</ul><ul>';}
				 echo '</ul>';
				 $l++;
				 if($l==$totalRows) break;
}
				echo '</div>';
                                }
			?>
		</ul>
			</div>
	</div>

</div>
<div id="previewTemplate" style="display:none">Email Preview <hr>
	<div id="pevPlace" style="height:100%;height:100%; font-weight:normal;"></div>
</div>
