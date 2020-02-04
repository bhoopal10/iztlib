<?php 
require_once('Utils.php');
class SqlServModel {
    public $pdo = NULL;
    public function __construct($serverName,$username,$password,$db_name){
        try  
        {  
        $conn = new PDO( "sqlsrv:server=$serverName ; Database=$db_name", "$username", "$password");  
        $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );  
        }  
        catch(Exception $e)  
        {   
            echo "sqlsrv:server=$serverName ; Database=$db_name"." ". "$username"." ". "$password";
        die( print_r( $e->getMessage() ) );   
        } 
        $this->pdo = $conn;
    }
    private function getData($sql,$single=false,$arr=true){
        $res = array();
        if($arr) $res = $this->fetchArray($sql,$single);
        else $res = $this->fetchObject($sql,$single);
        return $res;
    }

    private function fetchObject($sql,$single=false){
        $obj = array();
        // echo $sql;
        $stmt = $this->pdo->query($sql);
        if(!$single) $obj = $stmt->fetchAll(PDO::FETCH_OBJ);
        else $obj = $stmt->fetchObject();
        return $obj;
    }

    private function fetchArray($res,$single=false){
        $obj = array();
        // echo $res;
        $stmt = $this->pdo->query($res);
        if(!$single) $obj = $stmt->fetchAll(PDO::FETCH_ASSOC);
        else $obj = $stmt->fetch(PDO::FETCH_ASSOC);
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
        // echo $sql;
        return $this->getData($sql,false,true);
    }

    public function insertData($sql,$id=false){
        $utils = new Utils();
        $sql = str_replace('`','',$sql);
        // echo $sql.'<br>';
        $stmt = $this->pdo->query($sql);
        // var_dump($stmt);
        // $id = $stmt->lastInsertId('id');
        if($id){
            $stmt = $this->pdo->query("SELECT @@IDENTITY");
            $id = $stmt->fetchColumn();
        }
        return $id;
    }

    public function executeQuery($sql){
        // echo $sql;exit;
        // echo '<pre>'.$sql.'</pre>';
        $stmt = $this->pdo->query($sql);
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
        $stmt=$this->pdo->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->execute();  
        $count=$stmt->rowCount();
        return  $count;
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