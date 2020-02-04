<?php
//Declaration Section
require_once('auth.php');
// include('display.php');
include('CoreView.php');
// include('addrow.php');
include('CoreController.php');
// include('update.php');
include('AppView.php');
include("events.php");
 include('AppTemplateView.php');
//include('rowaccess.php');
include('sms_utils.php');
//$tbl=isset($_POST['tbl'])?$_POST['tbl']:'abc';
//$tbl='resourcing';
// include('testlib.php');
require_once('AppController.php');
// require_once('DbModel.php');
require_once('AppTemplateView.php');
include('tempupd.php');
require_once('NotificationLog.php');
// include('activitylog_lib.php');

//DB SQLs
// $db_model = new DbModel(); //includes at home.php
$coreC = new CoreController();
$coreV = new CoreView();
$appV = new AppView();
// $utils = new Utils();
$appC = new AppController();
$appTV=new AppTemplateView();
$notilog = new NotificationLog();


if(isset($_POST['delete']))
{
// delete($tbl);
$coreC->delete($tbl);
}
if(isset($_POST['update']) || isset($_POST['updates']))
{
// update($tbl);

$coreC->update($tbl);

/**
 * Added for Notification trigger
 */
if( isset($_POST['sel'])){
	$sel_i = $_POST['sel'];
	$id_i = explode('=',$sel_i);
	$id_i = isset($id_i[1])?$id_i[1]:'';
	$last_id_i =$id_i;
	// exec($utils->php_path." autocall.php {$tbl} {$last_id_i} update 1>> logs.log 2>&1");
	$notilog->initateLog($tbl,$id_i,'update');
}
//update_single($tbl);
//echo "<p> Records Updated </p>";
}
if(isset($_POST['addrow']))
{
if($tbl=='field')
{
// $sqli=alter();
$sqli=$coreC->alter();
$db_model->insertData($sqli);
$coreC->coreMigration($sqli);
	// coreMigration($sqli);
}
if($tbl=='config')
{
	// $sqli=createtable();
	$sqli=$coreC->createtable();
	$db_model->insertData($sqli);
}
// $sqli=insert($tbl);
$sqli=$coreC->insert($tbl);
$last_id =  $db_model->insertData($sqli,true);
$appC->insertRelatedData($last_id,$_POST);
if($tbl == 'orders') $utils->insertOrderDetails($tbl,$last_id);
// :TODO
// dbTrigger($tbl,$last_id);

$inserted_id ="id=".$last_id;
// shell_exec("my_script.sh 2>&1 | tee -a /tmp/mylog 2>/dev/null >/dev/null &");
// $shell->run($utils->php_path." -f autocall.php {$tbl} {$last_id} addrow > /dev/null 2>&1 &", 0, false);
$notilog->initateLog($tbl,$last_id,'addrow');
// exec($utils->php_path." -f autocall.php {$tbl} {$last_id} addrow > logs.log 2>&1 &");
// :TODO
activitycheck(null,null,$tbl,$inserted_id,$action="create",null,null);
if($tbl=='config'){
	// createid($last_id);
	$coreC->createid($last_id);
}

}
if(isset($_POST['create']))
{
// $sqli=create($tbl);
$sqli=$coreC->create($tbl);
// echo $sqli;exit;
$db_model->insertData($sqli);
	
}

if(isset($_POST['drop']))
{
$sqli="drop table $tbl";
$db_model->executeQuery($sqli);

}

	if(file_exists($_GET['page'])){
		include($_GET['page']);
		include('modal-bucket');
	}else{
		echo $_GET['page'];
		echo "<h3>Page Not found</h3>";
	}
 
//echo "<input id=\"btn\" type=\"submit\" name=\"addrow\" value=\"Add Row\">";
//echo "<input id=\"btn\" type=\"submit\" name=\"drop\" value=\"drop\">";

//if(!mysql_query("select * from ".$_GET['page']))
//{
//echo " Please click button to create table in the database";
//echo "<input id=\"btn\" type=\"submit\" name=\"create\" value=\"create\">";
//}
?>
