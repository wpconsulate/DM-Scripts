<?php
$listid=$_GET['listid'];
$sel_import_type = $wpdb->get_results("SELECT import_type FROM `".$wpdb->base_prefix."lists` WHERE id='".$listid."'");


if($sel_import_type[0]->import_type=="csv"){
	echo "The import type of this list is CSV and this page is for testing import type ONESITE so change the value of listid in the url";
}

else if($sel_import_type[0]->import_type=="onesite")
{
	echo "IMPORT TYPE : ".$sel_import_type[0]->import_type."</br>";
	
	$sel_list_rel= $wpdb->get_results("SELECT school_term, term_type FROM `".$wpdb->base_prefix."list_rel_onesite` WHERE list_id='".$listid."'");
	
	$termtypes=explode(";",$sel_list_rel[0]->term_type);
	
	foreach($termtypes as $termtype){
	
		echo $termtype;
		
		if($termtype=="Residents"){
		
			$sel_contacts= $wpdb->get_results("SELECT Firstname, LastName, Email FROM `".$wpdb->base_prefix."onsite_student_living_extracts` WHERE AcademicYear='".$sel_list_rel[0]->school_term."'");
			
			foreach($sel_contacts as $contact){
				echo $contact->Firstname." ".$contact->LastName." ".$contact->Email;
			}
			
		}
		
		else if($termtype=="Parents"){}
		
		else if($termtype=="Prospects"){
		
			$sel_contacts= $wpdb->get_results("SELECT ProspectFirstName, ProspectLastName, ProspectEmailAddress FROM `".$wpdb->base_prefix."onsite_prospect_survey`");
			foreach($sel_contacts as $contact){
				echo $contact->ProspectFirstName." ".$contact->ProspectLastName." ".$contact->ProspectEmailAddress;
			}
			
		}
	}
}
?>