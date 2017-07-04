<?php
class Lists extends MvcModel{

	var $display_field = 'list_name';
	var $display_date = 'date';
	var $order = 'Lists.list_name ASC';

	var $includes = array('Categories');
	var $has_many = array('Contacts');	
	var $categoryId = 'categoryId';
	var $belongs_to = array(
		'Contacts' => array(
		'foreign_key' => 'list_id'
		)
	);
}

?>
