<?php
session_start();
$user_id = $_SESSION['SESS_id'];
$time = time();
require_once(__DIR__.'/../Utils.php');
$utils = new Utils();
if(isset($_POST)){
    $qual_field = mysql_real_escape_string($_POST['qual_field']);
    $widget_type = $_POST['widget_type'];
    $widget_label = mysql_real_escape_string($_POST['widget_label']);
    $widget_lbl = $_POST['widget_lbl'];
    $widget_form = $_POST['widget_form'];
    $widget_field = $_POST['widget_field'];
    $widget_operator = $_POST['widget_operator'];
    $multi_delimit = mysql_real_escape_string($_POST['multi_delimit']);
    $multi_widget_lbl = $_POST['multi_widget_lbl'];
    $multi_widget_field = $_POST['multi_widget_field'];
    $multi_widget_operator = $_POST['multi_widget_operator'];
    $widget_name = mysql_real_escape_string($_POST['widget_name']);
    $repid = isset($_POST['repid']) ? $_POST['repid'] :0;
    $filter ="";
    $widget_chart_type = NULL;
    if($widget_type == 2){
        $widget_field = $widget_field.':'.$multi_widget_field;
        $widget_operator = $widget_operator.':'.$multi_widget_operator;
        $widget_lbl = mysql_real_escape_string($widget_lbl.':'.$multi_widget_lbl);
    }elseif($widget_type == 3){
        $widget_chart_type = $_POST['widget_chart_type'] ? $_POST['widget_chart_type'] : '';
        if($widget_chart_type){
            $chart_fields = isset($_POST['chart_fields'])? explode(',',$_POST['chart_fields']): '';
            if($chart_fields){

                // $chart_fields = array_map(function($arr){ return array($arr=>$arr);},$chart_fields);
                $chart_fields_assoc= array();
                foreach($chart_fields as $chart_field){
                    $chart_fields_assoc[$chart_field]=$chart_field;
                }
                if($widget_chart_type == 'pie'){
                    $filter = array('payload'=>$chart_fields_assoc);
                }else{
                    $x_axis = $_POST['widget_x_axis'];
                    $y_axis = $_POST['widget_y_axis'];
                    $filter = array('payload'=>$chart_fields_assoc,'x_axis'=>$x_axis,'y_axis'=>$y_axis);
                }
            }
        }
        if($filter){
            $qual_field = json_encode($filter);
        }
        
    }
    else{
        $widget_lbl = mysql_real_escape_string($widget_lbl);
    }
    $sql = "insert into widget 
        (status, widget_name, form_name, label_name, field_name, operator, filter, access, widget_label,created_at,created_by,widget_type,delimiter,repid,chart_type) 
        values(1,'{$widget_name}','{$widget_form}','{$widget_lbl}','{$widget_field}','{$widget_operator}','{$qual_field}','0','{$widget_label}',{$time},{$user_id},{$widget_type},'{$multi_delimit}','{$repid}','{$widget_chart_type}')";

    $utils->insertData($sql);
    $res = array("success"=>"success");
    echo json_encode($res);
    exit;


}