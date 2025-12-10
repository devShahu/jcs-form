<?php
// Test script to verify Bengali font in PDF

require __DIR__ . '/vendor/autoload.php';

use TCPDF;
use TCPDF_FONTS;

try {
    echo "Testing Bengali PDF Generation...\n\n";
    
    // Test Bengali fonts available in TCPDF
    $fontsToTry = ['freeserif', 'freesans', 'dejavusans'];
    $bengaliFontName = null;
    
    echo "Testing available fonts for Bengali support...\n";
    foreach ($fontsToTry as $font) {
        try {
            $testPdf = new TCPDF();
            $testPdf->SetFont($font, '', 10);
            echo "✓ Font available: $font\n";
            if ($bengaliFontName === null) {
                $bengaliFontName = $font;
            }
        } catch (Exception $e) {
            echo "✗ Font not available: $font\n";
        }
    }
    
    if ($bengaliFontName === null) {
        die("ERROR: No Bengali-compatible fonts found\n");
    }
    
    echo "\nUsing font: $bengaliFontName\n";
    
    // Create PDF
    echo "Creating PDF...\n";
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetFont($bengaliFontName, '', 14);
    
    // Add page
    $pdf->AddPage();
    
    // Test Bengali text
    $bengaliText = '<h1>জাতীয় ছাত্রশক্তি</h1>';
    $bengaliText .= '<p>এটি একটি পরীক্ষা। বাংলা টেক্সট সঠিকভাবে দেখা যাচ্ছে কিনা তা যাচাই করা হচ্ছে।</p>';
    $bengaliText .= '<p>নাম: আব্দুল করিম</p>';
    $bengaliText .= '<p>ঠিকানা: ঢাকা, বাংলাদেশ</p>';
    $bengaliText .= '<p>মোবাইল: ০১৭১২৩৪৫৬৭৮</p>';
    $bengaliText .= '<p>শিক্ষা: ঢাকা বিশ্ববিদ্যালয়</p>';
    
    $pdf->writeHTML($bengaliText, true, false, true, false, '');
    
    // Save PDF
    $outputPath = __DIR__ . '/test-bengali-output.pdf';
    $pdf->Output($outputPath, 'F');
    
    echo "✓ PDF created successfully: $outputPath\n";
    echo "\n";
    echo "SUCCESS! Open the PDF to verify Bengali text renders correctly.\n";
    echo "If you see Bengali characters (not ???), the font is working!\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
