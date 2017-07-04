<?php
/**
 * @package Wordpress
 * @version 1.2
 */
/*
Plugin Name: Meditation Timer
Plugin URI: http://dmarkweb.com/plugin/meditation-timer
Description: This plugins will allow user to create an HTML5 based meditation Timer.
Author: DM+
Version: 1.1
Author URI: http://dmarkweb.com/
*/

if(!defined('ABSPATH')) exit;
//exit if accessed directly

require_once( dirname(__FILE__) . '/meditation-widget.php' );


class MeditationTimer {
    
    public function __construct() {
        
        register_activation_hook( __FILE__, array( &$this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );
        add_action('init', array(&$this, 'dm_setup'), 1);
//        add_action('init', array(&$this, 'jquery_cdn'));
        add_action('wp_footer', array(&$this, 'load_scripts'));
        
    }
    
    
    /**
    * Function that'll run on Plugin activation
    * 
    */
    private function activate(){
//        add_option('dm_frm_options', $this->defaults['options'], '', 'no');
//        update_option('dm_frm_version', $this->defaults['version'], '', 'no');
    }
    
    /**
    * Function that'll run on Plugin De-activation
    * 
    */
    private function deactivate(){
        
    }
    
    /**
     * Loads required filters
    */
    public function dm_setup()
    {
        global $pagenow;
//        if($pagenow === 'wp-login.php') { }
        add_shortcode( 'medtimer', array( &$this, 'meditation_timer' )  );
        
    }
    
    public function load_scripts(){
        echo '<link href="'. plugins_url( 'meditation-timer/css/timer.css' , dirname(__FILE__) ).'" type="text/css" rel="stylesheet">'
        ?>
        <script type="text/javascript">
//            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
            var audio_url_path = '<?php echo plugins_url( 'meditation-timer/media' , dirname(__FILE__) ); ?>';
            <?php echo file_get_contents(dirname(__FILE__).'/js/meditationtimer.js'); ?>
        </script>
        <?php
    }
    
    function jquery_cdn() {
       if (!is_admin()) {
          wp_deregister_script('jquery');
          wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js', false, '1.11.1');
          wp_enqueue_script('jquery');
       }
    }

    
    

    public function meditation_timer( $atts, $content = "" ) {
            $args = shortcode_atts(
                                array(
                                    'id'                => '',
                                    'order'             => 'desc' ,
                                    'type'              =>'' ,
                                    'class'             =>''
                                    ), 
                                    $atts
                                );
                                
            $content = file_get_contents( dirname(__FILE__) . '/tmpl/timer_html.php' );
            return '<div class="mtContainer '.$args['class'].'">'.$content.'</div>';
            
            
    }
    
    
    
    

}

$medTimer = new MeditationTimer();