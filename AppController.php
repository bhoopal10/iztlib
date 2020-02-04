<?php
require_once('CoreView.php');
require_once('UiConstant.php');
class AppController extends CoreView{
 function get_file_path($tbl){

	$sql = "select upload_dir from path_dir where table_name = '$tbl'";
	$result=$this->first($sql);
	if($result && !empty($result)){
		return $result->upload_dir;
	}
	else 
	return "no upload_dir in path_dir";
	}

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function addrow1($tbl,$arr,$arr_show,$mode=1)
{
	$this->addrow_latest($tbl,$arr,$arr_show,array(),$mode);
}
function addrow_latest($tbl,$arr,$arr_show,$defaults,$mode=1)
{
	$ui = new UiConstant($tbl);
	$utils = new Utils();
	$sql1=$this->dbsql($tbl);
	$arr_default=$defaults;
	$col_wid=0;$col_space=0;
	if($mode==2)
	{$col_wid=4;$col_space=2;}
	if($mode==3)
	{$col_wid=3;$col_space=1;}
	$span=($col_wid*$mode)+(($mode-1)*$col_space);
	// $tg_row="<div class=\"row\">";
	// $tg_col1="<div class=\"col-md-1\">";
	$ui->tg_span="<div class=\"col-md-$span\">";
	$ui->tg_colspace="<div class=\"col-md-$col_space\"></div>";
	$ui->tg_col="<div class=\"col-md-$col_wid\">";
	// $tg_ip_gp="<div class=\"form-group\"><label for=\"";
	// $tg_ip_gp_cl="</label>";
	// $tg_static="<div class=\"col-sm-10\"><p class=\"form-control-static\">";
	// $tg_static_cl="</p>";
	// $tg_div_cl="</div>";
	// $tg_ip_class="\" class=\"form-control";
	
	// $tg_top="<div class=\"table-responsive\"><table class=\"table table-striped\" id=\"".$tbl."\" width=auto border=1 cellpadding=2 cellspacing=2>";
	// $tg_hdr="<th>";
	// $tg_hdr_cl="</th>";
	// $tg_ro="<tr>";
	// $tg_ro_cl="</tr>";
	// $tg_td="<td>";
	// $tg_td_cl="</td>";
	// $tg_top_cl="</table></div>";
	// $tg_ip="<input";
	// $tg_ip_type=" type=\"";
	// $tg_ip_name="\" name=\"";
	// $tg_ip_value="\" value=\"";
	// $tg_ip_size="\" size=\"";
	// $tg_ip_id="\" id=\"";
	// $tg_class="\" class=\"ro";
	// $tg_ip_cl="\" />";
	// $tg_chk="<input type=\"checkbox\" name=\"chb";
	// $tg_chk_val="";
	// $tg_dat="<a href=\"javascript:NewCal('";
	// $tg_dat_cl="','ddmmyyyy')\"><img src=datetimepick/cal.gif width=16 height=16 border=0 alt=Pick a date></a>";
	// $tg_sel="<select class=\"form-control\" id=\"";
	// $tg_cl="\" >";
	// $tg_sel_cl="</select>";
	// $tg_opt="<option value=\"";
	// $tg_opt_cl="</option>";
	// $tg_hidden="hidden";
	// $tg_readonly="\" readonly ";
	// $tg_text="<textarea rows=\"4\" cols=\"15\" ";
	// $tg_text."class=\"test\"";
	// $tg_text_cl="</textarea>";
	// // Variables added to define a tag
	// $tg_a = "<a ";
	// $tg_href = "href = \"";
	// $tg_a_cl_bar = ">";
	// $tg_a_cl = "</a>";
	// $tg_a_target = "\"target = '_blank' ";

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
	/*if(isset($_POST['sqli']))
	{
	echo $_POST['sqli'];
	if (!mysql_query($_POST['sqli']))
			{
			die('Error: ' . mysql_error());
			}
	}
	*/
	if(isset($_POST['fname']))
	{
	// echo $fname;
	$array=array($_POST['fname']);
	}
        //mode is horizontal row style
        if($mode==1)
        {
//Open Table
        echo $ui->tg_top;

//Print Header row
		$a= $ui->tg_ro;
		//blank cell for checkbox
		//$a=$a.$ui->tg_hdr.$ui->tg_hdr_cl;
		// while($row = mysql_fetch_array($result1))
		foreach($result1 as $row)
		{
		if(!$row['dbindex']=='primary')
		$a=$a.$ui->tg_hdr.$row['alias'].$ui->tg_hdr_cl;
		}
		$a=$a.$ui->tg_ro_cl;

//Print Data rows
		$cnt=0;
		{
		$j=1;

		$a=$a.$ui->tg_ro;

		//Input for checkbox
		//$a=$a.$ui->tg_td.$ui->tg_chk.$cnt.$ui->tg_chk_val;
		//$a=$a.$ui->tg_ip_cl.$ui->tg_td_cl;

		// $result1=mysql_query($sql1);
		$result1=$this->allArray($sql1);
		// while($row = mysql_fetch_array($result1))
		foreach($result1 as $row)
		{
		if($row['type']=="option")
				{
				
				$a=$a.$ui->tg_td.$ui->tg_sel_control.$row['name'].$cnt.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_cl;
				if($cnt==0)
				{
				$sql_opt="select * from field_option where tblid=".$row['tblid']." and fieldid=".$row['fieldid'];
				// $result_opt=mysql_query($sql_opt);
				$result_opt=$this->allArray($sql_opt);
				$opt[$j]="";
				// while($row_opt = mysql_fetch_array($result_opt))
				foreach($result_opt as $row_opt)
					{
					$opt[$j]=$opt[$j].$ui->tg_opt.$row_opt['value'].$ui->tg_cl.$row_opt['alias'].$ui->tg_opt_cl;
					}

				}

				$a=$a.$opt[$j].$ui->tg_sel_cl.$ui->tg_td_cl;
				$j++;
				}
				elseif($row['type']=="list")
					{
								$a=$a.$ui->tg_td.$ui->tg_sel_control.$row['name'].$cnt.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_cl;
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
												}
											}
										//}
										//below handles code if no value is set and also instead of value shows alias
									
										//introducing for clear value option
										$a=$a.$ui->tg_opt."".$ui->tg_cl."--NONE--".$ui->tg_opt_cl;
										$a=$a.$opt[$j].$ui->tg_sel_cl.$ui->tg_td_cl;
					$j++;
					}
				elseif(!$row['dbindex']=='primary')
				{
				$a=$a.$ui->tg_td.$ui->tg_ip.$ui->tg_ip_type.$row['type'].$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
					if($row['type']=="idate")
					{
					$a=$a.$ui->tg_ip_id.$row['name'].$cnt.$ui->tg_class;
					$a=$a.$ui->tg_ip_cl.$ui->tg_dat.$row['name'].$cnt.$ui->tg_dat_cl.$ui->tg_td_cl;
					}
					else {
					$a=$a.$ui->tg_ip_cl.$ui->tg_td_cl;
					}
				}
		}
				//Store variables for insert query
				if(!$row['dbindex']=='primary')
							{$fname=$fname.$row['name'].",";
							if($row['type']=='idate')
							$fval=$fval."'\"".$utils->setmydate($_POST[$row['name']])."\"',";
							else
							$fval=$fval."'\".$"."_"."POST['".$row['name']."'].\"',";
					}
		$cnt++;
		$a=$a.$ui->tg_ro_cl."\n";
		}
//Close Table
		echo $a.$ui->tg_top_cl;
        }
        if($mode>=2)
        {
          {
	//Open Table
	//echo $ui->tg_top;
	//Print Header column
	// hide system generated fields
	$nfield=0;
	$split=0;
	$a=$ui->tg_row.$ui->tg_col1.$ui->tg_div_cl;
	//echo $ui->tg_col;
	$a=$a.$ui->tg_col;$b=$ui->tg_col;
	$c=$ui->tg_col;
	if($mode==3)
	$d=$ui->tg_div_cl.$ui->tg_row.$ui->tg_col1.$ui->tg_div_cl.$ui->tg_span;	
	$cnt=0;
	// while($row = mysql_fetch_array($result1))
	foreach($result1 as $row)
	{
		$tg_ip_gp = 'tg_ip_gp';
		$sys_fields = array("created_at","modified_at","created_by","modified_by");
		if($row['dbindex'] == 'primary' || in_array($row['name'],$sys_fields )){
			$tg_ip_gp = 'tg_ip_gp_hide';
		}
		if(!in_array($row['name'], $arr)){
			$tg_ip_gp = 'tg_ip_gp_hide';
		}
		if($row['span']==2)
		{
			$d = isset($d) ? $d : '';
			$d=$d.$ui->$tg_ip_gp.$row['name'].$ui->tg_cl.$row['alias'].$ui->tg_ip_gp_cl;$split=1;}
		else{
			if($row['col']==2)
			$b=$b.$ui->$tg_ip_gp.$row['name'].$ui->tg_cl.$row['alias'].$ui->tg_ip_gp_cl;
			elseif($row['col']==3)
			$c=$c.$ui->$tg_ip_gp.$row['name'].$ui->tg_cl.$row['alias'].$ui->tg_ip_gp_cl;
			else
			{
		// if(in_array($row['name'],$arr_show))
			$a=$a.$ui->$tg_ip_gp.$row['name'].$ui->tg_cl.$row['alias'].$ui->tg_ip_gp_cl;
			}
		}
		
		//Print data columns
		
		$j=1;
		// print_r($arr);
		//while($datarow=mysql_fetch_array($result_data))
		
		if(empty($arr_show) || in_array($row['name'],$arr_show))
		{		
			if($row['dbindex']=='primary' || !in_array($row['name'], $arr))
			//if($row['dbindex']=='primary')
			{
				// echo $row['name'];
				// var_dump($arr);

				// echo $row['name']."<br>";
				$x=$ui->tg_ip.$ui->tg_ip_type."hidden".$ui->tg_readonly.$ui->tg_ip_class.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
				//if($row['type']=="idate")
				//			{
					//		$x=$x.getmydate($datarow[$row['name']]).$ui->tg_ip_cl;
					//			$x=$x.$ui->tg_static.getmydate($datarow[$row['name']]).$ui->tg_static_cl.$ui->tg_div_cl.$ui->tg_div_cl;
					//		}else
					//	{
						if(isset($arr_default[$row['name']]))
						$x=$x.$arr_default[$row['name']];
						$x=$x.$ui->tg_ip_cl;
						//$x=$x.$ui->tg_static.$datarow[$row['name']].$ui->tg_static_cl.$ui->tg_div_cl.$ui->tg_div_cl;
						//}
						
						$x=$x.$ui->tg_static.$ui->tg_static_cl.$ui->tg_div_cl.$ui->tg_div_cl;

					}
					elseif($row['type']=="textarea")
					{
							$x=$ui->tg_text.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_ip_cl;
							$x=$x.$ui->tg_text_cl.$ui->tg_div_cl;
						}	
						elseif($row['type']=="ihtml")
						{
								$x=$ui->tg_text.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_class.' ihtml'.$ui->tg_ip_cl;
								$x=$x.$row['name'].$ui->tg_text_cl.$ui->tg_div_cl;
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

										$x=$x.$opt[$j].$ui->tg_sel_cl.$ui->tg_div_cl;
										$j++;
						}
						elseif($row['type']=="file"){

						$path = $this->get_file_path($tbl).$row['name'];
						$x=$ui->tg_ip.$ui->tg_ip_type.$row['type'].$ui->tg_ip_class.$ui->tg_ip_name.$row['name'].$cnt.'[]'.$ui->tg_ip_size.$row['size'].$ui->tg_ip_value.$ui->tg_ip_cl.$ui->tg_div_cl;
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
										$x=$x.$opt[$j].$ui->tg_sel_cl.$ui->tg_div_cl;
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
								$x=$x.$ui->tg_ip_cl.$ui->tg_dat.$row['name'].$cnt.$ui->tg_dat_cl.$ui->tg_div_cl;
							}
							else
							{
								if(isset($arr_default[$row['name']]))
						{$x=$x.$arr_default[$row['name']];}
								$x=$x.$ui->tg_ip_cl.$ui->tg_div_cl;}
			}
		
		if($row['span']==2)
		{$d=$d.$x;}
		else{
		if($row['col']==2)
		$b=$b.$x;
		elseif($row['col']==3)
		$c=$c.$x;
		else
		$a=$a.$x;
		}
		
		}
		
		
		
		}$cnt++;
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
	echo $a;
	}  
        }
$fname=chop($fname,",");
$fval=chop($fval,",");
$sqli = "INSERT INTO ".$tbl." ( ".$fname." ) VALUES (".$fval.")";
 
//$sqla= "Alter table 
echo "<input class=\"btn btn-primary\" id=\"btn\" type=\"submit\" name=\"addrow\" value=\"Add Row\">";
//echo "<input class=\"btn btn-warning\" id=\"btn\" type=\"submit\" name=\"modify\" value=\"modify\">";
return $sqli;
}


function addrow_new($tbl,$arr,$arr_show,$mode=1)
{
	$ui = new UiConstant($tbl);
	$utils = new Utils();
	$sql1=dbsql($tbl);
	$col_wid=0;$col_space=0;
	if($mode==2)
	{$col_wid=4;$col_space=2;}
	if($mode==3)
	{$col_wid=3;$col_space=1;}
	$span=($col_wid*$mode)+(($mode-1)*$col_space);
	$ui->tg_row="<div class=\"row\">";
	$ui->tg_col1="<div class=\"col-md-1\">";
	$ui->tg_span="<div class=\"col-md-$span\">";
	$ui->tg_colspace="<div class=\"col-md-$col_space\"></div>";
	$ui->tg_col="<div class=\"col-md-$col_wid\">";
	// $tg_ip_gp="<div class=\"form-group\"><label for=\"";
	// $tg_ip_gp_cl="</label>";
	// $tg_static="<div class=\"col-sm-10\"><p class=\"form-control-static\">";
	// $tg_static_cl="</p>";
	// $tg_div_cl="</div>";
	// $tg_ip_class="\" class=\"form-control";
	
	// $tg_top="<div class=\"table-responsive\"><table class=\"table table-striped\" id=\"".$tbl."\" width=auto border=1 cellpadding=2 cellspacing=2>";
	// $tg_hdr="<th>";
	// $tg_hdr_cl="</th>";
	// $tg_ro="<tr>";
	// $tg_ro_cl="</tr>";
	// $tg_td="<td>";
	// $tg_td_cl="</td>";
	// $tg_top_cl="</table></div>";
	// $tg_ip="<input";
	// $tg_ip_type=" type=\"";
	// $tg_ip_name="\" name=\"";
	// $tg_ip_value="\" value=\"";
	// $tg_ip_size="\" size=\"";
	// $tg_ip_id="\" id=\"";
	// $tg_class="\" class=\"ro";
	// $tg_ip_cl="\" />";
	// $tg_chk="<input type=\"checkbox\" name=\"chb";
	// $tg_chk_val="";
	// $tg_dat="<a href=\"javascript:NewCal('";
	// $tg_dat_cl="','ddmmyyyy')\"><img src=datetimepick/cal.gif width=16 height=16 border=0 alt=Pick a date></a>";
	// $tg_sel="<select class=\"form-control\" id=\"";
	// $tg_cl="\" >";
	// $tg_sel_cl="</select>";
	// $tg_opt="<option value=\"";
	// $tg_opt_cl="</option>";
	// $tg_hidden="hidden";
	// $tg_readonly="\" readonly ";
	// $tg_text="<textarea rows=\"4\" cols=\"15\" ";
	// $tg_text_cl="</textarea>";

	// $result1=mysql_query($sql1);
	$result1=$this->allArray($sql1);


	//echo $data_sql;
//mode 0 is columnar and mode 1 for row-wise printing
	$a="";
	$opt=array(1 => "k");
	//$mode=1;
	$j=0;

	if($mode==0)
	{
	//Open Table
	echo $ui->tg_top;
	//Print Header column
		// while($row = mysql_fetch_array($result1))
		foreach($result1 as $row)
		{

		echo $ui->tg_ro;

		echo $ui->tg_hdr.$row['alias'].$ui->tg_hdr_cl;

	//Print data columns
		//$result_data=mysql_query($data_sql);
		// while($datarow=mysql_fetch_array($result1))
		foreach($result1 as $datarow)
		{
		if($row['dbindex']=='primary')
		{
		echo $ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl;
		}else
		{
		echo $ui->tg_td.$ui->tg_ip.$ui->tg_ip_type.$row['type'].$ui->tg_ip_name.$row['name'].$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
						echo $datarow[$row['name']];
				echo $ui->tg_ip_cl.$ui->tg_td_cl;
		}
		}

		echo $ui->tg_ro_cl;
		}
	//Close Table
		echo $ui->tg_top_cl;
	}
	if($mode==1)
	{
//Open Table
		echo $ui->tg_top;

//Print Header row
		$a= $ui->tg_ro;
		// adding for display only temporary fields
		$tmp='';
		//blank cell for checkbox
		$a=$a.$ui->tg_hdr.$ui->tg_chk.$ui->tg_chk_val.$ui->tg_ip_cl.$ui->tg_hdr_cl;
		// while($row = mysql_fetch_array($result1))
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
		
		$cnt=0;
		// while($datarow=mysql_fetch_array($result1))
		foreach($result1 as $datarow)
		{
		$j=1;

		$a=$a.$ui->tg_ro;

		//Input for checkbox
		$a=$a.$ui->tg_td.$ui->tg_chk.$cnt.$ui->tg_chk_val;
		$a=$a.$ui->tg_ip_cl.$ui->tg_td_cl;

		// $result1=mysql_query($sql1);
		$result1=$this->allArray($sql1);
		// while($row = mysql_fetch_array($result1))
		foreach($result1 as $row)
		{
			if(empty($arr_show) || in_array($row['name'],$arr_show))
				{
					if($row['dbindex']=="primary" || !in_array($row['name'], $arr))
					//if($row['dbindex']=="primary")
					{
						//$a=$a.$ui->tg_td.$ui->tg_ip.$ui->tg_ip_type.$row['type'].$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_readonly.$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
						$a=$a.$ui->tg_td.$ui->tg_ip.$ui->tg_ip_type.$ui->tg_hidden.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
						//$a=$a.$datarow[$row['name']].$ui->tg_ip_cl.$ui->tg_td_cl;

						if($row['type']=="idate")
						{
						$a=$a.$utils->getmydate($datarow[$row['name']]).$ui->tg_ip_cl;
						$a=$a.$utils->getmydate($datarow[$row['name']]).$ui->tg_td_cl;
						}else
						{
						$a=$a.$datarow[$row['name']].$ui->tg_ip_cl;
						$a=$a.$datarow[$row['name']].$ui->tg_td_cl;
						}
					}elseif($row['type']=="textarea")
					{
						$a=$a.$ui->tg_td.$ui->tg_text.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_ip_cl;
						$a=$a.$datarow[$row['name']].$ui->tg_text_cl.$ui->tg_td_cl;
					}
					elseif($row['type']=="option")
					{					$a=$a.$ui->tg_td.$ui->tg_sel_control.$row['name'].$cnt.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_cl;
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
										$a=$a.$ui->tg_opt.$datarow[$row['name']].$ui->tg_cl.$alias.$ui->tg_opt_cl;
										//introducing for clear value option
										$a=$a.$ui->tg_opt.$datarow[$row['name']].$ui->tg_cl.$ui->tg_opt_cl;
										$a=$a.$opt[$j].$ui->tg_sel_cl.$ui->tg_td_cl;
					$j++;
					}
					elseif($row['type']=="list")
					{
								$a=$a.$ui->tg_td.$ui->tg_sel_control.$row['name'].$cnt.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_cl;
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
										// echo "alias is".$alias;
										$a=$a.$ui->tg_opt.$datarow[$row['name']].$ui->tg_cl.$alias.$ui->tg_opt_cl;
										//introducing for clear value option
										$a=$a.$ui->tg_opt."".$ui->tg_cl."--NONE--".$ui->tg_opt_cl;
										$a=$a.$opt[$j].$ui->tg_sel_cl.$ui->tg_td_cl;
					$j++;
					}
					else
					{
						$a=$a.$ui->tg_td.$ui->tg_ip.$ui->tg_ip_type.$row['type'].$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
							if($row['type']=="idate")
							{
							$a=$a.$utils->getmydate($datarow[$row['name']]);
							$a=$a.$ui->tg_ip_id.$row['name'].$cnt.$ui->tg_class;
							$a=$a.$ui->tg_ip_cl.$ui->tg_dat.$row['name'].$cnt.$ui->tg_dat_cl.$ui->tg_td_cl;
							}
							else
							{$a=$a.$datarow[$row['name']].$ui->tg_ip_cl.$ui->tg_td_cl;}
					}
//close if condition
				}	
//close inner while
		}
			for($i=0;$i<sizeof($arr_show);$i++)
				{
				$k=is_array($arr_show[$i])?$arr_show[$i][0]:$arr_show[$i];
				$val=is_array($arr_show[$i])?$arr_show[$i][1]:NULL;
				$j=explode(',',$tmp);
				if(!in_array($k,explode(',',$tmp)))
				$a=$a.$ui->tg_td.$ui->tg_ip.$ui->tg_ip_type.'text'.$ui->tg_ip_name.$k.$cnt.$ui->tg_ip_size.'10'.$ui->tg_ip_value.$val.$ui->tg_ip_cl.$ui->tg_td_cl;
				}
		$cnt++;
		$a=$a.$ui->tg_ro_cl."\n";
		}
//Close Table
		$a=$a.$ui->tg_top_cl;
		$a=$a."<input type=\"number\" name=\"icnt\" value=$cnt style=\"display:none\">";
		$a=$a."<input type=\"hidden\" name=\"field_edit\" value=\"".implode(",",$arr)."\" >";
//Close Mode
	}
if($mode>=2)
	{
	//Open Table
	//echo $ui->tg_top;
	//Print Header column
	$nfield=0;
	$split=0;
	$a=$ui->tg_row.$ui->tg_col1.$ui->tg_div_cl;
	//echo $ui->tg_col;
	$a=$a.$ui->tg_col;$b=$ui->tg_col;
	if($mode==3)
	$c=$ui->tg_col;
	$d=$ui->tg_div_cl.$ui->tg_row.$ui->tg_col1.$ui->tg_div_cl.$ui->tg_span;	
	$cnt=0;
		// while($row = mysql_fetch_array($result1))
		foreach($result1 as $row)
		{
		if($row['span']==2)
		{$d=$d.$ui->tg_ip_gp.$row['name'].$ui->tg_cl.$row['alias'].$ui->tg_ip_gp_cl;$split=1;}
		else{
		if($row['col']==2)
		$b=$b.$ui->tg_ip_gp.$row['name'].$ui->tg_cl.$row['alias'].$ui->tg_ip_gp_cl;
		elseif($row['col']==3)
		$c=$c.$ui->tg_ip_gp.$row['name'].$ui->tg_cl.$row['alias'].$ui->tg_ip_gp_cl;
		else
		$a=$a.$ui->tg_ip_gp.$row['name'].$ui->tg_cl.$row['alias'].$ui->tg_ip_gp_cl;
		}

	//Print data columns
		
		$j=1;
		//while($datarow=mysql_fetch_array($result_data))
		
		if(empty($arr_show) || in_array($row['name'],$arr_show))
		{		
		if($row['dbindex']=='primary' || !in_array($row['name'], $arr))
		//if($row['dbindex']=='primary')
		{
		$x=$ui->tg_ip.$ui->tg_ip_type.$ui->tg_hidden.$ui->tg_ip_class.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
		//if($row['type']=="idate")
			//			{
				//		$x=$x.getmydate($datarow[$row['name']]).$ui->tg_ip_cl;
			//			$x=$x.$ui->tg_static.getmydate($datarow[$row['name']]).$ui->tg_static_cl.$ui->tg_div_cl.$ui->tg_div_cl;
				//		}else
					//	{
						$x=$x.$ui->tg_ip_cl;
						//$x=$x.$ui->tg_static.$datarow[$row['name']].$ui->tg_static_cl.$ui->tg_div_cl.$ui->tg_div_cl;
						//}
		
		$x=$x.$ui->tg_static.$ui->tg_static_cl.$ui->tg_div_cl.$ui->tg_div_cl;
		}elseif($row['type']=="textarea")
					{
						$x=$x.$ui->tg_text.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_ip_cl;
						$x=$x.$ui->tg_text_cl;
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
												
													{$alias='';$alias=$row_opt['alias'];}
												}
										//}
										//below handles code if no value is set and also instead of value shows alias
										$alias=isset($alias)?$alias:NULL;
										$x=$x.$ui->tg_opt.$ui->tg_cl.$alias.$ui->tg_opt_cl;
										//introducing for clear value option
										$x=$x.$ui->tg_opt.$ui->tg_cl.$ui->tg_opt_cl;
										$x=$x.$opt[$j].$ui->tg_sel_cl.$ui->tg_div_cl;
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
												
													{$alias=$row_opt['alias'];}
												}
											}
										//}
										//below handles code if no value is set and also instead of value shows alias
										$alias=(isset($alias)&&($alias!=null))?$alias:"--NONE--";
										// echo "alias is".$alias;
										$x=$x.$ui->tg_opt.$ui->tg_cl.$alias.$ui->tg_opt_cl;
										//introducing for clear value option
										$x=$x.$ui->tg_opt."".$ui->tg_cl."--NONE--".$ui->tg_opt_cl;
										$x=$x.$opt[$j].$ui->tg_sel_cl.$ui->tg_div_cl;
					$j++;
					}			
		else
		{
		$x=$ui->tg_ip.$ui->tg_ip_type.$row['type'].$ui->tg_ip_class.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
							if($row['type']=="idate")
							{
							
							$x=$x.$ui->tg_ip_id.$row['name'].$cnt.$ui->tg_ip_class.$ui->tg_class;
							$x=$x.$ui->tg_ip_cl.$ui->tg_dat.$row['name'].$cnt.$ui->tg_dat_cl.$ui->tg_div_cl;
							}
							else
							{$x=$x.$ui->tg_ip_cl.$ui->tg_div_cl;}
			}
		
		if($row['span']==2)
		{$d=$d.$x;}
		else{
		if($row['col']==2)
		$b=$b.$x;
		elseif($row['col']==3)
		$c=$c.$x;
		else
		$a=$a.$x;
		}
		
		}
		
		
		
		}$cnt++;
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
	
	}
	//only get pages if this is main table
	if($_GET['page']==$tbl)
        {
            $del='';$bk='';
            $nav="<div class=\"navbar navbar-default\" role=\"navigation\"><div class=\"col-md-8\">";
            $bk="<div class=\"col-md-4\"><button class=\"btn btn-default\" id=\"btn_back\" type=\"submit\" value=\"back\" ><span class=\"glyphicon glyphicon-chevron-left\"></span></button></div>"; 
            $upd="<div class=\"col-md-4\"><button class=\"btn btn-default\" id=\"btn\" type=\"submit\" value=\"update\" >Update</button></div>";
        if($_SESSION['SESS_perm']=='admin')
$del="<div class=\"col-md-4\"><button class=\"btn btn-danger\" id=\"btn_del\" type=\"submit\" value=\"delete\">Delete</button></div>";
//$a=$z."</div>".$a.$z."</div>";
        //$bk='';
$nav=$nav.$bk.$del.$upd."</div></div>";
$a=$nav.$a.$nav;
        }

//$a=$a."&nbsp<input class=\"btn btn-danger\" id=\"btn\" type=\"submit\" name=\"update\" value=\"Update\">";
return $a;
}
public function insertRelatedData($id,$data){
	if(isset($data['releated_tables']) && !empty($data['releated_tables'])){
		// print_r($data);
		foreach($data['releated_tables'] as $related_table){
			$utils = new Utils();
			$sql1=$this->dbsql($related_table);
			$result1=$this->allArray($sql1);
				$i=0;
			$user_id = $_SESSION['SESS_id'];
			$field_edit = $data[$related_table.'_edit'];
			$rel_id = $data[$related_table.'_rel_id'];
			$field_edit = explode(',',$field_edit);
			while($i < $data[$related_table.'_cnt']){
				$fname="";	$fval="";
				foreach($result1 as $row)
				{
					if(!$row['dbindex']=='primary')
					{
						$sys_field = array('created_at','modified_at','created_by','modified_by',$rel_id);
						if(isset($_POST[$row['name'].$i]) || in_array($row['name'],$sys_field)) 
						{
							$fname=$fname.$row['name'].",";

							if($row['name'] == $rel_id){
								$field_name =  $rel_id;
								$fval=$fval."".$id.",";
							}elseif($row['name'] == 'created_at'){
								$field_name= isset($_POST[$row['name'].$i]) ? $_POST[$row['name'].$i] : time();
								$fval=$fval."".time().",";
							}
							elseif($row['name'] == 'modified_at'){
								$field_name=isset($_POST[$row['name'].$i])?$_POST[$row['name'].$i]:time();;
								$fval=$fval."".time().",";
								
							}
							elseif($row['name'] == 'created_by'){
								$field_name=isset($_POST[$row['name'].$i]) ?$_POST[$row['name'].$i]: $user_id;
								$fval=$fval."".$user_id.",";
							}
							elseif($row['name'] == 'modified_by'){
								$field_name=isset($_POST[$row['name'].$i])?$_POST[$row['name'].$i]:$user_id;
								$fval=$fval."".$user_id.",";
							}
							elseif($row['type']=='idate')
							{
								// if($field_name)
								if(isset($_POST[$row['name'].$i])){
									$field_name=$_POST[$row['name'].$i];
									$fval=$fval."".$utils->setmydate($field_name).",";
								}else{
									$fval=$fval."NULL,";
								}
							}
							elseif($row['type']=='ihtml'){
								//	$field_name=$field_name.$row['name']."_filter,";
								$message = isset($_POST[$row['name'].$i]) ? addslashes($_POST[$row['name'].$i]) : '';
								$message_filter = $utils->cleanup($message);
								// $fval=$fval."'".mysql_real_escape_string(stripslashes($message))."',";
								$fval=$fval."'".stripslashes($message)."',";
								//$fval = $fval."'".$message_filter."',";
								// echo $fval;exit;
							}
							elseif($row['type']=='password')
							{
								// if($field_name)
								$field_name=$_POST[$row['name'].$i];
								$fval=$fval."'".sha1($field_name)."',";
							}
							elseif($row['type'] == 'file'){
								// if(isset($_FILES[$row['name'].$i]))
								$file_name = $this->uploadFile( $_FILES[$row['name'].$i],$tbl);
								if($file_name) $fval=$fval."'".$file_name."',";
								else $fval = $fval."'',";
							}else
							{
								if(isset($_POST[$row['name'].$i])){
									
									$field_name=$_POST[$row['name'].$i];
									if($row['dbtype'] =='int' || $row['dbtype'] =='decimal' ){
										if($field_name) $fval=$fval.$field_name.",";
										else $fval=$fval."0,";
									}else{
										
										$fval=$fval."'".addslashes($field_name)."',";
									}
								}else
								{
									if($row['dbtype'] =='int' || $row['dbtype'] =='decimal' ){
										$fval=$fval."0,";}else{
											$fval=$fval."'',";	
										}
								}
									//echo "fval is".$row['name'];
							}
						}
					}
				}
				$fname=chop($fname,",");
				$fval=chop($fval,",");
				// echo $fval;exit;
				// echo '<br>--'.$i.'--<br>';
				$this->executeQuery("INSERT INTO ".$related_table." ( ".$fname." ) VALUES (".$fval.")");
				$i++;
			}
		}
	}
	// exit;
}



}


?>
