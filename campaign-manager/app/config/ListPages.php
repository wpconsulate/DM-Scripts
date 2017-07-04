<?php
	if(1){         //if(is_admin()){

		global $wpdb; // this is how you get access to the database
		//echo 'Npg: '.$s_page.' Rlen: '.$s_rowlen.'<br>';

		$rowPerpage = 5; // Rows per page default value.
		$limit = " Limit 0, $rowPerpage";
		$start = 0; $showNum = 0;
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
			$limit = " LIMIT ".$start." , ".$showNum." ";
			$pstart = $start;
			$pmaximum =($showNum*$s_curId);
		}else{
		$pstart = 0;
		$pmaximum = $rowPerpage;
		}

		if(empty($s_field)){ $s_field = 'list_name';}
		if(empty($s_order)){ $s_order = 'ASC';}
		
		//$where = " WHERE ".$prefix."lists.active = '1' ";
		if(!empty($s_dtPicker)){
			$where = "  WHERE ".$prefix."lists.date = '".$s_dtPicker."' ";
		}
		
//$s_props		
		if(!empty($s_props)){
			//$where .= " AND ".$prefix."lists.propertyId = '".$s_props."' ";
		}
		
		
		if(!empty($s_searchq)){
		if($where){
			$where .= " AND ".$prefix."lists.list_name LIKE '".$s_searchq."%' ";
		}else{
		$where .= " WHERE ".$prefix."lists.list_name LIKE '".$s_searchq."%' ";
		}
		}

		if(!empty($s_rowlen)){
			//$limit .= " LIMIT 0 , ".$s_rowlen." ";
		}

	
		if(!empty($s_order)){
			if($s_order == 'ASC'){ $newOrder = 'DESC';} else { $newOrder = 'ASC'; }
	
		}

		//echo $sql = "SELECT * FROM ". $prefix."campaigns ".$where." ORDER BY ". $s_field." ". $s_order ." ". $limit.'';

		//****************************************
		// Get SITEIDs on which logged user has admin permission
		global $blog_id;
			
		$user_ID= get_current_user_id();

		$userSql = "SELECT umeta_id, user_id, meta_key
			FROM ".$wpdb->base_prefix."usermeta
			WHERE meta_value like '%administrator%' 
			OR meta_value like '%contributor%' 
			OR meta_value like '%editor%' 
			OR meta_value like '%author%'
			OR meta_value like '%subscriber%'
			and user_id={$user_ID}";
		$whichSites = $wpdb->get_results($userSql);
		//$whichSites = $whichSites[0];

		foreach ( $whichSites as $whichSite ){
			list($pre,$sid,$cap) = explode('_',$whichSite->meta_key); // wp_3_capabilities 
			if($sid == 'capabilities') { $sid = 1; }
			$applicableSites[] = $sid;
			//echo "$pre : $sid : $cap <br>";	
		}
		$applicableSites = implode(",", $applicableSites);
		//echo $applicableSites;

//*************************************	
##########LIST FROM ALL BLOGS####
if(is_super_admin()){
  if(!empty($s_props)){
	$blogids[]=$s_props;		
   }else{
      $blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM ".$wpdb->base_prefix."blogs"));
   }

  foreach ($blogids as $blogid) {
        if($blogid==1){ $prefix1="wp_";} else {	$prefix1="wp_{$blogid}_"; }
        $sqlx = "SELECT ". $prefix1."lists. * FROM ". $prefix1 ."lists ".$where." ORDER BY ". $prefix1."lists.". $s_field." ". $s_order .'';
        //echo  $sqlx;
        $results = $wpdb->get_results($sqlx);
        if($wpdb->num_rows){
        			foreach($results as $result):
				// Name Property Date Sent Opens Clicks Bounces Action
				$listId[] = $result->id;
				$listName[] = $result->list_name;
				$numContacts[] = $result->num_contacts;
				$propertyId[] = $result->propertyId;
				$date[] = $result->date;
				$categoryId[]=$result->categoryId;
				$importType[]=$result->import_type;
			endforeach;
			}
  }
}else{

        global $blog_id;
        if($blog_id==1){ $prefix1="wp_";} else {	$prefix1="wp_{$blog_id}_"; }
        $sqlx = "SELECT ". $prefix1."lists. * FROM ". $prefix1 ."lists ".$where." ORDER BY ". $prefix1."lists.". $s_field." ". $s_order .'';
        $results = $wpdb->get_results($sqlx);
        			foreach($results as $result):
				// Name Property Date Sent Opens Clicks Bounces Action
				$listId[] = $result->id;
				$listName[] = $result->list_name;
				$numContacts[] = $result->num_contacts;
				$propertyId[] = $result->propertyId;
				$date[] = $result->date;
				$categoryId[]=$result->categoryId;
				$importType[]=$result->import_type;
			endforeach;


}
//print_r($listName);
//echo $pstart;
//echo "max ".$pmaximum;
$total=count($listId);
$num_of_pages = ceil($total/$rowPerpage);
################################
/*
		$total_sql = "SELECT ". $prefix."lists. * FROM ". $prefix ."lists ".$where." ORDER BY ". $prefix."lists.". $s_field." ". $s_order .'';

		$sql = "SELECT ". $prefix."lists. * FROM ". $prefix."lists ".$where." ORDER BY ". $prefix."lists.". $s_field." ". $s_order ." ". $limit.'';
	//echo $sql;
		$sql2 = "SELECT *
		FROM ". $prefix."lists
		WHERE `active` =1
		ORDER BY ". $prefix."lists.list_name ASC";
		$wpdb->get_results($total_sql);
		$total = $wpdb->num_rows;
*/		
		//$num_of_pages = ceil($total/$rowPerpage);
//echo $total_sql;
//echo "total ".$total;
//echo " Num of pages ".$num_of_pages;

		$pluginImagesDir = plugins_url().'/campaign-manager/images/';	
		
		$htmlThead = '<table border="0" cellspacing="0" cellpadding="0" style="width:100%; text-align: left;" id="example" class="display dataTable" aria-describedby="example_info"  >
	<thead>
		<tr role="row">
		<th id="colHead" aria-label="Rendering engine: activate to sort column descending" aria-sort="ascending" style="width: 25px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper" ><input name="sdf" type="checkbox" id="selectall"/></div></th>
        <th id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1" style="width: 504px;" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending">
		<div class="DataTables_sort_wrapper"  onClick="ListPages(\'list_name\',\''.$newOrder.'\',\'sort\');">Name</div></th>';
	if(is_super_admin()){	
      $htmlThead .=  '<th id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1" style="width: 504px;" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending">
		<div class="DataTables_sort_wrapper"  onClick="ListPages(\'propertyId\',\''.$newOrder.'\',\'sort\');">Property</div></th>';
		}		
		
     $htmlThead .=   '<th id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1" style="width: 269px;" aria-label="Platform(s): activate to sort column ascending">
		<div class="DataTables_sort_wrapper" onClick="ListPages(\'date\',\''.$newOrder.'\',\'sort\');">Date</div></th>
		        <th id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1" style="width: 504px;" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending">
		<div class="DataTables_sort_wrapper"  onClick="ListPages(\'categoryId\',\''.$newOrder.'\',\'sort\');">Category</div></th>
		
        <th id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1" style="width: 269px;" aria-label="Engine version: activate to sort column ascending">
		<div class="DataTables_sort_wrapper">Total Contacts</div></th>
        <th id="colHead" class="ui-state-default" role="columnheader" tabindex="0" aria-controls="example" rowspan="1" colspan="1" style="min-width: 105px;" aria-label="CSS grade: activate to sort column ascending">
		<div class="DataTables_sort_wrapper">Action</div></th></tr>
	</thead>
		
<tbody role="alert" aria-live="polite" aria-relevant="all">';

		//$objects = $wpdb->get_results($sql); //$wpdb->prepare(
		$class = 'odd';
		if(count($listId) > 0){	
			
			$a = 1;
			for($i=$pstart;$i<$pmaximum;$i++){
			
			if(!empty($listId[$i])){
			if($propertyId[$i]==1){ $prefix2="wp_";} else {	$prefix2="wp_{$propertyId[$i]}_"; }
				if($importType[$i]=="csv"){
				    $user_count = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) as count FROM ".$prefix2."contacts WHERE list_id  ='".$listId[$i]."'" ) );}
				else if($importType[$i]=="onesite"){
// 				    $listid=$objects[$i]->list_id;
//                                     include("count_list.php");

                                }
				$cnt = $a%2; $class = ($cnt) ? 'odd' : 'even'; $a++; // Alternate row color
					  /*$objects[$i]->propertyId*/
					  $cnid= "cname_".$listId[$i];
					  $pnid="pname_".$listId[$i];
	 $resCat=mysql_query("SELECT category_name FROM {$prefix2}categories WHERE id='".$categoryId[$i]."'"); 
	 //echo "SELECT category_name FROM {$prefix2}categories WHERE id='".$categoryId[$i]."'";
	 $rowcat=mysql_fetch_array($resCat);
	
				$htmlRows .= '<tr onmouseover="showIt('.$listId[$i].',\''.$rcolor.'\')" onmouseout="hideIt('.$listId[$i].',\''.$rcolor.'\')" id="row_'.$listId[$i].'" class="gradeA '.$class.'">
				<td><input type="checkbox" class="case" name="case" value="'.$listId[$i].'-'.$propertyId[$i].'"></td>
					<td style="width:290px;">'.$listName[$i].'</td>';
					if(is_super_admin()){
					$resPrp=mysql_query("SELECT `property_name` FROM wp_properties WHERE `id`='$propertyId[$i]'");
					$rowPrp=mysql_fetch_array($resPrp);
					$htmlRows .='<td style="width:290px;">'.$rowPrp['property_name'].'</td>';
					}	
				$htmlRows .='<td style="width:85px;">'. $date[$i].'</td>

			                <td style="width:290px;">'.$rowcat['category_name'].'</td>
					<td style="width:75px;">'.$numContacts[$i].'</td>
					<td>
					<img id="editImg-'.$listId[$i].'" alt="edit" class="editButton" onClick="edit_msg_board('.$listId[$i].','.$propertyId[$i].');" src="'. $pluginImagesDir.'edit.png" /> <img src="'. $pluginImagesDir.'seperator-18.png">
					<img id="dropImg-'.$listId[$i].'"  alt="delete" id="dropButton" onClick="delete_list('.$listId[$i].','.$propertyId[$i].');" src="'.$pluginImagesDir.'delete.png" /> <img src="'. $pluginImagesDir.'seperator-18.png">
					<img id="download-'.$listId[$i].'"  alt="download csv" id="dropButton" onClick="download_list('.$listId[$i].','.$propertyId[$i].');" src="'.$pluginImagesDir.'download.png" height="18" width="18" />
					</td>
					</tr>';
			}
		}
			
		} else { 
			// No Data Fount
			$htmlRows .= '<tr class="gradeA '.$class.'">
					<td class="center" colspan="6" height="50"><b>No lists available </b><br/></td></tr>';
		}

		$pagination .= '<thead>
			<tr role="row">
				<!--<th class="ui-state-default" colspan="3"> Showing 1 to '.$rowPerpage.' of '.$total.' entries  &nbsp;</th>-->
				<td colspan="2" align="left">
					<img id="dropImg" src="'. $pluginImagesDir.'arrow_ltr.png" width="38" height="22" style=" margin-left: 0.3em; margin-right: 0.3em;" />
					<img id="dropImg" onClick="delete_list_group(\'delSelected\');" id="dropButton" src="'. $pluginImagesDir.'delete.png" /> 
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
							if(empty($s_curId)){ $nextPgHilite = 2;$prevPgHilite = 0;} else { 
								$nextPgHilite = $s_curId+1; $prevPgHilite = $s_curId-1; }
							if($start > 0) { //if($num_of_pages > $rowPerpage){
								$prevPg = $start-$rowPerpage;
								$prevButton = '<a class="fg-button ui-button ui-state-default" style="width:33px; padding: 5px 5px; font-weight:bold;" onclick="ListPages(\''.$s_field.'\',\''.$s_order.'\',0,\''.$prevPg.'.'.$rowPerpage.'\',\''.$prevPgHilite.'\');">Prev</a>';
							}
							if($i == 1){ $nextI = 0;} else { $nextI = ($i-1)*$rowPerpage; }
							$pageButtons .= '<a class="fg-button ui-button ui-state-default" onclick="ListPages(\''.$s_field.'\',\''.$s_order.'\',0,\''.$nextI.'.'.$rowPerpage.'\',\''.$i.'\');" id="'.$i.'"';
							if($s_curId == $i){ $pageButtons .= 'style="color:#000000 !important;font-weight:bold; padding: 5px 5px;"'; } else { $pageButtons .= ' style="padding: 5px 5px; "'; }
							$pageButtons .= '>';
							$pageButtons .= $i.'</a>';
						}
						$pagination .= $prevButton;
						$pagination .= $pageButtons;
						$startEnd = $start+$showNum;
						if(($s_rowlen < $total) && ($startEnd < $total) ) { //if(($start+$showNum) < $total) {
							$nextPg = $start+$rowPerpage;
							$pagination .= '<a class="fg-button ui-button ui-state-default" style="width:33px; padding: 5px 5px; font-weight:bold;" onclick="ListPages(\''.$s_field.'\',\''.$s_order.'\',0,\''.$nextPg.'.'.$rowPerpage.'\',\''.$nextPgHilite.'\');">Next</a>';
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
