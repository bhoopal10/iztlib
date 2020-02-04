<?php
// require_once('lib/triggers.php');
require_once('relatedforms.php');
require_once('AppController.php');
require_once('UiConstant.php');
require_once('Utils.php');
class AppView extends CoreView{
	function input($tbl,$qual,$arr,$arr_show,$mode=1)
	{
		$ui = new UiConstant($tbl);
		$utils = new Utils();
		$sql1=$this->dbsql($tbl);
	
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
		// $tg_sel="<select id=\"";
		// $tg_cl="\" >";
		// $tg_div_cl = "</div>";
		// $tg_sel_cl="</select>";
		// $tg_opt="<option value=\"";
		// $tg_opt_cl="</option>";
		// $tg_hidden="hidden";
		// $tg_readonly="\" readonly ";
		// $tg_text="<textarea rows=\"4\" cols=\"15\" class=\"\"";
		// $tg_text_cl="</textarea>";
	
		// $result1=mysql_query($sql1);
		$result1=$this->allArray($sql1);
		$data_sql="select * from ".$tbl;
		if(isset($qual) && trim($qual))
			{
			$qual=" where ".$qual;
			$data_sql=$data_sql.$qual;
		}
		//echo $data_sql;
		$pageSql = $utils->getPagesql($data_sql,7,$tbl,'asc');
	$data_sql = $pageSql['sql'];
	echo $pageSql['pagination'];
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
			// $result_data=mysql_query($data_sql);
			$result_data=$this->allArray($data_sql);
			// while($datarow=mysql_fetch_array($result_data))
			foreach($result_data as $datarow)
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
			// $result_data=mysql_query($data_sql);
			$result_data=$this->allArray($data_sql);
			$cnt=0;
			// while($datarow=mysql_fetch_array($result_data))
			foreach($result_data as $datarow)
			{
			$j=1;
	
			$a=$a.$ui->tg_ro;
	
			//Input for checkbox
			//echo $datarow['status'];
			// Start Added by Anshul for no Check box in paystate
			if($tbl == 'paystate'){
				if($datarow['status'] == 'Open'){
					$a=$a.$ui->tg_td.$ui->tg_chk.$cnt.$ui->tg_chk_val;
					$a=$a.$ui->tg_ip_cl.$ui->tg_td_cl;
					}
				else{$a=$a.$ui->tg_td;
					$a=$a;}
			}
			// End Added by Anshul for no Check box in paystate
			
			// Start Added by Anshul for no Check box in Bank
			elseif($tbl == 'bank'){
				if($datarow['status'] == 'Loaded'){
					$a=$a.$ui->tg_td.$ui->tg_chk.$cnt.$ui->tg_chk_val;
					$a=$a.$ui->tg_ip_cl.$ui->tg_td_cl;
					}
				else{$a=$a.$ui->tg_td;
					$a=$a;}
			}
			
			// End Added by Anshul for no Check box in Bank
			
			// Start Added by Anshul for no Check box in Payroll
			elseif($tbl == 'payroll'){
				if($datarow['status'] == 'Open'){
					$a=$a.$ui->tg_td.$ui->tg_chk.$cnt.$ui->tg_chk_val;
					$a=$a.$ui->tg_ip_cl.$ui->tg_td_cl;
					}
				else{$a=$a.$ui->tg_td;
					$a=$a;}
			}
			// End Added by Anshul for no Check box in Paroll
			
			else{
			$a=$a.$ui->tg_td.$ui->tg_chk.$cnt.$ui->tg_chk_val;
			$a=$a.$ui->tg_ip_cl.$ui->tg_td_cl;}
			
	
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
						elseif($row['type']=="ihtml")
						{
								$a=$a.$ui->tg_td.$ui->tg_text.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_ip_cl;
								$a=$a.$datarow[$row['name']].$ui->tg_text_cl.$ui->tg_div_cl;
						} 
						elseif($row['type']=="option")
						{					$a=$a.$ui->tg_td.$ui->tg_sel.$row['name'].$cnt.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_cl;
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
							$sql_filter="select source,filter,id,value,alias,multi_option from valuelist where id in (select optid from field_option where tblid=".$row['tblid']." and fieldid=".$row['fieldid'].")";
							// echo $sql_filter.';';
							// $result_filter=mysql_query($sql_filter);
							$result_filter=$this->firstArray($sql_filter);
							// $vsource=mysql_result($result_filter,0,0);
							// $vfilter=mysql_result($result_filter,0,1);
							// $vid=mysql_result($result_filter,0,2);
							// $vvalue=mysql_result($result_filter,0,3);
							// $valias=mysql_result($result_filter,0,4);
							$vmulti_opt= $result_filter['multi_option'];
							// echo $vmulti_opt.'test';
							if($vmulti_opt==1)
								
							{
									$_tmp = $utils->getField('list',$row['tblid'],$row['fieldid'],$cnt,$datarow,$row['name']);
									$a = $a.$ui->tg_td.$_tmp.$ui->tg_td_cl;
	
								}
								else{
									$a=$a.$ui->tg_td.$ui->tg_sel.$row['name'].$cnt.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_cl;
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
								{$a=$a.htmlspecialchars($datarow[$row['name']]).$ui->tg_ip_cl.$ui->tg_td_cl;}
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
	
		//only get pages if this is main table
	if($_GET['page'] == 'bank'){}
	elseif($_GET['page'] == 'load_master'){}
	elseif($_GET['page'] == 'payroll'){
	$a=$a."&nbsp<input class=\"btn btn-danger\" id=\"btn\" type=\"submit\" name=\"cancel_payroll\" value=\"Cancel Payroll\">&nbsp;&nbsp;&nbsp;"; 
	$a=$a."<input class=\"btn btn-primary\" id=\"btn\" type=\"submit\" name=\"close_payroll\" value=\"Close Payroll\">";}
	elseif($_GET['page'] == 'paystate'){$a=$a."&nbsp<input class=\"btn btn-danger\" id=\"btn\" type=\"submit\" name=\"update\" value=\"Close Paystate\">";}
	else{	
	if($_GET['page']==$tbl){
	$a=$a."&nbsp<input class=\"btn btn-danger\" id=\"btn\" type=\"submit\" name=\"update\" value=\"Update\">";}
	if($_SESSION['SESS_perm']=='sys_admin'){
	$a=$a."&nbsp<input class=\"btn btn-danger\" id=\"btn_del\" type=\"submit\" name=\"delete\" value=\"Delete\">";}}
	return $a;
	
	}

// written to show fields in arranged format in input mode
function input_new($tbl,$qual,$arr,$arr_show,$mode,$access_mode=false)
{
	// $arr = getTblQualWorkflow($tbl,$qual,$access_mode);
	// add_statusbar($tbl,$qual);//moved from update.php to lib/statusbar_lib.php
	// $activitylog = activityLog($tbl,$qual);//activityLog moved from update.php to lib/activitylog_lib.php
	// $arr = ($field_access) ? explode(',',$field_access) : array();
	// $x=explode('=',$qual);
	// $pid=$x[1];
	$appC = new AppController();
	$utils = new Utils();
	$ui = new UiConstant($tbl);
	$sql1=$this->dbsql($tbl);
	// echo $sql1;
	$col_wid=0;$col_space=0;
	if($mode==2)
	{$col_wid=4;$col_space=2;
	}
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
	
	// $tg_sel_tbl="<select name=\"tableid\"  id=\"table\">";
	// $tg_sel_field="<select name=\"fieldid\"  id=\"field\">";

	// $tg_opt="<option value=\"";
	// $tg_opt_cl="</option>";
	// $tg_hidden="hidden";
	// $tg_readonly="\" readonly ";
	// $tg_text="<textarea rows=\"4\" cols=\"15\" ";
	// $tg_text_cl="</textarea>";

	// // Variables added to define a tag
	// $tg_a = "<a ";
	// $tg_href = "href = \"";
	// $tg_a_cl_bar = ">";
	// $tg_a_cl = "</a>";
	// $tg_a_target = "\"target = '_blank' ";

	$data_sql="select * from ".$tbl;
	
	// $result1=mysql_query($sql1) or die(mysql_error());
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
		// $result_data=mysql_query($data_sql);
		$result_data=$this->allArray($data_sql);
		// while($datarow=mysql_fetch_array($result_data))
		foreach($result_data as $datarow)
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
		// $result_data=mysql_query($data_sql);
		$result_data=$this->allArray($data_sql);
		$cnt=0;
		// while($datarow=mysql_fetch_array($result_data))
		foreach($result_data as $datarow)
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
						// $a=$a.$ui->tg_td.$ui->tg_sel.$row['name'].$cnt.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_cl;
						//adding new on13/12/2014.can be deleted
						$sql_filter="select source,filter,id,value,alias,multi_option from valuelist where id in (select optid from field_option where tblid=".$row['tblid']." and fieldid=".$row['fieldid'].")";
						// echo $sql_filter;
						$result_filter=$this->firstArray($sql_filter);
							$vsource= $result_filter['source'];
							$vfilter= $result_filter['filter'];
							$vid= $result_filter['id'];
							$vvalue= $result_filter['value'];
							$valias= $result_filter['alias'];
							$vmulti_opt=$result_filter['multi_option'];
							
						// echo $vmulti_opt.'test';
						// if($vmulti_opt==1)
						// 	{$a = $a.getField('list',$row['tblid'],$row['fieldid'],$a,$cnt);}
						// 	else{
								$a=$a.$ui->tg_td.$ui->tg_sel_control.$row['name'].$cnt.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_cl;
								// 			//adding new on13/12/2014.can be deleted
								// 			$sql_filter="select source,filter,id,value,alias from valuelist where id in (select optid from field_option where tblid=".$row['tblid']." and fieldid=".$row['fieldid'].")";
								// 			//echo $sql_filter;
								// 			$result_filter=mysql_query($sql_filter);
								// 			$vsource=mysql_result($result_filter,0,0);
								// 			$vfilter=mysql_result($result_filter,0,1);
								// 			$vid=mysql_result($result_filter,0,2);
								// 			$vvalue=mysql_result($result_filter,0,3);
								// 			$valias=mysql_result($result_filter,0,4);
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
									// }
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
		// if($mode==3)
		$c=$ui->tg_col;
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
			if(empty($arr_show)||in_array($row['name'],$arr_show))
			{
			if($row['span']==2)
			{
				 $d=$d.$ui->$tg_ip_gp.$row['name'].$ui->tg_cl.$row['alias'].$ui->tg_ip_gp_cl;$split=1;
			}
			elseif($row['col']==2)
			{
				$b=$b.$ui->$tg_ip_gp.$row['name'].$ui->tg_cl.$row['alias'].$ui->tg_ip_gp_cl;
			}
			elseif($row['col']==3)
			{
					$c=$c.$ui->$tg_ip_gp.$row['name'].$ui->tg_cl.$row['alias'].$ui->tg_ip_gp_cl;
			}else{
			// if(in_array($row['name'],$arr_show))
			$a=$a.$ui->$tg_ip_gp.$row['name'].$ui->tg_cl.$row['alias'].$ui->tg_ip_gp_cl;			
			}
		// echo $row['alias'];
			}
	//Print data columns
	// $result_data=mysql_query($data_sql) or die(mysql_error());
	$result_data=$this->allArray($data_sql);
	$j=1;
	// while($datarow=mysql_fetch_array($result_data))
	foreach($result_data as $datarow)
	{
		
		if(empty($arr_show) || in_array($row['name'],$arr_show))
		//if(empty($arr) || in_array($row['name'],$arr))
		{	
			
			if($row['dbindex']=='primary' || !in_array($row['name'], $arr))
			//if($row['dbindex']=='primary')
			{
				
			 		//if($row['type']=="idate")
					//			{
						//		$x=$x.getmydate($datarow[$row['name']]).$ui->tg_ip_cl;
						//			$x=$x.$ui->tg_static.getmydate($datarow[$row['name']]).$tog_static_cl.$ui->tg_div_cl.$ui->tg_div_cl;
						//		}else
					 
						// echo $ui->tg_ip_cl;
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
						//$x=$x.$ui->tg_static.$datarow[$row['name']].$ui->tg_static_cl.$ui->tg_div_cl.$ui->tg_div_cl;
						//	{
							//}
							
							//$x=$x.$ui->tg_static.$datarow[$row['name']].$ui->tg_static_cl.$ui->tg_div_cl;
						$x=$x.$ui->tg_div_cl;
							 
		}
		elseif($row['type']=="textarea")
		{
			 $x=$x.$ui->tg_text.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_ip_cl;
			 $x=$x.$datarow[$row['name']].$ui->tg_text_cl;
		}	
		elseif($row['type']=="ihtml")
		{
			$x=$ui->tg_text.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_class.' ihtml'.$ui->tg_ip_cl;
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
										 $x=$x.$opt[$j].$ui->tg_sel_cl.$ui->tg_div_cl;
					$j++;
					}
		elseif($row['type']=="list")
					{
						
						 $x=$ui->tg_sel_control.$row['name'].$cnt.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_cl;
											//adding new on13/12/2014.can be deleted
											$sql_filter="select source,filter,id,value,alias from valuelist where id in (select optid from field_option where tblid=".$row['tblid']." and fieldid=".$row['fieldid'].")";
											// echo $sql_filter;
											$result_filter=$this->firstArray($sql_filter);
											if($result_filter){

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
												$x=$x.$opt[$j].$ui->tg_sel_cl.$ui->tg_div_cl;
											}else{
												$x=$x.$ui->tg_opt."".$ui->tg_cl."--NONE--".$ui->tg_opt_cl.$ui->tg_sel_cl.$ui->tg_div_cl;
											}
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
				$x=$x.$ui->tg_td;
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
				$x=$x.$ui->tg_td_cl;
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
									$x=$x.$ui->tg_ip_cl.$ui->tg_dat.$row['name'].$cnt.$ui->tg_dat_cl.$ui->tg_div_cl;
							}
							else
							{
									$x=$x.$datarow[$row['name']].$ui->tg_ip_cl.$ui->tg_div_cl;
							}
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
			 
		}
		
		}$cnt++;
                  $x=$ui->tg_td.$ui->tg_chk."0".$ui->tg_chk_val."\" hidden checked=\"checked";
		$x=$x.$ui->tg_ip_cl.$ui->tg_td_cl;
		$a=$a.$x.$ui->tg_div_cl.$ui->tg_colspace.$b.$ui->tg_div_cl;
		if($mode==3)
		$a=$a.$ui->tg_colspace.$c.$ui->tg_div_cl;
		if($split==1)
		$a=$a.$d.$ui->tg_div_cl;
		$a=$a.$ui->tg_col1.$ui->tg_div_cl.$ui->tg_div_cl;
	//Close Table
		//echo $ui->tg_top_cl;

		//table dbrule----------------------
		if($tbl=="dbrule")
		{
			$tbl_query="select * from config where alias not in ('config','field','field_option','access','groups','Users','Events','valuelist')";
				// $result=mysql_query($tbl_query) or die(mysql_error());
				$result=$this->allArray($tbl_query);
				// $rowscount=mysql_num_rows($result);			
				$rowscount=count($result);			
			$a=$a."<label for=\"tableid\">Table </label>";
			$a=$a."<select name=\"tableid\" class=\"form-control \" id=\"table\">";
			$a=$a."<option value=\"\">Select Table</option>";
			
				if($rowscount > 0)
				{
					// while($row=mysql_fetch_assoc($result))
					foreach($result as $row)
					{
						$a=$a.'<option value="'.$row['tblid'].'">'.$row['alias'].'</option>';

					}
				}
				else
					$a=$a.'<option value="">Table Not Available</option>';

			
			$a=$a."</select>";
			$a=$a."<br>";
			$a=$a."<label for=\"fieldid\">Field </label>";

			$a=$a."<select name=\"fieldid\" class=\"form-control\"  id=\"field\">";
			$a=$a.'<option value="">Select Field</option>';

			$a=$a."</select>";
			$a=$a."<br>";

		}	
		//dbrule closing----------------------	

		$a=$a."<input type=\"number\" name=\"icnt\" value=$cnt style=\"display:none\">";
		$a=$a."<input type=\"hidden\" name=\"field_edit\" value=\"".implode(",",$arr)."\" >";
	
	}
	//only get pages if this is main table
	if($_GET['page']==$tbl)
        {
            $del='';$bk='';
            $nav="<div class=\"navbar navbar-default\" role=\"navigation\"><div class=\"col-md-8\">";
            $bk="<div class=\"col-md-4\"><button class=\"btn btn-default\" id=\"btn_back\" type=\"submit\" name=\"back\" value=\"back\"><span class=\"glyphicon glyphicon-chevron-left\"></span></button></div>"; 
            $upd="<div class=\"col-md-4\"><button class=\"btn btn-default\" id=\"btn_update\" type=\"submit\" name=\"updates\" value=\"update\" >Update</button></div>";
        if($_SESSION['SESS_perm']=='user')
//$del="<div class=\"col-md-4\"><button class=\"btn btn-danger\" id=\"btn_del\" type=\"submit\" value=\"delete\">Delete</button></div>";
//$a=$z."</div>".$a.$z."</div>";
        $bk='';
$nav=$nav.$bk.$upd."</div></div>";
$a=$a.$nav;
        }

// $a=$a."&nbsp<input class=\"btn btn-danger\" id=\"btn\" type=\"submit\" name=\"update\" value=\"Update\"><br>";
 
return $a;
}

function display_array($tbl,$qual,$md,$arr,$status=false,$page_size=7)
{
	$ui = new UiConstant($tbl);
	$utils= new Utils();
	$sql1=$this->dbsql($tbl);


	$data_sql="select * from ".$tbl;
	 
	if(isset($qual))
	{
		$_qual = $qual;
		// if( $status !== false){
		// 	$qual = " and status = {$status}";
		// }
		if(!trim($qual)) $qual = ' 1=1';
	$qual=" where ".$qual;
	$data_sql=$data_sql.$qual;
	}
	//only get pages if this is main table
	if($_GET['page']==$tbl){

		$pageSql = $utils->getPagesql($data_sql,$page_size,$tbl,'ASC','id',$_qual);
		$data_sql = $pageSql['sql'];
		echo $pageSql['pagination'];
	}
	// echo $data_sql;
	// $result1=mysql_query($sql1);
	$result1=$this->allArray($sql1);
	//  print_r($result1);
//mode 0 is columnar and mode 1 for row-wise printing
	$a="";
	$opt=array(1 => "k");
	$mode=1;
	$j=0;
	$mode=$md;
	 
	 
	if($mode==0)
	{
		 
	//Open Table
	$a=$ui->tg_top;
 
	//Print Header column

		 
		// while($row = mysql_fetch_array($result1))
		foreach($result1 as $row)
		{
		$a=$a.$ui->tg_ro;
		$a=$a.$ui->tg_hdr.$row['alias'].$ui->tg_hdr_cl;

	//Print data columns

		// $result_data=mysql_query($data_sql);
		$result_data=$this->allArray($data_sql);
		// while($datarow=mysql_fetch_array($result_data))
		foreach($result_data as $datarow)
		{
			 
		if($row['type']=="idate")
		{
		$a=$a.$ui->tg_td.$utils->getmydate($datarow[$row['name']]).$ui->tg_td_cl;
		}elseif($row['type']=="password"){
		$a=$a.$ui->tg_td.$ui->tg_td_cl;
		}
		else
		{
		$a=$a.$ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl;
		}
		}

		$a=$a.$ui->tg_ro_cl;
		}
	//Close Table
		$a=$a.$ui->tg_top_cl;
	}
	if($mode==1)
	{
//Open Table 

		$a= $ui->tg_top;

//Print Header row
		$a=$a.$ui->tg_ro;
		
		
		$hdr=array();
		// while($row = mysql_fetch_array($result1))
		foreach($result1 as $row)
		{
			 
		if((empty($arr) || in_array($row['name'],$arr)) )
		$a=$a.$ui->tg_hdr.$row['alias'].$ui->tg_hdr_cl;
		$hdr[$row['name']]=$row['alias'];
		}
		$a=$a.$ui->tg_ro_cl;
	 
//var_dump($hdr);
//echo $hdr['id'];
$record=array();$rec=array();
//Print Data rows
		$result_data=$this->allArray($data_sql);

		// while($datarow=mysql_fetch_array($result_data))
		foreach($result_data as $datarow)
		{
		$a=$a.$ui->tg_ro;
		$result1=$this->allArray($sql1);
		// while($row = mysql_fetch_array($result1))
		foreach($result1 as $row)
		{
			if(empty($arr) || in_array($row['name'],$arr))
			{
				
				if($row['type']=="idate")
				{$a=$a.$ui->tg_td.$utils->getmydate($datarow[$row['name']]).$ui->tg_td_cl;
					$rec[$row['name']]=$utils->getmydate($datarow[$row['name']]);
					/*$record[$datarow[$row['name']]]=array($row['name']=>getmydate($datarow[$row['name']]))*/
				}	
				
		//$a=$a.$ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl;
		elseif($row['type']=="password"){

		$a=$a.$ui->tg_td.$ui->tg_td_cl;
		}elseif($row['dbindex']=="primary" ){
			 
		
			$rec[$row['name']]=$datarow[$row['name']];
			 
                //$record[$datarow[$row['name']]]=array($row['name']=>$datarow[$row['name']]);
		$a=$a.$ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl;
		$a=$a.$ui->tg_ip.$ui->tg_ip_type.$ui->tg_hidden.$ui->tg_ip_name.$row['name'].$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
		$a=$a.$datarow[$row['name']].$ui->tg_ip_cl;
		 
		}else
		{$a=$a.$ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl;
		$rec[$row['name']]=$datarow[$row['name']];/*$record[$datarow[$row['name']]]=array($row['name']=>$datarow[$row['name']])*/;
		}	
	}elseif($row['dbindex']=="primary" && !in_array($row['name'],$arr))
		{
		 
		$a=$a.$ui->tg_ip.$ui->tg_ip_type.$ui->tg_hidden.$ui->tg_ip_name.$row['name'].$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
		$a=$a.$datarow[$row['name']].$ui->tg_ip_cl;
		//$rec=$rec.$row['name'].'=>'.$datarow[$row['name']];
                //$record[$datarow[$row['name']]]=array($row['name']=>$datarow[$row['name']]);
		}
		//echo $a;
		//close while loop
             
		 
		}
				//chop($rec,",");
			 
                $record[]=$rec;
				unset($rec);
				$a=$a.$ui->tg_ro_cl;
				
			}
			//Close Table
			$a=$a.$ui->tg_top_cl;
	}
//var_dump($record);
	//$a=$a."<input class=\"btn btn-warning\" id=\"btn\" type=\"submit\" name=\"modify\" value=\"modify\">";
	//return $a;
		 //  print_r($record);exit;
		// return "test"; 
        return $record;
}

function display_link($tbl,$qual,$md,$arr,$link='',$search=array())
{
	$utils = new Utils();
	$ui = new UiConstant($tbl);
	// $qual=createSearchBuilder($tbl,$qual,false,false,$search);
	$sql1=$this->dbsql($tbl);
	// $tg_top="<div class=\"table-responsive\"><table class=\"table table-hover\" id=\"".$tbl."\" width=auto >";
	// 	$tg_hdr="<th>";
	// 	$tg_hdr_cl="</th>";
	// 	//$tg_ro="<tr class=\"clickableRow\" href=\"".$link."constant\">";
	// 	$tg_ro="<tr class=\"clickableRow\" href=\"\" onclick=\"openUser(constant);\">";
	// 	$tg_ro_cl="</tr>";
	// 	$tg_td="<td>";
	// 	$tg_td_cl="</td>";
	// $tg_top_cl="</table></div>";

	// //$tg_top="<div><ul class=\"nav nav-tabs nav-stacked\" id=\"".$tbl."\" >";
	// $tg_hdr="<th>";
	// $tg_hdr_cl="</th>";
	// $tg_al="<a href=\"#\" onclick=\"openPage(";
	// $tg_alo=");\" >";
	// $tg_alc="</a>";
	//echo "<a href=\"#\" onclick=\"openUser(".$_SESSION['SESS_empid'].");\" >;
	//$tg_ro="<tr href=\"#\" onclick=\"openUser(".$_SESSION['SESS_empid'].");\">";
	//$tg_ro_cl="</tr>";
	//$tg_td="<li><a href=\"".$link."constant\">";
	//$tg_td_cl="</a></li>";
	//$tg_top_cl="</ul></div>";
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
	// $tg_dat_cl="','yyyymmdd')\"><img src=datetimepick/cal.gif width=16 height=16 border=0 alt=Pick a date></a>";
	// $tg_sel="<select name=\"";
	// $tg_cl="\" >";
	// $tg_sel_cl="</select>";
	// $tg_opt="<option value=\"";
	// $tg_opt_cl="</option>";
	// $tg_hidden="hidden";
	// $tg_readonly="\" readonly ";
	// $tg_text="<textarea rows=\"4\" cols=\"15\" ";
	// $tg_text_cl="</textarea>";

	$data_sql="select * from ".$tbl;
	$main_qual = $qual;
	if(trim($qual))
	{
		preg_match_all('/\{\{.+?\}\}/',$qual,$matches, PREG_SET_ORDER);
		foreach ($matches as $val) { 
			$oldval = $val[0]; 
			$newval = preg_replace('/\{\{(.+?)\}\}/', '$1', $val[0]); 
			$qual = str_replace($oldval, strtotime($newval), $qual); 
		}
		// echo $qual;
		$qual=" where ".$qual;
		$data_sql=$data_sql.$qual;
	}
	// echo $data_sql;
	//only get pages if this is main table
	if($_GET['page']==$tbl)
	$pageSql = $utils->getPagesql($data_sql,7,$tbl,'asc','id',$main_qual);
	$data_sql = $pageSql['sql'];
	// echo $data_sql;
	echo $pageSql['pagination'];
$result1=$this->allArray($sql1);
//mode 0 is columnar and mode 1 for row-wise printing
	$a="";
	$opt=array(1 => "k");
	$mode=1;
	$j=0;

	$mode=$md;

	if($mode==0)
	{
	//Open Table
	$a=$ui->tg_top;
	//Print Header column

		// while($row = mysql_fetch_array($result1))
		foreach($result1 as $row)
		{
		$a=$a.$ui->tg_ro;
		$a=$a.$ui->tg_hdr.$row['alias'].$ui->tg_hdr_cl;

	//Print data columns

		$result_data=$this->allArray($data_sql);
		// while($datarow=mysql_fetch_array($result_data))
		foreach($result_data as $datarow)
		{
		if($row['type']=="idate")
		{
		$a=$a.$ui->tg_td.$utils->getmydate($datarow[$row['name']]).$ui->tg_td_cl;
		}elseif($row['type']=="password"){
		$a=$a.$ui->tg_td.$ui->tg_td_cl;
		}
		else
		{
		$a=$a.$ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl;
		}
		}

		$a=$a.$ui->tg_ro_cl;
		}
	//Close Table
		$a=$a.$ui->tg_top_cl;
		if(empty($result_data)){ $a .= "<p>No Data</p>"; }
	}
	if($mode==1)
	{
		// echo 'model1';
//Open Table
		$a= $ui->tg_top;

//Print Header row
		$a=$a."<tr>";
		// while($row = mysql_fetch_array($result1))
		foreach($result1 as $row)
		{
		if((empty($arr) || in_array($row['name'],$arr)) )
		$a=$a.$ui->tg_hdr.$row['alias'].$ui->tg_hdr_cl;
		}
		$a=$a."</tr>";

//Print Data rows
		$result_data=$this->allArray($data_sql);
		// while($datarow=mysql_fetch_array($result_data))
		// echo $data_sql;
		foreach($result_data as $datarow)
		{
		$a=$a.$ui->tg_ro;
		$result1=$this->allArray($sql1);
		// while($row = mysql_fetch_array($result1))
		foreach($result1 as $row)
		{
		if(empty($arr) || in_array($row['name'],$arr))
		{
		if($row['type']=="idate")
		$a=$a.$ui->tg_td.$utils->getmydate($datarow[$row['name']]).$ui->tg_td_cl;
		//$a=$a.$ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl;
		elseif($row['type']=="password"){
		$a=$a.$ui->tg_td.$ui->tg_td_cl;
		}
		elseif($row['type']=="file" )//////changes
		{
			 
				$a=str_replace('constant',$datarow[$row['name']],$a);
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
				$a=$a.$ui->tg_td;
				foreach($file_names as $file_name)
				{
					$file="./".$file_path.$file_name;
					$a=$a."<a href='".$file_name."' target=\"_blank\">".$file_name."</a><br>";
					// echo $a;exit;
					// $a=$a.$ui->tg_ip.$ui->tg_ip_type.$ui->tg_hidden.$ui->tg_ip_name.$row['name'].$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
					// $a=$a.$datarow[$row['name']].$ui->tg_ip_cl;
				}
				$a=$a.$ui->tg_td_cl;
			}
   } ////changes
                elseif($row['type']=="option")
		{$j=1;
		$a=$a.$ui->tg_td;		
		$sql_opt="select * from field_option where tblid=".$row['tblid']." and fieldid=".$row['fieldid']." and value like '".$datarow[$row['name']]."'";
		        $result_opt=$this->allArray($sql_opt);
				// while($row_opt = mysql_fetch_array($result_opt))
					foreach($result_opt as $row_opt)
					{$alias='';
					$alias=$row_opt['alias'];
					}
                                $alias=isset($alias)?$alias:NULL;
				$a=$a.$alias.$ui->tg_td_cl;
				$j++;
                 }elseif($row['type']=="list")
					{
                    $a=$a.$ui->tg_td;
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
						$vfilter= $vfilter==null?" where 1=2 ":" where ".$utils->parseFilter($vfilter);
						$sql_opt="select ".$vvalue." as value,".$valias." as alias from ".$vsource.$vfilter." and ".$vvalue."='".$datarow[$row['name']]."'";
						// echo $sql_opt;	
						//Completion of change
						$result_opt=$this->allArray($sql_opt);
						// echo $result_opt;
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
						// echo $alias;
						$alias=(isset($alias)&&($alias!=null))?$alias:"";
						//echo "alias is".$alias;
						$a=$a.$alias.$ui->tg_td_cl;
						$j++;
					}
					}
                elseif($row['dbindex']=="primary" ){
		$a=str_replace('constant',$datarow[$row['name']],$a);
		$a=$a.$ui->tg_td."<a onclick=openRec(".$datarow[$row['name']].",'".$tbl."'".") href=\"#\"".">".$datarow[$row['name']]."</a>".$ui->tg_td_cl;
		$a=$a.$ui->tg_ip.$ui->tg_ip_type.$ui->tg_hidden.$ui->tg_ip_name.$row['name'].$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
		$a=$a.$datarow[$row['name']].$ui->tg_ip_cl;
		}else
		$a=$a.$ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl;
		}elseif($row['dbindex']=="primary" && !in_array($row['name'],$arr))
		{
		$a=str_replace('constant',$datarow[$row['name']],$a);
		$a=$a.$ui->tg_ip.$ui->tg_ip_type.$ui->tg_hidden.$ui->tg_ip_name.$row['name'].$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
		$a=$a.$datarow[$row['name']].$ui->tg_ip_cl;
		}
		//echo $a;
		//close while loop
		}

		$a=$a.$ui->tg_ro_cl;
		}
//Close Table
		$a=$a.$ui->tg_top_cl;
		if(empty($result_data)){
			$a .= "<p>No Data</p>";
		}
	}
	if($mode==2)
	{
//Open Table
		$a= $ui->tg_top;

//Print Header row
		$a=$a."<tr>";
		// while($row = mysql_fetch_array($result1))
		foreach($result1 as $row)
		{
		if((empty($arr) || in_array($row['name'],$arr)) )
		$a=$a.$ui->tg_hdr.$row['alias'].$ui->tg_hdr_cl;
		}
		$a=$a."</tr>";

//Print Data rows
		$result_data=$this->allArray($data_sql);
		// while($datarow=mysql_fetch_array($result_data))
		foreach($result_data as $datarow)
		{
		$a=$a.$ui->tg_ro;
		// $result1=mysql_query($sql1);
		$result1=$this->allArray($sql1);
		// while($row = mysql_fetch_array($result1))
		foreach($result1 as $row)
		{
		if(empty($arr) || in_array($row['name'],$arr))
		{
		if($row['type']=="idate")
		$a=$a.$ui->tg_td.$utils->getmydate($datarow[$row['name']]).$ui->tg_td_cl;
		//$a=$a.$ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl;
		elseif($row['type']=="password"){
		$a=$a.$ui->tg_td.$ui->tg_td_cl;
		}
		elseif($row['type']=="file" )//////changes
					{
						$a=str_replace('constant',$datarow[$row['name']],$a);
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
							$a=$a.$ui->tg_td;
							foreach($file_names as $file_name)
							{
								$file="./".$file_path.$file_name;
								$a=$a."<a href='".$file_name."' target=\"_blank\">".$file_name."</a><br>";
								// echo $a;exit;
								// $a=$a.$ui->tg_ip.$ui->tg_ip_type.$ui->tg_hidden.$ui->tg_ip_name.$row['name'].$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
								// $a=$a.$datarow[$row['name']].$ui->tg_ip_cl;
							}
							$a=$a.$ui->tg_td_cl;
						}
   					} ////changes
		elseif($row['dbindex']=="primary" ){
		$a=str_replace('constant',$datarow[$row['name']],$a);
		$a=$a.$ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl;
		$a=$a.$ui->tg_ip.$ui->tg_ip_type.$ui->tg_hidden.$ui->tg_ip_name.$row['name'].$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
		$a=$a.$datarow[$row['name']].$ui->tg_ip_cl;
		}else
		$a=$a.$ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl;
		}elseif($row['dbindex']=="primary" && !in_array($row['name'],$arr))
		{
		$a=str_replace('constant',$datarow[$row['name']],$a);
		$a=$a.$ui->tg_ip.$ui->tg_ip_type.$ui->tg_hidden.$ui->tg_ip_name.$row['name'].$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
		$a=$a.$datarow[$row['name']].$ui->tg_ip_cl;
		}
		//echo $a;
		//close while loop
		}

		$a=$a.$ui->tg_ro_cl;
		}
//Close Table
		$a=$a.$ui->tg_top_cl;
		if(empty($result_data)){
			$a .= "<p>No Data</p>";
		}
	}
	return $a;
}

function display_array_alias($tbl,$qual,$md,$arr,$status=false){
	$ui = new UiConstant($tbl);
	$utils= new Utils();
	$sql1=$this->dbsql($tbl);
	$data_sql="select * from ".$tbl;
	if(isset($qual))
	{
		$_qual = $qual;
		if(!trim($qual)) $qual = ' 1=1';
		$qual=" where ".$qual;
		$data_sql=$data_sql.$qual;
	}
	//only get pages if this is main table
	$pageSql = $utils->getPagesql($data_sql,7,$tbl,'ASC','id',$_qual);
	$data_sql = $pageSql['sql'];
	echo $pageSql['pagination'];
	$result1=$this->allArray($sql1);
	//mode 0 is columnar and mode 1 for row-wise printing
	$a="";
	$opt=array(1 => "k");
	$mode=1;
	$j=0;
	$mode=$md;
	
	if($mode==1)
	{
		//Open Table 
		$hdr=array();
		foreach($result1 as $row)
		{
			if((empty($arr) || in_array($row['name'],$arr)) ){

			}
			$hdr[$row['name']]=$row['alias'];
		}
		$record=array();$rec=array();
		//Print Data rows
		$result_data=$this->allArray($data_sql);
		foreach($result_data as $datarow)
		{
			$result1=$this->allArray($sql1);
			foreach($result1 as $row)
			{
				if(empty($arr) || in_array($row['name'],$arr))
				{
					
					if($row['type']=="idate"){
						$rec[$row['name']]=$utils->getmydate($datarow[$row['name']]);
					}elseif($row['type']=="password"){

					}elseif($row['dbindex']=="primary" ){
						$rec[$row['name']]=$datarow[$row['name']];
					}elseif($row['type']=="list"){
						$list_val = $utils->getFirstListAlias($row['fieldid'],$datarow[$row['name']]);
						$rec[$row['name']] = $list_val;
					}elseif($row['type']=="option"){
						$list_val = $utils->getFirstListAlias($row['fieldid'],$datarow[$row['name']]);
						$rec[$row['name']] = $list_val;
					}else{
						$a=$a.$ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl;
						$rec[$row['name']]=$datarow[$row['name']];/*$record[$datarow[$row['name']]]=array($row['name']=>$datarow[$row['name']])*/;
					}	
				}elseif($row['dbindex']=="primary" && !in_array($row['name'],$arr)) {

				}
				//close while loop
			}
			$record[]=$rec;
			unset($rec);
			
		}
		//Close Table
		$a=$a.$ui->tg_top_cl;
	}
    return $record;
}
function execute_data($tbl,$qual,$join,$select_fields,$relfields=array('relid','parentrecid')){
	$utils= new Utils();
	$sql1=$this->dbsql($tbl);
	$data_sql = "select {$tbl}.*{$select_fields} from ".$tbl.$join;
	if(isset($qual))
	{
		$_qual = $qual;
		if(!trim($qual)) $qual = ' 1=1';
		$qual=" where ".$qual;
		$data_sql = $data_sql.$qual;
	}
	$result1=$this->allArray($sql1);
	$result_data=$this->allArray($data_sql);
	$record=array();
	// echo $data_sql;
	foreach($result_data as $datarow){

		foreach($result1 as $row){
			if($row['type']=="text"){
				$rec[$row['name']] = $datarow[$row['name']];
			}else{
				$rec[$row['name']] = $utils->getAlias($tbl,$row['name'],$datarow[$row['name']]);
			}
		}
		$record[]=$rec;
	}
	return $record;


}
function display_raw($tbl,$qual,$md,$arr,$status=false)
{
	$ui = new UiConstant($tbl);
	$utils= new Utils();
	$sql1=$this->dbsql($tbl);
	$data_sql="select * from ".$tbl;
	if(isset($qual))
	{
		$_qual = $qual;
		if(!trim($qual)) $qual = ' 1=1';
		$qual=" where ".$qual;
		$data_sql=$data_sql.$qual;
	}
	$result1=$this->allArray($sql1);
	// print_r($result1);
	$a="";
	$opt=array(1 => "k");
	$mode=1;
	$j=0;
	$mode=$md;
	
	if($mode==1)
	{
		$record=array();
		//Print Data rows
		$result_data=$this->allArray($data_sql);
		foreach($result_data as $datarow)
		{
			$rec=array();
			foreach($result1 as $row)
			{
				if(empty($arr) || in_array($row['name'],$arr))
				{
					if($row['type']=="idate")
					{
						$rec[$row['name']]=$utils->getmydate($datarow[$row['name']]);
						/*$record[$datarow[$row['name']]]=array($row['name']=>getmydate($datarow[$row['name']]))*/
					}elseif($row['type']=="password"){

					}elseif($row['type'] == 'list'){
						// $rec[$row['name']] = $utils->getAlias($tbl,$row['name'],$datarow[$row['name']]);
						$rec[$row['name']] = $utils->getFirstListAlias($row['fieldid'],$datarow[$row['name']]);
						$rec[$row['name'].'_value'] = $datarow[$row['name']];
					}elseif($row['type'] == 'option'){
						if($row['name'] == 'status'){
							$rec[$row['name']] = $datarow[$row['name']];
						}else{
							$rec[$row['name']] = $utils->getOptionAlias($row['fieldid'],$datarow[$row['name']]);
						}
					}elseif($row['dbindex']=="primary" ){
						$rec[$row['name']]=$datarow[$row['name']];
					}else{
						$rec[$row['name']]=$datarow[$row['name']];
					}	
				}elseif($row['dbindex']=="primary" && !in_array($row['name'],$arr)){

				}
			}
			$record[] = $rec;
		}
	}
        return $record;
}
function display_rawReport($tbl,$qual,$md,$arr,$status=false)
{
	$ui = new UiConstant($tbl);
	$utils= new Utils();
	$sql1=$this->dbsql($tbl);
	$data_sql="select * from ".$tbl;
	$main_qual = $qual;
	if(trim($qual))
	{
		preg_match_all('/\{\{.+?\}\}/',$qual,$matches, PREG_SET_ORDER);
		foreach ($matches as $val) { 
			$oldval = $val[0]; 
			$newval = preg_replace('/\{\{(.+?)\}\}/', '$1', $val[0]); 
			$qual = str_replace($oldval, strtotime($newval), $qual); 
		}
		// echo $qual;
		$qual=" where ".$qual;
		$data_sql=$data_sql.$qual;
	}
	//only get pages if this is main table
	$pageSql = $utils->getPagesql($data_sql,7,$tbl,'asc','id',$main_qual);
	$data_sql = $pageSql['sql'];
	echo $pageSql['pagination'];
	$result1=$this->allArray($sql1);
	$a="";
	$opt=array(1 => "k");
	$mode=1;
	$j=0;
	$mode=$md;
	if($mode==0)
	{
	//Open Table
	$a=$ui->tg_top;
 
	//Print Header column

		 
		// while($row = mysql_fetch_array($result1))
		foreach($result1 as $row)
		{
		$a=$a.$ui->tg_ro;
		$a=$a.$ui->tg_hdr.$row['alias'].$ui->tg_hdr_cl;

	//Print data columns

		// $result_data=mysql_query($data_sql);
		$result_data=$this->allArray($data_sql);
		// while($datarow=mysql_fetch_array($result_data))
		foreach($result_data as $datarow)
		{
			 
		if($row['type']=="idate")
		{
		$a=$a.$ui->tg_td.$utils->getmydate($datarow[$row['name']]).$ui->tg_td_cl;
		}elseif($row['type']=="password"){
		$a=$a.$ui->tg_td.$ui->tg_td_cl;
		}elseif($row['type']=="list"){
		$a=$a.$ui->tg_td.$ui->tg_td_cl;
		}elseif($row['type']=="option"){
		$a=$a.$ui->tg_td.$ui->tg_td_cl;
		}
		else
		{
		$a=$a.$ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl;
		}
		}

		$a=$a.$ui->tg_ro_cl;
		}
	//Close Table
		$a=$a.$ui->tg_top_cl;
	}
	if($mode==1)
	{
//Open Table 

		$a= $ui->tg_top;

//Print Header row
		$a=$a.$ui->tg_ro;
		
		
		$hdr=array();
		// while($row = mysql_fetch_array($result1))
		foreach($result1 as $row)
		{
			 
		if((empty($arr) || in_array($row['name'],$arr)) )
		$a=$a.$ui->tg_hdr.$row['alias'].$ui->tg_hdr_cl;
		$hdr[$row['name']]=$row['alias'];
		}
		$a=$a.$ui->tg_ro_cl;
	 
$record=array();$rec=array();
//Print Data rows
		$result_data=$this->allArray($data_sql);
		// while($datarow=mysql_fetch_array($result_data))
		foreach($result_data as $datarow)
		{
		$a=$a.$ui->tg_ro;
		$result1=$this->allArray($sql1);
		// while($row = mysql_fetch_array($result1))
		foreach($result1 as $row)
		{
			if(empty($arr) || in_array($row['name'],$arr))
			{
				
				if($row['type']=="idate")
				{$a=$a.$ui->tg_td.$utils->getmydate($datarow[$row['name']]).$ui->tg_td_cl;
					$rec[$row['name']]=$utils->getmydate($datarow[$row['name']]);
					/*$record[$datarow[$row['name']]]=array($row['name']=>getmydate($datarow[$row['name']]))*/
				}	
				
		//$a=$a.$ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl;
		elseif($row['type']=="password"){

		$a=$a.$ui->tg_td.$ui->tg_td_cl;
		}elseif($row['dbindex']=="primary" ){
			 
		
			$rec[$row['name']]=$datarow[$row['name']];
			 
                //$record[$datarow[$row['name']]]=array($row['name']=>$datarow[$row['name']]);
		$a=$a.$ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl;
		$a=$a.$ui->tg_ip.$ui->tg_ip_type.$ui->tg_hidden.$ui->tg_ip_name.$row['name'].$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
		$a=$a.$datarow[$row['name']].$ui->tg_ip_cl;
		 
		}else
		{$a=$a.$ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl;
		$rec[$row['name']]=$datarow[$row['name']];/*$record[$datarow[$row['name']]]=array($row['name']=>$datarow[$row['name']])*/;
		}	
	}elseif($row['dbindex']=="primary" && !in_array($row['name'],$arr))
		{
		 
		$a=$a.$ui->tg_ip.$ui->tg_ip_type.$ui->tg_hidden.$ui->tg_ip_name.$row['name'].$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
		$a=$a.$datarow[$row['name']].$ui->tg_ip_cl;
		}
		}
                $record[]=$rec;
				unset($rec);
				$a=$a.$ui->tg_ro_cl;
				
			}
			//Close Table
			$a=$a.$ui->tg_top_cl;
	}
        return $record;
}
function display_report($tbl,$qual,$md,$arr,$link='',$search=array())
{
	$utils = new Utils();
	$ui = new UiConstant($tbl);
	$sql1=$this->dbsql($tbl);
	$data_sql="select * from ".$tbl;
	$main_qual = $qual;
	if(trim($qual))
	{
		preg_match_all('/\{\{.+?\}\}/',$qual,$matches, PREG_SET_ORDER);
		foreach ($matches as $val) { 
			$oldval = $val[0]; 
			$newval = preg_replace('/\{\{(.+?)\}\}/', '$1', $val[0]); 
			$qual = str_replace($oldval, strtotime($newval), $qual); 
		}
		// echo $qual;
		$qual=" where ".$qual;
		$data_sql=$data_sql.$qual;
	}
	//only get pages if this is main table
	$pageSql = $utils->getPagesql($data_sql,7,$tbl,'asc','id',$main_qual);
	$data_sql = $pageSql['sql'];
	echo $pageSql['pagination'];
$result1=$this->allArray($sql1);
//mode 0 is columnar and mode 1 for row-wise printing
	$a="";
	$opt=array(1 => "k");
	$mode=1;
	$j=0;

	$mode=$md;

	if($mode==1)
	{
		$a= $ui->tg_top;

	//Print Header row
		$a=$a."<tr>";
		foreach($result1 as $row)
		{
		if((empty($arr) || in_array($row['name'],$arr)) )
		$a=$a.$ui->tg_hdr.$row['alias'].$ui->tg_hdr_cl;
		}
		$a=$a."</tr>";
		$result_data=$this->allArray($data_sql);
		foreach($result_data as $datarow)
		{
		$a=$a.$ui->tg_ro;
		$result1=$this->allArray($sql1);
		foreach($result1 as $row)
		{
		if(empty($arr) || in_array($row['name'],$arr))
		{
		if($row['type']=="idate")
		$a=$a.$ui->tg_td.$utils->getmydate($datarow[$row['name']]).$ui->tg_td_cl;
		elseif($row['type']=="password"){
		$a=$a.$ui->tg_td.$ui->tg_td_cl;
		}
		elseif($row['type']=="file" )//////changes
		{
			 
				$a=str_replace('constant',$datarow[$row['name']],$a);
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
				$a=$a.$ui->tg_td;
				foreach($file_names as $file_name)
				{
					$file="./".$file_path.$file_name;
					$a=$a."<a href='".$file_name."' target=\"_blank\">".$file_name."</a><br>";
				}
				$a=$a.$ui->tg_td_cl;
			}
   } ////changes
                elseif($row['type']=="option")
		{$j=1;
		$a=$a.$ui->tg_td;		
		$sql_opt="select * from field_option where tblid=".$row['tblid']." and fieldid=".$row['fieldid']." and value like '".$datarow[$row['name']]."'";
		        $result_opt=$this->allArray($sql_opt);
				// while($row_opt = mysql_fetch_array($result_opt))
					foreach($result_opt as $row_opt)
					{$alias='';
					$alias=$row_opt['alias'];
					}
                                $alias=isset($alias)?$alias:NULL;
				$a=$a.$alias.$ui->tg_td_cl;
				$j++;
                 }elseif($row['type']=="list")
					{
                    $a=$a.$ui->tg_td;
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
						$vfilter= $vfilter==null?" where 1=2 ":" where ".$utils->parseFilter($vfilter);
						$sql_opt="select ".$vvalue." as value,".$valias." as alias from ".$vsource.$vfilter." and ".$vvalue."='".$datarow[$row['name']]."'";
						// echo $sql_opt;	
						//Completion of change
						$result_opt=$this->allArray($sql_opt);
						// echo $result_opt;
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
						// echo $alias;
						$alias=(isset($alias)&&($alias!=null))?$alias:"";
						//echo "alias is".$alias;
						$a=$a.$alias.$ui->tg_td_cl;
						$j++;
					}
					}
                elseif($row['dbindex']=="primary" ){
			$a=str_replace('constant',$datarow[$row['name']],$a);
			$a=$a.$ui->tg_td."<a onclick=openRec(".$datarow[$row['name']].",'".$tbl."'".") href=\"#\"".">".$datarow[$row['name']]."</a>".$ui->tg_td_cl;
			$a=$a.$ui->tg_ip.$ui->tg_ip_type.$ui->tg_hidden.$ui->tg_ip_name.$row['name'].$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
			$a=$a.$datarow[$row['name']].$ui->tg_ip_cl;
		}else
		$a=$a.$ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl;
		}elseif($row['dbindex']=="primary" && !in_array($row['name'],$arr))
		{
		$a=str_replace('constant',$datarow[$row['name']],$a);
		$a=$a.$ui->tg_ip.$ui->tg_ip_type.$ui->tg_hidden.$ui->tg_ip_name.$row['name'].$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
		$a=$a.$datarow[$row['name']].$ui->tg_ip_cl;
		}
		//echo $a;
		//close while loop
		}

		$a=$a.$ui->tg_ro_cl;
		}
//Close Table
		$a=$a.$ui->tg_top_cl;
		if(empty($result_data)){
			$a .= "<p>No Data</p>";
		}
	}
	
	return $a;
}

function dashboardView($array_data,$tbl,$display_fields,$primaryfield,$status_date_fields=array(),$show_label=true,$status_field ='status',$clm=3,$childtbl=false,$last_column=1)
{
	$utils = new Utils();
	$truncat_num = 14;
	if($clm == 4) $truncat_num = 45;
	$first_clm  = 11;
	if($last_column == 2){
		$first_clm  = 10;
	}
	 $access = $_SESSION['SESS_access'];
	 $modifyall = $this->first("select modifyall from access where page_name = '{$tbl}' and groupname = '{$access}'");
	 $modifyaccess ="";
	 $b ="";
	 if($modifyall) $modifyaccess  = $modifyall->modifyall;
	 
	$a="";
	$st=array("0"=>"Draft","1"=>"Requested","2"=>"SentBack","3"=>"Re-requested","4"=>"Ordered","5"=>"Dispatched","6"=>"Rejected","7"=>"Cancelled","8"=>"Delivered","9"=>"Sentback","10"=>"Arrived","11"=>"Unloaded");
	$st_date=array("0"=>"created_at","1"=>"order_date","2"=>"sentback_time","3"=>"reordered_time","4"=>"confirmed_time","5"=>"dispatch_date","6"=>"rejected_time","7"=>"cancelled_time","8"=>"truck_arrival_date","9"=>"sentback_time","10"=>"truck_arrival_date","11"=>"unload_date");
	$imaget=array("0"=>"draft.png","1"=>"ordered.png","2"=>"sentback.png","3"=>"reorder.png","4"=>"confirm-order.png","5"=>"dispatch.png","6"=>"rejected.png","7"=>"cancel.png","8"=>"delivered.png","9"=>"reorder_dispatch.png","10"=>"delivered.png","11"=>"unloaded.png");    
	if($modifyaccess){
		 $b.= "<div class=\"panel panel-default\" style=\"background-color:#f4f9fb\"><div class=\"panel-body\">";
		 $b.= "<input type=\"checkbox\" name=\"orders_checkbox\">&nbsp;Select All &nbsp;&nbsp;&nbsp; "; 
		 $b.= "<button type='button' class='btn btn-info' name='acceptall'>Accept Selected Orders</button>"; 
		 $b.="</div></div><div id=\"order-list\">";
		 $a .= $b;
	}
	foreach($array_data as $field_value){
	// echo"<pre>"; print_r($field_value['id']);
		$a.= "<div class=\"col-md-12\"><div class=\"panel panel-default\" style=\"border-radius: 25px 25px 25px 25px;background-color:#f4f9fb\">";
		$a.= "<div class=\"panel-body target_id_".$field_value['id']."\">";
		// primary clickable link
		$a .="<div class=\"row\">";
		$a .="<div class=\"col-md-{$first_clm} col-xs-12\">";
		$img = "";
		if(isset($primaryfield['img'])){
			$src = $field_value[$primaryfield['img']];

			if(trim($src)){
				$img= "<img src='{$src}' alt='Image' width='150' height='40' class='pad-5' />";
			}else{
				$src = 'images/nologo-text.jpg';
				$img= "<img src='{$src}' alt='Image' width='150' height='40' class='pad-5' />";
			}
		}
		// check id badge is availble
		$badge = "";
		if(isset($primaryfield['badge'])){
			$badge = "<div class='member'> <span class='member-initials' title='".$field_value['assigned_to_alias']."'>{$field_value[$primaryfield['badge']]}</span></div>";
		}
		// check id modified is availble
		$modified = "";
		if(isset($primaryfield['modified'])){
			$modified = '<small class="pull-right">'.$field_value[$primaryfield['modified']].'</small>';
		}
		$a.= "<div class='col-md-{$clm} col-xs-8 p0 '><a class='dashboard-link' onclick=openRec(".$field_value[$primaryfield['link']].",'".$tbl."'".") href=\"#\"".">".$img."<label class='tilemargin'>{$primaryfield['alias']} ".$utils->getAlias($tbl,$primaryfield['field'],$field_value[$primaryfield['field']])."</label></a>".
			 "</div>";	  
		$i = 1;
		foreach($display_fields as $key=>$display_field){
			if($i == ($clm-2)) $clear = "<div class='clearfix hidden-xs hidden-sm'></div>";
			else $clear = '';
			$i++;
			if((trim($key) !='') || (trim($utils->getAlias($tbl,$display_field,$field_value[$display_field])) !='')){
				$a.= "<div class='col-md-{$clm} col-xs-4 p0 text-center'>";
				if($key	==	'Value'){
					$key	=	"<em class='fa fa-inr'>&nbsp;</em>";
				}
				if($display_field	==	'next_action_date'){
					$datestyle	=	'date-style';
				}else{
					$datestyle	=	'';
				}
				if($show_label) if(trim($key) !='') $a.= "<label class='label-".$display_field." tilemargin'>{$key}</label>";
				if(trim($utils->getAlias($tbl,$display_field,$field_value[$display_field])) !='')	$a .= "<span class='tilemargin ".$datestyle."  value-".$display_field."' title='".$utils->getAlias($tbl,$display_field,$field_value[$display_field])."'>".$utils->truncate($utils->getAlias($tbl,$display_field,$field_value[$display_field]),$truncat_num).	"</span>";
				$a .= "</div>";	
				$a .= $clear;
			}elseif($display_field == 'next_action_date'){
				$a.= "<div class='col-md-{$clm} col-xs-4 p0 text-center'>";
				$a .="<span class='tilemargin ".$datestyle."  value-".$display_field."'>".$field_value[$display_field]."</span>";
				$a .="</div>";
				$a .= $clear;
			}
			else{

				$a.= "<div class='col-md-{$clm} col-xs-4 p0 text-center'>";
				$a .="<span class='tilemargin ".$datestyle."  value-".$display_field."' title='".$utils->getAlias($tbl,$display_field,$field_value[$display_field])."'>-</span>";
				$a .="</div>";
				$a .= $clear;
			}
			
		}
		if(isset($field_value[$status_field]) && isset($status_date_fields[$field_value[$status_field]])){
			$img = isset($status_date_fields[$field_value[$status_field]]) ? 'images/d-'.$status_date_fields[$field_value[$status_field]]['img']:'';
			$title = isset($status_date_fields[$field_value[$status_field]]) ? $status_date_fields[$field_value[$status_field]]['title']:'';
		}else{
			$img= 'images/noimage.png';
			$title= 'noimage';
		}
		$a	.= 	"<div class='col-md-{$clm} col-xs-4 p0 hidden-md hidden-lg' onclick=openRec(".$field_value[$primaryfield['link']].",'".$tbl."'".")> 
					<span class='tilemargin'><img width='25' src='".$img."' alt='".$title."' title='".$title."' /></span>
					<p><small>".$title."</small></p>
				</div>";
		$a.= "</div>";
		
		// data-toggle='modal' data-target='#tasks' aria-controls='tasks'
		if($img) {
			$a .= "<div class='col-md-{$last_column} hidden-xs hiddem-sm text-center' style='padding-top:5px;'>
						<div class='col-md-12 pointer' style='margin-left: -14px !important;'  onclick=openRec(".$field_value[$primaryfield['link']].",'".$tbl."'".")>
							<span><img width='25' src='".$img."' alt='".$title."' title='".$title."' /></span>
							<p><small>".$title."</small></p>
						</div>";
			if($childtbl	==	'tasks'){
			$a .=		"<div class='col-md-12' style='margin-left: -14px !important;'>
							{$badge}
							<a title='Tasks' data-toggle='modal' data-target='#tasks' aria-controls='tasks' data-table='".$tbl."' data-id='".$field_value['id']."' class='tasksdata' id='tasks".$field_value['id']."' style='padding-left: 10px;'>
								<span class='glyphicon glyphicon-plus'></span>
							</a>
							{$modified}
						</div>";
			// $a .= $modified;
			// $a .= $badge;

			}
			$a .=		"</div>";
			$a.= "</div>";
		}else { 
			$a .= "<div class='col-md-{$last_column}'></div>";
		}

		$a .= "<div class='clearfix'></div>";
		$a.= "</div>";
		$a.= "</div>";
		$a.= "</div>";
	}
	if(empty($array_data)){
		$a.= "<div>No Data</div>";
	}
	if($modifyaccess) $a.= "</div>";
	return $a;
}

}

?>