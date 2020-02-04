<?php
require_once(__DIR__.'/../AppView.php');
require_once(__DIR__.'/../Utils.php');
require_once('./WidgetLib.php');
if(isset($_POST['tbl'])){
    $appV = new AppView();
    $utils = new Utils();
    $widget = new WidgetLib();
    $tbl = $_POST['tbl'];
    $qual = $_POST['qual'];
    $field_show = $utils->getTblFieldList($tbl,'fieldlist');
    $field_show = explode(',',$field_show); 
    $array_row = $appV->display_raw($tbl,$qual,1,$field_show,$status=false);
    $content = $widget->getAllWidgets($tbl,$qual,'',array('data'=>$array_row));
    header("Content-Type: application/json");
    echo json_encode(array('success'=>$content));
    exit;
}