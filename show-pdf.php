<?php

// Include Dompdf autoloader
require_once __DIR__ . '/lib/php/dompdf/autoload.inc.php';
// return;

// load wordpress
define( 'WP_USE_THEMES', false );
$path = __DIR__ . '/../../../wp-load.php';
require_once( $path );

// include WebbsitesFormSub
require_once __DIR__ . '/lib/php/object-sub.php';

if( intval( $_GET['id'] ) == 0 ) return false;

$sub_id = intval( $_GET['id'] );

// Start the output buffer
ob_start();

// Generate the PDF
$sub = new WebbsitesFormSub( $sub_id );
$sub->generate_pdf();

// Save the buffer contents
$html = ob_get_clean();


// reference the Dompdf namespace
use Dompdf\Dompdf;

// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->loadHtml( $html );

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('Letter', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream();