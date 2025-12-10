<?php

require __DIR__ . '/vendor/autoload.php';

use App\PDFGeneratorMpdf;

try {
    echo "Testing form submission with Bengali data...\n\n";
    
    // Sample form data with Bengali content
    $formData = [
        'full_name' => 'ABDUL KARIM RAHMAN',
        'full_name_bengali' => 'আব্দুল করিম রহমান',
        'father_name' => 'Mohammad Ali',
        'father_name_bengali' => 'মোহাম্মদ আলী',
        'mother_name' => 'Fatema Begum',
        'mother_name_bengali' => 'ফাতেমা বেগম',
        'date_of_birth' => '1995-05-15',
        'nid' => '1234567890123',
        'mobile' => '01712345678',
        'email' => 'abdul@example.com',
        'present_address' => 'House 10, Road 5, Dhanmondi, Dhaka',
        'present_address_bengali' => 'বাড়ি ১০, রোড ৫, ধানমন্ডি, ঢাকা',
        'permanent_address' => 'Village: Rampur, Post: Sreepur, District: Chittagong',
        'permanent_address_bengali' => 'গ্রাম: রামপুর, ডাক: শ্রীপুর, জেলা: চট্টগ্রাম',
        'education_level' => 'Bachelor of Science',
        'institution' => 'Dhaka University',
        'institution_bengali' => 'ঢাকা বিশ্ববিদ্যালয়',
        'passing_year' => '2020',
        'blood_group' => 'A+',
        'occupation' => 'Software Engineer',
        'occupation_bengali' => 'সফটওয়্যার ইঞ্জিনিয়ার',
        'organization' => 'Tech Solutions Ltd',
        'organization_bengali' => 'টেক সলিউশন্স লিমিটেড',
        'designation' => 'Senior Developer',
        'designation_bengali' => 'সিনিয়র ডেভেলপার',
        'monthly_income' => '50000',
        'declaration_date' => date('Y-m-d'),
    ];
    
    echo "Form Data:\n";
    echo "- Name (English): " . $formData['full_name'] . "\n";
    echo "- Name (Bengali): " . $formData['full_name_bengali'] . "\n";
    echo "- Address (Bengali): " . $formData['present_address_bengali'] . "\n\n";
    
    // Generate PDF
    $pdfGenerator = new PDFGeneratorMpdf();
    $outputPath = __DIR__ . '/test-form-submission-output.pdf';
    
    echo "Generating PDF...\n";
    $pdfGenerator->generate($formData, $outputPath);
    
    echo "✓ PDF generated successfully!\n";
    echo "Output file: $outputPath\n";
    echo "File size: " . number_format(filesize($outputPath) / 1024, 2) . " KB\n\n";
    echo "Please open the PDF to verify:\n";
    echo "1. Bengali text displays correctly (no question marks)\n";
    echo "2. English text displays correctly\n";
    echo "3. Mixed Bengali/English lines work properly\n";
    echo "4. Layout and formatting look good\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
