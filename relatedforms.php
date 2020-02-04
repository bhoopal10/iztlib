<?php
$parentid="";
$parenttbl="";
require_once('DbModel.php');
require_once('Utils.php');
function relatedformload($sel,$tbl,$related_btn= array())
{
if(!empty($_POST['tbl']) && !empty($_POST['sel']) && !empty($_POST['chdtbl'])){
    $tbl            =   $_POST['tbl'];
    $sel            =   $_POST['sel'];
    $cdtbl        =   $_POST['chdtbl'];
    $related_btn    =   array(
        'edit'=>array('comments','agreement','pdf_attachment','purchase_order_inward','tasks'),
        'new'=>array('comments','agreement','pdf_attachment','purchase_order_inward','tasks','notification_log','quotations','custom_contact'),
        'delete'=>array('comments','agreement','pdf_attachment','purchase_order_inward','tasks')
    );
    $utils = new Utils();
    $db_model = new DbModel();
    $x=explode('=',$sel);
    $pid= $x[1];
    $parentid= $pid;
    $parenttbl=$tbl;
    $formreldata=array();
    $tbc="";$k=0;
    // checking for direct relations rel_type=1
    $qqq="select id,child from formrel where parent='$parenttbl' and rel_type=1";
    $res=$db_model->allArray($qqq);
    foreach($res as $row)
    { 
        if($row['child']    ==  $cdtbl){
            $formreldata[$row['id']]=array('tbl'=>$row['child'],'pid'=>$parentid);
        }
    }
    // Check if any refernce relations rel_type=2
    $ref_datas = $db_model->allArray("select id,ref_parent,child,parent_ref_field from formrel where parent='$parenttbl' and rel_type = 2 and child='$cdtbl'");
    foreach($ref_datas as $ref_data){
        $cur_parent = $ref_data['ref_parent'];
        $cur_child = $ref_data['child'];
        $ref_field = $ref_data['parent_ref_field'];
        $qqq="select id,child from formrel where parent ='{$cur_parent}' and child = '{$cur_child}' ";
            $res=$db_model->allArray($qqq);
            foreach($res as $row)
            {
                $ref_sql = $db_model->firstArray("select {$ref_field} from {$tbl} where {$sel}");
                if($ref_sql && !empty($ref_sql)){

                    $_parent_id = $ref_sql[$ref_field];
                    $formreldata[$row['id']] = array('tbl'=>$row['child'],'pid'=>$_parent_id);
                }
            }
    }
    $tb ="";
    // $tb.="<div class='accordion-group'>";
        // $tb.="<div class='panel-group' id='accordion2' aria-multiselectable='true'>";
            // $tb.="<div class='panel' style='border:none; background-color: transparent;'>";
                // $tb.="<ul class='floating-icon'>";
                $k=0;
                    foreach($formreldata as $relconf)
                    {      
                        $tbl =  $relconf['tbl']; 
                        $tbl_alias = $utils->getTableAlias($tbl);
                //         $tb.="<li class='pointer' role='$tbl'><a data-toggle='modal' data-target='#$tbl' aria-controls='$tbl'> $tbl_alias </a></li>";
                        $k++; 
                    }
                // $tb.= "</ul>";
    $k=0;
    foreach($formreldata as $id => $relconf)
    {  
        $tbl =  $relconf['tbl']; 
        $pid = $relconf['pid'];
        $btn_new ="";
        $edit_flag = 0;
        $delete_flag = 0;
        if(isset($related_btn['edit']) && in_array($tbl,$related_btn['edit'])) $edit_flag = 1;
        if(isset($related_btn['edit']) && in_array($tbl,$related_btn['edit'])) $delete_flag = 1;
        // if(isset($related_btn['new']) && in_array($tbl,$related_btn['new'])){
        //     $btn_new = "<button style='margin-top:0px !important;' class=\"btn btn-warning new addnewdata\" type=\"button\" data-editable=\"$edit_flag\" data-deletable=\"$delete_flag\" data-table=\"$tbl\" name=\"newbtn\" id=\"newbtn\" value=\"new\" data-type=\"new\" data-toggle=\"modal\" data-target=\"#childModal\"/>New</button>";
        // }
        $k++;
        $tbl_alias = $utils->getTableAlias($tbl);
        // $tb.="<div  class='tab-col-md-12 modal  collapse fade tabpane' id='$tbl' style='overflow-y: hidden !important;'>";
            // $tb.="<div class='modal-header childmodalheader'>
            //         <button type='button' class='close relativeModalclose' data-dismiss='modal'>&times;</button>
            //         <h4 class='modal-title' style='color:black;'>$tbl_alias</h4>
            //     </div>";                 
            // $tb.= "<div class='row childmodalbody realtedForm' id='' style='margin-top: 5px; margin-bottom: 5px; overflow-y: scroll; max-height:85%;'>"; 
                // $tb.="<div class='relativeTable'>"; 
                    // $tb.="<div class='relativeTableHeading'>";
                        $q="select showfields from workflow where formname='$tbl'";
                        $res=$db_model->first($q);                       
                        $show=array();
                        if($res)
                        {
                            $fieldstring=$res->showfields;
                            $show=explode(',',$fieldstring);
                            array_unshift($show,"id");
                        }
                        else
                        {
                            $columnnamequery=" select column_name from information_schema.columns where table_name='$tbl'";
                            $res=$db_model->allArray($columnnamequery);
                            foreach($res as $row)
                            {
                                array_push($show,$row['column_name']);
                            }
                        }
                        $count="";
                        foreach($show as $col)
                        {
                            $rs=$db_model->first("select alias from field where name='$col'");
                            if($rs)
                            {
                                $alias=$rs->alias;
                            }
                            else
                            {
                                $alias=$col;
                            }
                            $count                =   count(array_keys($show));
                            if($count   <=  5){
                                $div_col    =   'col-lg-2';
                            }else{
                                $div_col    =   'col-lg-1';
                            }
                            // $tb.="<div class='divTableHead $div_col hidden-xs hidden-sm' style='font-weight:normal;'>$alias</div>";
                        }                        
                    // $tb.="</div>";
                    // $tb.="<div class='relativeTableBody modalTableBody$tbl' style='overflow-y: scroll;'>";
                        $q="select showfields from workflow where formname='$tbl'";
                        $res=$db_model->first($q);                               
                        if($res)
                        {
                            $fieldstring=$res->showfields;
                            $ff=explode(",",$fieldstring);
                            if(!in_array("id",$ff))
                            {
                                array_unshift($ff,"id");
                            }
                            $fieldstring=implode(",",$ff);
                            $qual="1=1";
                            $data_sql="select $fieldstring from ".$tbl." where $qual and  id in (select childrecid from reldata where relid=".$id." and  parentrecid=".$pid.") order by id desc";
                        }
                        else
                        {
                            $data_sql="select * from $tbl where id in (select childrecid from reldata where relid=".$id." and  parentrecid=".$pid.") order by id desc";
                        }

                        $res=$db_model->allArray($data_sql);
                        if(!empty($res))
                        {
                            // $cnt="";
                            foreach($res as $row)
                            {
                                $cnt                =   count(array_keys($row));
                                $col                =   floor(10/$cnt);
                                if($cnt       <=  5){
                                    $div_col    =   'col-lg-'.$col;
                                }else if($cnt   ==  6){
                                    if((isset($related_btn['edit']) && in_array($tbl,$related_btn['edit'])) || (isset($related_btn['delete']) && in_array($tbl,$related_btn['delete']))){
                                        $div_col    =   'col-lg-1';
                                    }else{
                                        $div_col    =   'col-lg-2';
                                    }
                                }else{
                                    $div_col    =   'col-lg-1';
                                }
                                $tb.="<div class='row relativeTableRow responsiveTableRow'> ";
                                foreach($row as $field => $value)
                                { 
                                    $rs=$db_model->first("select alias from field where name='$field'");
                                    if($rs)
                                    {
                                        $alias=$rs->alias;
                                    }
                                    else{
                                        $alias=$col;
                                    }
                                    if($field=="id")                                    
                                    $x=$value;
                                    $field_name=$field;
                                    $rs=$db_model->first("select tblid from config where name='$tbl'");
                                    // echo"<pre>"; print_r($rs); echo"</br>";
                                    $childtblid=$rs->tblid;
                                    $rss=$db_model->first("select * from field where name='$field_name' and tblid='$childtblid'") or die("error7".mysql_error());
                                    // echo"<pre>"; print_r($rss); echo"</br>";
                                    if($rss)
                                    $fieldtype=$rss->type;
                                    $field_index=$rss->dbindex;
                                    $fieldid = $rss->fieldid;
                                    $i=0;                                    
                                    if($field_index == 'primary'){                                        
                                        $tb.="<div class='relativeTableCell relativeprimaryval $div_col col-xs-6 hidden-xs hidden-sm '><span data-id='$value' data-tbl='$tbl'>$value</span>";
                                        $tb.="</div>";                                        
                                    }
                                    elseif($fieldtype=="file" )
                                    {
                                        $qry_for_f_path="select upload_dir from admin where table_name='".$tbl."'";
                                        $path=$db_model->first($qry_for_f_path);
                                        if($path)
                                        {
                                            $file_path=$path->upload_dir;
                                            $qry_for_f_name="select ".$field." from ".$tbl." where ".$field."='".$value."'";
                                            $name=$db_model->first($qry_for_f_name);
                                            if($name)
                                            {
                                                $file_name=$name->$field;
                                                $file_names=explode(',',$file_name);
                                                foreach($file_names as $key => $file_name)
                                                {
                                                    $tb.="<div class='relativeTableCell $div_col col-xs-6 '><label class='hidden-md hidden-lg' style='font-weight:normal;'><strong>$alias : </strong></label><a href='".$file_name."' target='_blank' style='word-wrap: break-word;'>".$file_name."</a><br>";
                                                    $tb.="</div>";
                                                }
                                            }else{
                                                $tb.="<div class='relativeTableCell $div_col col-xs-6 '>
                                                ";
                                                $tb.="</div>";
                                            }
                                        }else{
                                            $tb.="<div class='relativeTableCell $div_col col-xs-6 '><label class='hidden-md hidden-lg' style='font-weight:normal;'><strong>$alias : </strong></label><a href='".$value."' target='_blank'>".$value."</a><br>";
                                            $tb.="</div>";
                                        }
                                    } 
                                    elseif($fieldtype=="list"){
                                        if($value){
                                            $tb.="<div class='relativeTableCell $div_col col-xs-6 '><label class='hidden-md hidden-lg' style='font-weight:normal;'><strong>$alias : </strong></label>".$utils->getFirstListAlias($fieldid,$value);
                                            $tb.="</div>";
                                        }else{
                                            $tb.="<div class='relativeTableCell $div_col col-xs-6 '><label class='hidden-md hidden-lg' style='font-weight:normal;'><strong>$alias : </strong></label>".$value;
                                            $tb.="</div>";
                                        }
                                    }
                                    elseif($fieldtype=="option" || $fieldtype=="radio"){
                                        if($value){
                                            $tb.="<div class='relativeTableCell $div_col col-xs-6 '><label class='hidden-md hidden-lg' style='font-weight:normal;'><strong>$alias : </strong></label>".$utils->getOptionAlias($fieldid,$value);
                                            $tb.="</div>";
                                        }else{
                                            $tb.="<div class='relativeTableCell $div_col col-xs-6 '><label class='hidden-md hidden-lg' style='font-weight:normal;'><strong>$alias : </strong></label>".$value;
                                            $tb.="</div>";
                                        }
                                    }
                                    elseif($fieldtype=="idate"){
                                        if($value){
                                            $tb.="<div class='relativeTableCell $div_col col-xs-6 '> <label class='hidden-md hidden-lg' style='font-weight:normal;'><strong>$alias : </strong></label>".$utils->getmydate($value);
                                            $tb.="</div>";
                                        }else{
                                            $tb.="<div class='relativeTableCell $div_col col-xs-6 '><label class='hidden-md hidden-lg' style='font-weight:normal;'><strong>$alias : </strong></label>".$value;
                                            $tb.="</div>";
                                        }
                                    }else{
                                        $tb.="<div class='relativeTableCell $div_col col-xs-6 '> <label class='hidden-md hidden-lg' style='font-weight:normal;'><strong>$alias : </strong></label>".$value;
                                        $tb.="</div>";
                                    }  
                                }
                                if(isset($related_btn['edit']) && in_array($tbl,$related_btn['edit'])){
                                    $tb.="<div class='relativeTableCell col-lg-1 col-xs-3 '><br /><button class='btn btn-success editbtn' type='button' name='edit[$x]' value='edit' data-table='$tbl'  data-ptbl='$parenttbl'  data-type='edit' data-id='$x' data-toggle='modal' data-target='#childModal'><em class='glyphicon glyphicon-pencil'></em></button>";
                                    $tb.="</div>";
                                }
                                if(isset($related_btn['delete']) && in_array($tbl,$related_btn['delete'])){
                                    $tb.="<div class='relativeTableCell col-lg-1 col-xs-3 '><br /><button class=\"btn btn-danger deletebtn\" type=\"button\" name=\"del[$x]\" value=\"delete\" data-table=\"$tbl\"  data-ptbl=\"$parenttbl\"  data-type=\"delete\" data-id=\"$x\" ><em class='glyphicon glyphicon-trash'></em></button>";
                                    $tb.="</div>";
                                }
                                $tb.="</div>";
                            }
                        }                
                    // $tb.="</div>";
                // $tb.="</div>";
            // $tb.= "</div>";
            // if($tbl!='activitylog')
            // $tb.=$btn_new;
        // $tb.="</div>";
    }
    // $tb=$tb."</div>";
    // $tb=$tb."</div>";
    // $tb=$tb."</div>";
    echo $tb;
    // return $tb;
} 
}
function displayrelated($sel,$tbl,$related_btn= array(),$default='comments')
{   
    $utils = new Utils();
    $db_model = new DbModel();
    $x=explode('=',$sel);
    if(!empty($sel)){
        $pid= $x[1];
    }else{
        $pid="";
    }
    $parentid= $pid;
    $parenttbl=$tbl;
    $formreldata=array();
    $tbc="";$k=0;
    // checking for direct relations rel_type=1
    $qqq="select id,child from formrel where parent='$parenttbl' and rel_type=1";
    $res=$db_model->allArray($qqq);
    foreach($res as $row)
    {
        $formreldata[$row['id']]=array('tbl'=>$row['child'],'pid'=>$parentid);
    }
    // Check if any refernce relations rel_type=2
    $ref_datas = $db_model->allArray("select id,ref_parent,child,parent_ref_field from formrel where parent='$parenttbl' and rel_type = 2");
    foreach($ref_datas as $ref_data){
        $cur_parent = $ref_data['ref_parent'];
        $cur_child = $ref_data['child'];
        $ref_field = $ref_data['parent_ref_field'];
        $qqq="select id,child from formrel where parent ='{$cur_parent}' and child = '{$cur_child}' ";
            $res=$db_model->allArray($qqq);
            foreach($res as $row)
            {
                if(!$sel) $sel = '1=1 limit 0,1';
                $ref_sql = $db_model->firstArray("select {$ref_field} from {$tbl} where {$sel}");
                if($ref_sql && !empty($ref_sql)){
                    $_parent_id = $ref_sql[$ref_field];
                    $formreldata[$row['id']] = array('tbl'=>$row['child'],'pid'=>$_parent_id);
                }
            }
    }

    $tb ="";
    $tb.="<div class='accordion-group'>";
        $tb.="<div class='panel-group' id='accordion2' aria-multiselectable='true'>";
            $tb.="<div class='panel' style='border:none; background-color: transparent;'>";
                $tb.="<ul class='floating-icon'>";
                $k=0;
                    foreach($formreldata as $relconf)
                    {   
                        $tbl = $relconf['tbl'];   
                        $tbl_div = ($tbl == 'invoice_payment') ? $tbl.'_div' : $tbl;
                        $tbl_alias = $utils->getTableAlias($tbl);
                        $tb.="<li class='pointer' role='$tbl'><a data-toggle='modal' data-target='#$tbl_div' aria-controls='$tbl'>  </a></li>";
                        $k++; 
                    }
                $tb.= "</ul>";
    $k=0;
    foreach($formreldata as $id => $relconf)
    {  
        $tbl = $relconf['tbl'];
        $pid = $relconf['pid'];
        $tbl_div = ($tbl == 'invoice_payment') ? $tbl.'_div' : $tbl; 
        $btn_new ="";
        $edit_flag = 0;
        $delete_flag = 0;
        if(isset($related_btn['edit']) && in_array($tbl,$related_btn['edit'])) $edit_flag = 1;
        if(isset($related_btn['edit']) && in_array($tbl,$related_btn['edit'])) $delete_flag = 1;
        if(isset($related_btn['new']) && in_array($tbl,$related_btn['new'])){
            $btn_new = "<button style='margin-top:0px !important;' class=\"btn btn-warning new addnewdata\" type=\"button\" data-editable=\"$edit_flag\" data-deletable=\"$delete_flag\" data-table=\"$tbl\" name=\"newbtn\" id=\"newbtn\" value=\"new\" data-type=\"new\" data-toggle=\"modal\" data-target=\"#childModal\"/><em class='glyphicon glyphicon-plus-sign'></em></button>";
        }
        $k++;
        $tbl_alias = $utils->getTableAlias($tbl);
        $tb.="<div  class='tab-col-md-12 modal  collapse fade tabpane' id='$tbl_div' style='overflow-y: hidden !important;'>";
            $tb.="<div class='modal-header childmodalheader'>
                    <button type='button' class='close relativeModalclose' data-dismiss='modal'>&times;</button>
                    <h4 class='modal-title' style='color:black;'>".$tbl_alias.' '.$btn_new."</h4>
                </div>";                 
            $tb.= "<div class='row childmodalbody realtedForm' id='' style='margin-top: 5px; margin-bottom: 5px; overflow-y: scroll; max-height:75%;'>"; 
                $tb.="<div class='relativeTable'>"; 
                    $tb.="<div class='relativeTableHeading'>";
                        $q="select showfields from workflow where formname='$tbl'";
                        $res=$db_model->first($q);
                        $show=array();
                        if($res)
                        {
                            $fieldstring=$res->showfields;
                            $show=explode(',',$fieldstring);
                            array_unshift($show,"id");
                        }
                        else
                        {
                            $columnnamequery=" select column_name from information_schema.columns where table_name='$tbl'";
                            $res=$db_model->allArray($columnnamequery);
                            foreach($res as $row)
                            {
                                array_push($show,$row['column_name']);
                            }
                        }
                        $count="";
                        foreach($show as $col)
                        {
                            $rs=$db_model->first("select alias from field where name='$col'");
                            if($rs)
                            {
                                $alias=$rs->alias;
                            }
                            else
                            {
                                $alias=$col;
                            }
                            $cnt                =   count(array_keys($show));
                            $col                =   floor(10/$cnt);
                            if($cnt       <=  5){
                                $div_col    =   'col-lg-'.$col;
                            }else if($cnt   ==  6){
                                if((isset($related_btn['edit']) && in_array($tbl,$related_btn['edit'])) || (isset($related_btn['delete']) && in_array($tbl,$related_btn['delete']))){
                                    $div_col    =   'col-lg-1';
                                }else{
                                    $div_col    =   'col-lg-2';
                                }
                            }else{
                                $div_col    =   'col-lg-1';
                            }
                            $tb.="<div class='divTableHead $div_col hidden-xs hidden-sm' style='font-weight:normal;'>$alias</div>";
                        }                        
                    $tb.="</div>";
                    $tb.="<div class='relativeTableBody modalTableBody$tbl' style='overflow-y: scroll;'>";
                        $q="select showfields from workflow where formname='$tbl'";
                        $res=$db_model->first($q);        
                        if($res)
                        {
                            $fieldstring=$res->showfields;
                            $ff=explode(",",$fieldstring);
                            if(!in_array("id",$ff))
                            {
                                array_unshift($ff,"id");
                            }
                            $fieldstring=implode(",",$ff);
                            $qual="1=1";
                            $data_sql="select $fieldstring from ".$tbl." where $qual and  id in (select childrecid from reldata where relid=".$id." and  parentrecid=".$pid.") order by id desc";
                        }
                        else
                        {
                            $data_sql="select * from $tbl where id in (select childrecid from reldata where relid=".$id." and  parentrecid=".$pid.") order by id desc";
                        }

                        $res=$db_model->allArray($data_sql);
                        if(!empty($res))
                        {
                            // $cnt="";
                            foreach($res as $row)
                            {
                                $cnt                =   count(array_keys($row));
                                $col                =   floor(10/$cnt);
                                if($cnt       <=  5){
                                    $div_col    =   'col-lg-'.$col;
                                }else if($cnt   ==  6){
                                    if((isset($related_btn['edit']) && in_array($tbl,$related_btn['edit'])) || (isset($related_btn['delete']) && in_array($tbl,$related_btn['delete']))){
                                        $div_col    =   'col-lg-1';
                                    }else{
                                        $div_col    =   'col-lg-2';
                                    }
                                }else{
                                    $div_col    =   'col-lg-1';
                                }
                                $tb.="<div class='row relativeTableRow responsiveTableRow'> ";
                                foreach($row as $field => $value)
                                { 
                                    $rs=$db_model->first("select alias from field where name='$field'");
                                    if($rs)
                                    {
                                        $alias=$rs->alias;
                                    }
                                    else{
                                        $alias=$col;
                                    }
                                    if($field=="id")                                    
                                    $x=$value;
                                    $field_name=$field;
                                    $rs=$db_model->first("select tblid from config where name='$tbl'");
                                    $childtblid=$rs->tblid;
                                    $rss=$db_model->first("select * from field where name='$field_name' and tblid='$childtblid'") or die("error7".mysql_error());
                                    if($rss)
                                    $fieldtype=$rss->type;
                                    $field_index=$rss->dbindex;
                                    $fieldid = $rss->fieldid;
                                    $i=0;                                    
                                    if($field_index == 'primary'){                                        
                                        $tb.="<div class='relativeTableCell relativeprimaryval $div_col col-xs-6 hidden-xs hidden-sm '><span data-id='$value' data-tbl='$tbl'>$value</span>";
                                        $tb.="</div>";                                        
                                    }
                                    elseif($fieldtype=="file" )
                                    {
                                        $qry_for_f_path="select upload_dir from admin where table_name='".$tbl."'";
                                        $path=$db_model->first($qry_for_f_path);
                                        if($path)
                                        {
                                            $file_path=$path->upload_dir;
                                            $qry_for_f_name="select ".$field." from ".$tbl." where ".$field."='".$value."'";
                                            $name=$db_model->first($qry_for_f_name);
                                            if($name)
                                            {
                                                $file_name=$name->$field;
                                                $file_names=explode(',',$file_name);
                                                foreach($file_names as $key => $file_name)
                                                {
                                                    $tb.="<div class='relativeTableCell $div_col col-xs-6 '><label class='hidden-md hidden-lg' style='font-weight:normal;'><strong>$alias : </strong></label><a href='".$file_name."' target='_blank' style='word-wrap: break-word;'>".$file_name."</a><br>";
                                                    $tb.="</div>";
                                                }
                                            }else{
                                                $tb.="<div class='relativeTableCell $div_col col-xs-6 '>
                                                ";
                                                $tb.="</div>";
                                            }
                                        }else{
                                            $tb.="<div class='relativeTableCell $div_col col-xs-6 '><label class='hidden-md hidden-lg' style='font-weight:normal;'><strong>$alias : </strong></label><a href='".$value."' target='_blank'>".$value."</a><br>";
                                            $tb.="</div>";
                                        }
                                    } 
                                    elseif($fieldtype=="list"){
                                        if($value){
                                            $tb.="<div class='relativeTableCell $div_col col-xs-6 '><label class='hidden-md hidden-lg' style='font-weight:normal;'><strong>$alias : </strong></label>".$utils->getFirstListAlias($fieldid,$value);
                                            $tb.="</div>";
                                        }else{
                                            $tb.="<div class='relativeTableCell $div_col col-xs-6 '><label class='hidden-md hidden-lg' style='font-weight:normal;'><strong>$alias : </strong></label>".$value;
                                            $tb.="</div>";
                                        }
                                    }
                                    elseif($fieldtype=="option" || $fieldtype=="radio"){
                                        if($value){
                                            $tb.="<div class='relativeTableCell $div_col col-xs-6 '><label class='hidden-md hidden-lg' style='font-weight:normal;'><strong>$alias : </strong></label>".$utils->getOptionAlias($fieldid,$value);
                                            $tb.="</div>";
                                        }else{
                                            $tb.="<div class='relativeTableCell $div_col col-xs-6 '><label class='hidden-md hidden-lg' style='font-weight:normal;'><strong>$alias : </strong></label>".$value;
                                            $tb.="</div>";
                                        }
                                    }
                                    elseif($fieldtype=="idate"){
                                        if($value){
                                            $tb.="<div class='relativeTableCell $div_col col-xs-6 '> <label class='hidden-md hidden-lg' style='font-weight:normal;'><strong>$alias : </strong></label>".$utils->getmydate($value);
                                            $tb.="</div>";
                                        }else{
                                            $tb.="<div class='relativeTableCell $div_col col-xs-6 '><label class='hidden-md hidden-lg' style='font-weight:normal;'><strong>$alias : </strong></label>".$value;
                                            $tb.="</div>";
                                        }
                                    }else{
                                        $tb.="<div class='relativeTableCell $div_col col-xs-6 '> <label class='hidden-md hidden-lg' style='font-weight:normal;'><strong>$alias : </strong></label>".$value;
                                        $tb.="</div>";
                                    }  
                                }
                                if(isset($related_btn['edit']) && in_array($tbl,$related_btn['edit'])){
                                    $tb.="<div class='relativeTableCell col-lg-1 col-xs-3 '><br /><button class='btn btn-success editbtn' type='button' name='edit[$x]' value='edit' data-table='$tbl'  data-ptbl='$parenttbl'  data-type='edit' data-id='$x' data-toggle='modal' data-target='#childModal'><em class='glyphicon glyphicon-pencil'></em></button>";
                                    $tb.="</div>";
                                }
                                if(isset($related_btn['delete']) && in_array($tbl,$related_btn['delete'])){
                                    $tb.="<div class='relativeTableCell col-lg-1 col-xs-3 '><br /><button class=\"btn btn-danger deletebtn\" type=\"button\" name=\"del[$x]\" value=\"delete\" data-table=\"$tbl\"  data-ptbl=\"$parenttbl\"  data-type=\"delete\" data-id=\"$x\" ><em class='glyphicon glyphicon-trash'></em></button>";
                                    $tb.="</div>";
                                }
                                $tb.="</div>";
                            }
                        }                
                    $tb.="</div>";
                $tb.="</div>";
            $tb.= "</div>";
            if($tbl!='activitylog')
            // $tb.=$btn_new;
        $tb.="</div>";
    }
    $tb=$tb."</div>";
    $tb=$tb."</div>";
    $tb=$tb."</div>";
    return $tb;
}