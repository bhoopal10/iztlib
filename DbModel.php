<?php
require_once('MysqlSchema.php');
require_once('SqlSchema.php');
require_once('MySqlModel.php');
require_once('SqlServModel.php');
class DbModel{
    protected $host="192.168.100.24";
    protected $username = "sujit";
    protected $password = "izt@321"; 
    protected $port = "3360";
    protected $db_name = "orders_manager";
    public $table = 'users';
    public $auth_field = 'username'; 
    public $driver = 'mysql';
    public $page = 'page';
    public $sql_schema = NULL;
    public $db=NULL;
    public $dir='';
    public $php_path = "'C:\Program Files\PHP\PHP7\php.exe'";
    public $MSG_ID="UBLIKE";
    public $MSG_AUTH_KEY="205679AIgLmkV4S5ab637fc";
    public $SMS_URL_OTP ="";
    public $SMS_ENTERPRISE ="";
    public $SMS_SUB_ENTERPRISE ="";
    public $SMS_PUSH_ID ="";
    public $SMS_PUSH_PWD ="";
    public $SMS_SENDER ="";

    public function __construct(){
        // echo __DIR__;
        $ini_array = parse_ini_file(__DIR__.'/../../conf.ini',true);
        // print_r($ini_array);
        $this->host = $ini_array['snf']['host']?$ini_array['snf']['host']:$this->host;
        $this->username = $ini_array['snf']['username']?$ini_array['snf']['username']:$this->username;
        $this->password = isset($ini_array['snf']['password'])?$ini_array['snf']['password']:$this->password;
        $this->db_name = $ini_array['snf']['db_name']?$ini_array['snf']['db_name']:$this->db_name;
        $this->driver = $ini_array['snf']['driver']?$ini_array['snf']['driver']:$this->driver;
        // $this->php_path = $ini_array['snf']['php_path']?$ini_array['snf']['php_path']:$this->php_path;
        // $this->MSG_ID = $ini_array['snf']['MSG_ID']?$ini_array['snf']['MSG_ID']:$this->MSG_ID;
        // $this->MSG_AUTH_KEY = $ini_array['snf']['MSG_AUTH_KEY']?$ini_array['snf']['MSG_AUTH_KEY']:$this->MSG_AUTH_KEY;
        // $this->SMS_URL_OTP = $ini_array['snf']['SMS_URL_OTP']?$ini_array['snf']['SMS_URL_OTP']:$this->SMS_URL_OTP;
        // $this->SMS_ENTERPRISE = $ini_array['snf']['SMS_ENTERPRISE']?$ini_array['snf']['SMS_ENTERPRISE']:$this->SMS_ENTERPRISE;
        // $this->SMS_SUB_ENTERPRISE = $ini_array['snf']['SMS_SUB_ENTERPRISE']?$ini_array['snf']['SMS_SUB_ENTERPRISE']:$this->SMS_SUB_ENTERPRISE;
        // $this->SMS_PUSH_ID = $ini_array['snf']['SMS_PUSH_ID']?$ini_array['snf']['SMS_PUSH_ID']:$this->SMS_PUSH_ID;
        // $this->SMS_PUSH_PWD = $ini_array['snf']['SMS_PUSH_PWD']?$ini_array['snf']['SMS_PUSH_PWD']:$this->SMS_PUSH_PWD;
        // $this->SMS_SENDER = $ini_array['snf']['SMS_SENDER']?$ini_array['snf']['SMS_SENDER']:$this->SMS_SENDER;

        if($this->driver == 'sqlserv')
        {
            $this->db = new SqlServModel($this->host,$this->username,$this->password,$this->db_name);
            $this->pdo = $this->db->pdo;
            $this->sql_schema = new SqlSchema();
            
        } 
        elseif($this->driver == 'mysql'){
            $this->db = new MySqlModel($this->host,$this->username,$this->password,$this->db_name);
            $this->sql_schema = new MysqlSchema();
        } 
    }

    private function getData($sql,$single=false,$arr=true){
        $res = array();
        // echo $sql;
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
    //     $db =  new MySqlModel($this->host,$this->username,$this->password,$this->db_name);
    //    return $db->first($sql);
       return $this->db->first($sql);
    }

    public function firstArray($sql){
        // $db =  new MySqlModel($this->host,$this->username,$this->password,$this->db_name);
        // return $db->firstArray($sql);
        return $this->db->firstArray($sql);
    }

    public function all($sql){
        return $this->db->all($sql);
    }

    public function allArray($sql){
        return $this->db->allArray($sql);
    }

    public function insertData($sql,$id=false){
        // echo $sql;
        return $this->db->insertData($sql,$id);
    }

    public function executeQuery($sql){
        return $this->db->executeQuery($sql);
    }

    public function checkTable($sql){
        if(mysql_query($sql)){
            return true;
        }else{
            return false;
        }
    }

    public function getCount($sql){
        return $this->db->getCount($sql);
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