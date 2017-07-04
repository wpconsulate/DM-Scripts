<?php

	if(is_super_admin()){

		global $wpdb; 

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
			$pstart = $start;
			$pmaximum =($showNum*$s_cid);
		}

		if(empty($s_field)){ $s_field = 'id';}
		if(empty($s_order)){ $s_order = 'DESC';}

		$where = " WHERE ".$wpdb->base_prefix ."templates.active = '1' ";

		if(!empty($s_dtPicker)){
			$where .= " AND DATE(".$wpdb->base_prefix."templates. created_date)  = '".$s_dtPicker."' ";
		}

		if(!empty($s_searchq)){
			$where .= " AND ".$wpdb->base_prefix."templates.template_name LIKE '".$s_searchq."%' ";
		}


		if(!empty($s_order)){
			if($s_order == 'ASC'){ $newOrder = 'DESC';} else { $newOrder = 'ASC'; }
		}

		$total_sql = "SELECT * FROM ". $wpdb->base_prefix ."templates ".$where." ORDER BY ". $wpdb->base_prefix ."templates.". $s_field." ". $s_order .'';

		$sql = "SELECT * FROM ". $wpdb->base_prefix ."templates ".$where." ORDER BY ". $wpdb->base_prefix ."templates.". $s_field." ". $s_order ." ". $limit.'';

		$sql2 = "SELECT *
		FROM ". $wpdb->base_prefix ."templates
		WHERE `active` =1
		ORDER BY ". $wpdb->base_prefix ."templates.`id` ASC";
		$wpdb->get_results($total_sql);
		$total = $wpdb->num_rows;
		
		$num_of_pages = ceil($total/$rowPerpage);


		$pluginImagesDir = plugins_url().'/campaign-manager/images/';	
		
		$htmlThead = '<table border="0" cellspacing="0" cellpadding="0"  id="example" class="display dataTable" aria-describedby="example_info">
	<thead>
		<tr role="row">
		<th id="colHead" aria-label="Rendering engine: activate to sort column descending" aria-sort="ascending" style="width: 25px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper" ><input name="sdf" type="checkbox" id="selectall"/></div></th>
        <th style="text-align: left; width: 580px;" id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1"  aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending">
		<div class="DataTables_sort_wrapper" onClick="paginateTemplate(\'template_name\',\''.$newOrder.'\',\'sort\');">Name</div></th>
        <th style="text-align: center; width: 150px;" id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1"  aria-label="Browser: activate to sort column ascending">
		<div class="DataTables_sort_wrapper" onClick="paginateTemplate(\'created_date\',\''.$newOrder.'\',\'sort\');">Created On</div></th>
        <th style="text-align: center; width: 150px;" id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1"  aria-label="CSS grade: activate to sort column ascending">
		<div class="DataTables_sort_wrapper">Action</div></th></tr>
	</thead>
		<tbody aria-relevant="all" aria-live="polite" role="alert">';


		$objects = $wpdb->get_results($sql); //$wpdb->prepare(
		$class = 'odd';
		if(count($objects) > 0){		
			$a = 1;
			for($i=0;$i<count($objects);$i++){
				$cnt = $a%2; //$class = ($cnt) ? 'odd' : 'even'; $a++; // Alternate row color
				if($cnt) {
					$class = 'odd';
					$rcolor = '#F0F0F0';
				} else { 
					$class = 'even';
					$rcolor = '#FFFFFF';
				}
				$a++;


					  /*$objects[$i]->propertyId*/ 
					  $date=strtotime($objects[$i]->created_date);
				$htmlRows .= '<tr onmouseover="showIt('.$objects[$i]->id.',\''.$rcolor.'\')" onmouseout="hideIt('.$objects[$i]->id.',\''.$rcolor.'\')" id="row_'.$objects[$i]->id.'" class="gradeA '.$class.'">
				<td><input type="checkbox" class="case" name="case" value="'.$objects[$i]->id.'"></td>
					<td style="text-align: left; width: 580px;" id="cname" >'. $objects[$i]->template_name.'</td>
					<td style="text-align: center; width: 150px;">'. date("Y-m-d",$date).' </td>
					<td style="text-align: center; width: 150px;">
					<a href="admin.php?page=mvc_campaigns-templateedit&id='. $objects[$i]->id.'"><img id="editImg-'.$objects[$i]->id.'" src="'.$pluginImagesDir.'edit.png" /> </a><img src="'. $pluginImagesDir.'seperator-18.png">
					<img id="dropImg-'.$objects[$i]->id.'" onClick="delete_template('.$objects[$i]->id.');" id="dropButton" src="'.$pluginImagesDir.'delete.png" />';
				$htmlRows .= '</td></tr>';
			}
		} else {
			// No Data Fount
			$htmlRows = '<tr class="gradeA '.$class.'">
					<td class="center" colspan="8" height="150"> No Data Found </td></tr>';
		}

		$pagination .= '<thead>
			<tr role="row">
				<!--<th class="ui-state-default" colspan="3"> Showing 1 to '.$rowPerpage.' of '.$total.' entries  &nbsp;</th>-->
				<td colspan="2" align="left" >
					<img id="dropImg" src="'. $pluginImagesDir.'arrow_ltr.png" width="38" height="22" style=" margin-left: 0.3em; margin-right: 0.3em;" />
					<img id="dropImg" onClick="delete_template_group(\'delSelected\');" id="dropButton" src="'. $pluginImagesDir.'delete.png" /> 
				</td>
				<th class="infooter" colspan="8" style="border:0px;">
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
							if(empty($s_cid)){ $nextPgHilite = 2;$prevPgHilite = 0;} else {
								$nextPgHilite = $s_cid+1; $prevPgHilite = $s_cid-1; }
							if($start > 0) { 
								$prevPg = $start-$rowPerpage;
								$prevButton = '<a class="fg-button ui-button ui-state-default" style="width:33px; padding: 5px 5px; font-weight:bold;" onclick="paginateTemplate(\''.$s_field.'\',\''.$s_order.'\',0,\''.$prevPg.'.'.$rowPerpage.'\',\''.$prevPgHilite.'\');">Prev</a>';
							}
							if($i == 1){ $nextI = 0;} else { $nextI = ($i-1)*$rowPerpage; }
							$pageButtons .= '<a class="fg-button ui-button ui-state-default" onclick="paginateTemplate(\''.$s_field.'\',\''.$s_order.'\',0,\''.$nextI.'.'.$rowPerpage.'\','.$i.');" id="'.$i.'"' ;
							if($s_cid == $i){ $pageButtons .= 'style="color:#000000 !important;font-weight:bold; padding: 5px 5px;"'; } else { $pageButtons .= ' style="padding: 5px 5px; "'; }
							$pageButtons .= '>';
							$pageButtons .= $i.'</a>';
						}
						$pagination .= $prevButton;
						$pagination .= $pageButtons;
						$startEnd = $start+$showNum;
						if(($s_rowlen < $total) && ($startEnd < $total) ) {
							$nextPg = $start+$rowPerpage;
							$pagination .= '<a class="fg-button ui-button ui-state-default" style="width:33px; padding: 5px 5px; font-weight:bold;" onclick="paginateTemplate(\''.$s_field.'\',\''.$s_order.'\',0,\''.$nextPg.'.'.$rowPerpage.'\',\''.$nextPgHilite.'\');">Next</a>';
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
