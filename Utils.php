<?php
if(!isset($_SESSION)) session_start();
date_default_timezone_set("Asia/Kolkata");
require_once('DbModel.php');
require_once('UiConstant.php');
class Utils extends DbModel{
public $menu = array();
function tbldef($tbl){
	$memp=array();$mfields=array();
$sql_fields="select fieldid,tblid,name,alias,type from field where tblid in (select tblid from config where name='$tbl')";
$resultf=$this->allArray($sql_fields);
// while($rowf=mysql_fetch_array($resultf))
foreach($resultf as $rowf)
{
$mfields[$rowf['alias']]=$rowf['name'];
$mfields[$rowf['name'].'type']=$rowf['type'];
$mfields[$rowf['name'].'fieldid']=$rowf['fieldid'];
$mfields[$rowf['name'].'tblid']=$rowf['tblid'];
}
//$memp[$empid]=$mfields;

//$memp['1011']=array('vipul'=>'great');
//$memp['1011']=array_add('neha');
//var_dump($mfields);
return $mfields;
}
function datacurr($d){
	return 5;
}

function getPagelink_1($iteration,$start)
	{
        $x=1;$j=1;
        if(isset($_GET['num'])){$j=$_GET['num'];}
		$pagi = "<ul class=\"pagination\"><li><a onclick=\"openPage(".$x.",'".$_GET['page']."');\" href=\"#\">&laquo;</a></li>";
		if($j > 1)  {
			$pagi .= "<li class=\"previous\"><a ";
                    $pagi .= "onclick=openPage(".($j-1).",'".$_GET['page']."'".") href=\"#\"";
                    $pagi .= ">"."&lsaquo;"."</a></li>";
		}
		$max = 10;
		// maintain 4 number if more then dots and last will come sp starting page
		if($j < $max)
			$sp = 1;
		elseif($j >= ($iteration - floor($max / 2)) )
			$sp = $iteration - $max + 1;
		elseif($j >= $max)
			$sp = $j  - floor($max/2);

		if($j >= $max && $sp != 1){
			$pagi .= "<li class=\"\"><a ";
				$pagi .= "onclick=openPage(1,'".$_GET['page']."'".") href=\"#\"";
				$pagi .= ">1</a></li><li><a>..</a></li>";
		}
		$activecls = '';
		$last_page = 1;
		for($i = $sp; $i <= ($sp + $max -1);$i++){
			if($i > $iteration) continue;
			if($j == $i)	$activecls = 'active';
			else $activecls = '';
			$pagi .= "<li class=\"{$activecls}\"><a ";
			$pagi .= "onclick=openPage(".$i.",'".$_GET['page']."'".") href=\"#\"";
			$pagi .= ">".$i."</a></li>";
			$last_page = $i;
		}
		if($j < ($iteration - floor($max / 2)) && $last_page != $iteration){
			$pagi .= "<li><a>..</a></li><li class=\"{$activecls}\"><a ";
			$pagi .= "onclick=openPage(".$iteration.",'".$_GET['page']."'".") href=\"#\"";
			$pagi .= ">".$iteration."</a></li>";
		}
		if($j < $iteration)  {
			$pagi .= "<li class=\"next\"><a ";
                    $pagi .= "onclick=openPage(".($j+1).",'".$_GET['page']."'".") href=\"#\"";
                    $pagi .= ">"."&rsaquo;"."</a></li>";
		}
        $pagi .= "<li><a onclick=\"openPage(".$iteration.",'".$_GET['page']."');\" href=\"#\">&raquo;</a></li></ul>";
        return $pagi;
	}

// function getPagelink_1($iteration)
// 	{$x=1;$j=1;
//         if(isset($_GET['num'])){$j=$_GET['num'];}
      
// 	$pagi = "<ul class=\"pagination\"><li><a onclick=\"openPage(".$x.",'".$_GET['page']."');\" href=\"#\">&laquo;</a></li>";
 
//     for($i=1;$i<=$iteration;$i++)
// 	{ 
// 			//  echo "j is $j";
// 			if($i == $j) $pagi .= "<li class=\"active\"><a ";
//             else $pagi .= "<li><a ";
//             $pagi .= "onclick=openPage(".$i.",'".$_GET['page']."'".") href=\"#\"";
// 			$pagi .= ">".$i."</a></li>";
// 	}$x=$i-1;$pagi .= "<li><a onclick=\"openPage(".$x.",".$_GET['page'].");\" href=\"home.php?page=".$_GET['page']."&num=".$x."\">&raquo;</a></li></ul>";
// 	return $pagi;
// 	}
function getPagelink_1_bkp($iteration)
	{$x=1;$j=1;
        if(isset($_GET['num'])){$j=$_GET['num'];}
      
	echo "<ul class=\"pagination\"><li><a onclick=\"openPage(".$x.",'".$_GET['page']."');\" href=\"#\">&laquo;</a></li>";
	for($i=1;$i<=$iteration;$i++)
	{ 
			//  echo "j is $j";
			if($i == $j) echo "<li class=\"active\"><a ";
            else echo "<li><a ";
            echo "onclick=openPage(".$i.",'".$_GET['page']."'".") href=\"#\"";
	echo ">".$i."</a></li>";
	}$x=$i-1;echo "<li><a onclick=\"openPage(".$x.",".$_GET['page'].");\" href=\"home.php?page=".$_GET['page']."&num=".$x."\">&raquo;</a></li></ul>";
	}
 
function getPagelink($iteration)
   {
   echo "<ul class=\"pagination\"><li><a href=\"#\">&laquo;</a></li>";
   for($i=1;$i<=$iteration;$i++)
   { echo "<li><a href=home.php?page=".$_GET['page']."&num=".$i;
   echo ">".$i."</a></li>";
   }$x=$i-1;echo "<li><a href=\"home.php?page=".$_GET['page']."&num=".$x."\">&raquo;</a></li></ul>";
   }

   

function getPagesql($sql,$rec_limit,$tbl='config',$order = 'desc',$order_field='id',$qual='')
	{
		$paignation = '';
		if($this->driver == 'sqlserv'){
			if( $order_field === NULL) $order_field = 'id';
			if($tbl == 'config') $order_field = 'tblid';
			if( $tbl == 'field') $order_field = 'fieldid';
			$start=0;
			$condition ="";
			$end = $start+$rec_limit;
			if(isset($_GET['num']))
			{
				$start=(($_GET['num'])-1)*$rec_limit;
				$end = $start+$rec_limit;
				$start = $start+1;
			}
			if(trim($qual))	$condition =  'where '.$qual ;
			$total=$this->getCount($sql);
			//  BHoopal: added for if empt record then go one step back
			if($start > $total){
				$start = $start-$rec_limit;
				$end = $end-$rec_limit;
				
			}
			$sql = "select * from (select *,row_number() over (order by {$order_field} {$order}) as row_num from {$tbl} {$condition}) as tmp_table where row_num between {$start} and {$end}";
			if(ceil($total/$rec_limit)>1)
			$paignation = $this->getPagelink_1(ceil($total/$rec_limit),$start);
			// $sql=$sql." limit ".$start.", ".$rec_limit;
			return array('sql'=>$sql,'pagination'=>$paignation);
		}else{
			$start=0;
			$result_page=mysql_query($sql) or die('PaginateError:'.mysql_error());
			$total=mysql_num_rows($result_page);
			if(isset($_GET['num']))
			{
				$start=(($_GET['num'])-1)*$rec_limit;
			}
			if(ceil($total/$rec_limit)>1)
			$paignation =  $this->getPagelink_1(ceil($total/$rec_limit),$start);
			$sql=$sql." limit ".$start.", ".$rec_limit;
			return array('sql'=>$sql,'pagination'=>$paignation);
		}
	}
function datasearch($search=NULL,$qual=NULL,$table=NULL,$field=NULL,$title=NULL)
{
$sql_filter="";
$sql="";
//if(isset($_POST['string']))
//{
//$search=$_POST['string'];
//$search=cleanup($search);
//}
$sql_filter="";
if(isset($search))
{
$search=strtolower($search);
$sql_filter=" and page_filter like '%$search%'";
$search=cleanup($search);
}
if(!$field && !$table){
   $sql="select id,catid,page_filter,page,title,status from pages where ";
   $sql_mid=" status='published'";
   $sql_limit=" limit 10";
   if(isset($qual))
   {
       $sql=$sql.$qual;
       echo " ";
   }
   $sql=$sql.$sql_mid.$sql_filter.$sql_limit;
}else{
   $field_filter = $field.'_filter';
   $sql="select id,{$title},{$field},{$field_filter} from {$table} where ";
   $sql_limit=" limit 10";
   $sql=$sql."{$field_filter} like '%{$search}%'";
   $query = @mysql_query($sql);
   if(mysql_num_rows($query)){
       echo '<p><b>No.of Search Results: </b>'.mysql_num_rows($query).'</p>';
       echo "<ul>";
       while($data = mysql_fetch_assoc($query)){
           $tit = $data[$title]?$data[$title]:'NA';
           echo "<li><h4><a href='javascript://' data-href='home.php?page=".$table."' class='search-details' data-sel='id=".$data['id']."' data-ptbl='".$table."'>".$tit."</a></h4></li><p>";
           $pos = strpos($data[$field_filter],$search);
           echo preg_replace("/(".$search.")/i",'<span class="highlight">'.$search.'</span>',substr($data[$field_filter],0,200));
           if(strlen($data[$field_filter]) > 200){
               echo '<span class=""><a href="#" class="readmore">... &nbsp;Read more</a><span class="main-span hide">'. 
               preg_replace("/(".$search.")/i",'<span class="highlight">'.$search.'</span>',substr($data[$field_filter],200,-1)).'</span><a href="#" class="readless hide">&nbsp;Read Less</a></span>';
           }
       }
       echo "</ul>";
   }
   else{
       echo $search." not found in any post";
       // exit;
   }
}

//echo $sql;
// if(!$result=mysql_query($sql))
if(false)
{
echo "Result not returned properly ".mysql_error();
echo $search." not found in any post";
}elseif(false)
{
	$cat=$this->category();
	echo "<ul>";
	while($row=mysql_fetch_array($result))
	{

	echo "<li><a href=\"home.php?page=update&id=".$row['id']."\"><b>".$cat[$row['catid']]."</b></a></li>";
	echo "<b>".strtoupper($row['title'])."</b>";
	$j=stripos($row['page_filter'],$search);
	if( false !== $j )
	{
	$k=strlen($search);

	$i=$j;$cnt=0;
	$str=substr($row['page_filter'],0,$i);
	while($i>0 && $cnt<15)
	{
	$i--;$cnt++;
	$str=substr($str,0,$i);
	$i=strrpos($str," ");
	//echo " ".$i." cnt=".$cnt;
	//echo substr($row['page_filter'],0,$j+$k);
	//echo "<br />";
	}
	echo "<p>".substr($row['page_filter'],$i,$j-$i);
	echo "<b>".substr($row['page_filter'],$j,$k)."</b>";

	echo substr($row['page_filter'],$j+$k,$j+$k-$i+200)."....</p>";
	}else {echo "<p>".substr($row['page_filter'],0,200)."....</p>";}
//echo "<br />".$row['pge']."</a>";
   }
   echo "</ul>";
}
//else echo "nothing to search";
}
function check_is_ajax() {
   $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND
   strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
   if(!$isAjax) {
     return false;
   }
   return true;
}

function extractDateFromQual($qual){
	$qual = trim($qual);
   if($qual)
   {
       preg_match_all('/\{\{.+?\}\}/',$qual,$matches, PREG_SET_ORDER);
       foreach ($matches as $val) { 
		   $oldval = $val[0]; 
		//    echo strpos($qual,$val[0]);
		   $newval = preg_replace('/\{\{(.+?)\}\}/', '$1', $val[0]); 
			$newval = date('Y-m-d 00:00',strtotime($newval));
           $qual = str_replace($oldval, strtotime($newval), $qual); 
       }
   }
//    check if unfinished and / or opertion then remove
if($qual){
	$qual = rtrim($qual," or");
	$qual = rtrim($qual," and");
}
   return $qual;
}


function createpage()
{
//Set Category
$category=isset($_GET['catname'])?$_GET['catname']:1;
echo "<input type=\"hidden\" name=\"catid\" value=\"".$category."\" />";

//call category function to retrieve cat names based on catid
$cat=$this->category();
echo "<h2>".strtoupper($cat[$category])."</h2>";

//Set Title
echo "<h3>Title:</h3>".'<input type="text" name="title" id="title" size="95" value="" required/>';
echo "<br /><br />";
echo "<textarea id=\"message\" name=\"message\" rows=\"15\" cols=\"80\">enter</textarea>";

if (get_magic_quotes_gpc()) $_POST = array_map('stripslashes', $_POST);

$catid = isset($_POST['catid']) ? $_POST['catid'] : 1;
$message = isset($_POST['message']) ? addslashes($_POST['message']) : 'Message';
$title=isset($_POST['title']) ? addslashes($_POST['title']) : 'Title';
$title=cleanup($title);
$message_filter=cleanup($message);
$message_filter=strtolower($cat[$category])." ".$title.$message_filter;

if(isset($_POST['submit']))
{

$tbl="pages";
$time=time();

$sql="insert into $tbl (catid,page,page_filter,status,author,time,link,title,modified_by) values($catid,'$message','$message_filter','draft','$_SESSION[SESS_uname]',$time,0,'$title','$_SESSION[SESS_uname]')";
//extractimage($message);
if(!$result=mysql_query($sql))
{ die(mysql_error());
}

}
echo "<input class=\"btn btn-primary\" name=\"submit\" type=\"submit\" value=\"Submit\" />";
}

function cleanup($msg)
{
$msg= strip_tags(html_entity_decode($msg));
//$msg=preg_replace('|[^a-zA-Z0-9_.,\s\t\r]|', '', $msg);
$msg=preg_replace('|[^a-zA-Z0-9_.,]([\s\t\r\n]+)|', ' ', $msg);
//$msg=preg_replace("|[']|", "", $msg);
$msg=strtolower ( $msg );
return $msg;
}

function extractimage($message)
{
$message=stripslashes($message);
$i=0;
while(strpos($message,'<img'))
{
$k=strpos($message,'<img');
$j= stripos($message,'/>',$k);
//echo "start of img ".$k."till position".$j."total length ".strlen($message);
//$sub1=substr($message,$k-1,$j);
$sub1=substr($message,$k,$j-$k+2);
//echo htmlentities($sub1);
// echo $sub1;
$message=substr($message,$j+1,strlen($message)-$j+1);
//echo $message;
$i++;
}
//echo addslashes($sub1);
$sub2=strstr($sub1,'/>',true)."/>";
//echo $sub2;
//echo substr($sub1,1,strstr($sub1,'/>')+2);
// and now we print out all the images
//preg_match_all('/< img.+ src = [\'"](?P< src >.+)[\'"].*>/i', $message, $images);
//$path="C:/wamp/www/editor/tinymce/uploads/";
//$ext=".jpeg";
//$fname=$path.md5((mt_rand(10,10000000000000))).$ext;
//$data=$sub1;
//$data = base64_decode($data);
//$im = imagecreatefromstring($data);
//if ($im !== false) {
 //  header('Content-Type: image/jpeg');
 //imagejpeg($im,$fname);
//    imagedestroy($im);
//}
// lets see the images array
//print_r( $images['src'] );
//echo $message_filter;
//echo htmlentities($sub1);
//echo htmlentities($message);
//preg_match_all("|<img(.*)/>|", $message, $match,PREG_PATTERN_ORDER);
//print_r($match);
//foreach($match as $val)
//{$i=0;
//echo "<img ".stripslashes($val[0][$i])." />";$i++;}
//echo "<img ".stripslashes($match[0][1])." />";
//echo stripslashes($match[1][0]);

}

function category($out='catname')
{
$sql="select id,catname from category";
$result=$this->allArray($sql);
// while($row_cat = mysql_fetch_array($result))
foreach( $result as $row_cat)
{
if($out=='catid')
$cat[$row_cat['catname']]=$row_cat['id'];
else
$cat[$row_cat['id']]=$row_cat['catname'];
}
return $cat;
}

function search()
{
echo "<input type=\"text\" name=\"qual\" value=\"\" />";
$qual=isset($_POST['qual'])? $_POST['qual']." and status='draft'":"status='draft'";
if(isset($_POST['qual']))
{
$qual=$_POST['qual'];
while(strpos($qual,'time'))
{$k=strpos($qual,'time');
$j= stripos($qual,'"',$k);
$p= stripos($qual,'"',$k+$j);
$sub=substr($qual,$j+1,$p);
echo $sub;
$qual=str_replace($sub,$this->setmydate($sub),$qual);
echo $qual;
}
}
}
// function getmydate($time)
// {
// $tdate = date_create();
// date_timestamp_set($tdate,$time);
// return date_format($tdate, 'd-m-Y');
// }
function getmydate($time)
{
	if(trim($time) && $time > 0)
	{
	   $tdate = date_create();
	// if (!preg_match("/\d{4}\-\d{2}-\d{2}/", $time)) $time = strtotime($time);
	if($time == 'today') $time = time();
	// date_timestamp_set($tdate,$time);
	
	// return date_format($tdate, 'd-M-y H:i');
	if(!is_numeric($time)) return $time;
	return date("d-M-y",$time);
	}else{
	   return NULL;
	}
}
function getmytime($time)
{
$tdate = date_create();
// if (!preg_match("/\d{4}\-\d{2}-\d{2}/", $time)) $time = strtotime($time);
$time=$time+19800;
date_timestamp_set($tdate,$time);
return date_format($tdate, 'd-m-Y H:i:s');
}
function setmydate($time)
{
	if($time){
		$ts1 = date_create($time);
		return date_format($ts1,'U');
	}else return '0';
}

function comments($pageid)
{
$db_name="editor";
$tbl1="comments";
if(isset($_POST['comment']) && isset($_POST['com']))
{
 insert_data('comments');


//echo "<meta http-equiv=\"refresh\" content=\".1\">";

// header("location: home.php?page=update&id=$pageid");
//echo "<meta http-equiv='refresh' content='0;url=home.php?update&id=$pageid'>";
//exit();
}
$sql="select id,comment,author,time from $tbl1 where pageid=$pageid";
$time=time();
$i=1;
if($result=mysql_query($sql))
{
while($row=mysql_fetch_array($result))
{

echo "<p>";
echo "<b>".'Comment'.$i."."."</b>";
echo "<br />commneted by :".$row['author'];
echo "<br />at :".getmytime($row['time']);
echo "<br />".$row['comment'];
echo"</p>";
echo "<hr/>";
$i++;
}
}else{
die(mysql_error());
}
//if($status!='draft')
{
echo "<form role=\"form\" name=\"form1\" id=\"frm1\" action=\"home.php?page=update&id=$pageid\" method=\"POST\">";
echo "<p><h3>Leave a comment</h3><br />";
echo  "<b>Message:</b>&nbsp;<textarea name=\"comment\" rows=\"4\" cols=\"40\" required></textarea>";
echo  "<br /><input type=\"hidden\" name=\"author\" value=\"".$_SESSION['SESS_uname']."\">";
echo  "<br /><input type=\"hidden\" name=\"time\" value=\"".time()."\">";
echo  "<br /><input type=\"hidden\" name=\"pageid\" value=\"".$pageid."\">";
echo "<input class=\"btn btn-primary\" name=\"com\" type=\"submit\" value=\"comment\" /></p>";
echo "</form>";
}
}

function firstOfMonth($m)
{
return date("Y/n/j", mktime(0,0,0,$m,'01',date("Y")));
}

function lastOfMonth($m)
{
$m=$m+1;
return date("w:D:Y/n/j", mktime(0,0,0,$m,0,date("Y")));
}

function edit($pageid,$mod){

//include('rowaccess.php');
if (get_magic_quotes_gpc()) $_POST = array_map('stripslashes', $_POST);

$subject = isset($_POST['subject']) ? $_POST['subject'] : 'Subject';
$message = isset($_POST['message']) ? addslashes($_POST['message']) : 'Message';
$title = isset($_POST['title']) ? addslashes($_POST['title']) : 'Title';

$message_filter=cleanup($message);
$message_filter=cleanup($message);
$message_filter=strtolower($cat[$category])." ".$title.$message_filter;


$db_name="editor";
$tbl="pages";



if(isset($_POST['submit']))
{
	$dd=$_POST['id'];
	$cat=$this->category('catid');
	echo $subject;

   $d='draft';
   $time=time();
//echo "Post author is :".$_POST['author1'];
//echo "session name is :".$_SESSION['SESS_uname'];
//echo " authorinsubmit= ".$author;

   if($_POST['author1']==$_SESSION['SESS_uname'])
   {

   $date=time();
   $sql="update $tbl set page='$message',page_filter='$message_filter',time='$date',modified_by='$_SESSION[SESS_uname]'where id='$pageid'";
   //echo $sql;
       if(!$result=mysql_query($sql))
       { die(mysql_error());
       }//end if
   }else
   {
   $sql="INSERT INTO $tbl (catid,page,page_filter,status,author,time,link,title) VALUES ('$cat[$subject]','$message','$message_filter','$d','$_SESSION[SESS_uname]','$time','$dd','$title') ";
if(!$result=mysql_query($sql))
		{ die(mysql_error());
		}//end if
}
}
	$sql="select id,title,catid,page,page_filter,status,author,time,modified_by,flag from $tbl where id=$pageid";
	$cat=$this->category();
	if($result=mysql_query($sql))
	{
		while($row=mysql_fetch_array($result))
		{
			if(isset($_POST['edit']))
			{
			echo "author<input type=\"hidden\" name=\"author1\" value=\"".$row['author']."\" />";
			echo "<h2>".strtoupper($cat[$row['catid']])."</h2>";
			echo  '<textarea id="message" name="message" rows="15" cols="80">'.$row['page'].'</textarea>';
			echo  "<input type=\"hidden\" name=\"subject\" value=\"".$cat[$row['catid']]."\" />";
			echo "<input type=\"hidden\" name=\"id\" value=\"".$row['id']."\" />";
			echo "<input type=\"hidden\" name=\"title\" value=\"".$row['title']."\" />";

			echo "<input class=\"btn btn-primary\" name=\"submit\" type=\"submit\" value=\"Submit\" />";
			}else
			{

			echo "<input type=\"hidden\" name=\"author\" value=\"".$row['author']."\" />";
			echo "<h2>".strtoupper($cat[$row['catid']])."</h2>";
			echo "<b>Title :".$row['title']."</b>";
			echo "<br />";
			echo "<b>Author :".$row['author']."</b>";
			echo "<br /><b>Last Update time :".getmytime($row['time'])."</b>";
			echo "<br /><b>Last Update by :".$row['modified_by']."</b>";
			echo "<br />".$row['page'];
			if(	$_SESSION['SESS_uname']==$row['author'] || $mod==1 || !$row['status']=='draft' || $_SESSION['SESS_perm']=='admin')
			echo "<input class=\"btn btn-primary\" name=\"edit\" type=\"submit\" value=\"Edit\" />";
			echo "<hr/>";
			echo "<p>";
			echo "</p>";

			}
		$flag=$row['flag'];
		return $flag;
		}
	}

}

function show()
{

$db_name="editor";
$tbl="pages";

$pageid=isset($_GET['id'])?$_GET['id'] : 3;
$sql="select id,title,catid,page,page_filter,status,author,time,modified_by from $tbl where id=$pageid";
$cat=$this->category();
if($result=mysql_query($sql))
{
while($row=mysql_fetch_array($result))
{
if(isset($_POST['edit']))
{//echo '<input type="text" name="subject" value="'.$cat[$row['catid']].'" />';
echo "<h2>".strtoupper($cat[$row['catid']])."</h2>";
echo  '<textarea id="message" name="message" rows="15" cols="80">'.$row['page'].'</textarea>';
echo  "subject<input type=\"text\" name=\"subject\" value=\"".$cat[$row['catid']]."\" />";
echo '<input type="hidden" name="id" value="'.$row['id'].'" />';
echo '<input type="text" name="title" value="'.$row['title'].'" />';

echo "<input class=\"btn btn-primary\" name=\"submit\" type=\"submit\" value=\"Submit\" />";
}elseif(!(isset($_POST['edit'])) || isset($_POST['search']))
{


echo "<h2>".strtoupper($cat[$row['catid']])."</h2>";
echo "<b>Title :".$row['title']."</b>";
echo "<br />";
echo "<b>Author :".$row['author']."</b>";
echo "<br /><b>Last Update time :".getmytime($row['time'])."</b>";
echo "<br /><b>Last Update by :".$row['modified_by']."</b>";
echo "<br />".$row['page'];

//if(isset($_SESSION['SESS_uname']=='admin'))
//{
if($row['status']!='draft' || $_SESSION['SESS_uname']=='admin' || $_SESSION['SESS_uname']==$author)
echo "<input class=\"btn btn-primary\" name=\"edit\" type=\"submit\" value=\"Edit\" />";//}
echo "<hr/>";
echo "<p>";
echo "</p>";
//return array($row['id'],$row['catid'],$cat[$row['catid']],$row['page'],$row['title'],$row['status']);
}
}}

}
function enc()
{
$key_value = "123321";
$plain_text = "YqvjywySafVDSDej";
$encrypted_text = mcrypt_ecb(MCRYPT_DES, $key_value, $plain_text, MCRYPT_ENCRYPT);


$decrypted_text = mcrypt_ecb(MCRYPT_DES, $key_value, $encrypted_text, MCRYPT_DECRYPT);
return $decrypted_text;
}

function getUsers($users,$users_group){
	$group  = false;
	$sql = "select * from users ";
	if($users || $users_group) $sql .= 'where ';
	if($users_group){
		$group = str_replace(',','|',$users_group);
		// $sql .=  "access REGEXP \"[[:<:]](".$group.")[[:>:]]\" ";
		$sql .=  "access LIKE '%".$group."%'";
	}
	if($users && $group) $sql .= " OR empid in ($users) ";
	if($users && !$group) $sql .= " empid in ($users) ";
	// echo $sql;
	$query = $this->all($sql);
		$data = array();
	if($query){
		// while($res = mysql_fetch_object($query)){
			foreach($query as $res){
			$data[] = $res;
		}
	}
	return $data;
}

function userByTblFileds($tbl,$fields="",$id){
	$ids = array();
	if($fields){
		$fields = explode(',',$fields);
		foreach($fields as $field){
			$sql = "select {$field} from {$tbl} where id = {$id}";
			$user = $this->first($sql);
			array_push($ids,$user->$field);
		}
	}
	return $ids;
}

function contentMap($content,$arr=array(),$is_primary=false){
   // write_log('debug','ttteeee');
   preg_match_all("({{([^}]+)\}\})", $content, $str,PREG_PATTERN_ORDER);
   $files=array();
   $data = array();
   
   if(!empty($arr)){
       foreach($arr as $tbl => $id){
           $data[$tbl] = $this->getDataValue($tbl,$id,$is_primary);
       }
   }
   // print_r($data['pipeline']);
   if(isset($str[0])){
       foreach($str[0] as $spr)
       {
           // echo $spr.'<br>';
           $sr=str_replace("{{","",$spr);
           $sr=str_replace("}}","",$sr);
           $var=htmlentities($sr);
           $vr=explode(".",$sr);
           // print_r($data[$vr[0]]);
        //    if(isset($data[$vr[0]][$vr[1]])){
			if(isset($data[$vr[0]]) && isset($data[$vr[0]][$vr[1]])){
               if($data[$vr[0]][$vr[1]]['type'] != 'file'){
                   $content = str_replace($spr,$data[$vr[0]][$vr[1]]['value'],$content);
               }else{
                   $files = $data[$vr[0]][$vr[1]]['value'];
                   $content = str_replace($spr,'',$content);
               }
           }
       }
   }
//    print_r($data);
   return array('content'=>$content,'files'=>$files);
}

function getDataValue($tbl,$id,$is_primary=false){
	$qual = "";
	$data = array();
		$qual = " where id = {$id}";
		// $qual = " where {$p_field} = {$id}";
		$sql = "select * from {$tbl} $qual";
		$record = $this->firstArray($sql);
		if($record){
			$data = array($tbl);
			foreach($record as $key=>$value){
				$data[$key] = $this->getDataType($tbl,$key,$value);
			}
		}
		// print_r($data);
	return $data;
}

function getSysidFromTable($tbl){
	$p_key_sql = $this->firstArray("show COLUMNS from {$tbl} where Extra = 'auto_increment'");
	$sysid ='';
	if($p_key_sql){
		$sysid = $p_key_sql['Field'];
	}
	return $sysid;
}

function getDataType($tbl,$field,$value){
	$sql = "select fieldid,tblid,type from field where tblid in (select tblid from config where name = '{$tbl}') and name = '{$field}'";
	$field = $this->firstArray($sql);
	if($field){
		$tblid = $field['tblid'];
		$fieldid = $field['fieldid'];
		if($field['type'] == 'list'){
			$field['value'] = $this->getFirstListAlias($fieldid,$value);
		}else if($field['type'] == 'option'){
			$data = $this->firstArray("select alias from field_option where tblid = '{$tblid}' and fieldid = '{$fieldid}'");
			if($data){
				$field['value'] = $data['alias'];
			}else{
				$field['value'] = $value;
			}
		}else if($field['type'] == 'idate'){
			if($value && is_int($value)){
				$field['value'] = date('d-M-y H:i',$value);
			}else{
				$field['value'] = '';
			}
		}else{
			$field['value'] = $value;
		}
	}
	// print_r($field);
	return $field;
}

function write_log($prefix="",$msg){
   $today_log = 'logs.log';
   $real_path = __DIR__;
   if(file_exists($real_path.'/../logs/'.$today_log)){
       file_put_contents($real_path.'/../logs/'.$today_log,$msg,FILE_APPEND);
   }else{
       if(!is_dir($real_path.'/../logs/')) mkdir($real_path.'/../logs',0777,true);
	   file_put_contents($real_path.'/../logs/'.$today_log,$msg,FILE_APPEND | LOCK_EX);
	   chmod($real_path.'/../logs/'.$today_log, 0755);
   }
}

function scheduleTime($type,$time,$days,$start_at,$hours,$new=false,$current){
   $current_exe_time = strtotime(date('Y-m-d H:i:00'));
   $next_exe_time = strtotime(' +1 day',$current_exe_time);
   $_hours =array();
   if($hours) {
       $_hours[] = date('H',strtotime($hours));
       $_hours[] = date('i',strtotime($hours));
   }
   if($type == 'daily'){
       $current_exe_time = strtotime(date("Y-m-d").' '. $hours);
       if($new) $next_exe_time = $current_exe_time;
       else $next_exe_time = strtotime(" +1 day",$current_exe_time);
       // echo date("Y-m-d").' '. $hours;
   }
   elseif($type == 'weekly'){
       $current_exe_time = strtotime("+{$_hours[0]} +{$_hours[1]}",strtotime($days.' this week'));
       $next_exe_time = strtotime("+1 week",$current_exe_time);

   }elseif($type == 'monthly'){
       $current_exe_time = strtotime("+{$_hours[0]} +{$_hours[1]}",strtotime(date('Y-m-'.$days)));
       $next_exe_time = strtotime("+1 week",$current_exe_time);
   }elseif($type == 'periodically'){
       if($new) $current_exe_time = $start_at;
       else  $current_exe_time = strtotime($current);
       if($new) $next_exe_time = $start_at;
       else $next_exe_time = strtotime("+{$days} days",$current_exe_time);
   }
   elseif($type == 'once'){
       if($new) $current_exe_time = $start_at;
       else  $current_exe_time = strtotime($current);
       if($new) $next_exe_time = $start_at;
       else $next_exe_time = strtotime("+{$days} days",$current_exe_time);
   }else{
       $current_exe_time = $start_at;
       $next_exe_time = $next_exe_time;
   }
   return array('current'=>$current_exe_time,'next'=>$next_exe_time);
}


function getNewTblQualWorkflow($tbl,$qual){
	$status = $this->getStatus($tbl,$qual);
	// echo $status;
	$fieldlist = "";
	$field_edit = "";
	$action_buttons = "";
	$view_name = "";
	$default_values="";
	if($status !== false){
		$group = $_SESSION['SESS_access'];
		// $regexp =  "and permission REGEXP \"[[:<:]](".$group.")[[:>:]]\" ";
		$regexp =  "and permission LIKE '%".$group."%'";
		$workflow_sql = "select fieldlist,defaultfields,field_edit,action_buttons,view_name from workflow where formname = '{$tbl}' and status = ".$status.' '.$regexp;
	}else{
		$workflow_sql = "select fieldlist,defaultfields,field_edit,action_buttons,view_name from workflow where formname = '{$tbl}' and status = 0 ".$regexp;
	}
	// echo $workflow_sql;
		$data = $this->first($workflow_sql);

		if($data){
			$default_values=$data->defaultfields;
			if(isset($default_values)){
				$default_values = $this->parseFilter($default_values);
				// $empid=$_SESSION['SESS_empid'];
				// $today_date=date('d-m-Y');
				// $default_values=str_replace('SESSIONEMPID',$empid,$default_values);
				// $default_values=str_replace('TODAY',$today_date,$default_values);
			}
			$field_edit = $data->field_edit;
			$fieldlist=$data->fieldlist;
			$action_buttons = $data->action_buttons;
			$view_name = $data->view_name;
		} 
	return array('default_values'=>$default_values,'fieldshow'=>$fieldlist,'fieldedit'=>$field_edit,'action_buttons'=>$action_buttons,'view_name'=>$view_name);

}
// get current status from table if table is new  returns 0
function getStatus($tbl,$qual){
   $res = 0;
   if($qual){
       $sql = "select status from {$tbl} where {$qual}";
       $data = $this->first($sql);
       if($data){
           $res = $data->status;
       }
   }
   return $res;
}

function getOrderNumber($customer_id=NULL){
	// if prefix state code
	$now = strtotime(date("Y-m-d 00:00"));
	$to = strtotime(date("Y-m-d 23:59"));
	$time = time();
	$flag = true;
	$customer = $this->first("select s.short_description from states s join customers c on s.id = c.state_id where c.id = {$customer_id}");
	if($customer && !empty($customer)){
		$st_prefix = $customer->short_description;
		$sql = "select o.id,o.next_order,f.order_prefix,o.prefix from order_sequence o join financial_years f on o.finanical_year_id = f.id where o.status = 1 and
		f.valid_from < $to and f.valid_to > $now and o.prefix = '{$st_prefix}'";
		// echo $sql;
		$data = $this->first($sql);
		if(!$data){
			$financial_year_sql = "select * from financial_years where valid_from < $to and valid_to > $now";
			// echo $financial_year_sql;
			$financial_year = $this->first($financial_year_sql);
			if($financial_year){
				$fin_id = $financial_year->id;
				$fin_prefix = $financial_year->order_prefix;
				$next_order = $fin_prefix."000002";
				$next_order = intval($next_order);
				// disable all expired orders by seting status 0
				// $this->executeQuery("update order_sequence set status =0");
				// insert new sequence
				$insert = "insert into order_sequence (created_at,status,next_order,finanical_year_id,prefix)
				values($time,1,$next_order,$fin_id,'{$st_prefix}')";
				$this->insertData($insert);
				$current = $st_prefix.$fin_prefix."000001";
			}else{
				$flag = false;
			}
		}else{
			$id = $data->id;
			$current = $data->next_order;
			$next = $current+1;
			$current = $data->prefix.$current;
			$update = "update order_sequence set next_order = '{$next}' where id='{$id}'";
			$this->executeQuery($update);
		}
	}else{
		$flag = false;
	}
	if($flag) return $current;
	else return false;
}
function getInvoiceNumber($loc_id){
	// if prefix state code
	$now = strtotime(date("Y-m-d 00:00"));
	$to = strtotime(date("Y-m-d 23:59"));
	$time = time();
	$flag = true;
	$location = $this->first("select prefix from izt_details where id= {$loc_id}");
	if($location && !empty($location)){
		$st_prefix = $location->prefix;
		$sql = "select i.id,i.next_invoice,f.order_prefix,i.prefix from invoice_sequence i join financial_years f on i.finanical_year_id = f.id where i.status = 1 and
		f.valid_from < $to and f.valid_to > $now and i.prefix = '{$st_prefix}'";
		// echo $sql;
		$data = $this->first($sql);
		if(!$data){
			$financial_year_sql = "select * from financial_years where valid_from < $to and valid_to > $now";
			// echo $financial_year_sql;
			$financial_year = $this->first($financial_year_sql);
			if($financial_year){
				$fin_id = $financial_year->id;
				$fin_prefix = $financial_year->order_prefix;
				$next_order = "002";
				$next_order = intval($next_order);
				// disable all expired orders by seting status 0
				// $this->executeQuery("update order_sequence set status =0");
				// insert new sequence
				$insert = "insert into invoice_sequence (created_at,status,next_invoice,finanical_year_id,prefix)
				values($time,1,$next_order,$fin_id,'{$st_prefix}')";
				$this->insertData($insert);
				$current = $st_prefix.'-'.$fin_prefix.'-'."001";
			}else{
				$flag = false;
			}
		}else{
			$id = $data->id;
			$current = $data->next_invoice;
			$next = $current+1;
			$current = $data->prefix.'-'.$data->order_prefix.'-'.str_pad($current, 3, "0", STR_PAD_LEFT);
			$update = "update invoice_sequence set next_invoice = '{$next}' where id='{$id}'";
			$this->executeQuery($update);
		}
	}else{
		$flag = false;
	}
	if($flag) return $current;
	else return false;
}
function getQuotationNumber($loc_id){
	// if prefix state code
	$now = strtotime(date("Y-m-d 00:00"));
	$to = strtotime(date("Y-m-d 23:59"));
	$time = time();
	$flag = true;
	$location = $this->first("select q_prefix from izt_details where id= {$loc_id}");
	if($location && !empty($location)){
		$st_prefix = $location->q_prefix;
		$sql = "select i.id,i.next_quotation,f.q_prefix as order_prefix,i.prefix from quotation_sequence i join financial_years f on i.financial_year_id = f.id where i.status = 1 and
		f.valid_from < $to and f.valid_to > $now and i.prefix = '{$st_prefix}'";
		// $this->write_log("debug",$sql);
		$data = $this->first($sql);
		// $sql = "select id,next_quotation,prefix from quotation_sequence where status = 1 and prefix = '{$st_prefix}'";
		// // echo $sql;
		if(!$data){
			$financial_year_sql = "select * from financial_years where valid_from < $to and valid_to > $now";
			$financial_year = $this->first($financial_year_sql);
			if($financial_year){
				$fin_id = $financial_year->id;
				$fin_prefix = $financial_year->q_prefix;
				$next_order = "002";
				$next_order = intval($next_order);
				// insert new sequence
				$insert = "insert into quotation_sequence (created_at,status,next_quotation,financial_year_id,prefix)
				values($time,1,$next_order,$fin_id,'{$st_prefix}')";
				$this->insertData($insert);
				$current = $st_prefix.'-'.$fin_prefix."001";
			}else{
				$flag = false;
			}
			
		}else{
			$id = $data->id;
			$current = $data->next_quotation;
			$next = $current+1;
			$current = $data->prefix.'-'.$data->order_prefix.str_pad($current, 3, "0", STR_PAD_LEFT);
			$update = "update quotation_sequence set next_quotation = '{$next}' where id='{$id}'";
			$this->executeQuery($update);
		}
	}else{
		$flag = false;
	}
	if($flag) return $current;
	else return false;
}

function getButtonAccess($tbl,$operation){
	$group = $_SESSION['SESS_access'];
	$btn = "";
	// $access_sql = "select * from access where page_name='{$tbl}' and groupname REGEXP \"[[:<:]](".$group.")[[:>:]]\" ";
	$access_sql = "select * from access where page_name='{$tbl}' and groupname LIKE '%".$group."%'";
	$data = $this->first($access_sql);
	if($data || $group == '1'){
		// <input class="btn btn-primary" id="btn" type="submit" name="addrow" value="Add Row">
		$addrow = "<input class=\"btn btn-info\" type=\"submit\" value=\"Save\" name=\"addrow\" style=\"float:right;\">";
		// $addrow = "<input class=\"btn btn-info\" type=\"submit\" id=\"".$tbl."\" value=\"Save\" name=\"alBtn\" style=\"float:right;\">";
		//$update = "<button class=\"btn btn-info\" type=\"submit\" id=\"".$tbl."|update\" value=\"update\" name=\"updates\">Update</button>";
    	$update = "<button class=\"btn btn-info\" type=\"submit\" id=\"".$tbl."\" value=\"update\" name=\"updates\" style=\"float:right;\">Update</button>";
    	$new = "<button class=\"btn btn-danger\" type=\"submit\" name=\"new\" value=\"new\" /><i class='glyphicon glyphicon-plus-sign
    '></i></button>";
		if((($data && $data->submit == '1')|| $group == 1) && $operation == 'new') $btn=$addrow;
		if((($data && $data->submit == '1')|| $group == 1) && ($operation == 'back' || $operation == 'list')) $btn=$new;
		if((($data && $data->modify == '1')|| $group == 1) && ($operation == 'updates' || $operation == 'click')) $btn = $update;
		if((($data && $data->search == '1')|| $group == 1) && ($operation =='search')) $btn=1;
	}
	return $btn;
}

function getActionButton($tbl,$btns){
	$a="";
	$a.= "<div class='col-md-6 pull-right text-right'>";
	foreach($btns as $btn){
		list($key,$value) = explode(":",$btn); 
		$a.= "&nbsp;<button class=\"btn btn-info btn-md status-cls\" id=\"".$tbl."\" name=\"alBtn\" type=\"button\" value=\"".$key."\" name=\"".$key."\">".$value."</button>";
	}
	$a.= "</div>";
	$a.= "<div class='clearfix'>&nbsp;</div>";
	return $a;
}

function addAutoStatus($tbl,$current_status){
   $btn = "select ";
}
function getButtonNameForCurrentStatus($tbl,$current_status){

}
function getTblQualWorkflow($tbl,$qual,$mode){
	$qual = $qual? " where {$qual}":"";
	$status_sql = "select * from {$tbl}".$qual;
	$status = false;
	$fields = "";
	$status_query = $this->first($status_sql);
	if($status_query){
		$status = isset($status_query->status)? $status_query->status : false;
	}
	//  check statusbar table and field is enable
	$statusbar_query = $this->allArray("select * from statusbar where tblname='{$tbl}' and visible = '1'");
	$check_status = false;
	$_status ="";
	$regexp ="";
	if($statusbar_query && !empty($statusbar_query)) $check_status = true;
	// echo $status;
	// if($status !== false){
	if(true){
		$fieldlist = 'fieldlist';
		if($check_status){
			$group = str_replace(';','|',$_SESSION['SESS_access']);
			// if($mode) $regexp =  "and {$mode} REGEXP \"[[:<:]](".$group.")[[:>:]]\" ";
			if($mode) $regexp =  "and permission LIKE '%".$group."%'";
			if($mode == 'edit_mode') $fieldlist = 'field_edit';
			else if($mode == 'create_mode') $fieldlist = 'field_show';
			else  $fieldlist = 'fieldlist';
			$_status ="and status = '{$status}' ";
		}
		$workflow_sql = "select {$fieldlist} from workflow where formname = '{$tbl}' ".$_status.$regexp;
		// echo $workflow_sql;
		$field_access_query = $this->first($workflow_sql);
		if($field_access_query){
			$fields = $field_access_query->$fieldlist;
		}
	}
	// echo $fields;
	return $fields;

}

function getTblFieldList($tbl,$fieldlist){
	$fields = "";
	$workflow_sql = "select {$fieldlist} from workflow where formname = '{$tbl}'";
	$field_access_query = $this->first($workflow_sql);
	if($field_access_query){
		$fields = $field_access_query->$fieldlist;
	}
	return $fields;

}

function getField($list,$tblid,$feildid,$cnt,$datarow=array(),$rowname,$is_view=false){
	
	// $a=$a.$tg_td.$tg_sel.$row['name'].$cnt.$tg_ip_name.$row['name'].$cnt.$tg_cl;
	//adding new on13/12/2014.can be deleted
	// print_r($datarow);
	$sql_filter="select source,filter,id,value,alias,multi_option from valuelist where id in (select optid from field_option where tblid=".$tblid." and fieldid=".$feildid.")";
	// echo $sql_filter;
	$result_filter=$this->firstArray($sql_filter);
	$vsource= $result_filter['source'];
	$vfilter= $result_filter['filter'];
	$vid= $result_filter['id'];
	$vvalue= $result_filter['value'];
	$valias= $result_filter['alias'];
	// echo $vfilter;
	if($vfilter){
		preg_match_all('/\{\{.+?\}\}/',$vfilter,$matches, PREG_SET_ORDER);
		foreach ($matches as $val) { 
			$oldval = $val[0]; 
			$newval = preg_replace('/\{\{(.+?)\}\}/', '$1', $val[0]); 
			$vfilter = str_replace($oldval, "'".$datarow[$newval]."'", $vfilter); 
			$vfilter = stripslashes($vfilter);
		}
	}
	
	if($is_view) $a = $datarow[$rowname];
	else $a = "<input type='text' value='".$datarow[$rowname]."' name='".$rowname.$cnt."' data-source='".$vsource."' data-filter=\"".$vfilter."\" data-field='".$vvalue."'><a href='#' data-toggle='modal' data-target='#modal-bucket'>...</a>";
	return $a;
}

function getTblRawFields($tbl){
	$sql = "select name, alias from field where tblid in (select tblid from config where name ='{$tbl}')";
	$fields = $this->all($sql);
	$names = array();
	$alias = array();
	foreach($fields as $field){
		$names[] = $field->name;
		$alias[] = $field->alias;
	}
	return array('name'=>$names,'alias'=>$alias);
}

function getTableAlias($tbl){
	$alias = "";
	$sql = "select * from config where name='{$tbl}'";
	$query = $this->first($sql);
	if($query){
		$alias = $query->alias;
	}
	return $alias;
}

function getTableName($tblid){
	$name = "";
	$sql = "select name from config where tblid='{$tblid}'";
	$query = $this->first($sql);
	if($query){
		$name = $query->name;
	}
	return $name;
	
}

function getFilePath($tbl){

   $sql = "select upload_dir from path_dir where table_name = '$tbl'";
   $result=$this->first($sql);
   if($result && !empty($result)){
       return $result->upload_dir;
   }
   else 
   return "no upload_dir in path_dir";
   }
   function getAliasValue($tbl,$val)
   {
           $sql="select * from $tbl where id='$val'";
           $result=$this->firstArray($sql);
           return $result;
   }

   function displayOrdersDashboard($array_data,$tbl)
   {
	   
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
		//    print_r($field_value);
		   // $res=mysql_query("select plant_name from source_master where id='$srcid'");
		   
           $res=$this->getAliasValue('source_master',$field_value['source_id']);
		   $res1=$this->getAliasValue('customers',$field_value['customer_id']);
		   $res2=$this->getAliasValue('payment_types',$field_value['payment_types_id']);
		   $bank_sql = $this->getAliasValue('bank_master',$field_value['bank_id']);
		   $bank = ($bank_sql && !empty($bank_sql)) ? $bank_sql['name_bank']:'NA';
		   $cust_id = $field_value['customer_id'];
		   $virtual_ac = "";
		  
		   $paymentid=$field_value['Payment_id'];
		   $paymentid = strlen($paymentid) > 20 ? substr($paymentid,0,20): $paymentid;
		   $source = "";
		   $customer="";
		   $paymenttype="";
           if(isset($res['plant_name'])) $source=$res['source_short_desc'];
		   if(isset($res1['customer_name1'])) $customer=$res1['customer_name1'];
		   if(isset($res2['type_payment'])) $paymenttype=$res2['type_payment'];
		   else $paymenttype="";
		   if($modifyaccess){
			//    if($field_value['final_value'] > $field_value['payment_amount'])
			//    {
			// 		$a.= "<div class=\"panel panel-default\" style=\"border-radius: 0 25px 25px 25px;background-color:#f5dfdf;border-color:red;\">";
			// 		$a.= "&nbsp;	<input type=\"checkbox\" name=\"chb[".$field_value['id']."]\"  value=\"".$field_value['id']."\">";
			//    }
			//    else
			//    {
			  		$a.= "<div class=\"panel panel-default\" style=\"border-radius: 0 25px 25px 25px;background-color:#f4f9fb\">";
			   		$a.= "&nbsp;	<input type=\"checkbox\" name=\"chb[".$field_value['id']."]\"  value=\"".$field_value['id']."\">";
			//    }
			}else{
				// if($field_value['final_value'] > $field_value['payment_amount'])
				// 	$a.= "<div class=\"panel panel-default\" style=\"border-radius: 25px 25px 25px 25px;background-color:#f5dfdf;border-color:red;\">";
				// else
					$a.= "<div class=\"panel panel-default\" style=\"border-radius: 25px 25px 25px 25px;background-color:#f4f9fb\">";
		   }
           $a.= "<div class=\"panel-body\">";
           $a.= "<div class='col-md-2 p0 '><a onclick=openRec(".$field_value['id'].",'".$tbl."'".") href=\"#\""."><label>&nbsp;&nbsp;Doc No: ".$field_value['prefix'].$field_value['order_no']."</label></a>".
		   "<p>&nbsp;&nbsp;".$field_value['order_date']."</p>".	
		     	
				"</div>";	   
			$a.= "<div class='col-md-2 p0'>".
					"<p><b>Customer:</b>&nbsp;".$customer."</p>".  
					"<p><b>Source:</b>&nbsp;".$source."</p>".
				"</div>";
			$a.= "<div class='col-md-2 p0'>".
					"<p>".$bank."</p>".
					"<p><b>Payment Type:</b>&nbsp;".$paymenttype."</p>".
					
				"</div>";
			$a.= "<div class='col-md-2	 p0'>".
			"<p><b>Quantity:</b>&nbsp;".$field_value['total_quantity']."</p>".
					"<p><b>Payment Ref:</b>&nbsp;".$paymentid."</p>".
				 "</div>"; 
           $a.= "<div class='col-md-2 p0'>".
		   			"<p><b>Total Value:</b>&nbsp;".$field_value['final_value']."</p>".  
		   			"<p><b>Paid Amount:</b>&nbsp;".$field_value['payment_amount']."</p>".  
                "</div>";   		
           if(in_array($field_value['status'],array_keys($st)))
           {
			$status_date="";
			if(isset($field_value[$st_date[$field_value['status']]]) && $field_value[$st_date[$field_value['status']]])
			// echo $field_value['modified_at'];
				$status_date = $field_value[$st_date[$field_value['status']]];
			// else
               $a.= "<div class='col-md-1 p0'>"."<b>".$st[$field_value['status']]."</b>"."<br>".$status_date."</div>";
			   $a.= "<div class='col-md-1 p0'>"."<img src=\"images/".$imaget[$field_value['status']]."\"></img>";
			//    $a.="</div>";
           }    
		   else 
		   {
			   $a.= "<div class='col-md-2 p0'>".""."</div>";
		   }
		   if($field_value['final_value'] > $field_value['payment_amount'])
		   {
				$a.=  "&nbsp;<img title=\"Payment Pending\" src=\"images/"."alert.png"."\"></img>"."</div>";
				// $a.= "<div class='col-md-1 p0'>"."<img title=\"Payment Pending\" src=\"images/"."alert.png"."\"></img>"."</div>";
		   }
			else
			{
				$a.="</div>";
				// $a.= "<div class='col-md-1 p0	'>".""."</div>";
			}		
           $a.= "</div>";
           $a.= "</div>";
	   }
	   if(empty($array_data)){
		   $a.= "<div>No Data</div>";
	   }
	   if($modifyaccess) $a.= "</div>";
       return $a;
   }
   function displayOrdersList($array_data,$tbl)
   {
		$b ="";
       $a="";
       $st=array("0"=>"Draft","1"=>"Ordered","2"=>"SentBack","3"=>"Reordered","4"=>"Confirmed","5"=>"Dispatched","6"=>"Rejected","7"=>"Cancelled","8"=>"Delivered");
       $st_date=array("0"=>"created_at","1"=>"order_date","2"=>"send_back_date","3"=>"reordered_time","4"=>"confirmed_time","5"=>"dispatch_date","6"=>"rejected_time","7"=>"cancelled_time","8"=>"truck_arrival_date");
	   $imaget=array("0"=>"draft.png","1"=>"ordered.png","2"=>"sentback.png","3"=>"reorder.png","4"=>"confirm-order.png","5"=>"dispatch.png","6"=>"rejected.png","7"=>"cancel.png","8"=>"delivered.png");    
	   $a .= "<table class=\"table table-fixed \">";
	   $a .= "<thead>";
	   $a .= "<tr><td class=\"col-md-1\"><span><b>S.No.</b></span> </td><td class=\"col-md-1\"><span><b>Order No</b></span>  </td><td class=\"col-md-1\"><span><b>&nbsp;Ordered</b></span> </td><td class=\"col-md-1\"><span><b>Quantity</b></span> </td><td class=\"col-md-1\"><span><b>Value</b></span> </td><td class=\"col-md-1\"><span><b>Paid Amount</b></span></td></tr>";
	//    $a .="<tr><th class=\"col-md-1\">S.No.</th><th class=\"col-md-1\">Order No</th><th class=\"col-md-1\">Ordered</th class=\"col-md-1\"><th class=\"col-md-1\">Total Quantity</th><th class=\"col-md-1\">Total Value</th><th class=\"col-md-1\">Paid Amount</th>";
	//    $a .="<t class=\"col-md-1\">S.No.</th><th class=\"col-md-1\">Order No</th><th class=\"col-md-1\">Ordered</th class=\"col-md-1\"><th class=\"col-md-1\">Total Quantity</th><th class=\"col-md-1\">Total Value</th><th class=\"col-md-1\">Paid Amount</th>";
	   $a .= "</thead>";
	   $a .= "<tbody>";	 
	   $ordercount=0;$grand_total_qty=0;$grand_total_value=0;$grand_paid_amount=0;
	   foreach($array_data as $field_value){
		   $ordercount++;
		   $grand_total_qty += $field_value['total_quantity'];
		   $grand_total_value += $field_value['final_value'];
		   $grand_paid_amount += $field_value['payment_amount'];
           $res=$this->getAliasValue('source_master',$field_value['source_id']);
		   $res1=$this->getAliasValue('customers',$field_value['customer_id']);
		   $cust_id = $field_value['customer_id'];
		   $bank = "";
		   $virtual_ac = "";
		   if($cust_id){
			   $sql = "select b.name_bank,c.virtual_code from customers c left join bank_master b on c.bank_name = b.id where c.id={$cust_id}";
			   $customer_data = $this->first($sql);
			   if($customer_data){
					$bank = $customer_data->name_bank;
					$virtual_ac = $customer_data->virtual_code;
				}
			}
		   
		   $paymentid=$field_value['Payment_id'];
		   $paymentid = strlen($paymentid) > 20 ? substr($paymentid,0,20): $paymentid;
		   $source = "";
		   $customer="";
           if(isset($res['plant_name'])) $source=$res['plant_name'];
		   if(isset($res1['customer_name1'])) $customer=$res1['customer_name1'];
			$order_date=strtotime($field_value['order_date']);
		   	$order_date=date("d-M-y",$order_date);
		   
		   $a .= "<tr>".
						"<td class=\"col-md-1\">".$ordercount."</td>".
					   "<td class=\"col-md-1\">".$field_value['prefix'].$field_value['order_no']."</td>".
						"<td class=\"col-md-1\">".$order_date."</td>".	   
						"<td class=\"col-md-1\">".$field_value['total_quantity']."</td>".	   
						"<td class=\"col-md-1\">".$field_value['final_value']."</td>".
						"<td class=\"col-md-1\">".$field_value['payment_amount']."</td>".
		   		 "</tr>";
	   }
	$a .= "<tfoot>";   
	$a .= "<tr><td class=\"col-md-1\"><span><b>Total orders</b></span><br>$ordercount</td><td class=\"col-md-1\"></td><td class=\"col-md-1\"></td><td class=\"col-md-1\"><span><b>Total Quantity</b></span><br>$grand_total_qty</td><td class=\"col-md-1\"><span><b>Total Value</b></span><br>$grand_total_value</td><td class=\"col-md-1\"><span><b>Total Paid Amount</b></span><br>$grand_paid_amount</td></tr>";
	$a .=  "</tfoot>";  
	$a .= "</tbody>";
	   $a .= "</table>";
       return $a;
   }		
   function getCustomerMapQual($qual)
   {
       $customers="";$db_model=new DbModel();
       $sql_customer_map="select customer_id from user_customer_map where user_id=".$_SESSION['SESS_empid'];
       
       $res=$db_model->allArray($sql_customer_map);
       if($res)
       {
           foreach($res as $customer)
            {
               $customers.=$customer['customer_id'].",";
           }
               $customers= trim($customers,",");
               $qual=" 1=1 and customer_id in (".$customers.")";
       }
       else
       {
        //    echo "<h3>You don't have any customers mapped to your profile</h3>";
           $qual=" 1=2";
       }
       
       return $qual;    
   } 

   function getGstType($izt_id,$state_id){
	   $izt_details = $this->first("select count(*) as tot from izt_details where id = {$izt_id} and state = {$state_id}");
	   $type = 'center';
	   if($izt_details->tot >0){
			$type = 'state';
	   }else{
			$type = 'center';
	   }
	   return $type;
   }

   function getTax($invoice_date = NULL){
	if(!$invoice_date){
		$now = strtotime(date("Y-m-d 00:00"));
		$to = strtotime(date("Y-m-d 23:59"));
	}else{
		$now = strtotime($invoice_date);
		$to = strtotime($invoice_date)+(23*60*60);
	}
	$tax = $this->first("select percentage from service_tax where valid_from < $to and valid_to > $now");
	$percent = 0;
	if($tax && !empty($tax)){
		$percent = $tax->percentage;
	}
	return $percent;
   }
   function getTDSTax($invoice_date = NULL){
	if(!$invoice_date){
		$now = strtotime(date("Y-m-d 00:00"));
		$to = strtotime(date("Y-m-d 23:59"));
	}else{
		$now = strtotime($invoice_date);
		$to = strtotime($invoice_date)+(23*60*60);
	}
	$tax = $this->first("select percent from tds where tax_name = 'TDS' and valid_from < $to and valid_to > $now");
	$percent = 0;
	if($tax && !empty($tax)){
		$percent = $tax->percent;
	}
	return $percent;
   }
   function tcsCheck($customerid){
		$res = true;
		$time= strtotime(date('Y-m-d'));
		$check_customer = $this->first("select tcs_expemtion_type from customers where id = {$customerid} ");
		if(!$check_customer || empty($check_customer)){
			$res = false;
		}
		else{
			$exemption_type = $check_customer->tcs_expemtion_type;
			if($exemption_type == 0){
				$standard_rate_res= $this->first("select rate_amount from tcs_standard_master  where '$time' > valid_from and '$time' <= valid_to ");
				if(!$standard_rate_res || empty($standard_rate_res)){
					$res = false;
				}
			}elseif($exemption_type == 1){
				$standard_rate_res= $this->first("select amount_rate from tcs_exemption_master where customer_id='$customerid' and ('$time' >= valid_from and '$time' <= valid_to) ");
				if(!$standard_rate_res || empty($standard_rate_res)){
					$res = false;
				}
			}
		}
		return $res;
		

   }

   function tcspercent($customerid,$amount=0)
   {
	   $tcs_data=array();
	   $db_model = new DbModel();
	   $time=time();
	   $tcs_value=0;$exemption_type=""; 
	   $q="select tcs_expemtion_type from customers where id='$customerid' ";
	   $res=$db_model->allArray($q);
	   $tcs_data['tcs_exeption'] = false;
	   if(!empty($res))
	   {
		   // while($row=mysql_fetch_assoc($res))
		   foreach($res as $row)
		   {
			   $exemption_type=$row['tcs_expemtion_type'];
			  
		   }
	   }	
	   //standard	
	   if($exemption_type==0)
	   {
		   $standard_rate_res=$db_model->first("select rate_amount from tcs_standard_master  where '$time' > valid_from and '$time' <= valid_to ");
		   $standard_rate = $standard_rate_res->rate_amount;
		   if(is_float($standard_rate))
		   {
			   $standard_rate=round($standard_rate);
			   $tcs_value=($amount/100)*$standard_rate;
		   }
		   else
		   {
			   $tcs_value=($amount/100)*$standard_rate;
		   }
			$tcs_data['tcspercent']=$standard_rate;
	   }
	   //Exemption
	   else if($exemption_type==1)
	   {
		   
		   $q="select amount_rate from tcs_exemption_master where customer_id='$customerid' and ('$time' >= valid_from and '$time' <= valid_to) ";
		   $ex_rate_res=$db_model->first($q);
		   if($ex_rate_res) $ex_rate=$ex_rate_res->amount_rate;
		   else $ex_rate = 0;
			   if(is_float($ex_rate))
			   {
				   $ex_rate=round($ex_rate);
				   $tcs_value=($amount/100)*$ex_rate;
			   }
			   else
			   {
				   $tcs_value=($amount/100)*$ex_rate;
			   }
			   $tcs_data['tcspercent']=$ex_rate;
	   } 
	   else 
	   {
		   //Full exemption
		   if($exemption_type==2)
		   {
			   $tcs_value=0;
			   $tcs_data['tcspercent']=0;
		   }	
	   }
	   return $tcs_data;
   }
   public function accesFilter($tbl,$access){
	   $filtersql = "select matching_filter from access where groupname in ($access) and page_name = '{$tbl}'";
	   $data = $this->first($filtersql);
	   if($data && !empty($data)){
			return $data->matching_filter;
	   }else{
		   return false;
	   }
   }
   public function valueQual($tbl,$access){
	//    query for fetch row from valuelist join on access table based on page and access
		$access = $this->parseFilter($access);
	   $value_list_sql = "select v.*,a.matching_field,a.matching_filter from valuelist v join access a on v.id = a.valuelistid where a.page_name = '{$tbl}' and a.groupname in ($access) ";
	   $value_lists = $this->all($value_list_sql);
	   $qual = array();
	   $valeQual = "";
	   if($value_lists && !empty($value_lists)){
		   foreach($value_lists as $value_list){
			   $value = $value_list->value;
			   $source = $value_list->source;
			   $matching_field = $value_list->matching_field;
			   $matching_filter = $value_list->matching_filter ? " and (".$this->parseFilter($value_list->matching_filter).") ":"";
			   $filter = $this->parseFilter($value_list->filter);
			   // from source based on filter get list
			   $query = "select $value from {$source} where $filter";
			   $lists = $this->all($query);
			   if($lists && !empty($lists)){
				   $all_list = array();
				   foreach($lists as $_list){
					   array_push($all_list,$_list->$value);
				   }
				   $list = implode(',',$all_list);

				   array_push($qual," $matching_field in ($list)  $matching_filter");
				}else{
					array_push($qual," $matching_field in (0)  $matching_filter");
			   }
			}
		}
		if(!empty($qual)) $valeQual = implode("or",$qual);
		if(trim($valeQual)) $valeQual = " ($valeQual) ";
		return $valeQual;
   }

   public function parseFilter($filter){
		//    replace  SESSIONUSERID with session SESS_USERID
		if(!isset($_SESSION['SESS_id'])) $_SESSION['SESS_id'] = 0;
		if(!isset($_SESSION['SESS_access'])) $_SESSION['SESS_access'] = 0;
		if(!isset($_SESSION['SESS_empid'])) $_SESSION['SESS_empid'] = 0;

	   $filter = str_replace('SESSIONID',$_SESSION['SESS_id'],$filter);
	   //    replace SESSIONACCESS
		//    :TODO regexp
	   $filter = str_replace('SESSIONACCESS',$_SESSION['SESS_access'],$filter);

	   $filter=str_replace('SESSIONEMPID',$_SESSION['SESS_empid'],$filter);

		$filter=str_replace('TODAY',time(),$filter);

		$filter=str_replace('BEFOREAWEEK',strtotime("-1 week"),$filter);

		$filter=str_replace('CURRENT_ID',$this->getCurrentId(),$filter);

		$filter=str_replace('ORDERCUST_ID',$this->getOrderCustomerId(),$filter);

		$filter=str_replace('{{11-02-2019}}',12,$filter);
		//   replace IMATCHES
	   $filter = str_replace('IMATCHES','like',$filter);
	//    
	   $filter = str_replace('CUSTOMERID',$this->getCustomerFromUser($_SESSION['SESS_id']),$filter);
		//    replace all matched | to ,
		$filter = preg_replace('/\|/', ",", $filter);
	// echo $filter;
		return $filter;
   }

   function getCurrentId(){
	   $id =0;
	   if($_POST && isset($_POST['order_no0']) && isset($_POST['tblname'])){
		$v=$this->getValueByField($_POST['tblname'],'order_no',$_POST['order_no0']);
		$id = $v->id;
	   }else{
		if($_POST && isset($_POST['sel'])){
			$sel = $_POST['sel'];
			$ids = explode('=',$sel);
			if(count($ids) == 2)  $id = $ids[1];
		}
	   }
	   return $id;
   }

   function getOrderCustomerId(){
	   $id = $this->getCurrentId();
	   $cust_id = 0;
	   if($id){
			$sql = $this->first("select customer_id from orders where id = {$id}");
			if($sql){
				$cust_id = $sql->customer_id ? $sql->customer_id : 0;
			}  
		}
	   return $cust_id;
   }

   function getCustomerFromUser($id){

	$q="select * from user_customer_map where user_id='$id'";
    $res=$this->firstArray($q);
	$customerid=isset($res['customer_id'])?$res['customer_id']:"";
   return $customerid;
   }

   function getSelectBoxData($type,$fieldid){
	   $result_opt = array();
	   if($type == 'list'){
		   $sql_filter="select source,filter,id,value,alias from valuelist where id in (select optid from field_option where fieldid=".$fieldid.")";
		   $result_filter=$this->firstArray($sql_filter);
		   if($result_filter && !empty($result_filter)){
			   $vsource= $result_filter['source'];
			   $vfilter= $result_filter['filter'];
			   $vid= $result_filter['id'];
			   $vvalue= $result_filter['value'];
			   $valias= $result_filter['alias'];
			   $vfilter= $vfilter==null?" where 1=2 ":" where ".$this->parseFilter($vfilter);
			   $sql_opt="select ".$vvalue." as value,".$valias." as alias from ".$vsource.$vfilter;
			   $result_opt=$this->allArray($sql_opt);
			}
	   }else if($type == 'option'){
				$sql_opt="select value,alias from field_option where fieldid=".$fieldid;
				$result_opt=$this->allArray($sql_opt);
				// $result_opt= $sql_opt;
	   }

	   return $result_opt;
	}
   function getCustomerName($customerid)
   {
	$q1="select * from customers where id='$customerid'";
	$res1=$this->firstArray($q1);
	$customer_name=isset($res1['customer_name1'])?$res1['customer_name1']:"";
	return $customer_name;
   }
   function getCustomerHeaderRow($id,$tbl)
   {
		$a="";
		$customer=$this->getCustomerName($this->getCustomerFromUser($id));
		$a.="<div class=\"col-md-12\">";
		if($_SESSION['SESS_access']==4)
		{
			$a.="<div class=\"col-md-3\"><span id=\"header_customer\" ><h4><font size=\"3\" color=\"blue\"></font></h4></span></div>";
			// $a.="<div class=\"col-md-3\"><span id=\"header_customer\" ><h4><font size=\"3\" color=\"blue\">$customer</font></h4></span></div>";
		}
		else
		{
			$a.="<div class=\"col-md-3\"><span id=\"header_customer\"></span></div>";
		}
		// $a.="<a href=\"home.php?page=".$tbl."\">Home</a> &nbsp;";
		// echo '<b><font size="4">'.$utils->getTableAlias($tbl).'</font></b>';
		$a.= "<div class=\"pull-right\" id=\"paginationdiv\" > </div>";
		$a.="<div class=\"template-gap pull-right\">&nbsp;</div>";
		$a.= "<div class=\"pull-right\" id=\"filterdiv\" > </div>&nbsp;";
		$a.="<div class=\"template-gap pull-right\">&nbsp;</div>";
		$a.= "<div class=\"pull-right\" id=\"excelimportdiv\" > </div>&nbsp;";
		$a.="<div class=\"template-gap pull-right\">&nbsp;</div>";
		$a.= "<div class=\"pull-right\" id=\"newbtndiv\" ></div>&nbsp;";
		$a.= "</div>"; 
		$a.= "<div class=\"clearfix template-gap-height\">&nbsp;</div>";
		return $a;
   }

   function sendOTP($to,$msg){
		$url = $this->SMS_URL_OTP;
        $enterpriseid = $this->SMS_ENTERPRISE;
        $subEnterpriseid = $this->SMS_SUB_ENTERPRISE;
        $pusheid = $this->SMS_PUSH_ID;
        $pushepwd = $this->SMS_PUSH_PWD;
        $sender = $this->SMS_SENDER;
        $msg = urlencode($msg);
        $query = "enterpriseid={$enterpriseid}&subEnterpriseid={$subEnterpriseid}&pusheid={$pusheid}&pushepwd={$pushepwd}&msisdn={$to}&msgtext={$msg}&sender={$sender}";
        $url = $url.'?'.$query;
        $ch = curl_init();
        curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        ));
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
			// echo 'Error:' . curl_error($ch);
			return 'error';
        }
        curl_close ($ch);
		return $result;
	}

	function sendSMS($to,$msg){
		$msg_id = $this->MSG_ID;
        $auth_key = $this->MSG_AUTH_KEY;
        $msg = urlencode($msg);
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "http://api.msg91.com/api/sendhttp.php?sender=$msg_id&route=4&mobiles=$to&authkey=$auth_key&country=91&message=$msg",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
        // echo "cURL Error #:" . $err;
        } else {
        // echo $response;
        }
	}

	function insertOrderDetails($tbl,$id){
		$sql = "select id,order_no from $tbl where id = {$id}";
		$order=$this->first($sql);
		$id = $order->id;
		$order_no = $order->order_no;
		$update_sql = "update order_details set order_id ={$id} where orderno = '{$order_no}'";
		$this->executeQuery($update_sql);
	}
	function getTableId($tbl){
		$name = "";
		$sql = "select tblid from config where name='{$tbl}'";
		$query = $this->first($sql);
		if($query){
			$tblid = $query->tblid;
		}
		return $tblid;
		
	}

	function getTableStatus($tblid,$status){
		$sql = "select alias from field_option where tblid = {$tblid}
				 and fieldid in(select fieldid from field where name ='status' and tblid = {$tblid}) and value = '{$status}'";
		$status = $this->first($sql);
		if($status){
			return $status->alias;
		}
		else
		return '';
	}
	function getAllStatus($tbl,$field=false){
		if(!$field) $field = 'status';
		$sql = "select value,alias from field_option where fieldid in(select fieldid from field where tblid in (select tblid from config where name='{$tbl}') and name='{$field}')";
		$status = $this->all($sql);

		if($status && !empty($status)){
			return $status;
		}
		else
		return array();
	}
	function getOrderStatus($sel,$tbl)
	{
		$tblid=$this->getTableId($tbl);
		$ar=explode("=",$sel);
		$orderid=$ar[1]; 
		$sql="select * from orders where id={$orderid}";
		$res=$this->firstArray($sql);
		$status=$res['status'];
		$statusalias=$this->getTableStatus($tblid,$status);
		return $statusalias;
	}

	function getOrderStock($order_id,$sku,$today=null){
		$in_stock = 0;
		$from = strtotime(date('Y-m-d 00:00'));
		$to = strtotime(date('Y-m-d 23:59'));
		$order_qual = "";
		if($order_id){
			$order_qual = " and o.order_no not in ('{$order_id}')";
		}
			// echo "select in_stock from inventory where sku_master_id in (select id from sku_master where sku_code = '{$sku}') and status = 1 and inventory_date > {$from} and inventory_date < {$to}";
		$invetory_sql = $this->first("select in_stock from inventory where sku_master_id in (select id from sku_master where sku_code = '{$sku}') and status = 1 and inventory_date > {$from} and inventory_date < {$to}");
		$confirmed_qty = $this->first("select sum(s.qty) as confirm_qty from orders o join order_details s on o.order_no = s.orderno where o.status = '4' and s.sku = '{$sku}' {$order_qual}");
		$dispatched_qty = $this->first("select sum(s.qty) as dispatch_qty from orders o join order_details s on o.order_no = s.orderno where o.status = '5' and s.sku = '{$sku}' and o.dispatch_date between {$from} and {$to} {$order_qual}");
		$arrival_qty = $this->first("select sum(s.qty) as arrival_qty from orders o join order_details s on o.order_no = s.orderno where o.status = '10' and s.sku = '{$sku}' and o.truck_arrival_date between {$from} and {$to} {$order_qual}");
		$unload_qty = $this->first("select sum(s.qty) as unload_qty from orders o join order_details s on o.order_no = s.orderno where o.status = '11' and s.sku = '{$sku}' and o.unload_date between {$from} and {$to} {$order_qual}");
		$remain = $confirmed_qty->confirm_qty + $dispatched_qty->dispatch_qty + $arrival_qty->arrival_qty + $unload_qty->unload_qty ;
		if($invetory_sql && !empty($invetory_sql)) $in_stock = $invetory_sql->in_stock;
		$in_stock = $in_stock - abs($remain);
		return $in_stock;
		
	}

	function getOptionAlias($fieldid,$value){
		// $this->write_log("debug","select alias from field_option where fieldid = {$fieldid} and value = '{$value}'");
		$option = $this->first("select alias from field_option where fieldid = {$fieldid} and value = '{$value}'");
		if($option && !empty($option)){
			$value = $option->alias;
		}
		return $value;
	}

	function getOptionValueAlias($fieldid){
		$options = $this->allArray("select value,alias from field_option where fieldid = '{$fieldid}'");
		$res= array();
		if($options && !empty($options)){
			$res = $options; 
		}
		return $res;
	}

	function getAlias($tbl,$field,$value){
		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		$start_t = $time;
		// $option = $this->first("select type,fieldid from field where tblid in (1179) and name = 'customer_name'");
		$option = $this->first("select type,fieldid from field where tblid in (select tblid from config where name = '{$tbl}') and name = '{$field}'");
		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		$finish = $time;
		$total_time = round(($finish - $start_t), 4);
		// echo 'Page generated in '.$total_time.' seconds.<br>';

		// return 'test';
		// echo "select type,fieldid from field where tblid in (select tblid from config where name = '{$tbl}') and name = '{$field}';<br>";
		// echo "select type,fieldid from field where tblid in (select tblid from config where name = '{$tbl}') and name = '{$field}')";
		// print_r($option);
		$alias = 'trst';
		if($option && !empty($option)){

			switch($option->type){
				case 'list':
				$alias = $this->getFirstListAlias($option->fieldid,$value);
				break;
				case 'option':
				case 'radio':
				$alias = $this->getOptionAlias($option->fieldid,$value);
				break;
				case 'idate':
				$alias = $value ? $this->getmydate($value) : $value;
				break;
				default:
				$alias = $value;
			}
		}else{
			$alias = $value;
		}
		return $alias;
	}

	function getOptionAliasTblField($tbl,$field,$value){
		// $this->write_log("debug","select alias from field_option where fieldid = {$fieldid} and value = '{$value}'");
		$option = $this->first("select alias from field_option where fieldid in(select fieldid from field where tblid in (select tblid from config where name = '{$tbl}') and name = '{$field}') and value = '{$value}'");
		if($option && !empty($option)){
			$value = $option->alias;
		}
		return $value;
	}

	function getOrderDetailsBySKU($sku,$order_no){
		$sql = "select qty from order_details where orderno = '{$order_no}' and sku = '{$sku}'";
		$sql_data = $this->first($sql);
		$qty = 0;
		if($sql_data) $qty = $sql_data->qty;
		else $qty = 0;
		return $qty; 
	}
	
	function getFirstListAlias($fieldid,$value){
		$alias = "";
			$sql_filter="select source,filter,id,value,alias from valuelist where id in (select optid from field_option where fieldid=".$fieldid.")";
		   	$result_filter=$this->first($sql_filter);
		   if($result_filter && !empty($result_filter)){
			   $vsource= $result_filter->source;
			   $vfilter= $result_filter->filter;
			   $vid= $result_filter->id;
			   $vvalue= $result_filter->value;
			   $valias= $result_filter->alias;
			//    $vfilter= $vfilter==null?" where 1=2 ":" where ".$this->parseFilter($vfilter);
			   $vfilter =" where {$vvalue} = '{$value}'"; 
			   $sql_opt = "select ".$vvalue." as value,".$valias." as alias from ".$vsource.$vfilter;
			//    echo $sql_opt;
			   $result_opt=$this->first($sql_opt);
			//    print_r($result_opt);
			   if($result_opt){
					if(isset($result_opt->alias)){
						$alias = $result_opt->alias;
					}
			   } 
		   }
		//    echo $alias;
		return $alias;
	}

	public function getTableObject($tbl,$id){
		$obj = $this->first("select * from {$tbl} where id = '{$id}'");
		if($obj && !empty($obj)){
			return $obj;
		}else{
			$fields = $this->all("select name from field where tblid in (select tblid from config where name = '{$tbl}')");
			$arr = array();
			foreach($fields as $field){
				$arr[$field->name] = '';
			}
			return (object) $arr;
		}
	}
	// thisw function will check whether user able to update or create particular filed values exists or not
	public function checkFieldAccess($qual,$data) {
		$res = true;
		if($qual){
			$operation = explode("in",$qual);
			if(count($operation) == 2){
				$field = trim($operation[0]);
				$availability = trim($operation[1]);
				$field = str_replace("(","",$field);
				$field = trim($field);
				$availability =  str_replace("(","",$availability);
				$availability =  str_replace(")","",$availability);
				if($availability){
					$availability = trim($availability);
					$availability = explode(",",$availability);
					// check particular field having access values or not
					// foreach($data as $key=>$value){

					// }
					if($data['icnt']){
						for($i=0; $i<$data['icnt']; $i++){
							if(isset($data[$field.$i])){
								if(!in_array($data[$field.$i],$availability)){
									$res = false;
									// echo $data[$field.$i];
								}else{
									// echo $data[$field.$i];
								}
							}
						}
					}
				}
			}
		}
		return $res;
	}

	public function inrCurrency($num){
		$num = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
		if($num) return $num;
		else return 0;
	}

	public function getCurrentExchange($sel){
		$res = 1;
		if(trim($sel)){
			$today = strtotime(date('Y-m-d 00:00:00'));
			$sql = "select b.exchange_value from billing_currency b 
						join currency_codes c on b.currency_code=c.code 
						where c.id in (select crncy_code from invoice where {$sel}) and b.b_date <= {$today}
						order by b.b_date desc limit 1
					";
			$data = $this->first($sql);
			if($data && !empty($data)){
				$res = $data->exchange_value;
			}
		}
		return $res;
	}
	public function getExchangeValue($currency_id,$from=NULL,$to=NULL){
		if($from && $to){
			$from = strtotime($from);
			$to = strtotime($to);
		}else{
			$from = strtotime(date('Y-m-d 00:00:00'));
			$to = strtotime(date('Y-m-d 23:59:59'));
		}
		$sql = "select * from billing_currency where currency_code in (select code from currency_codes where id = {$currency_id}) and b_date between '{$from}' and '{$to}'";
		$exchange = $this->first($sql);

		if($exchange && !empty($exchange)){
			return $exchange->exchange_value;
		}
		else{
			$sql = "select code from currency_codes where id = {$currency_id}";
			$exchange = $this->first($sql);
			if($exchange && !empty($exchange)){
				if(strtolower($exchange->code) == 'inr'){
					return 1;
				}
			}
			return false;
		}
	}

	public function inrWord($number) {
		$no = round($number);
		$decimal = round($number - ($no = floor($number)), 2) * 100;    
		$digits_length = strlen($no);    
		$i = 0;
		$str = array();
		$words = array(
			0 => '',
			1 => 'One',
			2 => 'Two',
			3 => 'Three',
			4 => 'Four',
			5 => 'Five',
			6 => 'Six',
			7 => 'Seven',
			8 => 'Eight',
			9 => 'Nine',
			10 => 'Ten',
			11 => 'Eleven',
			12 => 'Twelve',
			13 => 'Thirteen',
			14 => 'Fourteen',
			15 => 'Fifteen',
			16 => 'Sixteen',
			17 => 'Seventeen',
			18 => 'Eighteen',
			19 => 'Nineteen',
			20 => 'Twenty',
			30 => 'Thirty',
			40 => 'Forty',
			50 => 'Fifty',
			60 => 'Sixty',
			70 => 'Seventy',
			80 => 'Eighty',
			90 => 'Ninety');
		$digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
		while ($i < $digits_length) {
			$divider = ($i == 2) ? 10 : 100;
			$number = floor($no % $divider);
			$no = floor($no / $divider);
			$i += $divider == 10 ? 1 : 2;
			if ($number) {
				$plural = (($counter = count($str)) && $number > 9) ? 's' : null;            
				$str [] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural;
			} else {
				$str [] = null;
			}  
		}
		
		$Rupees = implode(' ', array_reverse($str));
		$paise = ($decimal) ? "And Paise " . ($words[$decimal - $decimal%10]) ." " .($words[$decimal%10])  : '';
		return ($Rupees ? $Rupees : '') . $paise . "Only";
	}

	function getListField($row,$cnt,$j,$fielddata=NULL,$related){
		$ui = new UiConstant();
		$x=$ui->tg_sel_control.$row['name'].$cnt.$ui->tg_ip_name.$related.$row['name'].$cnt.$ui->tg_cl;
		$sql_filter="select source,filter,id,value,alias from valuelist where id in (select optid from field_option where tblid=".$row['tblid']." and fieldid=".$row['fieldid'].")";
		$result_filter=$this->firstArray($sql_filter);
		if($result_filter && !empty($result_filter)){
			$vsource= $result_filter['source'];
			$vfilter= $result_filter['filter'];
			$vid= $result_filter['id'];
			$vvalue= $result_filter['value'];
			$valias= $result_filter['alias'];
			$vfilter=$vfilter==null?" where 1=2 ":" where ".$utils->parseFilter($vfilter);
			$sql_opt="select ".$vvalue." as value,".$valias." as alias from ".$vsource.$vfilter;
			//Completion of change
			$result_opt=$this->allArray($sql_opt);
			$opt[$j]="";
			$alias='';
			if($result_opt)
			{
				foreach($result_opt as $row_opt)
				{
					$opt[$j]=$opt[$j].$ui->tg_opt.$row_opt['value'].$ui->tg_cl.$row_opt['alias'].$ui->tg_opt_cl;
					if($row_opt['value']==$fielddata) 
					{$alias=$row_opt['alias'];}
				}
			}
			//below handles code if no value is set and also instead of value shows alias
			$alias=(isset($alias)&&($alias!=null))?$alias:"--NONE--";
			$x=$x.$ui->tg_opt.$fielddata.$ui->tg_cl.$alias.$ui->tg_opt_cl;
			$x=$x.$opt[$j].$ui->tg_sel_cl;
		}
    	return $x;
	}
	function getRelDataIds($parent,$child,$field='childrecid',$parent_id=false){
		$rel_id = $this->getRelId($parent,$child);
		if($parent_id){
			$rel_sql = "select {$field} from reldata where relid = '{$rel_id}' and parentrecid = '{$parent_id}'";
		}else{
			$rel_sql = "select {$field} from reldata where relid = '{$rel_id}'";
		}
		$res = $this->allArray($rel_sql);
		if($res && !empty($res)){
			$arr = array_map(function ($ar) use($field) {return $ar[$field];}, $res);
		}else{
			$arr = array();
		}
		return $arr;

	}
	function getTasksRecords($parent,$child,$parent_id,$field='childrecid',$status,$main_status){
		$rel_id = $this->getRelId($parent,$child);
		$rel_sql = "select {$field} from reldata where relid = '{$rel_id}' and parentrecid = {$parent_id}";

		$res = $this->allArray($rel_sql);
		if($res && !empty($res)){
			$arr = array_map(function ($ar) use($field) {return $ar[$field];}, $res);
			$arr = implode(',',$arr);
			$sql = "select count(*) as total from $child where id in ($arr) and status in ($status)";
			$limited = "select count(*) as limits from $child where id in ($arr) and status in ($main_status)";
			// echo $limited;
			$total = $this->first($sql);
			$limited = $this->first($limited);
			$tot = $total->total;
			$limit = $limited->limits;
			$res = $limit.' / '.$tot;
		}else{
			$res = false;
		}
		return $res;

	}
	function getRelId($parent,$child){
		$parent_id = $this->getTableId($parent);
		$child_id = $this->getTableId($child);
		$sql = $this->first("select id from formrel where sourceid = {$parent_id} and childid = {$child_id}");
		if($sql){
			return $sql->id;
		}else{
			return 1;
		}
	}

	function dataExchange($records,$fields,$exchange_value){
		foreach($fields as $key => $field){
			foreach($records[$key] as $_keys=>$_values){
				foreach($_values as $_key=>$_value){
					if(isset($fields[$key]) && in_array($_key,$fields[$key])){
						$records[$key][$_keys]->$_key = $_value*$exchange_value;
					}
				}
			}
		}
	return $records;
	}

	function newDataExchange($datas,$replaceing){
		$new_datas = array();
		foreach($datas as $data){
			$new_data = $data;
			foreach($replaceing as $key=>$replace){
				$new_data->$key = $data->$replace;
			}
			array_push($new_datas,$new_data);
		}
		return $new_datas;
	}

	function getLUT(){
		$lut_data = $this->first("select * from lut_ar");
		$lut = "";
		if($lut_data && !empty($lut_data)){
			$lut = $lut_data->lut_arn_no;
		}
		return $lut;

	}
	/**
	 * var $tbl
	 * var $id current record Id
	 * var $msg msg content  
	 */
	function compileTemplate($tbl,$id,$msg){
		
	}
	function truncate($str, $chars, $end = '...') {
		if (strlen($str) <= $chars) return $str;
		$new = substr($str, 0, $chars + 1);
		return substr($new, 0, strrpos($new, ' ')) . $end;
	}

	function customerTransaction($customer_id,$date){
		if(!$date) $from = strtotime(date('Y-m-1 00:00:00')).' and '. strtotime(date('Y-m-d 23:59:59'));
		$usd_total = 0;
		$inr_total = 0;
		$charge = 0;
		$igst = 0;
		$cgst = 0;
		$sgst = 0;
		$tds = 0;
		$paid = 0;
		$due_amount = 0;
		$datas = $this->all("select paid,id,tax_amount,total,sub_total,exchng_val,gst_percent,inv_type,gst_amount,due_amount,export_total,export_subtotal,gst_type from invoice where customer_id = {$customer_id} and invoice_date between {$date}");
		foreach($datas as $data){
			if($data->inv_type == 'Export'){
				$usd_total = ($data->total) + $usd_total;
			}
			if($data->inv_type == 'Tax' || $data->inv_type == 'SEZ'){
				$charge = ($data->total) + $charge;
			}
			// iner total
			$inr_total = ($data->total * $data->exchng_val) + $inr_total;

			if($data->gst_type == 'center'){
				$igst = $igst + (($data->sub_total*$data->gst_percent)/100);
			}
			if($data->gst_type == 'state'){
				$cgst = $cgst + ((($data->sub_total*$data->gst_percent)/100)/2);
				$sgst = $sgst + ((($data->sub_total*$data->gst_percent)/100)/2);
			}
			$tds = $tds + $data->tax_amount;
			$paid = $paid + $data->paid;

			$due_amount = $due_amount + $data->due_amount;
		}
		// pending amount 
		$pending_datas = $this->all("select total,sub_total,exchng_val from invoice where status between 2 and 5 and customer_id = {$customer_id} and invoice_date between {$date}");
		foreach($pending_datas as $pending_data){
			$due_amount = $due_amount + $pending_data->total;
		}
		$alldata = array(
			'usd_total' => $usd_total,
			'inr_total' => $inr_total,
			'charge' => $charge,
			'igst' => $igst,
			'cgst' => $cgst,
			'sgst' => $sgst,
			'tds' => $tds,
			'paid' => $paid,
			'due_amount' => $due_amount
		);

		return $alldata;
	}
	
	function totalCount($tbl,$qual){
		$data_sql="select count(*) aggregate from ".$tbl;
		if(isset($qual))
		{
			$_qual = $qual;
			if(!trim($qual)) $qual = ' 1=1';
			$qual=" where ".$qual;
			$data_sql=$data_sql.$qual;
		}
		$query = $this->first($data_sql);
		$count = $query->aggregate;
		return $count;
	}

	function updateCustomerAccount($value,$sel){
		$sel = explode('=',$sel);
		$id = isset($sel[1]) ? trim($sel[1]) : false;
		if($id){
			$sql_clear = "update account_name set customer_name = NULL where customer_name = '{$id}'";
			$this->executeQuery($sql_clear);
			$sql  = "update account_name set customer_name = '{$id}' where id = '{$value}'";
			$this->executeQuery($sql);
		}
	}

	function getMenu($pages,$current_page){
		$pages_list = "'".implode("','",$pages)."'"; 
		$lists = "";
		$menus = $this->all("
				select m.main_menu,c.name as tbl, c.alias from menu m 
					join config c on c.tblid = m.page 
					where page in (select tblid from config where name in ($pages_list))");
		if(count($menus)){
			$final_menu = array();
			$main_menu_active = '';
			foreach($menus as $_pages){
				$main_menu = $this->getAlias('menu','main_menu',$_pages->main_menu);
				if(!isset($final_menu[$main_menu])) $final_menu[$main_menu] = array();

				if($current_page == $_pages->tbl){
					$main_menu_active = $main_menu;
					$sel = "sel";
				} 
				else $sel ="";

				$final_menu[$main_menu][] = "<li class='".$sel."'><a class=\"menu_left\" href=\"home.php?page=".$_pages->tbl."\">"."<i class=\"fa fa-circle-o\"></i><span>".$_pages->alias."</span></a></li>";
			}
			
			foreach($final_menu as $key=>$ul){
				if($main_menu_active == $key) {
					$sel = "menu-open";
					$ul_display = "block";
				}else{
					$ul_display = "none";
					$sel ="";
				} 
				$lists .="<li class=\"treeview {$sel}\">
							<a href=\"#\">
								<i class=\"fa fa-pie-chart\"></i>
								<span class='treeview-title'>{$key}</span>
								<span class=\"pull-right-container\">
									<i class=\"fa fa-angle-left pull-right\"></i>
								</span>
							</a>
							<ul class=\"treeview-menu\" style=\"display: {$ul_display};\">";
				foreach($ul as $li){
					$lists .= $li;
				}
				$lists .= "</ul></li>";
			}
		}
		return $lists;
	}
	function array_columns($array, $columnKey, $indexKey = null)
    {
        $result = array();
        foreach ($array as $subArray) {
            if (is_null($indexKey) && array_key_exists($columnKey, $subArray)) {
                $result[] = is_object($subArray)?$subArray->$columnKey: $subArray[$columnKey];
            } elseif (array_key_exists($indexKey, $subArray)) {
                if (is_null($columnKey)) {
                    $index = is_object($subArray)?$subArray->$indexKey: $subArray[$indexKey];
                    $result[$index] = $subArray;
                } elseif (array_key_exists($columnKey, $subArray)) {
                    $index = is_object($subArray)?$subArray->$indexKey: $subArray[$indexKey];
                    $result[$index] = is_object($subArray)?$subArray->$columnKey: $subArray[$columnKey];
                }
            }
        }
        return $result;
	}
	function multiSearch($array, $pairs)
    {
        $found = array();
        foreach ($array as $aKey => $aVal) {
            $coincidences = 0;
            foreach ($pairs as $pKey => $pVal) {
                if (array_key_exists($pKey, $aVal) && $aVal[$pKey] == $pVal) {
                    $coincidences++;
                }
            }
            if ($coincidences == count($pairs)) {
                $found[$aKey] = $aVal;
            }
        }

        return $found;
	}   
	function listCheckbox($arr,$name='filter_check'){
		$filter= isset($_SESSION['filter'])?$_SESSION['filter']:false;
		$list = '<div class="checkbox">';
		$radio = array();
		if(!empty($arr)){
			$i=0;
			// already extra_qual available the added
			$qual = (isset($_POST['extra_qual'])) ? $_POST['extra_qual'] : '';
			$qual_arr = $this->qualToArray($qual);
			$qual_arr = array_values($qual_arr);
			if($qual) $qual = htmlspecialchars($qual,ENT_QUOTES);
			$list .= "<input type='hidden' name='extra_qual' value='{$qual}'>";
			foreach($arr as $key=>$value){
				if($filter){
					$checked = array_search($key,$filter) !== false ? 'checked':'';
				}else{
					$checked = 'checked';
				}
				if($checked) $radio[] = $key;
				$list .='<label>
							<input type="checkbox" class="checkbox-position" data-name="'.$name.'" name="radio_filter_'.$name.$i.'" value="'.$key.'" '.$checked.'> <strong>'.$value.'</strong>
						</label>';
				$i++;
			}
		}
		$list .='</div>';
		return array('check_boxes'=>$list,'radio'=>$radio);
	}

	function qualToArray($qual){
		$new_array = array();
		if($qual){
			$qual = str_replace("(",'',$qual);
			$qual = str_replace(")",'',$qual);
			$arr = explode("or",$qual);
			if(!empty($arr)){
				foreach($arr as $all_value){
					$all_value = explode('=', trim($all_value));
					
					$new_array[] = trim(str_replace(array("'"),'',$all_value[1]));
				}
			}
		}
		return $new_array;
	}
	function getAccountImg($ac_id){
		$account = $this->first("select acct_image from account_name where id = '{$ac_id}'");
		$img = "";
		if($account){
			$img = $account->acct_image;
			if(!$img){
				$img = 'images/nologo-text.jpg';
			}
		}else{
			$img = 'images/nologo-text.jpg';
		}

		return $img;
	}

	function time_elapsed_string($datetime, $full = false) {
		$now = new DateTime;
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);
	
		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;
	
		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}
	
		if (!$full) $string = array_slice($string, 0, 1);
		$final_string = $string ? implode(', ', $string) . '' : 'today';
		if(strpos($final_string,'hours')) $final_string = 'today';
		return $final_string;
	}

	function getIntials($values){
		$words = explode(' ', $values);
		$f = isset($words[0][0]) ? $words[0][0] : '';
		$s = isset($words[1][0]) ? $words[1][0] : '';
		$badge = $f.$s;
		return $badge;
	}

	function dateLabel($date,$labels){
		$cls = '';
		if($date){
			$t_date = date('Y-m-d 00:00:00',strtotime($date));
			$c_time = date('Y-m-d 00:00:00');
			$first_date = new DateTime($c_time);
			$second_date = new DateTime($t_date);
			$difference = $first_date->diff($second_date);
			$days = $difference->format("%R%a");
			$days = intval($days);
			$cls = '';
			if($days < 0) $cls = 'label label-danger';
			else if ($days < 2) $cls= 'label label-info';
			else $cls = '';
			// foreach($labels as $key=>$label){
				// 	if($key < $days){
					// 		$cls = $label;
					// 	}else{
						// 		$cls = "";
						// 	}
						// }
		}else $date = '-';
		return '<span class="'.$cls.'">'.$date.'</span>';
		// return $date;
	}

	function getValueFromAlias($tbl,$field,$value){
		$sql  = "select id from {$tbl} where {$field} like '{$value}'";
		$data = $this->first($sql);
		if($data && !empty($data)){
			return $data->id;
		}else{
			return false;
		}
	}
}
?> 