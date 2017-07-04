<?php 
	$pluginImagesDir = plugins_url().'/campaign-manager/images/';

?>
<fieldset><legend>Add Message</legend>

<style>
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
margin-left: 5px;
}
#camapign_list {
margin-left: -3px;
}

#templateList {
margin-left: 5px;
}
#datepicker20{
margin-left:32px;
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

</style>
<script>
$('#tempSelErr').click( function() {
	$('#tempSelErr').hide();
});

$('#openTemplateBtn').click( function() {
	if($('#template_list').val() == 0){
		$('#tempSelErr').html('Please Select any template');
		$('#tempSelErr').addClass('error');
		$('#tempSelErr').show();
	} else {
		$('#tempSelErr').hide();
		
		//alert($('#myarea1').html());
		//loadProprtyAndCategory('templates_preview', 'template_name','template_list',0);
	}
});


	//$("#addCampaignpop").live('click', function() {
$("#addCampaignpop").click( function() {
		var error = 0;
		if($("#campaign_name").val() == ''){
			$('#campaign_name').css("background-color",'#FFEDEF');
			error = 1;
		} else { $('#campaign_name').css("background-color",'#FFF'); }
		if($("#datepicker2").val() == ''){
			$('#datepicker2').css("background-color",'#FFEDEF');
			error = 1;
		} else { $('#datepicker2').css("background-color",'#FFF'); }

		if($("#categoryId").val() == 0){
			$('#categoryId').css("background-color",'#FFEDEF');
			error = 1;
		} else { $('#categoryId').css("background-color",'#FFF'); }
		if($("#propertyId").val() == 0){
			$('#propertyId').css("background-color",'#FFEDEF');
			error = 1;
		} else { $('#propertyId').css("background-color",'#FFF'); }
		if($("#list option:selected").length == 0){
			$('#list').css("background-color",'#FFEDEF');
			error = 1;
		} else { $('#list').css("background-color",'#FFF'); }

		if(error == 0){

			saveAddCampaign();
			//return false;
		}
		return false;
	});


</script>
<div id="msgResponceDiv" ></div>
<?php

	// echo is_admin(); echo is_super_admin();

	$options['action'] = 'add';
	$options['id'] = 'addForm';
	echo $this->form->create('Message',$options); ?>

<?php 	
	$options = array();
	$options['id'] = 'message_name';
 	$options['class'] = 'padLeft';
	//$options['required'] = 'required';
	//$options['name'] = 'data[Campaign][categoryId]';
	echo $this->form->input('message_name', $options); 
?>
<?php 

	echo '<div><label style="min-width:150px; " for="sent_date">Sent Date: </label>';
	echo '<input type="text" name="data[Campaign][sent_date]" id="datepicker20">'; // id="datepicker" class="dpClass"
	echo '</div>';
	
	$options['value'] = 'category2';
	$options['label'] = 'Select Campaign';
	$options['id'] = 'camapign_list';
 	$options['class'] = 'padLeft';
	$options['required'] = 'required';

	echo $this->form->select('data[Campaign][categoryId]', $options);  
?>

<?php
	$options = array();
	$options['label'] = 'Select Template';
	$options['id'] = 'template_list';
 	$options['class'] = 'padLeft';
	$options['name'] = 'data[Template][templateList]';
	$options['required'] = 'required';
	$options['before'] = '<div id="templateDiv">';
	$addImg = '<img id="openTemplateBtn" src="'.$pluginImagesDir.'folder.png" >';
	$options['after'] = "$addImg <span id='tempSelErr' style='display:none'></span></div>";

	echo $this->form->select('template', $options); 
?>
<?php //echo $this->form->has_many_dropdown('Speaker', $speakers, array('style' => 'width: 200px;', 'empty' => true)); ?>

<?php //echo $this->form->belongs_to_dropdown('Category', $categories, array('style' => 'width: 200px;', 'empty' => 'Select Category', 'value'=>$object->categoryId)); ?>

<div>
<textarea id="myarea1" name="content" class="mceEditor1222"> </textarea>
</div>

<?php //echo $this->form->hidden_input('lists'); ?>
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
	echo '<div style="margin-top:15px;margin-bottom:10px;">';
	echo $this->form->button('Send Preview Email',$options);
		$addMailBox = '<input type="text" id="previewMailId" value="">';
	echo $addMailBox .'</div>';
	//echo after_input('Send Preview Email');
?>

<?php  
	$option = array(); 
	$option['id'] = 'addCampaignpop';
	$option['type'] = 'button';
	$option['class'] = 'button';
	echo $this->form->end('Submit For Approval',$option);
?>
</fieldset>
