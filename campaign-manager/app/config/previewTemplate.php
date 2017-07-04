<?php
$TempHeader=txt2html($TempHeader);
$TempFooter=txt2html($TempFooter);
echo $TempHeader;
?>
<p>This is the preview of the template</p>
<?php
echo $TempFooter;
function stri_replace( $find, $replace, $string ) {
	$parts = explode( strtolower($find), strtolower($string) );
	$pos = 0;
	foreach( $parts as $key=>$part ){
		$parts[ $key ] = substr($string, $pos, strlen($part));
		$pos += strlen($part) + strlen($find);
	}
	return( join( $replace, $parts ) );
}
function txt2html($txt) {
	// $txt=nl2br($txt);
	while( !( strpos($txt,'  ') === FALSE ) ) $txt = str_replace('  ',' ',$txt);
	$txt = str_replace(' >','>',$txt);
	$txt = str_replace('< ','<',$txt);
	$txt = htmlentities($txt);
	$txt = str_replace('"','"',$txt);
	$txt = str_replace('<','<',$txt);
	$txt = str_replace('>','>',$txt);
	$txt = str_replace('&','&',$txt);
	$txt = stri_replace("<a href=\"http://","<a target=\"_blank\" href=\"http://",$txt);
	$txt = stri_replace("<a href=http://","<a target=\"_blank\" href=http://",$txt);
	$path=get_site_url()."/wp-admin/images/18.jpg";
	$email_logo_path='<img src="'.$path.'" width="75px" height="75px"/>';
	$txt = stri_replace("{email_logo}",$email_logo_path,$txt);
	
	$eol = ( strpos($txt,"\r") === FALSE ) ? "\n" : "\r\n";
	$html = '<p>'.str_replace("$eol","</p><p>",$txt).'</p>';
	$html = str_replace("$eol","
	\n",$html);
	$html = str_replace("</p>","</p>\n\n",$html);
	$html = str_replace("<p></p>","<p> </p>",$html);
	$wipebr = Array("table","tr","td","blockquote","ul","ol","li");
	for($x = 0; $x < count($wipebr); $x++) {
		$tag = $wipebr[$x];
		$html = stri_replace("<$tag>
		","<$tag>",$html);
		$html = stri_replace("</$tag>","</$tag>",$html);
	}
	return $html;
}

//*/put your html code here 
/*$html_code = '<div>'.$TempHeader.'here comes the body'.$TempFooter.'</div>';

 $img = imagecreate("300", "600"); 
 imagecolorallocate($img,0,0,0); 
 $c = imagecolorallocate($img,70,70,70); 
 imageline($img,0,0,300,600,$c2); 
 imageline($img,300,0,0,600,$c2); 

$white = imagecolorallocate($img, 255, 255, 255); 
imagettftext($img, 9, 0, 1, 1, $white, "latha.ttf", $html_code); 


header("Content-type: image/jpeg"); 
imagejpeg($img,"underline.jpeg"); */
// echo "<img src='underline.jpeg'/>"; */
?>  