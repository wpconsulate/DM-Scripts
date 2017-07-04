<?php
/**
 * Include and setup custom metaboxes and fields.
 *
 * @category YourThemeOrPlugin
 * @package  Metaboxes
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/jaredatch/Custom-Metaboxes-and-Fields-for-WordPress
 */

add_filter( 'dm_fields', 'dm_sample_metaboxes' );
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function dm_sample_metaboxes( array $meta_boxes ) {

	// Start with an underscore to hide fields from custom fields list
	

	$meta_boxes['test_metabox'] = array(
		'id'         => 'test_metabox',
		'title'      => __( 'Test Metabox', 'dm' ),
		'pages'      => array( 'page', 'services' ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		// 'dm_styles' => true, // Enqueue the DM stylesheet on the frontend
		'fields'     => array(
			array(
				'name' => __( 'Test Text', 'dm' ),
				'desc' => __( 'field description (optional)', 'dm' ),
				'id'   => $prefix . 'test_text',
				'type' => 'text',
				// 'repeatable' => true,
				// 'on_front' => false, // Optionally designate a field to wp-admin only
			),
			array(
				'name' => __( 'Test Text Small', 'dm' ),
				'desc' => __( 'field description (optional)', 'dm' ),
				'id'   => $prefix . 'test_textsmall',
				'type' => 'text_small',
				// 'repeatable' => true,
			),
			array(
				'name' => __( 'Test Text Medium', 'dm' ),
				'desc' => __( 'field description (optional)', 'dm' ),
				'id'   => $prefix . 'test_textmedium',
				'type' => 'text_medium',
				// 'repeatable' => true,
			),
			array(
				'name' => __( 'Website URL', 'dm' ),
				'desc' => __( 'field description (optional)', 'dm' ),
				'id'   => $prefix . 'url',
				'type' => 'text_url',
				// 'protocols' => array('http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet'), // Array of allowed protocols
				// 'repeatable' => true,
			),
			array(
				'name' => __( 'Test Text Email', 'dm' ),
				'desc' => __( 'field description (optional)', 'dm' ),
				'id'   => $prefix . 'email',
				'type' => 'text_email',
				// 'repeatable' => true,
			),
			array(
				'name' => __( 'Test Time', 'dm' ),
				'desc' => __( 'field description (optional)', 'dm' ),
				'id'   => $prefix . 'test_time',
				'type' => 'text_time',
			),
			array(
				'name' => __( 'Time zone', 'dm' ),
				'desc' => __( 'Time zone', 'dm' ),
				'id'   => $prefix . 'timezone',
				'type' => 'select_timezone',
			),
			array(
				'name' => __( 'Test Date Picker', 'dm' ),
				'desc' => __( 'field description (optional)', 'dm' ),
				'id'   => $prefix . 'test_textdate',
				'type' => 'text_date',
			),
			array(
				'name' => __( 'Test Date Picker (UNIX timestamp)', 'dm' ),
				'desc' => __( 'field description (optional)', 'dm' ),
				'id'   => $prefix . 'test_textdate_timestamp',
				'type' => 'text_date_timestamp',
				// 'timezone_meta_key' => $prefix . 'timezone', // Optionally make this field honor the timezone selected in the select_timezone specified above
			),
			array(
				'name' => __( 'Test Date/Time Picker Combo (UNIX timestamp)', 'dm' ),
				'desc' => __( 'field description (optional)', 'dm' ),
				'id'   => $prefix . 'test_datetime_timestamp',
				'type' => 'text_datetime_timestamp',
			),
			array(
				'name' => __( 'Test Date/Time Picker/Time zone Combo (serialized DateTime object)', 'dm' ),
				'desc' => __( 'field description (optional)', 'dm' ),
				'id'   => $prefix . 'test_datetime_timestamp_timezone',
				'type' => 'text_datetime_timestamp_timezone',
			),
			array(
				'name'   => __( 'Test Money', 'dm' ),
				'desc'   => __( 'field description (optional)', 'dm' ),
				'id'     => $prefix . 'test_textmoney',
				'type'   => 'text_money',
				// 'before' => '£', // override '$' symbol if needed
				// 'repeatable' => true,
			),
			array(
				'name' => __( 'Test Color Picker', 'dm' ),
				'desc' => __( 'field description (optional)', 'dm' ),
				'id'   => $prefix . 'test_colorpicker',
				'type' => 'colorpicker',
				'std'  => '#ffffff'
			),
			array(
				'name' => __( 'Test Text Area', 'dm' ),
				'desc' => __( 'field description (optional)', 'dm' ),
				'id'   => $prefix . 'test_textarea',
				'type' => 'textarea',
			),
			array(
				'name' => __( 'Test Text Area Small', 'dm' ),
				'desc' => __( 'field description (optional)', 'dm' ),
				'id'   => $prefix . 'test_textareasmall',
				'type' => 'textarea_small',
			),
			array(
				'name' => __( 'Test Text Area for Code', 'dm' ),
				'desc' => __( 'field description (optional)', 'dm' ),
				'id'   => $prefix . 'test_textarea_code',
				'type' => 'textarea_code',
			),
			array(
				'name' => __( 'Test Title Weeeee', 'dm' ),
				'desc' => __( 'This is a title description', 'dm' ),
				'id'   => $prefix . 'test_title',
				'type' => 'title',
			),
			array(
				'name'    => __( 'Test Select', 'dm' ),
				'desc'    => __( 'field description (optional)', 'dm' ),
				'id'      => $prefix . 'test_select',
				'type'    => 'select',
				'options' => array(
					array( 'name' => __( 'Option One', 'dm' ), 'value' => 'standard', ),
					array( 'name' => __( 'Option Two', 'dm' ), 'value' => 'custom', ),
					array( 'name' => __( 'Option Three', 'dm' ), 'value' => 'none', ),
				),
			),
			array(
				'name'    => __( 'Test Radio inline', 'dm' ),
				'desc'    => __( 'field description (optional)', 'dm' ),
				'id'      => $prefix . 'test_radio_inline',
				'type'    => 'radio_inline',
				'options' => array(
					array( 'name' => __( 'Option One', 'dm' ), 'value' => 'standard', ),
					array( 'name' => __( 'Option Two', 'dm' ), 'value' => 'custom', ),
					array( 'name' => __( 'Option Three', 'dm' ), 'value' => 'none', ),
				),
			),
			array(
				'name'    => __( 'Test Radio', 'dm' ),
				'desc'    => __( 'field description (optional)', 'dm' ),
				'id'      => $prefix . 'test_radio',
				'type'    => 'radio',
				'options' => array(
					array( 'name' => __( 'Option One', 'dm' ), 'value' => 'standard', ),
					array( 'name' => __( 'Option Two', 'dm' ), 'value' => 'custom', ),
					array( 'name' => __( 'Option Three', 'dm' ), 'value' => 'none', ),
				),
			),
			array(
				'name'     => __( 'Test Taxonomy Radio', 'dm' ),
				'desc'     => __( 'field description (optional)', 'dm' ),
				'id'       => $prefix . 'text_taxonomy_radio',
				'type'     => 'taxonomy_radio',
				'taxonomy' => 'category', // Taxonomy Slug
			),
			array(
				'name'     => __( 'Test Taxonomy Select', 'dm' ),
				'desc'     => __( 'field description (optional)', 'dm' ),
				'id'       => $prefix . 'text_taxonomy_select',
				'type'     => 'taxonomy_select',
				'taxonomy' => 'category', // Taxonomy Slug
			),
			array(
				'name'		=> __( 'Test Taxonomy Multi Checkbox', 'dm' ),
				'desc'		=> __( 'field description (optional)', 'dm' ),
				'id'		=> $prefix . 'test_multitaxonomy',
				'type'		=> 'taxonomy_multicheck',
				'taxonomy'	=> 'post_tag', // Taxonomy Slug
			),
			array(
				'name' => __( 'Test Checkbox', 'dm' ),
				'desc' => __( 'field description (optional)', 'dm' ),
				'id'   => $prefix . 'test_checkbox',
				'type' => 'checkbox',
			),
			array(
				'name'    => __( 'Test Multi Checkbox', 'dm' ),
				'desc'    => __( 'field description (optional)', 'dm' ),
				'id'      => $prefix . 'test_multicheckbox',
				'type'    => 'multicheck',
				'options' => array(
					'check1' => __( 'Check One', 'dm' ),
					'check2' => __( 'Check Two', 'dm' ),
					'check3' => __( 'Check Three', 'dm' ),
				),
			),
			array(
				'name'    => __( 'Test wysiwyg', 'dm' ),
				'desc'    => __( 'field description (optional)', 'dm' ),
				'id'      => $prefix . 'test_wysiwyg',
				'type'    => 'wysiwyg',
				'options' => array(	'textarea_rows' => 5, ),
			),
			array(
				'name' => __( 'Test Image', 'dm' ),
				'desc' => __( 'Upload an image or enter a URL.', 'dm' ),
				'id'   => $prefix . 'test_image',
				'type' => 'file',
			),
			array(
				'name' => __( 'Multiple Files', 'dm' ),
				'desc' => __( 'Upload or add multiple images/attachments.', 'dm' ),
				'id'   => $prefix . 'test_file_list',
				'type' => 'file_list',
			),
			array(
				'name' => __( 'oEmbed', 'dm' ),
				'desc' => __( 'Enter a youtube, twitter, or instagram URL. Supports services listed at <a href="http://codex.wordpress.org/Embeds">http://codex.wordpress.org/Embeds</a>.', 'dm' ),
				'id'   => $prefix . 'test_embed',
				'type' => 'oembed',
			),
		),
	);

	$meta_boxes['about_page_metabox'] = array(
		'id'         => 'about_page_metabox',
		'title'      => __( 'About Page Metabox', 'dm' ),
		'pages'      => array( 'page', ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'show_on'    => array( 'key' => 'id', 'value' => array( 2, ), ), // Specific post IDs to display this metabox
		'fields' => array(
			                array(
				                'name' => __( 'Test Text', 'dm' ),
				                'desc' => __( 'field description (optional)', 'dm' ),
				                'id'   => $prefix . 'test_text',
				                'type' => 'text',
			                ),
		            )
	);

	/**
	 * Metabox for the user profile screen
	 */
	$meta_boxes['user_edit'] = array(
		'id'         => 'user_edit',
		'title'      => __( 'User Profile Metabox', 'dm' ),
		'pages'      => array( 'user' ), // Tells DM to use user_meta vs post_meta
		'show_names' => true,
		// 'dm_styles' => true, // Show dm bundled styles.. not needed on user profile page
		'fields'     => array(
			array(
				'name' => __( 'Extra Info', 'dm' ),
				'desc' => __( 'field description (optional)', 'dm' ),
				'id'   => $prefix . 'exta_info',
				'type' => 'title',
				'on_front' => false,
			),
			array(
				'name' => __( 'Avatar', 'dm' ),
				'desc' => __( 'field description (optional)', 'dm' ),
				'id'   => $prefix . 'avatar',
				'type' => 'file',
				'save_id' => true,
			),
			array(
				'name' => __( 'Facebook URL', 'dm' ),
				'desc' => __( 'field description (optional)', 'dm' ),
				'id'   => $prefix . 'facebookurl',
				'type' => 'text_url',
			),
			array(
				'name' => __( 'Twitter URL', 'dm' ),
				'desc' => __( 'field description (optional)', 'dm' ),
				'id'   => $prefix . 'twitterurl',
				'type' => 'text_url',
			),
			array(
				'name' => __( 'Google+ URL', 'dm' ),
				'desc' => __( 'field description (optional)', 'dm' ),
				'id'   => $prefix . 'googleplusurl',
				'type' => 'text_url',
			),
			array(
				'name' => __( 'Linkedin URL', 'dm' ),
				'desc' => __( 'field description (optional)', 'dm' ),
				'id'   => $prefix . 'linkedinurl',
				'type' => 'text_url',
			),
			array(
				'name' => __( 'User Field', 'dm' ),
				'desc' => __( 'field description (optional)', 'dm' ),
				'id'   => $prefix . 'user_text_field',
				'type' => 'text',
			),
		)
	);

	// Add other metaboxes as needed

	return $meta_boxes;
}

add_action( 'init', 'dm_initialize_dm_fieldses', 9999 );
/**
 * Initialize the metabox class.
 */
function dm_initialize_dm_fieldses() {

	if ( ! class_exists( 'dm_fields' ) )
		require_once 'init.php';

}
