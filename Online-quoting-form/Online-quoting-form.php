<?php
/*
Plugin Name: Online Quoting Form
Description: Form Plugin for Online Quoting form on  parklifelandscaping.com.au . Add shortcode [Online_quoting_form] in the content area where you want to use this form.
Version: 1.0.9
Author: DM+
Author URI: http://deepakoberoi.com/
Plugin URI: http://deepakoberoi.com/plugins/math-captcha/
License: MIT License
basename: dm_frm
License URI: http://opensource.org/licenses/MIT
*/

if(!defined('ABSPATH'))    exit; //exit if accessed directly

new EnquiryForm();

class EnquiryForm {
    
    
    public $defaults = array(
                            'verion'=>'1.2.1',
                            'options' => array(
                                                'deactivation_delete'=>'no'
                                                ),
                            'upload_dir' => "/uploads/Online-quoting-form",
                            );
    
    public function __construct()
    {
        
        register_activation_hook(__FILE__, array(&$this, 'activation'));
        register_deactivation_hook(__FILE__, array(&$this, 'deactivation'));

        //changes from older versions
        $db_version = get_option( 'dm_frm_version' );

        //actions
//        add_action('plugins_loaded', array(&$this, 'load_textdomain'));

        add_action('init', array(&$this, 'load_actions_filters'), 1);
        add_action('wp_footer', array(&$this, 'load_scripts'));
        
        //Admin hooks
        //add_action('admin_init', array(&$this, 'admin_register_settings'));
//        add_action('admin_menu', array(&$this, 'admin_menu_options'));
//        add_action('admin_enqueue_scripts', array(&$this, 'admin_comments_scripts_styles'));
        
        /*set email content-type*/
        add_filter( 'wp_mail_content_type', array(&$this, 'set_content_type') );
        
        add_action('wp_ajax_dm_frm_enquiry_upload', array(&$this, 'upload_callback') );
        add_action('wp_ajax_nopriv_dm_frm_enquiry_upload', array(&$this, 'upload_callback') );

        add_action('wp_ajax_dm_frm_enquiry_submit', array(&$this, 'submit_callback') );
        add_action('wp_ajax_nopriv_dm_frm_enquiry_submit', array(&$this, 'submit_callback') );

        
        
    }
    
    /**
     * Activation
    */
    public function activation() {
        add_option('dm_frm_options', $this->defaults['options'], '', 'no');
        update_option('dm_frm_version', $this->defaults['version'], '', 'no');
    }


    /**
     * Deactivation
    */
    public function deactivation()
    {
        if($this->options['options']['deactivation_delete'] === TRUE)
        {
            delete_option('dm_frm_options');
            delete_option('dm_frm_version');
        }
    }
    
    /**
     * Loads required filters
    */
    public function load_actions_filters()
    {
        global $pagenow;

        if($pagenow === 'wp-login.php') { }
        
        
        add_shortcode( 'Online_quoting_form', array(&$this, 'handle_shortcode') );
        
    }
    
    public function load_scripts(){
        
        ?>
        
        <link href="<?php echo plugins_url( 'Online-quoting-form/css/ajaxfileupload.css' , dirname(__FILE__) )?>" type="text/css" rel="stylesheet">
        <script type="text/javascript" src="<?php echo plugins_url( 'Online-quoting-form/js/ajaxfileupload.js' , dirname(__FILE__) )?>"></script>
        <script type="text/javascript">
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        
        jQuery(document).ready(function(){
            
//            jQuery('.dm_frm_send').on('click', function(event){
            jQuery('#fileToUpload').on('change', function(event){
                
                jQuery('body').ajaxStart(function(){
                    jQuery("#loading").show();
                })
                .ajaxComplete(function(){
                    jQuery("#loading").hide();
                });

                jQuery.ajaxFileUpload({
                        url: ajaxurl,
                        secureuri:false,
                        fileElementId:'fileToUpload',
                        dataType: 'json',
//                        data:{action:'dm_frm_enquiry_submit'},
                        data:{action:'dm_frm_enquiry_upload'},
                        success: function (data, status) {
                            if(typeof(data.error) != 'undefined')
                            {
                                if(data.error != '') {
                                    alert(data.error);
                                }else
                                {
                                    jQuery('#Enquiry_form').append('<input type="hidden" name="file_attached" value='+data.path+' />');
                                    jQuery('#Enquiry_form .upload-btn').append('<img id="uploaded_t" style="width: 50px; vertical-align: top;" src='+data.src+' />');
//                                    alert(data.msg);
                                }
                            }
                        },
                        error: function (data, status, e)
                        {
                            alert(e);
                        }
                    }
                )

                return false;

            });
            
            
            
            
            jQuery('#dm_frm_overlay, #dm_frm_overlay_img').on('click', function(event){
                jQuery("#dm_frm_overlay").hide();
                jQuery("#dm_frm_overlay_img").hide();
                location.reload();
            });
            
            jQuery('.dm_frm input#send').on('click', function(event){
                
                event.preventDefault();
                var submitButton = jQuery( this );
                
                var form = jQuery( 'form#Enquiry_form' );
                $postData = jQuery('#Enquiry_form').serialize();
               // alert(ajaxurl+"?action=enquiry_frm_submit");
                var jqxhr = jQuery.ajax({
                                          type: "POST",
                                          url: ajaxurl+"?action=dm_frm_enquiry_submit",
                                          data: $postData
                                      })
                                      .done(function(response) {
                                      
                                            $res = jQuery.parseJSON(response);
                                            if(! $res.error){
//                                       jQuery(".dm_frm .captcha-box").html($res.captcha_html);
                                                jQuery("#dm_frm_overlay").show();
                                                jQuery("#dm_frm_overlay_img").show();
                                                jQuery("#uploaded_t").remove();
                                                document.getElementById(form.attr('id')).reset();
                                            } else {
                                                alert($res.msg);
                                            }
                                            
                                      })
                                      .fail(function() {
                                          alert( "Error Processing your request please try again!" );
                                      })
                                      .always(function(msg) {

                                      });
                
            }); 
            
            
            
        });
        </script>
        
        <style type="text/css">
            .file-wrapper {
              cursor: pointer;
              display: inline-block;
              overflow: hidden;
              position: relative;
            }
            
            .file-wrapper .button {
              background: #79130e;
              -moz-border-radius: 5px;
              -webkit-border-radius: 5px;
              border-radius: 5px;
              cursor: pointer;
              display: inline-block;
              font-size: 11px;
              font-weight: bold;
              padding: 8px 25px;
              color: #FFFFFF;
              text-transform: uppercase;
            }
            .file-wrapper input {
              cursor: pointer;
              height: 100%;
              position: absolute;
              right: 0;
              top: 0;
              font-size: 100px;
              filter: alpha(opacity=50);
              -moz-opacity: 0.5;
              opacity: 0.5;
            }
        </style>
        
        <?php
    }
    
    public function handle_shortcode($atts){
        
        extract(
                    shortcode_atts(
                                array(
                                    'class_name'        => '',
                                    'totalposts'        => '-1',
                                    'category'          => '',
                                    'thumbnail'         => 'false',
                                    'height'            => '130',
                                    'width'             => '130',
                                    'date'              => 'false',
                                    'excerpt'           => 'true',
                                    'orderby'           => 'post_date',
                                    'id'                => '',
                                    'order'             => 'desc' ,
                                    'type'              =>'' ,
                                    'count'             =>'250'
                                    ), 
                                    $atts
                                )
                    );
                                    
        $this->generate_form();
                                    
        
                                
    }

    public function generate_form(){
        global $post;
        ?>
        
       <div class="dm-box">
           
           <div class="inner">
            <div class="order_form_head">
                <h2>Get a<span> Quick </span>Quote Here<!-- <img class="arrowred" src="<?php echo plugins_url( 'Online-quoting-form/img/' , dirname(__FILE__) );?>/arrowred.png">-->
                </h2>
            </div>
                <div class="dm_frm">
                        
                        <?php
//                        print_r($post->ID); die();
                         
                          $id = $post->ID;
                          $post_title = get_the_title($id); 
                         // $upload = get_field("show_upload_button", $id);
                         
                         /*checkboxes*/
                          $checkboxes_val=array('Paving','Retaining Walls','Decking','Lawn+Retic','Concreting','Mini Bobcat Wire','Fencing','Pool Installation');

                          //print_r($checkboxes_val);
                          ?>
                        <div class="order_query">
                        <p><?php // $value_heading = get_field("order_query_form_heading", $id);  echo $value_heading; ?></p>
                        </div> 
                        
                        <form action="" method="post" id="Enquiry_form" enctype="multipart/form-data"> 
                  
                         <input type="hidden" name="title" id="title" value="<?php echo $post_title; ?>">
                         <input type="hidden" name="post_id" id="post_id" value="<?php echo $post->ID; ?>">
                         
                       <!-- <h2>Provide Contact Info</h2>-->
                        <div class="contact-details">
                         <div class="contact-details-inner">
                            <h4> 1. Contact Details</h4>
                            <p>
                                <label>Name</label>
                                <input type="text" name="fname" id="fname" placeholder="">
                            </p>
                            <p>
                                <label>Email</label>
                                <input type="text" name="emailid" id="emailid" placeholder="">
                            </p>
                            <p>
                                <label>Phone</label>
                                <input type="text" name="phone" id="phone" placeholder="">
                            </p>
                            <p>
                                <label>Mobile</label>
                                <input type="text" name="mobile" id="mobile" placeholder="">
                            </p>
                            <p>
                                <label>Address</label>
                                <input type="text" name="address" id="address" placeholder="">
                            </p>
                        </div>
                        
                        <div class="nature-container">
                            <h4> 2. Nature of Work</h4>
                          
                            <?php 
                            /*$Key == 0;
                            foreach($checkboxes_val as $val){ 
                           echo' <p> <input type="checkbox" name="nature_work[]" id="'.$Key.'" value="'.$val.'"><span>'.$val.'</span>  </p>';
                            $Key++;
                            }*/?>
                        <p><input type="checkbox" name="nature_work[]" id="1" value="Paving"><span>Paving</span></p>
                            <p><input type="checkbox" name="nature_work[]" id="2" value="Retaining Walls"><span>Retaining Walls</span></p>
                            <p><input type="checkbox" name="nature_work[]" id="3" value="Decking"><span>Decking</span></p>
                            <p><input type="checkbox" name="nature_work[]" id="4" value="Lawn + Retic"><span>Lawn+Retic</span></p>
                            <p><input type="checkbox" name="nature_work[]" id="5" value="Concreting"><span>Concreting</span></p>
                            <p><input type="checkbox" name="nature_work[]" id="6" value="Mini Bobcat Wire"><span>Mini Bobcat Wire</span></p>
                            <p><input type="checkbox" name="nature_work[]" id="7" value="Fencing"><span>Fencing</span></p>
                            <p><input type="checkbox" name="nature_work[]" id="8" value="Pool Installation"><span>Pool Installation</span></p>
                            <br class="clr" />
                        </div>
                        
                        <div class="council-container">
                            <h4> 3. Council Approval</h4>
                            <p>
                            <label>Required But Do Not Have</label>
                            <input type="radio" name="council_approval" id="council_approval" value="Required_But_Do_Not_Have">
                            </p>
                            <p>
                            <label>Required And Approved</label>
                            <input type="radio" name="council_approval" id="council_approval" value="Required_And_Approved">
                            </p>
                            <p>
                            <label>Not Required </label>
                            <input type="radio" name="council_approval" id="council_approval" value="Not_Required">
                            </p>
                            <p>
                            <label>Don't Know</label>
                            <input type="radio" name="council_approval" id="council_approval" value="Don't_Know">
                            </p>
                        </div>
                            
                        <div class="tell_us_content">
                            <h4> 4. Tell Us About Your Project </h4>
                            <p>Provide a Brief Work Description (Optional) </p>
                            <p><textarea name="work_desc" id="work_desc" cols="20" rows="5"></textarea></p>
                            
                            <h4> 5. Upload Photo (Optional) </h4>
                            <p>You can upload your drawings or photo of the project or yard to help us with your quote.</p>
                            <span class="file-wrapper upload-btn">
                                <input type="file" name="fileToUpload" id="fileToUpload" value="upload">
                                <span class="uplod-btn">
                                    <a href="#">Upload Image</a>
                                </span>
                            </span>
                        </div>
                            
                            <!--<input type="button" class="dm_frm_send"  name="send" id="send" value="get quote">  -->
                        
                        <!--<p>
                            <label>Suberb</label>
                            <input type="text" name="suberb" id="suberb" >
                        </p>

                        <p class="half_left">
                            <label>State </label>
                            <input type="text" name="state" id="state">
                        </p>
                        <p class="half_right">
                            <label>P.C. </label>
                            <input type="text" name="post_code" id="post_code">
                        </p>
                        
                          <p>
                            <input type="text" name="Business" id="Business" >
                        </p>
                        
                        <div class="query">
                            <p><label>Comment or Query</label><br>
                                <textarea rows="4" cols="50" name="comment" id="comment" style="width: 195px; height: 38px;"></textarea>
                            </p>-->
                        
                          <div class="captcha-box">
                           <h2>Enter Code + send <br /></h2>
                                <?php
                                if( function_exists( 'cptch_display_captcha_custom' ) ) { 
                                echo "<input type='hidden' name='cntctfrm_contact_action' value='true' />"; 
                                echo cptch_display_captcha_custom();
                                    }
                                ?>
                            <input type="button" class="dm_frm_send"  name="send" id="send" value="Send">     
                            </div>
                            
                        </div>
                        </div>

                    </form>
                </div>
                <div id="dm_frm_overlay" style="background: rgba( 0,0,0 ,0.7);position: fixed; width: 100%; top: 0px; left: 0px; right: 0px; bottom: 0px; display: none;z-index: 9990;"></div>
                <img id="dm_frm_overlay_img" src="<?php echo content_url() .'/plugins/Online-quoting-form/img/thank-you-image.png'?>" alt="" style="position: fixed; right: 0px; bottom: 0px; left: 50%; cursor: pointer; z-index: 9999; display: none;margin-left: -368px; top: 150px;" />
                <img id="loading" src="<?php echo plugins_url( 'Online-quoting-form/img/loading.gif' , dirname(__FILE__) )?>" style="position: fixed; right: 0px; bottom: 0px; left: 50%; cursor: pointer; z-index: 9999; display: none;margin-left: -16px; top: 150px;">
            
           </div>
       </div>
        
        <?php
    }
    
    
    function set_content_type( $content_type ){
        return 'text/html';
    }

    public function upload_callback(){
        $error = "";
        $msg = "";
        $fileElementName = 'fileToUpload';
        if(!empty($_FILES[$fileElementName]['error'])) {
                switch($_FILES[$fileElementName]['error'])
                {

                    case '1':
                        $error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
                        break;
                    case '2':
                        $error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                        break;
                    case '3':
                        $error = 'The uploaded file was only partially uploaded';
                        break;
                    case '4':
                        $error = 'No file was uploaded.';
                        break;

                    case '6':
                        $error = 'Missing a temporary folder';
                        break;
                    case '7':
                        $error = 'Failed to write file to disk';
                        break;
                    case '8':
                        $error = 'File upload stopped by extension';
                        break;
                    case '999':
                    default:
                        $error = 'No error code avaiable';
                }
            }elseif(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none')
            {
                $error = 'No file was uploaded..';
            }
            else {
                
                $msg .= " File Name: " . $_FILES[$fileElementName]['name'] . ", ";
                $msg .= " File Size: " . @filesize($_FILES[$fileElementName]['tmp_name']);
             //   move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],plugins_url( 'enquiry-form/img/'.$_FILES['fileToUpload']['name']));

                
                $upload_dir = WP_CONTENT_DIR . $this->defaults['upload_dir'];
                if( !is_dir( $upload_dir ) ) mkdir($upload_dir, 0777);
                
                $_uploaded_file = $this->defaults['upload_dir'] . '/' . $_FILES[$fileElementName]["name"];
                $uploaded_file_path = $upload_dir . '/' . $_FILES[$fileElementName]["name"];
                $uploaded_thumb_path = $upload_dir . '/thumb_' . $_FILES[$fileElementName]["name"];
                $uploaded_file_URL = content_url( $this->defaults['upload_dir'] ) . '/thumb_' . $_FILES[$fileElementName]["name"];
                
                move_uploaded_file($_FILES[$fileElementName]["tmp_name"], $uploaded_file_path);
                
                $image = wp_get_image_editor( $uploaded_file_path );

                if (!is_wp_error($image)) {
                    $image->resize(50, 50, false);
                    $image->save($uploaded_thumb_path);
                }else{
                    $msg .= "Image Err: ". $image->get_error_message();
                }
                
            }
            
            
            $return['error'] = $error;
            $return['msg'] = $msg;
            $return['src'] = $uploaded_file_URL;
            $return['full_path'] = $uploaded_file_path;
            $return['path'] = $_uploaded_file;
            
   //   $return['msg']= WP_CONTENT_DIR."/uploads/" . $_FILES['fileToUpload']['name'];
//            $return['msg']= plugins_url( 'enquiry-form/img/'.$_FILES['fileToUpload']['name']);
//            echo $_FILES['fileToUpload']['name']; //die();
            
            echo json_encode($return);
        
          die();
    }
    
    public function submit_callback(){
            $return['error']=0;
            $return['msg']='';
            
            
            if( !isset($_POST['fname']) || trim($_POST['fname']) == '' ){
                $return['error']=1;
                $return['msg']='Enter Full name!';                    echo json_encode($return);die();
            }
            if( !isset($_POST['emailid']) || trim($_POST['emailid']) == '' ){
                $return['error']=1;
                $return['msg']='Email is Required!';            echo json_encode($return);die();
            }
            if( !isset($_POST['phone']) || trim($_POST['phone']) == '' ){
                $return['error']=1;
                $return['msg']='Phone Number should not be empty!';      echo json_encode($return);die();
            }
            
            if( !isset($_POST['mobile']) || trim($_POST['mobile']) == '' ){
                $return['error']=1;
                $return['msg']='Please Enter Mobile Number !';      echo json_encode($return);die();
            }
            if( !isset($_POST['address']) || trim($_POST['address']) == '' ){
                $return['error']=1;
                $return['msg']='Please Enter Your Address !';      echo json_encode($return);die();
            }
            
            if( !isset($_POST['nature_work']) || $_POST['nature_work'] == '' ){
                $return['error']=1;
                $return['msg']='Select the Nature of Work !';      echo json_encode($return);die();
            }
             
            /* if( !isset($_POST['nature_work']) || trim($_POST['nature_work']) == '' ){
                $return['error']=1;
                $return['msg']='Select the Nature of Work !';      echo json_encode($return);die();
            }*/
            
             if( !isset($_POST['council_approval']) || trim($_POST['council_approval']) == '' ){
                $return['error']=1;
                $return['msg']='Select atleast one option for Council Approval !';      echo json_encode($return);die();
            }
             
           
          
            
            if( function_exists( 'cptch_check_custom_form' ) && cptch_check_custom_form() !== true ){
                $return['error']=1;
                $return['msg']='Captcha Code Incorrect!';
            }else{
                //Captcha OKAY
                
                $email_message = $this->get_email_content($_POST);
               // print_r($email_message); die();
                //$attachments = array( WP_CONTENT_DIR . '/' . $_POST['file_attached'] );
                $headers = 'From: Online Quoting Form <info@parklifelandscaping.com.au/>' . "\r\n";
              //  $headers = 'From: Online Quoting Form <neem.chand@dmarkweb.com>'. "\r\n";
                
//              $admin_email = 'd@dmarkweb.com'; 
              $admin_email = get_settings('admin_email'); 
               /// print_r($admin_email); die();
                wp_mail( $admin_email, 'enquiry Form Submission!', $email_message, $headers, $attachments );

//                print_r($return); die();
            }
        
        
            //echo json_encode($return);
             echo json_encode($return);
        
          die();
    }
    
    
    public function get_email_content($post){
    
        $message =  '<p>Hello Admin,</p>';
        $message =  '<p>&nbsp;</p>';
        $message .= '<p>Following Info has been submitted on Enquiry Form!</p>';
      /* $message .= '<p>PAGE TITLE : <a href="'.get_permalink($post['post_id']).'">'. $post['title'].'</a></p>'; */ 
        $message .= '<p>FULLNAME: '.$post['fname'].'</p>';
        $message .= '<p>EMAIL: '.$post['emailid'].'</p>';
        $message .= '<p>PH NUMBER: '.$post['phone'].'</p>';
        $message .= '<p>Mobile: '.$post['mobile'].'</p>';
        //$message .= '<p>Nature of Work: '.$post['nature_work'].'</p>';
        $message .= '<p>Nature of Work: '.implode(', ', $_POST['nature_work']).'</p>';  
        $message .= '<p>Council Approval: '.$post['council_approval'].'</p>';
        $message .= '<p><h3>Work Description</h3>: '.$post['work_desc'].'</p>';
        
//        $email_contents = file_get_contents(dirname(__FILE__).'/tmpl/email.php');
        ob_start();
            require_once( dirname(__FILE__).'/tmpl/email.php');
            $email_contents = ob_get_contents();
        ob_end_clean();

//        print_r($email_contents); die();

        $tplImageURL = content_url() .'/plugins/Online-quoting-form/tmpl/images';
        $toReplace['IMAGE_PATH'] = $tplImageURL;
       // $toReplace['LOGO'] = 'http://vernedwards.com/wp-content/uploads/2013/12/logo.jpg';
        $toReplace['LOGO'] = $tplImageURL.'/logo.jpg';
        $toReplace['DATE'] = date("F j, Y");
        $toReplace['TITLE'] = 'Online Quoting Form Submission!';
        $toReplace['MESSAGE'] = $message;
        
        return $this->do_replace($email_contents, $toReplace);
        
    }
    
    function do_replace($txt, $to_replace_array){

        foreach($to_replace_array as $key=>$val){
            $txt = str_replace('{'.$key.'}', $val, $txt);
            // echo $key."=".$val;
        }

        return $txt;
    }
    


    /**
     * Registers settings
    */
    public function admin_register_settings() {
        //inline edit
        register_setting('math_captcha_options', 'math_captcha_options', array(&$this, 'validate_configuration'));
        add_settings_section('math_captcha_settings', __('Math Captcha settings', 'math-captcha'), '', 'math_captcha_options');
        add_settings_field('mc_enable_for', __('Enable Math Captcha for', 'math-captcha'), array(&$this, 'mc_enable_captcha_for'), 'math_captcha_options', 'math_captcha_settings');
        add_settings_field('mc_hide_for_logged_users', __('Hide for logged in users', 'math-captcha'), array(&$this, 'mc_hide_for_logged_users'), 'math_captcha_options', 'math_captcha_settings');
        add_settings_field('mc_mathematical_operations', __('Mathematical operations', 'math-captcha'), array(&$this, 'mc_mathematical_operations'), 'math_captcha_options', 'math_captcha_settings');
        add_settings_field('mc_groups', __('Display captcha as', 'math-captcha'), array(&$this, 'mc_groups'), 'math_captcha_options', 'math_captcha_settings');
        add_settings_field('mc_title', __('Captcha field title', 'math-captcha'), array(&$this, 'mc_title'), 'math_captcha_options', 'math_captcha_settings');
        add_settings_field('mc_time', __('Captcha time', 'math-captcha'), array(&$this, 'mc_time'), 'math_captcha_options', 'math_captcha_settings');
        add_settings_field('mc_deactivation_delete', __('Deactivation', 'math-captcha'), array(&$this, 'mc_deactivation_delete'), 'math_captcha_options', 'math_captcha_settings');
    }


    //------------------------------------------------------------------------------------------------------------------------
    //      BOF Admin Settings
    //------------------------------------------------------------------------------------------------------------------------
    
            /**
             * Adds options menu
            */
            public function admin_menu_options()
            {
                $watermark_settings_page = add_options_page(
                    __('Math Captcha', 'math-captcha'),
                    __('Math Captcha', 'math-captcha'),
                    'manage_options',
                    'math-captcha',
                    array(&$this, 'options_page')
                );
            }


            /**
             * Shows options page
            */
            public function options_page()
            {
                echo '
                <div class="wrap">'.screen_icon().'
                    <h2>'.__('Math Captcha', 'math-captcha').'</h2>
                    <div class="metabox-holder postbox-container math-captcha-settings">
                        <form action="options.php" method="post">';

                wp_nonce_field('update-options');
                settings_fields('math_captcha_options');
                do_settings_sections('math_captcha_options');
                submit_button('', 'primary', 'save_math_captcha_options', TRUE);

                echo '
                        </form>
                    </div>
                    <div class="df-credits postbox-container">
                        <h3 class="metabox-title">'.__('Math Captcha', 'math-captcha').'</h3>
                        <div class="inner">
                            <h3>'.__('Need support?', 'math-captcha').'</h3>
                            <p>'.__('If you are having problems with this plugin, please talk about them in the', 'math-captcha').' <a href="http://dfactory.eu/support/" target="_blank" title="'.__('Support forum','math-captcha').'">'.__('Support forum', 'math-captcha').'</a></p>
                            <hr />
                            <h3>'.__('Do you like this plugin?', 'math-captcha').'</h3>
                            <p><a href="http://wordpress.org/support/view/plugin-reviews/wp-math-captcha" target="_blank" title="'.__('Rate it 5', 'math-captcha').'">'.__('Rate it 5', 'math-captcha').'</a> '.__('on WordPress.org', 'math-captcha').'<br />'.
                            __('Blog about it & link to the', 'math-captcha').' <a href="http://dfactory.eu/plugins/math-captcha/" target="_blank" title="'.__('plugin page', 'math-captcha').'">'.__('plugin page', 'math-captcha').'</a><br />'.
                            __('Check out our other', 'math-captcha').' <a href="http://dfactory.eu/plugins/" target="_blank" title="'.__('WordPress plugins', 'math-captcha').'">'.__('WordPress plugins', 'math-captcha').'</a>
                            </p>            
                            <hr />
                            <p class="df-link">Created by <a href="http://www.dfactory.eu" target="_blank" title="dFactory - Quality plugins for WordPress"><img src="'.plugins_url('/images/logo-dfactory.png' , __FILE__ ).'" title="dFactory - Quality plugins for WordPress" alt="dFactory - Quality plugins for WordPress" /></a></p>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>';
            }


            /**
             * Enqueues scripts and styles (admin side)
            */
            public function admin_comments_scripts_styles($page)
            {
                if(is_admin() && $page === 'settings_page_math-captcha')
                {
                    wp_enqueue_script(
                        'math-captcha',
                        plugins_url('/js/admin.js', __FILE__),
                        array('jquery', 'jquery-ui-core', 'jquery-ui-button')
                    );

                    wp_enqueue_style('math-captcha-admin', plugins_url('/css/admin.css', __FILE__));
                    wp_enqueue_style('math-captcha-front', plugins_url('/css/wp-like-ui-theme.css', __FILE__));
                }
            }


            /**
             * Loads textdomain
            */
            public function load_textdomain()
            {
                load_plugin_textdomain('math-captcha', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
            }
    
    //------------------------------------------------------------------------------------------------------------------------
    //      EOF Admin Settings
    //------------------------------------------------------------------------------------------------------------------------

    
}
