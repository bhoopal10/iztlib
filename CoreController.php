<?php
#v0.1 Updated create() function . Added strtolower and str_replace function to ensure tables created have special chars removed from fields.
require_once("auth.php");
require_once("lib/activitylog_lib.php");
require_once('UiConstant.php');
// require_once("triggers.php");
class CoreController extends CoreView{
function addrow($tbl)
{
	$utils = new Utils();
	$ui = new UiConstant($tbl);
	// $tg_ip_class="\" class=\"form-control";
	// $tg_div_cl="</div>";
	// $tg_top="<table width=auto border=1 cellpadding=2 cellspacing=2>";
	// $tg_hdr="<th>";
	// $tg_hdr_cl="</th>";
	// $tg_ro="<tr>";
	// $tg_ro_cl="</tr>";
	// $tg_td="<td>";
	// $tg_td_cl="</td>";
	// $tg_top_cl="</table>";
	// $tg_ip="<input";
	// $tg_ip_type=" type=\"";
	// $tg_ip_name="\" name=\"";
	// $tg_ip_value="\" value=\"";
	// $tg_ip_size="\" size=\"";
	// $tg_ip_id="\" id=\"";
	// $tg_class="\" class=\"ro";
	// $tg_ip_cl="\" >";
	// $tg_chk="<input type=\"checkbox\" name=\"chb";
	// $tg_chk_val="";
	// $tg_dat="<a href=\"javascript:NewCal('";
	// $tg_dat_cl="','ddmmyyyy')\"><img src=datetimepick/cal.gif width=16 height=16 border=0 alt=Pick a date></a>";
	// $tg_sel="<select id=\"";
	// $tg_cl="\" >";
	// $tg_sel_cl="</select>";
	// $tg_opt="<option value=\"";
	// $tg_opt_cl="</option>";
	// $tg_text="<textarea";
	// $tg_text_name=" name=\"";
	// $tg_text_cl = " rows=\"4\" cols=\"15\"></textarea> ";

	//DB SQLs
	$sql="select tblid from config where name='$tbl'";
	$result=$this->firstArray($sql);
	$myvar=$result['tblid'];
	$sql1="select * from field where tblid=$myvar";
	$result1=$this->allArray($sql1);
	$fname="";
	$fval="";
	$a="";
	$opt=array(1 => "k");
	$mode=1;
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

		$result1=$this->allArray($sql1);
		// while($row = mysql_fetch_array($result1))
		foreach($result1 as $row)
		{
			// $row['size'] = 10;
			if($row['type']=="option")
			{
				$a=$a.$ui->tg_td.$ui->tg_sel.$row['name'].$cnt.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_cl;
				if($cnt==0)
				{
				$sql_opt="select * from field_option where tblid=".$row['tblid']." and fieldid=".$row['fieldid'];
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
			elseif($row['type']=="file"){
				$path = $utils->getFilePath($tbl).$row['name'];
				$a=$a.$ui->tg_td.$ui->tg_ip.$ui->tg_ip_type.$row['type'].$ui->tg_ip_name.$row['name'].$cnt.'[]'.$ui->tg_ip_size.$row['size'].$ui->tg_ip_value.' multiple '.$ui->tg_ip_cl.$ui->tg_div_cl.$ui->tg_td_cl;
			}
			elseif($row['type']=="list")
				{
					$a=$a.$ui->tg_td.$ui->tg_sel.$row['name'].$cnt.$ui->tg_ip_name.$row['name'].$cnt.$ui->tg_cl;
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
						// echo $sql_opt;	
						//Completion of change
						$result_opt=$this->allArray($sql_opt);
						$opt[$j]="";
						$alias='';
						if($result_opt)
						{
							// while($row_opt = mysql_fetch_array($result_opt))
							foreach($result_opt as $row_opt)
							{
								//echo $row_opt['alias'];exit;
								// $arr=['config','field','field_option','access','groups','Users','Events','valuelist'];	
								// if(!in_array($row_opt['alias'],$arr)) 
								$opt[$j]=$opt[$j].$ui->tg_opt.$row_opt['value'].$ui->tg_cl.$row_opt['alias'].$ui->tg_opt_cl;
							}
						}
					}
					//below handles code if no value is set and also instead of value shows alias
				
					//introducing for clear value option
					$a=$a.$ui->tg_opt."".$ui->tg_cl."--NONE--".$ui->tg_opt_cl;
					$a=$a.$opt[$j].$ui->tg_sel_cl.$ui->tg_td_cl;
					$j++;
				}elseif(!$row['dbindex']=='primary')
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

$fname=chop($fname,",");
$fval=chop($fval,",");
$sqli = "INSERT INTO ".$tbl." ( ".$fname." ) VALUES (".$fval.")";
// echo $sqli;
//$sqla= "$this-> table 
echo "<input class=\"btn btn-primary\" id=\"btn\" type=\"submit\" name=\"addrow\" value=\"Add Row\">";
//echo "<input class=\"btn btn-warning\" id=\"btn\" type=\"submit\" name=\"modify\" value=\"modify\">";
return $sqli;
}
 
function uploadFile($files,$tbl){
	// write_log('debug',"Test1234");
	$utils = new Utils();
	$path = $utils->getFilePath($tbl);
	$path = __DIR__.'/../'.$path;
	if(!is_dir($path)){
		$oldmask = umask(0);
		mkdir($path, 0777);
		umask($oldmask);
        // mkdir($path,0777);
	}
	$attachment_name = array();
    if(isset($files['name'])){
		// print_r($files['name']);
		foreach($files['name'] as $key=>$file){

			// echo $key." ".$file;
			if($file){
				$file_name = time()."_".$key."_".$file;
                $attachment_name[]=$utils->getFilePath($tbl).$file_name;
                move_uploaded_file($files['tmp_name'][$key],$path.$file_name);
            }
        }
	}
	if(!empty($attachment_name)){
		// echo implode(',',$attachment_name);
		return implode(',',$attachment_name);
	}
	return '';
}
function insert($tbl)
{
// $sql1=$this->dbsql($tbl);
$utils = new Utils();
$coreV = new CoreView();
$sql1=$this->dbsql($tbl);
$result1=$this->allArray($sql1);
$fname="";	$fval="";	$i=0;
$user_id = $_SESSION['SESS_id'];
	// while($row = mysql_fetch_array($result1))
	foreach($result1 as $row)
	{
		
		//echo "---".$row['name']."---<br>";
		if(!$row['dbindex']=='primary')
		{
			$fname=$fname.$row['name'].",";
			$sys_field = array('created_at','modified_at','created_by','modified_by');
		//	if(isset($_POST[$row['name'].$i]) || in_array($row['name'],$sys_field)) 
			
		if($row['name'] == 'created_at'){
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
			$file_name = "";
			if(isset($_FILES[$row['name'].$i])){
				$file_name = $this->uploadFile( $_FILES[$row['name'].$i],$tbl);
			}

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
						$fval=$fval."'0',";	
					}
			}
		//echo "fval is".$row['name'];
		}


		}
	}
$fname=chop($fname,",");
$fval=chop($fval,",");
// echo $fval;exit;
$sqli="INSERT INTO ".$tbl." ( ".$fname." ) VALUES (".$fval.")";
 //echo $sqli;exit;
return $sqli;
}
function insert1($tbl)
{
$sql1=$this->dbsql($tbl);
$result1=$this->allArray($sql1);
$fname="";	$fval="";	$i=0;
	// while($row = mysql_fetch_array($result1))
	foreach($result1 as $row)
	{
		if(!$row['dbindex']=='primary')
		{
		$fname=$fname.$row['name'].",";
		// if($row['type']=='idate')
		// $fval=$fval."".$utils->setmydate($_POST[$row['name'].$i]).",";
		if($row['type']=='password')
		$fval=$fval."'".sha1($_POST[$row['name'].$i])."',";
		elseif($row['type'] == 'file'){
			$file_name = $this->uploadFile( $_FILES[$row['name'].$i],$tbl);
			if($file_name) $fval=$fval."'".$file_name."',";
			$fval = $fval."'',";
		}else
		{$fval=$fval."'".$_POST[$row['name'].$i]."',";
		//echo "fval is".$row['name'];
		}
		}
	}
$fname=chop($fname,",");
$fval=chop($fval,",");
$sqli="INSERT INTO ".$tbl." ( ".$fname." ) VALUES (".$fval.")";
// echo $sqli;exit;
return $sqli;
}

function alter($i=0,$action='ADD')
{
	//$tbl=$_POST['tblid0'];
	// echo 'dddddq';exit;
	$utils = new Utils();
$sql1="select name from config where tblid=".$_POST['tblid'.$i];
$result=$this->firstArray($sql1);
$tbl=$result['name'];
$sqli="ALTER TABLE ".$tbl." ".$action." ".$_POST['name'.$i];
if($action=='ADD' || $action=='MODIFY')
{
   if($_POST['dbtype'.$i] == 'text'|| $_POST['dbtype'.$i] == 'ihtml' || $_POST['dbtype'.$i] == 'int') $sqli=$sqli." ".$_POST['dbtype'.$i];
   elseif($_POST['dbtype'.$i] == 'datetime') $sqli=$sqli." ".$_POST['dbtype'.$i];
 else $sqli=$sqli." ".$_POST['dbtype'.$i]."(".$_POST['size'.$i].")";
    if($_POST['dbindex'.$i]=='primary' && $action=='ADD')
	$sqli=$sqli." NOT NULL AUTO_INCREMENT FIRST,ADD PRIMARY KEY "."(".$_POST['name'.$i].")";
	
	if($_POST['type'.$i] == 'ihtml') $sqli = $sqli.', '.$action.' '.$_POST['name'.$i].'_filter '.$_POST['dbtype'.$i];
}
$this->coreMigration(NULL,'Field '.$_POST['name'.$i].' for table '.$tbl.' has been modified');
// echo $sqli;exit;
return $sqli;
}

function insert_data($tbl)
{
$sql=$this->dbsql($tbl);
$utils = new Utils();
$fname="";	$fval="";	$i=0;
$cnt=$_POST['icnt'];
$field_list=explode(",",$_POST['field_edit']);
while($i<$cnt)
{
	
	$result1=$this->allArray($sql);
	// while($row = mysql_fetch_array($result1))
	foreach($result1 as $row)
	{
		if(!$row['dbindex']=='primary' && in_array($row['name'], $field_list))
		{
		$fname=$fname.$row['name'].",";
		if($row['type']=='idate')
		$fval=$fval."".$utils->setmydate($_POST[$row['name'].$i]).",";
		elseif($row['type']=='password')
		$fval=$fval."'".sha1($_POST[$row['name'].$i])."',";
		else
		{$fval=$fval."'".$_POST[$row['name'].$i]."',";
		//echo "fval is".$row['name'];
		}
		}
	}
$fname=chop($fname,",");
$fval=chop($fval,",");
$sqli="INSERT INTO ".$tbl." ( ".$fname." ) VALUES (".$fval.")";
// echo $sqli;
$fname="";	$fval="";
// if(!mysql_query($sqli))
// 	{
// 		die.mysql_error();
// 	}
$this->insertData($sqli);
$i++;
}
}

//function created to load data form excel import. It will modify date etc.
function load_data($tbl)
{
	$utils = new Utils();	 
$sql=$this->dbsql($tbl);
if (get_magic_quotes_gpc()) $_POST = array_map('stripslashes', $_POST);
$fname="";	$fval="";	$i=0;
$cnt=$_POST['icnt'];
$field_list=explode(",",$_POST['field_edit']);
 
while($i<$cnt)
{
	
	$result1=$this->allArray($sql);
	// while($row = mysql_fetch_array($result1))
	foreach($result1 as $row)
	{	
		if(!$row['dbindex']=='primary' && in_array($row['name'], $field_list))
		{
		$fname=$fname.$row['name'].",";
		if($row['type']=='idate'){
			$time = $_POST[$row['name'].$i] ? strtotime($_POST[$row['name'].$i]) : time();
			$time = strtotime(date('Y-m-d 00:00',$time));
			// $fval=$fval."".$utils->setmydate(date('Y-m-d 00:00',strtotime($_POST[$row['name'].$i]))).",";
			$fval=$fval."".$time.",";
		}
		elseif($row['type']=='password')
		$fval=$fval."'".sha1($_POST[$row['name'].$i])."',";
		else
		{$fval=$fval."'".$_POST[$row['name'].$i]."',";
		//echo "fval is".$row['name'];
		}
		}
	}
$fname=chop($fname,",");
$fval=chop($fval,",");
$sqli="INSERT INTO ".$tbl." ( ".$fname." ) VALUES (".$fval.")";
// echo $sqli;
$fname="";	$fval="";
// if(!mysql_query($sqli))
// 	{
// 		die.mysql_error();
// 	}
$this->insertData($sqli);
$i++;
}
}

function create($tbl)
{
$sql1=$this->dbsql($tbl);
$result1=$this->allArray($sql1);
$fname="";	$fval="";	$i=0;
$special=array(".",",",")","("," ","  ","   ","/");
	// while($row = mysql_fetch_array($result1))
	foreach($result1 as $row)
	{
		if('primary' == $row['dbindex'])
		{
		$fname=$fname.strtolower(str_replace($special,"",$row['name']))." ".$row['dbtype']."(".$row['size'].") NOT NULL AUTO_INCREMENT PRIMARY KEY,";
		}else
		{$fname=$fname.strtolower(str_replace($special,"",$row['name']))." ".$row['dbtype']."(".$row['size']."),";}
	}
$fname=chop($fname,",");
$fval=chop($fval,",");
if(!$this->checkTable("select 1 from $tbl"))
$sqli="CREATE Table ".$tbl." ( ".$fname." ) ";
//else
//$sqli="$this-> table ".$tbl." add ( ".$fname." ) ";
return $sqli;
}

function update($tbl)
{
	$utils = new Utils();
	$sqlu=$this->dbsql($tbl);
	// echo $sqlu;exit;
	$fname="";	$fval="";	$i=0;
	// $data_sql="select * from ".$tbl;
	//$result_data=mysql_query($data_sql);
	$cnt=$_POST['icnt'];
	// $field_list = getTblQualWorkflow($tbl,'','edit_mode');
	$field_list=explode(",",$_POST['field_edit']);
	// $field_list = explode(',',$field_list);
	// print_r($field_list);
	// $_POST['chb0'];
	// print_r($field_list);exit;
	$user_id = $_SESSION['SESS_id'];
	while($i<$cnt)
	{
		// echo $_POST['chb'.$i];exit;
		if(isset($_POST['chb'.$i]))
		{
	
			$result1=$this->allArray($sqlu);
			
			foreach($result1 as $row)
			{
				
				//if($row['dbindex']=="primary" ||in_array($row['name'], $field_list))
				if(isset($_POST[$row['name'].$i]) || isset($_FILES[$row['name'].$i]))
				{
					if($row['dbindex']=='primary')
					{
						$fval=$fval.$row['name']."=";
						if($row['dbtype']=='int')
						{
						$fval=$fval.$_POST[$row['name'].$i].",";
						}else
						{
						$fval=$fval."'".$_POST[$row['name'].$i]."',";
						}
					}
					elseif($row['name'] == 'modified_at'){
						$fname = $fname.$row['name'].' = '. time().', ';
					}
					elseif($row['name'] == 'modified_by'){
						$fname = $fname.$row['name'].' = '. $user_id.', ';
					}else{
						$fname=$fname.$row['name']."=";
						if($row['type']=="idate")
						{
							$fname=$fname."'".$utils->setmydate($_POST[$row['name'].$i])."',";
						}elseif($row['type']=='password')
						{$fname=$fname."'".sha1($_POST[$row['name'].$i])."',";}
						elseif($row['type']=='ihtml'){
							// $message = isset($_POST[$row['name'].$i]) ? addslashes($_POST[$row['name'].$i]) : '';
							// $message_filter = $utils->cleanup($message);
							// // $or_msg = mysql_real_escape_string(stripslashes($message));
							// $or_msg = stripslashes($message);
							// $fname = $fname."'".$or_msg."',";
							// $fname = $fname.$row['name']."_filter ='".$message_filter."',";
							// $fname = $fname.$row['name']."_filter ='NA',";
						// echo $fval;exit;
						// $message = isset($_POST[$row['name'].$i]) ? $_POST[$row['name'].$i] : '';
						//  $x = str_ireplace("'",  "&apos;", $message);
						// 	$x = str_ireplace("\\", "&bsol;", $x);
						// 	$x = str_ireplace('"',  "&quot;", $x);
						// 	$fname = $fname."'".$x."',";
						$message = isset($_POST[$row['name'].$i]) ? addslashes($_POST[$row['name'].$i]) : '';
						$message_filter = $utils->cleanup($message);
						// $fval=$fval."'".mysql_real_escape_string(stripslashes($message))."',";
						$fname=$fname."'".stripslashes($message)."',";

						}elseif($row['type']=='textarea'){
							$message = isset($_POST[$row['name'].$i]) ? $_POST[$row['name'].$i] : '';
							// $message_filter = $utils->cleanup($message);
							$x = str_ireplace("'",  "&apos;", $message);
							$x = str_ireplace("\\", "&bsol;", $x);
							$x = str_ireplace('"',  "&quot;", $x);
							$fname = $fname."'".$x."',";
						}
						elseif($row['type'] == 'file'){
							$file_name = $this->uploadFile( $_FILES[$row['name'].$i],$tbl);
							if($file_name) $fname=$fname."'".$file_name."',";
							else $fname = str_replace($row['name'].'=','',$fname);
						}
						else{
							if(isset($_POST[$row['name'].$i]))
							{

								
								if($row['dbtype'] =='int' || $row['dbtype'] =='decimal')
								{
									if(trim($_POST[$row['name'].$i]) || $_POST[$row['name'].$i] == '0') {
										$fname=$fname."".addslashes(($_POST[$row['name'].$i])).",";
										// echo $row['name'].$i; var_dump($_POST[$row['name'].$i]);
									}
									else $fname=$fname."0,";
								}else
								{
									$fname=$fname."'".addslashes(($_POST[$row['name'].$i]))."',";
										
								}
							}
						}
					}
		
				}
			}

			$fname = trim($fname);
			$fname=rtrim($fname,",");
$fval=chop($fval,",");
$buquery = $this->firstArray("select * from $tbl where $fval");
$beforeupdatedata = $buquery;
$recordid=$fval;
// echo "<br>";
// echo $fname;
$sqli="UPDATE ".$tbl." set ".$fname." Where ".$fval;
// This code will only run when the page name is Bank otherwise not


// This code will only run when the Validated Button is clicked otherwise not
if(isset($_POST['validate']))
{$my_table =$_GET['page'];
	if($my_table == "loaded_payroll"){$sqli="UPDATE ".$tbl." set status = 'Validated' Where ".$fval;}}
	
$fname="";	$fval="";
// if(isset($sqli) && !mysql_query($sqli))
// 	{
	// 	die.mysql_error();
	// 	}
	if(isset($sqli)) $this->executeQuery($sqli);
	// TODO
		$auquery=$this->firstArray("select * from $tbl where $recordid");
		$afterupdatedata = $auquery;
		$changeddata=array_diff($afterupdatedata,$beforeupdatedata);
		foreach($changeddata as $field => $value)
		{	
			$msg=$field." "."is modified from"." ".$beforeupdatedata[$field]." "."to"." ".$value."<br>";
			
		}
		$action="update";
		// :TODO
		activitycheck($beforeupdatedata,$afterupdatedata,$tbl,$recordid,$action,null); 
}
$i++;
}
}

function delete($tbl)
{
$sqlu=$this->dbsql($tbl);
$fname="";	$fval="";	$i=0;
//$data_sql="select * from ".$tbl;
//$result_data=mysql_query($data_sql);
$cnt=$_POST['icnt'];
$field_list=explode(",",$_POST['field_edit']);

while($i<$cnt)
{

if(isset($_POST['chb'.$i]))
{
	$result1=$this->allArray($sqlu);
	// while($row = mysql_fetch_array($result1))
	foreach($result1 as $row)
	{
		if($row['dbindex']=="primary" ||in_array($row['name'], $field_list))
		{if($row['dbindex']=='primary')
		{
		$fval=$fval.$row['name']."=";
			if($row['dbtype']=='int')
			{
			$fval=$fval.$_POST[$row['name'].$i].",";
			}else
			{
			$fval=$fval."'".$_POST[$row['name'].$i]."',";
			}
		}
		else{
		$fname=$fname.$row['name']."=";
		if($row['type']=="idate")
		{
		$fname=$fname."'".$utils->setmydate($_POST[$row['name'].$i])."',";
		}else{
		$fname=$fname."'".$_POST[$row['name'].$i]."',";
		}

		}
		}
	}


$fname=chop($fname,",");
// echo $fname;
//adding new below row for admin operations
if($tbl=='field')
{
$sqli=$this->alter($i,'DROP');
// echo $sqli;
// if(!mysql_query($sqli))
// 	{
// 	die.mysql_error();
// 	}
$this->executeQuery($sqli);
}
$fval=chop($fval,",");
$sqli="DELETE from ".$tbl." Where ".$fval;
// echo $sqli."<br/>";
$fname="";	$fval="";
// if(isset($sqli) && !mysql_query($sqli))
// 	{
// 	die.mysql_error();
// 	}
if(isset($sqli)) $this->executeQuery($sqli);
}
$i++;
}
}

function update_single($tbl)
{
$sqlu=$this->dbsql($tbl);
$fname="";	$fval="";	$i=0;
//$data_sql="select * from ".$tbl;
//$result_data=mysql_query($data_sql);
$cnt=$_POST['icnt'];
$field_list=explode(",",$_POST['field_edit']);
//echo "count is $cnt";
while($i<$cnt)
{
//echo "caught the issue";
//if(isset($_POST['chb'.$i]))
//{
	$result1=$this->allArray($sqlu);
	// while($row = mysql_fetch_array($result1))
	foreach($result1 as $row)
	{
		if($row['dbindex']=="primary" ||in_array($row['name'], $field_list))
		{if($row['dbindex']=='primary')
		{
		$fval=$fval.$row['name']."=";
			if($row['dbtype']=='int')
			{
			$fval=$fval.$_POST[$row['name'].$i].",";
			}else
			{
			$fval=$fval."'".$_POST[$row['name'].$i]."',";
			}
		}
		else{
		$fname=$fname.$row['name']."=";
		if($row['type']=="idate")
		{
		$fname=$fname."'".$utils->setmydate($_POST[$row['name'].$i])."',";
		}elseif($row['type']=='password')
		{$fname=$fname."'".sha1($_POST[$row['name'].$i])."',";}
		else{
		$fname=$fname."'".addslashes(($_POST[$row['name'].$i]))."',";
		}

		}
		}
	}
//adding new below row for admin operations

if($tbl=='field')
{
/*$sqli=$this->($i,'DROP');
echo $sqli;
if(!mysql_query($sqli))
	{
	die.mysql_error();
	}
*/
        $sqli=$this->alter($i,'MODIFY');
//echo $sqli;
// if(!mysql_query($sqli))
// 	{
// 	die.mysql_error();
// 	}	
$this->executeQuery($sqli);
}

$fname=chop($fname,",");
$fval=chop($fval,",");
$sqli="UPDATE ".$tbl." set ".$fname." Where ".$fval;
//echo $sqli."<br/>";
$fname="";	$fval="";
// if(isset($sqli) && !mysql_query($sqli))
// 	{
// 	die.mysql_error();
// 	}
if(isset($sqli)) $this->executeQuery($sqli);
//}
$i++;
}

}

function createtable()
{
	if($this->driver == 'sqlserv'){
		$sql_create="CREATE TABLE ".$_POST['name0']."( id INT IDENTITY(1,1) NOT NULL PRIMARY KEY CLUSTERED ,created_by int DEFAULT NULL,modified_by int DEFAULT NULL,created_at int DEFAULT NULL ,modified_at int DEFAULT NULL, status INT DEFAULT NULL)";

	}else{
		$sql_create="CREATE TABLE ".$_POST['name0']."( `id` INT(9) NOT NULL AUTO_INCREMENT ,`created_by` int(11) DEFAULT NULL,`modified_by` int(11) DEFAULT NULL,`created_at` int(11) DEFAULT NULL ,`modified_at` int(11) DEFAULT NULL, `status` int(11), PRIMARY KEY (`id`)) ENGINE = InnoDB";
	}
	$myfile = fopen($_POST['name0'], "w");
	$template = file_get_contents('app_template');
	fwrite($myfile,$template );
	fclose($myfile);
	$this->createWorkflow($_POST['name0']);
	$this->coreMigration($sql_create,'New table '.$_POST['name0'].' has been created');
   return $sql_create;
}

function createWorkflow($tblname){
	$sqli_workflow =  "INSERT INTO `workflow` (`name`,`formname`) VALUES ('{$tblname}','{$tblname}')";
	$this->insertData($sqli_workflow);
	$this->coreMigration($sqli_workflow);
}

function coreMigration($change,$user_message=NULL){
	$path="./logs/";
	if(!file_exists($path.'coremigration.sql')) {
		$myfile = fopen($path.'coremigration.sql', "w");
		fwrite($myfile,' ');
		fclose($myfile);
	}
	if(!file_exists($path.'changelog.html')) {
		$myfile = fopen($path.'changelog.html', "w");
		fwrite($myfile,' ');
		fclose($myfile);
	}
	if($change) file_put_contents($path.'coremigration.sql','------------------'.date('d-m-Y H:is').'--------'."\n".$change.';'."\n",FILE_APPEND);
	if($user_message) file_put_contents($path.'changelog.html','------------------'.date('d-m-Y H:is').'--------'."\n<b>".$user_message.'</b>'."\n",FILE_APPEND);

}

function createid($rid)
{
	// $sql="select max(tblid)+1 from config";
    // if($resultid=mysql_query($sql))
    //     {$rid=mysql_result($resultid,0);}
    //     else{die.mysql_error();}
   $sqli="INSERT INTO `field` (`tblid`, `name`, `alias`, `type`, `dbtype`, `dbindex`, `size`, `col`, `ord`, `span`) VALUES 
						   (".$rid.", 'id', 'ID', 'text', 'int', 'primary', '9', '0', '0', '0'),
						   (".$rid.", 'created_by', 'Created By', 'text', 'int', '', '9', '0', '0', '0'),
						   (".$rid.", 'modified_by', 'Modified By', 'text', 'int', '', '9', '0', '0', '0'),
						   (".$rid.", 'created_at', 'Created At', 'text', 'int', '', '11', '0', '0', '0'),     
						   (".$rid.", 'modified_at', 'Modified At', 'text', 'int', '', '11', '0', '0', '0')";    
	$this->insertData($sqli);
	$this->coreMigration($sqli);
	$this->createOption($rid);
}

function createOption($tblid){
	$status_sql = "INSERT INTO `field` (`tblid`, `name`, `alias`, `type`, `dbtype`, `dbindex`, `size`, `col`, `ord`, `span`) VALUES
						 (".$tblid.", 'status', 'Status', 'option', 'varchar', '', '50', '0', '0', '0')
						";
	$field_id = $this->insertData($status_sql,true);
	$this->coreMigration($status_sql);
	$optsql="select max(optid)+1 as autoid from field_option";
	$optid = 0;
	// if($resultid=mysql_query($optsql)) $optid=mysql_result($resultid,0);
	$result = $this->firstArray($optsql);
	if($result && !empty($result)) $optid = $result['autoid'];
	$field_option_sql = "INSERT INTO `field_option` (`optid`,`tblid`,`fieldid`,`value`,`alias`) VALUES
							(".$optid.",".$tblid.",".$field_id.",'1','active')
							";
	$this->insertData($field_option_sql);
	$this->coreMigration($field_option_sql);
}
}

?>
