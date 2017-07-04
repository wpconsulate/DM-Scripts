<style type="text/css">
table.display td{
font-size: 0.8em;
padding: 3px 10px;
width: auto !important
}
</style>
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
</script>

<?php

is_super_admin();



	if(is_admin()){

		global $wpdb; // this is how you get access to the database
		$s_field = $_POST['s_field_key'];
		$s_order = $_POST['s_order_key']; 
		$s_type = $_POST['s_type_key'];
		$s_q = $_POST['s_q'];

		$s_props = $_POST['s_props'];
		$s_searchq = $_POST['s_searchq'];
		$s_rowlen = $_POST['s_rowlen'];
		$s_page = $_POST['s_page'];
		$s_cid = $_POST['s_cid'];

		$rowPerpage = 5; // Rows per page default value.
		$limit = " Limit 0, $rowPerpage";
		$start = 0; $showNum = 0;

		if($s_props == 1){
			$prefix =" wp_";
		} else { 
			$prefix =" wp_".$s_props."_";
		}

		if($s_props == 1){
			$prefix =" wp_";
		} else { 
			$prefix =" wp_".$s_props."_";
		}


		//For drop down
		if(!empty($s_rowlen)){ 
			$limit = "  LIMIT 0, $s_rowlen";
			$rowPerpage = $s_rowlen; //Rest rows per page as per the dropdown value
		}

		if(!empty($s_page)){ // Processing pagination link click

			list($start, $showNum) = explode('.',$s_page);
			$pstart = $start;
			$pmaximum =($showNum*$s_cid); //
			$limit = " LIMIT ".$start." , ".$showNum." ";
		} else {
			$pstart = 0;$pmaximum = $rowPerpage-1;
		}

		if(empty($s_field)){ $s_field = 'category_name';}
		if(empty($s_order)){ $s_order = 'ASC';}
		
	
		if(!empty($s_order)){
			if($s_order == 'ASC'){ $newOrder = 'DESC';} else { $newOrder = 'ASC'; }
	
		}

		
		$pluginImagesDir = plugins_url().'/campaign-manager/images/';
		$total = 0;
		$old_blog = $wpdb->blogid;
	if(is_super_admin()){

		$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
		foreach ($blogids as $blog_id) {
			if($blog_id==1){ $prefix="wp_";} else {	$prefix="wp_{$blog_id}_"; }
			
			$where = " WHERE ".$prefix ."categories.active = '1' ";
	
		
			if(!empty($s_props)){
		
				$where .= " AND ".$prefix."categories.category_type = '".$s_props."' ";
			}
			
			if(!empty($s_searchq)){
				$where .= " AND ".$prefix."categories.category_name LIKE '".$s_searchq."%' ";
			}


			$sql1 = "SELECT  * FROM  ". $prefix ."categories ".$where." ORDER BY ". $prefix."categories.". $s_field." ". $s_order ." "; //. $limit.''
			//echo $sql1;
			$results = $wpdb->get_results($sql1);

			foreach($results as $result):
				// Name Property Date Sent Opens Clicks Bounces Action
				$campaignId[] = $result->id;
				$campainName[] = $result->category_name;
				$propertyId[] = $result->category_type;
				$pid[]=$blog_id;
			endforeach;
			

			
			$total += $wpdb->num_rows;
			//echo "  total=$total<br>";
		}
	} else { //Not networkadmin
	                
	
			global $blog_id;
			if($blog_id==1){ $prefix="wp_";} else {	$prefix="wp_{$blog_id}_"; }

			//for($siteCnt=0;$siteCnt<count($applicableSites);$siteCnt++){
			$where = " WHERE ".$prefix ."categories.active = '1' ";
	
			
			if(!empty($s_searchq)){
				$where .= " AND ".$prefix."categories.category_name LIKE '".$s_searchq."%' ";
			}


			$sql1 = "SELECT * FROM ".$prefix ."categories ".$where." ORDER BY ". $prefix."categories.". $s_field." ". $s_order ." "; 
			//echo $sql1;
			$results = $wpdb->get_results($sql1);

			foreach($results as $result):
				$campaignId[] = $result->id;
				$campainName[] = $result->category_name;
				$propertyId[] = $result->category_type;
				$pid[]="0";
			endforeach;
			
			$total += $wpdb->num_rows;
	}

		$num_of_pages = ceil($total/$rowPerpage);
		$htmlThead = '';
		/*$htmlThead = '<div id="responceDiv" style="display:none"></div>';
		$loaderPath = plugins_url().'/campaign-manager/images/ajax-loader.gif';
		$htmlThead = '<div style="display:none" class="loadingDiv"><img src="'.$loaderPath.'"></div>';*/

		$htmlThead .= '<table id="example" class="display dataTable" cellspacing="0" cellpadding="0" border="0" aria-describedby="example_info" style="width:100%">
			<thead>
			<tr role="row">
				<th class="columhead" aria-label="Rendering engine: activate to sort column descending" aria-sort="ascending" style="padding-left: 11px; width: 15px;" colspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper" ><input name="sdf" type="checkbox" id="selectall"/></div></th>
				<th class="columhead" aria-label="Rendering engine: activate to sort column descending" aria-sort="ascending" style="width: auto;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper" onClick="category_pagination(\'category_name\',\''.$newOrder.'\',\'sort\');">Name</div></th>
				<th class="columhead" aria-label="Browser: activate to sort column ascending" width="300" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper" onClick=\'category_pagination("category_type","'.$newOrder.'","sort");\'>Type
				</div></th>
				<th class="columhead" aria-label="CSS grade: activate to sort column ascending" style="width: 117px;" colspan="1" rowspan="1" aria-controls="example" tabindex="0" role="columnheader" class="ui-state-default">
				<div class="DataTables_sort_wrapper" style="text-align:center">Action</div></th>
			</tr></thead>
			<tbody aria-relevant="all" aria-live="polite" role="alert">';



	$class = 'odd';
	if(count($campaignId) > 0){
		$a = 1; //echo $pstart.' -'. $pmaximum;
		for($i=$pstart;$i<$pmaximum;$i++){
			if(!empty($campaignId[$i])){
				$cnt = $a%2; $class = ($cnt) ? 'odd' : 'even'; $a++; // Alternate row color

				$cnid= "cname_".$campaignId[$i];
				$pnid="pname_".$campaignId[$i];
                                $propid=$propertyId[$i];
                                switch($propid){
			          case 1 : $propertyName="Campaign";break;
			          case 2 : $propertyName="List";break;
			          case 3 : $propertyName="Message";break; 
			          default: $propertyName="Unassigned";       
			}

				$htmlRows .= '<tr id="row_'.$campaignId[$i].'" class="gradeA '.$class.'" onmouseover=hiliterow("'.$campaignId[$i].'"); onmouseout=normalrow("'.$campaignId[$i].'"); >
					<td><input type="checkbox" class="case" name="case" value="'.$campaignId[$i].','.$propertyId[$i].','.$pid[$i].'"></td>
					<td style="width:350px" id="cname_'.$campaignId[$i].'" onclick=hili("'. $campaignId[$i].'","'.$cnid.'"); class="" style=" white-space: nowrap;">'. stripslashes($campainName[$i]).'</td>
					<td style="width:400px" id="pname_'.$campaignId[$i].'" onclick=hili("'. $campaignId[$i].'","'.$pnid.'","'.$propid.'"); class=" ">'.$propertyName.'</td>
					<td class="center ">

						<img onClick="delete_cat('.$campaignId[$i].','.$propertyId[$i].','.$pid[$i].');" id="imgDel_'.$campaignId[$i].'" src="'. $pluginImagesDir.'delete.png" /></td>
					</tr>';
			} //if
		} //4loop
	} else { 
		// No Data Fount
		$htmlRows = '<tr class="gradeA '.$class.'">
					<td class="center" colspan="9" height="150"> No Data Found </td></tr>';
	}

		$pagination .= '<thead>
			<tr role="row">
				<!--<th class="ui-state-default" colspan="3"> Showing 1 to '.$rowPerpage.' of '.$total.' entries  &nbsp;</th>-->
				<td align="left" colspan="2">
					<img id="dropImg" src="'. $pluginImagesDir.'arrow_ltr.png" width="38" height="22" style=" margin-left: 0.3em; margin-right: 0.3em;" />
					<img id="dropImg" onClick="delete_category_group(\'delSelected\');" id="dropButton" src="'. $pluginImagesDir.'delete.png" /> 
				</td>
				<th class="infooter" colspan="7" style="border:0px;">
					<span style="text-align:right">';
					
						$pageButtons = '';
						$prevButton = ''; //$s_cid
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
								$prevButton = '<a class="fg-button ui-button ui-state-default" style="width:33px; padding: 5px 5px;" onclick="category_pagination(0,0,0,\''.$prevPg.'.'.$rowPerpage.'\','.$prevPgHilite.');">Prev</a>';
							}
							if($i == 1){ $nextI = 0;} else { $nextI = ($i-1)*$rowPerpage; }
							$nextPstart = $nextI;//+1;
							//if($total-1 != $nextPstart){
								$pageButtons .= '<a class="fg-button ui-button ui-state-default" onclick="category_pagination(0,0,0,\''.$nextPstart.'.'.$rowPerpage.'\','.$i.');" id="'.$i.'" ';
								if($s_cid == $i){ $pageButtons .= 'style="color:#000000 !important;font-weight:bold; padding: 5px 5px;"'; } else { $pageButtons .= ' style="padding: 5px 5px;  "'; }
								$pageButtons .= '>';
								$pageButtons .= $i.'</a>';
							//}
						}
						$pagination .= $prevButton;
						$pagination .= $pageButtons;
						$startEnd = $start+$showNum;
						if(($s_rowlen < $total) && ($startEnd < $total) ) { //if(($start+$showNum) < $total) {
							$nextPg = $start+$rowPerpage;

							$pagination .= '<a class="fg-button ui-button ui-state-default" style="width:33px; padding: 5px 5px;" onclick="category_pagination(0,0,0,\''.$nextPg.'.'.$rowPerpage.'\','.$nextPgHilite.');">Next</a>';
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
