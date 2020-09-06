<?
include("config.php");
require_once '../dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;
$dompdf = new Dompdf($options);
$filename = 'ticketscreen_2000789002644.php';
$html = file_get_contents($filename);
$dompdf->loadHtml($html);
$dompdf->setPaper('ticket', 'landscape');
$dompdf->render();
$dompdf->stream("_",array("Attachment"=>false));