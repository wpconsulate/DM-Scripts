<?php
class WPMU_Network_Publish extends WPMU_Network_Publish_Base {

    protected $site_options = array(

        'always_use_required_list' => false,                    // Require blogs only when publishing to network?
        'blacklist' => '',                                      // Comma-separated string of blogs to automatically exclude
        'canonical_url' => true,                                // Override the canonical URLs with the parent post's.
        'custom_field_exceptions' => array( '_wp_page_template', '_wplp_', '_aioseop_' ),                
                                                                // Custom fields that should be networkPublished, even though they start with_
        'save_post_priority' => 640,                            // Priority of save_post action. Higher = lets other plugins do their stuff first
        'override_child_permalinks' => false,                   // Make the child's permalinks link back to the parent item?
        'post_types' => array(
                                'post' => array(), 
                                'page' => array() 
                              ),
                                                                // Custom post types which use networkPublishing
        'requiredlist' => '',                                   // Comma-separated string of blogs to require
        'role_networkPublish' => 'super_admin',                 // Role required to use networkPublish function
        'role_link' => 'super_admin',                           // Role required to use the link function
        'role_networkPublish_as_draft' => 'super_admin',        // Role required to networkPublish posts as templates
        'role_networkPublish_scheduled_posts' => 'super_admin', // Role required to networkPublish scheduled, future posts
        'role_groups' => 'super_admin',                         // Role required to use groups
        'role_taxonomies' => 'super_admin',                     // Role required to networkPublish the taxonomies
        'role_taxonomies_create' => 'super_admin',              // Role required to create taxonomies automatically
        'role_custom_fields' => 'super_admin',                  // Role required to networkPublish the custom fields

    );


    private $blogs_cache = null;
    private $networkPublishing = false;
    private $menu_items = array();

    public function __construct() {
        
        parent::__construct(__FILE__);

        //return if it's not a Network admin
        if ( ! $this->is_network ) return;
        
        add_action( 'network_admin_menu', array( &$this, 'network_admin_menu' ) );
        add_action( 'admin_menu', array( &$this, 'add_menu' ) );
        add_action( 'admin_menu', array( &$this, 'create_meta_box' ) );
        add_action( 'admin_print_styles', array( &$this, 'admin_print_styles' ) );
        
        if ( $this->get_site_option( 'override_child_permalinks' ) )
            add_action( 'post_link', array( &$this, 'post_link' ) );
        add_filter( 'wpmu_activity_monitor_list_activities', array( &$this, 'list_activities') );
        
        if ( $this->get_site_option( 'canonical_url' ) )
            add_action( 'wp_head', array( &$this, 'wp_head' ), 1 );
    }
    
    public function network_admin_menu() {
        add_submenu_page( 'settings.php', 'wpmu_network_publish', 'WPMU Network Publish', 'activate_plugins', 'wpmu_network_publish', array ( &$this, 'admin' ) );
    }

    public function add_menu() {

        add_submenu_page( 'profile.php', 'WPMU Network Publish', $this->_( 'Network Publish' ), 'edit_posts', 'wpmu_network_publish', array ( &$this, 'user' ) );

        if ( $this->role_at_least( $this->get_site_option( 'role_link' ) ) ) {

            add_action( 'post_row_actions', array( &$this, 'post_row_actions' ), 10, 2 );
            add_action( 'page_row_actions', array( &$this, 'post_row_actions' ), 10, 2 );

            add_filter( 'manage_posts_columns', array( &$this, 'manage_posts_columns' ) ); 
            add_action( 'manage_posts_custom_column', array( &$this, 'manage_posts_custom_column' ), 10, 2 );

            add_filter( 'manage_pages_columns', array( &$this, 'manage_posts_columns' ) ); 
            add_action( 'manage_pages_custom_column', array( &$this, 'manage_posts_custom_column' ), 10, 2 );

            add_action( 'trash_post', array( &$this, 'trash_post' ) );
            add_action( 'trash_page', array( &$this, 'trash_post' ) );

            add_action( 'untrash_post', array( &$this, 'untrash_post' ) );
            add_action( 'untrash_page', array( &$this, 'untrash_post' ) );

            add_action( 'delete_post', array( &$this, 'delete_post' ) );
            add_action( 'delete_page', array( &$this, 'delete_post' ) );

        }
        
    }
    
    public function admin_print_styles() {
        
        $load = false;
        
        $pages = array(get_class(), 'WPMU_Activity_Monitor' );
        
        if ( isset( $_GET['page'] ) )
            $load |= in_array( $_GET['page'], $pages);
            
        foreach(array( 'post-new.php', 'post.php' ) as $string)
            $load |= strpos( $_SERVER['SCRIPT_FILENAME'], $string) !== false;
        
        if (!$load)
            return;
        
        wp_enqueue_script( 'wpmu_network', '/' . $this->paths['path_from_base_directory'] . '/js/wpmu_network_publish.js' );
        wp_enqueue_style( 'wpmu_network', '/' . $this->paths['path_from_base_directory'] . '/css/wpmu_network_publish.css', false, '0.0.1', 'all' );
        
    }
    
    // --------------------------------------------------------------------------------------------
    // ----------------------------------------- Activate / Deactivate
    // --------------------------------------------------------------------------------------------

    public function activate() {
        
        parent::activate();
        
        if ( !$this->is_network ) wp_die("This plugin requires a Wordpress Network installation.");
            
        $this->register_options();
            
        $this->query(
                        "CREATE TABLE IF NOT EXISTS `".$this->wpdb->base_prefix."wpmu_network_publish` (
                          `user_id` int(11) NOT NULL COMMENT 'User ID',
                          `data` text NOT NULL COMMENT 'User\'s data',
                          PRIMARY KEY (`user_id`)
                        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Contains the group settings for all the users';"
                    );

        $this->query(
                        "CREATE TABLE IF NOT EXISTS `".$this->wpdb->base_prefix."wpmu_network_publish_data` (
                          `blog_id` int(11) NOT NULL COMMENT 'Blog ID',
                          `post_id` int(11) NOT NULL COMMENT 'Post ID',
                          `data` text NOT NULL COMMENT 'Serialized NetworkData',
                          KEY `blog_id` (`blog_id`,`post_id`)
                        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;"
                   );
                   
        $this->query(
                        "CREATE TABLE `".$this->wpdb->base_prefix."wmpu_network_publish_menu` (
                          `id` INT(15) UNSIGNED NOT NULL AUTO_INCREMENT,
                          `title` VARCHAR(255) NOT NULL,
                          `alias` VARCHAR(255) DEFAULT NULL,
                          `path` VARCHAR(255) DEFAULT NULL,
                          `parent` INT(15) NOT NULL DEFAULT '0',
                          `ordering` INT(15) NOT NULL DEFAULT '0',
                          `published` ENUM('1','0') NOT NULL DEFAULT '1',
                          `menutype` TINYINT(15) NOT NULL,
                          PRIMARY KEY (`id`)
                        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;"
                   );
        
        $this->query(
                        "CREATE TABLE `".$this->wpdb->base_prefix."wmpu_network_publish_menutype` (
                          `int` TINYINT(15) UNSIGNED NOT NULL AUTO_INCREMENT,
                          `title` VARCHAR(50) NOT NULL,
                          `alias` VARCHAR(50) NOT NULL,
                          `published` ENUM('1','0') NOT NULL DEFAULT '1',
                          PRIMARY KEY (`int`)
                        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;"
                   );
                   
        $this->query(
                        "CREATE TABLE `".$this->wpdb->base_prefix."wpmu_network_publish_menu_to_blog` (
                          `blog_id` INT(15) UNSIGNED NOT NULL,
                          `menu_id` INT(15) NOT NULL
                        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;"
                   );
                   
        

        // Cats and tags replaced by taxonomy support. Version 1.5
        $this->delete_site_option( 'role_categories' );
        $this->delete_site_option( 'role_categories_create' );
        $this->delete_site_option( 'role_tags' );
        $this->delete_site_option( 'role_tags_create' );
        
    }
    
    public function uninstall() {

        parent::uninstall();
        $this->query("DROP TABLE `".$this->wpdb->base_prefix."wpmu_network_publish`");
        $this->query("DROP TABLE `".$this->wpdb->base_prefix."wpmu_network_publish_data`");
        $this->query("DROP TABLE `".$this->wpdb->base_prefix."wmpu_network_publish_menu`");
        $this->query("DROP TABLE `".$this->wpdb->base_prefix."wmpu_network_publish_menutype`");
        $this->query("DROP TABLE `".$this->wpdb->base_prefix."wpmu_network_publish_menu_to_blog`");

    }
    
    // --------------------------------------------------------------------------------------------
    // ----------------------------------------- Menus
    // --------------------------------------------------------------------------------------------

    public function admin() {
        
        $this->load_language();
        
        $tabs = array(
            'tabs' => array(),
            'functions' => array(),
        );
        
        $tabs[ 'tabs' ][ 'admin_post_types' ]            = $this->_('Post types');
        $tabs[ 'functions' ][ 'admin_post_types' ]        = 'admin_post_types';
        
        $this->tabs( $tabs );
        
    }
    
    public function user() {
        
        $this->load_language();
        
        $tab_data = array(
            'tabs'        =>    array(),
            'functions' =>    array(),
        );
        
        if ( isset( $_GET['action'] ) )
        {
            switch( $_GET[ 'action' ] )
            {
                case 'find_orphans':
                    $tab_data['tabs']['user_find_orphans'] = $this->_( 'Find orphans' );
                    $tab_data['functions']['user_find_orphans'] = 'user_find_orphans';
                    break;
                case 'trash':
                    $tab_data['tabs']['user_trash'] = $this->_( 'Trash' );
                    $tab_data['functions']['user_trash'] = 'user_trash';
                    break;
                case 'unlink':
                    $tab_data['tabs']['user_unlink'] = $this->_( 'Unlink' );
                    $tab_data['functions']['user_unlink'] = 'user_unlink';
                break;
            }
        }

        $tab_data['tabs']['user_help'] = $this->_( 'Help' );
        $tab_data['functions']['user_help'] = 'user_help';
        
        if ( $this->role_at_least( $this->get_site_option( 'role_groups' ) ) )
        {
            $tab_data['tabs']['user_edit_groups'] = $this->_( 'WPMU Network groups' );
            $tab_data['functions']['user_edit_groups'] = 'user_edit_groups';
        }

        $this->tabs( $tab_data );

    }
    
    protected function admin_post_types() {
        
        if ( isset( $_POST['save_post_types'] ) )
        {
            $post_types = trim( $_POST['post_types'] );
            $post_types = array_filter( explode( ' ', $post_types ) );
            $post_types = array_flip( $post_types );
            $this->update_site_option( 'post_types', $post_types);
            $this->message( 'Custom post types saved!' );
        }
        
        $post_types = $this->get_site_option( 'post_types' );
        
        $inputs = array();
        
        $inputs[ 'post_types' ] = array(
            'type' => 'text',
            'name' => 'post_types',
            'label' => $this->_( 'Post types to broadcast' ),
            'description' => $this->_( 'A space-separated list of post types that have networkPublishing enabled. The default value is <code>post page</code>.' ),
            'value' => implode( ' ', array_keys( $post_types ) ),
            'size' => 50,
            'max_length' => 1024,
        );
        
        $input_submit = array(
            'name' => 'save_post_types',
            'type' => 'submit',
            'value' => 'Save the allowed post types',
            'css_class' => 'button-primary',
        );
        
        $form = $this->form();
        
        echo '
            <p>' . $this->_( 'This page lets the admin select which post types in the network should be able to be broadcasted.' ) . '</p>
            
            <p>' . $this->_( 'Post types must be specified using their internal Wordpress names with a space between each. It is not possible to automatically make a list of available post types on the whole network because of limitation within Wordpress.' ) . '</p>
        
        '.$form->start().'
        
        ' . $this->display_form_table( $inputs ) .'
        
        <p>
            '.$form->make_input( $input_submit).'
        </p>
        
        '.$form->stop().'
        ';
    }

    
    protected function user_edit_groups() {
        
        $user_id = $this->user_id();        // Convenience.
        $form = $this->form();
        
        // Get a list of blogs that this user can write to.        
        $blogs = $this->list_user_writable_blogs( $user_id );
        
        $data = $this->sql_user_get( $user_id );
        
        if ( isset( $_POST['groupsSave'] ) )
        {
            $newGroups = array();
            foreach( $data['groups'] as $groupID=>$ignore)
            {
                if ( isset( $_POST['broadcast']['groups'][$groupID] ) )
                {
                    $newGroups[$groupID]['name'] = $data['groups'][$groupID]['name'];
                    $selectedBlogs =  $_POST['broadcast']['groups'][$groupID];
                    $newGroups[$groupID]['blogs'] = array_flip(array_keys( $selectedBlogs) );
                    
                    // Notify activity monitor that a group has changed.
                    $blog_text = count( $newGroups[$groupID]['blogs'] ) . ' ';
                    if ( count( $newGroups[$groupID]['blogs'] ) < 2 )
                        $blog_text .= 'blog: ';
                    else
                        $blog_text .= 'blogs: ';

                    $blogs_array = array();
                    foreach( $newGroups[$groupID]['blogs'] as $blogid => $ignore )
                        $blogs_array[] = $blogs[$blogid]['blogname'];
                    
                    $blog_text .= '<em>' . implode( '</em>, <em>', $blogs_array) . '</em>';
                    
                    $blog_text .= '.';
                    do_action( 'wpmu_activity_monitor_new_activity', array(
                        'activity_id' => 'wpmu_group_added',
                        'activity_strings' => array(
                            '' => '%user_display_name_with_link% updated the blog group <em>' . $newGroups[$groupID]['name'] . '</em> with ' . $blog_text,
                        ),
                    ) );
                }
                else
                {
                    do_action( 'wpmu_activity_monitor_new_activity', array(
                        'activity_id' => 'wpmu_group_deleted',
                        'activity_strings' => array(
                            '' => '%user_display_name_with_link% deleted the blog group <em>' . $data['groups'][$groupID]['name'] . '</em>',
                        ),
                    ) );
                    unset( $data['groups'][$groupID] );
                }
            }
            $data['groups'] = $newGroups;
            $this->sql_user_set( $user_id, $data);
            $this->message( $this->_( 'Group blogs have been saved.' ) );
        }
        
        if ( isset( $_POST['groupCreate'] ) )
        {
            $groupName = stripslashes( trim( $_POST['groupName'] ) );
            if ( $groupName == '' )
                $this->error( $this->_( 'The group name may not be empty!' ) );
            else
            {
                do_action( 'wpmu_activity_monitor_new_activity', array(
                    'activity_id' => 'wpmu_group_modified',
                    'activity_strings' => array(
                        '' => '%user_display_name_with_link% created the blog group <em>' . $groupName . '</em>',
                    ),
                ) );
                
                $data['groups'][] = array( 'name' => $groupName, 'blogs' => array() );
                $this->sql_user_set( $user_id, $data);
                $this->message( $this->_( 'The group has been created!' ) );
            }
        }
        
        $groupsText = '';
        if (count( $data['groups'] ) == 0)
            $groupsText = '<p>'.$this->_( 'You have not created any groups yet.' ).'</p>';
        foreach( $data['groups'] as $groupID=>$groupData)
        {
            $id = 'broadcast_group_'.$groupID;
            $groupsText .= '
                <div class="wpmu_network_publish_group">
                    <h4>'.$this->_( 'Group' ).': '.$groupData['name'].'</h4>

                    <div id="'.$id.'">
                        '.$this->show_group_blogs(array(
                            'blogs' => $blogs,
                            'nameprefix' => $groupID,
                            'selected' => $groupData['blogs'],
                        ) ).'
                    </div>
                </div>
            ';
        }
        
        $inputs = array(
            'groupsSave' => array(
                'name' => 'groupsSave',
                'type' => 'submit',
                'value' => $this->_( 'Save groups' ),
                'css_class' => 'button-primary',
            ),
            'groupName' => array(
                'name' => 'groupName',
                'type' => 'text',
                'label' => $this->_( 'New group name' ),
                'size' => 25,
                'maxlength' => 200,
            ),
            'groupCreate' => array(
                'name' => 'groupCreate',
                'type' => 'submit',
                'value' => $this->_( 'Create the new group' ),
                'css_class' => 'button-secondary',
            ),
        );
        
        echo '
            <h3>'.$this->_( 'Your groups' ).'</h3>

            '.$form->start().'

            '.$groupsText.'

            <p>
                '.$form->make_input( $inputs['groupsSave'] ).'
            </p>

            '.$form->stop().'

            <h3>'.$this->_( 'Create a new group' ).'</h3>

            '.$form->start().'

            <p>
                '.$form->make_label( $inputs['groupName'] ).' '.$form->make_input( $inputs['groupName'] ).'
            </p>

            <p>
                '.$form->make_input( $inputs['groupCreate'] ).'
            </p>

            '.$form->stop().'

            <h3>'.$this->_( 'Delete' ).'</h3>

            <p>
                '.$this->_( 'To <strong>delete</strong> a group, leave all blogs in that group unmarked and then save.' ).'
            </p>
        ';
    }
    
    /**
        Finds orhpans for a specific post.
    **/
    protected function user_find_orphans()
    {
        global $blog_id;
        $current_blog_id = $blog_id;
        $nonce = $_GET['_wpnonce'];
        $post_id = $_GET['post'];
            
        // Generate the nonce key to check against.            
        $nonce_key = 'broadcast_find_orphans';
        $nonce_key .= '_' . $post_id;
            
        if ( ! wp_verify_nonce( $nonce, $nonce_key) )
            die("Security check: not finding orphans for you!");
            
        $post = get_post( $post_id );
        $post_type = get_post_type( $post_id );
        $returnValue = '';
        $form = $this->form();
        
        $broadcast_data = $this->get_post_np_data( $blog_id, $post_id );

        // Get a list of blogs that this user can link to.        
        $user_id = $this->user_id();        // Convenience.
        $blogs = $this->list_user_writable_blogs( $user_id );
        
        $orphans = array();
        
        foreach( $blogs as $blog )
        {
            $temp_blog_id = $blog[ 'blog_id' ];
            if ( $temp_blog_id == $current_blog_id )
                continue;

            if ( $broadcast_data->has_linked_child_on_this_blog( $temp_blog_id ) )
                continue;
            
            switch_to_blog( $temp_blog_id );
            
            $args = array(
                'post_title' => $post->post_title,
                'numberposts' => 1,
                'post_type'=> $post_type,
                'post_status' => $post->post_status,
            );
            $posts = get_posts( $args );
            
            if ( count( $posts ) > 0 )
            {
                $orphan = reset( $posts );
                $orphan->permalink = get_permalink( $orphan->ID );
                $orphans[ $temp_blog_id ] = $orphan;
            }
            
            restore_current_blog();
        }
        
        if ( isset( $_POST['action_submit'] ) && isset( $_POST['blogs'] ) )
        {
            if ( $_POST['action'] == 'link' )
            {
                foreach( $orphans as $blog => $orphan )
                {
                    if ( isset( $_POST[ 'blogs' ][ $blog ] [ $orphan->ID ] ) )
                    {
                        $broadcast_data->add_linked_child( $blog, $orphan->ID );
                        unset( $orphans[ $blog ] );        // There can only be one orphan per blog, so we're not interested in the blog anymore.
                        
                        $_child_publish_data = $this->get_post_np_data( $blog, $orphan->ID );
                        $_child_publish_data->set_linked_parent( $blog_id, $post_id );
                        $this->set_post_broadcast_data( $blog, $orphan->ID, $_child_publish_data );
                    }
                }
                // Save the broadcast data.
                $this->set_post_broadcast_data( $blog_id, $post_id, $broadcast_data );
                echo $this->message( 'The selected children were linked!' ); 
            }    // link
        }
        
        if ( count( $orphans ) < 1 )
        {
            $message = $this->_( 'No possible child posts were found on the other blogs you have write access to. Either there are no posts with the same title as this one, or all possible orphans have already been linked.' );
        }
        else
        {
            $t_body = '';
            foreach( $orphans as $blog => $orphan )
            {
                $select = array(
                    'type' => 'input',
                    'checked' => false,
                    'label' => $orphan->ID,
                    'name' => $orphan->ID,
                    'nameprefix' => '[blogs][' . $blog . ']',
                );

                $t_body .= '
                    <tr>
                        <th scope="row" class="check-column">' . $form->make_input( $select ) . ' <span class="screen-reader-text">' . $form->make_label( $select ) . '</span></th>
                        <td><a href="' . $orphan->permalink . '">' . $blogs[ $blog ][ 'blogname' ] . '</a></td>
                    </tr>
                ';
            }
            
            $input_actions = array(
                'type' => 'select',
                'name' => 'action',
                'label' => $this->_('With the selected rows'),
                'options' => array(
                    array( 'value' => '', 'text' => $this->_( 'Do nothing' ) ),
                    array( 'value' => 'link', 'text' => $this->_( 'Create link' ) ),
                ),
            );
            
            $input_action_submit = array(
                'type' => 'submit',
                'name' => 'action_submit',
                'value' => $this->_('Apply'),
                'css_class' => 'button-secondary',
            );
            
            $selected = array(
                'type' => 'checkbox',
                'name' => 'check',
            );
            
            $returnValue .= '
                ' . $form->start() . '
                <p>
                    ' . $form->make_label( $input_actions ) . '
                    ' . $form->make_input( $input_actions ) . '
                    ' . $form->make_input( $input_action_submit ) . '
                </p>
                <table class="widefat">
                    <thead>
                        <th class="check-column">' . $form->make_input( $select ) . '<span class="screen-reader-text">' . $this->_('Selected') . '</span></th>
                        <th>' . $this->_( 'Domain' ) . '</th>
                    </thead>
                    <tbody>
                        ' . $t_body . '
                    </tbody>
                </table>
                ' . $form->stop() . '
            ';
        }
        
        if ( isset( $message ) )
            echo $this->message( $message );
        
        echo $returnValue;
        
        echo '
            <p>
                <a href="edit.php?post_type='.$post_type.'">Back to post overview</a>
            </p>
        ';
    }
    
    /**
        Trashes a broadcasted post.
    **/
    protected function user_trash()
    {
        // Check that we're actually supposed to be removing the link for real.
        global $blog_id;
        global $current_user;
        get_current_user();

//        $user_level = $current_user->roles[0];
        
        $nonce = $_GET['_wpnonce'];
        $post_id = $_GET['post'];
        $child_blog_id = $_GET['child'];
        
        // Generate the nonce key to check against.            
        $nonce_key = 'broadcast_trash';
        $nonce_key .= '_' . $child_blog_id;
        $nonce_key .= '_' . $post_id;
            
        if (!wp_verify_nonce( $nonce, $nonce_key) )
            die("Security check: not supposed to be trashing broadcasted post!");

        //Unlink The Post First
//        $this->user_unlink(1);
            
        $broadcast_data = $this->get_post_np_data( $blog_id, $post_id );
        switch_to_blog( $child_blog_id );
        $broadcasted_post_id = $broadcast_data->get_linked_child_on_this_blog();
//        pr($broadcasted_post_id, 1);
        wp_trash_post( $broadcasted_post_id );
        
        $this->remove_blog_menu_item($child_blog_id, $broadcasted_post_id);

        $broadcast_data->remove_linked_child( $child_blog_id );
        
        restore_current_blog();
        
        $this->set_post_broadcast_data( $blog_id, $post_id, $broadcast_data );
        
        $message = $this->_( 'The broadcasted child post has been put in the trash.' );
        
        echo '
            '.$this->message( $message).'
            <p>
                <a href="'.wp_get_referer().'">Back to post overview</a>
            </p>
        ';
    }
    
    protected function user_unlink($skip_security = 0)
    {
        // Check that we're actually supposed to be removing the link for real.
        $nonce = $_GET['_wpnonce'];
        $post_id = $_GET['post'];
        if ( isset( $_GET['child'] ) )
            $child_blog_id = $_GET['child'];
        
        
        if(!$skip_security){
            // Generate the nonce key to check against.            
            $nonce_key = 'broadcast_unlink';
            if ( isset( $child_blog_id) )
                $nonce_key .= '_' . $child_blog_id;
            $nonce_key .= '_' . $post_id;
            
            if (!wp_verify_nonce( $nonce, $nonce_key) )
                die("Security check: not supposed to be unlinking broadcasted post!");
        }
        
        global $blog_id;

        $broadcast_data = $this->get_post_np_data( $blog_id, $post_id );
        $linked_children = $broadcast_data->get_linked_children();
        
        // Remove just one child?
        if ( isset( $child_blog_id) )
        {
            // Inform Activity Monitor that a post has been unlinked.
            // Get the info about this post.
            $post_data = get_post( $post_id );
            $post_url = get_permalink( $post_id );
            $post_url = '<a href="'.$post_url.'">'.$post_data->post_title.'</a>';
            
            // And about the child blog
            switch_to_blog( $child_blog_id );
            $blog_url = '<a href="'.get_bloginfo( 'url' ).'">'.get_bloginfo( 'name' ).'</a>';
            restore_current_blog();                
            
            do_action( 'wpmu_activity_monitor_new_activity', array(
                'activity_id' => 'wpmu_post_unlinked',
                'activity_strings' => array(
                    '' => '%user_display_name_with_link% unlinked ' . $post_url . ' with the child post on ' . $blog_url,
                ),
            ) );
//            pr($broadcast_data, 1);
            $this->delete_post_broadcast_data( $child_blog_id, $linked_children[$child_blog_id] );
            $broadcast_data->remove_linked_child( $child_blog_id );
            $this->set_post_broadcast_data( $blog_id, $post_id, $broadcast_data );
            $message = $this->_( 'Link to child post has been removed.' );
        }
        else
        {
            $blogs_url = array();
            foreach( $linked_children as $linked_child_blog_id => $linked_child_post_id)
            {
                // And about the child blog
                switch_to_blog( $linked_child_blog_id );
                $blogs_url[] = '<a href="'.get_bloginfo( 'url' ).'">'.get_bloginfo( 'name' ).'</a>';
                restore_current_blog();                
                $this->delete_post_broadcast_data( $linked_child_blog_id, $linked_child_post_id );
            }
            
            // Inform Activity Monitor
            // Get the info about this post.
            $post_data = get_post( $post_id );
            $post_url = get_permalink( $post_id );
            $post_url = '<a href="'.$post_url.'">'.$post_data->post_title.'</a>';
            
            $blogs_url = implode( ', ', $blogs_url);
            
            do_action( 'wpmu_activity_monitor_new_activity', array(
                'activity_id' => 'wpmu_post_unlinked',
                'activity_strings' => array(
                    '' => '%user_display_name_with_link% unlinked ' . $post_url . ' with the child posts on ' . $blogs_url,
                ),
            ) );

            $broadcast_data = $this->get_post_np_data( $blog_id, $post_id );
            $broadcast_data->remove_linked_children();
            $message = $this->_( 'All links to child posts have been removed!' );
        }
        
//        pr($blog_id, 1);
        
        $this->set_post_broadcast_data( $blog_id, $post_id, $broadcast_data );
        
        echo '
            '.$this->message( $message).'
            <p>
                <a href="'.wp_get_referer().'">Back to post overview</a>
            </p>
        ';
    }
    
    protected function user_help()
    {
        echo '
            <div id="broadcast_help">
                <h2>'.$this->_( 'What is Network Publishing?' ).'</h2>
    
                <p class="float-right">
                    <img src="'.$this->paths['url'].'/screenshot-1.png" alt="" title="'.$this->_( 'What the Network Publish window looks like' ).'" />
                </p>
    
                <p>
                    '.$this->_( 'With Network Publish you can post to several blogs at once. The broadcast window is first shown at the bottom right on the Add New post/page screen.' ).'
                    '.$this->_( 'The window contains several options and a list of blogs you have access to.' ).'
                </p>

                <p>
                    '.$this->_( 'Some settings might be disabled by the site administrator and if you do not have write access to any blogs, other than this one, the Network Publish window might not appear.' ).' 
                </p>

                <p>
                    '.$this->_( 'To use the Network Publish plugin, simply select which blogs you want to broadcast the post to and then publish the post normally.' ).' 
                </p>

                <h3>'.$this->_( 'Options' ).'</h3>
                
                <p>
                    <em>'.$this->_( 'Link this post to its children' ).'</em> '.$this->_( 'will create a link from this post (the parent) to all the broadcasted posts (children). Updating the parent will result in all the children being updated. Links to the children can be removed in the page / post overview.' ).'
                </p>

                <p class="textcenter">
                    <img class="border-single" src="'.$this->paths['url'].'/screenshot-2.png" alt="" title="'.$this->_( 'Post overview with unlink options' ).'" />
                </p>

                <p>
                    '.$this->_( 'When a post is linked to children, the children are overwritten when post is updated - all the taxonomies, tags and fields (including featured image) are also overwritten -  and when the parent is trashed or deleted the children get the same treatment. If you want to keep any children and delete only the parent, use the unlink links in the post overview. The unlink link below the post name removes all links and the unlinks to the right remove singular links.' ).'
                </p>

                <p>
                    <em>'.$this->_( 'Network Publish categories also' ).'</em> '.$this->_( 'will also try to send the taxonomies together with the post.' ).'
                    '.$this->_( 'In order to be able to broadcast the taxonomies, the selected blogs must have the same taxonomy names (slugs) as this blog.' ).'
                </p>

                <p>
                    <em>'.$this->_( 'Network Publish tags also' ).'</em> '.$this->_( 'will also mark the broadcasted posts with the same tags.' ).'
                </p>

                <p>
                    <em>'.$this->_( 'Network Publish custom fields' ).'</em> '.$this->_( 'will give the broadcasted posts the same custom fields as the original. Use this setting to broadcast the featured image.' ).'
                </p>

                <h3>'.$this->_( 'Groups' ).'</h3>

                <p>
                    '.$this->_( 'If the site administrator allows it you may create groups to quickly select several blogs at once. To create a group, start by typing a group name in the text box and pressing the create button.' ).' 
                </p>

                <p class="textcenter">
                    <img class="border-single" src="'.$this->paths['url'].'/screenshot-5.png" alt="" title="'.$this->_( 'Group setup' ).'" />
                </p>

                <p>
                    '.$this->_( 'Then select which blogs you want to be automatically selected when you choose this group when editing a new post. Press the save button when you are done. Your new group is ready to be used!' ).'
                    '.$this->_( 'Simply choose it in the dropdown box and the blogs you specified will be automatically chosen.' ).'
                </p>

            </div>
        ';
    }
    
    // --------------------------------------------------------------------------------------------
    // ----------------------------------------- Callbacks
    // --------------------------------------------------------------------------------------------

    public function add_meta_box_type( $post ) {
        return $this->add_meta_box( $post->post_type );
    }
    
    public function add_meta_box( $type ) {
        
        global $blog_id;
        global $post;
        $form = $this->form();

        $published = $post->post_status == 'publish';

        // Find out if this post is already linked
        $broadcast_data = $this->get_post_np_data( $blog_id, $post->ID );

        if ( $broadcast_data->get_linked_parent() !== false)
        {
            echo '<p>';
                echo $this->_( 'This post is published as child post. It cannot be published further.' );
            echo '</p>';
            return;
        }

        $has_linked_children = $broadcast_data->has_linked_children();

        $blogs = $this->list_user_writable_blogs( $this->user_id() );
        // Remove the blog we're currently working on from the list of writable blogs.
        unset( $blogs[$blog_id] );

        global $current_user;
        get_current_user();
        $user_id = $current_user->ID;
        $last_used_settings = $this->load_last_used_settings( $user_id );
        
        $post_type = $post->post_type;
        $post_type_object = get_post_type_object( $post_type );
        $post_type_supports_thumbnails = post_type_supports( $post_type, 'thumbnail' );
        $post_type_supports_custom_fields = post_type_supports( $post_type, 'custom-fields' );
        $post_type_is_hierarchical = $post_type_object->hierarchical;
        
        
//        pr($last_used_settings);
        
        //IF Menus are enabled
        if( 1 == 1 ){
            
            $menuType = array(
                'name' => 'menuType',
                'type' => 'hidden',
                'nameprefix' => '[broadcast]['.$post->ID.']',
                'value' => '1',
            );
            echo $form->make_input( $menuType);

            //--------------------------------------------------------

            $inputAddMenu = array(
                'name' => 'addtomenu',
                'type' => 'checkbox',
                'nameprefix' => '[broadcast]['.$post->ID.']',
                'value' => '1',
                'checked' => isset( $last_used_settings[$post->ID]['addtomenu'] ),
                'label' => $this->_( 'Add to Menu' ),
                'title' => $this->_( 'Add current page/post to Menu.' ),
            );
            echo '<p class="broadcast_input_addtomenu">'.$form->make_input( $inputAddMenu).' '.$form->make_label( $inputAddMenu).'</p>';
            
            $labelAddMenu = array(
                'name' => 'menulabel',
                'type' => 'text',
                'nameprefix' => '[broadcast]['.$post->ID.']',
                'label' => $this->_( 'Menu Title' ),
                'title' => $this->_( 'Menu Label (leave blank to use page/post title)' ),
            );
            echo '<p class="broadcast_input_addtomenu">'.$form->make_label( $labelAddMenu);
            
            $inputMenuLabel = array(
                'name' => 'menulabel',
                'type' => 'text',
                'size' => '35',
                'nameprefix' => '[broadcast]['.$post->ID.']',
                'value' => isset( $last_used_settings[$post->ID]['menulabel'] )?$last_used_settings[$post->ID]['menulabel']:$post->post_title,
            );
                                
            echo $form->make_input( $inputMenuLabel ).' '.$form->make_label( $inputMenuLabel ).'</p>';


            //----------------------------------------------------------
            echo '<div style="height: 1px; background-color: #ddd;"></div>';
            
        }


        //Added for Select All
        echo '<p style="text-align:right;"><a href="javascript://" id="selectAllBlogs">Toggle All</a></p>';

        if ( $this->role_at_least( $this->get_site_option( 'role_link' ) ) ) {

            // Check the link box is the post has been published and has children OR it isn't published yet.
            $linked = ( ( $published && $broadcast_data->has_linked_children() ) || !$published ); 

            $inputPublishLink = array(
                'name' => 'link',
                'type' => 'hidden',
                'nameprefix' => '[broadcast]',
                'value' => 'link',
                'checked' => $linked,
//                'title' => $this->_( 'Create a link to the children, which will be updated when this post is updated, trashed when this post is trashed, etc.' ),
//                'label' => $this->_( 'Link this post to its children' ),
            );
            echo '<p>'.$form->make_input( $inputPublishLink).'</p>';

        }

//        echo '<div style="height: 1px; background-color: #ddd;"></div>';

        if ( $this->role_at_least( $this->get_site_option( 'role_taxonomies' ) ) ) {

            $input_taxonomies = array(
                'name' => 'taxonomies',
                'type' => 'checkbox',
                'nameprefix' => '[broadcast]',
                'value' => 'taxonomies',
                'checked' => isset( $last_used_settings['taxonomies'] ),
                'label' => $this->_( 'Network Publish taxonomies (categories / tags etc) also' ),
                'title' => $this->_( 'The taxonomies must have the same name (slug) on the selected blogs.' ),
            );
            echo '<p class="broadcast_input_taxonomies">'.$form->make_input( $input_taxonomies).' '.$form->make_label( $input_taxonomies).'</p>';

        }
        
        /*
        if ( $this->role_at_least( $this->get_site_option( 'role_taxonomies_create' ) ) ) {
            $input_taxonomies_create = array(
                'name' => 'taxonomies_create',
                'type' => 'checkbox',
                'nameprefix' => '[broadcast]',
                'value' => 'taxonomies_create',
                'checked' => isset( $last_used_settings['taxonomies_create'] ),
                'label' => $this->_( 'Create taxonomies automatically' ),
                'title' => $this->_( "The taxonomies will be created if they don't exist on the selected blogs." ),
            );
            echo '<p class="broadcast_input_taxonomies_create">&emsp;'.$form->make_input( $input_taxonomies_create).' '.$form->make_label( $input_taxonomies_create).'</p>';
        }
        */

        if ( $this->role_at_least( $this->get_site_option( 'role_custom_fields' ) ) && ( $post_type_supports_custom_fields || $post_type_supports_thumbnails) ) {

            $inputCustomFields = array(
                'name' => 'custom_fields',
                'type' => 'checkbox',
                'nameprefix' => '[broadcast]',
                'value' => 'custom_fields',
                'checked' => isset( $last_used_settings['custom_fields'] ),
                'title' => $this->_( 'Network Publish all the custom fields and the featured image?' ),
                'label' => $this->_( 'Network Publish custom fields' ),
            );
            
            echo '<p>'.$form->make_input( $inputCustomFields).' '.$form->make_label( $inputCustomFields).'</p>';

        }
        
        echo '<div style="height: 1px; background-color: #ddd;"></div>';


        echo '
            <script type="text/javascript">
                var broadcast_strings = {
                    select_deselect_all : "' . $this->_( 'Select / deselect all' ) . '",
                    invert_selection : "' . $this->_( 'Invert selection' ) . '"
                };
            </script>
        ';

        // Similarly, groups are only available to those who are allowed to use them.
        $data = $this->sql_user_get( $this->user_id() );
        if ( $this->role_at_least( $this->get_site_option( 'role_groups' ) ) && (count( $data['groups'] )>0) ) {
            
            $inputGroups = array(
                'name' => 'broadcast_group',
                'type' => 'select',
                'nameprefix' => '[broadcast]',
                'label' => $this->_( 'Select blogs in group' ),
                'options' => array(array( 'value' => '', 'text' => $this->_( 'No group selected' ) )),
            );
            
            foreach( $data['groups'] as $groupIndex=>$groupData)
                $inputGroups['options'][] = array( 'text' => $groupData['name'], 'value' => implode( ' ', array_keys( $groupData['blogs'] ) ));
            
            // The javascripts just reacts on a click to the select box and selects those checkboxes that the selected group has.            
            echo '
                <p>
                '.$form->make_label( $inputGroups).' '.$form->make_input( $inputGroups).'
                </p>
            ';
        }
        
        $blog_class = array();
        $blog_title = array();
        $selectedBlogs = array();
        
        // Preselect those children that this post has.
        $linked_children = $broadcast_data->get_linked_children();
//        pr($linked_children, 1);
        if ( count( $linked_children) > 0 ) {
            foreach( $linked_children as $temp_blog_id => $postID) {
                $selectedBlogs[ $temp_blog_id ] = true;
                @$blog_class[ $temp_blog_id ] .= ' blog_is_already_linked';
                @$blog_title[ $temp_blog_id ] .= $this->_( 'This blog has already been linked.' );
            }
        }
        
        if ( $this->get_site_option( 'always_use_required_list' ) )        
            $required_blogs = $this->get_required_blogs();
        else
            $required_blogs = array();
        
        foreach ( $required_blogs as $temp_blog_id => $ignore)
        {
            @$blog_class[ $temp_blog_id ] .= ' blog_is_required';
            @$blog_title[ $temp_blog_id ] .= $this->_( 'This blog is required and cannot be unselected.' );
        }

        $selectedBlogs = array_flip( array_merge(
            array_keys( $selectedBlogs ),
            array_keys( $required_blogs )
        ) );
        
        
        // Remove all blacklisted blogs.
        foreach( $blogs as $temp_blog_id=>$ignore)
            if ( $this->is_blog_blacklisted( $temp_blog_id) )
                unset( $blogs[ $temp_blog_id ] );
        
        // Disable all blogs that do not have this post type.
        // I think there's a bug in WP since it reports the same post types no matter which blog we've switch_to_blogged.
        // Therefore, no further action.
//        pr($selectedBlogs, 1);
//        pr($blogs, 1);
        
        echo '<div class="network_publish_to">
                <p class="howto">'. $this->_( 'Network Publish to:' ) .'</p>
                <div class="blogs" style="height: 250px; overflow-y: scroll;"> ' 
                        . $this->show_group_blogs(
                                array(
                                    'blogs' => $blogs,
                                    'blog_class' => $blog_class,
                                    'blog_title' => $blog_title,
                                    'nameprefix' => 666,
                                    'selected' => $selectedBlogs,
                                    'readonly' => $required_blogs,
                                    'disabled' => $required_blogs,
                                    'post_id' => $post->ID,
                                    'linked_children' => $linked_children,
                                )
                                ) . '
                </div>
              </div>';
    }
    
    public function list_activities( $activities ) {
        
        // First, fill in our own activities.
        $this->activities = array(
            'wpmu_published' => array(
                'name' => $this->_( 'A post was broadcasted.' ),
            ),
            'wpmu_group_added' => array(
                'name' => $this->_( 'A Network Publish blog group was added.' ),
            ),
            'wpmu_group_deleted' => array(
                'name' => $this->_( 'A Network Publish blog group was deleted.' ),
            ),
            'wpmu_group_modified' => array(
                'name' => $this->_( 'A Network Publish blog group was modified.' ),
            ),
            'wpmu_post_unlinked' => array(
                'name' => $this->_( 'A post was unlinked.' ),
            ),
        );

        // Insert our module name in all the values.
        foreach( $this->activities as $index => $activity )
        {
            $activity['plugin'] = 'WPMU Network Publish';
            $activities[ $index ] = $activity;
        } 

        return $activities;
    }

    public function save_post( $post_id ) {

        if ( $this->is_networkPublishing() )
            return;

        if (!$this->role_at_least( $this->get_site_option( 'role_networkPublish' ) ) )
            return;

        $allowed_post_status = array( 'pending', 'private', 'publish' );

        if ( $this->role_at_least( $this->get_site_option( 'role_networkPublish_as_draft' ) ) )
            $allowed_post_status[] = 'draft';

        if ( $this->role_at_least( $this->get_site_option( 'role_networkPublish_scheduled_posts' ) ) )
            $allowed_post_status[] = 'future';

        $post = get_post( $post_id, 'ARRAY_A' );
        if ( !in_array( $post['post_status'], $allowed_post_status) )
            return;

        // Check if the user hasn't marked any blogs for forced networkPublishing but it the admin wants forced blogs.
        if ( !isset( $_POST['broadcast'] ) ) {
            // Site admin is never forced to do anything.
            if ( is_super_admin() )    return;
            
            if ( ! $this->get_site_option( 'always_use_required_list' ) == true ) return;
        }

        //sort blogs by ID
        @ksort($_POST['broadcast']['groups']['666']);

        $post_type = $_POST['post_type'];
        $post_type_object = get_post_type_object( $post_type);
        $post_type_supports_thumbnails = post_type_supports( $post_type, 'thumbnail' );
        $post_type_supports_custom_fields = post_type_supports( $post_type, 'custom-fields' );
        $post_type_is_hierarchical = $post_type_object->hierarchical;

        // Create new post data from the original stuff.
        $new_post = $post;
        foreach(array( 'comment_count', 'guid', 'ID', 'menu_order', 'post_parent' ) as $key)
            unset( $new_post[$key] );

        if ( isset( $_POST['broadcast']['groups']['666'] ) )
            $blogs = array_keys( $_POST['broadcast']['groups']['666'] );
        else
            $blogs = array();

        // Now to add and remove blogs.
        $blogs = array_flip( $blogs );

        // Remove the blog we're currently working on. No point in networkPublishing to ourselves.
        global $blog_id;
        unset( $blogs[$blog_id] );

        $user_id = $this->user_id();        // Convenience.

        // Remove blacklisted
        foreach( $blogs as $blogID=>$ignore)
            if ( !$this->is_blog_user_writable( $user_id, $blogID ) )
                unset( $blogs[ $blogID ] );


        // Add required blogs.
        if ( $this->get_site_option( 'always_use_required_list' ) ) {
            $requiredBlogs = $this->get_required_blogs();
            foreach( $requiredBlogs as $requiredBlog=>$ignore )
                $blogs[ $requiredBlog ] = $requiredBlog;
        }
        $blogs = array_keys( $blogs );
        // Now to add and remove blogs: done


        // Do we actually need to to anything?
        if (count( $blogs ) < 1)
            return;

        $link = ( $this->role_at_least( $this->get_site_option( 'role_link' ) ) && isset( $_POST['broadcast']['link'] ) );
        
        if ( $link) {
            // Prepare the broadcast data for linked children.
            $broadcast_data = $this->get_post_np_data( $blog_id, $post_id );
//            pr($broadcast_data, 1);
            // Does this post type have parent support, so that we can link to a parent?
            if ( $post_type_is_hierarchical && $_POST['post_parent'] > 0)            {
                $post_id_parent = $_POST['post_parent'];
                $parent_broadcast_data = $this->get_post_np_data( $blog_id, $post_id_parent );
            }
        }

        $taxonomies = (
            $this->role_at_least( $this->get_site_option( 'role_taxonomies' ) ) && isset( $_POST['broadcast']['taxonomies'] )
        );
        
        $taxonomies_create = ( $this->role_at_least( $this->get_site_option( 'role_taxonomies_create' ) ) && isset( $_POST['broadcast']['taxonomies_create'] ) );
        
        if ( $taxonomies) {
            $source_blog_taxonomies = get_object_taxonomies( array(
                                                                    'object_type' => $post_type,
                                                                ), 
                                                             'array' 
                                                           );

            $source_post_taxonomies = array();
            
            foreach( $source_blog_taxonomies as $source_blog_taxonomy => $taxonomy ) {
                
                // Source blog taxonomy terms are used for creating missing target term ancestors
                $source_blog_taxonomies[ $source_blog_taxonomy ] = array(
                    'taxonomy' => $taxonomy,
                    'terms'    => $this->get_current_blog_taxonomy_terms( $source_blog_taxonomy ),
                );

                $source_post_taxonomies[ $source_blog_taxonomy ] = get_the_terms( $post_id, $source_blog_taxonomy );
                if ( $source_post_taxonomies[ $source_blog_taxonomy ] === false )
                    unset( $source_post_taxonomies[ $source_blog_taxonomy ] ); 
            
            }
            
        }
//        pr($blogs, 1);
        require_once( 'AttachmentData.php' );
        $upload_dir = wp_upload_dir();    // We need to find out where the files are on disk for this blog.
        $attachment_data = array();
        $attached_files =& get_children( 'post_parent='.$post_id.'&post_type=attachment' );
        $has_attached_files = count( $attached_files) > 0;
        
        if ( $has_attached_files) {
            foreach( $attached_files as $attached_file)
                $attachment_data[ $attached_file->ID ] = AttachmentData::from_attachment_id( $attached_file->ID, $upload_dir );
        }

        $custom_fields = (
            $this->role_at_least( $this->get_site_option( 'role_custom_fields' ) )
            &&
            isset( $_POST['broadcast']['custom_fields'] )
            &&
            ( $post_type_supports_custom_fields || $post_type_supports_thumbnails)
        );
        
        if ( $custom_fields) {
            
            $post_custom_fields = get_post_custom( $post_id );
            
            $has_thumbnail = isset( $post_custom_fields['_thumbnail_id'] );
            
            if ( $has_thumbnail ) {

                $thumbnail_id = $post_custom_fields['_thumbnail_id'][0];
                unset( $post_custom_fields['_thumbnail_id'] ); // There is a new thumbnail id for each blog.
                $attachment_data['thumbnail'] = AttachmentData::from_attachment_id( $thumbnail_id, $upload_dir);
                // Now that we know what the attachment id the thumbnail has, we must remove it from the attached files to avoid duplicates.
                unset( $attachment_data[$thumbnail_id] );
                
            }

            // Remove all the _internal custom fields.
            $post_custom_fields = $this->keep_valid_custom_fields( $post_custom_fields);
        }
        
        // Sticky isn't a tag, taxonomy or custom_field.
        $post_is_sticky = @( $_POST['sticky'] == 'sticky' );
        
        // And now save the user's last settings.
        $this->save_last_used_settings( $user_id, $_POST['broadcast'] );
        
        $to_broadcasted_blogs = array();                // Array of blog names that we're networkPublishing to. To be used for the activity monitor action.
        $to_broadcasted_blog_details = array();         // Array of blog and post IDs that we're networkPublishing to. To be used for the activity monitor action.
        
//        pr($_POST['broadcast'], 1);
        
        // To prevent recursion
        $this->networkPublishing = $_POST['broadcast'];
        unset( $_POST['broadcast'] );
        
        $original_blog = $blog_id;
        
        //Get Current linked Childs
        $_prev_linked_children = $broadcast_data->get_linked_children();
        
        
        //-------------------------------------------------------------------------
        //  Add/Update Menu Item
        //-------------------------------------------------------------------------
        $add_menu_item = (isset($this->networkPublishing[$post_id]['addtomenu']))?1:0;
        
        if ( $add_menu_item) {
            
            $menuType = (isset($this->networkPublishing[$post_id]['menuType']))?$this->networkPublishing[$post_id]['menuType']:NULL;
            $menulabel = (isset($this->networkPublishing[$post_id]['menulabel']))?trim($this->networkPublishing[$post_id]['menulabel']):$_POST['post_title'];

            if($menu_item_id = $this->__chk_blog_menu_item($blog_id, $post_id, $menulabel, $menuType)){
                $this->__update_menu_item($menu_item_id, $menulabel, $post_id, $menuType);
            }else{
                $menu_item_id = $this->__add_menu_item($menulabel, $post_id, $menuType);
            }

            $this->__reset_menu_relation( $menu_item_id );
            $this->__add_menu_item_relation($blog_id, $menu_item_id, $post_id);
            
        }else{
            $this->__reset_menu_relation( $menu_item_id );
        }
        
        //Loop through All Blogs to Update Data        
        foreach( $blogs as $blogID ) {
            // Another safety check. Goes with the safety dance.
            if ( !$this->is_blog_user_writable( $user_id, $blogID) )
                continue;
            switch_to_blog( $blogID );
            
            // Post parent
            if ( $link && isset( $parent_broadcast_data) )
                if ( $parent_broadcast_data->has_linked_child_on_this_blog() )
                {
                    $linked_parent = $parent_broadcast_data->get_linked_child_on_this_blog();
                    $new_post['post_parent'] = $linked_parent;
                }
            
            // Insert new? Or update? Depends on whether the parent post was linked before or is newly linked?
            $need_to_insert_post = true;
            if ( $link )
                if ( $broadcast_data->has_linked_child_on_this_blog() )
                {
                    $child_post_id = $broadcast_data->get_linked_child_on_this_blog();
                    
                    // Does this child post still exist?
                    $child_post = get_post( $child_post_id );
                    if ( $child_post !== null )
                    {
                        $temp_post_data = $new_post;
                        $temp_post_data['ID'] = $child_post_id;
                        $new_post_id = wp_update_post( $temp_post_data );
                        $need_to_insert_post = false;
                    }
                }
            
            if ( $need_to_insert_post )
            {
                $new_post_id = wp_insert_post( $new_post );
                
                if ( $link )
                    $broadcast_data->add_linked_child( $blogID, $new_post_id );
            }
            
            if ( $taxonomies )
            {
                foreach( $source_post_taxonomies as $source_post_taxonomy => $source_post_terms )
                {
                    // If we're updating a linked post, remove all the taxonomies and start from the top.
                    if ( $link )
                        if ( $broadcast_data->has_linked_child_on_this_blog() )
                            wp_set_object_terms( $new_post_id, array(), $source_post_taxonomy );
                    
                    // Get a list of cats that the target blog has.
                    $target_blog_terms = $this->get_current_blog_taxonomy_terms( $source_post_taxonomy );

                    // Go through the original post's terms and compare each slug with the slug of the target terms.
                    $taxonomies_to_add_to = array();
                    $have_created_taxonomies = false;
                    foreach( $source_post_terms as $source_post_term )
                    {
                        $found = false;
                        $source_slug = $source_post_term->slug;
                        foreach( $target_blog_terms as $target_blog_term )
                        {
                            if ( $target_blog_term['slug'] == $source_slug)
                            {
                                $found = true;
                                $taxonomies_to_add_to[ $target_blog_term['term_id'] ] = intval( $target_blog_term['term_id'] );
                                break;
                            }
                        }
                            
                        // Should we create the taxonomy if it doesn't exist?
                        if ( !$found && $taxonomies_create )
                        {
                            // Does the term have a parent?
                            $target_parent_id = 0;
                            if ( 0 != $source_post_term->parent )
                            {
                                // Recursively insert ancestors if needed, and get the target term's parent's ID
                                $target_parent_id = $this->insert_term_ancestors(
                                    $this->object_to_array( $source_post_term ),
                                    $source_post_taxonomy,
                                    $target_blog_terms,
                                    $source_blog_taxonomies[ $source_post_taxonomy ]['terms']
                                );
                            }

                            $new_taxonomy = wp_insert_term(
                                $source_post_term->name,
                                $source_post_taxonomy,
                                array( 
                                    'slug' => $source_post_term->slug,
                                    'description' => $source_post_term->description,
                                    'parent' => $target_parent_id,
                                )
                            );

                            $taxonomies_to_add_to[] = $source_post_term->slug;
                            $have_created_taxonomies = true;
                        }
                    }

                    if ( $taxonomies_create )
                        $this->sync_terms( $source_post_taxonomy, $original_blog, $blogID );

                    if ( count( $taxonomies_to_add_to) > 0 )
                    {
                        // This relates to the bug mentioned in the method $this->set_term_parent()
                        delete_option( $source_post_taxonomy . '_children' );
                        clean_term_cache( '', $source_post_taxonomy ); 

                        wp_set_object_terms( $new_post_id, $taxonomies_to_add_to, $source_post_taxonomy );
                    }
                }
            }
            
            /**
                Remove the current attachments.
            */
            $attachments_to_remove =& get_children( 'post_parent='.$new_post_id.'&post_type=attachment' );
            foreach ( $attachments_to_remove as $attachment_to_remove )
                wp_delete_attachment( $attachment_to_remove->ID );
            
            // Copy the attachments
            $copied_attachments = array();
            foreach( $attachment_data as $key=>$attachment )
            {
                if ( $key != 'thumbnail' )
                {
                    $new_attachment_id = $this->copy_attachment( $attachment, $new_post_id );
                    $c = new stdClass();
                    $c->old = $attachment;
                    $c->new = $new_attachment_id;
                    $copied_attachments[] = $c;
                }
            }
            
            // If there were any image attachments copied...
            if ( count( $copied_attachments ) > 0 )
            {
                // Update the URLs in the post to point to the new images.
                $wp_upload_dir = wp_upload_dir();
                $modified_post = get_post( $new_post_id );
                foreach( $copied_attachments as $a )
                {
                    $ald_attachment = $a->old;
                    
                    $new_attachment_id = $a->new;
                    $new_attachment = AttachmentData::from_attachment_id( $new_attachment_id, $wp_upload_dir );
                    $a->new = $new_attachment;
                    
                    $modified_post->post_content = str_replace( $ald_attachment->guid, $new_attachment->guid, $modified_post->post_content );
                    $modified_post->post_content = str_replace( 'id="attachment_' . $ald_attachment->id . '"', 'id="attachment_' . $new_attachment->id . '"', $modified_post->post_content );
                }
                
                // Update any [gallery] shortcodes found.
                $rx =get_shortcode_regex();
                $matches = '';
                preg_match_all( '/' . $rx . '/', $modified_post->post_content, $matches );
                
                // [2] contains only the shortcode command / key. No options.
                foreach( $matches[ 2 ] as $index => $key )
                {
                    // Look for only the gallery shortcode.
                    if ( $key !== 'gallery' )
                        continue;
                    // Complete matches are in 0.
                    $old_shortcode = $matches[ 0 ][ $index ];
                    // Extract the IDs
                    $ids = preg_replace( '/.*ids=\"([0-9,]*)".*/', '\1', $old_shortcode );
                    // And put in the new IDs.
                    $new_ids = array();
                    // Try to find the equivalent new attachment ID.
                    // If no attachment found
                    foreach( explode( ',', $ids ) as $old_id )
                        foreach( $copied_attachments as $a )
                            if ( $old_id == $a->old->id )
                                $new_ids[] = $a->new->id;
                    $new_shortcode = str_replace( $ids, implode( ',', $new_ids ) , $old_shortcode );
                    $modified_post->post_content = str_replace( $old_shortcode, $new_shortcode, $modified_post->post_content );
                }
                wp_update_post( $modified_post );
            }
            
            if ( $custom_fields )
            {
                // Remove all old custom fields.
                $old_custom_fields = get_post_custom( $new_post_id );

                foreach( $old_custom_fields as $key => $value )
                {
                    // This post has a featured image! Remove it from disk!
                    if ( $key == '_thumbnail_id' )
                    {
                        $thumbnail_post = $value[0];
                        wp_delete_post( $thumbnail_post);
                    }
                    
                    delete_post_meta( $new_post_id, $key );
                }
                
                foreach( $post_custom_fields as $meta_key => $meta_value ) {
                    if ( is_array( $meta_value ) ) {
                        foreach( $meta_value as $single_meta_value )
                        {
                            $single_meta_value = maybe_unserialize( $single_meta_value );
                            add_post_meta( $new_post_id, $meta_key, $single_meta_value );
                        }
                    }
                    else {
                        $meta_value = maybe_unserialize( $meta_value );
                        add_post_meta( $new_post_id, $meta_key, $meta_value );
                    }
                }
                
                // Attached files are custom fields... but special custom fields.
                if ( $has_thumbnail ) {
                    
                    $new_attachment_id = $this->copy_attachment( $attachment_data['thumbnail'], $new_post_id );
                    if ( $new_attachment_id !== false )
                        update_post_meta( $new_post_id, '_thumbnail_id', $new_attachment_id );
                }
            }
            
            // Sticky behaviour
            $child_post_is_sticky = is_sticky( $new_post_id );
            if ( $post_is_sticky && ! $child_post_is_sticky )
                stick_post( $new_post_id );
            if ( ! $post_is_sticky && $child_post_is_sticky )
                unstick_post( $new_post_id );
            
            if ( $link) {            
                $new_post_broadcast_data = $this->get_post_np_data( $blog_id, $new_post_id );
                $new_post_broadcast_data->set_linked_parent( $original_blog, $post_id );
                $this->set_post_broadcast_data( $blogID, $new_post_id, $new_post_broadcast_data );
            }

            $to_broadcasted_blogs[] = '<a href="' . get_permalink( $new_post_id ) . '">' . get_bloginfo( 'name' ) . '</a>';
            $to_broadcasted_blog_details[] = array( 'blog_id' => $blogID, 'post_id' => $new_post_id, 'inserted' => $need_to_insert_post );
            
            //-------------------------------------------------------------------
            // Add/Update Menu Item Relationship
            //-------------------------------------------------------------------
            if ( $add_menu_item) {
                $this->__add_menu_item_relation($blogID, $menu_item_id, $new_post_id);
            }

            //Remove Linked Childs that are unchecked in update
            if(array_key_exists($blogID, $_prev_linked_children))
                unset($_prev_linked_children[$blogID]);
            
            
            restore_current_blog();
        }
        
        
        // Finished networkPublishing.
        $this->networkPublishing = false;
        
        $this->load_language();
        
        $post_url_and_name = '<a href="' . get_permalink( $post_id ) . '">' . $post['post_title']. '</a>';
        do_action( 'wpmu_activity_monitor_new_activity', array(
            'activity_id' => 'wpmu_published',
            'activity_strings' => array(
                '' => '%user_display_name_with_link% has broadcasted '.$post_url_and_name.' to: ' . implode( ', ', $to_broadcasted_blogs ),
            ),
            'activity_details' => $to_broadcasted_blog_details,
        ) );
        
        
        //Remove Linked Childs that are unchecked in update
        foreach($_prev_linked_children as $_children_to_unlink => $val)
            $broadcast_data->remove_linked_child($_children_to_unlink);
        
        
//        pr($broadcast_data, 1);
        // Save the post broadcast data.
        if ( $link )
            $this->set_post_broadcast_data( $blog_id, $post_id, $broadcast_data );        
    }

    /**
     * Recursively adds the missing ancestors of the given source term at the
     * target blog. 
     * 
     * @param array $source_post_term           The term to add ancestors for
     * @param array $source_post_taxonomy       The taxonomy we're working with
     * @param array $target_blog_terms          The existing terms at the target
     * @param array $source_blog_taxonomy_terms The existing terms at the source
     * @return int The ID of the target parent term
     */
    public function insert_term_ancestors( $source_post_term, $source_post_taxonomy, $target_blog_terms, $source_blog_taxonomy_terms )
    {
        // Fetch the parent of the current term among the source terms
        foreach ( $source_blog_taxonomy_terms as $term )
        {
            if ( $term['term_id'] == $source_post_term['parent'] )
            {
                $source_parent = $term;
            }
        }

        if ( ! isset( $source_parent ) )
        {
            return 0; // Sanity check, the source term's parent doesn't exist! Orphan!
        }

        // Check if the parent already exists at the target
        foreach ( $target_blog_terms as $term )
        {
            if ( $term['slug'] == $source_parent['slug'] )
            {
                // The parent already exists, return its ID
                return $term['term_id'];
            }
        }

        // Does the parent also have a parent, and if so, should we create the parent?
        $target_grandparent_id = 0;
        if ( 0 != $source_parent['parent'] )
        {
            // Recursively insert ancestors, and get the newly inserted parent's ID
            $target_grandparent_id = $this->insert_term_ancestors( $source_parent, $source_post_taxonomy, $target_blog_terms, $source_blog_taxonomy_terms );
        }

        // The target parent does not exist, we need to create it
        $new_term = wp_insert_term(
            $source_parent['name'],
            $source_post_taxonomy,
            array( 
                'slug' => $source_parent['slug'],
                'description' => $source_parent['description'],
                'parent' => $target_grandparent_id,
            )
        );


        return $new_term['term_id'];
    }
    
    public function trash_post( $post_id )
    {
        $this->trash_untrash_delete_post( 'wp_trash_post', $post_id );
    }
    
    public function untrash_post( $post_id )
    {
        $this->trash_untrash_delete_post( 'wp_untrash_post', $post_id );
    }
    
    public function delete_post( $post_id)
    {
        $this->trash_untrash_delete_post( 'wp_delete_post', $post_id );
    }
    
    /**
     * Issues a specific command on all the blogs that this post_id has linked children on. 
     * @param string $command Command to run.
     * @param int $post_id Post with linked children
     */
    private function trash_untrash_delete_post( $command, $post_id)
    {
        global $blog_id;
        $broadcast_data = $this->get_post_np_data( $blog_id, $post_id );
        if ( $broadcast_data->has_linked_children() ) {
            foreach( $broadcast_data->get_linked_children() as $childBlog=>$childPost)
            {
                if ( $command == 'wp_delete_post' ) {
                    // Delete the broadcast data of this child
                    $this->delete_post_broadcast_data( $childBlog, $childPost );
                }
                switch_to_blog( $childBlog );
                $command( $childPost);
                $this->remove_blog_menu_item($childBlog, $childPost);
                restore_current_blog();
            }
        }
        
        if ( $command == 'wp_delete_post' ) {
            
            global $blog_id;
            // Find out if this post has a parent.
            $linked_parent_broadcast_data = $this->get_post_np_data( $blog_id, $post_id );
            $linked_parent_broadcast_data = $linked_parent_broadcast_data->get_linked_parent();
            if ( $linked_parent_broadcast_data !== false) {
                // Remove ourselves as a child.
                $parent_broadcast_data = $this->get_post_np_data( $linked_parent_broadcast_data['blog_id'], $linked_parent_broadcast_data['post_id'] );
                $parent_broadcast_data->remove_linked_child( $blog_id );
                $this->set_post_broadcast_data( $linked_parent_broadcast_data['blog_id'], $linked_parent_broadcast_data['post_id'], $parent_broadcast_data );
            }
            $this->delete_post_broadcast_data( $blog_id, $post_id );
            $this->remove_blog_menu_item($blog_id, $post_id);
        }
    }
    
    public function manage_posts_columns( $defaults ) {
        
        $defaults['wpmu_publish'] = '<span title="'.$this->_( 'Shows which blogs have posts linked to this one' ).'">'.$this->_( 'Network Published' ).'</span>';
        return $defaults;
        
    }
    
    public function manage_posts_custom_column( $column_name, $post_id ) {
        
        global $blog_id, $post;

        if ( $column_name != 'wpmu_publish' )
            return;

        $broadcast_data = $this->get_post_np_data( $blog_id, $post_id );

        if ( $broadcast_data->get_linked_parent() !== false) {

            $parent = $broadcast_data->get_linked_parent();
            $parent_blog_id = $parent['blog_id'];
            switch_to_blog( $parent_blog_id );
            echo $this->_(sprintf( 'Linked from %s', '<a href="' . get_bloginfo( 'url' ) . '/wp-admin/post.php?post=' .$parent['post_id'] . '&action=edit">' . get_bloginfo( 'name' ) . '</a>' ) );
            restore_current_blog();

        }
        
        if ( $broadcast_data->has_linked_children() ) {

            $children = $broadcast_data->get_linked_children();

            if (count( $children) < 0)
                return;

            $display = array(); // An array makes it easy to manipulate lists
            $blogs = $this->cached_blog_list();
            
            foreach( $children as $blogID => $postID) {

                switch_to_blog( $blogID );
                $url_child = get_permalink( $postID );
                restore_current_blog();

                // The post id is for the current blog, not the target blog.
                $url_unlink = wp_nonce_url("profile.php?page=wpmu_network_publish&amp;action=unlink&amp;post=$post_id&amp;child=$blogID", 'broadcast_unlink_' . $blogID . '_' . $post_id );
                $url_trash = wp_nonce_url("profile.php?page=wpmu_network_publish&amp;action=trash&amp;post=$post_id&amp;child=$blogID", 'broadcast_trash_' . $blogID . '_' . $post_id );

                $display[] = '
                              <div class="broadcasted_blog">
                                    <a class="broadcasted_child" href="'.$url_child.'">'.$blogs[$blogID]['blogname'].'</a>
                                    <div class="row-actions broadcasted_blog_actions">
                                        <small>
                                            <a href="'.$url_unlink.'" title="'.$this->_( 'Remove links to this broadcasted child' ).'">'.$this->_( 'Unlink' ).'</a>
                                            | 
                                            <span class="trash"><a href="'.$url_trash.'" title="'.$this->_( 'Put this broadcasted child in the trash' ).'">'.$this->_( 'Trash' ).'</a></span>
                                        </small>
                                    </div>
                                </div>
                              ';

                $display[0] = ' YES ';

            }
            echo '<ul style="height: 200px; overflow-y: scroll;"><li>' . implode( '</li><li>', $display) . '</li></ul>';
        }
        else
            echo 'N/A';


    }
    
    public function post_row_actions( $actions, $post ) {
        
        global $blog_id;
        
        $broadcast_data = $this->get_post_np_data( $blog_id, $post->ID );
        
        return $actions;
        
        if ( $broadcast_data->has_linked_children() )
            $actions = array_merge( $actions, array(
                'broadcast_unlink' => '<a href="'.wp_nonce_url("profile.php?page=wpmu_network_publish&amp;action=unlink&amp;post=".$post->ID."", 'broadcast_unlink_' . $post->ID).'" title="'.$this->_( 'Remove links to all the broadcasted children' ).'">'.$this->_( 'Unlink' ).'</a>',
            ) );

        $actions['broadcast_find_orphans'] = '<a href="'.wp_nonce_url("profile.php?page=wpmu_network_publish&amp;action=find_orphans&amp;post=".$post->ID."", 'broadcast_find_orphans_' . $post->ID).'" title="'.$this->_( 'Find posts on other blogs that are identical to this post' ).'">'.$this->_( 'Find orphans' ).'</a>';
        
        return $actions;
        
    }
    
    public function create_meta_box()  {
        
        if ( $this->role_at_least( $this->get_site_option( 'role_networkPublish' ) ) ) {

            // If the user isn't a site admin, or if the user doesn't have any other blogs to write to...
            if ( $this->role_at_least( 'super_admin' ) || count( $this->list_user_writable_blogs( $this->user_id() ) ) > 0 )    // User always has at least one to write to, if he's gotten THIS far.
            {
                $this->load_language();
                $post_types = $this->get_site_option( 'post_types' );
                foreach( $post_types as $post_type => $ignore )
                    add_meta_box( 'wpmu_network_publish', $this->_( 'Network Publish' ), array( &$this, 'add_meta_box_type' ), $post_type, 'normal', 'high' );
                add_action( 'save_post', array( &$this, 'save_post' ), $this->get_site_option( 'save_post_priority' ) );
            }
            
        }
    }
    
    public function the_permalink( $link ) {
        global $id;
        global $blog_id;
        
        $broadcast_data = $this->get_post_np_data( $blog_id, $id );
        
        $linked_parent = $broadcast_data->get_linked_parent();
        
        if ( $linked_parent === false)
            return $link;

        switch_to_blog( $linked_parent['blog_id'] );
        $returnValue = get_permalink( $linked_parent['post_id'] );
        restore_current_blog();
        return $returnValue;
    }
    
    public function post_link( $link ) {
        
        global $id;
        global $blog_id;
        
        $broadcast_data = $this->get_post_np_data( $blog_id, $id );
        
        $linked_parent = $broadcast_data->get_linked_parent();
        
        if ( $linked_parent === false)
            return $link;

        switch_to_blog( $linked_parent['blog_id'] );
        $post = get_post( $linked_parent['post_id'] );
        $permalink = get_permalink( $post );
        restore_current_blog();
        
        return $permalink;
    }
    
    /**
        @brief        Use the correct canonical link.
    **/
    public function wp_head() {
        // Only override the canonical if we're looking at a single post.
        if ( ! is_single() ) return;

        global $post;
        global $blog_id;

        // Find the parent, if any.
        $broadcast_data = $this->get_post_np_data( $blog_id, $post->ID );
        $linked_parent = $broadcast_data->get_linked_parent();
        if ( $linked_parent === false) return;
        
        // Post has a parent. Get the parent's permalink.
        switch_to_blog( $linked_parent['blog_id'] );
        $url = get_permalink( $linked_parent['post_id'] );
        restore_current_blog();
        
        echo sprintf( '<link rel="canonical" href="%s" />', $url ) . "\n";
        
        // Prevent Wordpress from outputting its own canonical.
        remove_action( 'wp_head', 'rel_canonical' );
    }
    
    // --------------------------------------------------------------------------------------------
    // ----------------------------------------- Misc functions
    // --------------------------------------------------------------------------------------------

    /**
        Creates a new attachment, from the $attachment_data, to a post.
        
        Returns the attachment's post_id.
    */
    private function copy_attachment( $attachment_data, $post_id)
    {
        // Copy the file to the blog's upload directory
        $upload_dir = wp_upload_dir();
        if ( ! file_exists( $attachment_data->filename_path ) )
            return false;

        copy( $attachment_data->filename_path, $upload_dir['path'] . '/' . $attachment_data->filename_base );
        
        // And now create the attachment stuff.
        // This is taken almost directly from http://codex.wordpress.org/Function_Reference/wp_insert_attachment
        $wp_filetype = wp_check_filetype( $attachment_data->filename_base, null );
        $attachment = array(
            'guid' => $upload_dir['url'] . '/' . $attachment_data->filename_base,
            'menu_order' => $attachment_data->menu_order,
            'post_excerpt' => $attachment_data->post_excerpt,
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => $attachment_data->post_title,
            'post_content' => '',
            'post_status' => 'inherit',
        );
        $attach_id = wp_insert_attachment( $attachment, $upload_dir['path'] . '/' . $attachment_data->filename_base, $post_id );
        
        // Now to handle the metadata.
        // 1. Create new metadata for this attachment.
        require_once(ABSPATH . "wp-admin" . '/includes/image.php' );
        $attach_data = wp_generate_attachment_metadata( $attach_id, $upload_dir['path'] . '/' . $attachment_data->filename_base );
        
        // 2. Write the old metadata first.
        foreach( $attachment_data->post_custom as $key => $value )
        {
            $value = reset( $value );
            $value = maybe_unserialize( $value );
            switch( $key )
            {
                // Some values need to handle completely different upload paths (from different months, for example).
                case '_wp_attached_file':
                    $value = $attach_data[ 'file' ];
                    break;
            }
            update_post_meta( $attach_id, $key, $value );
        }
        
        // 3. Overwrite the metadata that needs to be overwritten with fresh data. 
        wp_update_attachment_metadata( $attach_id,  $attach_data );

        return $attach_id;
    }
    
    private function show_group_blogs( $options )
    {
        
        $form = $this->form();
        $returnValue = '<ul class="broadcast_blogs">';
        $nameprefix = "[broadcast][groups][" . $options['nameprefix'] . "]";
        $post_id = $options['post_id'];
//        pr($options, 1);

        foreach( $options['blogs'] as $blog) {
            $blog_id = $blog['blog_id'];    // Convience
            $required = $this->isRequired( $blog_id );
            $checked = isset( $options['selected'][ $blog_id ] ) || $required;
            $input = array(
                'name' => $blog_id,
                'type' => 'checkbox',
                'nameprefix' => $nameprefix,
                'label' => $blog['blogname'],
                'disabled' => isset( $options['disabled'][ $blog_id ] ),
                'readonly' => isset( $options['readonly'][ $blog_id ] ),
                'value' => 'blog_' .$checked,
                'checked' => $checked,
                'title' => $blog['siteurl'],
            );

//            $url_unlink = wp_nonce_url("profile.php?page=wpmu_network_publish&amp;action=unlink&amp;post=".$options['linked_children'][$blog_id]."&amp;child=$blog_id", 'broadcast_unlink_' . $blog_id . '_' . $post_id );
//            $url_trash = wp_nonce_url("profile.php?page=wpmu_network_publish&amp;action=trash&amp;post=".$options['linked_children'][$blog_id]."&amp;child=$blog_id", 'broadcast_trash_' . $blog_id . '_' . $post_id );


            $url_unlink = wp_nonce_url("profile.php?page=wpmu_network_publish&amp;action=unlink&amp;post=$post_id&amp;child=$blog_id", 'broadcast_unlink_' . $blog_id . '_' . $post_id );
            $url_trash = wp_nonce_url("profile.php?page=wpmu_network_publish&amp;action=trash&amp;post=$post_id&amp;child=$blog_id", 'broadcast_trash_' . $blog_id . '_' . $post_id );


            $blog_class = isset( $options['blog_class'][$blog_id] ) ? $options['blog_class'][$blog_id] : '';
            $blog_title = isset( $options['blog_title'][$blog_id] ) ? $options['blog_title'][$blog_id] : '';
            
            $returnValue .= '<li class="'.$blog_class.'" title="'.$blog_title.'">'.
                                $form->make_input( $input).' '.$form->make_label( $input);
            if($checked){
            $returnValue .=     '<div class="actions" >
                                    <small>
                                            <a href="'.$url_unlink.'" title="'.$this->_( 'Remove links to this broadcasted child' ).'">'.$this->_( 'Unlink' ).'</a>
                                            | 
                                            <span class="trash"><a href="'.$url_trash.'" title="'.$this->_( 'Put this broadcasted child in the trash' ).'">'.$this->_( 'Trash' ).'</a></span>
                                    </small>
                                </div>';
            }

            $returnValue .= '</li>';
            
        }
        $returnValue .= '</ul>';
        return $returnValue;
    }
    
    protected function list_user_writable_blogs( $user_id )
    {
        // Super admins can write anywhere they feel like.
        if (is_super_admin() )
        {
            $blogs = $this->get_blog_list();
            $blogs = $this->sort_blogs( $blogs);
            return $blogs;
        }
        
        $blogs = get_blogs_of_user( $user_id );
        foreach( $blogs as $index=>$blog)
        {
            $blog = $this->object_to_array( $blog);
            $blog['blog_id'] = $blog['userblog_id'];
            $blogs[$index] = $blog;
            if (!$this->is_blog_user_writable( $user_id, $blog['blog_id'] ) )
                unset( $blogs[$index] );
        }
        return $this->sort_blogs( $blogs);
    }
    
    protected function is_blog_user_writable( $user_id, $blog_id)
    {
        // If this blog is in the blacklist, reply no.
        if ( $this->is_blog_blacklisted( $blog_id) )
            return false;
            
        // Else, check that the user has write access.
        switch_to_blog( $blog_id );
        
        global $current_user;
        wp_get_current_user();
        $returnValue = current_user_can( 'edit_posts' );
        
        restore_current_blog();
        return $returnValue;
    }
    
    /**
     * Returns whether the site admin has blacklisted the blog.
     */
    protected function isRequired( $blog_id)
    {
        if (is_super_admin() )
            return false;
        $requiredlist = $this->get_site_option( 'requiredlist' );
        $requiredlist = explode( ',', $requiredlist);
        $requiredlist = array_flip( $requiredlist);
        return isset( $requiredlist[$blog_id] );
    }
    
    /**
     * Returns whether the site admin has blacklisted the blog.
     */
    protected function is_blog_blacklisted( $blog_id)
    {
        $blacklist = $this->get_site_option( 'blacklist' );
        if ( $blacklist == '' )
            return false;
        $blacklist = explode( ',', $blacklist);
        $blacklist = array_flip( $blacklist);
        return isset( $blacklist[$blog_id] );
    }
    
    /**
     * Lists ALL of the blogs. Including the main blog.
     */
    protected function get_blog_list()
    {
        $site_id = get_current_site();
        $site_id = $site_id->id;
        
        // Get a custom list of all blogs on this site. This bypasses Wordpress' filter that removes private and mature blogs.
        $blogs = $this->query("SELECT * FROM `".$this->wpdb->base_prefix."blogs` WHERE site_id = '$site_id' ORDER BY blog_id");
        $blogs = $this->array_rekey( $blogs, 'blog_id' );
        
        foreach( $blogs as $blog_id=>$blog)
        {
            $tempBlog = $this->object_to_array(get_blog_details( $blog_id, true) );
            $blogs[$blog_id]['blogname'] = $tempBlog['blogname'];
            $blogs[$blog_id]['siteurl'] = $tempBlog['siteurl'];
            $blogs[$blog_id]['domain'] = $tempBlog['domain'];
        }

        return $this->sort_blogs( $blogs);
    }
    
    /**
        Returns a list of all the, as per admin, required blogs to broadcast to.
    **/
    private function get_required_blogs()
    {
        $requiredBlogs = array_filter( explode( ',', $this->get_site_option( 'requiredlist' ) ) );
        $requiredBlogs = array_flip( $requiredBlogs);
        return $requiredBlogs;
    }

    /**
     * Provides a cached list of blogs.
     * 
     * Since the _SESSION variable isn't saved between page loads this cache function works just fine for once-per-load caching.
     * Keep the list cached for anything longer than a page refresh (a minute?) could mean that it becomes stale - admin creates a
     * new blog or blog access is removed or whatever. 
     */
    private function cached_blog_list()
    {
        $blogs = $this->blogs_cache;
        if ( $blogs === null)
        {
            $blogs = $this->get_blog_list();
            $this->blogs_cache = $blogs;
        }
        return $blogs;        
    }
    
    /**
     * Sorts the blogs by name. The Site Blog is first, no matter the name.
     */
    protected function sort_blogs( $blogs )
    {
        // Make sure the main blog is saved.
        $firstBlog = array_shift( $blogs);
        
        $blogs = self::array_rekey( $blogs, 'blogname' );
        ksort( $blogs);
        
        // Put it back up front.
        array_unshift( $blogs, $firstBlog);
            
        return self::array_rekey( $blogs, 'blog_id' );
    }
    
    /**
     * Retrieves the NetworkData for this post_id.
     * 
     * Will return a fully functional NetworkData class even if the post doesn't have NetworkData.
     * 
     * Use NetworkData->is_empty() to check for that.
     * @param int $post_id Post ID to retrieve data for.
     */
    private function get_post_np_data( $blog_id, $post_id )
    {
        
        require_once( 'NetworkData.php' );
        $returnValue = $this->sql_get_broadcast_data( $blog_id, $post_id );
        $returnValue['linked_children'] = @array_filter($returnValue['linked_children']);
        
//        pr($returnValue, 1);

        if (count( $returnValue) < 1)
            return new NetworkData(array() );
        return new NetworkData( $returnValue);
        
    }
    
    /**
     * Updates / removes the NetworkData for a post.
     * 
     * If the NetworkData->is_empty() then the NetworkData is removed completely.
     * 
     * @param int $blog_id Blog ID to update
     * @param int $post_id Post ID to update
     * @param NetworkData $broadcast_data NetworkData file.
     */
    private function set_post_broadcast_data( $blog_id, $post_id, $broadcast_data )
    {
        require_once( 'NetworkData.php' );
        if ( $broadcast_data->is_modified() )
            if ( $broadcast_data->is_empty() )
                $this->sql_delete_broadcast_data( $blog_id, $post_id );
            else
                $this->sql_update_broadcast_data( $blog_id, $post_id, $broadcast_data->getData() );
    }
    
    /**
        Deletes the broadcast data completely of a post in a blog.
    */
    private function delete_post_broadcast_data( $blog_id, $post_id)
    {
        $this->sql_delete_broadcast_data( $blog_id, $post_id );
    }
    
    private function load_last_used_settings( $user_id)
    {
        $data = $this->sql_user_get( $user_id );
        if (!isset( $data['last_used_settings'] ) )
            $data['last_used_settings'] = array();
        return $data['last_used_settings'];
    }
    
    private function save_last_used_settings( $user_id, $settings)
    {
        $data = $this->sql_user_get( $user_id );
        $data['last_used_settings'] = $settings;
        $this->sql_user_set( $user_id, $data);
    }
    
    private function get_current_blog_taxonomy_terms( $taxonomy )
    {
        $terms = get_terms( $taxonomy, array(
            'hide_empty' => false,
        ) );
        $terms = $this->object_to_array( $terms );
        $terms = $this->array_rekey( $terms, 'term_id' );
        return $terms;
    }
    
    private function sync_terms( $taxonomy, $source_blog_id, $target_blog_id )
    {
        global $wpdb;
        switch_to_blog( $source_blog_id );
        $source_terms = $this->get_current_blog_taxonomy_terms( $taxonomy );
        restore_current_blog();
        
        switch_to_blog( $target_blog_id );

        $target_terms = $this->get_current_blog_taxonomy_terms( $taxonomy );
        
        // Keep track of which terms we've found.
        $found_targets = array();
        $found_sources = array();
        
        // First step: find out which of the target terms exist on the source blog
        foreach( $target_terms as $target_term_id => $target_term )
            foreach( $source_terms as $source_term_id => $source_term )
            {
                if ( isset( $found_sources[ $source_term_id ] ) )
                    continue;
                if ( $source_term['slug'] == $target_term['slug'] )
                {
                    $found_targets[ $target_term_id ] = $source_term_id;
                    $found_sources[ $source_term_id ] = $target_term_id;
                }
            }
        
        // Now we know which of the terms on our target blog exist on the source blog.
        // Next step: see if the parents are the same on the target as they are on the source.
        // "Same" meaning pointing to the same slug.
        foreach( $found_targets as $target_term_id => $source_term_id)
        {
            $parent_of_target_term = $target_terms[ $target_term_id ][ 'parent' ];
            $parent_of_equivalent_source_term = $source_terms[ $source_term_id ]['parent'];
            
            if ( $parent_of_target_term != $parent_of_equivalent_source_term &&
                (isset( $found_sources[ $parent_of_equivalent_source_term ] ) || $parent_of_equivalent_source_term == 0 )
            )
            {
                if ( $parent_of_equivalent_source_term != 0)
                    $new_term_parent = $found_sources[ $parent_of_equivalent_source_term ];
                else
                    $new_term_parent = 0;
                $this->set_term_parent( $taxonomy, $target_term_id, $new_term_parent );
            }
        }
            
        restore_current_blog();
    }
    
    private function set_term_parent( $taxonomy, $term_id, $parent_id )
    {
        wp_update_term( $term_id, $taxonomy, array(
            'parent' => $parent_id,
        ) );

        // wp_update_category alone won't work. The "cache" needs to be cleared.
        // see: http://wordpress.org/support/topic/category_children-how-to-recalculate?replies=4
        delete_option( 'category_children' );
    }
    
    private function keep_valid_custom_fields( $custom_fields)
    {
        foreach( $custom_fields as $key => $array)
            if ( !$this->is_custom_field_valid( $key) )
                unset( $custom_fields[$key] ); 

        return $custom_fields;
    }
    
    private function is_custom_field_valid( $custom_field)
    {
        if ( !isset( $this->custom_field_exceptions_cache) )
            $this->custom_field_exceptions_cache = $this->get_site_option( 'custom_field_exceptions' );

        if ( strpos( $custom_field, '_' ) !== 0 )
            return true;
        
        foreach( $this->custom_field_exceptions_cache as $exception)
            if (strpos( $custom_field, $exception) !== false )
                return true;
        
        return false;
    }
    
    /**
        If networkPublishing, will return $_POST['broadcast'].
        Else false.
    */
    public function is_networkPublishing()
    {
        return $this->networkPublishing !== false;
    }
    
    // --------------------------------------------------------------------------------------------
    // ----------------------------------------- SQL
    // --------------------------------------------------------------------------------------------

    /**
     * Gets the user data.
     * 
     * Returns an array of user data.
     */
    private function sql_user_get( $user_id ) {
        
        $returnValue = $this->query("SELECT * FROM `".$this->wpdb->base_prefix."wpmu_network_publish` WHERE user_id = '$user_id'");
        $returnValue = @unserialize( base64_decode( $returnValue[0]['data'] ) );        // Unserialize the data column of the first row.
        if ( $returnValue === false)
            $returnValue = array();
        
        // Merge/append any default values to the user's data.
        return array_merge(array(
            'groups' => array(),
        ), $returnValue);
        
    }
    
    /**
     * Saves the user data.
     */
    private function sql_user_set( $user_id, $data)    {
        
        $data = serialize( $data);
        $data = base64_encode( $data);
        $this->query("DELETE FROM `".$this->wpdb->base_prefix."wpmu_network_publish` WHERE user_id = '$user_id'");
        $this->query("INSERT INTO `".$this->wpdb->base_prefix."wpmu_network_publish` (user_id, data) VALUES ( '$user_id', '$data' )");
        
    }
    
    private function sql_get_broadcast_data( $blog_id, $post_id)
    {
        $sql = "SELECT `data` FROM `".$this->wpdb->base_prefix."wpmu_network_publish_data` WHERE blog_id = '$blog_id' AND post_id = '$post_id'";
        
        $returnValue = $this->query($sql);
        $returnValue = @unserialize( base64_decode( $returnValue[0]['data'] ) );
        
//        pr($sql);
//        pr($returnValue);
        // Unserialize the data column of the first row.
        if ( $returnValue === false)
            $returnValue = array();
        return $returnValue;
    }
    
    private function sql_delete_broadcast_data( $blog_id, $post_id)
    {
        $this->query("DELETE FROM `".$this->wpdb->base_prefix."wpmu_network_publish_data` WHERE blog_id = '$blog_id' AND post_id = '$post_id'");
    }
        
    private function sql_update_broadcast_data( $blog_id, $post_id, $data)
    {
        $data = serialize( $data);
        $data = base64_encode( $data);
        $this->sql_delete_broadcast_data( $blog_id, $post_id );
        $this->query("INSERT INTO `".$this->wpdb->base_prefix."wpmu_network_publish_data` (blog_id, post_id, data) VALUES ( '$blog_id', '$post_id', '$data' )");
    }
    
    
    //-----------------------------------------------------------------------------------------------------
    //      Network Menu Functions
    //-----------------------------------------------------------------------------------------------------
    
    private function __add_menu_item( $menu_title, $post_id, $menuType )
    {
        $data = get_post($post_id);
        $_sql = "INSERT INTO `".$this->wpdb->base_prefix."wmpu_network_publish_menu` (title, alias, parent, ordering, published, menutype) VALUES ( '$menu_title', '".$this->stringURLSafe($menu_title)."', '$data->post_parent', '$data->menu_order', '1', '$menuType' )";
        $this->query($_sql);
//        pr($_sql);
        return $this->wpdb->insert_id;
    }
    
    private function __update_menu_item( $menu_id, $menu_title, $post_id, $menuType )
    {
        $data = get_post($post_id);
        $_sql = "UPDATE `".$this->wpdb->base_prefix."wmpu_network_publish_menu` SET title='$menu_title', alias='".$this->stringURLSafe($menu_title)."', parent='$data->post_parent', ordering='$data->menu_order', menutype='$menuType' WHERE id = $menu_id";
        
//        pr($_sql, 1);
        $this->query($_sql);
        
        return $this->wpdb->insert_id;
    }
    
    private function __add_menu_item_relation( $blog_id, $menu_id, $post_id )
    {
        $_sql = "INSERT INTO `".$this->wpdb->base_prefix."wpmu_network_publish_menu_to_blog` (blog_id, menu_id, post_id) VALUES ( '$blog_id',  '$menu_id', '$post_id' )";
//        pr($_sql);
        $this->query($_sql);
    }
    
    private function remove_blog_menu_item( $blog_id, $post_id )
    {
        $_sql = "DELETE FROM `".$this->wpdb->base_prefix."wpmu_network_publish_menu_to_blog` WHERE blog_id = '$blog_id' AND post_id = '$post_id' ";
//        pr($_sql);
        $this->query($_sql);
    }

    
    private function __reset_menu_relation($menu_id){
        $_sql = "DELETE FROM `".$this->wpdb->base_prefix."wpmu_network_publish_menu_to_blog` WHERE `menu_id` = '$menu_id'";
//        pr($_sql);
        $this->query($_sql);
    }
    
    
    private function __chk_blog_menu_item( $blog_id, $post_id, $menulabel, $menuType = 1) {
        
        //Blog Specific SQL
        $sql = "SELECT wm.id FROM `".$this->wpdb->base_prefix."wmpu_network_publish_menu` wm
                JOIN `".$this->wpdb->base_prefix."wpmu_network_publish_menu_to_blog` wm2b ON wm.id = wm2b.menu_id
                JOIN `".$this->wpdb->base_prefix."wmpu_network_publish_menutype` wmt ON wm.menutype = wmt.id
                WHERE wm2b.blog_id = '$blog_id'
                AND (wm2b.post_id='$post_id' || wm.alias='".$this->stringURLSafe($menulabel)."')
                AND wmt.id = '$menuType'
                GROUP BY wm.id
                ";
        //We want it to Blog Independednt
        $sql = "SELECT wm.id FROM `".$this->wpdb->base_prefix."wmpu_network_publish_menu` wm
                JOIN `".$this->wpdb->base_prefix."wpmu_network_publish_menu_to_blog` wm2b ON wm.id = wm2b.menu_id
                JOIN `".$this->wpdb->base_prefix."wmpu_network_publish_menutype` wmt ON wm.menutype = wmt.id
                WHERE wmt.id = '$menuType'
                AND (wm2b.post_id='$post_id' || wm.alias='".$this->stringURLSafe($menulabel)."')
                GROUP BY wm.id
                ";
//        pr($sql, 1);
        $returnValue = $this->query($sql);
        
//        pr($returnValue, 1);
        
        if ( count($returnValue) > 0 )
            return $returnValue[0]['id'];
        else
            return false;
        
    }
    
    
    private function __update_blog_menu_item( $menu_title, $blog_id, $post_id, $menuType = 1) {
        
        if($menu_id = $this->__chk_blog_menu_item($blog_id, $post_id, '', $menuType)){
            $this->__update_menu_item($menu_id, $menu_title, $post_id, $menuType);
        }else{
            $menu_id = $this->__add_menu_item($menu_title, $post_id, $menuType);
        }
        $this->__add_menu_item_relation($blog_id, $menu_id, $post_id);
    }
    
    public function __display_np_menu_arr( $menuType = 1, $parent = 0 ) {
        
        global $blog_id;
        $menu_items = array();
        
        $sql = "
                SELECT wm2b.post_id, wm.*  FROM `".$this->wpdb->base_prefix."wmpu_network_publish_menu` wm
                    JOIN `".$this->wpdb->base_prefix."wpmu_network_publish_menu_to_blog` wm2b ON wm.id = wm2b.menu_id
                    JOIN `".$this->wpdb->base_prefix."wmpu_network_publish_menutype` wmt ON wm.menutype = wmt.id
                    WHERE wmt.id = '$menuType' AND wm.parent = '$parent'
                    GROUP BY wm.id
                    ORDER BY wm.ordering
                ";
        
        $returnValue = $this->query($sql);
        
        foreach($returnValue as $key=>$menu_item){
            $menu_items[$menu_item['id']] = $menu_item;
            $submenus = $this->__display_np_menu_arr($menuType, $menu_item['post_id']);
            $menu_items[$menu_item['id']]['child'] = $submenus;
        }
        
        return $menu_items;
        
    }
    
    public function __display_np_menu( $menuType = 1, $parent = 0 ) {
        
        global $blog_id;

        $sql = "
                SELECT wm2b.post_id, wm.*  FROM `".$this->wpdb->base_prefix."wmpu_network_publish_menu` wm
                    JOIN `".$this->wpdb->base_prefix."wpmu_network_publish_menu_to_blog` wm2b ON wm.id = wm2b.menu_id
                    JOIN `".$this->wpdb->base_prefix."wmpu_network_publish_menutype` wmt ON wm.menutype = wmt.id
                    WHERE wmt.id = '$menuType'
                        AND wm2b.blog_id = $blog_id
                    ORDER BY wm.ordering
                ";
        
        $allItems = $this->query($sql);

        return $this->menu_items = $this->build_nav($allItems, 0);

    }
    
    public function __display_np_menu_alias( $menualias = 'mainmenu', $parent = 0 ) {
        
        global $blog_id;

        $sql = "
                SELECT wm2b.post_id, wm.title, wm.parent,  wm.ordering FROM `".$this->wpdb->base_prefix."wmpu_network_publish_menu` wm
                    JOIN `".$this->wpdb->base_prefix."wpmu_network_publish_menu_to_blog` wm2b ON wm.id = wm2b.menu_id
                    JOIN `".$this->wpdb->base_prefix."wmpu_network_publish_menutype` wmt ON wm.menutype = wmt.id
                    WHERE wmt.alias = '$menualias'
                        AND wm2b.blog_id = $blog_id
                    ORDER BY wm.ordering
                ";
//        pr($sql, 1);
        $allItems = $this->query($sql);
//        pr($allItems, 1);
        return $this->menu_items = $this->build_nav($allItems, 0);

    }
    
    
    function build_nav($items, $parent = NULL) {
        
        $hasChildren = false;
        if($parent == 0)
            $outputHtml = '<ul class="menu" id="menu-main-menu">%s</ul>';
        else
            $outputHtml = '<ul class="sub-menu">%s</ul>';
            
        $childrenHtml = '';
        $i = 0;
        foreach($items as $item)
        {
            if ($item['parent'] == $parent) {
                $class .= " menu-item menu-item-type-post_type menu-item-object-page ";
                if($i == 0) $class .= " first";
                
                $hasChildren = true;
                $childrenHtml .= '<li class="'.$class.'" id="menu-item-'.$item['post_id'].'">';
//                    $childrenHtml .= '<a href="'.get_permalink( $item['post_id'] ).'">'.$item['title'].' ( '.$item[post_id].' )</a>';
                    $childrenHtml .= '<a href="'.get_permalink( $item['post_id'] ).'">'.$item['title'].'</a>';
                    $childrenHtml .= $this->build_nav($items, $item['post_id']);
                $childrenHtml .= '</li>';
                
                $i++;
            }
        }

        // Without children, we do not need the <ul> tag.
        if (!$hasChildren) {
            $outputHtml = '';
        }

        // Returns the HTML
        return sprintf($outputHtml, $childrenHtml);

    }
    
    
    function stringURLSafe($string){
        //remove any '-' from the string since they will be used as concatenaters
        $str = str_replace('-', ' ', $string);
        // Convert certain symbols to letter representation
        $str = str_replace(array('&', '"', '<', '>'), array('a', 'q', 'l', 'g'), $str);
        // Lowercase and trim
        $str = trim(strtolower($str));
        // Remove any duplicate whitespace, and ensure all characters are alphanumeric
        return preg_replace(array('/\s+/','/[^A-Za-z0-9\-]/'), array('-',''), $str);
    }
    
    
}