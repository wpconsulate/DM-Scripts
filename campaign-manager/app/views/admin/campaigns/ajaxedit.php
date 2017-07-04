<style>

.wrap fieldset div{ display:table-row;}
.wrap fieldset label{ display:table-cell;}

#CampaignCampaignName {width: 370px; height:23px; }
#datepicker3{width: 115px; height:23px; }
#form1 label{ height:30px !important;}

</style>
<script>

	$("#updateButton").click( function() {
		var error = 0;
		if($("#CampaignCampaignName").val() == ''){
			$('#CampaignCampaignName').css("background-color",'#FFEDEF');
			error = 1;
		} else { $('#CampaignCampaignName').css("background-color",'#FFF'); }
		if($("#datepicker3").val() == ''){
			$('#datepicker3').css("background-color",'#FFEDEF');
			error = 1;
		} else { $('#datepicker3').css("background-color",'#FFF'); }

		if($("#Campaign_Category_select").val() == 0){
			$('#Campaign_Category_select').css("background-color",'#FFEDEF');
			error = 1;
		} else { $('#Campaign_Category_select').css("background-color",'#FFF'); }
		if($("#Campaign_Property_select").val() == 0){
			$('#Campaign_Property_select').css("background-color",'#FFEDEF');
			error = 1;
		} else { $('#Campaign_Property_select').css("background-color",'#FFF'); }
		if($("#listedit option:selected").length == 0){
			$('#listedit').css("background-color",'#FFEDEF');
			error = 1;
		} else { $('#listedit').css("background-color",'#FFF'); }

		if(error == 0){
			updateCampaign();
			//return false;
		}
		return false;
	});

</script>
	<div id="editMsgDiv" ></div>

<fieldset>
<?php

	//Creates an array of ids from campaign_list_rel table
	// this is for pre selection of selected lists
	if(count($campListRel) > 0){
		foreach($campListRel as $campListRels):
			$selectedLists[] = $campListRels->list_id;
		endforeach;
	}

?>

<?php 		$options = array(); echo $this->form->create($model->name, $options); ?>

<?php 		$options = array();
		$options['value'] = stripslashes($campaign[0]->campaign_name);

		echo $this->form->input('campaign_name',$options); ?>
<?php

		echo '<div><label style="min-width:150px; " for="sent_date">Sent Date: </label>';
		echo '<input type="text" name="data[Campaign][sent_date]" id="datepicker3" value="'.$campaign[0]->sent_date.'"></div>'; // id="datepicker" class="dpClass"
		
?>

<div id="categoryDiv"><label for="categoryId">Select Category</label>
<?php 

$categories =(array) $categories;
		echo '<select style="width: 200px;" name="data[Campaign][category_id]" id="Campaign_Category_select">

<option value="">Select Category</option>';

foreach($categories as $cate){
if($campaign[0]->categoryId==$cate->id){
$selected="selected='selected'";
}else{
$selected="";
}
echo '<option '.$selected.' value="'.$cate->id.'">'.$cate->category_name.'</option>';
}
echo '</select>';
		
		
		?>
		</div>
<?php 
/*
	if (is_super_admin()) {
		echo $this->form->belongs_to_dropdown('Property', $properties, array('style' => 'width: 200px;', 'empty' => 'Select Property','value'=>$campaign[0]->propertyId)); //propertyId
	} else {
		global $blog_id;
		echo '<input type="hidden" name="Campaign_Property_select" id="Campaign_Property_select" value="'.$blog_id.'">';
	}
*/
		global $blog_id;
		echo '<input type="hidden" name="Campaign_Property_select" id="Campaign_Property_select" value="'.$campaign[0]->propertyId.'">';
?>

<?php

		// Multiple select list dropdown
		echo '<div><label for="list">Select List Test'.count($completeLists).'</label>';
		
		echo '<select multiple="multiple" name="data[Campaign][propertyId]" class="padLeft" id="listedit" >';

		if(count($completeLists) > 0){
			for($lst=0;$lst<count($completeLists);$lst++){
				$arid = $completeLists[$lst]->id; $match = '';
				if (in_array($arid,$selectedLists)){ $match = ' selected="selected"'; }
				echo '<option value="'.$completeLists[$lst]->id.'" '.$match.'">'.$completeLists[$lst]->list_name.'-'.$completeLists[$lst]->id.'</option>';
			}
		} else {
			/* id- listedit data[Campaign][propertyId] */ 
			echo '<option value="0">No list is available</option>';
		}
		echo '</select>';
		echo '</div>';


?>

<?php 		echo $this->form->hidden_input('active', array('value' => $object->active)); ?>
<?php 		echo $this->form->hidden_input('model', array('value' => $model->name)); ?>
		<input type="hidden" id="CampaignHiddenId" name="CampaignHiddenId" value="<?php echo $campaign[0]->id; ?>">

<?php 		//echo $this->form->end('Update'); ?>
<?php
		/*$options = array();
		$options = array(
			'id' => 'updateButton',
			'type' => 'button',
			'class' => 'savebutton'
		);
		echo '<br><br>';
		echo $this->form->button('Update',$options);*/
		echo '<div style="background: none repeat scroll 0 0 #FFFFFF !important;"><label>&nbsp; </label>
			<br><button class="savebutton" type="button" id="updateButton">Save Campaign</button></div>';
		echo '</form>';
?>

</fieldset>
