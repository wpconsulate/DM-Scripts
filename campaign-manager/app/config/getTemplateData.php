<?php
	global $wpdb;

	if(1){  //if(is_admin()){
	
		if(!empty($getTtemplateId)){
			//Fetch data for the property selected.
			$psql = "SELECT * FROM ". $wpdb->base_prefix."properties WHERE `id` = $propertySelected ";
			$propObjects = $wpdb->get_results($psql);
			//echo '<pre>';print_r($propObjects); echo '</pre>';
		
			$propid = $propObjects[0]->id;
			$property_name = $propObjects[0]->property_name;
			$twitter_url = $propObjects[0]->twitter_url;
			$facebook_url = $propObjects[0]->facebook_url;
			$pinterest_url = $propObjects[0]->pinterest_url;
			$google_url = $propObjects[0]->google_url;
			$youtube_url = $propObjects[0]->youtube_url;
			$streetaddress = $propObjects[0]->streetaddress;
			$city = $propObjects[0]->city;
			$state = $propObjects[0]->state;
			$zip = $propObjects[0]->zip;
			$phone = $propObjects[0]->phone;
			$email = $propObjects[0]->email;
			$property_domain_url = $propObjects[0]->property_domain_url;


			$tableName = 'templates';
			$sql = "SELECT  * 
				FROM ". $wpdb->base_prefix."".$tableName." 
				WHERE active = 1 AND id = ".$getTtemplateId ;
			$objects = $wpdb->get_results($sql);

			$header = $objects[0]->header;
			$footer = $objects[0]->footer;
			global $blog_id; 
			$domain_name_holder = $property_domain_url; //get_site_url(); //output: http://hillplaceapts.com 
			$domainnameparts = explode('//',$domain_name_holder);
		/**
			// EDITED: 07 Jan 13 sam
			//This edit is to translate {email_logo} with src attribute
			 $email_logo_path = '<img width="254" height="114" id="logoImage" src="http://somdev1.us/wp-content/plugins/campaign-manager/images/logo/'.$blog_id.'.png" >';
		*/
			$email_logo_path = 'http://hillplaceapts.com/wp-content/plugins/campaign-manager/images/logo/'.$propid.'.png';
			$header = stripslashes($header);
			$footer = stripslashes($footer);

			$footer = str_replace("{email_logo}", $email_logo_path, $footer);
			$footer = str_replace("{domain_url}", $domain_name_holder, $footer); ///output: http://hillplaceapts.com
			$footer = str_replace("{domain}", $domainnameparts[1], $footer); ///output: hillplaceapts.com
			$footer = str_replace("{property_name}", $property_name, $footer);
			$footer = str_replace("{streetaddress}", $streetaddress, $footer);
			$footer = str_replace("{city}", $city, $footer);
			$footer = str_replace("{state}", $state, $footer);
			$footer = str_replace("{zip}", $zip, $footer);
			$footer = str_replace("{phone}", $phone, $footer);
			$footer = str_replace("{email}", $email, $footer);
			
			
                        if($twitter_url && $twitter_url !="#"){
			       $footer = str_replace("{twitter_url}", $twitter_url, $footer);
                        }else{
                                $footer = str_replace("{twitter_url}", $twitter_url, $footer);
                                $footer = str_replace("$domain_name_holder/email_images/t.gif","$domain_name_holder/email_images/t.gif \" style='display:none'",$footer);
                        }
                        if($facebook_url && $facebook_url !="#"){
			       $footer = str_replace("{facebook_url}", $facebook_url, $footer);
                        }else{
                                $footer = str_replace("{facebook_url}", $facebook_url, $footer);
                                $footer = str_replace("$domain_name_holder/email_images/f.gif","$domain_name_holder/email_images/f.gif \" style='display:none'",$footer);
                                
                        }
                        if($pinterest_url && $pinterest_url !="#"){
			      $footer = str_replace("{pinterest_url}", $pinterest_url, $footer);
                        }else{
                                $footer = str_replace("{pinterest_url}", $pinterest_url, $footer);
                                $footer = str_replace("$domain_name_holder/email_images/p.gif","$domain_name_holder/email_images/p.gif \" style='display:none'",$footer);
                        }
			if($google_url && $google_url !="#"){
			      $footer = str_replace("{google_url}", $google_url, $footer);
                        }else{
                                $footer = str_replace("{google_url}", "", $footer);
                                $footer = str_replace("$domain_name_holder/email_images/g.gif","$domain_name_holder/email_images/g.gif \" style='display:none'",$footer);
                        }
			if($youtube_url && $youtube_url !="#"){
			      $footer = str_replace("{youtube_url}", $youtube_url, $footer);
                        }else{
                                 $footer = str_replace("{youtube_url}", $youtube_url, $footer);
                                 $footer = str_replace("$domain_name_holder/email_images/y.gif","$domain_name_holder/email_images/y.gif \" style='display:none'",$footer);
                        }
			
			
			echo $footer;
			//echo "image $domain_name_holder/email_images/g.gif";
		}
	}
?>
