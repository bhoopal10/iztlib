<?php
require_once("lib/CoreView.php");
function autrigger($tbl)
{
  // echo $tbl;
  $coreV = new CoreView();
  $q="select tblid from config where name='$tbl'";
  $res=mysql_query($q) or die(mysql_error());
  $row=mysql_fetch_assoc($res);
  $tblid=$row['tblid'];
 // $query="select fieldid,trigger_status from activitylogconfig where tblid=$tblid and trigger_status='1' and logshow='1' ";

  $query="select fieldid,trigger_status from activitylogconfig where tblid=$tblid and trigger_status=1 and logshow=1 ";
  // echo $query;
  $result=mysql_query($query) or die(mysql_error());
  if(PHP_V > 5.6){
    $selfieldsid=array();
  }else{
    $selfieldsid= array();
  }
  while($row=mysql_fetch_assoc($result))
  {
      array_push($selfieldsid,$row['fieldid']);
  }
  $selfields=array();
  foreach($selfieldsid as $selfieldid)
  {
    $alias_query="select name,alias from field where fieldid=$selfieldid";
    $result=mysql_query($alias_query) or die(mysql_error());
    while($row=mysql_fetch_assoc($result))
    {
      $selfields[$row['name']]=$row['alias'];
    }
  }  
  // $rel = 'activitylog';
  // $refid = getRelationIdByTable($tbl,$rel);  
  //$selfields=['customer_name','lead_status','assigned_to','engagement','value','customer_contact','short_description'];
  $user_id = $_SESSION['SESS_empid'];
  $q="select empname  from users where empid='$user_id'";
  $res=mysql_query($q) or die(mysql_error());
  while($row=mysql_fetch_assoc($res))
  {
    $username=$row['empname'];
  }
  // $selid=explode('=',$sel);
  // $id=$selid[1];
  // echo $id;
  // $del_trigger="delete from activitylogconfig where tblid='$tbl'";
  // mysql_query($del_trigger) or die(mysql_error());
  $tri1="";

  $tri1=$tri1."CREATE  OR REPLACE  after_update_trigger  AFTER UPDATE ON $tbl FOR EACH ROW BEGIN ";
  $tri2="";
  foreach($selfields as $selfield => $alias)
  {
    $tri2=$tri2."if old.$selfield <> new.$selfield then
    insert into activitylog(activity,user_id,modified_at,modified_by,parent_id,tableid) values (concat(' $alias ',' is modified from ',OLD.$selfield,' to ',NEW.$selfield),'$username',now(),'$username',OLD.id,$tblid); 
    set @lastid=LAST_INSERT_ID();
    insert into reldata (relid,parentrecid,childrecid) values('$refid','$id',@lastid);
    end if;"; 
  }

  $trigger_query= $tri1.$tri2."END;";    
  echo $trigger_query;
  mysql_query($trigger_query) or die("here".mysql_error());
}
 
?>
