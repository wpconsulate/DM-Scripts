<?php

class Category extends MvcModel {
//wert
	var $display_field = 'category_name';

	var $order = 'Category.category_name ASC';

	//var $includes = array('Campaign', 'Property');
	/*var $has_and_belongs_to_many = array(
		'Campaign' => array(
			'join_table' => '{prefix}campaigns',
			'fields' => array('id', 'categoryId')
		)
	);*/
	
	
}

?>
