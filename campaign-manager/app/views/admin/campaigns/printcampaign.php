<h2><?php echo $object->__name; ?></h2>

<p>
	<?php //echo $this->html->link('&#8592; All Campaigns', array('controller' => 'campaigns')); ?>
	<?php 
		//print_r($objects);
		$this->render_view('_print', array('locals' => array($model))); ?>
</p>
