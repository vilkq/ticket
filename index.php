<?
include("admincp/config.php");
// Include autoloader
require_once 'dompdf/autoload.inc.php';

// Reference the Dompdf namespace
use Dompdf\Dompdf;
use Dompdf\Options;

// Instantiate and use the dompdf class
$dompdf = new Dompdf($options);

$row = mysql_fetch_array(mysql_query("SELECT `count` FROM `ticket_config` AS `count`"));
$count = $row['count'];

$filename = 'ticketscreen'.$count.'.php';
$html = file_get_contents($filename);
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('ticket', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF (1 = download and 0 = preview)
$dompdf->stream("_",array("Attachment"=>false));
?>