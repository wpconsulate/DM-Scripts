<?php

class Property extends MvcModel {

	var $display_field = 'property_name'; // property_id 	property_name
	var $order = 'Property.property_name ASC';

	//var $includes = array('Campaign', 'Category');
	//var $has_many = array('Campaign');
	var $has_and_belongs_to_many = array(
		'Campaign' => array(
			'join_table' => '{prefix}campaigns',
			'fields' => array('id', 'propertyId')
		)
	);
	
	
}

?>
