<?php
echo '<pre>';
print_r($_POST);
echo '</pre>';
?>
<html>
<head>
  <title></title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta name="GENERATOR" content="Quanta Plus">
  <link>
  <style type="text/css">

	iframe {
	border-width: 0px;
	height: 450px;
	width: 400px;
	vertical-align:top;
	}
	iframe.hidden {
	visibility: hidden;
	width:0px;
	height:0px;
	}

  </style>
  <script>
alert(1);
  </script>
</head>
<body>
<form id="listForm" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">		<div><label for="ListsListName">List Name</label><input id="ListsListName" name="list_name" value="" type="text"></div>		<div><label for="ListsDescription">Description</label><textarea id="ListsDescription" name="description"></textarea></div>						<div><label for="categoryId">Select Category</label><select id="categoryId" class="padLeft" name="categoryId"><option value="0">Select One</option><option value="2">C2</option><option value="10">New Cat</option><option value="15">Information Database</option><option value="23">list category</option><option value="26">list category2</option><option value="31">test list category</option></select><img id="AddCatBtn" src="http://somdev1.us/wp-content/plugins/campaign-manager/images/add.png" alt="add" height="16" width="16">
				<br> <span id="catform"></span> <!--d--> </div>				
		<div><label for="ListsTags">Tags</label><input id="ListsTags" name="tags" value="" type="text"></div>				<div><label for="categoryId2">Import Type</label><select id="categoryId2" class="padLeft" name="categoryId2"><option value="None">Select One</option><option value="csv">CSV Import</option></select></div>		<div id="browseField" style="background: none repeat scroll 0px 0px rgb(240, 240, 240); display: none;">
				<label>&nbsp;</label><input style="display: none;" name="images" id="images" type="file"></div>		 <span id="response"></span>
		 <span id="responseOnesite"></span>
       		 
         					<input id="active" name="active" value="1" type="hidden"><div></div>
		<div style="display: none;" id="modedisplaydiv"><label for="chk_testlist">Make this a test list:</label>
			<input id="chk_testlist" name="chk_testlist" type="checkbox"></div>
<div style="background:none;"><label>&nbsp; </label><br><button class="savebutton" type="button" id="addlistContacts" onclick="this.form.submit();">Save List</button></div>

	<div id="iframe">
		<iframe src="upload.php" frameborder="0" scrolling="0"></iframe>
	</div>
	<div id="list"></div>

</form>

</body>
</html>