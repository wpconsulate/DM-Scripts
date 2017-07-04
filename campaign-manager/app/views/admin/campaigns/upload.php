<?php 

echo '<pre>'; print_r($_FILES); echo '</pre>';

@$ftmp = $_FILES['image']['tmp_name'];
@$oname = $_FILES['image']['name'];
@$fname = $_FILES['image']['name'];
@$fsize = $_FILES['image']['size'];
@$ftype = $_FILES['image']['type'];

if(IsSet($ftmp)) : $fp = fopen($ftmp, 'r');
$content = fread($fp, filesize($ftmp));
$content = addslashes($content);
fclose($fp);
// include your database configuration & connection
$new_file_name = time()."_".$fname;


echo '<br><br>N: '.$new_file_name."<br>T: ".$ftype."<br>S:  ".$fsize;//.", ".$content;
/*
$query = "INSERT INTO file_upload (name, type, size, content)
VALUES('".$new_file_name."', '".$ftype."', ".$fsize.", '".$content."')";
$result = mysql_query($query);
$file_id = mysql_insert_id();
*/

?>
<html>
<head>
<script>
var par = window.parent.document;
var list = par.getElementById('list');
var fileid = par.createElement('div');
var inpid = par.createElement('input');
var imgdiv = list.getElementsByTagName('div')[];
var image = imgdiv.getElementsByTagName('img')[0];

imgdiv.removeChild(image);
list.removeChild(imgdiv);

fileid.setAttribute('id', 'upfile<?php echo $file_id;?>');//Remove 1 in the php tag
fileid.innerHTML = '';
inpid.type = 'hidden';
inpid.name = 'filename[]';
inpid.value = '<?php echo $file_id;?>';//Remove 1 in the php tag
list.appendChild(fileid);
fileid.appendChild(inpid);
</script>
</head>
</html>

<?php //remove 1 in php tag
   exit();
endif;
?>


<html>
<head>
<script language="javascript">
function upload(){
window.form.submit();
/*
//alert('upload function call');
// hide old iframe
var par = window.parent.document;
var num = par.getElementsByTagName('iframe').length - 1;
var iframe = par.getElementsByTagName('iframe')[num];
iframe.className = 'hidden';
// create new iframe
var new_iframe = par.createElement('iframe');
new_iframe.src = 'upload.php';
new_iframe.frameBorder = '0';
par.getElementById('iframe').appendChild(new_iframe);
// add image progress
var list = par.getElementById('list');
var new_div = par.createElement('div');
var new_img = par.createElement('img');
new_img.src = 'Green-004-loading.gif';
new_img.className = 'load';
new_div.appendChild(new_img);
list.appendChild(new_div);
// send
var imgnum = list.getElementsByTagName('div').length - 1;
document.iform.imgnum.value = imgnum;
*/
//document.iform.submit();
}
</script>
<style>
body {vertical-align:top;}
</style>
</head>


<body>

<form name="iform" action="" method="post" enctype="multipart/form-data" style="height:150px">
<input id="file" type="file" name="image" onchange="javascript:this.form.submit();">**
<input type="submit" name="submit" value="Submit">
<input type="hidden" name="hidfld" value="1315">
<input type="hidden" name="imgnum">
</form>
</html>