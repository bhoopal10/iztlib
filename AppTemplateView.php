<?php
// require_once('lib/triggers.php');
libxml_use_internal_errors(true);
require_once('lib/relatedforms.php');
require_once('AppController.php');
require_once('UiConstant.php');
require_once('Utils.php');
require_once('AppProperties.php');
class AppTemplateView extends CoreView{
 public $ui;
public $appC;
public $utils;
public $properties;

function __construct(){
	parent::__construct();
    $this->ui = new UiConstant();
    $this->appC = new AppController();
    $this->utils = new Utils();
    $this->properties = new AppProperties();
}

function get_template($tbl,$view_name=""){
    $templat=$tbl;
	$sql_templ="Select content from form_templates where tname='$templat' AND view_name='$view_name'";
	// echo $sql_templ;
    $result_templ=$this->allArray($sql_templ);
    $a='';
	// while($row=mysql_fetch_array($result_templ))
	foreach($result_templ as $row)
    {        $a=$row['content'];
    } 
    return $a;
    }
function setTemplate($tbl,$field_name,$x,$a){
    $a=$this->get_template($tbl);
    $a=str_replace("{{".$tbl.".".$field_name."}}",$x,$a);
    return $a; 
}

function addrow_template($tbl,$arr,$arr_show,$defaults,$mode=1)
{
	$ui = $this->ui;
	$utils = $this->utils;
	$sql1=$this->dbsql($tbl);
	$arr_default=$defaults;

	$data_sql="select * from ".$tbl;

	//DB SQLs
	$sql="select tblid from config where name='$tbl'";
	// echo $sql;
	// $result=mysql_query($sql);
	$result=$this->firstArray($sql);
	$myvar=$result['tblid'];
	$sql1="select * from field where tblid=$myvar";
	// $result1=mysql_query($sql1);
	$result1=$this->allArray($sql1);
    $fname="";
    $fval="";
	$a="";
	$opt=array(1 => "k");
	//$mode=1;
	$j=0;

        //mode is horizontal row style
           
	//Open Table
	//echo $ui->tg_top;
	//Print Header column
	// hide system generated fields
	$nfield=0;
	$split=0;
	
	//echo $ui->tg_col;
	
	$c=$ui->tg_col;
	if($mode==3)
	$d=$ui->tg_div_cl.$ui->tg_row.$ui->tg_col1.$ui->tg_div_cl.$ui->tg_span;	
	$cnt=0;
    // while($row = mysql_fetch_array($result1))
    $a=$this->get_template($tbl);
	foreach($result1 as $row)
	{
		$tg_ip_gp = 'tg_ip_gp';
		$sys_fields = array("created_at","modified_at","created_by","modified_by");

		
		//Print data columns
		
		$j=1;

		if(empty($arr_show) || in_array($row['name'],$arr_show))
		{		
			if($row['dbindex']=='primary' || !in_array($row['name'], $arr))
			{
				// var_dump($arr);

				// $x=$ui->tg_ip.$ui->tg_ip_type."hidden".$ui->tg_readonly.$ui->tg_ip_class.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
                $x=$ui->tg_ip.$ui->tg_ip_type.$row['type'].$ui->tg_ip_class.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
						
						if(isset($arr_default[$row['name']]))
						$x=$x.$arr_default[$row['name']];
                        
                        $x=$x."\" readonly \"".$ui->tg_ip_cl;
			
						// $x=$x.$ui->tg_static.$ui->tg_static_cl.$ui->tg_div_cl.$ui->tg_div_cl;
						// $x=$ui->tg_ip.$ui->tg_ip_type.$row['type'].$ui->tg_ip_class.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
						// if($row['type']=="idate")
						// 	{
						// 		//  echo getmydate($datarow[$row['name']]);
						// 			$x=$x.$utils->getmydate($datarow[$row['name']]);
						// 			// $x=$x.$ui->tg_ip_id.$row['name'].$cnt.$ui->tg_ip_class.$ui->tg_class;
						// 			// $x=$x.$ui->tg_ip_cl.$ui->tg_dat.$row['name'].$cnt.$ui->tg_dat_cl.$ui->tg_div_cl;
						// 			$x=$x."\" readonly \"".$ui->tg_ip_cl;
						// 	}
						
					}
					elseif($row['type']=="textarea")
					{
							$x=$ui->tg_text.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_ip_cl;
							$x=$x.$ui->tg_text_cl;
						}	
						elseif($row['type']=="ihtml")
						{
								$x=$ui->tg_text.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_ip_cl;
								$x=$x.$row['name'].$ui->tg_text_cl;
						} 
						elseif($row['type']=="option")
						{					$x=$ui->tg_sel_control.$row['name'].$cnt.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_cl;
							//$a=$a.$ui->tg_opt.$datarow[$row['name']].$ui->tg_cl.$datarow[$row['name']].$ui->tg_opt_cl;
							//if($cnt==0)
										//{
											$sql_opt="select * from field_option where tblid=".$row['tblid']." and fieldid=".$row['fieldid'];
											// $result_opt=mysql_query($sql_opt);
											$result_opt=$this->allArray($sql_opt);
											$opt[$j]="";
											// while($row_opt = mysql_fetch_array($result_opt))
										
											foreach($result_opt as $row_opt)
												{
		
													if(isset($arr_default[$row['name']]))
													{
													if($row_opt['value']==$arr_default[$row['name']]) 
													{//$alias=$row_opt['alias'];
													$x=$x.$ui->tg_opt.$row_opt['value'].$ui->tg_cl.$row_opt['alias'].$ui->tg_opt_cl;
													}	
												}else
													{$opt[$j]=$opt[$j].$ui->tg_opt.$row_opt['value'].$ui->tg_cl.$row_opt['alias'].$ui->tg_opt_cl;
													}

													
												}
										//}
										//below handles code if no value is set and also instead of value shows alias
										$alias=isset($alias)?$alias:NULL;
										// $x=$x.$ui->tg_opt.$ui->tg_cl.$alias.$ui->tg_opt_cl; // disabled of defult value
										//introducing for clear value option
										// $x=$x.$ui->tg_opt.$ui->tg_cl.$ui->tg_opt_cl; // remove null value and added empty for select
										if(!isset($arr_default[$row['name']]))
													{
										$x=$x.$ui->tg_opt." ".$ui->tg_cl.'-- Select --'.$ui->tg_opt_cl;
													}

										$x=$x.$opt[$j].$ui->tg_sel_cl;
										$j++;
						}
						elseif($row['type']=="file"){

						$path = $this->get_file_path($tbl).$row['name'];
						$x=$ui->tg_ip.$ui->tg_ip_type.$row['type'].$ui->tg_ip_class.$ui->tg_ip_name.$row['name'].$cnt.'[]'.$ui->tg_ip_size.$row['size'].$ui->tg_ip_value.$ui->tg_ip_cl;
					}
					elseif($row['type']=="list")
					{
								$x=$ui->tg_sel_control.$row['name'].$cnt.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_cl;
											//adding new on13/12/2014.can be deleted
											$sql_filter="select source,filter,id,value,alias from valuelist where id in (select optid from field_option where tblid=".$row['tblid']." and fieldid=".$row['fieldid'].")";
											$result_filter=$this->firstArray($sql_filter);
											if($result_filter && !empty($result_filter)){
												$vsource= $result_filter['source'];
												$vfilter= $result_filter['filter'];
												$vid= $result_filter['id'];
												$vvalue= $result_filter['value'];
												$valias= $result_filter['alias'];
												$vfilter= $vfilter==null?" where 1=2 ":" where ".$utils->parseFilter($vfilter);
												$sql_opt="select ".$vvalue." as value,".$valias." as alias from ".$vsource.$vfilter;
												//echo $sql_opt;	
												//Completion of change
												// $result_opt=mysql_query($sql_opt);
												$result_opt=$this->allArray($sql_opt);
												$opt[$j]="";
												$alias='';
												if($result_opt)
												{
												// while($row_opt = mysql_fetch_array($result_opt))
												foreach($result_opt as $row_opt)
													{
													
														if(isset($arr_default[$row['name']]) && ($row_opt['value']==$arr_default[$row['name']]))
														{
														//$alias=$row_opt['alias'];
														$x=$x.$ui->tg_opt.$row_opt['value'].$ui->tg_cl.$row_opt['alias'].$ui->tg_opt_cl;
													
													}else
														{$opt[$j]=$opt[$j].$ui->tg_opt.$row_opt['value'].$ui->tg_cl.$row_opt['alias'].$ui->tg_opt_cl;
														}
													}
												}
											}
										//}
										//below handles code if no value is set and also instead of value shows alias
										$alias=(isset($alias)&&($alias!=null))?$alias:"";
										// echo "alias is".$alias;
										if(!isset($arr_default[$row['name']]))
													{
										//$x=$x.$ui->tg_opt.$ui->tg_cl.$alias.$ui->tg_opt_cl : '';
										//introducing for clear value option
											}
										$x=$x.$ui->tg_opt."\"\"".$ui->tg_cl."--NONE--".$ui->tg_opt_cl;
										$x=$x.$opt[$j].$ui->tg_sel_cl;
					$j++;
					}			
		else
		{

		$x=$ui->tg_ip.$ui->tg_ip_type.$row['type'].$ui->tg_ip_class.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
							if($row['type']=="idate")
							{
								if(isset($arr_default[$row['name']]))
								{$x=$x.$arr_default[$row['name']];}
							$x=$x.$ui->tg_ip_id.$row['name'].$cnt.$ui->tg_ip_class.$ui->tg_class;
							$x=$x.$ui->tg_ip_cl.$ui->tg_dat.$row['name'].$cnt.$ui->tg_dat_cl;
							}
							else
							{
								if(isset($arr_default[$row['name']]))
						{$x=$x.$arr_default[$row['name']];}
								$x=$x.$ui->tg_ip_cl;}
			}
		
        
            $a=str_replace("{{".$tbl.".".$row['name']."}}",$x,$a);
            $x='';  
		}
		
		
		
		}$cnt++;
			//Close Table
		//echo $ui->tg_top_cl;
			$a=$a."<input type=\"number\" name=\"icnt\" value=$cnt style=\"display:none\">";
		$a=$a."<input type=\"hidden\" name=\"field_edit\" value=\"".implode(",",$arr)."\" >";
	    echo $a; 
        
$fname=chop($fname,",");
$fval=chop($fval,",");
$sqli = "INSERT INTO ".$tbl." ( ".$fname." ) VALUES (".$fval.")";
 
//$sqla= "Alter table 
echo "<input class=\"btn btn-primary\" id=\"btn\" type=\"submit\" name=\"addrow\" value=\"Add Row\">";
//echo "<input class=\"btn btn-warning\" id=\"btn\" type=\"submit\" name=\"modify\" value=\"modify\">";
return $sqli;
}
function vtbView($data,$status,$tbl,$status_field='status',$parent_tbl = 'pipeline'){
	$lists = array();
	$utils = $this->utils;
	$stat_array = array();
	// harcode for if pipeline the alias and if tasks then value
	$val = ($tbl == 'tasks') ? 'value': 'alias';
	if(!empty($status)){
		foreach($status as $stat){
			$stat_array[strtolower($stat->value)] = $stat->alias;
			foreach($data as $newdata){
				$trimed_value = trim($newdata[$status_field]);

				if(strtolower($stat->$val) == strtolower($trimed_value)){
					if(isset($lists[$trimed_value])){
						array_push($lists[$trimed_value],$newdata);
					}else{
						$lists[$trimed_value] = array($newdata);
					}	
				}
			}
		}
	}
	$all = $utils->listCheckbox($stat_array,$status_field);
	$check_boxes  = $all['check_boxes'];
	$radio  = $all['radio'];
	// listing data
	$output = "<div class='dummy-div'><div class='dummy-inner-div'></div></div><div class='vtb-scroll'><ul id='vtb-list'>";
	foreach($lists as $key=>$list){
		if($tbl != 'tasks'){
			$main_key = array_search($key,$stat_array,true);
			$key = $main_key;
		}
		$h2 = '<h4 class="text-center taskstatusbar"><strong>'.$stat_array[$key].' <small class="text-black pad-5 pull-right">('.count($list).')</small></strong></h4>';
		if(array_search($key,$radio) === false) $style = ' style="display:none;" ';
		else $style ='';
		$output .= "<li class='tabpane tab_status_{$key}' {$style}>{$h2}";
		if($tbl == 'tasks'){

			foreach($list as $innerlist){
				// status label
				$t_date = date('Y-m-d 00:00:00',strtotime($innerlist['task_date']));
				$c_time = date('Y-m-d 00:00:00');
				$words = explode(' ', $innerlist['task_assigned_to']);
				$f = isset($words[0][0]) ? $words[0][0] : '';
				$s = isset($words[1][0]) ? $words[1][0] : '';
				$badge = $f.$s;
				$first_date = new DateTime($c_time);
				$second_date = new DateTime($t_date);
				$difference = $first_date->diff($second_date);
				$days = $difference->format("%R%a");
				$cls = 'label label-danger';
				if($days >= 0) $cls = 'label label-info';
				if($days > 2 ) $cls = '';
				if(in_array($stat_array[$key],array('Completed','Archive'))) $cls = '';
				$output .= "<div class='tails' >";
				$output .= '<span style="color:#0c8c87;"><strong>';
				$output .= "<a >";
				$output .= $innerlist['description'];
				$output .= "</a>";
				$output .= "</strong></span><span class='pull-right'>";
				$output .= "<a title='Edit' class='editbtn' href='#' data-id='{$innerlist['id']}' data-table='{$tbl}' data-type='edit' data-toggle='modal' data-target='#childModal' data-view='no'><i class='glyphicon glyphicon-pencil'></i></a></span><br>";
				isset($innerlist['opp_name'])? $innerlist['opp_name'] ."<br>":"";
				$output .= '<strong>Task Due: </strong><span class="'.$cls.'">'.$innerlist['task_date']." </span><div class='member pull-right'> <span class=' member-initials' title='".$innerlist['task_assigned_to']."'>{$badge}</span></div><br>";
				$output .= "</div>";
			}
		}else{
			foreach($list as $innerlist){
				$t_date = date('Y-m-d 00:00:00',strtotime($innerlist['next_action_date']));
				$c_time = date('Y-m-d 00:00:00');
				$first_date = new DateTime($c_time);
				$second_date = new DateTime($t_date);
				$difference = $first_date->diff($second_date);
				$days = $difference->format("%R%a");
				$modified = '<div class="col-md-4 col-xs-4"><small class="pull-right pad-t-5">'.$utils->time_elapsed_string($innerlist['modified_at']).'</small></div>';
				$cls = 'label label-danger';
				if($days >= 0)$cls = 'label label-info';
				if($days > 2 ) $cls = '';
				// $output .= "<div class='tails editbtn' data-id='{$innerlist['id']}' data-table='{$tbl}' data-type='edit' data-toggle='modal' data-target='#childModal' data-view='no'>";
				$output .= "<div class='tails '>";
				$task = '<div class="col-md-2 col-xs-2 text-right"><a title="Tasks" href="#" data-toggle="modal" data-reload="true" data-target="#tasks" aria-controls="tasks" data-table="'.$tbl.'" data-id="'.$innerlist['id'].'" class="tasksdata pull-right" id="tasks'.$innerlist['id'].'" style="padding-left: 10px;">
				<span class="glyphicon glyphicon-tasks"></span>
				</a></div>';
				$comments = '<div class="col-md-2 col-xs-2 text-right"><a title="Comments" href="#" data-toggle="modal" data-target="#comments" aria-controls="comments" data-table="'.$tbl.'" data-id="'.$innerlist['id'].'" class="commentsdata pull-right" id="comments'.$innerlist['id'].'" >
				<span class="glyphicon glyphicon-comment"></span>
				</a></div>';
				$related = '<div class="col-md-2 col-xs-2 text-right"><span data-related="yes"><a title="Related Tables" href="#"  data-table="'.$tbl.'" data-id="'.$innerlist['id'].'" class="pull-right related-btn dropdown-toggle" data-toggle="dropdown">
				<span class="glyphicon fa fa-ellipsis-v"></span>
				</a></span></div>';
				$pencil = "<div class='col-md-2 col-xs-2 text-right'><a  title='Edit' href ='#' class='editbtn' data-id='{$innerlist['id']}' data-table='{$tbl}' data-type='edit' data-toggle='modal' data-target='#childModal' data-view='no' data-related='no' data-template='yes'>
				<span class='glyphicon glyphicon-pencil'></span>
				</a></div>";
				$words = explode(' ', $innerlist['assigned_to']);
				$f = isset($words[0][0]) ? $words[0][0] : '';
				$s = isset($words[1][0]) ? $words[1][0] : '';
				$badge = $f.$s;
				$assigned_to = "<div class='col-md-2 col-xs-2'><div class='member'> <span class='member-initials' title='".$innerlist['assigned_to']."'>{$badge}</span></div></div>";
				$check_list = $utils->getTasksRecords($tbl,'tasks',$innerlist['id'],'childrecid','1,2,3','2');
				if($check_list) $tasks = "<span class=''><i class='fa fa-check-square-o'></i>{$check_list}</span>&nbsp;";
				else $tasks = "";
				$tasks ="<div class='col-md-4 col-xs-4'>{$tasks}</div>";
				$img_src = $utils->getAccountImg($innerlist['accname_value']);
				$img_tag = "<div class='col-md-5 col-xs-5'><img src='{$img_src}' alt='Image' width='70' height='20' /></div>";
				$value = '<div class="col-md-3 col-xs-3 p0 text-with-dots" title="'.$innerlist['value'].'"><i class="fa fa-inr"></i>'.$innerlist['value'].'</div>';
				$engage = '<div class="col-md-2 col-xs-2">'.$innerlist['engagement'].'</div>';
				$output .= '<div class="row">'.$img_tag.$value.$engage.$pencil.'</div>';
				$output .= '<div class="row pad-t-5"><div class="col-md-10 col-xs-10 text-with-dots"><span title="'.$innerlist['description'].'"><strong>'.$innerlist['description'].'</strong></span></div>'.$task.'</div>';
				$output .= '<div class="row pad-t-5"><div class="col-md-6 col-xs-6">By: '.$innerlist['expected_closure'].'</div><div class="col-md-4 col-xs-4"><span class="'.$cls.'">'.$innerlist['next_action_date'].'</span></div>'.$comments.'</div>';
				$output .= "<div class='row pad-t-5'>".$assigned_to.$tasks.$modified.$related."</div>";
				$output .="<div class='clearfix'></div></div>";
			}
		}
		$output .="</li>";
	}
	$output .="</ul></div>";
	return array('output'=>$output,'checkbox'=>$check_boxes);
}
function getTextArea($row,$cnt,$field_value=NULL,$related=false){
	$ui = $this->ui;
    $x=$ui->tg_text.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_cl;
    $x=$x.$field_value.$ui->tg_text_cl;
    return $x;
}
function getRadioBtn($row,$cnt,$fielddata=NULL,$related=NULL){
	$ui = $this->ui;
	$fieldid = $row['fieldid'];
	$lists = $this->utils->getOptionValueAlias($fieldid);
	$x = "<div class='radio'>";
	if(!empty($lists)){
		foreach($lists as $list){
			if($list['value'] == $fielddata) $checked = "\" checked=\"checked";
			else $checked = "";
			$x .='<label class="radio-inline">'.$ui->tg_ip.$ui->tg_ip_type."radio".$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_ip_value.$list['value'].$checked.$ui->tg_ip_cl.$list['alias'].'</label>';
		}
	}
	$x .="</div>";
    return $x;
}
function getReadOnlyList($row,$cnt,$j,$fielddata=NULL,$related=false){
    // $ui = new UiConstant();
	//echo 'fileddata'.$fielddata;
	$utils = $this->utils;
	$ui = $this->ui;
	$x=$ui->tg_ip.$ui->tg_ip_type.'hidden'.$ui->tg_ip_class.$ui->tg_ip_name.$related.$row['name'].$cnt.$ui->tg_ip_value.$fielddata.$ui->tg_ip_cl;
    $x .=$ui->tg_sel_control.$row['name'].$cnt.$ui->tg_ip_name.$row['name'].$cnt."\" disabled=\"true ".$ui->tg_cl;
    						//adding new on13/12/2014.can be deleted
    						$sql_filter="select source,filter,id,value,alias from valuelist where id in (select optid from field_option where tblid=".$row['tblid']." and fieldid=".$row['fieldid'].")";
    						// echo $sql_filter;
    						$result_filter=$this->firstArray($sql_filter);
    						$vsource= $result_filter['source'];
    						$vfilter= $result_filter['filter'];
    						$vid= $result_filter['id'];
    						$vvalue= $result_filter['value'];
    						$valias= $result_filter['alias'];
    						$vfilter= $vfilter==null?" where 1=2 ":" where ".$utils->parseFilter($vfilter);
    						$sql_opt="select ".$vvalue." as value,".$valias." as alias from ".$vsource.$vfilter;
    						//echo $sql_opt;	
    						//Completion of change
    						// $result_opt=mysql_query($sql_opt);
    						$result_opt=$this->allArray($sql_opt);
    						$opt[$j]="";
    						$alias='';
    						if($result_opt)
    						{
    						// while($row_opt = mysql_fetch_array($result_opt))
    						foreach($result_opt as $row_opt)
    							{
    							$opt[$j]=$opt[$j].$ui->tg_opt.$row_opt['value'].$ui->tg_cl.$row_opt['alias'].$ui->tg_opt_cl;
    							if($row_opt['value']==$fielddata) 
    								{$alias=$row_opt['alias'];}
    							}
    						}
    					//}
    					//below handles code if no value is set and also instead of value shows alias
    					$alias=(isset($alias)&&($alias!=null))?$alias:"--NONE--";
    					$x=$x.$ui->tg_opt.$fielddata.$ui->tg_cl.$alias.$ui->tg_opt_cl;
    					//introducing for NONE value option
    					$x=$x.$ui->tg_opt."".$ui->tg_cl."--NONE--".$ui->tg_opt_cl;
    					$x=$x.$opt[$j].$ui->tg_sel_cl;
    // $j++;
    return $x;
}
function getListField($row,$cnt,$j,$fielddata=NULL,$related){
    // $ui = new UiConstant();
    //echo 'fileddata'.$fielddata;
	$ui = $this->ui;
	$utils = $this->utils;
    $x=$ui->tg_sel_control.$row['name'].$cnt.$ui->tg_ip_name.$related.$row['name'].$cnt.$ui->tg_cl;
    						//adding new on13/12/2014.can be deleted
    						$sql_filter="select source,filter,id,value,alias from valuelist where id in (select optid from field_option where tblid=".$row['tblid']." and fieldid=".$row['fieldid'].")";
    						// echo $sql_filter;
							$result_filter=$this->firstArray($sql_filter);
							if($result_filter && !empty($result_filter)){

								$vsource= $result_filter['source'];
								$vfilter= $result_filter['filter'];
								$vid= $result_filter['id'];
								$vvalue= $result_filter['value'];
								$valias= $result_filter['alias'];
								$vfilter=$vfilter==null?" where 1=2 ":" where ".$utils->parseFilter($vfilter);
								$sql_opt="select ".$vvalue." as value,".$valias." as alias from ".$vsource.$vfilter;
								// echo $sql_opt;	
								//Completion of change
								// $result_opt=mysql_query($sql_opt);
								$result_opt=$this->allArray($sql_opt);
								$opt[$j]="";
								$alias='';
								if($result_opt)
								{
									// while($row_opt = mysql_fetch_array($result_opt))
									foreach($result_opt as $row_opt)
									{
										// if(count($result_opt)	$opt[$j]=$opt[$j].$ui->tg_opt.$row_opt['value']."\" selected \"".$ui->tg_cl.$row_opt['alias'].$ui->tg_opt_cl;
										$opt[$j]=$opt[$j].$ui->tg_opt.$row_opt['value'].$ui->tg_cl.$row_opt['alias'].$ui->tg_opt_cl;
										if($row_opt['value']==$fielddata) 
										{$alias=$row_opt['alias'];}
									}
								}
								//}
								//below handles code if no value is set and also instead of value shows alias
								$alias=(isset($alias)&&($alias!=null))?$alias:"--NONE--";
								$x=$x.$ui->tg_opt.$fielddata.$ui->tg_cl.$alias.$ui->tg_opt_cl;
								//introducing for clear value option
								// $x=$x.$ui->tg_opt."".$ui->tg_cl."--NONE--".$ui->tg_opt_cl;
								$x=$x.$opt[$j].$ui->tg_sel_cl;
							}
    // $j++;
    return $x;
}
function getReadOnlyOption($row,$cnt,$j,$fielddata=NULL,$related=false){
	$ui = $this->ui;
	$x=$ui->tg_ip.$ui->tg_ip_type.'hidden'.$ui->tg_ip_class.$ui->tg_ip_name.$related.$row['name'].$cnt.$ui->tg_ip_value.$fielddata.$ui->tg_ip_cl;
	
    $x .=$ui->tg_sel_control.$row['name'].$cnt.$ui->tg_ip_name.$row['name'].$cnt."\" disabled=\"true ".$ui->tg_cl;
    //$a=$a.$ui->tg_opt.$datarow[$row['name']].$ui->tg_cl.$datarow[$row['name']].$ui->tg_opt_cl;
    //if($cnt==0)
    //{
        $sql_opt="select * from field_option where tblid=".$row['tblid']." and fieldid=".$row['fieldid'];
        // $result_opt=mysql_query($sql_opt);
        $result_opt=$this->allArray($sql_opt);
        $opt[$j]="";
        // while($row_opt = mysql_fetch_array($result_opt))
        foreach($result_opt as $row_opt)
            {
            $opt[$j]=$opt[$j].$ui->tg_opt.$row_opt['value'].$ui->tg_cl.$row_opt['alias'].$ui->tg_opt_cl;
            if($row_opt['value']==$fielddata) 
                {$alias='';$alias=$row_opt['alias'];}
            }
    //}
    //below handles code if no value is set and also instead of value shows alias
    $alias=isset($alias)?$alias:NULL;
        $x=$x.$ui->tg_opt.$fielddata.$ui->tg_cl.$alias.$ui->tg_opt_cl;
            
    //introducing for clear value option
     $x=$x.$ui->tg_opt.$fielddata.$ui->tg_cl.$ui->tg_opt_cl;
     $x=$x.$opt[$j].$ui->tg_sel_cl;
return $x;
}
function getOptionField($row,$cnt,$j,$fielddata=NULL,$related=false){
     $ui = $this->ui;
    $x=$ui->tg_sel_control.$row['name'].$cnt.$ui->tg_ip_name.$related.$row['name'].$cnt.$ui->tg_cl;
    //$a=$a.$ui->tg_opt.$datarow[$row['name']].$ui->tg_cl.$datarow[$row['name']].$ui->tg_opt_cl;
    //if($cnt==0)
    //{
        $sql_opt="select * from field_option where tblid=".$row['tblid']." and fieldid=".$row['fieldid'];
        // $result_opt=mysql_query($sql_opt);
        $result_opt=$this->allArray($sql_opt);
        $opt[$j]="";
        // while($row_opt = mysql_fetch_array($result_opt))
        foreach($result_opt as $row_opt)
            {
            $opt[$j]=$opt[$j].$ui->tg_opt.$row_opt['value'].$ui->tg_cl.$row_opt['alias'].$ui->tg_opt_cl;
            if($row_opt['value']==$fielddata) 
                {$alias='';$alias=$row_opt['alias'];}
            }
    //}
    //introducing for clear value option
	// $x=$x.$ui->tg_opt."\">".'--NONE--'.$ui->tg_opt_cl;
    //below handles code if no value is set and also instead of value shows alias
    $alias=isset($alias)?$alias:NULL;
    if($alias) $x=$x.$ui->tg_opt.$fielddata.$ui->tg_cl.$alias.$ui->tg_opt_cl;
            
     $x=$x.$opt[$j].$ui->tg_sel_cl;
return $x;
}

function getHtml($row,$cnt,$fielddata=NULL,$related=false){
	$ui= $this->ui;
	$x=$ui->tg_text.$ui->tg_ip_name.$row['name'].$cnt.'">';
    $x=$x.$fielddata.$ui->tg_text_cl;
    return $x;
}

function getFileField($tbl,$row,$cnt,$recid,$fielddata=NULL,$related=false){
    //$appC = new AppController();
    $appC = $this->appC;
    $ui= $this->ui;
    // $tbl= $tbl;
    $path = $appC->get_file_path($tbl).$row['name'];
			$x=$ui->tg_ip.$ui->tg_ip_type.$row['type'].$ui->tg_ip_class.$ui->tg_ip_name.$related.$row['name'].$cnt.'[]'.$ui->tg_data_name.$row['name'].$cnt.$ui->tg_ip_size.$row['size'].$ui->tg_ip_value.$ui->tg_ip_cl;
			// $x=str_replace('constant',$datarow[$row['name']],$x);
			//echo $datarow[$row['name']];
			//genrate path of a file////////
			$qry_for_f_path="select upload_dir from path_dir where table_name='".$tbl."'";
			$path=$this->firstArray($qry_for_f_path);
			$file_path=isset($path['upload_dir'])?$path['upload_dir']:'public/';
			$qry_for_f_name="select ".$row['name']." from ".$tbl." where ".$row['name']."='".$fielddata."'";
			$name=$this->firstArray($qry_for_f_name);
			if($name && !empty($name))
			{
				$file_name=$name[$row['name']];
				$file_names=explode(',',$file_name);
				//$x=$x.$ui->tg_td;
				foreach($file_names as $key => $file_name)
				{
					$file= $file_name;
					$rowid=$recid;
					$fieldname=$row['name'];
					$com='dellink'.$file_name;
					// $x=$x."file:".$file."filename".$file_name."tbl".$tbl."fieldname".$fieldname."rowid".$rowid."filepath".$file_path."filename".$file_name;
						$x=$x."<a href='".$file."' target='_blank' class='dellink_$key'>".$file_name."</a>";
					if($file_name)
						$x=$x."&nbsp<span style='cursor:pointer' class='glyphicon glyphicon-remove dellink' data-common='dellink_$key' role='button' data-table='$tbl' data-fieldname='$fieldname' data-rowid='$rowid' data-path='$file_path' data-filename='$file_name' id='del_attach'></span><br>";
					// $a=$a.$ui->tg_ip.$ui->tg_ip_type.$ui->tg_hidden.$ui->tg_ip_name.$row['name'].$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
					// $a=$a.$datarow[$row['name']].$ui->tg_ip_cl;
				}
				// $x=$x.$ui->tg_td_cl;
            }
            return $x;
}

function getTextField($row,$cnt,$fielddata=NULL,$related=false){
    $ui = $this->ui;
    $x=$ui->tg_ip;
    $x .= $ui->tg_ip_type.$row['type'].$ui->tg_ip_class.$ui->tg_ip_name.$related.$row['name'].$cnt.$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;					
    // $x=$x.$fielddata."\" readonly \"".$ui->tg_ip_cl;
    $x=$x.$fielddata.$ui->tg_ip_cl;
    return $x;
}
function getReadOnlyField($row,$cnt,$fielddata=NULL,$related=false){
    $utils = $this->utils;
    $ui = $this->ui;
    if($row['type']=="list"){
        $x=$this->getReadOnlyList($row,$cnt,1,$fielddata,$related); 
     }elseif($row['type']=="option"){ 
        $x=$this->getReadOnlyOption($row,$cnt,1,$fielddata,$related); 
    }elseif($row['type']=="idate")
    {
        $x=$ui->tg_ip.$ui->tg_ip_type.$row['type'].$ui->tg_ip_class.$ui->tg_ip_name.$related.$row['name'].$cnt.$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
        $x=$x.$utils->getmydate($fielddata);
        $x=$x."\" readonly \"".$ui->tg_ip_cl;
    }else{
        $x=$ui->tg_ip.$ui->tg_ip_type.$row['type'].$ui->tg_ip_class.$ui->tg_ip_name.$related.$row['name'].$cnt.$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
        $x=$x.$fielddata;$x=$x."\" readonly \"".$ui->tg_ip_cl;
    }

return $x;
}
function getDateField($row,$cnt,$fielddata=NULL,$related=false){
    $utils = $this->utils;
    $ui = $this->ui;
    $x=$ui->tg_ip.$ui->tg_ip_type.$row['type'].$ui->tg_ip_class.$ui->tg_ip_name.$related.$row['name'].$cnt.$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
    $x=$x.$utils->getmydate($fielddata);
    $x=$x.$ui->tg_ip_id.$row['name'].$cnt.$ui->tg_ip_class.$ui->tg_class;
    $x=$x.$ui->tg_ip_cl.$ui->tg_dat.$row['name'].$cnt.$ui->tg_dat_cl;	
    return $x;		
}
function getFieldType($tbl,$row,$cnt,$recid,$fielddata=NULL,$related=false){
	$utils = $this->utils;
    if($row['type']=="list"){
		$x=$this->getListField($row,$cnt,1,$fielddata,$related); 
	}elseif($row['type']=="option"){ 
		$x=$this->getOptionField($row,$cnt,1,$fielddata,$related); 
    }elseif($row['type'] == 'file'){
		$x=$this->getFileField($tbl,$row,$cnt,$recid,$fielddata,$related); 
    }
    elseif($row['type']=="idate")
    {
		$x=$this->getDateField($row,$cnt,$fielddata,$related);
    }
    elseif($row['type']=="textarea")
    {
		$x=$this->getTextArea($row,$cnt,$fielddata,$related);
    }	
    elseif($row['type']=="ihtml")
    {
		$x=$this->getHtml($row,$cnt,$fielddata,$related);
    }
    elseif($row['type']=="radio")
    {
		$x=$this->getRadioBtn($row,$cnt,$fielddata,$related);
    }else{
        $x=$this->getTextField($row,$cnt,$fielddata,$related);
    }
    return $x;

}
function modeList($tbl,$result1,$data_sql,$arr,$arr_show){
    $ui = new UiConstant($tbl);
    $utils = $this->utils;
    $a=$ui->tg_top;
    $cnt=0;
    $x="";
    //echo "a is ".$a;
    //Print Header row
        $a .= $ui->tg_ro;
        // adding for display only temporary fields
        $tmp='';
        //blank cell for checkbox
		$a=$a.$ui->tg_hdr.$ui->tg_chk.$ui->tg_chk_val.$ui->tg_ip_cl.$ui->tg_hdr_cl;
        foreach($result1 as $row)
		{
		$tmp=$tmp.$row['name'].',';
		if(empty($arr_show) || in_array($row['name'],$arr_show))
		$a=$a.$ui->tg_hdr.$row['alias'].$ui->tg_hdr_cl;
		// adding for display only temporary fields
		}
		for($i=0;$i<sizeof($arr_show);$i++)
		{
		$k=is_array($arr_show[$i])?$arr_show[$i][0]:$arr_show[$i];
		$j=explode(',',$tmp);
		if(!in_array($k,explode(',',$tmp)))
		$a=$a.$ui->tg_hdr.$k.$ui->tg_hdr_cl;
		}
        $a=$a.$ui->tg_ro_cl;
        //Print Data rows
		// $result_data=mysql_query($data_sql);
		$result_data=$this->allArray($data_sql);
        //echo $a;
        // echo "<pre>";
        // print_r($result1);
        $y=$z="";
        foreach($result_data as $datarow)
		{
            $j=1;
            $a=$a.$ui->tg_ro;
            //Input for checkbox
            $a=$a.$ui->tg_td.$ui->tg_chk.$cnt.$ui->tg_chk_val;
            $a=$a.$ui->tg_ip_cl.$ui->tg_td_cl;
   
            foreach($result1 as $row)
            {
               //$fielddef=$row;echo $row['name'];
                if(empty($arr_show) || in_array($row['name'],$arr_show))
                {
                    $a.=$ui->tg_td;	
                    if($row['dbindex']=='primary' || !in_array($row['name'], $arr))
                    {
                    $a .=$this->getReadOnlyField($row,$cnt,$datarow[$row['name']]);        
                     }else{
                    $a .= $this->getFieldType($tbl,$row,$cnt,$datarow['id'],$datarow[$row['name']]);
                    }    
                    $a .=$ui->tg_td_cl;
                }

            }

            $cnt++;
            $a=$a.$ui->tg_ro_cl."\n";
           
        }
        $a=$a.$ui->tg_top_cl;
        return $a;
}

function modeTemplate($tbl,$result1,$arr_default,$arr,$arr_show,$view_name){
    $ui = $this->ui;
    $nfield=0;
    $split=0;
    $a=$ui->tg_row.$ui->tg_col1.$ui->tg_div_cl;
    //echo $ui->tg_col;
    $a=$a.$ui->tg_col;$b=$ui->tg_col;
    $c=$ui->tg_col;
    $d=$ui->tg_div_cl.$ui->tg_row.$ui->tg_col1.$ui->tg_div_cl.$ui->tg_span;	
    $cnt=0;
        // while($row = mysql_fetch_array($result1))
    $a = $this->get_template($tbl,$view_name);
        foreach($result1 as $key=>$row)
		{ 

            // $result_data=$this->allArray($data_sql);
            //$j=1;
            // while($datarow=mysql_fetch_array($result_data))
            $fielddef=array('name'=>$row['name'],'type'=>$row['type'],'tblid'=>$row['tblid'],'fieldid'=>$row['fieldid'],'size'=>$row['size']);
            //print_r($fielddef['name']);
            // echo $row['name'].'<br>';               
            // foreach($result_data as $datarow)
            // {
                
                if(empty($arr_show) || in_array($row['name'],$arr_show))
                {	$fval='';
                    if(isset($arr_default[$row['name']]))
                    {$fval=$arr_default[$row['name']];
                    }
                    if($row['dbindex']=='primary' || !in_array($row['name'], $arr))
                    {
                    $x=$this->getReadOnlyField($fielddef,$cnt,$fval);
                    }else{
                        $x = $this->getFieldType($tbl,$fielddef,$cnt,NULL,$fval);
                    }
                    $a=str_replace("{{".$tbl.".".$row['name']."}}",$x,$a); 
                    $x='';  
                }
            
            // }
			 
		}
return $a;
    }
function modeSingle($tbl,$result1,$arr_default,$arr,$arr_show){
    $ui =$this->ui;
    $properties = $this->properties;
    $a = "";
    $b= $c=$d = "";
    $cnt = 0;
    $mode = $properties->col_mode;
    $col_wid = $properties->col_wid;
    $col_space = $properties->col_space;
    $span = $properties->span;
	// $tg_row="<div class=\"row\">";
	// $tg_col1="<div class=\"col-md-1\">";
	$ui->tg_span="<div class=\"col-md-$span\">";
	$ui->tg_colspace="<div class=\"col-md-$col_space\"></div>";
    $ui->tg_col="<div class=\"col-md-$col_wid\">";
    $nfield=0;
	$split=0;
	$a=$ui->tg_row.$ui->tg_col1.$ui->tg_div_cl;
	//echo $ui->tg_col;
	$a=$a.$ui->tg_col;$b=$ui->tg_col;
	$c=$ui->tg_col;
	if($mode==3)
	$d=$ui->tg_div_cl.$ui->tg_row.$ui->tg_col1.$ui->tg_div_cl.$ui->tg_span;	

    $cnt=0;
        foreach($result1 as $row)
        {
            // echo $row['name']."<br>";
            $tg_ip_gp = 'tg_ip_gp';
            $sys_fields = array("created_at","modified_at","created_by","modified_by");
            if($row['dbindex'] == 'primary' || in_array($row['name'],$sys_fields )){
                $tg_ip_gp = 'tg_ip_gp_hide';
            }
            if(!in_array($row['name'], $arr)){
                $tg_ip_gp = 'tg_ip_gp_hide';
            }
            if($row['span']== 2)
            {
                $d = isset($d) ? $d : '';
				$d=$d.$ui->$tg_ip_gp.$row['name'].$ui->tg_cl.$row['alias'].$ui->tg_ip_gp_cl;$split=1;
			}
            else{
                if($row['col']== 2){
					$b=$b.$ui->$tg_ip_gp.$row['name'].$ui->tg_cl.$row['alias'].$ui->tg_ip_gp_cl;
				}
                elseif($row['col']== 3){
					$c=$c.$ui->$tg_ip_gp.$row['name'].$ui->tg_cl.$row['alias'].$ui->tg_ip_gp_cl;
				}
                else
                {
					if(empty($arr_show) || in_array($row['name'],$arr_show)){
						$a=$a.$ui->$tg_ip_gp.$row['name'].$ui->tg_cl.$row['alias'].$ui->tg_ip_gp_cl;
					}
                }
            }
            
            //Print data columns
            $j=1;
            $fielddef=array('name'=>$row['name'],'type'=>$row['type'],'tblid'=>$row['tblid'],'fieldid'=>$row['fieldid'],'size'=>$row['size']);
            if(empty($arr_show) || in_array($row['name'],$arr_show))
            {	$fval='';
                if(isset($arr_default[$row['name']]))
                {$fval=$arr_default[$row['name']];
                }
                if($row['dbindex']=='primary' || !in_array($row['name'], $arr))
                { 
                $x=$this->getReadOnlyField($fielddef,$cnt,$fval);
                }else{
				$x = $this->getFieldType($tbl,$fielddef,$cnt,NULL,$fval);
				// echo $x;
                }
                // $a=str_replace("{{".$tbl.".".$row['name']."}}",$x,$a); 
                // $x='';  
                $x .= $ui->tg_div_cl;
                if($row['span']==2)
                {$d=$d.$x;}
                else{
                if($row['col']==2)
                {
                    $b=$b.$x;
                }
                elseif($row['col']==3)
                $c=$c.$x;
                else{
                    $a=$a.$x;
                }
                }
            }
                    
            //         }
            //     }
            // }
        }
            $cnt++;
            $a=$a.$ui->tg_div_cl.$ui->tg_colspace.$b.$ui->tg_div_cl;
            if($mode==3)
            $a=$a.$ui->tg_colspace.$c.$ui->tg_div_cl;
            if($split==1)
            $a=$a.$d.$ui->tg_div_cl;
            $a=$a.$ui->tg_col1.$ui->tg_div_cl.$ui->tg_div_cl;
        //Close Table
            //echo $ui->tg_top_cl;
            $a=$a."<input type=\"number\" name=\"icnt\" value=$cnt style=\"display:none\">";
            $a=$a."<input type=\"hidden\" name=\"field_edit\" value=\"".implode(",",$arr)."\" >";
    return $a;
}
function modeSingleUpdate($tbl,$result1,$data_sql,$arr,$arr_show){
    $ui =$this->ui;
    $properties = $this->properties;
    $a = "";
    $b= $c=$d = "";
    $cnt = 0;
    $mode = $properties->col_mode;
    $col_wid = $properties->col_wid;
    $col_space = $properties->col_space;
    $span = $properties->span;
	// $tg_row="<div class=\"row\">";
	// $tg_col1="<div class=\"col-md-1\">";
	$ui->tg_span="<div class=\"col-md-$span\">";
	$ui->tg_colspace="<div class=\"col-md-$col_space\"></div>";
    $ui->tg_col="<div class=\"col-md-$col_wid\">";
    $nfield=0;
	$split=0;
	$a=$ui->tg_row.$ui->tg_col1.$ui->tg_div_cl;
	//echo $ui->tg_col;
	$a=$a.$ui->tg_col;$b=$ui->tg_col;
	$c=$ui->tg_col;
	if($mode==3)
	$d=$ui->tg_div_cl.$ui->tg_row.$ui->tg_col1.$ui->tg_div_cl.$ui->tg_span;	
	$cnt=0;
	$arr_show = array_unique($arr_show);
	// print_r($arr_show);
        foreach($result1 as $row)
        {
			$tg_ip_gp = 'tg_ip_gp';
            $sys_fields = array("created_at","modified_at","created_by","modified_by");
            if($row['dbindex'] == 'primary' || in_array($row['name'],$sys_fields ) || !in_array($row['name'],$arr_show )){
				$tg_ip_gp = 'tg_ip_gp_hide';
			}
			// echo $row['name'].'->'.$tg_ip_gp.'<br>';
            // if(in_array($row['name'],$arr_show)){
			// 	$tg_ip_gp = 'tg_ip_gp';
            // }
			$tclose = ($tg_ip_gp == 'tg_ip_gp_hide') ? '</div>':'';
            if($row['span']== 2)
            {
				$d = isset($d) ? $d : '';
				$d=$d.$ui->$tg_ip_gp.$row['name'].$ui->tg_cl.$row['alias'].$ui->tg_ip_gp_cl;
				$split=1;
			}
			else{
				// echo $row['name']."=====>".htmlentities($ui->$tg_ip_gp)."<br>";
                if($row['col']== 2)
                $b=$b.$ui->$tg_ip_gp.$row['name'].$ui->tg_cl.$row['alias'].$ui->tg_ip_gp_cl.$tclose;
                elseif($row['col']== 3)
                $c=$c.$ui->$tg_ip_gp.$row['name'].$ui->tg_cl.$row['alias'].$ui->tg_ip_gp_cl.$tclose;
                else
                {
            if(empty($arr_show) || in_array($row['name'],$arr_show))
                $a=$a.$ui->$tg_ip_gp.$row['name'].$ui->tg_cl.$row['alias'].$ui->tg_ip_gp_cl;
                }
			}
			// echo "<pre>".$a."</pre>";
            
            //Print data columns
            
            $j=1;
        

            $result_data=$this->allArray($data_sql);
            $fielddef=array('name'=>$row['name'],'type'=>$row['type'],'tblid'=>$row['tblid'],'fieldid'=>$row['fieldid'],'size'=>$row['size']);
            foreach($result_data as $datarow)
            {
                if(empty($arr_show) || in_array($row['name'],$arr_show))
                {	
					// echo $row['name'].'<br>';
                    if($row['dbindex']=='primary' || !in_array($row['name'], $arr))
                    {
                    $x=$this->getReadOnlyField($fielddef,$cnt,$datarow[$row['name']]);
                    // echo $row['name'];
                    }else{
					$x= $this->getFieldType($tbl,$fielddef,$cnt,$datarow['id'],$datarow[$row['name']]);
					// echo $x;
                    }
                    // $a=str_replace("{{".$tbl.".".$row['name']."}}",$x,$a); 
                    // $x='';  
                    $x .= $ui->tg_div_cl;
                    if($row['span']==2)
                    {
						$d=$d.$x;
					}
                    else{
                    if($row['col']==2)
                    {
                        $b=$b.$x;
                    }
                    elseif($row['col']==3)
                    $c=$c.$x;
                    else{
                        $a=$a.$x;
                    }
                    }
                }
                    
            } 
            //     }
            // }
        }
            $cnt++;
            $a=$a.$ui->tg_div_cl.$ui->tg_colspace.$b.$ui->tg_div_cl;
            if($mode==3)
            $a=$a.$ui->tg_colspace.$c.$ui->tg_div_cl;
            if($split==1)
            $a=$a.$d.$ui->tg_div_cl;
            $a=$a.$ui->tg_col1.$ui->tg_div_cl.$ui->tg_div_cl;
        //Close Table
            //echo $ui->tg_top_cl;
            $a=$a."<input type=\"number\" name=\"icnt\" value=$cnt style=\"display:none\">";
            $a=$a."<input type=\"hidden\" name=\"field_edit\" value=\"".implode(",",$arr)."\" >";
    return $a;
}

function addrow_general($tbl,$arr,$arr_show,$defaults,$mode='single',$view_name){
    $ui = $this->ui;
	$utils = $this->utils;
	$sql1=$this->dbsql($tbl);

	$data_sql="select * from ".$tbl;

	//DB SQLs
	$sql="select tblid from config where name='$tbl'";

	$result=$this->firstArray($sql);
	$myvar=$result['tblid'];
	$sql1="select * from field where tblid=$myvar";

    $result1=$this->allArray($sql1);
    $a="";
    if($mode=='single'){
       $a=$this->modeSingle($tbl,$result1,$defaults,$arr,$arr_show);
    }elseif($mode=='template'){
        $a=$this->modeTemplate($tbl,$result1,$defaults,$arr,$arr_show,$view_name);
     }

return $a;

}

function input_general($tbl,$qual,$arr,$arr_show,$mode,$view_name,$access_mode=false)
{
	$appC = $this->appC;
	$utils = $this->utils;
	$ui = $this->ui;
	$sql1=$this->dbsql($tbl);
	$col_wid=0;$col_space=0;

	$data_sql="select * from ".$tbl;
	
	$result1=$this->allArray($sql1);
	$data_sql="select * from ".$tbl;
	if(trim($qual))
	{
		$qual=" where ".$qual;
		$data_sql=$data_sql.$qual;
    }
    // echo $data_sql;
    // print_r($arr_show);

	$a="";

	$j=0;
	
		$cnt=0;
        $b="";    
        $x="";
        if($mode=='single'){
            $a=$this->modeSingleUpdate($tbl,$result1,$data_sql,$arr,$arr_show);
         }elseif($mode=='template'){
            // $a=$this->modeTemplate($tbl,$result1,$arr,$arr_show);
             $a=$this->modeTemplateUpdate($tbl,$result1,$data_sql,$arr,$arr_show,$view_name);
          }elseif($mode=='list'){
            $a=$this->modeList($tbl,$result1,$data_sql,$arr,$arr_show);
          }
          $x=$x.$ui->tg_chk."0".$ui->tg_chk_val."\" hidden checked=\"checked";
          $x=$x.$ui->tg_ip_cl;
          $a=$a.$x; 
return $a;
}

function preview($tbl,$qual,$arr,$arr_show,$view_name){
	$appC = $this->appC;
	$utils = $this->utils;
	$ui = $this->ui;
	$sql1=$this->dbsql($tbl);
	$result1=$this->allArray($sql1);
	$data_sql="select * from ".$tbl;
	if(trim($qual))
	{
		$qual=" where ".$qual;
		$data_sql=$data_sql.$qual;
	}
	$rel_fields = array();
	$tblid = $utils->getTableId($tbl);
	$form_rel = "select c.name,c.tblid,f.id from config c  join formrel f on f.childid = c.tblid where sourceid = $tblid";
	$details = $this->all($form_rel);
	if(!empty($details)){
		foreach($details as $rel_detail){
			$rel_tbl = $rel_detail->name;
			$rel_id = $rel_detail->id;
			$sql_rel_tbl = $this->dbsql($rel_tbl);
			$_tblid= $rel_detail->tblid;
			$rel_fields_sql = $this->all("select name from field where tblid = $_tblid");
			$re_arr_show = array();
			foreach($rel_fields_sql as $field){
				array_push($re_arr_show,$field->name);
			}
			$rel_fields[$rel_tbl] = array('data'=>$this->allArray($sql_rel_tbl),'arr_show'=>$re_arr_show,'relid'=>$rel_id);
		}
	}
	$a="";

	$j=0;
	$a=$this->previewTemplate($tbl,$result1,$data_sql,$arr,$arr_show,$view_name,$rel_fields);
	// $a .= "<script>document.title='';window.print();</script>";
	return $a;
}

function previewTemplate($tbl,$result1,$data_sql,$arr,$arr_show,$view_name,$details){
	$utils = $this->utils;
	$a =$this->get_template($tbl,$view_name);
	$x="";$cnt=0;
	if($a){
		foreach($result1 as $key=>$row)
		{ 
			$result_data=$this->allArray($data_sql);
			
			$fielddef=array('name'=>$row['name'],'type'=>$row['type'],'tblid'=>$row['tblid'],'fieldid'=>$row['fieldid'],'size'=>$row['size']);
			
			foreach($result_data as $datarow)
			{    
				if(empty($arr_show) || in_array($row['name'],$arr_show))
				{	
					if($row['type']=="list"){
						$x=$utils->getFirstListAlias($row['fieldid'],$datarow[$row['name']]); 
					}elseif($row['type']=="option"){ 
						$x=$utils->getOptionAlias($row['fieldid'],$datarow[$row['name']]); 
					}elseif($row['type']=="idate"){ 
						$x=$utils->getmydate($datarow[$row['name']]); 
					}
					else{
						$x = $datarow[$row['name']];
					}
					$a=str_replace("{{".$tbl.".".$row['name']."}}",$x,$a); 
					$x='';  
				}
			}
		}
		$cnt++;
		$parent_data_sql = $this->first($data_sql);
		$parent_id = $parent_data_sql->id;
		foreach($details as $d_key=>$detail){
			$relid = $detail['relid'];
			$reldata_sql = $this->allArray("select r.* from {$d_key} r join reldata rd on rd.childrecid = r.id where rd.relid={$relid} and rd.parentrecid={$parent_id}");
			$doc = new \DOMDocument();
			$doc->loadHTML($a);
			$ids = $doc->getElementById($d_key);
			if( $ids instanceof \DOMNode ) {
				$loop =  $this->innerHTML( $ids, false );
			}
			$variable = $d_key.'_cnt';
			$$variable=0;
			$related_tbl = $d_key.'_';
			$arr_show = $detail['arr_show'];
			if($reldata_sql && !empty($reldata_sql)){
				foreach($reldata_sql as $kkey=>$datarow){
					// print_r($datarow);
					if($kkey >0 && isset($loop) && $ids){
						$this->appendHTML($ids,$loop);
						$a = $doc->saveHTML();
					}
					foreach($detail['data'] as $_key => $_row){
						$fielddef=array('name'=>$_row['name'],'type'=>$_row['type'],'tblid'=>$_row['tblid'],'fieldid'=>$_row['fieldid'],'size'=>$_row['size']);
						// foreach($reldata_sql as $keys=>$datarow){
							if(empty($arr_show) || in_array($_row['name'],$arr_show) && isset($datarow[$_row['name']]))
							{	
								$x = $datarow[$_row['name']];
								// echo $_row['name'].'-->'.$datarow[$_row['name']];
								$a=str_replace("{{".$d_key.".".$_row['name']."}}",$x,$a); 
								$x='';  
							}
							
							// }
						}
						// loading $a for each iteration
						$doc = new \DOMDocument();
						$doc->loadHTML($a);
						$ids = $doc->getElementById($d_key);
						$$variable++;
				}
			}else{
				foreach($detail['data'] as $_key => $_row){
					$fielddef=array('name'=>$_row['name'],'type'=>$_row['type'],'tblid'=>$_row['tblid'],'fieldid'=>$_row['fieldid'],'size'=>$_row['size']);
					if(empty($arr_show) || in_array($_row['name'],$arr_show))
					{	
						$x = '';
						
						$a=str_replace("{{".$d_key.".".$_row['name']."}}",$x,$a); 
						$x='';  
					}
				}
			}
			foreach($detail['data'] as $_key => $_row){
				$fielddef=array('name'=>$_row['name'],'type'=>$_row['type'],'tblid'=>$_row['tblid'],'fieldid'=>$_row['fieldid'],'size'=>$_row['size']);
				if($reldata_sql && !empty($reldata_sql)){
					foreach($reldata_sql as $keys=>$datarow){
						if(empty($arr_show) || in_array($_row['name'],$arr_show) && isset($datarow[$_row['name']]))
						{	
							$x = $datarow[$_row['name']];
							
							$a=str_replace("{{".$d_key.".".$_row['name']."}}",$x,$a); 
							$x='';  
						}
						
					}
				}else{
					if(empty($arr_show) || in_array($_row['name'],$arr_show))
					{	
						$x = "";
						$a=str_replace("{{".$d_key.".".$_row['name']."}}",$x,$a); 
						$x='';  
					}
				}
			}
		}

	}else $a = '<p>Template not found</p>';
	return $a;

}

function input_new($tbl,$qual,$arr,$arr_show,$mode,$view_name,$access_mode=false)
{
	$appC = $this->appC;
	$utils = $this->utils;
	$ui = $this->ui;
	$sql1=$this->dbsql($tbl);
	$col_wid=0;$col_space=0;

	$data_sql="select * from ".$tbl;
	
	$result1=$this->allArray($sql1);
	$data_sql="select * from ".$tbl;
	if(trim($qual))
	{
		$qual=" where ".$qual;
		$data_sql=$data_sql.$qual;
	}
	// check realtion table is present or not
	$rel_fields = array();
	$tblid = $utils->getTableId($tbl);
	$form_rel = "select c.name,c.tblid,f.id from config c  join formrel f on f.childid = c.tblid where sourceid = $tblid";
	$details = $this->all($form_rel);
	// echo $form_rel;
	// print_r($details);
	if(!empty($details)){
		foreach($details as $rel_detail){
			$rel_tbl = $rel_detail->name;
			$rel_id = $rel_detail->id;
			$sql_rel_tbl = $this->dbsql($rel_tbl);
			$_tblid= $rel_detail->tblid;
			$rel_fields_sql = $this->all("select name from field where tblid = $_tblid");
			$re_arr_show = array();
			foreach($rel_fields_sql as $field){
				array_push($re_arr_show,$field->name);
			}
			$rel_fields[$rel_tbl] = array('data'=>$this->allArray($sql_rel_tbl),'arr_show'=>$re_arr_show,'relid'=>$rel_id);
		}
	}
	

	$a="";

	$j=0;
	
		$cnt=0;
        $b="";    
        $x="";
        if($mode=='single'){
            $a=$this->modeSingleUpdate($tbl,$result1,$data_sql,$arr,$arr_show);
         }elseif($mode=='template'){
            // $a=$this->modeTemplate($tbl,$result1,$arr,$arr_show);
             $a=$this->modeTemplateUpdate($tbl,$result1,$data_sql,$arr,$arr_show,$view_name);
          }elseif($mode=='list'){
            $a=$this->modeList($tbl,$result1,$data_sql,$arr,$arr_show);
          }elseif($mode=='new'){
            $a=$this->modeNew($tbl,$result1,$data_sql,$arr,$arr_show,$view_name,$rel_fields);
          }
          $x=$x.$ui->tg_chk."0".$ui->tg_chk_val."\" hidden checked=\"checked";
          $x=$x.$ui->tg_ip_cl;
          $a=$a.$x; 
return $a;
}

function modeNew($tbl,$result1,$data_sql,$arr,$arr_show,$view_name,$details){
	// print_r($data_sql);exit;
	$utils = $this->utils;
	$a =$this->get_template($tbl,$view_name);
	$x="";$cnt=0;
	if($a){

		foreach($result1 as $key=>$row)
		{ 
			$result_data=$this->allArray($data_sql);
			// new method for related data insert
			// $result_data = $utils->relatedDataMapping($result_data); 
			$fielddef=array('name'=>$row['name'],'type'=>$row['type'],'tblid'=>$row['tblid'],'fieldid'=>$row['fieldid'],'size'=>$row['size']);
			
			foreach($result_data as $datarow)
			{    
				if(empty($arr_show) || in_array($row['name'],$arr_show))
				{	
					if($row['dbindex']=='primary' || !in_array($row['name'], $arr))
					{
						$y = '<span>'.$utils->getAlias($tbl,$row['name'],$datarow[$row['name']]).'</<span>';
						$x = $this->getReadOnlyField($fielddef,$cnt,$datarow[$row['name']]);
					}else{
						$y = '<span>'.$utils->getAlias($tbl,$row['name'],$datarow[$row['name']]).'</<span>';
						$x = $this->getFieldType($tbl,$fielddef,$cnt,$datarow['id'],$datarow[$row['name']]);
					}
					$a=str_replace("{{".$tbl.".".$row['name']."}}",$x,$a); 
					$a=str_replace("[[".$tbl.".".$row['name']."]]",$y,$a); 
					$x='';  
				}
			}
		}$cnt++;
		// print_r($details);
		$parent_data_sql = $this->first($data_sql);
		$parent_id = $parent_data_sql->id;
		foreach($details as $d_key=>$detail){
			$relid = $detail['relid'];
			$reldata_sql = $this->allArray("select r.* from {$d_key} r join reldata rd on rd.childrecid = r.id where rd.relid={$relid} and rd.parentrecid={$parent_id}");
			// echo "<pre>";
			// echo "select r.* from {$d_key} r join reldata rd on rd.childrecid = r.id where rd.relid={$relid} and rd.parentrecid={$parent_id}";
			// print_r($reldata_sql);
			// echo "</pre>";
			$doc = new \DOMDocument();
			$doc->loadHTML($a);
			$ids = $doc->getElementById($d_key);
			if( $ids instanceof \DOMNode ) {
				$loop =  $this->innerHTML( $ids, false );
			}
			
			$variable = $d_key.'_cnt';
			$$variable=0;
			$related_tbl = $d_key.'_';
			
			$arr_show = $detail['arr_show'];
			// echo "<pre>";
			// print_r($reldata_sql);
			// echo "</pre>";
			if($reldata_sql && !empty($reldata_sql)){
				foreach($reldata_sql as $kkey=>$datarow){
					// print_r($datarow);
					if($kkey >0 && isset($loop) && $ids){
						$this->appendHTML($ids,$loop);
						$a = $doc->saveHTML();
					}
					// echo  "<pre>";print_r($datarow);echo "</pre>";
					foreach($detail['data'] as $_key => $_row){
						$fielddef=array('name'=>$_row['name'],'type'=>$_row['type'],'tblid'=>$_row['tblid'],'fieldid'=>$_row['fieldid'],'size'=>$_row['size']);
						// foreach($reldata_sql as $keys=>$datarow){
							if(empty($arr_show) || in_array($_row['name'],$arr_show) && array_key_exists($_row['name'],$datarow))
							{	
								if($_row['dbindex']=='primary' || !in_array($_row['name'], $arr_show))
								{
									$x = $this->getReadOnlyField($fielddef,$$variable,$datarow[$_row['name']],$related_tbl);
								}else{

									$x = $this->getFieldType($tbl,$fielddef,$$variable,$datarow['id'],$datarow[$_row['name']],$related_tbl);
								}
								// echo $_row['name'].'-->'.$datarow[$_row['name']].'<br>';
								$a=str_replace("{{".$d_key.".".$_row['name']."}}",$x,$a); 
								$x='';  
							}
							
							// }
						}
						// loading $a for each iteration
						$doc = new \DOMDocument();
						$doc->loadHTML($a);
						$ids = $doc->getElementById($d_key);
						$$variable++;
					}
				}else{
					foreach($detail['data'] as $_key => $_row){
						$fielddef=array('name'=>$_row['name'],'type'=>$_row['type'],'tblid'=>$_row['tblid'],'fieldid'=>$_row['fieldid'],'size'=>$_row['size']);
						if(empty($arr_show) || in_array($_row['name'],$arr_show))
						{	
							if($_row['dbindex']=='primary' || !in_array($_row['name'], $arr_show))
							{
								$x = $this->getReadOnlyField($fielddef,$$variable,NULL,$related_tbl);
							}else{
								$x = $this->getFieldType($tbl,$fielddef,$$variable,NULL,NULL,$related_tbl);
							}
							$a=str_replace("{{".$d_key.".".$_row['name']."}}",$x,$a); 
							$x='';  
						}
					}
				}
				// $rel_id = $detail['rel_id'];
				foreach($detail['data'] as $_key => $_row){
					$fielddef=array('name'=>$_row['name'],'type'=>$_row['type'],'tblid'=>$_row['tblid'],'fieldid'=>$_row['fieldid'],'size'=>$_row['size']);
					if($reldata_sql && !empty($reldata_sql)){
						foreach($reldata_sql as $keys=>$datarow){
							if(empty($arr_show) || in_array($_row['name'],$arr_show) && isset($datarow[$_row['name']]))
							{	
								if($_row['dbindex']=='primary' || !in_array($_row['name'], $arr_show))
								{
									$x = $this->getReadOnlyField($fielddef,$$variable,$datarow[$_row['name']],$related_tbl);
								}else{
									$x = $this->getFieldType($tbl,$fielddef,$$variable,$datarow['id'],$datarow[$_row['name']],$related_tbl);
								}
								$a=str_replace("{{".$d_key.".".$_row['name']."}}",$x,$a); 
								$x='';  
							}
							
						}
					}else{
						if(empty($arr_show) || in_array($_row['name'],$arr_show))
						{	
							if($_row['dbindex']=='primary' || !in_array($_row['name'], $arr_show))
							{
								$x = $this->getReadOnlyField($fielddef,$$variable,NULL,$related_tbl);
							}else{
								$x = $this->getFieldType($tbl,$fielddef,$$variable,NULL,NULL,$related_tbl);
							}
							$a=str_replace("{{".$d_key.".".$_row['name']."}}",$x,$a); 
							$x='';  
						}
					}
				}
				$rel_edit = implode($arr_show);
				// $a=$a."<input type=\"hidden\" name=\"".$d_key."_edit\" value=\"".$rel_edit."\">";
				$a=$a."<input type=\"hidden\" name=\"".$d_key."_rel_id\" value=\"".$relid."\">";
				$a=$a."<input type=\"hidden\" name=\"".$variable."\" value=\"".$$variable."\">";
				// $a=$a."<input type=\"hidden\" name=\"relid\" value=\"".$relid."\">";
				$a=$a."<input type=\"hidden\" name=\"releated_tables[]\" value=\"".$d_key."\">";
			}
			$a=$a."<input type=\"number\" name=\"icnt\" value=$cnt style=\"display:none\">";
			$a=$a."<input type=\"hidden\" name=\"field_edit\" value=\"".implode(",",$arr)."\" >";
			// echo '<pre>'.$a.'</pre>';
		}
		if(!$a) $a = '<p>Template not found</p>';
			return $a;
	}



function appendHTML(DOMNode $parent, $source) {
    $tmpDoc = new DOMDocument();
    $tmpDoc->loadHTML($source);
    foreach ($tmpDoc->getElementsByTagName('body')->item(0)->childNodes as $node) {
        $node = $parent->ownerDocument->importNode($node, true);
        $parent->appendChild($node);
    }
}

function innerHTML(\DOMNode $node, $include_target_tag = true)
{
	$doc = new \DOMDocument();
	$doc->appendChild( $doc->importNode( $node, true ) );
	$html = trim( $doc->saveHTML() );
	if ( $include_target_tag  ) {
		return $html;
	}
	return preg_replace( '@^<' . $node->nodeName . '[^>]*>|</' . $node->nodeName . '>$@', '', $html );
}

function modeTemplateUpdate($tbl,$result1,$data_sql,$arr,$arr_show,$view_name){
    $a=$this->get_template($tbl,$view_name);
	$x="";$cnt=0;
    foreach($result1 as $key=>$row)
    { 

        $result_data=$this->allArray($data_sql);

        $fielddef=array('name'=>$row['name'],'type'=>$row['type'],'tblid'=>$row['tblid'],'fieldid'=>$row['fieldid'],'size'=>$row['size']);
        //  print_r($result_data);  
        foreach($result_data as $datarow)
        {
            
            if(empty($arr_show) || in_array($row['name'],$arr_show))
            {	
                if($row['dbindex']=='primary' || !in_array($row['name'], $arr))
                {
					$y = '<span>'.$datarow[$row['name']].'</<span>';
                $x=$this->getReadOnlyField($fielddef,$cnt,$datarow[$row['name']]);
                }else{
					$y = '<span>'.$datarow[$row['name']].'</<span>';
                $x = $this->getFieldType($tbl,$fielddef,$cnt,$datarow['id'],$datarow[$row['name']]);
                }
				$a=str_replace("{{".$tbl.".".$row['name']."}}",$x,$a); 
				$a=str_replace("[[".$tbl.".".$row['name']."]]",$y,$a);
                $x='';  
            }
        
        }
         
    }$cnt++;
    $a=$a."<input type=\"number\" name=\"icnt\" value=$cnt style=\"display:none\">";
    $a=$a."<input type=\"hidden\" name=\"field_edit\" value=\"".implode(",",$arr)."\" >";
  
    return $a;
}

function input_template($tbl,$qual,$arr,$arr_show,$mode,$access_mode=false)
{

	$appC = new AppController();
	$utils = $this->utils;
	$ui = new UiConstant($tbl);
	$sql1=$this->dbsql($tbl);
	$col_wid=0;$col_space=0;

	$data_sql="select * from ".$tbl;
	
	$result1=$this->allArray($sql1);
	$data_sql="select * from ".$tbl;
	if(isset($qual))
	{
		$qual=" where ".$qual;
		$data_sql=$data_sql.$qual;
	}
	//$data_sql=getPagesql($data_sql,7);
	// echo $data_sql;
	//mode 0 is columnar and mode 1 for row-wise printing
	$a="";
	$opt=array(1 => "k");
	//$mode=1;
	$j=0;
	
	
	{
		//Open Table
		//echo $ui->tg_top;
		//Print Header column
		$nfield=0;
		$split=0;
	
		$cnt=0;
        // while($row = mysql_fetch_array($result1))
        $a=$this->get_template($tbl);
		foreach($result1 as $key=>$row)
		{ 

	$result_data=$this->allArray($data_sql);
	$j=1;
	// while($datarow=mysql_fetch_array($result_data))
	foreach($result_data as $datarow)
	{
		
		if(empty($arr_show) || in_array($row['name'],$arr_show))
		{	
			
			if($row['dbindex']=='primary' || !in_array($row['name'], $arr))
			{
						$x=$ui->tg_ip.$ui->tg_ip_type.$row['type'].$ui->tg_ip_class.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
						if($row['type']=="idate")
							{
								//  echo getmydate($datarow[$row['name']]);
									$x=$x.$utils->getmydate($datarow[$row['name']]);
									// $x=$x.$ui->tg_ip_id.$row['name'].$cnt.$ui->tg_ip_class.$ui->tg_class;
									// $x=$x.$ui->tg_ip_cl.$ui->tg_dat.$row['name'].$cnt.$ui->tg_dat_cl.$ui->tg_div_cl;
									$x=$x."\" readonly \"".$ui->tg_ip_cl;
							}
							else{	
							$x=$x.$datarow[$row['name']]."\" readonly \"".$ui->tg_ip_cl;
							}
					 
		}
		elseif($row['type']=="textarea")
		{
            $x=$x.$this->getTextArea($row,$cnt,$datarow[$row['name']],NULL);
		}	
		elseif($row['type']=="ihtml")
		{
			$x=$ui->tg_text.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_ip_cl;
			$x=$x.$datarow[$row['name']].$ui->tg_text_cl.$ui->tg_div_cl;
		}						
		elseif($row['type']=="option")
					{					$x=$ui->tg_sel_control.$row['name'].$cnt.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_cl;
										//$a=$a.$ui->tg_opt.$datarow[$row['name']].$ui->tg_cl.$datarow[$row['name']].$ui->tg_opt_cl;
										//if($cnt==0)
										//{
											$sql_opt="select * from field_option where tblid=".$row['tblid']." and fieldid=".$row['fieldid'];
											// $result_opt=mysql_query($sql_opt);
											$result_opt=$this->allArray($sql_opt);
											$opt[$j]="";
											// while($row_opt = mysql_fetch_array($result_opt))
											foreach($result_opt as $row_opt)
												{
												$opt[$j]=$opt[$j].$ui->tg_opt.$row_opt['value'].$ui->tg_cl.$row_opt['alias'].$ui->tg_opt_cl;
												if($row_opt['value']==$datarow[$row['name']]) 
													{$alias='';$alias=$row_opt['alias'];}
												}
										//}
										//below handles code if no value is set and also instead of value shows alias
										$alias=isset($alias)?$alias:NULL;
											$x=$x.$ui->tg_opt.$datarow[$row['name']].$ui->tg_cl.$alias.$ui->tg_opt_cl;
												
										//introducing for clear value option
										 $x=$x.$ui->tg_opt.$datarow[$row['name']].$ui->tg_cl.$ui->tg_opt_cl;
										 $x=$x.$opt[$j].$ui->tg_sel_cl;
					$j++;
					}
		elseif($row['type']=="list")
					{
						
						 $x=$ui->tg_sel_control.$row['name'].$cnt.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_cl;
											//adding new on13/12/2014.can be deleted
											$sql_filter="select source,filter,id,value,alias from valuelist where id in (select optid from field_option where tblid=".$row['tblid']." and fieldid=".$row['fieldid'].")";
											//echo $sql_filter;
											$result_filter=$this->firstArray($sql_filter);
											$vsource= $result_filter['source'];
											$vfilter= $result_filter['filter'];
											$vid= $result_filter['id'];
											$vvalue= $result_filter['value'];
											$valias= $result_filter['alias'];
											$vfilter= $vfilter==null?" where 1=2 ":" where ".$utils->parseFilter($vfilter);
											$sql_opt="select ".$vvalue." as value,".$valias." as alias from ".$vsource.$vfilter;
											//echo $sql_opt;	
											//Completion of change
											// $result_opt=mysql_query($sql_opt);
											$result_opt=$this->allArray($sql_opt);
											$opt[$j]="";
											$alias='';
											if($result_opt)
											{
											// while($row_opt = mysql_fetch_array($result_opt))
											foreach($result_opt as $row_opt)
												{
												$opt[$j]=$opt[$j].$ui->tg_opt.$row_opt['value'].$ui->tg_cl.$row_opt['alias'].$ui->tg_opt_cl;
												if($row_opt['value']==$datarow[$row['name']]) 
													{$alias=$row_opt['alias'];}
												}
											}
										//}
										//below handles code if no value is set and also instead of value shows alias
										$alias=(isset($alias)&&($alias!=null))?$alias:"--NONE--";
										$x=$x.$ui->tg_opt.$datarow[$row['name']].$ui->tg_cl.$alias.$ui->tg_opt_cl;
										//introducing for clear value option
										$x=$x.$ui->tg_opt."".$ui->tg_cl."--NONE--".$ui->tg_opt_cl;
										$x=$x.$opt[$j].$ui->tg_sel_cl;
					$j++;
					}	

elseif($row['type']=="file" )//////changes
		{
			
			$path = $appC->get_file_path($tbl).$row['name'];
			$x=$ui->tg_ip.$ui->tg_ip_type.$row['type'].$ui->tg_ip_class.$ui->tg_ip_name.$row['name'].$cnt.'[]'.$ui->tg_ip_size.$row['size'].$ui->tg_ip_value.$ui->tg_ip_cl.$ui->tg_div_cl;
			$x=str_replace('constant',$datarow[$row['name']],$x);
			//echo $datarow[$row['name']];
			//genrate path of a file////////
			$qry_for_f_path="select upload_dir from path_dir where table_name='".$tbl."'";
			$path=$this->firstArray($qry_for_f_path);
			$file_path=$path['upload_dir'];
			$qry_for_f_name="select ".$row['name']." from ".$tbl." where ".$row['name']."='".$datarow[$row['name']]."'";
			$name=$this->firstArray($qry_for_f_name);
			if($name && !empty($name))
			{
				$file_name=$name[$row['name']];
				$file_names=explode(',',$file_name);
				//$x=$x.$ui->tg_td;
				foreach($file_names as $key => $file_name)
				{
					$file= $file_name;
					$rowid=$datarow['id'];
					$fieldname=$row['name'];
					$com='dellink'.$file_name;
					// $x=$x."file:".$file."filename".$file_name."tbl".$tbl."fieldname".$fieldname."rowid".$rowid."filepath".$file_path."filename".$file_name;
						$x=$x."<a href='".$file."' target='_blank' class='dellink_$key'>".$file_name."</a>";
					if($file_name)
						$x=$x."&nbsp<span style='cursor:pointer' class='glyphicon glyphicon-remove dellink' data-common='dellink_$key' role='button' data-table='$tbl' data-fieldname='$fieldname' data-rowid='$rowid' data-path='$file_path' data-filename='$file_name' id='del_attach'></span><br>";
					// $a=$a.$ui->tg_ip.$ui->tg_ip_type.$ui->tg_hidden.$ui->tg_ip_name.$row['name'].$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
					// $a=$a.$datarow[$row['name']].$ui->tg_ip_cl;
				}
				//$x=$x.$ui->tg_td_cl;
			}
			
   } ////changes
 		else
		{
				$x=$ui->tg_ip.$ui->tg_ip_type.$row['type'].$ui->tg_ip_class.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
							if($row['type']=="idate")
							{
								//  echo getmydate($datarow[$row['name']]);
									$x=$x.$utils->getmydate($datarow[$row['name']]);
									$x=$x.$ui->tg_ip_id.$row['name'].$cnt.$ui->tg_ip_class.$ui->tg_class;
									$x=$x.$ui->tg_ip_cl.$ui->tg_dat.$row['name'].$cnt.$ui->tg_dat_cl;
							}
							else
							{
									$x=$x.$datarow[$row['name']].$ui->tg_ip_cl;
							}
			}
                
        $a=str_replace("{{".$tbl.".".$row['name']."}}",$x,$a);   
		}
	
		}
			 
		}
		
		}$cnt++;
                
        $x=$ui->tg_chk."0".$ui->tg_chk_val."\" hidden checked=\"checked";
		$x=$x.$ui->tg_ip_cl;
        $a=$a.$x;

		$a=$a."<input type=\"number\" name=\"icnt\" value=$cnt style=\"display:none\">";
		$a=$a."<input type=\"hidden\" name=\"field_edit\" value=\"".implode(",",$arr)."\" >";
	
	
	//only get pages if this is main table
// 	if($_GET['page']==$tbl)
//         {
//             $del='';$bk='';
//             $nav="<div class=\"navbar navbar-default\" role=\"navigation\"><div class=\"col-md-8\">";
//             $bk="<div class=\"col-md-4\"><button class=\"btn btn-default\" id=\"btn_back\" type=\"submit\" name=\"back\" value=\"back\"><span class=\"glyphicon glyphicon-chevron-left\"></span></button></div>"; 
//             $upd="<div class=\"col-md-4\"><button class=\"btn btn-default\" id=\"btn_update\" type=\"submit\" name=\"updates\" value=\"update\" >Update</button></div>";
//         if($_SESSION['SESS_perm']=='user')
// //$del="<div class=\"col-md-4\"><button class=\"btn btn-danger\" id=\"btn_del\" type=\"submit\" value=\"delete\">Delete</button></div>";
// //$a=$z."</div>".$a.$z."</div>";
//         $bk='';
// $nav=$nav.$bk.$upd."</div></div>";
// $a=$a.$nav;
//         }

// $a=$a."&nbsp<input class=\"btn btn-danger\" id=\"btn\" type=\"submit\" name=\"update\" value=\"Update\"><br>";
 
return $a;
}
}