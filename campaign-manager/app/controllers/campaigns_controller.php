<?php

class CampaignsController extends MvcPublicController {
	
	// Overwrite the default index() method to include the 'is_public' => true condition
	public function index() {
	
		/*$this->params['page'] = empty($this->params['page']) ? 1 : $this->params['page'];
		
		$this->params['conditions'] = array('is_public' => true);
		
		$collection = $this->model->paginate($this->params);
		
		$this->set('objects', $collection['objects']);
		$this->set_pagination($collection);*/
$this->set_objects();
	
	}

    public function show() {
        $this->load_model('Campaign');
        $campaign = $this->Campaign->find();
    }

}

?>