<?php
require_once('Utils.php');
require_once('UiConstant.php');
class SearchHistory{
    public $bl;
    function __construct($tbl){
        $this->tbl = $tbl;
    }
    function displayTails(){
        $tbl = $this->tbl;
        $utils = new Utils();
        $ui = new UiConstant();
        $sql = "select * from search_criteria where page = '{$tbl}' and status = 1";
        $datas = $utils->all($sql);
        $div = $ui->tg_div.$ui->tg_div_class.'scrollmenu'.$ui->tg_cl;
        $div    .='<nav id="menu-container" class="arrow ">';
        $div    .='<div id="btn-nav-previous" class="hidden-md hidden-lg">&lt;</div>
                    <div id="btn-nav-next" class="hidden-md hidden-lg">&gt;</div>';
        $div    .='<div class="menu-inner-box">';

        if($datas && !empty($datas)){
            foreach($datas as $data){
               $div .= "<a href='#' class='pils' data-qual='".htmlspecialchars($data->criteria,ENT_QUOTES)."'>".$data->name."</a>";
            }
            $div    .='</div>';
            $div    .='</nav>';
            $div .= $ui->tg_div_cl;
            return $div;
        }else{
            return '';
        }
        

    }
}