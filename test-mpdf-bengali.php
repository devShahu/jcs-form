<?php

require __DIR__ . '/vendor/autoload.php';

use Mpdf\Mpdf;

try {
    echo "Testing mPDF with Bengali font support (using built-in fonts)...\n\n";
    
    // Use mPDF's built-in DejaVu Sans Condensed which supports Bengali
    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'default_font' => 'dejavusanscondensed',
        'autoScriptToLang' => true,
        'autoLangToFont' => true,
        'useSubstitutions' => true,
    ]);
    
    $mpdf->SetDirectionality('ltr');
    
    echo "Using built-in DejaVu Sans Condensed font (supports Bengali)\n\n";
    
    // Test HTML with both English and Bengali
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            body {
                font-family: "dejavusanscondensed", sans-serif;
                font-size: 12pt;
            }
            .title {
                font-size: 20pt;
                font-weight: bold;
                color: #0f6b3f;
                text-align: center;
                margin: 20px 0;
            }
            .section {
                margin: 15px 0;
                padding: 10px;
                border: 1px solid #ccc;
            }
            .label {
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div class="title">জাতীয় ছাত্রশক্তি</div>
        <div class="title">National Student Power</div>
        
        <div class="section">
            <div class="label">নাম (বাংলায়):</div>
            <div>আব্দুল করিম রহমান</div>
        </div>
        
        <div class="section">
            <div class="label">Name (English):</div>
            <div>Abdul Karim Rahman</div>
        </div>
        
        <div class="section">
            <div class="label">ঠিকানা / Address:</div>
            <div>ঢাকা, বাংলাদেশ / Dhaka, Bangladesh</div>
        </div>
        
        <div class="section">
            <div class="label">Mixed Text Test:</div>
            <div>This is English text mixed with বাংলা টেক্সট in the same line.</div>
        </div>
        
        <div class="section">
            <div class="label">Numbers Test:</div>
            <div>English: 0123456789</div>
            <div>Bengali: ০১২৩৪৫৬৭৮৯</div>
        </div>
        
        <div class="section">
            <div class="label">Full Bengali Paragraph:</div>
            <div>
                আমি এই মর্মে অঙ্গীকার করিতেছি যে, এই ফরমে উল্লেখিত সকল তথ্য সঠিক এবং 
                জুলাই–আগস্ট ২০২৪ এর গণঅভ্যুত্থানের মধ্য থেকে গড়ে উঠা ছাত্র সংগঠন, 
                জাতীয় ছাত্রশক্তিতে যোগদানের মাধ্যমে জুলাই–আগস্ট ২০২৪-এর চেতনাকে সমুন্নত 
                রাখিতে আমি আগ্রহী।
            </div>
        </div>
    </body>
    </html>
    ';
    
    $mpdf->WriteHTML($html);
    
    $outputPath = __DIR__ . '/test-mpdf-bengali-output.pdf';
    $mpdf->Output($outputPath, \Mpdf\Output\Destination::FILE);
    
    echo "✓ PDF generated successfully!\n";
    echo "Output file: $outputPath\n";
    echo "\nPlease open the PDF to verify Bengali text is displaying correctly.\n";
    echo "DejaVu Sans has excellent Unicode support including Bengali script.\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
