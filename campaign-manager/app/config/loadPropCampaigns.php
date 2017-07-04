<?php
$prefix=$wpdb->prefix;
if(!empty($s_propid)){
if($s_propid==1) $prefix="wp_";
else $prefix="wp_".$s_propid."_";
}
	$campaigns = $wpdb->get_results( "SELECT * FROM ".$prefix."campaigns WHERE `propertyId` = ". $s_propid );
	//echo '<select>';
	if(count($campaigns)){
		foreach ( $campaigns as $campaign ){
		
			echo '<option value="'.$campaign->id.'">'.$campaign->campaign_name.'</option>';
		
		}
	} else { echo '<option value="0">No campaigns available</option>'; }
	//echo '</select>';

?>
