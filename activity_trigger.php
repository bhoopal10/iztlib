<?php
function trigger($tbl){
    // check action available in notification
    $sql = "select * from notification_configuration where tblid in (select tblid from config where name like '$tbl') and status like 'active'";
    $query = mysql_query($sql) or die(mysql_error());
    if(mysql_num_rows($query)){
        while($res = mysql_fetch_object($query)){
        //    check if it is insert or update
            if($res->when_send == 'engine'){
                if($res->is_insert == '1') initInsertAlert('insert');
                if($res->is_update == '1') initIUpdateAlert('update');
            }
        //  check if Event triggers
            if($res->when_send == 'event'){
                if($res->is_insert == '1') initEvent('insert');
                if($res->is_update == '1') initEvent('update');
            }
            //  check if Event triggers
            if($res->when_send == 'trigger'){
                initTrigger();
            }

        }
       
    }
    // echo $sql;exit;
}

function initAlert(){

}

function initEvent(){

}
function initTrigger(){

}
