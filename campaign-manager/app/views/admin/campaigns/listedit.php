<?php 
	$pluginImagesDir = plugins_url().'/campaign-manager/images/';
?>
<style>
form#form1 div:nth-child(even) {background: #F0F0F0;}
form#form1 div:nth-child(odd) {background: #FFF; }
#form1 div{ display:table-row;}
#form1 label{ display:table-cell;}
#form1 label{ min-width:150px; padding-left: 10px; }

#updateButton{ 
    background: url(<?php echo $pluginImagesDir.'button-bg.png';?>) no-repeat scroll 0 0 transparent;
    border: medium none;
    font-family: Verdana,Arial,sans-serif;
    font-size: 12px; font-weight:bold;
    height: 20px;
    padding-bottom: 5px;
    padding-top: 5px;
    width: 190px;
}
/*.wrap { font-size:12px !important; font-weight:normal !important;}*/

.wrap fieldset div{ display:table-row;}
.wrap fieldset label{ display:table-cell;}
</style>
<script type="text/javascript">

$(document).ready(function(){
            $('#updateButton').click(function() {
            editList();
            });
        });
        function editList(){
		ListName=$("#edit_list_name").val();
		ListDescription=$("#edit_description").val();
		Listtag=$("#edit_tags").val();
		ListCat=$('#Lists_Categories_select').val();
		Listid=$("#edit_id").val();
		propertyId=$("#property_id").val();
		if(ListName==""){
			$("#error").html("Enter a name for the List.");
			$("#edit_list_name").focus();
			return false;
		}
		var data = {
			action: 'editList',
			Name : ListName,
			Description : ListDescription,
			lTag : Listtag,
			categoryId : ListCat,
			id : Listid,
			s_props : propertyId
		};
		ajaxurl="<?php echo bloginfo('wpurl');?>/wp-admin/admin-ajax.php";

		jQuery.post(ajaxurl, data, function(response) {
			$('#saveMsg').html(response);
			$('#saveMsg').css("color",'#008000');
			$('#saveMsg').css("font-weight",'bold');
			setTimeout(function() { 
				$('#saveMsg').hide(); 
				$("#editDialog").dialog('destroy');
			},2000); // Stay 3 seconds
			ListPages('list_name','ASC',0,'0.5','1');
		});
            }
</script>
<div id="saveMsg"> </div>
<input type='hidden' id='property_id' value="<?php echo $props; ?>" />
<?php
// displaying the current onesite conditions
if(!empty($Lists_Rel)){
	$display_conditions="";
	if($Lists_Rel[0]->residents!=""){
		$values1=explode(';',$Lists_Rel[0]->residents);
		$resval=explode(':',$values1[0]);
		if($resval[1]!=""){
			$display_conditions.="Residents From ".$resval[1]."<br/>";
		}elseif($resval[1]=='All'){
			$display_conditions.="All Residents <br/>";
		}
	}
	if($Lists_Rel[0]->parents!=""){
		$values2=explode(';',$Lists_Rel[0]->parents);
		$parval=explode(':',$values2[0]);
		if($parval[1]!=""){
			$display_conditions.="Parents From ".$parval[1]."<br/>";
		}elseif($parval[1]=='All'){
			$display_conditions.="All Parents <br/>";
		}
		
	}
	if($Lists_Rel[0]->prospects!=""){
		$display_conditions.="All Prospects <br/>";
		
	}
}
?>

<?php
	$options['onfocus'] = 'alertthis()';

?>
<div id="error" style="color:#FF0000;"></div>
<?php echo $this->form->create('Lists',$options); ?>
<?php 
//print_r($Lists);
?>
<?php
	$options = array(
		'value' => $Lists->list_name,
		'id' => 'edit_list_name',
		'class' => 'padLeft'
		);
	echo $this->form->input('list_name', $options); ?>

<?php
	$options = array(
		'value' => $Lists->description,
		'id' => 'edit_description',
		'class' => 'padLeft',
		'name' => 'data[Lists][preview]',
		'label' => 'Description'
		);
	echo $this->form->textarea_input($field_name, $options); ?>


<?php 
//echo $this->form->belongs_to_dropdown('Categories', $categories, array('style' => 'width: 200px;', 'empty' => 'Select Category', 'value'=>$Lists->categoryId)); 
?>
<div>
<label for="Lists_Categories_select">Categories</label>
<select style="width: 200px;" name="data[Lists][categories_id]" id="Lists_Categories_select">
<option value="">Select Category</option>
<?php foreach($categories2 as $category2){ 
if($category2->id==$Lists->categoryId) $selected="selected='selected'";
else $selected="";

echo "<option $selected value='{$category2->id}'>{$category2->category_name}</option>";
}
?>
</select>
</div>

<?php
	$options = array(
		'value' => $Lists->tags,
		'id' => 'edit_tags',
		'class' => 'padLeft'
		);
	echo $this->form->input('tags', $options); 
?>

<?php	$options = array();
	$options['id'] = 'edit_id';
	$options['name'] = 'data[Lists][id]';
	$options['value'] = $Lists->id;
 	echo $this->form->hidden_input('Lis_id', $options); ?>

<div></div>
<div>
	<label>Import Type</label> <?php echo $Lists->import_type;?>
	
</div>
<?php if($display_conditions!=""){?>
<div><label>Import Criteria</label><?php echo $display_conditions;?></div>
<?php }?>
<?php
	/*	$options = array(
			'id' => 'updateButton',
			'type' => 'button',
			'class' => 'button'
		);

echo $this->form->button('Update',$options);*/
echo '<div style="background:none;"><label>&nbsp;</label><br><button class="button" type="button" id="updateButton">Update</button></div>';
echo '</form>';
?>
