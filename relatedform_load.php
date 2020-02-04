<?php
require_once('relatedforms.php');
$utils = new Utils();
$related_btn = array(
    'edit'=>array('comments','agreement','pdf_attachment','purchase_order_inward','tasks'),
    'new'=>array('comments','agreement','pdf_attachment','purchase_order_inward','tasks','notification_log','quotations','custom_contact'),
    'delete'=>array('comments','agreement','pdf_attachment','purchase_order_inward','tasks')
);
$sel    =   $_POST['sel'];
$tbl    =   $_POST['tbl'];
$sql  = "select customer_name from {$tbl} where {$sel}";
$data = $utils->first($sql);
if($data && !empty($data)){
    $opp_no = $data->customer_name;
    echo "<input type='hidden' name='task_opp' value='{$opp_no}'>";
}
echo relatedformload($sel,$tbl,$related_btn);


?>