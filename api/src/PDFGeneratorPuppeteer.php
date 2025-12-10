<?php

namespace App;

use Spatie\Browsershot\Browsershot;

class PDFGeneratorPuppeteer
{
    private $templatePath;

    public function __construct()
    {
        $this->templatePath = __DIR__ . '/../../templates/pdf_template.html';
    }

    public function generate($formData, $outputPath)
    {
        try {
            // Load and process template
            $html = $this->loadTemplate($formData);
            
            // Generate PDF using Puppeteer via Browsershot
            Browsershot::html($html)
                ->setNodeBinary('node')
                ->setNpmBinary('npm')
                ->format('A4')
                ->margins(15, 15, 15, 15)
                ->showBackground()
                ->timeout(60) // 60 seconds timeout
                ->save($outputPath);
            
            return true;
        } catch (\Exception $e) {
            error_log("PDF Generation Error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw new \Exception("Failed to generate PDF: " . $e->getMessage());
        }
    }

    private function loadTemplate($formData)
    {
        if (!file_exists($this->templatePath)) {
            throw new \Exception("Template file not found: " . $this->templatePath);
        }

        $html = file_get_contents($this->templatePath);
        
        // Replace placeholders with actual data
        $html = $this->replacePlaceholders($html, $formData);
        
        // Add print-specific CSS
        $html = $this->addPrintCSS($html);
        
        return $html;
    }

    private function replacePlaceholders($html, $formData)
    {
        // Get logo from settings
        $logoHtml = $this->getLogoHtml();
        
        // Map form data to template placeholders
        $replacements = [
            // Logo (embedded as base64)
            '{{logo_path}}' => $logoHtml,
            
            // Form number
            '{{form_no}}' => $this->escape($formData['form_no'] ?? ''),
            
            // Personal info
            '{{name_bangla}}' => $this->escape($formData['name_bangla'] ?? $formData['full_name_bengali'] ?? ''),
            '{{name_english}}' => $this->escape($formData['name_english'] ?? $formData['full_name'] ?? ''),
            '{{father_name}}' => $this->escape($formData['father_name'] ?? $formData['father_name_bengali'] ?? ''),
            '{{mother_name}}' => $this->escape($formData['mother_name'] ?? $formData['mother_name_bengali'] ?? ''),
            '{{mobile_number}}' => $this->escape($formData['mobile_number'] ?? $formData['mobile'] ?? ''),
            '{{blood_group}}' => $this->escape($formData['blood_group'] ?? ''),
            '{{nid_birth_reg}}' => $this->escape($formData['nid_birth_reg'] ?? $formData['nid'] ?? ''),
            '{{birth_date}}' => $this->escape($formData['birth_date'] ?? $formData['date_of_birth'] ?? ''),
            '{{present_address}}' => $this->escape($formData['present_address'] ?? ''),
            '{{permanent_address}}' => $this->escape($formData['permanent_address'] ?? ''),
            '{{political_affiliation}}' => $this->escape($formData['political_affiliation'] ?? ''),
            '{{last_position}}' => $this->escape($formData['last_position'] ?? ''),
            
            // Education
            '{{ssc_year}}' => $this->escape($formData['ssc_year'] ?? ''),
            '{{ssc_board}}' => $this->escape($formData['ssc_board'] ?? ''),
            '{{ssc_group}}' => $this->escape($formData['ssc_group'] ?? ''),
            '{{ssc_institution}}' => $this->escape($formData['ssc_institution'] ?? ''),
            '{{hsc_year}}' => $this->escape($formData['hsc_year'] ?? ''),
            '{{hsc_board}}' => $this->escape($formData['hsc_board'] ?? ''),
            '{{hsc_group}}' => $this->escape($formData['hsc_group'] ?? ''),
            '{{hsc_institution}}' => $this->escape($formData['hsc_institution'] ?? ''),
            '{{graduation_year}}' => $this->escape($formData['graduation_year'] ?? ''),
            '{{graduation_board}}' => $this->escape($formData['graduation_board'] ?? ''),
            '{{graduation_subject}}' => $this->escape($formData['graduation_subject'] ?? ''),
            '{{graduation_institution}}' => $this->escape($formData['graduation_institution'] ?? ''),
            
            // Movement and aspirations
            '{{movement_role}}' => $this->escape($formData['movement_role'] ?? ''),
            '{{aspirations}}' => $this->escape($formData['aspirations'] ?? ''),
            
            // Declaration
            '{{declaration_name}}' => $this->escape($formData['declaration_name'] ?? $formData['name_bangla'] ?? ''),
            
            // Committee section
            '{{committee_member_name}}' => $this->escape($formData['committee_member_name'] ?? ''),
            '{{committee_member_position}}' => $this->escape($formData['committee_member_position'] ?? ''),
            '{{committee_member_comments}}' => $this->escape($formData['committee_member_comments'] ?? ''),
            
            // Photo
            '{{photo}}' => $this->getPhotoHtml($formData['photo'] ?? null),
        ];

        foreach ($replacements as $placeholder => $value) {
            $html = str_replace($placeholder, $value, $html);
        }
        
        // Remove any remaining placeholders
        $html = preg_replace('/\{\{[^}]+\}\}/', '', $html);

        return $html;
    }

    private function addPrintCSS($html)
    {
        // Add print-specific CSS to ensure proper page breaks
        $printCSS = '
        <style>
            @page {
                size: A4;
                margin: 15mm;
            }
            
            @media print {
                .pdf-page {
                    page-break-after: always;
                    page-break-inside: avoid;
                }
                
                .pdf-page:last-child {
                    page-break-after: auto;
                }
            }
        </style>
        ';
        
        // Insert before </head>
        $html = str_replace('</head>', $printCSS . '</head>', $html);
        
        return $html;
    }

    private function escape($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    private function getLogoHtml()
    {
        // Try to get logo from settings
        $settingsPath = __DIR__ . '/../../storage/settings.json';
        $logoPath = null;
        
        if (file_exists($settingsPath)) {
            $settings = json_decode(file_get_contents($settingsPath), true);
            $logoPath = $settings['logo_path'] ?? null;
        }
        
        // If no logo in settings, try default location
        if (empty($logoPath)) {
            $logoPath = 'public/images/logo.png';
        }
        
        $fullPath = __DIR__ . '/../../' . $logoPath;
        
        if (!file_exists($fullPath)) {
            // Return empty string if logo not found
            return '';
        }

        // Convert image to base64 for embedding
        $imageData = base64_encode(file_get_contents($fullPath));
        $imageType = pathinfo($fullPath, PATHINFO_EXTENSION);
        
        return 'data:image/' . $imageType . ';base64,' . $imageData;
    }
    
    private function getPhotoHtml($photoPath)
    {
        if (empty($photoPath)) {
            // Return placeholder text if no photo
            return '<div style="display: flex; align-items: center; justify-content: center; height: 100%; text-align: center; font-size: 10pt; line-height: 1.3;">পাসপোর্ট<br>সাইজ<br>ছবি<br><span style="font-size: 9pt;">(১ কপি, সাদা ব্যাকগ্রাউন্ড)</span></div>';
        }

        $fullPath = __DIR__ . '/../../' . $photoPath;
        
        if (!file_exists($fullPath)) {
            // Return placeholder if photo file not found
            return '<div style="display: flex; align-items: center; justify-content: center; height: 100%; text-align: center; font-size: 10pt; line-height: 1.3;">পাসপোর্ট<br>সাইজ<br>ছবি<br><span style="font-size: 9pt;">(১ কপি, সাদা ব্যাকগ্রাউন্ড)</span></div>';
        }

        // Convert image to base64 for embedding
        $imageData = base64_encode(file_get_contents($fullPath));
        $imageType = pathinfo($fullPath, PATHINFO_EXTENSION);
        
        return '<img src="data:image/' . $imageType . ';base64,' . $imageData . '" style="width: 100%; height: 100%; object-fit: cover;" />';
    }
}
