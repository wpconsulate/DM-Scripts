<?php


	if(is_admin()){

		global $wpdb; // this is how you get access to the database
		//echo 'Npg: '.$s_page.' Rlen: '.$s_rowlen.'<br>';

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

		if(empty($s_field)){ $s_field = 'campaign_name';}
		if(empty($s_order)){ $s_order = 'ASC';}
		
		$where = " WHERE ".$wpdb->prefix ."campaigns.propertyId = ".$wpdb->prefix ."properties.id 
			AND ".$wpdb->prefix ."campaigns.active = '1' AND ".$wpdb->prefix ."campaigns.id IN (".implode(',',$s_campidValues).")";
		if(!empty($s_dtPicker)){
			$where .= " AND ".$wpdb->prefix."campaigns.sent_date = '".$s_dtPicker."' ";
		}
	
		if(!empty($s_props)){
			$where .= " AND ".$wpdb->prefix."campaigns.propertyId = '".$s_props."' ";
		}
		
		if(!empty($s_searchq)){
			$where .= " AND ".$wpdb->prefix."campaigns.campaign_name LIKE '".$s_searchq."%' ";
		}

		if(!empty($s_order)){
			if($s_order == 'ASC'){ $newOrder = 'DESC';} else { $newOrder = 'ASC'; }
	
		}

		$sql = "SELECT ". $wpdb->prefix ."properties. * , ". $wpdb->prefix ."campaigns. * FROM ". $wpdb->prefix ."properties, ". $wpdb->prefix ."campaigns ".$where." ORDER BY ". $wpdb->prefix ."campaigns.". $s_field." ". $s_order ." ". $limit.'';
		//exit;
		$pluginImagesDir = plugins_url().'/campaign-manager/images/';	
		
		$htmlThead = '<table id="example" class="display dataTable" cellspacing="0" cellpadding="0" border="0" aria-describedby="example_info" style="width:980px">
			<thead>
			<tr role="row">
			<th aria-label="Rendering engine: activate to sort column descending" aria-sort="ascending" style="width: 172px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper" onClick="delete_msg_board(\'campaign_name\',\''.$newOrder.'\',\'sort\');">Name</div></th>
			<th aria-label="Browser: activate to sort column ascending" style="width: 248px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper" onClick=\'delete_msg_board("propertyId","'.$newOrder.'","sort");\'>Property
				</div></th>
			<th aria-label="Platform(s): activate to sort column ascending" style="width: 238px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper" onClick=\'delete_msg_board("sent_date","'.$newOrder.'","sort");\'>Date</div></th>
			<th aria-label="Engine version: activate to sort column ascending" style="width: 150px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper">Sent</div></th>
			<th aria-label="Engine version: activate to sort column ascending" style="width: 150px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper">Opens</div></th>
			<th aria-label="Engine version: activate to sort column ascending" style="width: 150px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper">Clicks</div></th>
			<th aria-label="Engine version: activate to sort column ascending" style="width: 150px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper">Bounces</div></th>
			</tr></thead>
		<tbody aria-relevant="all" aria-live="polite" role="alert">';


		$objects = $wpdb->get_results($sql); //$wpdb->prepare(
		$class = 'odd';
		if(count($objects) > 0){		
			$a = 1;
			for($i=0;$i<count($objects);$i++){
				$cnt = $a%2; $class = ($cnt) ? 'odd' : 'even'; $a++; // Alternate row color
					  /*$objects[$i]->propertyId*/ 

				/* ----------------------- */
				$opensql = "SELECT COUNT(event) FROM ".$wpdb->prefix."sendgrid_status where event = 'open' AND `campaign` = ". $objects[$i]->id;
				$totalOpened = $wpdb->get_var( $wpdb->prepare( $opensql ) );

				$proSql = "SELECT COUNT(event) FROM ".$wpdb->prefix."sendgrid_status where event = 'processed' AND `campaign` = ". $objects[$i]->id;
				$totalProcessed = $wpdb->get_var( $wpdb->prepare( $proSql ) );

				$bounceSql = "SELECT COUNT(event) FROM ".$wpdb->prefix."sendgrid_status where event = 'bounce' AND `campaign` = ". $objects[$i]->id;
				$totalBounced = $wpdb->get_var( $wpdb->prepare( $bounceSql ) );

				$clickSql = "SELECT COUNT(event) FROM ".$wpdb->prefix."sendgrid_status where event = 'click' AND `campaign` = ". $objects[$i]->id;
				$totalClicked = $wpdb->get_var( $wpdb->prepare( $clickSql ) );
				/* ----------------------- */
				$htmlRows .= '<tr id="row_'.$objects[$i]->id.'" class="gradeA '.$class.'">
					<td class="  sorting_1">'. $objects[$i]->campaign_name.'</td>
					<td class=" ">'.$objects[$i]->property_name .'</td>
					<td class=" ">'.$objects[$i]->sent_date.'</td>
					<td class="center ">'.$totalProcessed.'</td>
					<td class="center ">'.$totalOpened.'</td>
					<td class="center ">'.$totalClicked.'</td>
					<td class="center ">'.$totalBounced.'</td>
					</tr>';
			}
		} else {
			// No Data Fount
			$htmlRows = '<tr class="gradeA '.$class.'">
					<td class="center" colspan="8" height="150"> No Data Found </td></tr>';
		}

		echo $htmlThead;
		echo $htmlRows;
		echo $htmlTbodyEnd = '</tbody></table>';
}

?>