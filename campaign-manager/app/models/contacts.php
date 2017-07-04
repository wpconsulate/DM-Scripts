<?php
class Contacts extends MvcModel{

// 	 var $belongs_to = array('Contacts');
// 	 var $has_and_belongs_to_many = array(
// 		'Lists' => array(
// 			'join_table' => '{prefix}Lists',
// 			'fields' => array('id')
// 		)
// 	);
// 	 var $belongs_to = array(
// 		'Lists' => array(
// 			'id' => 'list_id'
// 		)
// 	);
	 var $belongs_to = array(
    'Lists' => array(
      'foreign_key' => 'id'
    )
  );
}

?>
