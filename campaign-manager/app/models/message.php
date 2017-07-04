<?php

class Message extends MvcModel {

	var $display_field = array('message_name','message','date','status','active');
	var $order = 'Message.message ASC';

	//var $includes = array('Categories', 'Properties');
	/*var $belongs_to = array(
		'Property' => array(
		'foreign_key' => 'propertyId'
		)
	);*/
}

?>
