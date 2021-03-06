<?php
include("../../../../../../wp-config.php");
include "swift/swift_required.php";
include 'SmtpApiHeader.php';
date_default_timezone_set('Asia/Calcutta');
$hdr = new SmtpApiHeader();

global $wpdb;
$today=date("Y-m-d");
$res=$wpdb->get_results("SELECT * FROM `wp_messages` WHERE status='2' AND wp_messages.date='$today'");
if(count($res) < 1) die("Nothing to send");
$num=0;
foreach($res as $camp){
$num++;
$fnameList=array();
$lnameList=array();
$logoList=array();
$toList=array();
$rows = $wpdb->get_results("SELECT * FROM `wp_messages`
INNER JOIN `wp_campaign_list_rel` ON wp_messages.campaignID = wp_campaign_list_rel.campaign_id 
INNER JOIN `wp_contacts` ON wp_campaign_list_rel.list_id = wp_contacts.list_id INNER JOIN `wp_campaigns` ON wp_messages.campaignID = wp_campaigns.id WHERE wp_messages.id='".$camp->id."'");

foreach($rows as $row){
$fnameList[]= $row->first_name;
$lnameList[]=$row->last_name;
$logolist[]= "<img src=http://somdev1.us/wp-content/plugins/campaign-manager/images/logo/".$row->propertyID.".png width='255' height='144' />"
$toList[]=$row->email;
}
$hdr->addSubVal('{first_name}', $fnameList);
$hdr->addSubVal('{last_name}', $lnameList);
$hdr->addSubVal('{email_logo}', $logoList);
$hdr->addTo($toList);
//propertyid,List id,Campaign id,
$hdr->setCategory($rows[0]->categoryId);     //cat id
$hdr->setCategory($rows[0]->list_id);       //list id
$hdr->setCategory($camp->campaignID);      //Campaign id
$hdr->setCategory($rows[0]->propertyID);   //Property Id
$subject = $camp->message_name;

echo "<br>Cat id:".$rows[0]->categoryId;
echo "<br>list id:".$rows[0]->list_id;
echo "<br>Camp id:".$camp->campaignID;
echo "<br>Prop id:".$rows[0]->propertyId;

$from = array('campaigns@somdev.us' => 'Somdev');

$to = array('defaultdestination@example.com'=>'Default');
$text = <<<EOM
Hello -name-,
This message contains html charectors.This cannot be viewed in your inbox.
EOM;
$html=$camp->message;
$username = 'PeakCampus';
$password = '@tl@nt@12!';

$transport = Swift_SmtpTransport::newInstance('smtp.sendgrid.net', 25);
$transport ->setUsername($username);
$transport ->setPassword($password);
$swift = Swift_Mailer::newInstance($transport);
$message = new Swift_Message($subject);
$headers = $message->getHeaders();
$headers->addTextHeader('X-SMTPAPI', $hdr->asJSON());

$message->setFrom($from);
$message->setBody($html, 'text/html');
$message->setTo($to);
$message->addPart($text, 'text/plain');

if ($recipients = $swift->send($message, $failures))
{
mysql_query("UPDATE `wp_messages` SET `status`='3' WHERE id='".$camp->id."'") or die(mysql_error());
echo '<br>Message-'.$num.' sent out to '.count($toList).' users <br>';
}
else
{
echo "<br>Something went wrong - ";
print_r($failures);
}
}
