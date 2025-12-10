<?php

return [
    'personal_info' => [
        'form_no' => [
            'name' => 'form_no',
            'label' => 'ফরম নং',
            'label_en' => 'Form No.',
            'type' => 'text',
            'required' => false,
            'placeholder' => 'সেট কমিটি কর্তৃক পূরণীয়',
            'validation' => ['max:50']
        ],
        'name_bangla' => [
            'name' => 'name_bangla',
            'label' => 'নাম (বাংলায়)',
            'label_en' => 'Name (in Bengali)',
            'type' => 'text',
            'required' => true,
            'placeholder' => 'আপনার নাম বাংলায় লিখুন',
            'validation' => ['required', 'min:2', 'max:100']
        ],
        'name_english' => [
            'name' => 'name_english',
            'label' => 'ইংরেজিতে বড় হাতের',
            'label_en' => 'Name (in English Capital Letters)',
            'type' => 'text',
            'required' => true,
            'placeholder' => 'YOUR NAME IN CAPITAL LETTERS',
            'validation' => ['required', 'min:2', 'max:100', 'uppercase']
        ],
        'father_name' => [
            'name' => 'father_name',
            'label' => 'পিতার নাম (বাংলায়)',
            'label_en' => "Father's Name (in Bengali)",
            'type' => 'text',
            'required' => true,
            'placeholder' => 'পিতার নাম বাংলায় লিখুন',
            'validation' => ['required', 'min:2', 'max:100']
        ],
        'mother_name' => [
            'name' => 'mother_name',
            'label' => 'মাতার নাম (বাংলায়)',
            'label_en' => "Mother's Name (in Bengali)",
            'type' => 'text',
            'required' => true,
            'placeholder' => 'মাতার নাম বাংলায় লিখুন',
            'validation' => ['required', 'min:2', 'max:100']
        ],
        'photo' => [
            'name' => 'photo',
            'label' => 'পাসপোর্ট সাইজ ছবি',
            'label_en' => 'Passport Size Photo',
            'type' => 'file',
            'required' => true,
            'accept' => 'image/jpeg,image/png',
            'max_size' => 5242880, // 5MB
            'note' => '১ কপি, সাদা ব্যাকগ্রাউন্ড',
            'validation' => ['required', 'image', 'max:5120']
        ]
    ],
    
    'contact_info' => [
        'mobile_number' => [
            'name' => 'mobile_number',
            'label' => 'মোবাইল নাম্বার',
            'label_en' => 'Mobile Number',
            'type' => 'tel',
            'required' => true,
            'placeholder' => '01XXXXXXXXX',
            'pattern' => '^01[3-9]\d{8}$',
            'validation' => ['required', 'regex:/^01[3-9]\d{8}$/']
        ],
        'blood_group' => [
            'name' => 'blood_group',
            'label' => 'রক্তের গ্রুপ',
            'label_en' => 'Blood Group',
            'type' => 'select',
            'required' => false,
            'options' => ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'],
            'validation' => ['max:5']
        ],
        'nid_birth_reg' => [
            'name' => 'nid_birth_reg',
            'label' => 'এনআইডি / জন্ম নিবন্ধন নম্বর',
            'label_en' => 'NID / Birth Registration Number',
            'type' => 'text',
            'required' => true,
            'placeholder' => 'NID বা জন্ম নিবন্ধন নম্বর',
            'validation' => ['required', 'min:10', 'max:20']
        ],
        'birth_date' => [
            'name' => 'birth_date',
            'label' => 'জন্ম তারিখ (দিন-মাস-সাল)',
            'label_en' => 'Date of Birth (DD-MM-YYYY)',
            'type' => 'date',
            'required' => true,
            'validation' => ['required', 'date', 'before:today']
        ]
    ],
    
    'address_info' => [
        'present_address' => [
            'name' => 'present_address',
            'label' => 'বর্তমান ঠিকানা',
            'label_en' => 'Present Address',
            'type' => 'textarea',
            'required' => true,
            'placeholder' => 'বর্তমান ঠিকানা লিখুন',
            'rows' => 3,
            'validation' => ['required', 'min:10', 'max:500']
        ],
        'permanent_address' => [
            'name' => 'permanent_address',
            'label' => 'স্থায়ী ঠিকানা',
            'label_en' => 'Permanent Address',
            'type' => 'textarea',
            'required' => true,
            'placeholder' => 'স্থায়ী ঠিকানা লিখুন',
            'rows' => 3,
            'validation' => ['required', 'min:10', 'max:500']
        ]
    ],
    
    'background_info' => [
        'political_affiliation' => [
            'name' => 'political_affiliation',
            'label' => 'রাজনৈতিক / সাংগঠনিক সম্পৃক্ততা',
            'label_en' => 'Political / Organizational Affiliation',
            'type' => 'text',
            'required' => false,
            'placeholder' => 'যদি থাকে',
            'validation' => ['max:200']
        ],
        'last_position' => [
            'name' => 'last_position',
            'label' => 'সর্বশেষ পদবী (কমিটির নাম সহ; যদি থাকে)',
            'label_en' => 'Last Position (with Committee Name if any)',
            'type' => 'text',
            'required' => false,
            'placeholder' => 'যদি থাকে',
            'validation' => ['max:200']
        ]
    ],
    
    'education' => [
        'ssc' => [
            'exam' => 'SSC / দাখিল / ভোকেশনাল / O levels',
            'fields' => [
                'year' => [
                    'name' => 'ssc_year',
                    'label' => 'Year / সাল',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => '2020',
                    'validation' => ['required', 'numeric', 'digits:4']
                ],
                'board' => [
                    'name' => 'ssc_board',
                    'label' => 'Board / বোর্ড',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'ঢাকা',
                    'validation' => ['required', 'max:100']
                ],
                'group' => [
                    'name' => 'ssc_group',
                    'label' => 'বিভাগ / গ্রুপ / সাবজেক্ট',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'বিজ্ঞান',
                    'validation' => ['required', 'max:100']
                ],
                'institution' => [
                    'name' => 'ssc_institution',
                    'label' => 'Institution / স্কুল / কলেজ',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'প্রতিষ্ঠানের নাম',
                    'validation' => ['required', 'max:200']
                ]
            ]
        ],
        'hsc' => [
            'exam' => 'HSC / আলিম / ডিপ্লোমা / A levels',
            'fields' => [
                'year' => [
                    'name' => 'hsc_year',
                    'label' => 'Year / সাল',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => '2022',
                    'validation' => ['required', 'numeric', 'digits:4']
                ],
                'board' => [
                    'name' => 'hsc_board',
                    'label' => 'Board / বোর্ড',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'ঢাকা',
                    'validation' => ['required', 'max:100']
                ],
                'group' => [
                    'name' => 'hsc_group',
                    'label' => 'বিভাগ / গ্রুপ / সাবজেক্ট',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'বিজ্ঞান',
                    'validation' => ['required', 'max:100']
                ],
                'institution' => [
                    'name' => 'hsc_institution',
                    'label' => 'Institution / কলেজ',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'প্রতিষ্ঠানের নাম',
                    'validation' => ['required', 'max:200']
                ]
            ]
        ],
        'graduation' => [
            'exam' => 'স্নাতক / মাস্টার্স / সমমানের ডিগ্রি',
            'note' => 'কেবল সেরা উচ্চতর ডিগ্রি',
            'fields' => [
                'year' => [
                    'name' => 'graduation_year',
                    'label' => 'Year / সাল',
                    'type' => 'text',
                    'required' => false,
                    'placeholder' => '2026',
                    'validation' => ['numeric', 'digits:4']
                ],
                'board' => [
                    'name' => 'graduation_board',
                    'label' => 'University / বিশ্ববিদ্যালয়',
                    'type' => 'text',
                    'required' => false,
                    'placeholder' => 'ঢাকা বিশ্ববিদ্যালয়',
                    'validation' => ['max:100']
                ],
                'subject' => [
                    'name' => 'graduation_subject',
                    'label' => 'Subject / বিষয়',
                    'type' => 'text',
                    'required' => false,
                    'placeholder' => 'কম্পিউটার সায়েন্স',
                    'validation' => ['max:100']
                ],
                'institution' => [
                    'name' => 'graduation_institution',
                    'label' => 'Institution / প্রতিষ্ঠান',
                    'type' => 'text',
                    'required' => false,
                    'placeholder' => 'প্রতিষ্ঠানের নাম',
                    'validation' => ['max:200']
                ]
            ]
        ]
    ],
    
    'declaration' => [
        'movement_role' => [
            'name' => 'movement_role',
            'label' => 'জুলাই–আগস্ট ২০২৪ এ ছাত্র–জনতার গণঅভ্যুত্থানে আপনার ভূমিকা ও সংযুক্তি',
            'label_en' => 'Your role in July-August 2024 student movement',
            'type' => 'textarea',
            'required' => true,
            'placeholder' => 'আপনার ভূমিকা লিখুন',
            'rows' => 4,
            'validation' => ['required', 'min:20', 'max:1000']
        ],
        'aspirations' => [
            'name' => 'aspirations',
            'label' => 'জাতীয় ছাত্রশক্তির জন্য আপনার চাওয়া এবং আকাঙ্ক্ষা কি?',
            'label_en' => 'What are your expectations from Jatiya Chhatra Shakti?',
            'type' => 'textarea',
            'required' => true,
            'placeholder' => 'আপনার চাওয়া এবং আকাঙ্ক্ষা লিখুন',
            'rows' => 5,
            'validation' => ['required', 'min:50', 'max:1000']
        ],
        'declaration_name' => [
            'name' => 'declaration_name',
            'label' => 'আমি',
            'label_en' => 'I',
            'type' => 'text',
            'required' => true,
            'placeholder' => 'আপনার নাম',
            'validation' => ['required', 'min:2', 'max:100']
        ],
        'declaration_agreement' => [
            'name' => 'declaration_agreement',
            'label' => 'আমি ঘোষণা করছি যে উপরের সমস্ত তথ্য সত্য এবং সঠিক',
            'label_en' => 'I declare that all the above information is true and correct',
            'type' => 'checkbox',
            'required' => true,
            'validation' => ['required', 'accepted']
        ]
    ],
    
    'committee_section' => [
        'committee_member_name' => [
            'name' => 'committee_member_name',
            'label' => 'সাট কমিটির সদস্যের নাম',
            'label_en' => 'Set Committee Member Name',
            'type' => 'text',
            'required' => false,
            'placeholder' => 'কমিটি সদস্যের নাম',
            'validation' => ['max:100']
        ],
        'committee_member_position' => [
            'name' => 'committee_member_position',
            'label' => 'পদবী',
            'label_en' => 'Position',
            'type' => 'text',
            'required' => false,
            'placeholder' => 'পদবী',
            'validation' => ['max:100']
        ],
        'committee_member_comments' => [
            'name' => 'committee_member_comments',
            'label' => 'সাট কমিটির সদস্যের মন্তব্য',
            'label_en' => 'Set Committee Member Comments',
            'type' => 'textarea',
            'required' => false,
            'placeholder' => 'মন্তব্য',
            'rows' => 3,
            'validation' => ['max:500']
        ],
        'recommended_position' => [
            'name' => 'recommended_position',
            'label' => 'রেকমেন্ডকৃত পদবী',
            'label_en' => 'Recommended Position',
            'type' => 'text',
            'required' => false,
            'placeholder' => 'রেকমেন্ডকৃত পদবী',
            'validation' => ['max:100']
        ]
    ]
];
