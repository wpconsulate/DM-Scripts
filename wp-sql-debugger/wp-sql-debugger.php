<?php
/*
Plugin Name: WP SQL Debugger
Plugin URI: http://deepakoberoi.com
Description: Plugin Helps you debug the SQL Queries in Wordpress
Author: Peter 
Version: 1.1
Author URI: http://deepakoberoi.com
*/

/*WP_DEBUG is a PHP constant (a permanent global variable) that can be used to trigger the "debug" mode throughout WordPress. 
It is assumed to be false by default and is usually set to true in the wp-config.php file on development copies of WordPress.*/
//define( 'WP_DEBUG', true );
/*SCRIPT_DEBUG is a related constant that will force WordPress to use the "dev" versions of core CSS and Javascript files rather than the minified versions 
that are normally loaded. This is useful when you are testing modifications to any built-in .js or .css files. Default is false.*/
define( 'SCRIPT_DEBUG', true);
/*The SAVEQUERIES definition saves the database queries to an array and that array can be displayed to help analyze those queries. 
The constant defined as true causes each query to be saved, how long that query took to execute, and what function called it.*/
define( 'SAVEQUERIES', true);


if(!function_exists('pr')){
    function pr($arr, $die = 0){
        echo "<pre>";
            print_r($arr);
        echo ($die)?die():"</pre>";
    }
}

function _scripts() {
        wp_enqueue_style( 'sql-debugger-css', plugins_url( 'inc/assets/sql-debugger.css', __FILE__ ) );
        wp_enqueue_script( 'sql-debugger-js', plugins_url( 'inc/assets/sql-debugger.js', __FILE__ ) , array(), '1.0.0', true );
    }
add_action( 'wp_head', '_scripts', 6 );

add_action( 'wp_footer', 'debug_sql', 999 );
function debug_sql(){
    global $wpdb;
    echo '<a href="#debug_contents" id="debug_handle">Debugger</a>';
    echo '<div id="debug_contents">';
        pr($wpdb->queries);
    echo '</div>';
}

//add_action( 'all', create_function( '', 'pr( current_filter() );' ) );