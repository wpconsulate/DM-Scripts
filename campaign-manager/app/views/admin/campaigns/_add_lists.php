<?php 
$urlin = plugins_url()."/campaign-manager/app/public/";
//$urlview = plugins_url()."/campaign-manager/app/views/admin/campaigns/";
$urlview = get_site_url()."/external/";
$pluginImagesDir = plugins_url().'/campaign-manager/images/';
?>
<style type="text/css">

.ui-dialog-titlebar-close ui-corner-all ui-state-hover {
    background-image: url("http://somdev1.us/wp-content/plugins/campaign-manager/images/close.png");
    background-repeat: no-repeat;
    display: block;
    margin: 1px;
}
.ui-icon-closeButton:hover{
	background-image: url(<?php echo $pluginImagesDir.'close.png';?>);
    background-repeat: no-repeat;
    display: block;
    margin: 1px;
}
.ui-dialog-titlebar-close  {
    background-image: url(<?php echo $pluginImagesDir.'close.png';?>);
    background-repeat: no-repeat;
    display: block;
    margin: 1px;
}
.ui-icon .ui-icon-closethick span{
    background-image:url(<?php echo $pluginImagesDir.'close.png';?>);
    height: 16px;
    width: 16px;z-index:-1;
}
.ui-dialog .ui-dialog-titlebar-close:hover {
	background: #E9F4F5; border:0;
	background-image:url(<?php echo $pluginImagesDir.'close.png';?>);
	height: 16px;
	width: 16px; z-index:1;
}

#pop { font-size:12px !important; font-weight:normal !important;}
#pop div{ display:table-row;}
#pop label{ display:table-cell;}

form#listForm div:nth-child(even) {background: #F0F0F0;}
form#listForm div:nth-child(odd) {background: #FFF; }

#listForm label{ min-width:150px; padding-left: 10px; }
.ui-widget input, .ui-widget select {
    font-family: Verdana,Arial,sans-serif;
    font-size: 12px;
    height: 21px;
}
#ListsListName{    width: 300px !important;}

.ui-widget input {
	width: 195px !important;
}
.ui-widget textarea {
    font-family: Verdana,Arial,sans-serif;
    font-size: 12px;
    height: 116px;
    width: 300px;
}
#catform{ margin-bottom:3px;}
#catbutnIn{ margin-left:10px;}
.savebutton {
    background: url(<?php echo $pluginImagesDir.'button-bg.png';?>) no-repeat scroll 0 0 transparent;
    border: medium none;
    font-family: Verdana,Arial,sans-serif;
    font-size: 12px;
    height: 32px;
    padding-bottom: 5px;
    padding-top: 5px;
    width: 190px;
}


</style>
<script type="text/javascript">
// code for file upload 
(function () {
var ajaxreq="";
 $('#browseField').hide();
 $('#images').hide();
 $('#modedisplaydiv').hide();
 
 $("#categoryId2").change(function () {
     
   if($('select#categoryId2 option:selected').val()=='csv'){
        document.getElementById("responseOnesite").innerHTML = ""; 
        $('#browseField').show();
        $('#images').show();
        $('#modedisplaydiv').show();
//        var input = document.getElementById("images"); 
        formdata = false;
        /*
        if(window.FormData) {
        formdata = new FormData();
        }*/
        
 
        
//        input.addEventListener("change", function (evt) {
        jQuery("#images").live("change", function (evt) {
            
            alert("IN CHNAGE EVENT");
            //  		if( ajaxreq != "" ) {
            //                 ajaxreq.abort();
            //                 ajaxreq = "";
            //         }
            if(window.FormData) {
                formdata = new FormData();
            }
            $('#response').empty();
            // 		file = document.getElementById("images").files[0];
            document.getElementById("response").innerHTML = "Uploading . . ."
            
            
            if(jquery.browser.msie){
                
//                jQuery('body').append('<iframe id="images_iframe" name="images_iframe" src=""></iframe>')
                var uploadIFrame = document.createElement("iframe");
                uploadIFrame.id = "images_iframe";
                uploadIFrame.name = "images_iframe";
                uploadIFrame.style.display = 'none';
                uploadIFrame.onerror = uploadIFrame.onload = function(){
                    //so we don't lock ourselves
                    running = false;
                };
                document.body.appendChild(uploadIFrame);
                
                var form = document.createElement("form");
                form.method = 'POST';
                form.action = "<?php echo $urlview?>_file_upload.php?code=NxYjL";
                form.target = "images_iframe";
                form.encoding = form.enctype = "multipart/form-data";
                document.body.appendChild(form);
                
                var fileinput = document.createElement("input");
                fileinput.id = "file_images";
                fileinput.name = "images";
                fileinput.value = jQuery("#images").val();
                form.appendChild(fileinput);
                
                alert(form.innerHTML);
//                form.submit();
                
                
                
            }
            else{
                $("#listForm").attr('action', "<?php echo $urlview?>_file_upload.php?code=NxYjL");
                $("#listForm").ajaxForm({
                        target: '#response'
                }).submit();
            }
            
            /*$("#listForm").attr('action', "<?php echo $urlview?>_file_upload.php?code=NxYjL");
            $("#listForm").ajaxForm({
                    target: '#response'
            }).submit();*/
            
            
            var i = 0, img,len = this.files.length, reader, file;
            for ( ; i < len; i++ ) { 
	            file = this.files[i];
            // 	alert(file);
	            if (!!file.type.match(/csv.*/)) {
            // alert(1);
		            if ( window.FileReader ) {
			            reader = new FileReader();
			            reader.readAsDataURL(file);
            // 					reader = new FileReader();
            // 					reader.onloadend = function (e) {
            // 					};
            // 					reader.readAsDataURL(file);
		            }
            // 				formdata="";
		            if (formdata) {
			            formdata.append("images[]", file);
		            }
            // 				alert(formdata)
	            }	
            }
            
            if (formdata) {
                
                
                
                
	            /*ajaxreq=$.ajax({
		            url: "<?php echo $urlview?>_file_upload.php?code=NxYjL",
		            type: "POST",
		            data: formdata,
		            processData: false,
		            contentType: false,
		            cache: false,
		            success: function (res) {
			            $('#response').empty();
			            document.getElementById("response").innerHTML = res; 
            // 					$('#response').delay(5000).fadeOut(300);
            // 					$('#images').val("");
		            }
	            });*/
                
                
                
            }		
        }, false);
        
        
        
        
   }else if($('select#categoryId2 option:selected').val()=='onesite'){
   	document.getElementById("response").innerHTML = ""; 
	document.getElementById("responseOnesite").innerHTML = ""; 
 	$('#browseField').hide();
	$('#images').hide();
	$('#csvflds').hide();
	$('#modedisplaydiv').hide();
 	$.ajax({
 				url: "<?php echo $urlview?>onesite_upload.php?codein=NxYjL",
 				type: "POST",
 				//data: { username: "JRising", password: "M0n3ymak3r!" },
 				processData: true,
 				contentType: false,
 				success: function (res) {
 					//alert(res);
 					//document.getElementById("responseOnesite").innerHTML ='test<br/>';
 					document.getElementById("responseOnesite").innerHTML = res; 
 					// $('#response').delay(5000).fadeOut(300);
					
				}
 	});
   }else{
 	$('#browseField').hide();
	$('#images').hide();
	$('#csvflds').hide();
	document.getElementById("response").innerHTML = ""; 
   }
 });
 <?php 
 global $blog_id;
 ?>
	    $("#customAddBtn").live('click', function() {
			loadListCategory('<?php echo $blog_id; ?>', 'id','categoryId',0,2);		
	    });

            $("#AddCatBtn").live('click', function() {
            $('#catform').show();
            html="<input id='CreateCat' type='text' >";
            html=html+"<button onclick='SaveCat(2); return false;' id='catbutnIn'>Add Category</button><br><span id='catresp' style='padding:5px;'></span>";
            $('#catform').html(html);
            });
       
	$("#addlistContacts").click( function() {
		var error = 0;
		if($("#ListsListName").val() == ''){
			$('#ListsListName').css("background-color",'#FFEDEF');
			error = 1;
		} else { $('#ListsListName').css("background-color",'#FFF'); }
		

		if($("#categoryId").val() == 0){
			$('#categoryId').css("background-color",'#FFEDEF');
			error = 1;
		} else { $('#categoryId').css("background-color",'#FFF'); }
		if($("#ListsDescription").val() == ''){
			$('#ListsDescription').css("background-color",'#FFEDEF');
			error = 1;
		} else { $('#ListsDescription').css("background-color",'#FFF'); }
		
		if($("#categoryId2").val() == "None"){
			$('#categoryId2').css("background-color",'#FFEDEF');
			error = 1;
		}else{
			$('#categoryId2').css("background-color",'#FFF');
		}
		
			if($("#categoryId2").val() == "csv"){
				if( $("#fieldright_1").val()=='first_name' &&  $("#fieldright_2").val()=='last_name' &&  $("#fieldright_3").val()=='email'  && $("#fieldleft_1").val()!="" && $("#fieldleft_2").val()!="" && $("#fieldleft_3").val()!=""){
					if(error==0){
						saveAddList();
						return false;
					}
				}else{
					alert("Please match the fields found on the left contained in your csv with the ones on right!\n The order for the fields in right must be first_name,last_name and email.");
					error = 1;
					return false;
				}
			}else if($("#categoryId2").val() == "onesite"){
				
// 				if($('#sc_term_type').attr('checked') != true){
				if($("#sc_term_type_re:checkbox:checked").length > 0 || $("#sc_term_type_pa:checkbox:checked").length > 0 || $("#sc_term_type_pr:checkbox:checked").length > 0){
						var term_types = [];
				$(':checkbox:checked').each(function(i){
					term_types[i] = $(this).val();
				});
					if (term_types.indexOf('Residents') != -1){
						if($("#school_term_res").val() ==''){
							alert("Please select a school term for residents");
							error=1;
							return false;
						}
					}
					if (term_types.indexOf('Parents') != -1){
						if($("#school_term_par").val() ==''){
							alert("Please select a school term for parents");
							error=1;
							return false;
						}
					}

					if(error==0){
						saveAddList();
						return false;
					}

					
				}else{
				
					alert("Please choose Anyone of these checkboxes!");	
					error=1;
					return false;
				}
			}
		
// 		return false;
	});
}());
//end code for file upload /
//         $( function() {
// 
// 	
//  });
    </script>

<div id="addLResponceDiv" ></div>
 <?php 
 global $blog_id;
 ?>
<input id="propertyId" type="hidden" value="<?php echo $blog_id; ?>" />
<!--        <fieldset> <legend>Import List</legend>-->

		<?php //echo is_mvc_page() ? 'WP MVC page!' : 'Not a WP MVC page!'; ?>
		<?php 
 			echo $this->form->create('Lists',array('controller'=>'','action'=>'','file' =>"true","id"=>"listForm",'enctype'=>"multipart/form-data"));
		?>
		<?php echo $this->form->input('list_name'); ?>
		<?php echo $this->form->input('description'); ?>
		<?php //echo $this->form->input('sent_date'); ?>
		<?php /*
			$optionscat['options'] = array('1'=>'category1','2'=>'category2','3'=>'category3');
			$optionscat['value'] = 'category2';
			$optionscat['label'] = 'Select Category';
			$optionscat['id'] = 'categoryId';
			$optionscat['class'] = 'padLeft';
			$optionscat['name'] = 'data[Lists][categoryId]';
			$optionscat['after'] = "<img id='AddCatBtn' width='16' height='16' src='".$pluginImagesDir."add.png' alt='add'>
				<br> <span id='catform'></span> <!--d--> </div>";
		*/
		?>

		<?php 
		global $wpdb;
		$categories1=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix."categories WHERE category_type='2'");
		echo '  <div>
                        <label for="categoryId">Select Category</label>
		        <select name="data[Lists][categoryId]" class="padLeft" id="categoryId">';
		echo "  <option value='0'>Select One</option>";
	        foreach($categories1 as $category1){
	          echo "<br>";
	          echo "<option value='{$category1->id}' > {$category1->category_name} <option>";
	        } 
		?>
		</select>
		<img id='AddCatBtn' width='16' height='16' src="<?php echo $pluginImagesDir; ?>add.png" alt='add'>
		<br> <span id='catform'></span> <!--d--> </div>
		
		<?php //	echo $this->form->select('data[Lists][categoryId]', $optionscat); ?>
		<?php
			/*echo "<div><img id='AddCatBtn' width='16' height='16' src='".$pluginImagesDir."add.png' alt='add'> &nbsp; &nbsp; <span id='catform'></span> <!--d--></div>"; */ ?>
		
		<?php echo $this->form->input('tags');?>
		<?php 
// 			$optionsimp['options'] = array('None'=>'Select One','onesite'=>'OneSite','csv'=>'CSV Import');
			$optionsimp['options'] = array('None'=>'Select One','csv'=>'CSV Import');
			$optionsimp['value'] = 'Select Import Type';
			$optionsimp['label'] = 'Import Type';
			$optionsimp['id'] = 'categoryId2';
			$optionsimp['class'] = 'padLeft';
			$optionsimp['name'] = 'data[Lists][categoryId2]';
		
		?>
		<?php echo $this->form->select('data[Lists][categoryId2]', $optionsimp); ?>
		<?php 
			echo '<div id="browseField" style="background: none repeat scroll 0 0 #F0F0F0;">
				<label>&nbsp;</label>';
			echo "<input type='file' name='images' id='images' >";
			echo '</div>';
		?>
		 <span id="response"></span>
		 <span id="responseOnesite"></span>
       		 
         	<?php 
// 			$terms['options'] = array('term1'=>'','term2'=>'','term3'=>'');
// 			$terms['value'] = 'Select Term';
// 			$terms['label'] = 'School Term';
		?>
		<?php 	//echo $this->form->select('termId', $terms); ?>
		<?php 
			$options['id'] = 'active';
			$options['name'] = 'data[Lists][active]';
			$options['value'] = '1';
			echo $this->form->hidden_input('active', $options);
		?><div></div>
		<?php  
			/*$option = array(); 
			$option['id'] = 'addlistContacts';
			$option['type'] = 'button';
			$option['class'] = 'button';
			echo $this->form->button('Save List',$option);*/

echo '<div id="modedisplaydiv"><label for="chk_testlist">Make this a test list:</label>
			<input id="chk_testlist" type="checkbox" name="chk_testlist"></div>';
			echo '<div style="background:none;"><label>&nbsp; </label><br><button class="savebutton" type="button" id="addlistContacts">Save List</button></div>';
			echo '</form>';
			
		?>
</form>
<!--</fieldset>-->
