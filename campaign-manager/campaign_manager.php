<?php
/*
Plugin Name: Campaign Manager
Plugin URI: 
Description: Campaign Manager
Author: Unknown, for now
Version: 1.0
Author URI: http://localhost/
License: GNU General Public License
*/


register_activation_hook(__FILE__, 'campaign_manager_activate');
register_deactivation_hook(__FILE__, 'campaign_manager_deactivate');

function campaign_manager_activate() {
	require_once dirname(__FILE__).'/campaign_manager_loader.php';
	$loader = new CampaignManagerLoader();
	$loader->activate();
}

function campaign_manager_deactivate() {
	require_once dirname(__FILE__).'/campaign_manager_loader.php';
	$loader = new CampaignManagerLoader();
	$loader->deactivate();
}

?>
