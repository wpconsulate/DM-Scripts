<?php
require_once("../wp-config.php");
$user_count=0;
global $wpdb;
$listid=$listInsertID;
$sel_list_rel= $wpdb->get_results("SELECT `residents`, `parents`, `prospects` FROM `".$wpdb->prefix."list_rel_onesite` WHERE `list_id`='".$listid."'");//retrieve conditions for collecting contacts

if($sel_list_rel[0]->residents != ""){

	$terms=explode(";",$sel_list_rel[0]->residents);
	foreach($terms as $term){

		$term_stmt=explode(":",$term);
		$term_key=$term_stmt[0];
		$term_value=$term_stmt[1];
		if($term_key=="school_term"){//if condition is school term
			if($term_value=="All"){
			$sel_contacts= $wpdb->get_var( $wpdb->prepare( "SELECT count(*) as count FROM '".$wpdb->prefix."onsite_student_living_extracts'"));
				$user_count=$user_count+$sel_contacts;
			}
			else{

				$sel_contacts= $wpdb->get_var( $wpdb->prepare( "SELECT count(*) as count FROM  `".$wpdb->prefix."onsite_student_living_extracts` WHERE AcademicYear='".$term_value."'"));
				$user_count=$user_count+$sel_contacts;
			}
		//add script for other conditions like age

		}
	}
}
if($sel_list_rel[0]->parents != ""){

	$terms=explode(";",$sel_list_rel[0]->parents);
	foreach($terms as $term){

		$term_stmt=explode(":",$term);
		$term_key=$term_stmt[0];$term_value=$term_stmt[1];
		if($term_key=="school_term"){//if condition is school term

			if($term_value=="All"){

				$sel_residency= $wpdb->get_results("SELECT `ResmID`, `ResidentID` FROM `".$wpdb->prefix."onsite_student_living_extracts`");

				foreach($sel_residency as $home){

					 ////script for find parent and his/her address
					/* HouseHold Extract à HouseHoldID  = StudentInformation 2  ResidentID
					HouseHold Extract ResidentList Node.ResMID=StudentInformation2.Resmid
					HouseHold Extract ResidentMemberList Node. Guarantor =1   Parent */
					$sel_parents= $wpdb->get_var($wpdb->prepare("SELECT count(*) as count FROM  '".$wpdb->prefix."onsite_household_extracts' WHERE Resmid='".$home->ResmID."'"));
					$user_count=$user_count+$sel_parents;
				}
			}
			else{

				$sel_residency= $wpdb->get_results("SELECT `ResmID`, `ResidentID` FROM `".$wpdb->prefix."onsite_student_living_extracts` WHERE AcademicYear='".$term_value."'");
				foreach($sel_residency as $home){

					//script for find parent and his/her address
					$sel_parents= $wpdb->get_var( $wpdb->prepare( "SELECT count(*) as count FROM  `".$wpdb->prefix."onsite_household_extracts` WHERE Resmid='".$home->ResmID."'"));
					$user_count=$user_count+$sel_parents;

				}
			}
		//add script for other conditions like age,semester etc

		}
	}
}
if($sel_list_rel[0]->prospects != ""){
	$sel_contacts= $wpdb->get_var( $wpdb->prepare( "SELECT count(*) as count FROM  `".$wpdb->prefix."onsite_prospect_survey`"));
	$user_count=$user_count+$sel_contacts;	
}
?>