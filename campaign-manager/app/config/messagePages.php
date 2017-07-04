<script>
function hiliterow(id){
edid="#imgEdit_"+id;
deid="#imgDel_"+id;
apid="#imgAppr_"+id;
imgdir="http://somdev1.us/wp-content/plugins/campaign-manager/images/";

		$(edid).attr('src', imgdir+'edit-hover.png');
		$(deid).attr('src', imgdir+'delete-hover.png');
		$(apid).attr('src', imgdir+'approve-hover.png');
}
function normalrow(id){
eid="#imgEdit_"+id;
did="#imgDel_"+id;
aid="#imgAppr_"+id;
imgdir="http://somdev1.us/wp-content/plugins/campaign-manager/images/";

		$(edid).attr('src', imgdir+'edit.png');
		$(deid).attr('src', imgdir+'delete.png');
		$(apid).attr('src', imgdir+'approve.png');
}


$(document).ready(function() {
// *******************CHEK ALL UNCHECK ALL **************************
	// add multiple select / deselect functionality
	$("#selectall").live('change', function () {
		$('.case').attr('checked', this.checked);
	});
	
	// if all checkbox are selected, check the selectall checkbox
	// and viceversa
	$(".case").live('change', function(){
	
		if($(".case").length == $(".case:checked").length) {
		$("#selectall").attr("checked", "checked");
		} else {
		$("#selectall").removeAttr("checked");
		}
	
	});
});
// *****************************************************************

</script>
<?php

	if(is_admin()){

		global $wpdb; // this is how you get access to the database
		//echo 'Npg: '.$s_page.' Rlen: '.$s_rowlen.'<br>';
		$prefix=$wpdb->prefix;
if(!empty($s_cProperties)){
if($s_cProperties==1) $prefix="wp_";
else $prefix="wp_".$s_cProperties."_";
}
		$rowPerpage = 5; // Rows per page default value.
		$limit = " Limit 0, $rowPerpage";
		$start = 0; $showNum = 0;

		//For drop down
		if(!empty($s_rowlen)){ 
			$limit = "  LIMIT 0, $s_rowlen";
			$rowPerpage = $s_rowlen; //Rest rows per page as per the dropdown value
		}

		if(!empty($s_page)){ // Processing pagination link click

			list($start, $showNum) = explode('.',$s_page);
			$limit = " LIMIT ".$start." , ".$showNum." ";
		}

		if(empty($s_field)){ $s_field = 'message_name';}
		if(empty($s_order)){ $s_order = 'ASC';}

//****************************************
		/// Get SITEIDs on which logged user has admin permission
		global $blog_id;
		$user_ID = get_current_user_id();

		$userSql = "SELECT umeta_id, user_id, meta_key
			FROM ".$wpdb->base_prefix."usermeta
			WHERE meta_value like '%administrator%' 
			OR meta_value like '%contributor%' 
			OR meta_value like '%editor%' 
			OR meta_value like '%author%' 
			and user_id={$user_ID}";
		$whichSites = $wpdb->get_results($userSql);
		//$whichSites = $whichSites[0];

		foreach ( $whichSites as $whichSite ){
			//echo '<br>'.$whichSite->meta_key;
			list($pre,$sid,$cap) = explode('_',$whichSite->meta_key); // wp_3_capabilities 
			if($sid == 'capabilities') { $sid = 1; }
			if(is_numeric($sid)){
			$applicableSites[] = $sid;
			}
			//echo "$pre : $sid : $cap <br>";	
		}
		$applicableSites = implode(",", $applicableSites);
		//echo 'AP: '.$applicableSites;

//*************************************	
	
		$where = " WHERE ".$wpdb->base_prefix ."messages.campaignID = ".$prefix ."campaigns.id 
			AND ".$wpdb->base_prefix ."messages.active = '1' ";
		if(!empty($s_dtPicker)){
			$where .= " AND ".$wpdb->base_prefix."messages.date = '".$s_dtPicker."' ";
		}
	

		if(!empty($s_campName)){
			list($campaignId, $propertyId) = explode('-',$s_campName);
			$where .= " AND ".$wpdb->base_prefix."messages.campaignID = '".$campaignId."' ";
		}

		if(!empty($s_cProperties)){
			//echo $s_cProperties;
			$where .= " AND ".$wpdb->base_prefix."messages.propertyID = '".$s_cProperties."' ";
		}

		
		if(!empty($s_searchq)){
			$where .= " AND ".$wpdb->base_prefix."messages.message_name LIKE '".$s_searchq."%' ";
		}

		// ************************************************************************
		/// If user has authorise permission,  can view all messages from all sites
		/// Otherwise, messages from sites on whick he has admin permission

		$usrCapSql = "SELECT * FROM ".$wpdb->base_prefix."capabilities WHERE `userId` = {$user_ID} AND `capability` = 'approve_message'";
		$userCapStats = $wpdb->get_results($usrCapSql);
		if($wpdb->num_rows == 0){
			$where .= " AND ".$wpdb->base_prefix."messages.propertyID IN ( $applicableSites ) ";
		}
		
		if(!empty($s_rowlen)){
			//$limit .= " LIMIT 0 , ".$s_rowlen." ";
		}

	
		if(!empty($s_order)){
			if($s_order == 'ASC'){ $newOrder = 'DESC';} else { $newOrder = 'ASC'; }
	
		}

		//echo $sql = "SELECT * FROM ". $prefix ."campaigns ".$where." ORDER BY ". $s_field." ". $s_order ." ". $limit.'';

		

		$total_sql = "SELECT ". $wpdb->base_prefix ."messages. * , ". $prefix ."campaigns. * FROM ". $wpdb->base_prefix ."messages, ". $prefix ."campaigns ".$where." ORDER BY ". $wpdb->base_prefix ."messages.". $s_field." ". $s_order .'';


		$sql = "SELECT ". $wpdb->base_prefix ."messages.id AS msgid, ". $wpdb->base_prefix ."messages. * , ". $prefix ."campaigns. * 
			FROM ". $wpdb->base_prefix ."messages, ". $prefix ."campaigns ".$where." 
			ORDER BY ". $wpdb->base_prefix ."messages.". $s_field." ". $s_order ." ". $limit.'';
		//echo $sql; //exit;
		$sql2 = "SELECT *
		FROM ". $wpdb->base_prefix ."messages
		WHERE `active` =1
		ORDER BY ". $wpdb->base_prefix ."messages.`message_name` ASC";
		$wpdb->get_results($total_sql);
		$total = $wpdb->num_rows;
		
		$num_of_pages = ceil($total/$rowPerpage);


		$pluginImagesDir = plugins_url().'/campaign-manager/images/';	
		
		$htmlThead = '<table id="example" class="display dataTable" cellspacing="0" cellpadding="0" border="0" aria-describedby="example_info" style="width:980px">
			<thead>
			<tr role="row">
<th aria-label="Rendering engine: activate to sort column descending" aria-sort="ascending" style="width: 10px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
<input name="sdf" type="checkbox" id="selectall"/>
</th>
			<th aria-label="Rendering engine: activate to sort column descending" aria-sort="ascending" style="width: 400px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper" onClick="paginatMessage(\'message_name\',\''.$newOrder.'\',\'sort\');">Name</div></th>
			<th aria-label="Browser: activate to sort column ascending" style="width: 226px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper" onClick=\'paginatMessage("campaignID","'.$newOrder.'","sort");\'>Campaign
				</div></th>
			<th aria-label="Platform(s): activate to sort column ascending" style="width: 118px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper" onClick=\'paginatMessage("date","'.$newOrder.'","sort");\'>Date</div></th>
			
			<th aria-label="Platform(s): activate to sort column ascending" style="width: 118px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper" onClick=\'paginatMessage("status","'.$newOrder.'","sort");\'>Status</div></th>
			<th aria-label="CSS grade: activate to sort column ascending" style="width: 117px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper">Action</div></th></tr></thead>
		<tbody aria-relevant="all" aria-live="polite" role="alert">';

		//echo $sql;
		$objects = $wpdb->get_results($sql); //$wpdb->prepare(
		$class = 'odd';
		if(count($objects) > 0){		
			$a = 1;
			for($i=0;$i<count($objects);$i++){
				$cnt = $a%2; $class = ($cnt) ? 'odd' : 'even'; $a++; // Alternate row color
					  /*$objects[$i]->propertyId*/ 
				$htmlRows .= '<tr id="row_'.$objects[$i]->msgid.'" class="gradeA '.$class.'" onmouseover=hiliterow("'.$objects[$i]->msgid.'"); onmouseout=normalrow("'.$objects[$i]->msgid.'"); >

					<td>';

					if($objects[$i]->status != 3){
						$htmlRows .= '<input type="checkbox" class="case" name="case" value="'. $objects[$i]->msgid.'">';
					} else { $htmlRows .= '&nbsp;'; }

					$htmlRows .= '</td>
					<td id="cname_'.$objects[$i]->msgid.'" class="">'. stripslashes($objects[$i]->message_name).'</td>
					<td id="pname_'.$objects[$i]->msgid.'" class=" ">'.stripslashes($objects[$i]->campaign_name) .'</td>
					<td id="sdate_'.$objects[$i]->msgid.'" class=" ">'.$objects[$i]->date.'</td>
					<td id="sdate_'.$objects[$i]->msgid.'" class=" ">';

					switch ($objects[$i]->status) {
	
						case 1:
							$status = 'Pending';
							break;
						case 2:
							$status = 'Approved';
							break;
						case 3:
							$status = 'Sent';
							break;
						default:
							$status = '-';
					}

				$htmlRows .= $status.'</td>

					<td class="center ">
					<table id="actionTable" border="0" align="center"><tr><td style="width:12px">';
			$capability_name = 'approve_message';
			$current_user = wp_get_current_user();
			$user = new WP_User($current_user->ID);
			//global $current_user;
			//print_r($current_user);
			///echo "Capa: ".current_user_can($capability_name);
			//echo "<br> stat:".$objects[$i]->status;
			//echo "capa: ".$user->has_cap( $capability_name);
			$res1=$wpdb->get_results("SELECT * FROM `".$wpdb->base_prefix."capabilities` WHERE `userId`='".$current_user->ID."' AND `capability`='".$capability_name."'");
			if($res1){   
			                //if ( current_user_can($capability_name) ) { 
			                /*if ( $user->has_cap( $capability_name ) ) {*/
				if($objects[$i]->status == 1){
					$htmlRows .= '<a href="admin.php?page=mvc_campaigns-approveMessage&mid='. $objects[$i]->msgid.'"><img title="Approve" id="imgAppr_'.$objects[$i]->msgid.'" src="'. $pluginImagesDir.'approve.png" /></a>'; 
				}
			}
					$htmlRows .= '</td><td style="width:12px">';
				if($objects[$i]->status > 0 && $objects[$i]->status != 3){
					$htmlRows .= '<a href="admin.php?page=mvc_campaigns-editmessage&mid='. $objects[$i]->msgid.'"><img id="imgEdit_'.$objects[$i]->msgid.'" src="'. $pluginImagesDir.'edit.gif" /></a> '; 
				} 
					$htmlRows .= '</td><td style="width:12px">';

				if($objects[$i]->status > 0 && $objects[$i]->status != 3){
					$htmlRows .= '<img onClick="delete_message('.$objects[$i]->msgid.');"  src="'. $pluginImagesDir.'delete.png" id="imgDel_'.$objects[$i]->msgid.'" />'; 
				}
				$htmlRows .= '</td></tr></table></td></tr>';
			}
		} else { 
			// No Data Fount
			$htmlRows = '<tr class="gradeA '.$class.'">
					<td class="center" colspan="8" height="150"> No Data Found </td></tr>';
		}

		$pagination .= '<thead>
			<tr role="row">
				<th colspan="3"> 
				<img id="dropImg" src="'. $pluginImagesDir.'arrow_ltr.png" width="38" height="22" style=" margin-left: 0.3em; margin-right: 0.3em;" />
				<img id="dropImg" onClick="delete_message_group(\'delSelected\');" id="dropButton" src="'. $pluginImagesDir.'delete.png" />
				</th>
				<th class="infooter" colspan="8">
					<span style="text-align:right">';
					
						$pageButtons = '';
						$prevButton = '';
						for($i=1; $i<=$num_of_pages; $i++)
						{
							$nextPage=0;
							if($page > 0){
								$prevPage = $page - 1;
							}
							if($page < $num_of_pages){
								$nextPage = $page + 1;
							}
							if(empty($s_curId)){ $nextPgHilite = 2;$prevPgHilite = 0;} else { 
								$nextPgHilite = $s_curId+1; $prevPgHilite = $s_curId-1; }
							if($start > 0) { //if($num_of_pages > $rowPerpage){
								$prevPg = $start-$rowPerpage;
								$prevButton = '<a class="fg-button ui-button ui-state-default" style="width:33px; padding: 0 5px;" onclick="paginatMessage(0,0,0,\''.$prevPg.'.'.$rowPerpage.'\','.$prevPgHilite.');">Prev</a>';
							}
							if($i == 1){ $nextI = 0;} else { $nextI = ($i-1)*$rowPerpage; }
							$pageButtons .= '<a class="fg-button ui-button ui-state-default" onclick="paginatMessage(0,0,0,\''.$nextI.'.'.$rowPerpage.'\','.$i.');" id="'.$i.'" ';
							if($s_curId == $i){ $pageButtons .= 'style="color:#000000 !important;font-weight:bold; padding: 0 5px;"'; } else { $pageButtons .= ' style="padding: 0 5px; "'; }
							$pageButtons .= '>';
							$pageButtons .= $i.'</a>';
						}
						$pagination .= $prevButton;
						$pagination .= $pageButtons;
						$startEnd = $start+$showNum;
						if(($s_rowlen < $total) && ($startEnd < $total) ) { //if(($start+$showNum) < $total) {
							$nextPg = $start+$rowPerpage;
							$pagination .= '<a class="fg-button ui-button ui-state-default" style="width:33px; padding: 0 5px;" onclick="paginatMessage(0,0,0,\''.$nextPg.'.'.$rowPerpage.'\','.$nextPgHilite.');">Next</a>';
						}

					$pagination .= '</span>
				</th>
			</tr>
		</thead>';

		echo $htmlThead;
		echo $htmlRows;
		echo $pagination;
		echo $htmlTbodyEnd = '</tbody></table>';
}

?>
