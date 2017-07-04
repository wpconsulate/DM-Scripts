<?php
$message =  '<p>Hello Admin,</p>';
$message =  '<p>&nbsp;</p>';
$message .= '<p>Following Info has been submitted on Enquiry Form!</p>';

$message .= '<p>FULLNAME: '.$post['fname'].'</p>';
$message .= '<p>EMAIL: '.$post['emailid'].'</p>';
$message .= '<p>PH NUMBER: '.$post['phone'].'</p>';
$message .= '<p>Mobile: '.$post['mobile'].'</p>';
$message .= '<p>Nature of Work: '.implode(', ', $_POST['nature_work']).'</p>';  
$message .= '<p>Council Approval: '.$post['council_approval'].'</p>';
$message .= '<p><h3>Work Description</h3>: '.$post['work_desc'].'</p>';

ob_start();
    require_once( dirname(__FILE__).'/tmpl/email.php');
    $email_contents = ob_get_contents();
ob_end_clean();

$tplImageURL = 'URL_TO_IMAGES_FOLDER';
$toReplace['IMAGE_PATH'] = $tplImageURL;
// $toReplace['LOGO'] = 'http://vernedwards.com/wp-content/uploads/2013/12/logo.jpg';
$toReplace['LOGO'] = $tplImageURL.'/logo.jpg';
$toReplace['DATE'] = date("F j, Y");
$toReplace['TITLE'] = 'Online Quoting Form Submission!';
$toReplace['MESSAGE'] = $message;


foreach($toReplace as $key=>$val){
    $email_contents = str_replace('{'.$key.'}', $val, $email_contents);
}

//$email_contents