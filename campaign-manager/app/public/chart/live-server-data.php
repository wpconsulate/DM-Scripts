<?php
include("../../../../../../wp-config.php");
header("Content-type: text/json");
global $wpdb;
 date_default_timezone_set('Asia/Calcutta');
$x = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sept","Oct","Nov","Dec");
if(isset($_GET["type"])){

        $type=$_GET["type"];
        switch($type){
        
        case "1":
                $x_type="";
                $y_click = array();
                $y_bounce = array();
                $y_send = array();
                $y_open = array();
                break;
        case "2":
                $date1= $_GET["val1"];
                $date2= $_GET["val2"];
                if($date1==$date2){
                
                $x = array("0:00:00","2:00:00","4:00:00","6:00:00","8:00:00","10:00:00","12:00:00","14:00:00","16:00:00","18:00:00","20:00:00","22:00:00","00:00:00");
for($i=0;$i<24;$i=$i+2){
$j=$i+2;
$time1="{$i}:00:00";
$time2=($i<22)?"{$j}:00:00":"23:59:59";

                $query= "SELECT COUNT(event) as click FROM `".$wpdb->prefix."sendgrid_status` WHERE `event`='click' AND time > '$time1' AND time < '$time2'  AND  date = '$date1'";
                $var_click[]=intval($wpdb->get_var($wpdb->prepare( $query)));
                
                
                $query= "SELECT COUNT(event) as click FROM `".$wpdb->prefix."sendgrid_status` WHERE `event`='processed'AND time > '$time1' AND time < '$time2'  AND  date = '$date1'";
                $var_sent[]=intval($wpdb->get_var($wpdb->prepare( $query)));
                
                $query= "SELECT COUNT(event) as click FROM `".$wpdb->prefix."sendgrid_status` WHERE `event`='bounce'  AND time > '$time1' AND time < '$time2'  AND  date = '$date1'";
                $var_bounce[]=intval($wpdb->get_var($wpdb->prepare( $query)));
                
                $query= "SELECT COUNT(event) as click FROM `".$wpdb->prefix."sendgrid_status` WHERE `event`='open'  AND time > '$time1' AND time < '$time2'  AND  date = '$date1'";
                $var_open[]=intval($wpdb->get_var($wpdb->prepare( $query))); 
                
               
        } 
                $y_click = $var_click;
                $y_bounce = $var_bounce;
                $y_send = $var_sent;
                $y_open = $var_open;
                $opt=array(6);
                $ret = array($opt,$x,$y_send,$y_click,$y_open,$y_bounce); 
                break;   
                }else{
                
                        $diff=round(abs(strtotime($date1)-strtotime($date2))/86400);
                      if($diff<31){
                                              # date < 31 (date view)
                              $date3=strtotime(date("Y-m-d", strtotime($date1)));
                              $date3=date("Y-m-d",$date3); 
                             $xval= $datex[]=date("d M Y", strtotime($date3)); 
                              while(strtotime($date3) <= strtotime($date2)){
                                 $query= "SELECT COUNT(event) as click FROM `".$wpdb->prefix."sendgrid_status` WHERE `event`='click' AND  date = '$date3'";        
                $val=$var_click[]=intval($wpdb->get_var($wpdb->prepare( $query)));            
                $query= "SELECT COUNT(event) as click FROM `".$wpdb->prefix."sendgrid_status` WHERE `event`='processed'AND  date = '$date3'";
                $var_sent[]=intval($wpdb->get_var($wpdb->prepare( $query)));
                
                $query= "SELECT COUNT(event) as click FROM `".$wpdb->prefix."sendgrid_status` WHERE `event`='bounce' AND  date = '$date3'";
                $var_bounce[]=intval($wpdb->get_var($wpdb->prepare( $query)));
                
                $query= "SELECT COUNT(event) as click FROM `".$wpdb->prefix."sendgrid_status` WHERE `event`='open' AND  date = '$date3'";
                $var_open[]=intval($wpdb->get_var($wpdb->prepare( $query))); 
          
                                  $date3=strtotime(date("Y-m-d", strtotime($date3)) . "+1 day");
                                  $xval=$datex[]=date("d M Y", $date3);
                                  $date3=date("Y-m-d",$date3);               
                                 
                                }
                 $x = $datex;
                $y_click = $var_click;
                $y_bounce = $var_bounce;
                $y_send = $var_sent;
                $y_open = $var_open;
                $opt=array(6);
                $ret = array($opt,$x,$y_send,$y_click,$y_open,$y_bounce);   
                                 break;
                        } 
                        
                        # date >31 (month view)
                        
                              $date3=strtotime(date("Y-m-d", strtotime($date1)));
                              $date3=date("Y-m-d",$date3); 
                             // $date2=strtotime(date("Y-m-d", strtotime($date2)) . "+1 month");
                           //  $xval= $datex[]=date("d M Y", strtotime($date3));
                           $f=0; 
                              while(1){
                                  
                                  $date3=strtotime(date("Y-m-d", strtotime($date3)) . "+1 month");
                                  $xval=$datex[]=date("d M Y", strtotime($date1));
                                  $date3=date("Y-m-d",$date3); 
                              
                                 $query= "SELECT COUNT(event) as click FROM `".$wpdb->prefix."sendgrid_status` WHERE `event`='click' AND  date >= '$date1' AND date <= '$date3'";      
                $val=$var_click[]=intval($wpdb->get_var($wpdb->prepare( $query))); 
                
                //echo "$query : $xval: $val\n";           
                $query= "SELECT COUNT(event) as click FROM `".$wpdb->prefix."sendgrid_status` WHERE `event`='processed'AND date >= '$date1' AND date <= '$date3'";
                $var_sent[]=intval($wpdb->get_var($wpdb->prepare( $query)));
                
                $query= "SELECT COUNT(event) as click FROM `".$wpdb->prefix."sendgrid_status` WHERE `event`='bounce' AND  date >= '$date1' AND date <= '$date3'";
                $var_bounce[]=intval($wpdb->get_var($wpdb->prepare( $query)));
                
                $query= "SELECT COUNT(event) as click FROM `".$wpdb->prefix."sendgrid_status` WHERE `event`='open' AND  date >= '$date1' AND date <= '$date3'";
                $var_open[]=intval($wpdb->get_var($wpdb->prepare( $query)));               
                                 $date1=$date3;
                                 if($f==1)break;
                                 if(strtotime($date3) > strtotime($date2)){
                                  $f=1;
                                 }
                                }
                 $x = $datex;
                $y_click = $var_click;
                $y_bounce = $var_bounce;
                $y_send = $var_sent;
                $y_open = $var_open;
                $opt=array(6);
                $ret = array($opt,$x,$y_send,$y_click,$y_open,$y_bounce);   
                                 break;



                }
                 $x = $datex;
                $y_click = $var_click;
                $y_bounce = $var_bounce;
                $y_send = $var_sent;
                $y_open = $var_open;
                $opt=array(6);
                $ret = array($opt,$x,$y_send,$y_click,$y_open,$y_bounce);    
                break;
        case "3":
                if(empty($_GET["val1"])) break;
                $campArray=explode(",",$_GET["val1"]);
                $ids="";
                $max=(count($campArray)>2)?2:count($campArray);
              //  print_r($campArray);
              for($l=0;$l<$max;$l++){
              $campid=$campArray[$l];
                for($i=1;$i<13;$i++){
                $j=$i+1; 
                $y=date("Y");
                 $date1="$y-$i-1";
                if($j==13){
                        $j=1;
                        $y=$y+1;
                }
                $date2="$y-$j-1";
                
                $query= "SELECT COUNT(event) as click FROM `".$wpdb->prefix."sendgrid_status` WHERE `event`='click' AND campaign='".$campid."' AND  date >= '$date1' AND date <= '$date2'";
                $var_click[$l][]=intval($wpdb->get_var($wpdb->prepare( $query)));
                
                
                $query= "SELECT COUNT(event) as click FROM `".$wpdb->prefix."sendgrid_status` WHERE `event`='processed'AND campaign='".$campid."' AND  date >= '$date1' AND date <= '$date2'";
                $var_sent[$l][]=intval($wpdb->get_var($wpdb->prepare( $query)));
                
                $query= "SELECT COUNT(event) as click FROM `".$wpdb->prefix."sendgrid_status` WHERE `event`='bounce' AND campaign='".$campid."' AND  date >= '$date1' AND date <= '$date2'";
                $var_bounce[$l][]=intval($wpdb->get_var($wpdb->prepare( $query)));
                
                $query= "SELECT COUNT(event) as click FROM `".$wpdb->prefix."sendgrid_status` WHERE `event`='open' AND campaign='".$campid."' AND  date >= '$date1' AND date <= '$date2'";
                $var_open[$l][]=intval($wpdb->get_var($wpdb->prepare( $query))); 
                }                                                      
              }
               $x = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sept","Oct","Nov","Dec");
                $y_click = $var_click[0];
                $y_bounce = $var_bounce[0];
                $y_send = $var_sent[0];
                $y_open = $var_open[0];
                $y1_click = $var_click[1];
                $y1_bounce = $var_bounce[1];
                $y1_send = $var_sent[1];
                $y1_open = $var_open[1];  
                $opt=array(10,$campArray[0],$campArray[1]);
                $ret = array($opt,$x,$y_send,$y_click,$y_open,$y_bounce,$y1_send,$y1_click,$y1_open,$y1_bounce);     
                break;
        case "4":
                if(empty($_GET["val1"])) break;
                $propArray=explode(",",$_GET["val1"]);
                $ids="";
                
              for($l=0;$l<2;$l++){
              $propid=$propArray[$l];
             // print_r($propArray);
                for($i=1;$i<13;$i++){
                $j=$i+1; 
                $y=date("Y");
                 $date1="$y-$i-1";
                if($j==13){
                        $j=1;
                        $y=$y+1;
                }
                $date2="$y-$j-1";
                
                $query= "SELECT COUNT(event) as click FROM `".$wpdb->prefix."sendgrid_status` WHERE `event`='click' AND property='".$propid."' AND  date >= '$date1' AND date <= '$date2'";
                $var_click[$l][]=intval($wpdb->get_var($wpdb->prepare( $query)));
                
                
                $query= "SELECT COUNT(event) as click FROM `".$wpdb->prefix."sendgrid_status` WHERE `event`='processed'AND property='".$propid."' AND  date >= '$date1' AND date <= '$date2'";
                $var_sent[$l][]=intval($wpdb->get_var($wpdb->prepare( $query)));
                
                $query= "SELECT COUNT(event) as click FROM `".$wpdb->prefix."sendgrid_status` WHERE `event`='bounce' AND property='".$propid."' AND  date >= '$date1' AND date <= '$date2'";
                $var_bounce[$l][]=intval($wpdb->get_var($wpdb->prepare( $query)));
                
                $query= "SELECT COUNT(event) as click FROM `".$wpdb->prefix."sendgrid_status` WHERE `event`='open' AND property='".$propid."' AND  date >= '$date1' AND date <= '$date2'";
                $var_open[$l][]=intval($wpdb->get_var($wpdb->prepare( $query))); 
                }                                                      
              }
               $x = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sept","Oct","Nov","Dec");
                $y_click = $var_click[0];
                $y_bounce = $var_bounce[0];
                $y_send = $var_sent[0];
                $y_open = $var_open[0];
                $y1_click = $var_click[1];
                $y1_bounce = $var_bounce[1];
                $y1_send = $var_sent[1];
                $y1_open = $var_open[1];  
                $opt=array(10);
                $ret = array($opt,$x,$y_send,$y_click,$y_open,$y_bounce,$y1_send,$y1_click,$y1_open,$y1_bounce);   
                break;
        default:
        
                $x_type="";
                $y_click = array();
                $y_bounce = array();
                $y_send = array();
                $y_open = array();
                $opt=array(6);
                $ret = array($opt,$x,$y_send,$y_click,$y_open,$y_bounce);
                break;        
        }

                echo json_encode($ret);
}


?>

