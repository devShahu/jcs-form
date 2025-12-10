<?php

require __DIR__ . '/vendor/autoload.php';

use App\PDFGeneratorMpdf;

try {
    echo "Testing full form with Bengali data...\n\n";
    
    // Complete form data matching the template structure
    $formData = [
        'form_no' => 'JCS-2024-001',
        'name_bangla' => 'আব্দুল করিম রহমান',
        'name_english' => 'ABDUL KARIM RAHMAN',
        'father_name' => 'মোহাম্মদ আলী',
        'mother_name' => 'ফাতেমা বেগম',
        'mobile_number' => '01712345678',
        'blood_group' => 'A+',
        'nid_birth_reg' => '1234567890123',
        'birth_date' => '০১-০১-২০০০',
        'present_address' => 'বাড়ি ১০, রোড ৫, ধানমন্ডি, ঢাকা',
        'permanent_address' => 'গ্রাম: রামপুর, ডাক: শ্রীপুর, জেলা: চট্টগ্রাম',
        'political_affiliation' => 'কোনো রাজনৈতিক সংগঠনের সাথে জড়িত নই',
        'last_position' => 'প্রযোজ্য নয়',
        
        // Education
        'ssc_year' => '২০১৫',
        'ssc_board' => 'ঢাকা',
        'ssc_group' => 'বিজ্ঞান',
        'ssc_institution' => 'ঢাকা কলেজিয়েট স্কুল',
        'hsc_year' => '২০১৭',
        'hsc_board' => 'ঢাকা',
        'hsc_group' => 'বিজ্ঞান',
        'hsc_institution' => 'নটরডেম কলেজ',
        'graduation_year' => '২০২১',
        'graduation_board' => 'ঢাকা বিশ্ববিদ্যালয়',
        'graduation_subject' => 'কম্পিউটার সায়েন্স',
        'graduation_institution' => 'ঢাকা বিশ্ববিদ্যালয়',
        
        // Movement and aspirations
        'movement_role' => 'জুলাই-আগস্ট ২০২৪ এর গণঅভ্যুত্থানে সক্রিয়ভাবে অংশগ্রহণ করেছি এবং ছাত্রদের অধিকার রক্ষায় কাজ করেছি।',
        'aspirations' => 'ছাত্রদের অধিকার রক্ষা এবং শিক্ষা ব্যবস্থার উন্নয়নে কাজ করতে চাই। একটি ন্যায়ভিত্তিক সমাজ গড়তে অবদান রাখতে চাই।',
        
        // Declaration
        'declaration_name' => 'আব্দুল করিম রহমান',
        
        // Committee (usually empty)
        'committee_member_name' => '',
        'committee_member_position' => '',
        'committee_member_comments' => '',
    ];
    
    echo "Form Data Summary:\n";
    echo "- Name (Bengali): " . $formData['name_bangla'] . "\n";
    echo "- Name (English): " . $formData['name_english'] . "\n";
    echo "- Address: " . $formData['present_address'] . "\n";
    echo "- Education: " . $formData['graduation_institution'] . "\n\n";
    
    // Generate PDF
    $pdfGenerator = new PDFGeneratorMpdf();
    $outputPath = __DIR__ . '/test-full-form-output.pdf';
    
    echo "Generating PDF...\n";
    $pdfGenerator->generate($formData, $outputPath);
    
    echo "✓ PDF generated successfully!\n";
    echo "Output file: $outputPath\n";
    echo "File size: " . number_format(filesize($outputPath) / 1024, 2) . " KB\n\n";
    
    echo "✓ Verification checklist:\n";
    echo "  1. Open the PDF file\n";
    echo "  2. Check Bengali text displays correctly (জাতীয় ছাত্রশক্তি)\n";
    echo "  3. Check English text displays correctly\n";
    echo "  4. Check mixed Bengali/English lines\n";
    echo "  5. Check table formatting\n";
    echo "  6. Check layout matches original design\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
