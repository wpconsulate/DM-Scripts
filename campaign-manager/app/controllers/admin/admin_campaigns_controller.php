<?php

class AdminCampaignsController extends MvcAdminController {
	
	//var $default_search_joins = array('Campaigns', 'Venue');
	var $default_columns = array('id',
		'campaign_name' => 'Campaign Name',
		'sent_date' => 'Sent Date',
		'categoryId');

	function index() {

/* To get user data:
	To load properties of the user...
		global $current_user;
		$current_user = wp_get_current_user();
		//echo '<pre>'; print_r($current_user); echo '</pre>';
		echo 'UID: '.$current_user->ID;
		$meta = get_user_meta($current_user->ID);
		//echo '<pre>'; print_r($meta); echo '</pre>';
		echo '<br>'.$meta['wp_user_level'][0];
*/
		$this->set_categories();
		$this->set_properties();
		$this->set_allLists();

		$rowPerpage = 5;
		$objects = $this->Campaign->find(array(
		'conditions' => array(
			'Campaign.active' => 1,
			),
			'order' => 'Campaign.campaign_name ASC',
			'page' => 1,
			'per_page' => $rowPerpage,
			
		));
		$this->set('objects', $objects);
//echo '<pre>'; print_r($objects); echo '</pre>';
		//echo '<pre>'; print_r($this->Campaign); echo '</pre>';
		$totalObjects = $this->Campaign->find(array(
		'conditions' => array(
			'Campaign.active' => 1,
			),
			'order' => 'Campaign.campaign_name ASC',
		));
		
		$total = count($totalObjects);
		$num_of_pages = ceil($total/$rowPerpage);


		$this->set('num_of_pages', $num_of_pages);
		$this->set('totalresult', $total);
		$this->set('rowPerpage', $rowPerpage);
		$this->set_pagination($objects);
	}

	function add(){
		$this->set_categories();
		$this->set_properties();

		$this->load_model('Campaign');
		$this->create_or_save();
	}
/*-	function ajaxedit(){

		$this->set_categories();
		$this->set_properties();
		$this->set_allLists();
		$this->set_lists($this->params['id']);
		$this->load_model('Campaign');
		$campaigns = $this->Campaign->find_by_id($this->params['id'], array('selects' => array('id','campaign_name','sent_date','categoryId','propertyId','active')));
		$this->set('campaign', $campaigns);

		//$this->verify_id_param();
		$this->set_object();
		//$this->create_or_save();
	}*/
	function ajaxedit(){
		global $wpdb;
		$this->set_categories($_POST['propertyId'],'1');
		$this->set_properties();
		$this->set_allLists($_POST['propertyId']); //Property id of the selected campaign
		$this->set_lists($this->params['id'],$_POST['propertyId']);

		if($this->params['propertyId'] == 1){
			$prefix=" wp_";
		} else {
			$prefix=" wp_".$this->params['propertyId']."_";
		}

	        $campSql = " SELECT * FROM ".$prefix."campaigns WHERE `id` ={$this->params[id]}";
		
		$campaigns = $wpdb->get_results($wpdb->prepare($campSql));
		$this->set('campaign', $campaigns);

	}

	function edit(){
		//echo '<pre>';print_r($this->params);echo '</pre>'; 
		$this->set_category();
		$this->set_property();
		$this->set_campaign();

		$this->verify_id_param();
		$this->set_object();
		$this->create_or_save();

	}

	function deletecampaign(){
		global $wpdb;
		$this->load_model('Campaign');
		$this->verify_id_param();
		$sql = "SELECT * FROM ".$wpdb->prefix."campaigns WHERE id = {$this->params[id]}";
		$res = $wpdb->get_row($wpdb->prepare($sql));
		//print_r($res);
		//echo $res['sent'];
		if($res->id){
			$msgFound = 0;
			$msgsql = " SELECT * FROM ".$wpdb->base_prefix."messages WHERE `campaignID` ={$res->id}";
			$messages = $wpdb->get_results($wpdb->prepare($msgsql));
			if(count($messages) > 0 ){
				$msgFound = $wpdb->num_rows;
				/*   [id] => 1    [message_name] => FirstMsg */
				foreach($messages as $message):
					$delMsgSql = "DELETE FROM ".$wpdb->base_prefix."messages WHERE id = {$message->id} LIMIT 1";
					$wpdb->query($delMsgSql);
				endforeach;
			}

			$delCamSql = " DELETE FROM `".$wpdb->prefix."campaigns` WHERE `id` =  ".$this->params['id']." LIMIT 1";
			$wpdb->query($delCamSql);
			$responseStr = 'Campaign has been deleted. ';
			if($msgFound){
				$responseStr .= " {$msgFound} associated messages also removed.";
			}
			echo $responseStr;

		} else {
			echo 'Invalid request. No such a campaign in database.';
		}

		exit();
	}

	function printcampaign(){
		$this->load_model('Campaign');
		$objects = $this->Campaign->find(array(
		'conditions' => array(
			'Campaign.active' => 1,
			),
			'order' => 'Campaign.sent_date DESC',
			'page' => 1,
		));

		$this->set('objects', $objects);
	}


/* MESSGES */

	function message(){
		$this->load_model('Message');
		$this->set_properties();
		$rowPerpage = 5;
		$this->set_campaignList();
		$objects = $this->Message->find(array(
		'conditions' => array(
			'Message.active' => 1,
			),
			'order' => 'Message.message_name ASC',
			'page' => 1,
			'per_page' => $rowPerpage,
			
		));
		$this->set('objects', $objects);
	}

	function addmessage(){
	        global $wpdb;
		$this->set_campaignList();
		$this->set_templates();
		$this->set_properties();
		$this->load_model('Templates');
		/*$templateList = $this->Templates->find(array('selects' => array('id','template_name','previewImgName'),
			'conditions' => array('active' => 1)
		)); */
		$sql="SELECT `id`,`template_name`,`previewImgName` FROM ".$wpdb->base_prefix."templates WHERE active='1'";
		$templateList=$wpdb->get_results($sql);
		$this->set('templateList', $templateList);
		$this->create_or_save();
		//$this->set_object();
	}


	//editmessage

	function editmessage(){
		global $wpdb;
		$this->set_campaignList();
		$this->set_templates();
		$this->load_model('Message');

		if(!empty($this->params['mid'])){
			$modifyCode = md5('modifyMessage');
			if($this->params['vc'] && $this->params['vc'] != $modifyCode){
				 $this->set('mError', 'Request verification failed'); 
			} else {
				/*$messageToEdit = $this->Message->find_by_id($this->params['mid'],array('selects' => array('id','message_name','message','date', 'campaignID','propertyID','status','active'),
					'conditions' => array('active' => 1)
				));*/ // ".$wpdb->base_prefix."
				
				$sql = "SELECT * FROM ".$wpdb->base_prefix."messages WHERE `id` = {$this->params[mid]} AND `active` =1";
				$messageToEdit = $wpdb->get_results($sql);

				$messageToEdit = $messageToEdit[0];
				if($messageToEdit->id){
					if($messageToEdit->status == 3){ // 3 = Sent
						$this->set('mError', 'This message cannot be edited, as it has already been sent.');
					} else {
						$this->set('messageToEdit', $messageToEdit);
					}
				} else { $this->set('mError', 'Message Not Found!'); }
			}
		} else { $this->set('mError', 'Invalid request.'); }
		

		$this->load_model('Templates');
		$templateList = $this->Templates->find(array('selects' => array('id','template_name','previewImgName'),
			'conditions' => array('active' => 1)
		));
                //print_r($templateList);
               /*
                $property=$messageToEdit->propertyID;
        if($property==1)$prefix=$wpdb->base_prefix;
	else $prefix="wp_{$property}_";
	
                //$res=$wpdb->get_results("SELECT * FROM {$prefix}templates WHERE active='1'");
                //$templateList =$res;
                */
		$this->set('templateList', $templateList);
		//$this->create_or_save();
		//$this->set_object();
	}

	function deletemessage(){
		global $wpdb;
		$this->load_model('Message');
		$this->verify_id_param();

		/// 	status 1=Pending:2=Approved:3=Sent
		$sql = "SELECT status FROM ".$wpdb->base_prefix."messages WHERE id = {$this->params[id]}";
		$res = $wpdb->get_row($wpdb->prepare($sql));
		if($res->status != 3){
			$sql = "DELETE FROM ".$wpdb->base_prefix."messages WHERE id =  ".$this->params['id']." LIMIT 1";
			$wpdb->query($sql);
			$rows_deleted = $wpdb->rows_affected;
			if($rows_deleted){
				echo "Success: Message has been deleted successfully ";
			}else{ echo "Error: Unable to delete! Please contact system admin";}
		} else { echo 'Error: Unable to Delete. This message has already been sent.';}
		die();
	}
        function approveMessage(){
		global $wpdb;	
		$capability_name = 'approve_message';
		$current_user = wp_get_current_user();
		$user = new WP_User($current_user->ID);
		$res1=$wpdb->get_results("SELECT * FROM `".$wpdb->base_prefix."capabilities` WHERE `userId`='".$current_user->ID."' AND `capability`='".$capability_name."'");
		if($res1){   
		//if(current_user_can('approve_message')){
			if(isset($_GET["mid"])){
				$id=$_GET["mid"];
				$res=$wpdb->get_results("SELECT `status`,`date` FROM ".$wpdb->base_prefix."messages WHERE `id`='$id'");
				$today=date("Y-m-d");
				
				if($res[0]->status == "1"){
					if(date("Y-m-d",(strtotime($res[0]->date))) >= $today){
						$res=$wpdb->get_results("UPDATE ".$wpdb->base_prefix."messages SET `status`='2' WHERE `id`='$id'");
						$message="Message approved sucessfully";
						
					}else{
						$err_message="Message elasped the send date. Please reset the send date.";
					}
				}else{
					$err_message="Message already approved/sent".$res->status;
				}

			}else{
				$err_message="Message id is missing";
			}
		}else{
			$err_message="You don't have sufficient permission to access this page";
		}

		$this->set("message",$message);
		$this->set("err_message",$err_message);
        }


	private function set_campaignList() {
		$this->load_model('Campaign');
		$campaignsList = $this->Campaign->find(array('selects' => array('id','campaign_name')));
		$this->set('campaignsList', $campaignsList);
	}

	// Create an array of templates
	function set_templates(){
/**
		$this->load_model('Templates');
		$templatesList = $this->Templates->find(array('selects' => array('id','template_name'),
			'conditions' => array('active' => 1)
		));
*/
		/// there are no 'site specific' templates, only global templates that are defined by the network admin.
		global $wpdb;
		$sql = "SELECT * FROM `".$wpdb->base_prefix."templates` WHERE `active` =1";
		$templates = $wpdb->get_results($sql);
           	$this->set('templatedrops', $templates);

	}
/*************/


	private function set_campaign() {
		$this->load_model('Campaign');
		//$campaigns = $this->Campaign->find(array('selects' => array('id','campaign_name','sent_date','categoryId','propertyId','active')));
		$campaigns = $this->Campaign->find_by_id($this->params['id'], array('selects' => array('id','campaign_name','sent_date','categoryId','propertyId','active')));
		///$this->set('campaign', $campaigns); // This function set variable 'campaign' to view file sa for dropdowns
	}

	private function set_allLists($propId=NULL){
	
		/*$this->load_model('Lists');
		$completeLists = $this->Lists->find(array('selects' => array('id', 'list_name')));
		$this->set('completeLists', $completeLists);*/

		global $wpdb;

		if($propId == 1){
			$prefix ="wp_";
		} else { 
			$prefix ="wp_".$propId."_";
		}

		$listSql = "SELECT * FROM `".$prefix."lists` WHERE `propertyId` = ".$propId." ";
		$completeLists = $wpdb->get_results($listSql);
		if(count($completeLists) > 0 ){
			$this->set('completeLists', $completeLists);
		}// else { }

	}

	private function set_category() {
	
		$this->load_model('Category');
		$categories = $this->Category->find(array('selects' => array('Id', 'category_name')));
		$this->set('categories', $categories);
	}

	private function set_categories($propertyId=FALSE,$catId=FALSE) {
	

		if($propertyId){
		
		   global $wpdb;

		   if($propertyId == 1){
			$prefix ="wp_";
		   } else { 
			$prefix ="wp_".$propertyId."_";
		   }
		   $Sql = "SELECT * FROM `".$prefix."categories` WHERE category_type='$catId'";
		   $categories=$wpdb->get_results($Sql);
		   $this->set('categories', $categories);
		   //print_r($cat);
		}else{
		  $this->load_model('Categories');
		  $categories = $this->Categories->find(array('selects' => array('id', 'category_name','active')));
		  $this->set('categories', $categories);
		}
//print_r($categories2);
	}
	
	public function manage_categories(){
	 	//$this->load_model('Categories');
		//$categories = $this->Categories->find(array('selects' => array('id', 'category_name','active')));
		global $wpdb;
		$categories = $wpdb->get_results("SELECT id,category_name,category_type  FROM {$wpdb->prefix}categories");
		$this->set('categories', $categories);
	}

	private function set_properties() {
		global $wpdb;
		//Only one table is there that can be accessed by base prefix(wp_)
		$psql = "SELECT * FROM `".$wpdb->base_prefix."properties` WHERE `prop_status` = 1";
		$properties = $wpdb->get_results($psql);
		if($wpdb->num_rows){
			$this->set('properties', $properties);
		}
	}

	private function set_lists($campaignId,$pid) {
		global $wpdb;
		$this->load_model('Lists');
		$lists = $this->Lists->find(array(
		'conditions' => array(
			'Lists.active' => 1,
			),
			'order' => 'Lists.list_name ASC',
		));
		$campListRel = 0;
		$this->set('lists', $lists);

		if($pid == 1){
			$prefix ="wp_";
		} else { 
			$prefix ="wp_".$pid."_";
		}

		$sql = "SELECT list_id
			FROM ".$prefix."campaign_list_rel
			WHERE `campaign_id` = $campaignId ";
		$campListRel = $wpdb->get_results($sql);

		if($wpdb->num_rows){
			$this->set('campListRel', $campListRel);
		}
	}

	/** SUJITH */
	##Changed function sujith 04 Oct 2012##
	function Settings() {
	global $wpdb;
	$this->set_properties(); // 15 Jan sam
	        $this->load_model('Sendgrid');
	        	//$sendlist = $this->Sendgrid->find(array('selects' => array("id",'Username',"password")));
	        	$sendlist=$wpdb->get_results("SELECT * FROM `".$wpdb->base_prefix."sendgrids`");		
           	        $this->set('sendlist', $sendlist[0]);
    }


	/** GLORY */
    ##Added new function glory 18 sept 2012##
	function Lists() {
		global $wpdb;
		$this->load_model('Lists');
		$this->set_list_category();
		$this->set_properties();		

		$rowPerpageli = 5;
		$objects = $this->Lists->find(array(
		    'conditions' => array(
			    'Lists.active' => 1,
			),
			'order' => 'Lists.list_name ASC',
			'page' => 1,
			'per_page' => $rowPerpageli,
			
		));
		$this->set('objects', $objects);
// 		foreach ($objects as $obj){	
// 			if($obj->import_type=='csv'){
// 				$sqllis = "SELECT count(*) as count FROM ".$wpdb->prefix."contacts WHERE list_id  = {$obj->id}";
// 				$reslis[$obj->id] =$wpdb->get_results($sqllis);
// 			}elseif($obj->import_type=='onesite'){
// 				
// 			}
// 			
// 		}
		
		$totalObjectsli = $this->Lists->find(
                                                array(
		                                            'conditions' => array(
			                                            'Lists.active' => 1,
			                                            ),
			                                            'order' => 'Lists.list_name DESC',
		                                            )
                                             );
		
		$totalli = count($totalObjectsli);
		$num_of_pagesli = ceil($totalli/$rowPerpageli);

// 		$this->set('contacts', $reslis);
		$this->set('objects', $objects);
		$this->set('num_of_pages', $num_of_pagesli);
		$this->set('rowPerpage', $rowPerpageli);
		$this->set_pagination($objects);
	}
	
	function deletelist(){
		global $wpdb;
		$this->load_model('Lists');
		$this->verify_id_param();
				
	$s_props=$_POST["s_props"];
	if($s_props){
           if($s_props=="1"){
                $prefix="wp_";
           }else{
                $prefix="wp_".$s_props."_";
           }
           $props=$s_props;
        }else{
                global $blog_id;
           $prefix=$wpdb->prefix;
           $props=$blog_id;
        }
        
		
		 $sql = "SELECT * FROM ".$prefix."lists WHERE id = {$this->params[id]}";
		$res = $wpdb->get_row($wpdb->prepare($sql));
		if(1){
			if($res->import_type=="csv"){
				$wpdb->query(
					$wpdb->prepare( 
						"
						DELETE FROM " .$prefix."contacts
						WHERE list_id=".$this->params['id']
					)
				);
			}elseif($res->import_type=="onesite"){
				$wpdb->query(
					$wpdb->prepare( 
						"
						DELETE FROM " .$prefix."list_rel_onesite
						WHERE list_id=".$this->params['id']
					)
				);
			}

			$wpdb->query(
				$wpdb->prepare( 
					"
					DELETE FROM " .$prefix."lists
					WHERE id=".$this->params['id']
				)
			);
			
		}
	}

	function listedit(){
		global $wpdb;
		$this->set_list_category();
		$this->load_model('Lists');
		$s_props=$_POST["s_props"];
	if($s_props){
           if($s_props=="1"){
                $prefix="wp_";
           }else{
                $prefix="wp_".$s_props."_";
           }
           $props=$s_props;
        }else{
                global $blog_id;
           $prefix=$wpdb->prefix;
           $props=$blog_id;
        }
        $lists2=$wpdb->get_results("select * FROM {$prefix}lists WHERE `id`='".$this->params['id']."'");
       // print_r($lists2);
		$lists = $this->Lists->find_by_id($this->params['id'], array('selects' => array('id','list_name','categoryId','description','tags','import_type')));

		$list_relations=$wpdb->get_results("SELECT * FROM ".$prefix."list_rel_onesite WHERE list_id='".$lists->id."'");

		$this->set('Lists_Rel', $list_relations);
		$this->set('Lists', $lists2[0]);
		$this->set('props', $props);
	}
	function set_list_category(){
	
	global $wpdb;
	$s_props=$_POST["s_props"];
		if($s_props){
           if($s_props=="1"){
                $prefix="wp_";
           }else{
                $prefix="wp_".$s_props."_";
           }
        }else{
           $prefix=$wpdb->prefix;
        }
        $categories2=$wpdb->get_results("SELECT * FROM {$prefix}categories WHERE category_type='2'");
        
		$this->load_model('Categories');
		$categories = $this->Categories->find(array('selects' => array('id', 'category_name'),
		'conditions' => array(
			'Categories.active' => 1,
			'Categories.category_type' => 2,
			),'order' => 'Categories.id ASC',
		));
		$this->set('categories', $categories);
                $this->set('categories2', $categories2);
	}
	## ends##

	/** VIDHU */

	function templates(){
		$this->load_model('Templates');
		$rowPerpage = 5;
		$objects = $this->Templates->find(array(
			'order' => 'Templates.id DESC',
			'page' => 1,
			'per_page' => $rowPerpage,
		));
		$this->set('objects', $objects);
		$this->set_pagination($objects);
		$totalObjects = $this->Templates->find(array(
		'conditions' => array(
			'Templates.active' => 1,
			),
			'order' => 'Templates.id Desc',
		));
		
		$total = count($totalObjects);
		$num_of_pages = ceil($total/$rowPerpage);


		$this->set('num_of_pages', $num_of_pages);
		$this->set('totalresult', $total);
		$this->set('rowPerpage', $rowPerpage);
		$this->set_pagination($objects);
	}
	function templateedit(){
		$this->load_model('Templates');
		$this->set_templates();
		$templates = $this->Templates->find_by_id($this->params['id'], array('selects' => array('id','template_name','preview','header','footer','created_date','previewImgName')));
		$this->set('Templates', $templates);
	}
	function addTemplate(){
		$this->load_model('Templates');
		$this->set_templates();
		$this->create_or_save();
	}
	function deleteTemplates(){
		global $wpdb;
		$this->load_model('Templates');
		$this->verify_id_param();
		 $sql = "SELECT * FROM ".$wpdb->base_prefix."templates WHERE id = {$this->params[id]}";
		$res = $wpdb->get_row($wpdb->prepare($sql));
		if(1){
			$wpdb->query(
				$wpdb->prepare( 
					"
					DELETE FROM " .$wpdb->prefix."templates
					WHERE id =".$this->params['id']
				)
			);
		}
	}
}
?>
