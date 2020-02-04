<?php
 class UiConstant {
	public $tg_row="<div class=\"row\">";
	public $tg_col1="<div class=\"col-md-1\">";
	public $tg_col2="<div class=\"col-md-2\">";
	public $tg_span="<div class=\"col-md-6\">";
	public $tg_colspace="<div class=\"col-md-6\"></div>";
	public $tg_col="<div class=\"col-md-6\">";
	public $tg_nrow="<div class=\"row clearfix\">";
	public $tg_ncol="<div class=\"col-md-4 column\">";
	public $tg_top = "<div class=\"table-responsive\"><table class=\"table table-striped table-bordered\" width=auto border=1 cellpadding=2 cellspacing=2>";
	public $tg_hdr = "<th>";
	public $tg_hdr_cl = "</th>";
	public $tg_ro = "<tr>";
	public $tg_ro_cl = "</tr>";
	public $tg_td = "<td>";
	public $tg_td_cl = "</td>";
	public $tg_top_cl = "</table></div>";
	public $tg_ip = "<input";
	public $tg_ip_type = " type=\"";
	public $tg_ip_name = "\" name=\"";
	public $tg_ip_value = "\" value=\"";
	public $tg_ip_size = "\" maxlength=\"";
	public $tg_ip_id = "\" id=\"";
	public $tg_class = "\" class=\"ro";
	public $tg_ip_cl = "\" />";
	public $tg_data_name= "\" data-name=\"";
	public $tg_chk = "<input type=\"checkbox\" name=\"chb";
	public $tg_chk_val = "";
	public $tg_dat = "<a  onclick=\"customeDateTimepicker('";
	public $tg_dat_cl = "','ddmmyyyy')\"><img src=datetimepick/cal.gif width=16 height=16 border=0 alt=Pick a date></a>";
	public $tg_sel = "<select id=\"";
	public $tg_sel_control = "<select class=\"form-control\" id=\"";
	public $tg_cl = "\" >";
	public $tg_div = "<div ";
	public $tg_div_class = "class=\"";
	public $tg_div_cl = "</div>";
	public $tg_sel_cl = "</select>";
	public $tg_opt = "<option value=\"";
	public $tg_opt_cl = "</option>";
	public $tg_hidden = "hidden";
	public $tg_readonly = "\" readonly ";
	public $tg_text = "<textarea rows=\"4\" class=\"form-control\" cols=\"15";
	public $tg_text_cl = "</textarea>";
	public $tg_img="<div><img src=\"";
	public $tg_img_alt="\" alt=\"";
	public $tg_img_ht="\" height=\"";
	public $tg_img_wd="\" width=\"";
	public $tg_img_cl="\"></div>"; 
	public $tg_thead="<thead>";
	public $tg_thead_cl="</thead>";
	public $tg_ip_class="\" class=\"form-control";
	public $tg_text_name=" name=\"";
	public $tg_ip_gp="<div class=\"form-group\"><label for=\"";
	public $tg_ip_gp_hide="<div class=\"form-group hide\"><label for=\"";
	public $tg_ip_gp_cl="</label>";
	public $tg_static="<div class=\"col-sm-10\"><p class=\"form-control-static\">";
	public $tg_static_cl="</p>";

	public function __construct($tbl=NULL){
	if($tbl) $this->tg_top = "<div class=\"table-responsive\"><table class=\"table table-striped\" id=\"".$tbl."\" width=auto border=1 cellpadding=2 cellspacing=2>";
	}
	
 }