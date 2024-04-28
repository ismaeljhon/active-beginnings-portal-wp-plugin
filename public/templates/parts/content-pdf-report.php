<?php
require_once PORTAL_URI . 'includes/class-pdf-generator.php';

$type = isset($_GET['student_id']) ? 'student' : 'centre';

$type = isset($_GET['gmp']) ? 'gmp' : $type;

$pdf = new PDF_Generator();

$pdf->generate($type);