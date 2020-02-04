<?php
require_once('Utils.php');
require_once('DbModel.php');
class NotificationLog {
    public function initateLog($tbl,$last_id,$type){
        $db_model = new DbModel();
        $utils = new Utils();
        $sql="select * from notification_configuration where status = 'Active' and when_send = 'engine' and tblid in (select tblid from config where name='{$tbl}') order by id desc";
        $query = $db_model->all($sql);
        if($query){
            foreach($query as $res){
                if($res->is_insert && $type == 'addrow'){
                    
                    if(trim($res->conditions)){ 
                        $_qual = $utils->extractDateFromQual($res->conditions); 
                    }
                    if(trim($res->conditions)) $sql = "select * from {$tbl} where id = {$last_id} and {$_qual} ";
                    else $sql = "select * from {$tbl} where id = {$last_id} ";
                    $_query1 = $db_model->first($sql);
                    if($_query1){
                        $this->CreateMailLog($res,array($tbl=>$last_id));
                        }
                        // execInBackground('php mail-trigger.php?id='.$res->id);
                }
                if($res->is_update && $type == 'update'){
                    if(trim($res->conditions)){ 
                        $_qual = $utils->extractDateFromQual($res->conditions); 
                    }
                    if(trim($res->conditions)) $sql = "select * from {$tbl} where id = '{$last_id}' and {$_qual}";
                    else $sql = "select * from {$tbl} where id = '{$last_id}'";
                    $_query = $db_model->first($sql);
                    if($_query){
                        $this->CreateMailLog($res,array($tbl=>$last_id));
                    }
                }
            }
            // echo 'executed';exit;
            }
    }

    function CreateMailLog($configuration,$tdata){
            $utils = new Utils();
            $db_model = new DbModel();
            $qual ="";
            // print_r($configuration);
            if(!empty($tdata)){
                $finalmsgs = $utils->contentMap($configuration->mailbody,$tdata,true);
                $finalmsg = $finalmsgs['content'];
                $attachments = $finalmsgs['files'];
            }
            // if($configuration->attachments){
            //     $attachments = explode(',',$configuration->attachments);
            // }else{
            //     $attachments = array();
            // }
            $attachments = $configuration->attachments;
            // recipients getting from userid and group
            $user_ids = "";
            // get fields from users
            
            foreach($tdata as $key=>$value){
                $field_users = $utils->userByTblFileds($key,$configuration->fields,$value);
                if(!empty($field_users)) $user_ids = implode(',',$field_users);
            }
            // $utils->write_log("debug",json_encode($configuration));
        
            if(trim($configuration->users)){
                if($user_ids) $user_ids .= ",";
                $user_ids .= trim($configuration->users);
            }
            if(trim($user_ids)){
                $qual = " id in ({$user_ids}) "; 
            }
            // $utils->write_log('debug',$qual);
            if(trim($configuration->user_group)){
                $user_group = trim($configuration->user_group);
                if($user_group){
                    if($qual){
                        $qual .= "or access in ({$user_group}) "; 
                    }else{
                        $qual = " access in ({$user_group}) "; 
                    }
                }
            }
            if(trim($qual)) $user_sql = "select * from users where ".$qual;
            else  $user_sql = "select * from users";
            $users = $db_model->all($user_sql);
            // $utils->write_log('debug',json_encode($users));
        $email_array = array();
        foreach( $users as $user){
            // print_r($user);
            // $utils->write_log("debug",json_encode($user));
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
                // $msg = send_mail(array($mail['recipient_email']), array($mail['recipient']), array($mail['notification_subject']), array($mail['template']), $seconds,$mail['attachments']);
                $insert =  "insert into notification_log (template,recipient,recipient_email,attachment,send_status,send_remark,notification_id,notification_subject) values
                            ('".$mail['template']."','".$mail['recipient']."','".$mail['recipient_email']."','".$mail['attachments']."','initiated','NA','".$configuration->id."','".$mail['notification_subject']."')";
                // $utils->write_log("debug",$mail['recipient_email']);
                $db_model->insertData($insert);
            }
        }
            // print_r($email_array);
            // exit;
        
    }
}