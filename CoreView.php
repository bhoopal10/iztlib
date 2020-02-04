<?php
// require_once('searchbuilder_new.php');
require_once('UiConstant.php');
require_once('DbModel.php');
require_once('Utils.php');
class CoreView extends DbModel{
function dbsql($tbl)
{
	
$sql="select tblid from config where name='$tbl'";
$result=$this->firstArray($sql);
// if(mysql_num_rows($result) > 0)
// {
// $myvar=mysql_result($result,0) or die(mysql_error());
$myvar = $result['tblid'];
 
$sql1="select * from field where tblid=$myvar";
//$result1=mysql_query($sql1);

 
return $sql1;
}

function display($tbl,$qual,$md,$arr,$comment=null,$search=array())
{ 
	$utils = new Utils();
	$ui = new UiConstant($tbl);
	// $qual=createSearchBuilder($tbl,$qual,false,false,$search);
	$sql1=$this->dbsql($tbl);

	//adding new below 2 rows
	// $tg_nrow="<div class=\"row clearfix\">";
	// $tg_ncol="<div class=\"col-md-4 column\">";
	// $tg_top="<div class=\"table-responsive\"><table class=\"table table-hover table-bordered table-striped\" id=\"".$tbl."\" width=auto cellpadding=2 cellspacing=2>";
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
	// //adding for image and attachments fields
	// //<div>  <img src="https://media.licdn.com/mpr/mpr/shrinknp_200_200/p/3/000/0b7/06d/0df966e.jpg" alt="Vipul Gupta" height="200" width="200"></div>
	// $tg_img="<div><img src=\"";
	// $tg_img_alt="\" alt=\"";
	// $tg_img_ht="\" height=\"";
	// $tg_img_wd="\" width=\"";
	// $tg_img_cl="\"></div>"; 
    //     $tg_thead="<thead>";
    //     $tg_thead_cl="</thead>";
        
	$data_sql="select * from ".$tbl;
	// var_dump($qual);
	$main_qual = trim($qual);
	if(isset($qual) && trim($qual))
	{
		preg_match_all('/\{\{.+?\}\}/',$qual,$matches, PREG_SET_ORDER);
		foreach ($matches as $val) { 
			$oldval = $val[0]; 
			$newval = preg_replace('/\{\{(.+?)\}\}/', '$1', $val[0]); 
			$qual = str_replace($oldval, strtotime($newval), $qual); 
		}
	$qual=" where ".$qual;
	$data_sql=$data_sql.$qual;
	}
	//only get pages if this is main table
	if($_GET['page']==$tbl)
	$pageSql = $utils->getPagesql($data_sql,7,$tbl,'asc',NULL,$main_qual);
	$data_sql = $pageSql['sql'];
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
			 
		$a=$a.$tg_ro;
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
		if(empty($result_data)){ $a .= "<p>No Data </p>"; }
	}

	if($mode==1)
	{
//Open Table
		$a= '<p>&nbsp;</p>'.$ui->tg_top;
		//Print Header row
		$a=$a.$ui->tg_thead.$ui->tg_ro;
		// while($row = mysql_fetch_array($result1))
		foreach($result1 as $row)
		{
			
			if((empty($arr) || in_array($row['name'],$arr)) )
			{
				 
				$a=$a.$ui->tg_hdr.$row['alias'].$ui->tg_hdr_cl;
			}
		}
		$a=$a.$ui->tg_ro_cl.$ui->tg_thead_cl;
		$customer_id;
//Print Data rows
// echo $data_sql;
		$result_data=$this->allArray($data_sql);	
		// while($datarow=mysql_fetch_array($result_data))
		foreach($result_data as $datarow)
		{
			//$datarow['id'];
			$a=$a.$ui->tg_ro;
			$result1=$this->allArray($sql1);
			// while($row = mysql_fetch_array($result1))
			foreach($result1 as $row)
			{
				//echo $row['name'];
				
				//echo $datarow[$row['name']];
				
				
				
				
				$row['size'] = 10;
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
						if($path && !empty($path))
						{
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
									$a=$a."<a href='".$file_name."'   target=\"_blank\">".$file_name."</a><br>";
									// echo $a;exit;
									// $a=$a.$ui->tg_ip.$ui->tg_ip_type.$ui->tg_hidden.$ui->tg_ip_name.$row['name'].$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
									// $a=$a.$datarow[$row['name']].$ui->tg_ip_cl;
								}
								$a=$a.$ui->tg_td_cl;
							}
						}	
   					} ////changes
					elseif($row['type']=="option")
					{	$j=1;$a=$a.$ui->tg_td;
                                        //$a=$a.$ui->tg_opt.$datarow[$row['name']].$ui->tg_cl.$datarow[$row['name']].$ui->tg_opt_cl;
                                        //if($cnt==0)
                                        //{
                                                $sql_opt="select * from field_option where tblid=".$row['tblid']." and fieldid=".$row['fieldid'];

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
                                        $a=$a.$alias;
                                        //introducing for clear value option
                                        
                                        $a=$a.$ui->tg_td_cl;
					$j++;
					}
                elseif($row['type']=="list")
					{
                    $a=$a.$ui->tg_td;
							//adding new on13/12/2014.can be deleted
							$sql_filter="select source,filter,id,value,alias from valuelist where id in (select optid from field_option where tblid=".$row['tblid']." and fieldid=".$row['fieldid'].")";
							//  echo $sql_filter.'<br>';
							// $a .= $sql_filter;
							$result_filter=$this->firstArray($sql_filter);
							if($result_filter){

								$vsource= $result_filter['source'];
								$vfilter= $result_filter['filter'];
								$vid= $result_filter['id'];
								$vvalue= $result_filter['value'];
								$valias= $result_filter['alias'];
								// $vmulti_opt=$result_filter['multi_option'];
								$vfilter= $vfilter==null?" where 1=2 ":" where ".$utils->parseFilter($vfilter);
								$sql_opt="select ".$vvalue." as value,".$valias." as alias from ".$vsource.$vfilter;
								// echo $sql_opt;	
								// $a .= $sql_opt;
								//Completion of change
								$result_opt=$this->allArray($sql_opt);
								// print_r($result_opt);
								$opt[$j]="";
								$alias='';
								if($result_opt)
								{
									// while($row_opt = mysql_fetch_array($result_opt))
									// $a .= count($result_opt);
									foreach($result_opt as $row_opt)
									{
										$opt[$j]=$opt[$j].$ui->tg_opt.$row_opt['value'].$ui->tg_cl.$row_opt['alias'].$ui->tg_opt_cl;
										// $alias = $datarow[$row['name']];
										// $alias = $row_opt['alias'];
										if($row_opt['value']==$datarow[$row['name']]) 
										{$alias=$row_opt['alias'];}
									}
								}
								//}
							}else{
									$alias = $datarow[$row['name']];
							}
					//below handles code if no value is set and also instead of value shows alias
					$alias=(isset($alias)&&($alias!=null))?$alias:"";
					// if(isset($vmulti_opt) && $vmulti_opt==1){
					// 	$_tmp = $utils->getField('list',$row['tblid'],$row['fieldid'],0,$datarow,$row['name'],true);
					// 	$a=$a.$_tmp.$ui->tg_td_cl;
					// }else{
						$a=$a.$alias.$ui->tg_td_cl;
					// }
						
					//echo "alias is".$alias;
					$j++;
					}			
                elseif($row['dbindex']=="primary" ){
					$a=$a.$ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl;
					
		$a=$a.$ui->tg_ip.$ui->tg_ip_type.$ui->tg_hidden.$ui->tg_ip_name.$row['name'].$ui->tg_ip_size."10".$ui->tg_ip_value;
		$a=$a.$datarow[$row['name']].$ui->tg_ip_cl;
		$customer_id= $datarow[$row['name']];

		}else
		$a=$a.$ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl;

		}elseif($row['dbindex']=="primary" && !in_array($row['name'],$arr))
		{
		$a=$a.$ui->tg_ip.$ui->tg_ip_type.$ui->tg_hidden.$ui->tg_ip_name.$row['name'].$ui->tg_ip_size."10".$ui->tg_ip_value;
		$a=$a.$datarow[$row['name']].$ui->tg_ip_cl;
		}
		//close while loop
	}
	if($comment)
	{
		//echo $customer_id;
	 $link="<a href=\"home.php?id=$customer_id & page=customer_comments\"".$tbl."\">Comments</a> ";
	 $a=$a.$ui->tg_td.$link;
	}	 
//$a=$a."test";	
}
//Close Table
 $a=$a.$ui->tg_top_cl;
 if(empty($result_data)){ $a .= "<p>No Data</p>"; }
	}
if($mode==2)
	{
	//Open Table
	$a=$ui->tg_top;
	//Print Header column

		// while($row = mysql_fetch_array($result1))
		foreach($result1 as $row)
		{
			$row['size']=10;
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
	if($_GET['page'] == 'payroll'){$a=$a."<button class=\"btn btn-warning\" id=\"btn_modify\" type=\"submit\" name=\"modify\" value=\"modify\">Cancel/Close</button>";}
	elseif($_GET['page'] == 'master'){$a=$a."<button class=\"btn btn-warning\" id=\"btn_modify\" type=\"submit\" name=\"modify\" value=\"modify\">Modify</button>";}
	elseif($_GET['page'] == 'accounts'){}
	else{$a=$a."<button class=\"btn btn-warning\" id=\"btn_modify\" type=\"submit\" name=\"modify\" value=\"modify\">Modify</button>";}
	return $a;
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
	if(isset($qual))
	{
		if(!trim($qual))$qual="1=1";
		preg_match_all('/\{\{.+?\}\}/',$qual,$matches, PREG_SET_ORDER);
		foreach ($matches as $val) { 
			$oldval = $val[0]; 
			$newval = preg_replace('/\{\{(.+?)\}\}/', '$1', $val[0]); 
			$qual = str_replace($oldval, strtotime($newval), $qual); 
		}
		$qual=" where ".$qual;
		$data_sql=$data_sql.$qual;
	}
	//only get pages if this is main table
	if($_GET['page']==$tbl)
	$pageSql = $utils->getPagesql($data_sql,7,$tbl,'asc');
	$data_sql = $pageSql['sql'];
	echo $pageSql['pagination'];
	// echo $data_sql;
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
	}
	if($mode==1)
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
	}
	return $a;
}

function display_array($tbl,$qual,$md,$arr,$status=false)
{
	$ui = new UiConstant($tbl);
	$utils= new Utils();
	$sql1=$this->dbsql($tbl);
	//  echo $tbl;exit;
	// $tg_top="<div class=\"table-responsive\"><table class=\"table hover table-striped\" id=\"".$tbl."\" width=auto border=1 cellpadding=2 cellspacing=2>";
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
	 
	 
	if(isset($qual) && trim($qual))
	{
		// if( $status !== false){
		// 	$qual = " and status = {$status}";
		// }
	$qual=" where ".$qual;
	$data_sql=$data_sql.$qual;
	}
	//only get pages if this is main table
	// if($_GET['page']==$tbl)
	$pageSql = $utils->getPagesql($data_sql,7,$tbl,'asc');
	$data_sql = $pageSql['sql'];
	echo $pageSql['pagination'];
	// echo $sql1;
	// $result1=mysql_query($sql1);
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
		$a=$a.$ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl;$rec[$row['name']]=$datarow[$row['name']];/*$record[$datarow[$row['name']]]=array($row['name']=>$datarow[$row['name']])*/;
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

function display_data($tbl,$qual,$md,$arr)
{
	$utils = new Utils();
	$ui = new UiConstant($tbl);
	// $qual=createSearchBuilder($tbl,$qual);
	$sql1=$this->dbsql($tbl);
	// $tg_top="<div class=\"table-responsive\"><table class=\"table hover table-striped\" id=\"".$tbl."\" width=auto border=1 cellpadding=2 cellspacing=2>";
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

	if(isset($qual))
	{
	$qual=" where ".$qual;
	$data_sql=$data_sql.$qual;
	}
	//only get pages if this is main table
	//if($_GET['page']==$tbl)
	//$data_sql=getPagesql($data_sql,7);
	// $result1=mysql_query($sql1);
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

		$a= $ui->tg_top;

//Print Header row
		$a=$a.$ui->tg_ro;
		
		/*$hdr=array();
		while($row = mysql_fetch_array($result1))
		{
		if((empty($arr) || in_array($row['name'],$arr)) )
		$a=$a.$ui->tg_hdr.$row['alias'].$ui->tg_hdr_cl;
		$hdr[$row['name']]=$row['alias'];
		}
		$a=$a.$ui->tg_ro_cl;
		*/
//var_dump($hdr);
//echo $hdr['id'];
$record=array();
//Print Data rows
		// $result_data=mysql_query($data_sql);
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
		{$a=$a.$ui->tg_td.$utils->getmydate($datarow[$row['name']]).$ui->tg_td_cl;$record[$datarow[$row['name']]]=array($row['name']=>getmydate($datarow[$row['name']]));}	
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
		$record[$datarow[$row['name']]]=array($row['name']=>$datarow[$row['name']]);
		$a=$a.$ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl;
		$a=$a.$ui->tg_ip.$ui->tg_ip_type.$ui->tg_hidden.$ui->tg_ip_name.$row['name'].$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
		$a=$a.$datarow[$row['name']].$ui->tg_ip_cl;
		}else
		$a=$a.$ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl;$record[$datarow[$row['name']]]=array($row['name']=>$datarow[$row['name']]);
		}elseif($row['dbindex']=="primary" && !in_array($row['name'],$arr))
		{
		$a=$a.$ui->tg_ip.$ui->tg_ip_type.$ui->tg_hidden.$ui->tg_ip_name.$row['name'].$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
		$a=$a.$datarow[$row['name']].$ui->tg_ip_cl;
		$record[$datarow[$row['name']]]=array($row['name']=>$datarow[$row['name']]);
		}
		//echo $a;
		//close while loop
		}

		$a=$a.$ui->tg_ro_cl;

		}
//Close Table
		$a=$a.$ui->tg_top_cl;
	}
//var_dump($record);
//$a=json_encode($record);
	//$a=$a."<input class=\"btn btn-warning\" id=\"btn\" type=\"submit\" name=\"modify\" value=\"modify\">";
	return $a;
}

function display_data_text($tbl,$qual,$md,$arr)
{
	// $qual=createSearchBuilder($tbl,$qual);
	$utils = new Utils();
	$sql1=$this->dbsql($tbl);
	$tg_top="";
	$tg_hdr="";
	$tg_hdr_cl="";
	$tg_ro="";
	$tg_ro_cl="";
	$tg_td="";
	$tg_td_cl="";
	$tg_top_cl="";
	$tg_ip="";
	$tg_ip_type="";
	$tg_ip_name="";
	$tg_ip_value="";
	$tg_ip_size="";
	$tg_ip_id="";
	$tg_class="";
	$tg_ip_cl="";
	$tg_chk="<input type=\"checkbox\" name=\"chb";
	$tg_chk_val="";
	$tg_dat="<a href=\"javascript:NewCal('";
	$tg_dat_cl="','yyyymmdd')\"><img src=datetimepick/cal.gif width=16 height=16 border=0 alt=Pick a date></a>";
	$tg_sel="<select name=\"";
	$tg_cl="\" >";
	$tg_sel_cl="</select>";
	$tg_opt="<option value=\"";
	$tg_opt_cl="</option>";
	$tg_hidden="";
	$tg_readonly="\" readonly ";
	$tg_text="<textarea rows=\"4\" cols=\"15\" ";
	$tg_text_cl="</textarea>";

	$data_sql="select * from ".$tbl;

	if(isset($qual))
	{
	$qual=" where ".$qual;
	$data_sql=$data_sql.$qual;
	}
	//only get pages if this is main table
	//if($_GET['page']==$tbl)
	//$data_sql=getPagesql($data_sql,7);
	// $result1=mysql_query($sql1);
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

		$a= $tg_top;

//Print Header row
		$a=$a.$tg_ro;
		
		/*$hdr=array();
		while($row = mysql_fetch_array($result1))
		{
		if((empty($arr) || in_array($row['name'],$arr)) )
		$a=$a.$tg_hdr.$row['alias'].$tg_hdr_cl;
		$hdr[$row['name']]=$row['alias'];
		}
		$a=$a.$tg_ro_cl;
		*/
//var_dump($hdr);
//echo $hdr['id'];
$record=array();
//Print Data rows
		// $result_data=mysql_query($data_sql);
		$result_data=$this->allArray($data_sql);
		// while($datarow=mysql_fetch_array($result_data))
		foreach($result_data as $datarow)
		{
		$a=$a.$tg_ro;
		// $result1=mysql_query($sql1);
		$result1=$this->allArray($sql1);
		// while($row = mysql_fetch_array($result1))
		foreach($result1 as $row)
		{
		if(empty($arr) || in_array($row['name'],$arr))
		{
		if($row['type']=="idate")
		{$a=$a.$tg_td.$utils->getmydate($datarow[$row['name']]).$tg_td_cl;$record[$datarow[$row['name']]]=array($row['name']=>getmydate($datarow[$row['name']]));}	
		//$a=$a.$tg_td.$datarow[$row['name']].$tg_td_cl;
		elseif($row['type']=="password"){
		$a=$a.$tg_td.$tg_td_cl;
		}elseif($row['dbindex']=="primary" ){
		$record[$datarow[$row['name']]]=array($row['name']=>$datarow[$row['name']]);
		//$a=$a.$tg_td.$datarow[$row['name']].$tg_td_cl;
		//$a=$a.$tg_ip.$tg_ip_type.$tg_hidden.$tg_ip_name.$row['name'].$tg_ip_size.$row['size'].$tg_ip_value;
		//$a=$a.$datarow[$row['name']].$tg_ip_cl;
		}else
		$a=$a.$tg_td.$datarow[$row['name']].$tg_td_cl;
                $record[$datarow[$row['name']]]=array($row['name']=>$datarow[$row['name']]);
		}elseif($row['dbindex']=="primary" && !in_array($row['name'],$arr))
		{
		//$a=$a.$tg_ip.$tg_ip_type.$tg_hidden.$tg_ip_name.$row['name'].$tg_ip_size.$row['size'].$tg_ip_value;
		//$a=$a.$datarow[$row['name']].$tg_ip_cl;
		$record[$datarow[$row['name']]]=array($row['name']=>$datarow[$row['name']]);
		}
		//echo $a;
		//close while loop
		}

		$a=$a.$tg_ro_cl;

		}
//Close Table
		$a=$a.$tg_top_cl;
	}
//var_dump($record);
//$a=json_encode($record);
	//$a=$a."<input class=\"btn btn-warning\" id=\"btn\" type=\"submit\" name=\"modify\" value=\"modify\">";
	return $a;
}
function display_total($tbl,$qual,$md,$arr)
{
	// $qual=createSearchBuilder($tbl,$qual);
	$ui = new UiConstant($tbl);
	$utils = new Utils();
 $sql1=$this->dbsql($tbl);
//  $tg_top="<div class=\"table-responsive\"><table class=\"table table-hover table-striped\" id=\"".$tbl."\" width=auto border=1 cellpadding=2 cellspacing=2>";
//  $tg_hdr="<th>";
//  $tg_hdr_cl="</th>";
//  $tg_ro="<tr>";
//  $tg_ro_cl="</tr>";
//  $tg_td="<td>";
//  $tg_td_cl="</td>";
//  $tg_top_cl="</table></div>";
//  $tg_ip="<input";
//  $tg_ip_type=" type=\"";
//  $tg_ip_name="\" name=\"";
//  $tg_ip_value="\" value=\"";
//  $tg_ip_size="\" size=\"";
//  $tg_ip_id="\" id=\"";
//  $tg_class="\" class=\"ro";
//  $tg_ip_cl="\" />";
//  $tg_chk="<input type=\"checkbox\" name=\"chb";
//  $tg_chk_val="";
//  $tg_dat="<a href=\"javascript:NewCal('";
//  $tg_dat_cl="','yyyymmdd')\"><img src=datetimepick/cal.gif width=16 height=16 border=0 alt=Pick a date></a>";
//  $tg_sel="<select name=\"";
//  $tg_cl="\" >";
//  $tg_sel_cl="</select>";
//  $tg_opt="<option value=\"";
//  $tg_opt_cl="</option>";
//  $tg_hidden="hidden";
//  $tg_readonly="\" readonly ";
//  $tg_text="<textarea rows=\"4\" cols=\"15\" ";
//  $tg_text_cl="</textarea>";
//  $tg_thead="<thead>";
//  $tg_thead_cl="</thead>";
        
 $data_sql="select * from ".$tbl;

 if(isset($qual))
 {
 $qual=" where ".$qual;
 $data_sql=$data_sql.$qual;
 }
 //only get pages if this is main table
 if($_GET['page']==$tbl)
 //$data_sql=getPagesql($data_sql,7);
//  $result1=mysql_query($sql1);
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

//   while($row = mysql_fetch_array($result1))
foreach($result1 as $row)
  {
  $a=$a.$ui->tg_ro;
  $a=$a.$ui->tg_hdr.$row['alias'].$ui->tg_hdr_cl;

 //Print data columns

//   $result_data=mysql_query($data_sql);
$result_data=$this->allArray($data_sql);
//   while($datarow=mysql_fetch_array($result_data))
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
  $a=$a.$ui->tg_thead.$ui->tg_ro;
//   while($row = mysql_fetch_array($result1))
foreach($result1 as $row)
  {
  if((empty($arr) || in_array($row['name'],$arr)) )
  $a=$a.$ui->tg_hdr.$row['alias'].$ui->tg_hdr_cl;
  }
  //add timetable total column
  //$a=$a.$ui->tg_hdr."Total".$ui->tg_hdr_cl;
  $a=$a.$ui->tg_ro_cl.$ui->tg_thead_cl;

  //define timetable specific variables to store values
  $tot = 0;
  $tcr = 0;
  $tdr = 0;
  
  //Print Data rows
  $result_data=$this->allArray($data_sql);
//   while($datarow=mysql_fetch_array($result_data))
foreach($result_data as $datarow)
  {
  $a=$a.$ui->tg_ro;
//   $result1=mysql_query($sql1);
$result1=$this->allArray($sql1);
  
  // To Calculate the Total of a row in a timetable.
  //$sumrow=$datarow['cr']+$datarow['dr'];
  //$tot=$tot+$sumrow;

  // To Calculate the Total of Column in a timetable.
  $tcr  = $tcr + $datarow['cr'];
  $tdr  = $tdr + $datarow['dr'];
   
//   while($row = mysql_fetch_array($result1))
foreach($result1 as $row)
  {
  if(empty($arr) || in_array($row['name'],$arr))
   {
  if($row['type']=="idate")
  $a=$a.$ui->tg_td.$utils->getmydate($datarow[$row['name']]).$ui->tg_td_cl;
  //$a=$a.$ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl;
  elseif($row['type']=="password"){
  $a=$a.$ui->tg_td.$ui->tg_td_cl;
  }elseif($row['dbindex']=="primary" ){
  $a=$a.$ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl;
  $a=$a.$ui->tg_ip.$ui->tg_ip_type.$ui->tg_hidden.$ui->tg_ip_name.$row['name'].$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
  $a=$a.$datarow[$row['name']].$ui->tg_ip_cl;
  }else
  $a=$a.$ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl; 
  }elseif($row['dbindex']=="primary" && !in_array($row['name'],$arr))
  {
  $a=$a.$ui->tg_ip.$ui->tg_ip_type.$ui->tg_hidden.$ui->tg_ip_name.$row['name'].$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
  $a=$a.$datarow[$row['name']].$ui->tg_ip_cl;
  }
  //echo $a;
  //close while loop
  }
  //adding below row for total column in timetable
 // $a=$a.$ui->tg_td.$sumrow.$ui->tg_td_cl;
  
 // $a=$a.$ui->tg_ro_cl;

  }
//Close Table
  //total row in the last;
  $z="<tr class=\"success\"> <td colspan=\"3\" class=\"tot\">Total</td> <td class=\"tot\">$tdr</td> <td class=\"tot\">$tcr</td> </tr>";
  $bal = $tdr - $tcr;
  if($bal < 0){$bal =  abs($bal);
$b="<tr class=\"warning\"> <td colspan=\"3\" class=\"tot\">Balance</td> <td class=\"tot\">$bal</td> <td class=\"tot\">0.00</td> </tr>";
$to = $tdr+$bal;
$q="<tr class=\"success\"> <td colspan=\"3\" class=\"tot\">Balance Sheet Statement</td> <td class=\"tot\">$to</td> <td class=\"tot\">$tcr</td> </tr>";
}
	  else{		  
$b="<tr class=\"danger\"> <td colspan=\"3\" class=\"tot\">Balance</td> <td class=\"tot\">0.00</td> <td class=\"tot\">$bal</td> </tr>";
$to = $tcr+$bal;
$q="<tr class=\"success\"> <td colspan=\"3\" class=\"tot\">Balance Sheet Statement</td> <td class=\"tot\">$tdr</td> <td class=\"tot\">$to</td> </tr>";
}
   
  //$tot=$tot+$sumrow;
  $a=$a.$z;
 
  
   $a=$a.$b;
   $a=$a.$q;
  $a=$a.$ui->tg_top_cl;
 }
if($_GET['page'] == 'accounts'){}
else{
 $a=$a."<input class=\"btn btn-warning\" id=\"btn\" type=\"submit\" name=\"modify\" value=\"modify\">";}
 return $a;
}

function display_ref($tbl,$qual,$md,$arr,$is_time)
{
	// $qual=createSearchBuilder($tbl,$qual);
	$utils = new Utils();
	$ui = new UiConstant($tbl);
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
$data_sql="select id,child from formrel where parent='$tbl'";
//echo $data_sql;
$result=$this->allArray($data_sql);
$a="";
$tb="<div class=\"tabbable\" id=\"tabs-344382\"><ul class=\"nav nav-tabs\" role=\"tablist\">";
$tbc="";$k=0;


// while($row = mysql_fetch_array($result))
foreach($result as $row)
{$a="";
  $v=$row['id'];
  $tbl=$row['child'];
  $sql1=$this->dbsql($tbl);
  $data_sql="select * from ".$tbl." where id in (select childrecid from reldata where relid=".$v." and ".$qual.")";
$btn_new="<br /><button class=\"btn btn-danger\" type=\"button\" name=\"new_$tbl\" value=\"new\" data-toggle=\"modal\" data-target=\".modal\"/>New Comment</button>";

if($k==0)
{$ad=" class=\"active\"";$adc=" active";}  
    $tb=$tb."<li role=\"$tbl\"".$ad.">"
        . "<a href=\"#".$tbl."\" aria-controls=\"$tbl\" role=\"tab\" data-toggle=\"tab\">"
            .$tbl."</a></li>";
$tbc=$tbc."<div class=\"tab-pane".$adc."\" id=\"$tbl\">.$btn_new";
$k++;$ad="";$adc="";
	/*if(isset($qual))
	{
	$qual=" where ".$qual;
	$data_sql=$data_sql.$qual;
	}*/
	//only get pages if this is main table
	//if($_GET['page']==$tbl)
	//$data_sql=getPagesql($data_sql,5);
	//echo $data_sql;
// $result1=mysql_query($sql1);
$result1=$this->allArray($sql1);
//mode 0 is columnar and mode 1 for row-wise printing
	
	$opt=array(1 => "k");
	$mode=1;
	$j=0;

	$mode=$md;
	if($mode==1)
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
		// $result_data=mysql_query($data_sql);
		// $result_data=$this->allArray($data_sql);
		$result_data=$this->allArray($data_sql);
		// while($datarow=mysql_fetch_array($result_data))
		foreach($result_data as $datarow)
		{
		$a=$a.$ui->tg_ro;
		$result1=allArray($sql1);
		// while($row = mysql_fetch_array($result1))
		foreach($result1 as $row)
		{
		if(empty($arr) || in_array($row['name'],$arr))
		{
		if($row['type']=="idate")
		if($is_time) $a=$a.$ui->tg_td.$utils->getmytime($datarow[$row['name']]).$ui->tg_td_cl;
		else $a=$a.$ui->tg_td.$utils->getmydate($datarow[$row['name']]).$ui->tg_td_cl;
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
								$a=$a."<a href='".$file_name."' download>".$file_name."</a><br>";
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
		$sql_opt="select * from field_option where tblid=".$row['tblid']." and fieldid=".$row['fieldid']." and value=".$datarow[$row['name']];
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
                                            //echo $sql_filter;
											$result_filter=$this->firstArray($sql_filter);
											$vsource= $result_filter['source'];
											$vfilter= $result_filter['filter'];
											$vid= $result_filter['id'];
											$vvalue= $result_filter['value'];
											$valias= $result_filter['alias'];
                                            $vfilter= $vfilter==null?" where 1=2 ":" where ".$utils->parseFilter($vfilter);
                                            $sql_opt="select ".$vvalue." as value,".$valias." as alias from ".$vsource.$vfilter." and ".$vvalue."=".$datarow[$row['name']];
                                            //echo $sql_opt;	
                                            //Completion of change
											// $result_opt=mysql_query($sql_opt);
											$result_opt=$this->allArray($sql_opt);
                                            $opt[$j]="";
                                            $alias='';
                                            if($result_opt && !empty($result_opt))
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
                                    $alias=(isset($alias)&&($alias!=null))?$alias:"";
                                    //echo "alias is".$alias;
                                    $a=$a.$alias.$ui->tg_td_cl;
                                    $j++;
					}
                elseif($row['dbindex']=="primary" ){
		$a=str_replace('constant',$datarow[$row['name']],$a);
		$a=$a.$ui->tg_td."<a onclick=openRec(".$datarow[$row['name']].",'".$tbl."'".") href=\"#\"".">".$datarow[$row['name']]."</a>".$ui->tg_td_cl;
		$a=$a.$ui->tg_ip.$ui->tg_ip_type.$ui->tg_hidden.$ui->tg_ip_name.$row['name'].$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
		$a=$a.$datarow[$row['name']].$ui->tg_ip_cl;
		} elseif($row['dbindex'] == 'foreign'){
			$a=$a.$ui->tg_td.$this->getForeginData($datarow[$row['name']],$row['name']).$ui->tg_td_cl;
		}
                else
		$a=$a.$ui->tg_td.$datarow[$row['name']].$ui->tg_td_cl;
		}elseif($row['dbindex']=="primary" && !in_array($row['name'],$arr))
		{
		$a=str_replace('constant',$datarow[$row['name']],$a);
		$a=$a.$ui->tg_ip.$ui->tg_ip_type.$ui->tg_hidden.$ui->tg_ip_name.$row['name'].$ui->tg_ip_size.$row['size'].$ui->tg_ip_value;
		$a=$a.$datarow[$row['name']].$ui->tg_ip_cl;
		}
		
		//close while loop
		}

		$a=$a.$ui->tg_ro_cl;
                //echo "ais".$a;
		}
//Close Table
		$a=$a.$ui->tg_top_cl;
	}
	$tbc=$tbc."<p>".$a."</p></div>";
}
//echo "tbcis".$tbc."this";
$tbc=$tb."</ul>"."<div class=\"tab-content\">".$tbc."</div></div>";
return $tbc;
}

function getRelationIdByTable($perent_table,$child_table,$pid=false){
	// checking for direct relations rel_type=1
    
	$sql = "select id from formrel where parent = '$perent_table' and child = '$child_table' and rel_type=1";
	// $query = mysql_query($sql) or die(mysql_error());
	$ids = $this->firstArray($sql);
	$id = '';
	if($ids && !empty($ids)){
		$id = $ids['id'];
	}else{
		$ref_datas = $this->first("select id,ref_parent,child,parent_ref_field from formrel where parent='$perent_table' and child = '$child_table' and rel_type = 2");
		if($ref_datas && !empty($ref_datas)){
			$cur_parent = $ref_datas->ref_parent;
			$ref_field = $ref_datas->parent_ref_field;
			$rel_data = $this->first("select id,child from formrel where parent ='{$cur_parent}' and child = '{$child_table}'");
			$id = $rel_data->id;
			if($pid){
				$p_data = $this->first("select $ref_field from $perent_table where id = '$pid'");
				if($p_data && !empty($p_data)){
					$pid = $p_data->$ref_field;
				}
				return array('pid'=>$pid,'relid'=>$id);
			}
		}
	}
	if($pid){
		return array('pid'=>$pid,'relid'=>$id);
	}else{
		return $id;
	}
}
function getForeginData($value,$field){
	$arr = explode('_',$field);
	$table = $arr[0];
	$field = $arr[1];
	$sql = "select * from $table where $field = '$value' limit 1";
	$res = "";
	if($record){
		$res = $record->empname;
	}
	return $res;
}

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
}
?>
