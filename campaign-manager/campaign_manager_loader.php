<?php

class CampaignManagerLoader extends MvcPluginLoader {

	var $db_version = '1.0';
	var $tables = array();

	function activate() {
	
		// This call needs to be made to activate this app within WP MVC
		
		$this->activate_app(__FILE__);
		
		// Perform any databases modifications related to plugin activation here, if necessary

		require_once ABSPATH.'wp-admin/includes/upgrade.php';
	
		add_option('campaign_manager_db_version', $this->db_version);
	/*	
global $wpdb;
  $old_blog = $wpdb->blogid;
  $sql="";
  $blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
  foreach ($blogids as $blog_id) {
  	if($blog_id==1) $prefix="wp_";
	else $prefix="wp_{$blog_id}_";
     $sql.=
	"CREATE TABLE IF NOT EXISTS `{$prefix}campaigns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_name` varchar(255) NOT NULL,
  `sent_date` date DEFAULT NULL,
  `categoryId` int(11) DEFAULT NULL,
  `propertyId` int(11) NOT NULL,
  `active` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{$prefix}campaign_list_rel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `list_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `{$prefix}camps` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `list_name` varchar(200) NOT NULL,
  `date` date NOT NULL,
  `categoryId` int(100) NOT NULL,
  `active` int(100) NOT NULL DEFAULT '1',
  `description` text NOT NULL,
  `tags` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{$prefix}categories` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(200) NOT NULL,
  `active` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{$prefix}campaign_category_rel` (
`id` INT NOT NULL AUTO_INCREMENT ,
`campaign_id` INT NOT NULL ,
`category_id` INT NOT NULL ,
PRIMARY KEY ( `id` )
);

CREATE TABLE IF NOT EXISTS `{$prefix}list_category_rel` (
`id` INT NOT NULL AUTO_INCREMENT ,
`list_id` INT NOT NULL ,
`category_id` INT NOT NULL ,
PRIMARY KEY ( `id` )
);

CREATE TABLE IF NOT EXISTS `{$prefix}message_category_rel` (
`id` INT NOT NULL AUTO_INCREMENT ,
`message_id` INT NOT NULL ,
`category_id` INT NOT NULL ,
PRIMARY KEY ( `id` )
);

CREATE TABLE IF NOT EXISTS `{$prefix}contacts` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `list_id` int(255) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{$prefix}emailsettings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from` varchar(50) NOT NULL,
  `reply` varchar(50) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{$prefix}lists` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `list_name` varchar(200) NOT NULL,
  `date` date NOT NULL,
  `categoryId` int(100) NOT NULL,
  `active` int(100) NOT NULL DEFAULT '1',
  `description` text NOT NULL,
  `tags` varchar(100) NOT NULL,
  `propertyId` int(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{$prefix}templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(255) NOT NULL,
  `preview` text NOT NULL,
  `header` text NOT NULL,
  `footer` text NOT NULL,
  `created_date` datetime NOT NULL,
  `active` int(11) NOT NULL,
  `previewImgName` varchar(255) NOT NULL,
  `propertyId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
";
}
$sql.="CREATE TABLE IF NOT EXISTS `wp_capabilities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(50) NOT NULL,
  `capability` varchar(75) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `wp_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_name` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `date` date NOT NULL,
  `campaignID` int(11) NOT NULL,
  `propertyID` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `active` int(11) NOT NULL,
  `reminder` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `wp_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_name` varchar(255) NOT NULL,
  `prop_status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

INSERT INTO `wp_properties` (`id`, `property_name`, `prop_status`) VALUES
(1, '2818 Place', 1),
(2, '901 Place', 1),
(3, 'Allston Place / Stadium Place', 1),
(4, 'Avalon Place', 1),
(5, 'Bayside Village', 1),
(6, 'Bradford Place', 1),
(7, 'Brook Place', 1),
(8, 'Bryant Place', 1),
(9, 'Campus Crossing', 1),
(10, 'Cardinal Towne', 1),
(11, 'First Street Place', 1),
(12, 'Hawks Landing', 1),
(13, 'High View Place', 1),
(14, 'Hill Country Place', 1),
(15, 'Hill Place', 1),
(16, 'Huntsville Place', 1),
(17, 'Lafayette Place', 1),
(18, 'Legends Place', 1),
(19, 'Level 27', 1),
(20, 'Level 51 ten', 1),
(21, 'Life''s Village Retreat', 1),
(22, 'Lorenzo', 1),
(23, 'Maverick Place', 1),
(24, 'Parkway Place', 1),
(25, 'Pembroke Place', 1),
(26, 'Rebel Place', 1),
(27, 'Spring Place', 1),
(28, 'St. Joe Place', 1),
(29, 'Stadium View', 1),
(30, 'Tailor Lofts', 1),
(31, 'The Edge', 1),
(32, 'The Granada on Hardy', 1),
(33, 'The Lex', 1),
(34, 'The Luxe', 1),
(35, 'The Pavilion at North Grounds', 1),
(36, 'The Summit at Coates Run', 1),
(37, 'The Uptown East', 1),
(38, 'University Edge ETSU', 1),
(39, 'University Edge UC', 1),
(40, 'Varsity Place', 1),
(41, 'Veranda Place', 1),
(42, 'Vista Place', 1),
(43, 'West 27th Place', 1);

CREATE TABLE IF NOT EXISTS `wp_sendgrid_status` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `event` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `category` tinyint(200) NOT NULL,
  `campaign` tinyint(200) NOT NULL,
  `list` tinyint(200) NOT NULL,
  `property` tinyint(100) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `wp_sendgrids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

    dbDelta($sql);
*/		
	}

	function deactivate() {
	
		
		$this->deactivate_app(__FILE__);
		//$sql="DROP TABLE `access`,`browsers`,`dept`,`extra`,`todo`,`users`,`user_access`,`user_dept`,`user_extra`";
		//dbDelta($sql);
		// Perform any databases modifications related to plugin deactivation here, if necessary
	
	}

}

?>
