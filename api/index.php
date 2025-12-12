<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\FormValidator;
use App\PDFGenerator;
use App\PDFGeneratorPuppeteer;
use App\PhotoProcessor;
use App\StorageManager;
use App\SettingsManager;
use App\Controllers\AdminController;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// Initialize session early with proper settings
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/',
        'domain' => '',
        'secure' => false, // Set to true in production with HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Create Slim app
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add CORS middleware
$app->add(function (Request $request, $handler) {
    $response = $handler->handle($request);
    $origin = $request->getHeaderLine('Origin') ?: 'http://localhost:3000';
    
    return $response
        ->withHeader('Access-Control-Allow-Origin', $origin)
        ->withHeader('Access-Control-Allow-Credentials', 'true')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, Cookie, X-Admin-Token')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
        ->withHeader('Access-Control-Expose-Headers', 'Set-Cookie');
});

// Handle OPTIONS requests
$app->options('/{routes:.+}', function (Request $request, Response $response) {
    return $response;
});

// Health check endpoint (root level for Railway)
$app->get('/health', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode([
        'status' => 'ok',
        'message' => 'API is running',
        'timestamp' => date('Y-m-d H:i:s')
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Health check endpoint (API level)
$app->get('/api/health', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode([
        'status' => 'ok',
        'message' => 'API is running',
        'timestamp' => date('Y-m-d H:i:s')
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Get form configuration
$app->get('/api/config', function (Request $request, Response $response) {
    $formFields = require __DIR__ . '/../config/form_fields.php';
    
    $response->getBody()->write(json_encode([
        'success' => true,
        'fields' => $formFields
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Get public settings (no auth required)
$app->get('/api/settings', function (Request $request, Response $response) {
    try {
        $settings = new SettingsManager();
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $settings->getSettings()
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        error_log('Get public settings error: ' . $e->getMessage());
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'Error retrieving settings'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// Validate form step
$app->post('/api/validate', function (Request $request, Response $response) {
    try {
        $data = $request->getParsedBody();
        $step = $data['step'] ?? null;
        $formData = $data['data'] ?? [];

        error_log('Validation request - Step: ' . $step);
        error_log('Form data: ' . json_encode($formData));

        $validator = new FormValidator();
        
        if ($step) {
            $result = $validator->validateStep($step, $formData);
        } else {
            $result = $validator->validate($formData);
        }

        error_log('Validation result - Valid: ' . ($result->isValid ? 'true' : 'false'));
        error_log('Errors: ' . json_encode($result->errors));

        $response->getBody()->write(json_encode([
            'success' => $result->isValid,
            'errors' => $result->errors,
            'data' => $result->sanitizedData
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        error_log('Validation exception: ' . $e->getMessage());
        $response->getBody()->write(json_encode([
            'success' => false,
            'errors' => ['general' => 'Validation error: ' . $e->getMessage()]
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// Upload photo
$app->post('/api/upload-photo', function (Request $request, Response $response) {
    try {
        $uploadedFiles = $request->getUploadedFiles();
        
        if (!isset($uploadedFiles['photo'])) {
            throw new Exception('No photo uploaded');
        }

        $photoFile = $uploadedFiles['photo'];
        $processor = new PhotoProcessor();
        $result = $processor->process($photoFile);

        $response->getBody()->write(json_encode([
            'success' => true,
            'photo_path' => $result['path'],
            'photo_url' => $result['url']
        ]));
    } catch (Exception $e) {
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]));
    }
    
    return $response->withHeader('Content-Type', 'application/json');
});

// Submit form
$app->post('/api/submit', function (Request $request, Response $response) {
    try {
        // Increase execution time for PDF generation
        set_time_limit(120); // 2 minutes
        
        $body = (string) $request->getBody();
        error_log('Raw body: ' . $body);
        
        $formData = json_decode($body, true);
        
        if ($formData === null) {
            $formData = $request->getParsedBody();
        }
        
        error_log('Submit request received');
        error_log('Form data: ' . json_encode($formData));

        if (empty($formData)) {
            throw new Exception('No form data received');
        }

        // Ensure temp directory exists
        $tempDir = __DIR__ . '/../storage/temp';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Generate PDF with actual data using Puppeteer
        error_log('Starting PDF generation...');
        $startTime = microtime(true);
        
        $pdfGenerator = new PDFGeneratorPuppeteer();
        
        // Generate temporary file path
        $tempPath = $tempDir . '/submission_' . uniqid() . '.pdf';
        $pdfGenerator->generate($formData, $tempPath);
        
        $endTime = microtime(true);
        $duration = round($endTime - $startTime, 2);
        error_log("PDF generated in {$duration} seconds");
        
        // Read the generated PDF
        $pdfContent = file_get_contents($tempPath);
        
        // Clean up temp file
        if (file_exists($tempPath)) {
            unlink($tempPath);
        }
        
        // Save submission
        $storage = new StorageManager();
        $submissionId = $storage->saveSubmission($formData, $pdfContent);
        error_log('Submission saved with ID: ' . $submissionId);

        $response->getBody()->write(json_encode([
            'success' => true,
            'submission_id' => $submissionId,
            'message' => 'Form submitted successfully'
        ]));
    } catch (Exception $e) {
        error_log('Submit error: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    return $response->withHeader('Content-Type', 'application/json');
});

// Download PDF
$app->get('/api/download/{id}', function (Request $request, Response $response, array $args) {
    try {
        $id = $args['id'];
        $storage = new StorageManager();
        $pdfPath = $storage->getPDF($id);

        if (!file_exists($pdfPath)) {
            throw new Exception('PDF not found');
        }

        $response->getBody()->write(file_get_contents($pdfPath));
        return $response
            ->withHeader('Content-Type', 'application/pdf')
            ->withHeader('Content-Disposition', 'attachment; filename="jcs_membership_' . $id . '.pdf"');
    } catch (Exception $e) {
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }
});

// ==================== ADMIN ENDPOINTS ====================

// Admin login
$app->post('/api/admin/login', function (Request $request, Response $response) {
    try {
        $body = (string) $request->getBody();
        $data = json_decode($body, true);
        
        if ($data === null) {
            $data = $request->getParsedBody();
        }
        
        if (empty($data)) {
            $data = []; // Provide empty array instead of null
        }
        
        $admin = new AdminController();
        $result = $admin->login($data);

        $statusCode = $result['success'] ? 200 : 401;
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    } catch (Exception $e) {
        error_log('Admin login error: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'Login error: ' . $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// Admin logout
$app->post('/api/admin/logout', function (Request $request, Response $response) {
    try {
        $admin = new AdminController();
        $result = $admin->logout();

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        error_log('Admin logout error: ' . $e->getMessage());
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'Logout error'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// List all submissions
$app->get('/api/admin/submissions', function (Request $request, Response $response) {
    try {
        $params = $request->getQueryParams();
        $admin = new AdminController();
        $result = $admin->listSubmissions($params);

        $statusCode = $result['success'] ? 200 : 401;
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    } catch (Exception $e) {
        error_log('Admin list submissions error: ' . $e->getMessage());
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'Error retrieving submissions'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// Get single submission
$app->get('/api/admin/submissions/{id}', function (Request $request, Response $response, array $args) {
    try {
        $id = $args['id'];
        $admin = new AdminController();
        $result = $admin->getSubmission($id);

        $statusCode = $result['success'] ? 200 : ($result['message'] === 'Unauthorized' ? 401 : 404);
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    } catch (Exception $e) {
        error_log('Admin get submission error: ' . $e->getMessage());
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'Error retrieving submission'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// Download PDF from admin
$app->get('/api/admin/submissions/{id}/pdf', function (Request $request, Response $response, array $args) {
    try {
        $id = $args['id'];
        $admin = new AdminController();
        $result = $admin->downloadPDF($id);

        if (!$result['success']) {
            $statusCode = $result['message'] === 'Unauthorized' ? 401 : 404;
            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
        }

        $response->getBody()->write(file_get_contents($result['path']));
        return $response
            ->withHeader('Content-Type', 'application/pdf')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $result['filename'] . '"');
    } catch (Exception $e) {
        error_log('Admin download PDF error: ' . $e->getMessage());
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'Error downloading PDF'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// Search submissions
$app->get('/api/admin/search', function (Request $request, Response $response) {
    try {
        $params = $request->getQueryParams();
        $admin = new AdminController();
        $result = $admin->searchSubmissions($params);

        $statusCode = $result['success'] ? 200 : 401;
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    } catch (Exception $e) {
        error_log('Admin search error: ' . $e->getMessage());
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'Error searching submissions'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// Get settings
$app->get('/api/admin/settings', function (Request $request, Response $response) {
    try {
        $admin = new AdminController();
        if (!$admin->verifyAuth()) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Unauthorized'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $settings = new SettingsManager();
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $settings->getSettings()
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        error_log('Get settings error: ' . $e->getMessage());
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'Error retrieving settings'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// Update settings
$app->post('/api/admin/settings', function (Request $request, Response $response) {
    try {
        $admin = new AdminController();
        if (!$admin->verifyAuth()) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Unauthorized'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $body = (string) $request->getBody();
        $data = json_decode($body, true);
        
        if (empty($data)) {
            throw new Exception('No data provided');
        }

        $settings = new SettingsManager();
        $success = $settings->updateSettings($data);

        $response->getBody()->write(json_encode([
            'success' => $success,
            'message' => $success ? 'Settings updated successfully' : 'Failed to update settings',
            'data' => $settings->getSettings()
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        error_log('Update settings error: ' . $e->getMessage());
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'Error updating settings: ' . $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// Upload logo
$app->post('/api/admin/upload-logo', function (Request $request, Response $response) {
    try {
        $admin = new AdminController();
        if (!$admin->verifyAuth()) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Unauthorized'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $uploadedFiles = $request->getUploadedFiles();
        
        if (!isset($uploadedFiles['logo'])) {
            throw new Exception('No logo file uploaded');
        }

        $settings = new SettingsManager();
        $result = $settings->uploadLogo($uploadedFiles['logo']);

        $statusCode = $result['success'] ? 200 : 400;
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    } catch (Exception $e) {
        error_log('Upload logo error: ' . $e->getMessage());
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'Error uploading logo: ' . $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// Test HTML generation with demo data
$app->get('/api/admin/test-html', function (Request $request, Response $response) {
    try {
        $admin = new AdminController();
        if (!$admin->verifyAuth()) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Unauthorized'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

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

        // Load template and replace placeholders
        $templatePath = __DIR__ . '/../templates/pdf_template.html';
        if (!file_exists($templatePath)) {
            throw new Exception('Template not found');
        }
        
        $html = file_get_contents($templatePath);
        
        // Load settings
        $settings = new SettingsManager();
        $orgNameBn = $settings->getSetting('org_name_bn', 'জাতীয় ছাত্রশক্তি');
        $html = str_replace('{{org_name_bn}}', $orgNameBn, $html);
        
        // Replace logo path
        $logoPath = $settings->getSetting('logo_path', '/images/logo.png');
        $html = str_replace('{{logo_path}}', $logoPath, $html);
        
        // Replace all placeholders
        foreach ($demoData as $key => $value) {
            $html = str_replace('{{' . $key . '}}', htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'), $html);
        }
        
        // Remove any remaining placeholders
        $html = preg_replace('/\{\{[^}]+\}\}/', '', $html);
        
        // Handle photo placeholder
        $html = str_replace('{{#if photo}}', '<!--', $html);
        $html = str_replace('{{else}}', '-->', $html);
        $html = str_replace('{{/if}}', '', $html);

        // Return HTML
        $response->getBody()->write($html);
        return $response
            ->withHeader('Content-Type', 'text/html; charset=UTF-8')
            ->withHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
    } catch (Exception $e) {
        error_log('Test HTML error: ' . $e->getMessage());
        $response->getBody()->write('Error: ' . $e->getMessage());
        return $response->withHeader('Content-Type', 'text/plain')->withStatus(500);
    }
});

// Test PDF generation with demo data
$app->get('/api/admin/test-pdf', function (Request $request, Response $response) {
    try {
        // Increase execution time for PDF generation
        set_time_limit(120);
        
        $admin = new AdminController();
        if (!$admin->verifyAuth()) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Unauthorized'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

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

        // Generate PDF using Puppeteer
        $pdfGenerator = new PDFGeneratorPuppeteer();
        
        // Generate temporary file path
        $tempPath = __DIR__ . '/../storage/temp/test_' . uniqid() . '.pdf';
        $pdfGenerator->generate($demoData, $tempPath);
        
        // Read the generated PDF
        $pdfContent = file_get_contents($tempPath);
        
        // Clean up temp file
        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        // Return PDF with cache-busting headers
        $response->getBody()->write($pdfContent);
        return $response
            ->withHeader('Content-Type', 'application/pdf')
            ->withHeader('Content-Disposition', 'inline; filename="test_pdf_' . date('YmdHis') . '.pdf"')
            ->withHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->withHeader('Pragma', 'no-cache')
            ->withHeader('Expires', '0');
    } catch (Exception $e) {
        error_log('Test PDF error: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'Error generating test PDF: ' . $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

$app->run();
