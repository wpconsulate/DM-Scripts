<?php
//is_super_admin();



	if(1){ //if(is_admin()){

		global $wpdb; // this is how you get access to the database

		$rowPerpage = 5; // Rows per page default value.
		$limit = " Limit 0, $rowPerpage";
		$start = 0; $showNum = 0;

//****************************************

if($s_props){
        if($s_props=="1"){
                $prefix="wp_";
        }else{
                $prefix="wp_".$s_props."_";
        }
}else{
        $prefix=$wpdb->prefix;
}


		//For drop down
		if(!empty($s_rowlen)){ 
			$limit = "  LIMIT 0, $s_rowlen";
			$rowPerpage = $s_rowlen; //Rest rows per page as per the dropdown value
		}

		if(!empty($s_page)){ // Processing pagination link click

			list($start, $showNum) = explode('.',$s_page);
			//if($start>0){ $pstart = $start-1; }else{ $pstart = $start; }
			$pstart = $start;
			$pmaximum =($showNum*$s_cid); //
			$limit = " LIMIT ".$start." , ".$showNum." ";
		} else {
			$pstart = 0;$pmaximum = $rowPerpage;
		}

		if(empty($s_field)){ $s_field = 'campaign_name';}
		if(empty($s_order)){ $s_order = 'ASC';}
		
	
		if(!empty($s_order)){
			if($s_order == 'ASC'){ $newOrder = 'DESC';} else { $newOrder = 'ASC'; }
	
		}

		
		$pluginImagesDir = plugins_url().'/campaign-manager/images/';
		$total = 0;
		$old_blog = $wpdb->blogid;
	if(is_super_admin()){

		$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM ".$wpdb->base_prefix."blogs"));
		foreach ($blogids as $blog_id) {
			if($blog_id==1){ $prefix="wp_";} else {	$prefix="wp_{$blog_id}_"; }

			//for($siteCnt=0;$siteCnt<count($applicableSites);$siteCnt++){
			$where = " WHERE ".$prefix ."campaigns.propertyId = ".$wpdb->base_prefix ."properties.id 
			AND ".$prefix ."campaigns.active = '1' ";
	
			if(!empty($s_dtPicker)){
				$where .= " AND ".$prefix."campaigns.sent_date = '".$s_dtPicker."' ";
			}
		
			if(!empty($s_props)){
		
				$where .= " AND ".$prefix."campaigns.propertyId = '".$s_props."' ";
			}
			
			if(!empty($s_searchq)){
				$where .= " AND ".$prefix."campaigns.campaign_name LIKE '".$s_searchq."%' ";
			}


			$sql1 = "SELECT ". $wpdb->base_prefix ."properties. * , ". $prefix ."campaigns. * FROM ". $wpdb->base_prefix ."properties, ". $prefix ."campaigns ".$where." ORDER BY ". $prefix."campaigns.". $s_field." ". $s_order ." "; //. $limit.''
			//echo $sql1;
			$results = $wpdb->get_results($sql1);

			foreach($results as $result):
				// Name Property Date Sent Opens Clicks Bounces Action
				$campaignId[] = $result->id;
				$campainName[] = $result->campaign_name;
				$propertyName[] = $result->property_name;
				$propertyId[] = $result->propertyId;
				$sendDate[] = $result->sent_date;
				$categoryId[]=$result->categoryId;
			endforeach;
			
			$total += $wpdb->num_rows;
		}
	} else { //Not networkadmin

			global $blog_id;
			if($blog_id==1){ $prefix="wp_";} else {	$prefix="wp_{$blog_id}_"; }

			//for($siteCnt=0;$siteCnt<count($applicableSites);$siteCnt++){
			$where = " WHERE ".$prefix ."campaigns.propertyId = ".$wpdb->base_prefix ."properties.id 
			AND ".$prefix ."campaigns.active = '1' ";
	
			if(!empty($s_dtPicker)){
				$where .= " AND ".$prefix."campaigns.sent_date = '".$s_dtPicker."' ";
			}
		
			//if(!empty($s_props)){
		
				$where .= " AND ".$prefix."campaigns.propertyId = '".$blog_id."' ";
			//}
			
			if(!empty($s_searchq)){
				$where .= " AND ".$prefix."campaigns.campaign_name LIKE '".$s_searchq."%' ";
			}


			$sql1 = "SELECT ". $wpdb->base_prefix ."properties. * , ". $prefix ."campaigns. * FROM ". $wpdb->base_prefix ."properties, ". $prefix ."campaigns ".$where." ORDER BY ". $prefix."campaigns.". $s_field." ". $s_order ." "; //. $limit.''
			//echo $sql1;
			$results = $wpdb->get_results($sql1);

			foreach($results as $result):
				// Name Property Date Sent Opens Clicks Bounces Action
				$campaignId[] = $result->id;
				$campainName[] = $result->campaign_name;
				$propertyName[] = $result->property_name;
				$propertyId[] = $result->propertyId;
				$sendDate[] = $result->sent_date;
				$categoryId[]=$result->categoryId;
			endforeach;
			
			$total += $wpdb->num_rows;
	} //End of not network admin
	
//print_r($propertyName);
		$num_of_pages = ceil($total/$rowPerpage);

		
		$htmlThead = '<table id="example" class="display dataTable" cellspacing="0" cellpadding="0" border="0" aria-describedby="example_info" style="width:980px">
			<thead>
			<tr role="row">
			<th id="colHead" aria-label="Rendering engine: activate to sort column descending" aria-sort="ascending" style="width: 25px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper" style="padding-left:5px" ><input name="sdf" type="checkbox" id="selectall"/></div></th>
			<th id="colHead" aria-label="Rendering engine: activate to sort column descending" aria-sort="ascending" style="width: 420px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper" onClick="delete_msg_board(\'campaign_name\',\''.$newOrder.'\',\'sort\');">Name</div></th>
			<th id="colHead" aria-label="Browser: activate to sort column ascending" style="width: 340px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper" onClick=\'delete_msg_board("propertyId","'.$newOrder.'","sort");\'>Property
				</div></th>
				
				<th id="colHead" aria-label="Browser: activate to sort column ascending" style="width: 340px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper" onClick=\'delete_msg_board("categoryId","'.$newOrder.'","sort");\'>Category
				</div></th>
				
				
			<th id="colHead" aria-label="Platform(s): activate to sort column ascending" style="width: 138px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper" onClick=\'delete_msg_board("sent_date","'.$newOrder.'","sort");\'>Date</div></th>
			<th id="colHead" aria-label="Engine version: activate to sort column ascending" style="width: 90px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper">Sent</div></th>
			<th id="colHead" aria-label="Engine version: activate to sort column ascending" style="width: 90px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper">Opens</div></th>
			<th id="colHead" aria-label="Engine version: activate to sort column ascending" style="width: 90px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper">Clicks</div></th>
			<th id="colHead" aria-label="Engine version: activate to sort column ascending" style="width: 90px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper">Bounces</div></th>
			<th id="colHead" aria-label="Engine version: activate to sort column ascending" style="width: 90px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper">Blacklisted</div></th>
			<th id="colHead" aria-label="CSS grade: activate to sort column ascending" style="min-width: 70px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper">Action</div></th></tr></thead>
		<tbody aria-relevant="all" aria-live="polite" role="alert">';



	$class = 'odd';
	if(count($campaignId) > 0){
		$a = 1; //echo $pstart.' -'. $pmaximum;
		for($i=$pstart;$i<$pmaximum;$i++){
			if(!empty($campaignId[$i])){
				$cnt = $a%2; //$class = ($cnt) ? 'odd' : 'even'; $a++; // Alternate row color
				if($cnt) {
					$class = 'odd';
					$rcolor = '#F0F0F0';
				} else { 
					$class = 'even';
					$rcolor = '#FFFFFF';
				}
				$a++;


				$opensql = "SELECT COUNT(event) FROM ".$wpdb->base_prefix."sendgrid_status where event = 'open' AND `campaign` = ". $campaignId[$i];
				$totalOpened = $wpdb->get_var( $wpdb->prepare( $opensql ) );

				$proSql = "SELECT COUNT(event) FROM ".$wpdb->base_prefix."sendgrid_status where event = 'processed' AND `campaign` = ". $campaignId[$i];
				$totalProcessed = $wpdb->get_var( $wpdb->prepare( $proSql ) );

				$bounceSql = "SELECT COUNT(event) FROM ".$wpdb->base_prefix."sendgrid_status where event = 'bounce' AND `campaign` = ". $campaignId[$i];
				$totalBounced = $wpdb->get_var( $wpdb->prepare( $bounceSql ) );
				
				$blaclistSql = "SELECT COUNT(event) FROM ".$wpdb->base_prefix."sendgrid_status where event = 'dropped' AND `campaign` = ". $campaignId[$i];
				$totalBlacklisted= $wpdb->get_var( $wpdb->prepare( $blaclistSql ) );
				
				$clickSql = "SELECT COUNT(event) FROM ".$wpdb->base_prefix."sendgrid_status where event = 'click' AND `campaign` = ". $campaignId[$i];
				$totalClicked = $wpdb->get_var( $wpdb->prepare( $clickSql ) );

				$cnid= "cname_".$campaignId[$i];
				$pnid="pname_".$campaignId[$i];
                                $propid=$propertyId[$i];
///gradeA
if($s_props){
        if($s_props=="1"){
                $prefix1="wp_";
        }else{
                $prefix1="wp_".$s_props."_";
        }
}else{

        if($propertyId[$i]=="1"){
                $prefix1="wp_";
        }else{
                $prefix1="wp_".$propertyId[$i]."_";
        }
}

	 $resCat=mysql_query("SELECT category_name FROM {$prefix1}categories WHERE id='".$categoryId[$i]."'"); 
	// echo "SELECT category_name FROM {$prefix1}categories WHERE id='".$categoryId[$i]."'";
	// echo "SELECT category_name FROM {$prefix1}categories WHERE id='".$categoryId[$i]."'";
	 $rowcat=mysql_fetch_array($resCat);
	 
				$htmlRows .= '<tr onmouseover="showIt('.$campaignId[$i].',\''.$rcolor.'\')" onmouseout="hideIt('.$campaignId[$i].',\''.$rcolor.'\')" id="row_'.$campaignId[$i].'" class=" '.$class.'">
					<td><input type="checkbox" class="case" name="case" value="'. $campaignId[$i].'-'.$propertyId[$i].'"></td>
					<td id="cname_'.$campaignId[$i].'" onclick=hili("'. $campaignId[$i].'","'.$cnid.'"); > '. stripslashes($campainName[$i]).'<input type="hidden" name="campid[]" value="'.$campaignId[$i].'" ></td>
					<td id="pname_'.$campaignId[$i].'" onclick=hili("'. $campaignId[$i].'","'.$pnid.'","'.$propid.'"); class=" ">'.$propertyName[$i].'</td>
					<td style="width:290px;">'.$rowcat['category_name'].'</td>
					<td id="sdate" class=" ">'.$sendDate[$i].'</td>
					<td class="center ">'.$totalProcessed.'</td>
					<td class="center ">'.$totalOpened.'</td>
					<td class="center ">'.$totalClicked.'</td>
					<td class="center ">'.$totalBounced.'</td>
					<td class="center ">'.$totalBlacklisted.'</td>
					<td class="center ">
						<img id="editImg-'.$campaignId[$i].'" onClick="edit_msg_board('.$campaignId[$i].','.$propertyId[$i].');" src="'. $pluginImagesDir.'edit.png" /> <img src="'. $pluginImagesDir.'seperator-18.png">
						<img id="dropImg-'.$campaignId[$i].'" onClick="delete_campaign('.$campaignId[$i].','.$propertyId[$i].');" id="dropButton" src="'. $pluginImagesDir.'delete.png" /></td>
					</tr>';
			} //if
		} //4loop
	} else {
		// No Data Fount
		$htmlRows = '<tr class="gradeA '.$class.'">
					<td class="center" colspan="9" height="150"> No Data Found </td></tr>';
	}
		//<thead>
		$pagination .= '
			<tr role="row">
				<td colspan="2" align="left" >
					<img id="dropImg" src="'. $pluginImagesDir.'arrow_ltr.png" width="38" height="22" style=" margin-left: 0.3em; margin-right: 0.3em;" />
					<img id="dropImg" onClick="delete_campaign_group(\'delSelected\');" id="dropButton" src="'. $pluginImagesDir.'delete.png" /> 
				</td>
				<td  colspan="9" align="right">

				<span style="text-align:right">';
					
						$pageButtons = '';
						$prevButton = ''; //$s_cid

//$num_of_pages = 5;  /// <<<< ******************************** REMOVE THIS LINE ***********************************
						for($i=1; $i<=$num_of_pages; $i++)
						{
							$nextPage=0;
							if($page > 0){
								$prevPage = $page - 1;
							}
							if($page < $num_of_pages){
								$nextPage = $page + 1;
							}
							if(empty($s_cid)){ $nextPgHilite = 2;$prevPgHilite = 0;} else {
								$nextPgHilite = $s_cid+1; $prevPgHilite = $s_cid-1; }
							if($start > 0) { //if($num_of_pages > $rowPerpage){
								$prevPg = $start-$rowPerpage;
								$prevButton = '<a class="fg-button ui-button ui-state-default" style="width:33px; padding: 5px 5px; font-weight:bold;" onclick="delete_msg_board(0,0,0,\''.$prevPg.'.'.$rowPerpage.'\','.$prevPgHilite.');">Prev</a>';
							}
							if($i == 1){ $nextI = 0;} else { $nextI = ($i-1)*$rowPerpage; }
							$nextPstart = $nextI;//+1;
							//if($total-1 != $nextPstart){
								$pageButtons .= '<a class="fg-button ui-button ui-state-default" onclick="delete_msg_board(0,0,0,\''.$nextPstart.'.'.$rowPerpage.'\','.$i.');" id="'.$i.'" ';
								if($s_cid == $i){ $pageButtons .= 'style="color:#000000 !important;font-weight:bold; padding: 5px 5px;"'; } else { $pageButtons .= ' style="padding: 5px 5px; "'; }
								$pageButtons .= '>';
								$pageButtons .= $i.'</a>';
							//}
						}
						$pagination .= $prevButton;
						$pagination .= $pageButtons;
						$startEnd = $start+$showNum;
						if(($s_rowlen < $total) && ($startEnd < $total) ) { //if(($start+$showNum) < $total) {
							$nextPg = $start+$rowPerpage;

							$pagination .= '<a class="fg-button ui-button ui-state-default" style="width:33px; padding: 5px 5px; font-weight:bold;" onclick="delete_msg_board(0,0,0,\''.$nextPg.'.'.$rowPerpage.'\','.$nextPgHilite.');">Next</a>';
						}

					$pagination .= '</span>
				</td>
			</tr>'; //</thead>

		echo $htmlThead;
		echo $htmlRows;
		echo $pagination; // ******************************************** 
		echo $htmlTbodyEnd = '</tbody></table>';
}

?>
