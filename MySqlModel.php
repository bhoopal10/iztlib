<?php
class MySqlModel{

    public function __construct($host,$username,$password,$db_name){
        $con = mysql_connect($host,$username,$password) or die('Error at MysqlModel4'.mysql_error());
        mysql_select_db($db_name,$con) or die('Error at MysqlModel5'.mysql_error());
        $this->db = $con;
    }
    
    private function getData($sql,$single=false,$arr=true){
        $res = array();
        $query = mysql_query($sql);
        if($query && mysql_num_rows($query)){
            if($arr) $res = $this->fetchArray($query,$single);
            else $res = $this->fetchObject($query,$single);
        }
        return $res;
    }

    private function fetchObject($res,$single=false){
        $obj = array();
        if(!$single){
            while($row = mysql_fetch_object($res)){
                array_push($obj,$row);
            }
        }else{
            $obj = mysql_fetch_object($res);
        }
        return $obj;
    }

    private function fetchArray($res,$single=false){
        $obj = array();
        if(!$single){
            while($row = mysql_fetch_assoc($res)){
                array_push($obj,$row);
            }
        }else{
            $obj = mysql_fetch_assoc($res);
        }
        return $obj;
    }

    /**
     * TODO params $table, $qual
     * return object ('key'=>value,...);
     */
    public function first($sql){
        return $this->getData($sql,true,false);
    }

    public function firstArray($sql){
        return $this->getData($sql,true,true);
    }

    public function all($sql){
        return $this->getData($sql,false,false);
    }

    public function allArray($sql){
        return $this->getData($sql,false,true);
    }

    public function insertData($sql,$id=false){
        $query = mysql_query($sql) or die("DBMOdel 96<br>$sql".mysql_error());
        $id = true;
        if($id) $id = mysql_insert_id();
        return $id;
    }

    public function executeQuery($sql){
        // echo $sql;
        $query = mysql_query($sql) or die('DBMOdel 103'.mysql_error());
        return true;
    }

    public function checkTable($sql){
        if(mysql_query($sql)){
            return true;
        }else{
            return false;
        }
    }

    public function getCount($sql){
        $count = 0;
        $result_page=mysql_query($sql) or die('PaginateError:123'.mysql_error().$sql);
        $count=mysql_num_rows($result_page);
        return $count;
    }
    private function getPagelink($iteration){
        $x=1;
        $j=1;
        if(isset($_GET['num'])){$j=$_GET['num'];}
        $pagination = "<ul class=\"pagination\"><li><a href=\"index.php?".$this->page."=".$_GET[$this->page]."&num=1\">&laquo;</a></li>";
        for($i=1;$i<=$iteration;$i++)
        { 
            //  echo "j is $j";
			if($i == $j) $pagination = $pagination. "<li class=\"active\"><a ";
            else $pagination = $pagination. "<li><a ";
            $pagination = $pagination. " href=\"index.php?".$this->page."=".$_GET[$this->page]."&num=".$i."\"";
            $pagination = $pagination. ">".$i."</a></li>";
        }
        $x=$i-1;
        $pagination = $pagination. "<li><a href=\"index.php?".$this->page."=".$_GET[$this->page]."&num=".$x."\" >&raquo;</a></li></ul>";
        return $pagination;
        
    }
    public function getPaginateSql($sql,$rec_limit)
	{
        $pagination = "";
		$start=0;
		$total = $this->getCount($sql);

		if(isset($_GET['num']))
		{
            $start=(($_GET['num'])-1)*$rec_limit;
		}
		if(ceil($total/$rec_limit)>1)
        $pagination = $this->getPagelink(ceil($total/$rec_limit));
        $sql=$sql." limit ".$start.", ".$rec_limit;
		return array('sql'=>$sql,'pagination'=>$pagination);
    }
    public function getValueByField($tblname,$field,$fieldvalue)
    {
        $sql="select id from $tblname where $field='$fieldvalue'";
       
        return $this->first($sql);

    }

}