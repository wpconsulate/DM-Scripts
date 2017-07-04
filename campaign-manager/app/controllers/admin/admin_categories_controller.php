<?php

class AdminCategoriesController extends MvcAdminController {


	// Overwrite the default index() method to include the 'is_public' => true condition
	public function index() {
		$this->load_model('Categories');
		//print_r($this->model);
	 	$collection = $this->Categories->paginate($this->params);
		$objects = $this->Categories->find(array(
		'conditions' => array(
			'Categories.active' => 1,
			),
			'order' => 'Categories.id DESC',
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
