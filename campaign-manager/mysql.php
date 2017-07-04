<?php
include("../../../wp-config.php");

global $wpdb;
  $old_blog = $wpdb->blogid;

			// Get all blog ids
			$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
			foreach ($blogids as $blog_id) {
			if($blog_id==1) continue;
				$prefix="wp_{$blog_id}_";
				$sql="
CREATE TABLE IF NOT EXISTS `{$prefix}campaign_list_rel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `list_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{$prefix}campaigns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_name` varchar(255) NOT NULL,
  `sent_date` date DEFAULT NULL,
  `categoryId` int(11) DEFAULT NULL,
  `propertyId` int(11) NOT NULL,
  `active` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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


CREATE TABLE IF NOT EXISTS `{$prefix}contacts` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `list_id` int(255) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `{$prefix}lists` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `list_name` varchar(200) NOT NULL,
  `date` date NOT NULL,
  `categoryId` int(100) NOT NULL,
  `active` int(100) NOT NULL DEFAULT '1',
  `description` text NOT NULL,
  `tags` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `{$prefix}messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_name` varchar(255) NOT NULL,
  `message` blob NOT NULL,
  `date` date NOT NULL,
  `campaignID` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `active` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `{$prefix}properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_name` varchar(255) NOT NULL,
  `prop_status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `{$prefix}sendgrids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `{$prefix}sendgrid_status` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{$prefix}templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(255) NOT NULL,
  `preview` text NOT NULL,
  `header` text NOT NULL,
  `footer` text NOT NULL,
  `created_date` datetime NOT NULL,
  `active` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
";
//echo $sql."<br/><br/>";
echo $wpdb->prepare($sql);
//var_dump($wpdb->print_error()) or die(mysql_error());			
}
        
