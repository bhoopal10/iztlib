<?php
require_once('mail_function.php');
require_once('Utils.php');
require_once('DbModel.php');
function dbTrigger($tbl,$last_id = false){
    $db_model = new DbModel();
    $utils = new Utils();
    if($utils->check_is_ajax()){
        if($_POST['action'] == 'update'){
            $_POST['updates'] = 'update';
            $last_id = $_POST['recordid'];
        }else if($_POST['action'] == 'insert'){
            $_POST['addrow'] = 'addrow';
        }
    }
    if(isset($_POST['updates']) || isset($_POST['update'])){
        $sel = isset($_POST['sel']);
        if($sel){
            $sel = $_POST['sel'];
            $id = explode('=',$sel);
            $id = isset($id[1])?$id[1]:'';
            $last_id =$id;
        }
    }
    if(isset($last_id) && $last_id){
        // echo $last_id;exit;
        $sql="select * from notification_configuration where status = 'Active' and when_send = 'engine' and tblid in (select tblid from config where name='{$tbl}') order by id desc";
        $query = $db_model->all($sql);
        if($query){
        foreach($query as $res){
            if($res->is_insert && (isset($_POST['addrow']))){
                
                if(trim($res->conditions)){ 
                    $_qual = $utils->extractDateFromQual($res->conditions); 
                }
                if(trim($res->conditions)) $sql = "select * from {$tbl} where id = {$last_id} and {$_qual} ";
                else $sql = "select * from {$tbl} where id = {$last_id} ";
                $_query1 = $db_model->first($sql);
                    if($_query1){
                        FormMailTrigger($res,array($tbl=>$last_id));
                    }
                    // execInBackground('php mail-trigger.php?id='.$res->id);
            }
            if($res->is_update && (isset($_POST['updates']) || isset($_POST['update']))){
                if(trim($res->conditions)){ 
                    $_qual = $utils->extractDateFromQual($res->conditions); 
                }
                if(trim($res->conditions)) $sql = "select * from {$tbl} where id = '{$last_id}' and {$_qual}";
                else if (isset($p_field)) $sql = "select * from {$tbl} where {$p_field} = '{$last_id}'";
                $_query = $db_model->first($sql);
                if($_query){
                    FormMailTrigger($res,array($tbl=>$last_id));
                }
            }
        }
        // echo 'executed';exit;
        }
    }
}
function FormMailTrigger($configuration,$tdata){
    $utils = new Utils();
    $db_model = new DbModel();
    $qual ="";
    if(!empty($tdata)){
        $finalmsgs = $utils->contentMap($configuration->mailbody,$tdata,true);
        $finalmsg = $finalmsgs['content'];
        $attachments = $finalmsgs['files'];
    }
    if($configuration->attachments){
        $attachments = explode(',',$configuration->attachments);
    }else{
        $attachments = array();
    }
    // recipients getting from userid and group
    $user_ids = "";
    // get fields from users
    foreach($tdata as $key=>$value){
        $field_users = $utils->userByTblFileds($key,$configuration->fields,$value);
        if(!empty($field_users)) $user_ids = implode(',',$field_users);
    }

    if(trim($configuration->users)){
        if($user_ids) $user_ids .= ",";
        $user_ids .= trim($configuration->users);
        $qual = " id in ({$user_ids}) "; 
    }
    if(trim($configuration->user_group)){
        $user_ids = trim($configuration->users);
        if($qual){
            $qual = "and access in ({$user_ids}) "; 
        }else{
            $qual = " access in ({$user_ids}) "; 
        }
    }
    $user_sql = "select * from users where ".$qual;
    $users = $db_model->all($user_sql);
$email_array = array();
foreach( $users as $user){
    // print_r($user);
$content = $utils->contentMap($finalmsg,array('users'=>$user->id),true);
    $user_arr = array(
        'recipient' => $user->empname,
        'recipient_email' => $user->email_id,
        'template' => $content['content'],
        'notification_subject' => $configuration->subject,
        'attachments' => $attachments
    );
    array_push($email_array,$user_arr);
    
}
foreach($email_array as $mail){
    if($mail['recipient_email']){
        $seconds = 20;
        send_mail(array($mail['recipient_email']), array($mail['recipient']), array($mail['notification_subject']), array($mail['template']), $seconds,$mail['attachments']);
    }
}
    // print_r($email_array);
    // exit;

}


function mailTrigger($notification_id,$tdata=array()){
    $utils = new Utils();
    $db_model = new DbModel();
    $sql = "select * from notification_log where notification_id = {$notification_id}";
    $query = $db_model->all($sql);
    $mail_array =array();
    if($query){
        foreach($query as $res){
            $finalmsg = $res->template;
            if(!empty($tdata)){
                    $finalmsgs = $utils->contentMap($res->template,$tdata,true);
                    $finalmsg = $finalmsgs['content'];
                    $attachments = $finalmsgs['files'];
            } 
            if($res->attachment){
                $attachments = explode(',',$res->attachment);
            }else{
                $attachments = array();
            }
            $mail_array[] =array(
                'recipient' => $res->recipient,
                'recipient_email' => $res->recipient_email,
                'template' => $finalmsg,
                'notification_subject' => $res->notification_subject,
                'attachments' => $attachments
            );
        }
    }
    $seconds=0;
    if(count($mail_array)){
        foreach($mail_array as $mail){
            send_mail(array($mail['recipient_email']), array($mail['recipient']), array($mail['notification_subject']), array($mail['template']), $seconds,$mail['attachments']);
        }
    }
}

function execInBackground($cmd) { 
    error_reporting(E_ALL);
    if (substr(php_uname(), 0, 7) == "Windows"){ 
        echo $cmd;
    //    echo exec($cmd . " > /dev/null &");   
    //    echo exec("start /C C:\\xampp\\php\\php.exe ". $cmd);  
    $my_file= pclose(popen("C:\\xampp\\php\\php.exe -q  C:\\xampp\\htdocs\\ub_order\\lib\\mail-trigger.php", "r")); 
    $last_line = system('C:\\xampp\\php\\php.exe -q C:\\xampp\\htdocs\\ub_order\\lib\\mail-trigger.php', $retval);
   
       
    } 
    else { 
        exec($cmd . " > /dev/null &");   
    } 
}
  