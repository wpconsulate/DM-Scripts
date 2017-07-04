<?php

class AdminPropertiesController extends MvcAdminController {

	// Overwrite the default index() method to include the 'is_public' => true condition
	public function index() {
		$this->load_model('Properties');
		//print_r($this->model);
	 	$collection = $this->Properties->paginate($this->params);
		$objects = $this->Properties->find(array(
		'conditions' => array(
			'Properties.prop_status' => 1,
			),
			'order' => 'Properties.id DESC',
			'page' => 1,
			'per_page' => 20,
		));

		$this->set('objects', $objects);
		$this->set_pagination($collection);
	
	}

 /*   public function show() {
        $this->load_model('Campaign');
        $campaign = $this->Campaign->find();
    }
*/

}

?>
