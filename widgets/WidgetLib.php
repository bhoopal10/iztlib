<?php
require_once(__DIR__.'/../Utils.php');
class WidgetLib extends Utils{
    public $counter=0;
    function getButton(){
        $btn = "<button type=\"button\" class='btn btn-info' id='widget-btn' title='Widget'><i class='glyphicon glyphicon-list-alt'></i></button>";
        return $btn;
    }

    function widgetContent($tbl=NULL){
       if(!$tbl) $tbl = $_GET['page'];
        $content = "
            <div class=\"widget-content hide\">
            <input type='hidden' value='".$tbl."' name='widget_form'>
                <div class=\"col-md-12 border\">
                    <div class=\"col-md-2 \">
                        <div class=\"form-group\">
                            <label for=\"widget_type\">Select Basic</label>
                            <select id=\"widget_type\" name=\"widget_type\" class=\"form-control col-md-2\">
                                <option value=\"1\">Single</option>
                                <option value=\"2\">Multiple</option>
                                <option value=\"3\">Chart</option>
                            </select>
                        </div>
                    </div>
                    <div class=\"clearfix\"></div>
                    <div id=\"single-widget\">
                        <div class=\"col-md-3\">
                            <div class=\"form-group\">
                                <label for=\"widget_lbl\">Label</label>
                                <input type=\"text\" name='widget_lbl' class=\"form-control\" placeholder=\"Label\" id=\"widget_lbl\">
                            </div>
                        </div>
                        <div class=\"col-md-3\">
                            <div class=\"form-group\">
                            <label for=\"inputValue\">Fields</label>
                            <select id=\"widget_field\" name=\"widget_field\" class=\"form-control\">
                                ".$this->getFieldOptions($tbl)."
                            </select>
                            </div>
                        </div>
                        <div class=\"col-md-3\">
                            <div class=\"form-group\">
                                <label for=\"widget_operator\">Operator </label>
                                <select id=\"widget_operator\" name=\"widget_operator\" class=\"form-control\">
                                    <option value=\"sum\">Sum</option>
                                    <option value=\"avg\">Avg</option>
                                    <option value=\"count\">Count</option>
                                </select>
                            </div>
                        </div>
                       <div class=\"clearfix\"></div>
                    </div>   
                    <!--- Multiple widget --->
                    <div id=\"multiple-widget\">
                    <div class=\"col-md-3\">
                        <div class=\"form-group\">
                            <label for=\"multi_widget_delimit\">Delimit</label>
                            <input type=\"text\" name='multi_delimit' class=\"form-control\" placeholder=\"Delimit\" id=\"multi_delimit\">
                        </div>
                    </div>

                    <div class=\"col-md-3\">
                        <div class=\"form-group\">
                            <label for=\"multi_widget_lbl\">Label</label>
                            <input type=\"text\" name='multi_widget_lbl' class=\"form-control\" placeholder=\"Label\" id=\"multi_widget_lbl\">
                        </div>
                    </div>
                    <div class=\"col-md-3\">
                        <div class=\"form-group\">
                            <label for=\"multi_widget_type\">Fields</label>
                            <select id=\"multi_widget_field\" name=\"multi_widget_field\" class=\"form-control\">
                                ".$this->getFieldOptions($tbl)."
                            </select>
                        </div>
                    </div>
                    <div class=\"col-md-3\">
                        <div class=\"form-group\">
                            <label for=\"multi_operator\">Operator </label>
                            <select id=\"multi_widget_operator\" name=\"multi_widget_operator\" class=\"form-control\">
                                <option value=\"sum\">Sum</option>
                                <option value=\"avg\">Avg</option>
                                <option value=\"count\">Count</option>
                            </select>
                        </div>
                    </div>
                    
                </div>
                <!-- Chart Widget -->
                <div id='chart-widget-div' class='hide'>".$this->chartForm()."</div>
                <div class=\"clearfix\"></div>
                <button type='button'  class=\"btn btn-info\" href='#' data-target='#widgetmodal' data-toggle='modal'>Save</button>
                </div>
                </div>
            <div class=\"clearfix\"></div>
        ";
        $content = $content.$this->getSaveModal();
        return $content;
    }

    function getFieldOptions($tbl=NULL,$is_value_alias=false){
        $repid = isset($_GET['repid']) ? $_GET['repid'] : false;
        if(!$tbl) $tbl = $_GET['page'];
        $options = "";
        if(!$repid){

            $dbtbl = explode('.',$tbl);
            $db = '';
            if(count($dbtbl)>1){
                $db = $dbtbl[0].'.';
                $tbl = $dbtbl[1];
            }
            $fields = "select fieldid,name,alias,type from {$db}field where tblid in (select tblid from {$db}config where name = '{$tbl}') and name not in ('id','created_at','modified_at','modified_by','created_by')";
            $fields = $this->all($fields);
            foreach($fields as $field){
                if(!$is_value_alias)$options .="<option value='".$field->fieldid."'>".$field->alias."</option>";
                else $options .="<option value='".$field->name."'>".$field->alias."</option>";
            }
        }
        return $options;
    }
    function getSaveModal(){
        $repid = isset($_GET['repid']) ? $_GET['repid'] : false;
        $modal = "
        <div class=\"modal fade\" id=\"widgetmodal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"widgetModalLabel\" aria-hidden=\"true\">
            <div class=\"modal-dialog\">
                <div class=\"modal-content\">
                <div class=\"modal-header\">
                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button>
                    <h4 class=\"modal-title\"  >Widget Details</h4>
                </div>
                <div class=\"modal-body\" >
                    <div class=\"col-md-12\">
                        <div class=\"form-group\">
                            <label for=\"\">Widget Name</label>
                            <input type=\"text\" name=\"widget_name\" id=\"widget_name\" class=\"form-control\">
                        </div>
                        <div class=\"form-group\">
                            <label for=\"\">Widget Label</label>
                            <input type=\"text\" name=\"widget_label\" id=\"widget_label\" class=\"form-control\">
                        </div>
                    </div>
                </div>
                <div class=\"modal-footer\">
                    <button type=\"button\" id=\"save-widget\" data-repid=\"{$repid}\" class=\"btn btn-primary\" data-url=\"lib/widgets/index.php\"  >Save</button>
                </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
            </div>
        ";
        return $modal;
    }

    function getAllWidgets($tbl,$qual='',$query='',$chart_data=array()){
        $repid = isset($_GET['repid']) ? $_GET['repid'] : false;
        if($repid) $sql = "select * from widget where form_name = '{$tbl}' and status = 1 and repid= '{$repid}'";
        else $sql = "select * from widget where form_name = '{$tbl}' and status = 1";
        $data = $this->all($sql);
        $widg = "";
        foreach($data as $value){
            if($value->filter) $value->filter = $this->extractDateFromQual($value->filter);
            $widg .= $this->setWidget($value,$qual,$query,$chart_data);
        }
        if($widg){
            $widg='<div class="col-md-12 widgetdiv" style="padding-left: 15px; padding-right: 15px;">
                        <div class=\"row\">
                            <nav id="history-box-container" class="arrow">
                            <div id="btn-history-nav-previous" class="hidden-md hidden-lg">&lt;</div>
                            <div id="btn-history-nav-next" class="hidden-md hidden-lg">&gt;</div> 
                            <div class="history-inner-box">
                            <div class="menu" style="overflow:hidden; width:1220px;">'
                            .$widg.
                            "</div>
                            </div>
                            </nav>
                        </div>
                    </div>";
        }
        return $widg;

    }
    function getHavingClause($tbl){
        $having = isset($_POST['having'])? $_POST['having'] :'';
        $order_by_select = isset($_POST['widget_order_by']) ? $_POST['widget_order_by']:'';
        $having_select = isset($_POST['widget_having_name'])? $_POST['widget_having_name'] :'';
        $having_op = isset($_POST['widget_having_operator'])? $_POST['widget_having_operator'] :'';
        $q = "<select name='widget_having_name' data-selected='".$having_select."'><option value=''>Select Having Field</option></select>&nbsp;
            <select name='widget_having_operator' data-selected='".$having_op."'>
                <option value=\"=\">Equal</option>
                <option value=\">\">greaterthan</option>
                <option value=\"<\">Lessthan</option>
                <option value=\">=\">greaterthan or equal</option>
                <option value=\"<=\">Lessthan or equal</option>
                <option value=\"!=\">Not Equal</option>
            </select>&nbsp;
            <input type='text' value='".$having."' name='having'>&nbsp;
            <select name='widget_order_by' data-selected='".$order_by_select."'><option value=''>Select Order By</option></select>&nbsp;
            <button class='btn btn-info btn-sm'><span class='glyphicon glyphicon-arrow-right'></span>Filter</button>";
        return $q;
    }
    function getAllChart($tbl,$filter){
        $sql = "select * from widget where form_name = '{$tbl}' and status = 1 and widget_type = 3";
        $data = $this->all($sql);
        $raw_data = array();
        $chart = "";
        foreach($data as $value){
            // extract filter from jso to json_decode
            if($value->filter){
                $arr = json_decode($value->filter);
                if($arr && $arr->filter && $arr->filter->common_for == $filter['common_for']){
                    $alldata = $arr;
                    $raw_data = $this->all($filter['sql']);
                    $json = json_encode($raw_data);
                    $chart .= $this->parseChart($json,$alldata,$value);
                }
                // print_r($arr->filter->common_for);
                // if($arr['filter']['common_for'] == $filter['common_for']){
                //     $data = $arr;
                // }
            }
        }
        return $chart;
    }

    function parseChart($json,$alldata,$data){
        $payload = $alldata->payload;
        $counter = $this->counter;
        $this->counter = $counter+1;
        $list = array();
        $type = $data->chart_type;
        $yaxis = "";
        $yaxisfield = '';
        $i =0;
        $x_axis = isset($alldata->x_axis)?$alldata->x_axis:'';
        $y_axis = isset($alldata->y_axis)?$alldata->y_axis:'';
        foreach($payload as $key=>$value){
            if($i == 0){
                $yaxis = $value;
                $yaxisfield= $key;
                $x_axis = $x_axis ? $x_axis : $value;
            }
            if($i == 1){
                $y_axis = $y_axis ? $y_axis : $value;
            }
            $temp = array(
                "balloonText" => "$value :- [[category]] : <b>[[value]]</b>",
                "bullet"=> "square",
                "lineAlpha" => 0.8,
                "title" => $value,
                "type" => "$type",
                "valueField" => $key
            );
            if($type == 'column') $temp['fillAlphas'] = '0.9';
            array_push($list,$temp);
            $i++;
        }
        $json_payload = json_encode($list);
        $main_type = $data->chart_type == 'pie'? 'pie':'serial';
        $chart = '<div id="chartdiv'.$counter.'"></div><script>AmCharts.makeChart("chartdiv'.$counter.'", {
            "theme": "light",
            "type": "'.$main_type.'",
            "dataProvider":'.$json.'  ,
            "graphs": '.$json_payload.',
            "valueAxes": [{
                //"unit": "%",
                "position": "left",
                "title": "'.$y_axis.'",
            }],
            "titleField": "'.$x_axis.'",
            "valueField": "'.$y_axis.'",
            
            "startDuration": 1,
            // "plotAreaFillAlphas": 0.1,
            "categoryField": "'.$x_axis.'",
            "categoryAxis": {
                "gridPosition": "start",
                "labelRotation": 30
            }
        
        })</script>';
        return $chart;
    }

    function setWidget($data,$qual,$query='',$chart_data=array()){
        $num = rand(1,6);
        $ref = $data->filter;
        $col = $data->col_size ? $data->col_size : '3';
        $div = "<div class=\"col-sm-{$col}\">";
        $div .='<div class="history-item ">';
        $div .="<div class=\"panel panel-default\">";
        $div .='<div class="panel-body text-center">';
        if($data->widget_type != '3'){
        $div .="<a href=\"#\"  data-ref=\"$ref\" class='tiles'>";
        $div .= "</br>";
        $div .="<h4  class=\"text-center\"><span>".$this->setLabel($data,$qual,$query,$chart_data)."</span></h4>";
        $div .= "<br>";
        $div .="<div class='clearfix'></div>";
        $div .="</a></div>";
        $div .="<div class=\"panel-footer text-center\">".$data->widget_label;
        }else{
            $div .= $this->setLabel($data,$qual,$query,$chart_data)."</div>";
            $div .="<div class=\"panel-footer text-center\"><a href='#' data-toggle='modal' data-target='#chart-modal'>".$data->widget_label.'</a>';
        }
        $div .="</div>";
        $div .="</div>";
        $div .="</div>";
        $div .="</div>";
        return $div;
    }

    function setLabel($data,$qual,$query='',$chart_data){
        $label = $data->label_name;
        $field_name = $data->field_name;
        $operator = $data->operator;
        $display = "";
        if(!trim($data->filter)) $data->filter = '1=1';
        if($data->widget_type == 1){
            $data->filter = ($qual) ? $data->filter.' and '.$qual: $data->filter;
            // echo $data->filter;
            $getval = $this->getValue($field_name,$operator,$data->filter,$data->form_name);
            $getval = $this->inrCurrency($getval);
            $display = $label." ".$getval;
        }else if($data->widget_type == 2){
            $data->filter = ($qual) ? $data->filter.' and '.$qual: $data->filter;
            $fields = explode(':',$field_name);
            $op = explode(':',$operator);
            $lbl = explode(':',$label);
            $val = array();
            foreach($fields as $key=>$value){
                $o = isset($op[$key])? $op[$key]:'';
                $getval = $this->getValue($value,$o,$data->filter,$data->form_name);
                $getval = $this->inrCurrency($getval);
                $list = $o." ".$getval;
                array_push($val,$list);
            }
            $delimiter = $data->delimiter? $data->delimiter:' ';
            $display = implode('  '.$delimiter.'  ',$val);
        }else if($data->widget_type == 3){
            $arr = json_decode($data->filter);
            $alldata = $arr;
            if(isset($chart_data['data'])){
                $raw_data = $chart_data['data'];
            }else if(isset($chart_data['sql'])){
                $raw_data = $this->all($chart_data['sql']);
            }
            $raw_data = array_map(function($arr) use($alldata){ 
                if($alldata->payload){
                    $payloads = $alldata->payload;
                    $tmp = array();
                    foreach($payloads as $key=>$payload){
                        if(isset($arr[$key])){
                            $tmp[$key] = $arr[$key];
                        }
                    }
                    return $tmp;
                }
                // return array('invoice_date'=>$arr['invoice_date'],'total'=>$arr['total']); 
            },$raw_data);
            $json = json_encode($raw_data);
            $display = $this->parseChart($json,$alldata,$data);
        }else if($data->widget_type == 4){
            $display = $label." ".$this->getMultiValue($field_name,$operator,$query);
            
        }

        return $display;
    }

    function getMultiValue($field,$operator,$query){
        $op = $operator.'('.$field.') as aggrigate ';
        $query = "SELECT $op from ({$query}) as tmp";
        $data = $this->first($query);
        return $data->aggrigate;
    }

    function getValue($field_id,$operator,$qual,$tbl){
        $dbtbl = explode('.',$tbl);
        $db = '';
        if(count($dbtbl)>1){
            $db = $dbtbl[0].'.';
            $tbl = $dbtbl[1];
        }
        $field = $this->first("select name from {$db}field where fieldid = {$field_id}");
        $field = $field->name;
        if(trim($qual)) $qual = "where {$qual}";
        if($operator){
            $sql = "select $operator($field) as res from {$db}{$tbl} $qual";
            $data = $this->first($sql);
            if($data) return $data->res;
            else return '';
        }else{
            return '';
        }

    }

    function getChart($data){
        $div ="<script>";
        // $data = json_decode($data->filter);
        // print_r($data);
        $div .= "google.charts.load('current', {packages: ['corechart', 'bar']});
google.charts.setOnLoadCallback(drawAxisTickColors);

function drawAxisTickColors() {
      var data = google.visualization.arrayToDataTable([
        ['City', '2010 Population', '2000 Population'],
        ['New York City, NY', 8175000, 8008000],
        ['Los Angeles, CA', 3792000, 3694000],
        ['Chicago, IL', 2695000, 2896000],
        ['Houston, TX', 2099000, 1953000],
        ['Philadelphia, PA', 1526000, 1517000]
      ]);

      var options = {
        title: 'Population of Largest U.S. Cities',
        chartArea: {width: '50%'},
        hAxis: {
          title: 'Total Population',
          minValue: 0,
          textStyle: {
            bold: true,
            fontSize: 12,
            color: '#4d4d4d'
          },
          titleTextStyle: {
            bold: true,
            fontSize: 18,
            color: '#4d4d4d'
          }
        },
        vAxis: {
          title: 'City',
          textStyle: {
            fontSize: 14,
            bold: true,
            color: '#848484'
          },
          titleTextStyle: {
            fontSize: 14,
            bold: true,
            color: '#848484'
          }
        }
      };
      var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
      chart.draw(data, options);
    }
        
        </script>";
        return $div;
    }
    
    function plainWidget($div_id){
        $div = "<div class=\"col-sm-3\">";
        $div .="<div class=\"panel panel-default\">";
        $div .='<div class="panel-body text-center">';
        $div .="<a href=\"#\" class='tiles'>";
        $div .= "</br>";
        $div .="<div id='".$div_id."'></div>";
        $div .= "<br>";
        $div .="<div class='clearfix'></div>";
        $div .="</a></div>";
        $div .="<div class=\"panel-footer text-center\">Chart";
        $div .="</div>";
        $div .="</div>";
        $div .="</div>";
        return $div;
    }

    function chartForm(){
        $chart_type = '<div class="col-md-12">
                        <div class="col-md-3 form-group">
                            <label for="inputValue">Chart Type</label>
                            <select id="widget_chart_type" name="widget_chart_type" class="form-control">
                                <option value="pie">Pie</option>
                                <option value="column">Column</option>
                                <option value="line">Line</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group widget_x_axis hide">
                            <label for="inputValue">X-axis</label>
                            <select id="widget_x_axis" name="widget_x_axis" class="form-control">
                            '.$this->getFieldOptions(null,true).'
                            </select>
                        </div>
                        <div class="col-md-3 form-group widget_y_axis hide">
                            <label for="inputValue">y-axis</label>
                            <select id="widget_y_axis" name="widget_y_axis" class="form-control">
                            '.$this->getFieldOptions(null,true).'
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="inputValue">Fields</label>
                            <select id="chart_fields" name="chart_fields" class="form-control" multiple>
                            '.$this->getFieldOptions(null,true).'    
                            </select>
                        </div>
                    </div>
                    ';
        return $chart_type;
    }




    
}