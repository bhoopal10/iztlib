<?php
class csrf{
    public $precsrf; // previous csrf token
    public $csrf; // new csrf token
    function __construct(){
        // checking csrf token
        $this->precsrf = isset($_SESSION['csrf']) ? $_SESSION['csrf'] : '';
        $this->csrf = md5(uniqid(rand(), TRUE));
        $_SESSION['csrf'] = $this->csrf;
        if(isset($_POST)){
            if(isset($_POST['csrf_token']) && $_POST['csrf_token'] != $this->precsrf){
//                 header("location: ".$_SERVER['HTTP_REFERER']);
  //               exit;
            }
        }
    }
}
