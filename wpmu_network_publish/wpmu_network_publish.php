<?php
/*
Plugin Name: WPMU Network Publlish
Plugin URI: http://deepakoberoi.com/wpmu-network-publish/
Description: A Plugin that let's you publish posts/pages to a wordpress multisite website.
Version: 1.01
Author: Deepak Oberoi
Author URI: http://deepakoberoi.com
Author Email: deepak@dmarkweb.com
*/

if ( !defined('ABSPATH') ) die('No Direct Access!');

require_once( 'wpmu_network_publish_base.php' );
require_once( 'wpmu_network_publish.class.php' );

$wpmu_network_publish = new WPMU_Network_Publish();


function display_np_menu($type = 1){
    global $wpmu_network_publish;
    if(is_numeric($type))
        return $wpmu_network_publish->__display_np_menu($type);
    else
        return $wpmu_network_publish->__display_np_menu_alias($type);
}
