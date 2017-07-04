<?php

//MvcRouter::public_connect('campaigns', array('controller' => 'documentation_nodes', 'action' => 'index'));
/*MvcRouter::public_connect('documentation/{:id:[\d]+}.*', array('controller' => 'documentation_nodes', 'action' => 'show'));
MvcRouter::public_connect('search', array('controller' => 'documentation_nodes', 'action' => 'search'));
MvcRouter::public_connect('{:controller}', array('action' => 'index'));
MvcRouter::public_connect('{:controller}/{:id:[\d]+}', array('action' => 'show'));
MvcRouter::public_connect('{:controller}/{:action}/{:id:[\d]+}');*/
 

//admin_campaigns_controller
//MvcRouter::public_connect('', array('controller' => 'admin_campaigns_controller', 'action' => 'index'));
//MvcRouter::public_connect('{:controller}/{:action}/{:id:[\d]+}');
 
MvcRouter::admin_ajax_connect(array('controller' => 'admin_campaigns', 'action' => 'ajaxedit'));
MvcRouter::admin_ajax_connect(array('controller' => 'admin_campaigns', 'action' => 'deletecampaign'));
MvcRouter::admin_ajax_connect(array('controller' => 'admin_campaigns', 'action' => 'templateedit'));
MvcRouter::admin_ajax_connect(array('controller' => 'admin_campaigns', 'action' => 'deleteTemplates'));
MvcRouter::admin_ajax_connect(array('controller' => 'admin_campaigns', 'action' => 'listedit'));
MvcRouter::admin_ajax_connect(array('controller' => 'admin_campaigns', 'action' => 'deletelist'));
MvcRouter::admin_ajax_connect(array('controller' => 'admin_campaigns', 'action' => 'deletemessage'));
//MvcRouter::admin_ajax_connect(array('controller' => 'admin_documentation_nodes', 'action' => 'preview_content'));

?>
