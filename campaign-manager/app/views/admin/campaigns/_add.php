<?php $pluginImagesDir = plugins_url().'/campaign-manager/images/'; ?>

<style>

#pop div{ display:table-row;}
#pop label{ display:table-cell;}
#campaign_name {width: 370px; height:23px; }
#datepicker2{width: 115px; height:23px; }
#addForm label{ height:30px !important;}
#CreateCat{height: 23px; width: 188px;}
.ui-dialog-titlebar-close ui-corner-all ui-state-hover {
    background-image: url("http://hillplaceapts.com/wp-content/plugins/campaign-manager/images/close.png");
    background-repeat: no-repeat;
    display: block;
    margin: 1px;
}
.ui-icon-closeButton{
	background-image: url(<?php echo $pluginImagesDir.'close.png';?>);
    background-repeat: no-repeat;
    display: block;
    margin: 1px;
}
.ui-icon-closeButton:hover{
	background-image: url(<?php echo $pluginImagesDir.'close.png';?>);
    background-repeat: no-repeat;
    display: block;
    margin: 1px;
}
.ui-dialog-titlebar-close  {
    background-image: url(<?php echo $pluginImagesDir.'close.png';?>);
    background-repeat: no-repeat;
    display: block;
    margin: 1px;
}
.ui-icon .ui-icon-closethick span{
    background-image:url(<?php echo $pluginImagesDir.'close.png';?>);
    height: 16px;
    width: 16px;z-index:-1;
  background-repeat: no-repeat;
}
.ui-dialog .ui-dialog-titlebar-close:hover {
	background: #E9F4F5; border:0;
	background-image:url(<?php echo $pluginImagesDir.'close.png';?>);
	height: 16px;
	width: 16px; z-index:1;
  background-repeat: no-repeat;
}

</style>
<script>
$(document).ready(function() {
	$('#propertyId').change(function() {
		var selectedProp = $('#propertyId').val();
		getPropertyBasedLists(selectedProp);
		getPropertyBasedCategories(selectedProp);
	});
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
<div id="editResponceDiv" ></div>



<?php

	// echo is_admin(); echo is_super_admin();

	$options['action'] = 'add';
	$options['id'] = 'addForm';
	echo $this->form->create('Campaign',$options); ?>


<?php 	

	$options = array();
	$options['id'] = 'campaign_name';
 	$options['class'] = 'padLeft';
	//$options['required'] = 'required';
	//$options['name'] = 'data[Campaign][categoryId]';

?>
<?php echo $this->form->input('campaign_name', $options); ?>

<?php 

	echo '<div><label style="min-width:150px; " for="sent_date">Send Date: </label>';
	echo '<input type="text" name="data[Campaign][sent_date]" id="datepicker2">'; // id="datepicker" class="dpClass"

	//echo $this->form->input('date', array('label' => 'Date (YYYY-MM-DD)'));
	echo '</div>';
	
	$options['options'] = array('1'=>'category1','2'=>'category2','3'=>'category3');
	$options['value'] = 'category2';
	$options['label'] = 'Select Category';
	$options['id'] = 'categoryId';
 	$options['class'] = 'padLeft';
	$options['name'] = 'data[Campaign][categoryId]';
	$options['before'] = '<div id="categoryDiv">';
	$options['required'] = 'required';

	//if (is_super_admin()) {
		$addImg = '<img id="AddCatBtn" src="'.$pluginImagesDir.'add.png" width="16" height="16">';
		$options['after'] = "$addImg </div><div style='display:none'></div><div id='catform' style='display:none'></div>";
	//} else { $options['after'] = "</div>"; }

?> <!-- '<span id="categorySpan">'. .'</span>' -->
<?php echo $this->form->select('data[Campaign][categoryId]', $options); ?>
<?php

if (is_super_admin()) {
	$options = array();
	$options['options'] = array('1'=>'category1','2'=>'category2','3'=>'category3');
	$options['value'] = 'category2';
	$options['label'] = 'Select Property';
	$options['id'] = 'propertyId';
 	$options['class'] = 'padLeft';
	$options['name'] = 'data[Campaign][propertyId]';
	$options['required'] = 'required';
	echo $this->form->select('data[Campaign][propertyId]', $options);
} else {
	global $blog_id;
	echo '<input type="hidden" name="propertyId" id="propertyId" value="'.$blog_id.'">';
}
?>

<?php //echo $this->form->has_many_dropdown('Speaker', $speakers, array('style' => 'width: 200px;', 'empty' => true)); ?>

<?php //echo $this->form->belongs_to_dropdown('Category', $categories, array('style' => 'width: 200px;', 'empty' => 'Select Category', 'value'=>$object->categoryId)); ?>
<?php //echo $this->form->belongs_to_dropdown('Property', $properties, array('style' => 'width: 200px;', 'empty' => 'Select Property','value'=>$object->propertyId)); ?>

<?php
	global $wpdb;
	global $blog_id;
	//if (is_super_admin()) {
		
		//$sql = "SELECT id,list_name FROM ". $wpdb->prefix."lists WHERE active = 1";
	//} else { 
		$sql = "SELECT id,list_name FROM ". $wpdb->prefix."lists WHERE propertyId = {$blog_id} AND active = 1";
	//}
	$completeLists = $wpdb->get_results($sql);
	///Creating List Dropdown
	foreach($completeLists as $completeList):
		$completeListArr[$completeList->id] = $completeList->list_name;
	endforeach; 

	$options = array();
	$options['options'] = $completeListArr;
	$options['value'] = 'category2';
	$options['label'] = 'Select List';
	$options['id'] = 'list';
 	$options['class'] = 'padLeft';
	$options['name'] = 'data[Campaign][propertyId]';
	$options['multiple'] = 'multiple';
	$options['required'] = 'required';

	echo $this->form->select('data[Campaign][propertyId]', $options); 

?>

<?php //echo $this->form->hidden_input('lists'); ?>
<?php 
	$options = array();
	$options['id'] = 'active';
	$options['name'] = 'data[Campaign][active]';
	$options['value'] = '1';
 	echo $this->form->hidden_input('active', $options); ?>
<?php  

	/*$options = array();
	$options['id'] = 'addCampaignpop';
	$options['type'] = 'button';
	$options['class'] = 'savebutton';
	$options['before'] = '<div><label>&nbsp; </label>';
	$options['after'] = '</div>';

	//echo '<br><br>';
	//echo $this->form->button('Save Campaign',$options);*/
	echo '<div><label>&nbsp; </label><button class="savebutton" type="button" id="addCampaignpop">Save Campaign</button></div>';
	echo '</form>';

	/*$option = array(); 
	$option['id'] = 'addCampaignpop';
	$option['type'] = 'button';
	$option['class'] = 'button';
	echo $this->form->end('Save Campaign',$option);*/
?>
<!--</fieldset>-->
