<?php
// Test PDF generation with actual PDFGenerator class

require __DIR__ . '/vendor/autoload.php';

use App\PDFGenerator;

try {
    echo "Testing PDF Generation with PDFGenerator class...\n\n";
    
    // Demo data
    $demoData = [
        'form_no' => 'DEMO-001',
        'name_bangla' => 'আব্দুল করিম',
        'name_english' => 'ABDUL KARIM',
        'father_name' => 'মোহাম্মদ আলী',
        'mother_name' => 'ফাতেমা বেগম',
        'mobile_number' => '01712345678',
        'blood_group' => 'A+',
        'nid_birth_reg' => '1234567890123',
        'birth_date' => '01-01-2000',
        'present_address' => 'ঢাকা, বাংলাদেশ',
        'permanent_address' => 'চট্টগ্রাম, বাংলাদেশ',
        'political_affiliation' => 'কোনো রাজনৈতিক সংগঠনের সাথে জড়িত নই',
        'last_position' => 'N/A',
        'ssc_year' => '2015',
        'ssc_board' => 'ঢাকা',
        'ssc_group' => 'বিজ্ঞান',
        'ssc_institution' => 'ঢাকা কলেজিয়েট স্কুল',
        'hsc_year' => '2017',
        'hsc_board' => 'ঢাকা',
        'hsc_group' => 'বিজ্ঞান',
        'hsc_institution' => 'নটরডেম কলেজ',
        'graduation_year' => '2021',
        'graduation_board' => 'ঢাকা বিশ্ববিদ্যালয়',
        'graduation_subject' => 'কম্পিউটার সায়েন্স',
        'graduation_institution' => 'ঢাকা বিশ্ববিদ্যালয়',
        'movement_role' => 'জুলাই-আগস্ট ২০২৪ এর গণঅভ্যুত্থানে সক্রিয়ভাবে অংশগ্রহণ করেছি',
        'aspirations' => 'ছাত্রদের অধিকার রক্ষা এবং শিক্ষা ব্যবস্থার উন্নয়নে কাজ করতে চাই',
        'declaration_name' => 'আব্দুল করিম',
        'committee_member_name' => '',
        'committee_member_position' => '',
        'committee_member_comments' => ''
    ];
    
    echo "Creating PDFGenerator instance...\n";
    $pdfGenerator = new PDFGenerator();
    
    echo "Setting field data...\n";
    $pdfGenerator->setFieldData($demoData);
    
    echo "Generating PDF...\n";
    $pdfContent = $pdfGenerator->generateFromHTML();
    
    echo "PDF generated successfully!\n";
    echo "PDF size: " . strlen($pdfContent) . " bytes\n";
    
    // Save to file
    $outputPath = __DIR__ . '/test-pdf-output.pdf';
    file_put_contents($outputPath, $pdfContent);
    
    echo "PDF saved to: $outputPath\n";
    echo "\nSUCCESS! Open the PDF to check if it looks correct.\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
