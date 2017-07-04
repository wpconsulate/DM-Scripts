<h2>Edit Campaign</h2>

<?php echo $object->campaign_name; //echo '<pre>'; print_r($model); echo '</pre>'; ?>
<?php echo $this->form->create($model->name); ?>
<?php echo $this->form->input('campaign_name'); ?>

<?php echo $this->form->input('categoryId'); ?>
<?php echo $this->form->input('propertyId'); ?>
<?php //$options['id'] = 'active';
	//$options['name'] = 'data[Campaign][active]';	$options['value'] = '1'; id 	campaign_name 	sent_date 	categoryId 	propertyId 	active
 	echo $this->form->hidden_input('active', array('value' => $object->active)); ?>
<?php echo $this->form->end('Update'); ?>

<?php

print_r($categories);
echo '<br>-------------------------<br>';
print_r($properties);

?>