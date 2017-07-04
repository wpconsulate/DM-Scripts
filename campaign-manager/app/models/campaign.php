<?php

class Campaign extends MvcModel {

	var $display_field = array('campaign_name','sent_date','categoryId','propertyId','list','active');
	var $display_date = 'sent_date';
	var $categoryId = 'categoryId';

	var $order = 'Campaign.campaign_name ASC';

	var $includes = array('Categories', 'Properties');
	var $belongs_to = array(
		'Property' => array(
		'foreign_key' => 'propertyId'
		)
	);

	
/*
	var $belongs_to = array(
		'Category' => array(
		'foreign_key' => 'categoryId'
		),
		'Property' => array(
		'foreign_key' => 'propertyId'
		)
	);
*/
	
}

?>
