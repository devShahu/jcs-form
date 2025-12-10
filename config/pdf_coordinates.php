<?php

/**
 * PDF Field Coordinates Configuration
 * 
 * Maps form fields to exact positions on the PDF template.
 * Coordinates are measured from bottom-left corner (PDF standard).
 * 
 * Note: These coordinates need to be measured from the actual PDF template.
 * Use a PDF editor or measurement tool to get exact positions.
 * 
 * Page dimensions: A4 (595.28 x 841.89 points)
 */

return [
    // Page 1 - Personal Information
    'page_1' => [
        'form_no' => [
            'x' => 450,
            'y' => 800,
            'width' => 120,
            'height' => 10,
            'font' => 'nikosh',
            'size' => 10,
            'align' => 'L'
        ],
        
        'name_bangla' => [
            'x' => 120,
            'y' => 770,
            'width' => 350,
            'height' => 10,
            'font' => 'nikosh',
            'size' => 11,
            'align' => 'L'
        ],
        
        'name_english' => [
            'x' => 120,
            'y' => 750,
            'width' => 350,
            'height' => 10,
            'font' => 'helvetica',
            'size' => 11,
            'align' => 'L'
        ],
        
        'father_name' => [
            'x' => 120,
            'y' => 730,
            'width' => 350,
            'height' => 10,
            'font' => 'nikosh',
            'size' => 10,
            'align' => 'L'
        ],
        
        'mother_name' => [
            'x' => 120,
            'y' => 710,
            'width' => 350,
            'height' => 10,
            'font' => 'nikosh',
            'size' => 10,
            'align' => 'L'
        ],
        
        // Photo position (top-right corner)
        'photo' => [
            'x' => 480,
            'y' => 700,
            'width' => 80,
            'height' => 100
        ],
        
        'mobile_number' => [
            'x' => 120,
            'y' => 680,
            'width' => 200,
            'height' => 10,
            'font' => 'helvetica',
            'size' => 10,
            'align' => 'L'
        ],
        
        'nid_birth_reg' => [
            'x' => 120,
            'y' => 660,
            'width' => 180,
            'height' => 10,
            'font' => 'helvetica',
            'size' => 10,
            'align' => 'L'
        ],
        
        'birth_date' => [
            'x' => 350,
            'y' => 660,
            'width' => 120,
            'height' => 10,
            'font' => 'helvetica',
            'size' => 10,
            'align' => 'L'
        ],
        
        'present_address' => [
            'x' => 120,
            'y' => 630,
            'width' => 450,
            'height' => 20,
            'font' => 'nikosh',
            'size' => 9,
            'align' => 'L',
            'multiline' => true
        ],
        
        'permanent_address' => [
            'x' => 120,
            'y' => 600,
            'width' => 450,
            'height' => 20,
            'font' => 'nikosh',
            'size' => 9,
            'align' => 'L',
            'multiline' => true
        ],
        
        'political_affiliation' => [
            'x' => 120,
            'y' => 570,
            'width' => 450,
            'height' => 10,
            'font' => 'nikosh',
            'size' => 9,
            'align' => 'L'
        ],
        
        'last_position' => [
            'x' => 120,
            'y' => 550,
            'width' => 450,
            'height' => 10,
            'font' => 'nikosh',
            'size' => 9,
            'align' => 'L'
        ],
        
        // Educational Qualification Table
        // SSC Row
        'ssc_year' => [
            'x' => 160,
            'y' => 480,
            'width' => 50,
            'height' => 10,
            'font' => 'helvetica',
            'size' => 9,
            'align' => 'C'
        ],
        'ssc_board' => [
            'x' => 220,
            'y' => 480,
            'width' => 70,
            'height' => 10,
            'font' => 'nikosh',
            'size' => 8,
            'align' => 'C'
        ],
        'ssc_group' => [
            'x' => 300,
            'y' => 480,
            'width' => 90,
            'height' => 10,
            'font' => 'nikosh',
            'size' => 8,
            'align' => 'C'
        ],
        'ssc_institution' => [
            'x' => 400,
            'y' => 480,
            'width' => 160,
            'height' => 10,
            'font' => 'nikosh',
            'size' => 8,
            'align' => 'L'
        ],
        
        // HSC Row
        'hsc_year' => [
            'x' => 160,
            'y' => 460,
            'width' => 50,
            'height' => 10,
            'font' => 'helvetica',
            'size' => 9,
            'align' => 'C'
        ],
        'hsc_board' => [
            'x' => 220,
            'y' => 460,
            'width' => 70,
            'height' => 10,
            'font' => 'nikosh',
            'size' => 8,
            'align' => 'C'
        ],
        'hsc_group' => [
            'x' => 300,
            'y' => 460,
            'width' => 90,
            'height' => 10,
            'font' => 'nikosh',
            'size' => 8,
            'align' => 'C'
        ],
        'hsc_institution' => [
            'x' => 400,
            'y' => 460,
            'width' => 160,
            'height' => 10,
            'font' => 'nikosh',
            'size' => 8,
            'align' => 'L'
        ],
        
        // Graduation Row
        'graduation_year' => [
            'x' => 160,
            'y' => 440,
            'width' => 50,
            'height' => 10,
            'font' => 'helvetica',
            'size' => 9,
            'align' => 'C'
        ],
        'graduation_board' => [
            'x' => 220,
            'y' => 440,
            'width' => 70,
            'height' => 10,
            'font' => 'nikosh',
            'size' => 8,
            'align' => 'C'
        ],
        'graduation_subject' => [
            'x' => 300,
            'y' => 440,
            'width' => 90,
            'height' => 10,
            'font' => 'nikosh',
            'size' => 8,
            'align' => 'C'
        ],
        'graduation_institution' => [
            'x' => 400,
            'y' => 440,
            'width' => 160,
            'height' => 10,
            'font' => 'nikosh',
            'size' => 8,
            'align' => 'L'
        ]
    ],
    
    // Page 2 - Declaration and Committee Section
    'page_2' => [
        'responsibility_ideology' => [
            'x' => 50,
            'y' => 750,
            'width' => 500,
            'height' => 50,
            'font' => 'nikosh',
            'size' => 9,
            'align' => 'L',
            'multiline' => true
        ],
        
        'declaration_name' => [
            'x' => 100,
            'y' => 620,
            'width' => 200,
            'height' => 10,
            'font' => 'nikosh',
            'size' => 10,
            'align' => 'L'
        ],
        
        // Set Committee Section
        'committee_member_name' => [
            'x' => 120,
            'y' => 450,
            'width' => 180,
            'height' => 10,
            'font' => 'nikosh',
            'size' => 9,
            'align' => 'L'
        ],
        
        'committee_member_position' => [
            'x' => 350,
            'y' => 450,
            'width' => 150,
            'height' => 10,
            'font' => 'nikosh',
            'size' => 9,
            'align' => 'L'
        ],
        
        'committee_member_comments' => [
            'x' => 120,
            'y' => 410,
            'width' => 450,
            'height' => 30,
            'font' => 'nikosh',
            'size' => 9,
            'align' => 'L',
            'multiline' => true
        ],
        
        'recommended_position' => [
            'x' => 120,
            'y' => 370,
            'width' => 200,
            'height' => 10,
            'font' => 'nikosh',
            'size' => 9,
            'align' => 'L'
        ]
    ],
    
    // Font paths
    'fonts' => [
        'nikosh' => __DIR__ . '/../fonts/Nikosh.ttf',
        'noto_sans_bengali' => __DIR__ . '/../fonts/NotoSansBengali-Regular.ttf'
    ]
];
