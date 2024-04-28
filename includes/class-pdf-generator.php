<?php
require_once PORTAL_URI . 'includes/lib/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

class PDF_Generator {
    private $pdf;

    public function __construct() {
        $this->pdf = new Dompdf(array('enable_remote' => true));
    }


    private function get_content($report) {
        $content = array(
            'centre' => PORTAL_URI . 'includes/reports/pdf-centre-report.php',
            'student' => PORTAL_URI . 'includes/reports/pdf-student-report.php',
            'gmp' => PORTAL_URI . 'includes/reports/pdf-gmp-report.php',
        );

        ob_start();
        require_once $content[$report];
        
        $html = ob_get_clean();
        ob_end_clean();
        
        return $html;
    }

    public function generate($report) {
        $html = $this->get_content($report);

        $this->pdf->loadHtml($html);
        $this->pdf->setPaper('A4', 'portrait');
        $this->pdf->render();
        $this->pdf->stream("report.pdf", array("Attachment" => false));
        
    }
}
