<?php
date_default_timezone_set("Asia/Kolkata");  
require_once('DbModel.php');
require_once('Utils.php');
function activitycheck($beforeupdatedata=null,$afterupdatedata=null,$tbl,$rid,$action,$pid=null,$parenttbl=null)
{    
    // echo "action:".$action.",pid:".$pid.",parenttbl:".$parenttbl.",rid:".$rid;
    $db_model = new DbModel();
    $utils = new Utils();
    $time=time();
    $parent_id=$pid;
    $q="select tblid from config where name='$tbl'";
    $row= $db_model->firstArray($q);
    if($row)$tblid=$row['tblid'];
    $x=explode("=",$rid);
    $id=$x[1];
    $user_id = $_SESSION['SESS_id'];
    
    if($beforeupdatedata && $afterupdatedata)
    {
        if($action=="update" || $action=="childupdate"){
    $query="select fieldid,trigger_status from activitylogconfig where tblid=$tblid and trigger_status=1 and logshow=1 ";
    $result=$db_model->allArray($query);
    $selfieldsid=array();
    // while($row=mysql_fetch_assoc($result))
    foreach($result as $row)
    {
        array_push($selfieldsid,$row['fieldid']);
    }
    $selfields=array();
    foreach($selfieldsid as $selfieldid)
    {
        $alias_query="select name,alias from field where fieldid=$selfieldid";
        $result=$db_model->allArray($alias_query);
        foreach($result as $row)
        {
            $selfields[$row['name']]=$row['alias'];
        }
    }
    // differentiate from and after arrays
    $changeddata = array();
    foreach($afterupdatedata as $key=>$value){
        if($beforeupdatedata[$key] != $value){
            $changeddata[$key] = $value;
        }
    }  
    // $utils->write_log("debug","selected:".json_encode($selfields));
	foreach($changeddata as $field => $value)
    {	
        if(in_array($field,array_keys($selfields)))
        {
            $row=$db_model->firstArray("select tblid,fieldid,type,alias from field where tblid = '{$tblid}' and name='$field'");
            if($row && !empty($row))
            {
                $field_type=$row['type'];
                $field_alias=$row['alias'];
                $tblid=$row['tblid'];
                $fieldid=$row['fieldid'];
            }
            else
            {
                echo "field type or alias is not defined for $field";
            }
            $utils->write_log("debug","option:-->>".$field.$field_type);
            if($field_type=='idate')
            {
                if($beforeupdatedata[$field]){

                    $beforeupdatedata[$field]=$utils->getmydate($beforeupdatedata[$field]);
                    $value=$utils->getmydate($value);
                    $msg="$field_alias:&nbsp&nbsp".$value."&nbspwas&nbsp".$beforeupdatedata[$field]."<br>";
                }else{
                    $value=$utils->getmydate($value);
                    $msg="$field_alias:&nbsp&nbsp".$value;
                }
            }else if($field_type=='option')
            {
                if($beforeupdatedata[$field]){
                    // $msg="$field_alias:&nbsp&nbsp".$utils->getOptionAlias($fieldid,$value)."&nbspwas&nbsp".$utils->getOptionAlias($fieldid,$beforeupdatedata[$field])."<br>";
                    $msg="$field_alias:&nbsp&nbsp".$utils->getOptionAlias($fieldid,$value)."&nbsp changed from  &nbsp".$utils->getOptionAlias($fieldid,$beforeupdatedata[$field])."<br>";
                }else{
                    $msg="$field_alias:&nbsp&nbsp".$utils->getOptionAlias($fieldid,$value);
                }
            }else if($field_type=='list')
            {
                if($beforeupdatedata[$field]){
                    // $msg="$field_alias:&nbsp&nbsp".$utils->getOptionAlias($fieldid,$value)."&nbspwas&nbsp".$utils->getOptionAlias($fieldid,$beforeupdatedata[$field])."<br>";
                    $msg="$field_alias:&nbsp&nbsp".$utils->getFirstListAlias($fieldid,$value)."&nbsp changed from  &nbsp".$utils->getOptionAlias($fieldid,$beforeupdatedata[$field])."<br>";
                }else{
                    $msg="$field_alias:&nbsp&nbsp".$utils->getFirstListAlias($fieldid,$value);
                }
            }
            else
            {
                // $msg="<b>$field_alias&nbsp&nbsp&nbsp&nbsp</b>"." "."is modified from"." ".$beforeupdatedata[$field]." "."to"." ".$value."<br>";
                if($beforeupdatedata[$field]) $msg="$field_alias&nbsp:&nbsp&nbsp".$value."&nbspwas&nbsp".$beforeupdatedata[$field];
                else $msg="$field_alias&nbsp:&nbsp&nbsp".$value;
            }
            
            if($action=="childupdate")
            {
                $q="select tblid from config where name='$parenttbl'";
                $row=$db_model->firstArray($q);
                $parenttblid=$row['tblid'];
                $qq2=$db_model->firstArray("select alias from config where name='$tbl'");
                if($qq2)
			        $tblalias=$qq2['alias'];
                else
                echo "No alias for $parenttbl in config";
                $msg="In <b>$tblalias</b> Record <b>$id</b>:<br>"."<b>$field_alias</b>&nbsp:&nbsp&nbsp".$value."&nbspwas&nbsp".$beforeupdatedata[$field];
                $qq="insert into activitylog(activity,user_id,modified_at,modified_by,parent_id,tableid) values ('$msg','$user_id','$time','$user_id','$parent_id','$parenttblid')"; 
            }
            else
            {
                if($tbl=='orders')
                {
                    $b=array();
                    $b= getOrderData($tbl,$id);
                    $status_id=$b['status'];
                    $qq="insert into activitylog(activity,user_id,modified_at,modified_by,parent_id,tableid,status_id) values ('$msg','$user_id','$time','$user_id','$id','$tblid','$status_id')"; 
                }
                else
                {
                    $qq="insert into activitylog(activity,user_id,modified_at,modified_by,parent_id,tableid) values ('$msg','$user_id','$time','$user_id','$id','$tblid')"; 
                }    
            }
            // $utils->write_log("debug"," sql:".$qq);
            $ins_query=$db_model->executeQuery($qq);

        }

    }
    }
    }
    else
    {
        if($action="create")
        {

            if($tbl=="orders")
            {
              $b=array();
              $b= getOrderData($tbl,$id);
              $orderno=$b['orderid'];
              $status_id=$b['status'];
              $msg="Order $orderno is created";
              $qq="insert into activitylog(activity,user_id,created_at,created_by,parent_id,tableid,status_id) values ('$msg','$user_id','$time','$user_id','$id','$tblid','$status_id')"; 
              
            }
            else
            {
                $msg="New $tbl is created";
                $qq="insert into activitylog(activity,user_id,created_at,created_by,parent_id,tableid) values ('$msg','$user_id','$time','$user_id','$id','$tblid')"; 
            }
            $ins_query=$db_model->executeQuery($qq);
            $utils->write_log("debug",$qq);

        }
    }
}

function getOrderData($tbl,$id)
{
    $db_model = new DbModel();
	$order_query="select * from orders where id='$id'";
	$rs=$db_model->firstArray($order_query);
    $orderid=$rs['order_no'];
    $status=$rs['status'];
    $arr=array();
    $arr['orderid']=$orderid;
    $arr['status']=$status;
    return $arr;
}

function activityLog($tbl,$qual,$ck=false){
    $db_model = new DbModel();
    $utils = new Utils();
	$q=$db_model->first("select tblid from config where name='$tbl'");
	$tblid=$q->tblid;
	$xx=explode("=",$qual);
	$id=$xx[1];
	$qq="select a.*,s.empname from activitylog a left join users s on a.user_id = s.id where a.parent_id='$id' and a.tableid='$tblid' order by a.id desc";
	$res1=$db_model->allArray($qq);
	$aa="";
	$aa=$aa.'<a class="btn btn-primary btn-xs" id="activitiesbtn" role="button" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">Activities </a><div class="clearfix">&nbsp;</div>';
	$tg_panel="<div class=\"panel panel-default\">";
	$tg_heading="<div class=\"panel-heading\">";
	$tg_body="<div class=\"panel-body\">";
	$tg_footer="<div class=\"panel-footer\">";
    $tg_div_cl="</div>";
    $b="";
	// $aa=$aa.'<div  class="in" id="collapseExample" >';
	// $aa=$aa.'<div class="row"><div class="col-sm-8 col-sm-push-2"><div class="input-group"><textarea class="col-md-4" rows="3" cols="10" id="commentbox" style="width:500px; height: 50px;"></textarea>&nbsp&nbsp';
	// $aa .= "<button class='btn btn-warning ' type='button' data-table='comments' data-pid='$id' data-ptblid='$tblid' data name='newcommentbtn' id='newcommentbtn' value='new' data-type='new'>POST</button></div></div></div><br>";
   
    $b.="<br>"."<a id=\"activitylogbtn\" data-toggle=\"modal\" data-target=\"#act\" class=\"actvtylogbutton\" style='padding:0px 0px !important;'><img src='images/activitylog.png' alt='log' /></a>";
    $b.="<div  class='tab-col-md-12 modal  collapse ' id='act' style='overflow-y: hidden !important;'>";
        $b.="<div class='modal-header childmodalheader'>
                <button type='button' class='close relativeModalclose' data-dismiss='modal'>&times;</button>
                <h4 class='modal-title' style='color:black;'>Activity Log</h4>
            </div>";
        $b.="<div class='row childmodalbody' style='margin-top: 5px; margin-bottom: 5px; overflow-y: scroll; max-height:85%;'>";
            $b.="<div class='relativeTable'>";
                $b.="<div class='relativeTableHeading'>";
                    $b.="<div class='divTableHead col-md-3 hidden-xs hidden-sm' style='font-weight:normal;'>Date Time</div>
                        <div class='divTableHead col-md-4 hidden-xs hidden-sm' style='font-weight:normal;'>User</div>
                        <div class='divTableHead col-md-5 hidden-xs hidden-sm' style='font-weight:normal;'>Activity Log Details</div>";
                $b.="</div>";
                $b.="<div class='relativeTableBody activitymodalTableBody'>";
                if($res1)
                {
                    $i=1;
                    foreach($res1 as $row)
                    {
                        $msg=$row['activity'];
                        $modified_at=$row['modified_at'];
                        $created_at=$row['created_at'];
                        $created_by=$row['created_by'];
                        $status=$row['status_id'];
                        if($modified_at)
                        {
                            $b.="<div class='row relativeTableRow responsiveTableRow'>".
                            "<div class='relativeTableCell col-lg-3 col-xs-6 '><label class='hidden-md hidden-lg' style='font-weight:normal;'><strong>Date Time : </strong></label>".date('d-m-Y h:i:s a',$modified_at)."</div>".
                            "<div class='relativeTableCell col-lg-4 col-xs-6 '><label class='hidden-md hidden-lg' style='font-weight:normal;'><strong>User : </strong></label>".$row['empname']."</div>".
                            "<div class='relativeTableCell col-lg-5 col-xs-12 '><label class='hidden-md hidden-lg' style='font-weight:normal;'><strong>Activity Log Details : </strong></label>".$msg."</div>".
                            "</div>";
                        }    
                        else
                        {
                            $b.="<div class='row relativeTableRow responsiveTableRow'>".
                            "<div class='relativeTableCell col-lg-3 col-xs-6 '><label class='hidden-md hidden-lg' style='font-weight:normal;'><strong>Date Time : </strong></label><small>".date('d-m-Y h:i:s a',$created_at)."</small></div>".
                            "<div class='relativeTableCell col-lg-4 col-xs-6 '><label class='hidden-md hidden-lg' style='font-weight:normal;'><strong>User : </strong></label>".$row['empname']."</div>".
                            "<div class='relativeTableCell col-lg-5 col-xs-12 '><label class='hidden-md hidden-lg' style='font-weight:normal;'><strong>Activity Log Details : </strong></label>".$msg."</div>".
                            "</div>";
                        }
                        $i++; 
                    } 
                }
                else
                {
                    $b.="No Activity Log for this record";
                }	
                $b.="</div>";
            $b.="</div>";
        $b.="</div>";
    $b.="</div>";            
    $aa=$b;
    $qu=$db_model->allArray("select logshow from activitylogconfig where tblid='$tblid'");
    if($qu)
    {
        foreach($qu as $row)
        {
        if($row['logshow']==1)
        $showstatus=1;
        else
        $showstatus=0;	
        }
    }
    else
        $showstatus=0;
    if($showstatus==1) 
    {
        return $aa;
    }
    else
        return "";    
    }
?>