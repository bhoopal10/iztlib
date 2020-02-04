
<?php
require_once 'vendor/autoload.php';
// include_once('invoice-generate-pdf-fun.php');
// generatePDF(91);
use Dompdf\Dompdf;
use Dompdf\Options;


class PdfGeneration {
    public $data;
    function genertePdf($data,$file_name,$utils,$is_header=false){
        $this->data = $data;
        $html = $this->renderTemplate($data,$utils,$is_header);
       return $this->savePdf($html,$file_name);
        // echo $html;

    }
    function renderTemplate($data,$utils,$is_header){
        ob_start();
        include(__DIR__.'/../pdf-generator.php');
        $var=ob_get_contents(); 
        ob_end_clean();
        return $var;
    }

    function savePdf($html,$file_name){
        $options = new Options();
        $options->set('defaultFont', 'Arial, Helvetica, sans-serif');
        $dompdf = new Dompdf($options);
        $dompdf->set_option('debugCss',true);
        $dompdf->set_option('isHtml5ParserEnabled',true);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();
        file_put_contents('public/pdf_attachment/'.$file_name.'.pdf', $dompdf->output());
        return 'public/pdf_attachment/'.$file_name.'.pdf';
    }
}