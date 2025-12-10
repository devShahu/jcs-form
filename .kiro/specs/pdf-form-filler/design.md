# Design Document

## Overview

The PDF Form Filler system is a modern web application with a decoupled architecture featuring a JavaScript-based Single Page Application (SPA) frontend and a PHP REST API backend. The system provides an elegant, responsive interface for completing the JCS membership form and automatically generates a professionally formatted PDF document.

The architecture follows modern web development best practices with complete separation between the presentation layer (JavaScript SPA with modern UI framework), business logic (PHP REST API), and data persistence (file system with optional database storage). This approach enables a fluid user experience with real-time validation, smooth animations, and progressive form filling.

## Architecture

### High-Level Architecture

```
┌──────────────────────────────────────────┐
│         Modern Web Browser               │
│  ┌────────────────────────────────────┐  │
│  │   JavaScript SPA (React/Vue/Svelte)│  │
│  │   - Multi-step Form UI             │  │
│  │   - Real-time Validation           │  │
│  │   - State Management               │  │
│  │   - Photo Upload & Preview         │  │
│  │   - Progress Indicator             │  │
│  └────────────┬───────────────────────┘  │
└───────────────┼──────────────────────────┘
                │ REST API (JSON)
                │ AJAX/Fetch
                ▼
┌──────────────────────────────────────────┐
│      Web Server (Apache/Nginx)           │
│      Serves: Static Assets + API         │
└────────────────┬─────────────────────────┘
                 │
                 ▼
┌──────────────────────────────────────────┐
│         PHP REST API Layer               │
│  ┌────────────────────────────────────┐  │
│  │  API Router                        │  │
│  │  - POST /api/validate              │  │
│  │  - POST /api/submit                │  │
│  │  - POST /api/upload-photo          │  │
│  │  - GET  /api/download/:id          │  │
│  └────────────┬───────────────────────┘  │
│               │                           │
│  ┌────────────▼───────────────────────┐  │
│  │  Form Validator                    │  │
│  │  - Field validation                │  │
│  │  - Sanitization                    │  │
│  └────────────┬───────────────────────┘  │
│               │                           │
│  ┌────────────▼───────────────────────┐  │
│  │  PDF Generator                     │  │
│  │  - FPDI/TCPDF Library              │  │
│  │  - Template Processing             │  │
│  │  - Bengali Font Rendering          │  │
│  └────────────┬───────────────────────┘  │
│               │                           │
│  ┌────────────▼───────────────────────┐  │
│  │  Storage Manager                   │  │
│  │  - File System                     │  │
│  │  - Database (optional)             │  │
│  └────────────────────────────────────┘  │
└──────────────────────────────────────────┘
```

### Technology Stack

**Frontend (Modern SPA)**:
- **Framework**: React 18+ / Vue 3+ / Svelte (or vanilla JS with modern patterns)
- **Build Tool**: Vite or Webpack for fast development and optimized builds
- **Styling**: Tailwind CSS or modern CSS-in-JS for responsive, beautiful UI
- **State Management**: React Context/Zustand or Vue Pinia for form state
- **Validation**: Zod or Yup for client-side schema validation
- **HTTP Client**: Axios or native Fetch API
- **UI Components**: Headless UI or Radix UI for accessible components
- **Animations**: Framer Motion or CSS animations for smooth transitions
- **Icons**: Heroicons or Lucide for modern iconography

**Backend (PHP REST API)**:
- **PHP**: 8.0+ with modern features (typed properties, attributes, enums)
- **Router**: Slim Framework or custom lightweight router
- **PDF Library**: FPDI (template import) + TCPDF (PDF generation)
- **Validation**: Respect/Validation or custom validator
- **File Upload**: Intervention/Image for photo processing
- **CORS**: Proper CORS headers for API access

**Infrastructure**:
- **Web Server**: Nginx (preferred) or Apache with mod_rewrite
- **Storage**: File system for PDFs, optional MySQL/PostgreSQL for metadata
- **Caching**: Redis or file-based cache for performance
- **Security**: JWT tokens or session-based auth, rate limiting

### Why FPDI + TCPDF?

FPDI (PDF Import) allows us to import the existing template PDF and overlay content, ensuring exact layout matching. TCPDF provides robust PDF generation capabilities with good Unicode support and positioning control.

## Components and Interfaces

### Frontend Components (JavaScript SPA)

#### 1. App Component
**Responsibility**: Main application shell and routing

**Features**:
- Application layout and navigation
- Global state management
- Error boundary handling
- Loading states and transitions

#### 2. Multi-Step Form Component
**Responsibility**: Orchestrate the form filling experience

**Features**:
- Step-by-step form progression (Personal Info → Education → Declaration)
- Progress indicator showing current step
- Navigation between steps with validation
- Form state persistence in browser storage
- Smooth animations between steps

**State Structure**:
```javascript
{
  currentStep: 1,
  formData: {
    personalInfo: {...},
    education: {...},
    declaration: {...}
  },
  validation: {
    errors: {},
    touched: {}
  },
  isSubmitting: false
}
```

#### 3. Personal Information Step Component
**Responsibility**: Capture personal details

**Fields**:
- Name (Bengali and English) with auto-transliteration suggestion
- Father's and Mother's name
- Photo upload with drag-and-drop and preview
- Mobile number with format validation
- NID/Birth registration with date picker
- Present and permanent address with copy functionality
- Political affiliation and last position

**Features**:
- Real-time validation with visual feedback
- Bengali keyboard support
- Photo cropping and preview
- Address autocomplete (optional)

#### 4. Educational Qualification Component
**Responsibility**: Capture education history in table format

**Features**:
- Dynamic table with SSC, HSC, Graduation rows
- Year picker with validation
- Board/University dropdown
- Subject/Group selection
- Institution autocomplete
- Responsive table design for mobile

#### 5. Declaration Component
**Responsibility**: Capture declaration and committee information

**Features**:
- Rich text area for ideology statement
- Checkbox for agreement
- Committee member fields (conditional)
- Digital signature capture (optional)

#### 6. Photo Upload Component
**Responsibility**: Handle photo upload with preview

**Features**:
- Drag and drop interface
- Click to browse
- Image preview with crop tool
- File size and format validation
- Compression before upload
- Progress indicator

#### 7. Success Modal Component
**Responsibility**: Show success message and download options

**Features**:
- Success animation
- Download PDF button
- View PDF in browser option
- Submit another form option
- Share functionality (optional)

### Backend Components (PHP REST API)

#### 1. API Router (`api/index.php`)
**Responsibility**: Route API requests to appropriate handlers

**Endpoints**:
```php
POST   /api/validate        - Validate form data without submission
POST   /api/upload-photo    - Upload and process photo
POST   /api/submit          - Submit complete form and generate PDF
GET    /api/download/:id    - Download generated PDF
GET    /api/config          - Get form configuration and field definitions
```

**Features**:
- RESTful routing
- CORS handling
- Request/response middleware
- Error handling middleware
- Rate limiting

#### 2. Form Validator (`src/FormValidator.php`)
**Responsibility**: Validate and sanitize user input

**Interface**:
```php
class FormValidator {
    public function validate(array $data): ValidationResult
    public function sanitize(array $data): array
    public function getErrors(): array
    public function validateStep(string $step, array $data): ValidationResult
}

class ValidationResult {
    public bool $isValid;
    public array $errors;
    public array $sanitizedData;
}
```

**Validation Rules**:
- Required field checking
- Bengali text validation (Unicode)
- Phone number format (Bangladesh format)
- Date format validation
- NID/Birth registration format
- String length constraints
- XSS prevention through sanitization
- File upload validation (photo)

#### 3. PDF Generator (`src/PDFGenerator.php`)
**Responsibility**: Create filled PDF from HTML template and user data

**Interface**:
```php
class PDFGenerator {
    public function __construct()
    public function setFieldData(array $data): void
    public function generateFromHTML(): string // Returns PDF content
    public function saveTo(string $path): bool
    public function getBase64(): string // For preview in browser
    private function buildHTMLContent(array $data): string
    private function embedPhoto(string $photoData): string
}
```

**Key Features**:
- Renders HTML template with form data using TCPDF's writeHTML()
- Populates template placeholders with actual form values
- Handles Bengali text rendering with Noto Sans Bengali font
- Embeds uploaded photo in base64 format
- Generates two-page PDF matching the provided HTML template
- Maintains proper styling and layout from HTML/CSS
- Supports responsive PDF generation
- Generates high-quality output with proper fonts and formatting

#### 4. Photo Processor (`src/PhotoProcessor.php`)
**Responsibility**: Process and optimize uploaded photos

**Interface**:
```php
class PhotoProcessor {
    public function process(UploadedFile $file): ProcessedPhoto
    public function resize(string $path, int $width, int $height): string
    public function compress(string $path, int $quality): string
    public function validate(UploadedFile $file): ValidationResult
}
```

**Features**:
- Validate file type (JPEG, PNG)
- Validate file size (max 5MB)
- Resize to passport size dimensions
- Compress for optimal file size
- Convert to appropriate format for PDF
- Generate thumbnail for preview

#### 5. Storage Manager (`src/StorageManager.php`)
**Responsibility**: Persist form data and generated PDFs

**Interface**:
```php
class StorageManager {
    public function saveSubmission(array $data, string $pdfPath): string // Returns submission ID
    public function savePDF(string $content, string $filename): string // Returns file path
    public function savePhoto(UploadedFile $file): string // Returns photo path
    public function getSubmission(string $id): array
    public function getPDF(string $id): string // Returns PDF path
    public function listSubmissions(int $page, int $limit): array
}
```

**Storage Structure**:
```
/storage
  /submissions
    /2024
      /01
        submission_20240115_123456.json
        submission_20240115_123456.pdf
  /photos
    /2024
      /01
        photo_20240115_123456.jpg
  /temp
    (temporary uploads, cleaned periodically)
```

#### 6. API Controllers

**SubmitController** (`api/controllers/SubmitController.php`):
```php
class SubmitController {
    public function validate(Request $request): JsonResponse
    public function submit(Request $request): JsonResponse
    public function download(Request $request, string $id): Response
}
```

**UploadController** (`api/controllers/UploadController.php`):
```php
class UploadController {
    public function uploadPhoto(Request $request): JsonResponse
}
```

**ConfigController** (`api/controllers/ConfigController.php`):
```php
class ConfigController {
    public function getFormConfig(): JsonResponse
}
```

**AdminController** (`api/controllers/AdminController.php`):
```php
class AdminController {
    public function login(Request $request): JsonResponse
    public function listSubmissions(Request $request): JsonResponse
    public function getSubmission(Request $request, string $id): JsonResponse
    public function downloadPDF(Request $request, string $id): Response
    public function searchSubmissions(Request $request): JsonResponse
}
```

### Frontend Admin Components

#### 1. Admin Login Component
**Responsibility**: Handle administrator authentication

**Features**:
- Username/password input fields
- Form validation
- Session management
- Redirect to dashboard on success

#### 2. Admin Dashboard Component
**Responsibility**: Display overview and navigation

**Features**:
- Summary statistics (total submissions, recent submissions)
- Quick access to common actions
- Navigation to submissions list

#### 3. Submissions List Component
**Responsibility**: Display all form submissions in a table

**Features**:
- Paginated table of submissions
- Sortable columns (date, name, NID)
- Search and filter functionality
- Action buttons (view, download PDF)
- Responsive table design

#### 4. Submission Detail Component
**Responsibility**: Display complete submission information

**Features**:
- All form fields displayed in organized sections
- Photo preview
- Download PDF button
- Back to list navigation

#### 5. Search and Filter Component
**Responsibility**: Allow filtering of submissions

**Features**:
- Text search (name, NID, mobile)
- Date range picker
- Real-time filtering
- Clear filters button

## Modern UI/UX Design

### Design Principles

1. **Clean and Minimal**: Uncluttered interface focusing user attention on the current task
2. **Progressive Disclosure**: Show only relevant fields for current step
3. **Immediate Feedback**: Real-time validation with helpful error messages
4. **Smooth Animations**: Subtle transitions that guide user attention
5. **Mobile-First**: Fully responsive design that works beautifully on all devices
6. **Accessibility**: WCAG 2.1 AA compliant with keyboard navigation and screen reader support
7. **Bengali Typography**: Beautiful rendering of Bengali text with appropriate fonts

### Visual Design

**Color Palette**:
```css
/* Primary - Based on JCS brand colors */
--primary-600: #DC2626;      /* Red from logo */
--primary-700: #B91C1C;
--primary-500: #EF4444;

/* Neutral */
--gray-50: #F9FAFB;
--gray-100: #F3F4F6;
--gray-200: #E5E7EB;
--gray-300: #D1D5DB;
--gray-600: #4B5563;
--gray-900: #111827;

/* Success/Error */
--success-500: #10B981;
--error-500: #EF4444;
--warning-500: #F59E0B;
```

**Typography**:
```css
/* English Text */
font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;

/* Bengali Text */
font-family: 'Noto Sans Bengali', 'Nikosh', 'SolaimanLipi', sans-serif;

/* Headings */
h1: 2.5rem (40px), font-weight: 700
h2: 2rem (32px), font-weight: 600
h3: 1.5rem (24px), font-weight: 600

/* Body */
body: 1rem (16px), font-weight: 400
small: 0.875rem (14px)
```

**Spacing System**:
- Base unit: 4px
- Scale: 4, 8, 12, 16, 24, 32, 48, 64, 96px

### Layout Structure

**Desktop (1024px+)**:
```
┌─────────────────────────────────────────────┐
│  Header: Logo + Progress Bar                │
├─────────────────────────────────────────────┤
│                                             │
│  ┌─────────────┐  ┌───────────────────┐   │
│  │             │  │                   │   │
│  │  Step       │  │   Form Fields     │   │
│  │  Navigation │  │   (Current Step)  │   │
│  │             │  │                   │   │
│  │  1. Personal│  │   [Input Fields]  │   │
│  │  2. Education│ │                   │   │
│  │  3. Declaration│                   │   │
│  │             │  │                   │   │
│  └─────────────┘  └───────────────────┘   │
│                                             │
│              [Back]  [Next/Submit]          │
└─────────────────────────────────────────────┘
```

**Mobile (< 768px)**:
```
┌─────────────────────┐
│  Logo + Step 1/3    │
├─────────────────────┤
│                     │
│   Form Fields       │
│   (Current Step)    │
│                     │
│   [Input Fields]    │
│                     │
│                     │
│                     │
│                     │
│  [Back]  [Next]     │
└─────────────────────┘
```

### Interactive Elements

**Form Inputs**:
- Floating labels that animate on focus
- Clear visual states: default, focus, error, success, disabled
- Helper text below inputs
- Character count for text areas
- Format hints (e.g., "01XXXXXXXXX" for phone)

**Buttons**:
- Primary: Solid background with hover/active states
- Secondary: Outline style
- Loading state with spinner
- Disabled state with reduced opacity

**Progress Indicator**:
- Visual step indicator at top
- Shows completed, current, and upcoming steps
- Clickable to navigate to completed steps
- Animated transitions between steps

**Photo Upload**:
- Drag-and-drop zone with dashed border
- Preview with edit/remove options
- Upload progress bar
- Crop tool overlay

**Validation**:
- Inline error messages below fields
- Red border for error state
- Green checkmark for valid fields
- Summary of errors at top of form

### Animations

**Page Transitions**:
```css
/* Fade and slide between steps */
.step-enter {
  opacity: 0;
  transform: translateX(20px);
}
.step-enter-active {
  opacity: 1;
  transform: translateX(0);
  transition: all 300ms ease-out;
}
```

**Micro-interactions**:
- Button hover: Scale 1.02, shadow increase
- Input focus: Border color transition, subtle glow
- Success: Checkmark animation
- Error shake: Subtle horizontal shake
- Loading: Smooth spinner rotation

### Responsive Breakpoints

```css
/* Mobile */
@media (max-width: 640px) { ... }

/* Tablet */
@media (min-width: 641px) and (max-width: 1024px) { ... }

/* Desktop */
@media (min-width: 1025px) { ... }
```

## Data Models

### Form Submission Data Structure

```php
class FormSubmission {
    public string $submissionId;
    public DateTime $submittedAt;
    public array $formData;
    public string $pdfPath;
    public string $ipAddress;
    public string $userAgent;
}
```

### Form Field Configuration

```php
class FormField {
    public string $name;
    public string $label;
    public string $type; // text, email, tel, date, select, textarea
    public bool $required;
    public ?string $pattern; // Regex for validation
    public ?array $options; // For select fields
    public array $pdfCoordinates; // [x, y, width, height]
}
```

### PDF Field Mapping

```php
// Configuration mapping form fields to PDF positions
// Note: Coordinates need to be measured from the actual PDF template
$fieldMapping = [
    // Page 1 - Personal Information
    'form_no' => ['x' => 50, 'y' => 750, 'width' => 150, 'height' => 10, 'font' => 'nikosh', 'size' => 10],
    'name_bangla' => ['x' => 50, 'y' => 730, 'width' => 400, 'height' => 10, 'font' => 'nikosh', 'size' => 10],
    'name_english' => ['x' => 50, 'y' => 710, 'width' => 400, 'height' => 10, 'font' => 'helvetica', 'size' => 10],
    'father_name' => ['x' => 50, 'y' => 690, 'width' => 400, 'height' => 10, 'font' => 'nikosh', 'size' => 10],
    'mother_name' => ['x' => 50, 'y' => 670, 'width' => 400, 'height' => 10, 'font' => 'nikosh', 'size' => 10],
    'photo' => ['x' => 500, 'y' => 650, 'width' => 80, 'height' => 100], // Image position
    'mobile_number' => ['x' => 50, 'y' => 650, 'width' => 200, 'height' => 10, 'font' => 'helvetica', 'size' => 10],
    'nid_birth_reg' => ['x' => 50, 'y' => 630, 'width' => 200, 'height' => 10, 'font' => 'helvetica', 'size' => 10],
    'birth_date' => ['x' => 300, 'y' => 630, 'width' => 150, 'height' => 10, 'font' => 'helvetica', 'size' => 10],
    'present_address' => ['x' => 50, 'y' => 610, 'width' => 400, 'height' => 20, 'font' => 'nikosh', 'size' => 10],
    'permanent_address' => ['x' => 50, 'y' => 580, 'width' => 400, 'height' => 20, 'font' => 'nikosh', 'size' => 10],
    'political_affiliation' => ['x' => 50, 'y' => 550, 'width' => 500, 'height' => 10, 'font' => 'nikosh', 'size' => 10],
    'last_position' => ['x' => 50, 'y' => 530, 'width' => 500, 'height' => 10, 'font' => 'nikosh', 'size' => 10],
    
    // Educational Qualification Table
    'ssc_year' => ['x' => 150, 'y' => 480, 'width' => 60, 'height' => 10, 'font' => 'helvetica', 'size' => 9],
    'ssc_board' => ['x' => 220, 'y' => 480, 'width' => 80, 'height' => 10, 'font' => 'nikosh', 'size' => 9],
    'ssc_group' => ['x' => 310, 'y' => 480, 'width' => 100, 'height' => 10, 'font' => 'nikosh', 'size' => 9],
    'ssc_institution' => ['x' => 420, 'y' => 480, 'width' => 200, 'height' => 10, 'font' => 'nikosh', 'size' => 9],
    
    'hsc_year' => ['x' => 150, 'y' => 460, 'width' => 60, 'height' => 10, 'font' => 'helvetica', 'size' => 9],
    'hsc_board' => ['x' => 220, 'y' => 460, 'width' => 80, 'height' => 10, 'font' => 'nikosh', 'size' => 9],
    'hsc_group' => ['x' => 310, 'y' => 460, 'width' => 100, 'height' => 10, 'font' => 'nikosh', 'size' => 9],
    'hsc_institution' => ['x' => 420, 'y' => 460, 'width' => 200, 'height' => 10, 'font' => 'nikosh', 'size' => 9],
    
    'graduation_year' => ['x' => 150, 'y' => 440, 'width' => 60, 'height' => 10, 'font' => 'helvetica', 'size' => 9],
    'graduation_board' => ['x' => 220, 'y' => 440, 'width' => 80, 'height' => 10, 'font' => 'nikosh', 'size' => 9],
    'graduation_subject' => ['x' => 310, 'y' => 440, 'width' => 100, 'height' => 10, 'font' => 'nikosh', 'size' => 9],
    'graduation_institution' => ['x' => 420, 'y' => 440, 'width' => 200, 'height' => 10, 'font' => 'nikosh', 'size' => 9],
    
    // Page 2 - Declaration
    'responsibility_ideology' => ['x' => 50, 'y' => 700, 'width' => 500, 'height' => 40, 'font' => 'nikosh', 'size' => 10],
    'declaration_name' => ['x' => 80, 'y' => 600, 'width' => 200, 'height' => 10, 'font' => 'nikosh', 'size' => 10],
    
    // Set Committee Section
    'committee_member_name' => ['x' => 50, 'y' => 400, 'width' => 200, 'height' => 10, 'font' => 'nikosh', 'size' => 10],
    'committee_member_position' => ['x' => 300, 'y' => 400, 'width' => 150, 'height' => 10, 'font' => 'nikosh', 'size' => 10],
    'committee_member_comments' => ['x' => 50, 'y' => 380, 'width' => 500, 'height' => 30, 'font' => 'nikosh', 'size' => 10],
    'recommended_position' => ['x' => 50, 'y' => 340, 'width' => 200, 'height' => 10, 'font' => 'nikosh', 'size' => 10],
];
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*


### Property Reflection

After analyzing all acceptance criteria, I identified several redundant properties that can be consolidated:
- Validation properties (2.1-2.4) test similar validation behavior and can be combined
- PDF positioning properties (4.2, 8.2) both verify text placement
- Error handling properties (7.1, 7.2, 7.4) cover overlapping error scenarios
- Storage properties (6.1, 6.3, 6.4) test different storage aspects that can be unified
- Download properties (5.1, 5.3) both verify HTTP response configuration

### Correctness Properties

Property 1: Form field completeness
*For any* form rendering, all fields defined in the field configuration should be present in the generated HTML
**Validates: Requirements 1.1**

Property 2: Field ordering consistency
*For any* form configuration, fields should appear in the HTML DOM in the same order as defined in the configuration
**Validates: Requirements 1.2**

Property 3: Label association
*For any* input field in the form, there should exist an associated label element with matching for/id attributes
**Validates: Requirements 1.3**

Property 4: Input type correctness
*For any* form field, the HTML input type attribute should match the field's intended data type (email fields use type="email", date fields use type="date", etc.)
**Validates: Requirements 1.4**

Property 5: Required field validation
*For any* form submission with one or more empty required fields, validation should fail and return specific error messages for each missing field
**Validates: Requirements 2.1**

Property 6: Format validation
*For any* form field with a format constraint (email, phone, date), submitting invalid format data should fail validation and return a format-specific error message
**Validates: Requirements 2.2, 2.3**

Property 7: Valid data acceptance
*For any* form submission where all fields pass their validation rules, the validation result should indicate success and allow processing to continue
**Validates: Requirements 2.4**

Property 8: Data preservation on error
*For any* form submission that fails validation, the returned form should contain all originally submitted values unchanged
**Validates: Requirements 2.5**

Property 9: Input sanitization
*For any* form submission containing potentially malicious input (XSS attempts, SQL injection patterns), the sanitized output should have dangerous characters properly escaped or removed
**Validates: Requirements 3.2**

Property 10: PDF generation trigger
*For any* successful form submission, the system should invoke the PDF generation process exactly once
**Validates: Requirements 3.5**

Property 11: Text positioning accuracy
*For any* form field with defined PDF coordinates, the generated PDF should contain that field's text at the specified x,y position with the specified dimensions
**Validates: Requirements 4.2, 8.2**

Property 12: PDF dimensions preservation
*For any* generated PDF, the page size (width and height) should match the template PDF's dimensions exactly
**Validates: Requirements 4.4**

Property 13: PDF round-trip validity
*For any* generated PDF content, the PDF should be readable by standard PDF libraries without errors and contain extractable text
**Validates: Requirements 5.5**

Property 14: Download headers correctness
*For any* successful PDF generation, the HTTP response should include Content-Type: application/pdf and Content-Disposition headers with a properly formatted filename
**Validates: Requirements 5.1, 5.3**

Property 15: Filename generation
*For any* PDF download, the filename should follow the pattern "jcs_membership_YYYYMMDD_HHMMSS.pdf" where the timestamp reflects the submission time
**Validates: Requirements 5.2**

Property 16: Submission persistence
*For any* successful form submission, the system should create a storage record containing the form data, timestamp, and PDF file path
**Validates: Requirements 6.1, 6.3, 6.4**

Property 17: Sensitive data encryption
*For any* stored submission containing sensitive fields (marked as sensitive in configuration), those field values should be encrypted in storage and not readable as plaintext
**Validates: Requirements 6.2**

Property 18: Directory structure organization
*For any* saved PDF file, the file path should follow the structure /storage/submissions/YYYY/MM/ where YYYY and MM correspond to the submission date
**Validates: Requirements 6.4**

Property 19: Error logging
*For any* exception or error during PDF generation or storage, an error log entry should be created with timestamp, error type, and error message
**Validates: Requirements 7.1**

Property 20: Safe error messages
*For any* error displayed to users, the error message should not contain system paths, database details, or stack traces
**Validates: Requirements 7.2**

Property 21: Data integrity on failure
*For any* database operation failure during submission, either all data should be saved or none should be saved (atomic transaction)
**Validates: Requirements 7.4**

Property 22: Template import success
*For any* PDF generation request, the system should successfully import the template PDF and use it as the base for the generated document
**Validates: Requirements 8.3**

Property 23: Special character handling
*For any* form submission containing Unicode characters, accented characters, or special symbols, those characters should appear correctly in the generated PDF
**Validates: Requirements 8.4**

Property 24: Responsive layout adaptation
*For any* viewport width, the form should apply appropriate CSS styles to maintain usability (single column on mobile, multi-column on desktop)
**Validates: Requirements 9.2**

Property 25: Keyboard navigation
*For any* interactive form element, the element should be reachable and operable using only keyboard input (Tab, Enter, Space, Arrow keys)
**Validates: Requirements 9.3**

Property 26: Accessibility markup
*For any* form field, the HTML should include appropriate ARIA attributes (aria-label, aria-required, aria-invalid) and use semantic HTML elements
**Validates: Requirements 9.4**

Property 27: HTML template data population
*For any* form submission, all template placeholders in the HTML should be replaced with corresponding form data values
**Validates: Requirements 4.2**

Property 28: Photo embedding in PDF
*For any* form submission with an uploaded photo, the generated PDF should contain the photo embedded in base64 format at the designated location
**Validates: Requirements 4.6**

Property 29: Admin authentication
*For any* admin login attempt with valid credentials, the system should grant access and create a session
**Validates: Requirements 10.2**

Property 30: Submission listing
*For any* admin dashboard request, the system should return a list of all submissions ordered by submission date descending
**Validates: Requirements 10.3**

Property 31: Search functionality
*For any* search query, the system should return only submissions where the search term matches name, NID, or mobile number fields
**Validates: Requirements 11.1**

Property 32: Date range filtering
*For any* date range filter, the system should return only submissions with submission dates within the specified range (inclusive)
**Validates: Requirements 11.2**

## Error Handling

### Validation Errors

**Strategy**: Fail fast with clear feedback
- Client-side validation provides immediate feedback
- Server-side validation is authoritative
- Return structured error objects with field-specific messages
- Preserve user input on validation failure

**Error Response Format**:
```php
{
    "success": false,
    "errors": {
        "email": "Please enter a valid email address",
        "phone": "Phone number must be in format: (XXX) XXX-XXXX"
    },
    "data": { /* preserved user input */ }
}
```

### PDF Generation Errors

**Common Scenarios**:
1. Template file not found
2. Insufficient permissions to write files
3. PDF library errors (memory, corruption)
4. Invalid coordinates or formatting

**Handling**:
- Log detailed error information
- Display user-friendly message: "We encountered an issue generating your PDF. Please try again or contact support."
- Provide fallback: Store form data even if PDF generation fails
- Implement retry mechanism for transient failures

### Storage Errors

**Scenarios**:
- Disk space full
- Permission denied
- Database connection failure

**Handling**:
- Implement graceful degradation (store to alternate location)
- Queue failed operations for retry
- Alert administrators of storage issues
- Ensure data is not lost

### Security Errors

**Scenarios**:
- CSRF token mismatch
- Suspicious input patterns
- Rate limiting exceeded

**Handling**:
- Reject request immediately
- Log security events
- Return generic error message (don't reveal security details)
- Implement progressive delays for repeated failures

## Testing Strategy

### Unit Testing

The system will use PHPUnit for unit testing. Unit tests will focus on:

**Validation Logic**:
- Test specific validation rules with known valid/invalid inputs
- Test edge cases (empty strings, very long strings, special characters)
- Test sanitization functions with XSS payloads

**PDF Generation**:
- Test coordinate calculation functions
- Test filename generation with various timestamps
- Test field mapping logic

**Storage Operations**:
- Test directory creation and file writing
- Test data serialization/deserialization
- Test encryption/decryption functions

**Example Unit Tests**:
```php
// Test email validation with specific examples
testValidEmailAccepted()
testInvalidEmailRejected()
testEmptyEmailWhenRequired()

// Test filename generation
testFilenameIncludesTimestamp()
testFilenameHasCorrectExtension()

// Test coordinate mapping
testFieldMapsToCorrectPosition()
```

### Property-Based Testing

The system will use PHPUnit with a property-based testing extension (such as Eris or php-quickcheck) for property-based testing. Property tests will verify universal properties across many randomly generated inputs.

**Configuration**: Each property-based test should run a minimum of 100 iterations to ensure thorough coverage of the input space.

**Tagging**: Each property-based test must include a comment tag in this exact format:
```php
/**
 * Feature: pdf-form-filler, Property {number}: {property description}
 */
```

**Property Test Coverage**:

Each correctness property listed above will be implemented as a single property-based test that:
- Generates random valid/invalid inputs appropriate to the property
- Executes the system behavior
- Asserts the property holds for all generated inputs

**Example Property Tests**:
```php
/**
 * Feature: pdf-form-filler, Property 5: Required field validation
 */
testRequiredFieldValidationProperty() {
    // Generate random form configurations with required fields
    // Generate submissions with random combinations of missing fields
    // Assert: validation fails and returns errors for missing fields
}

/**
 * Feature: pdf-form-filler, Property 9: Input sanitization
 */
testInputSanitizationProperty() {
    // Generate random XSS payloads and injection attempts
    // Submit through sanitization
    // Assert: output has dangerous patterns escaped/removed
}

/**
 * Feature: pdf-form-filler, Property 13: PDF round-trip validity
 */
testPDFRoundTripProperty() {
    // Generate random valid form data
    // Generate PDF
    // Assert: PDF can be opened and text extracted without errors
}
```

### Integration Testing

Integration tests will verify the complete workflow:
- Form submission → Validation → PDF Generation → Storage → Download
- Test with realistic form data
- Verify PDF output matches expectations
- Confirm files are stored correctly

### Manual Testing Checklist

While not automated, these manual tests ensure quality:
- Visual comparison of generated PDF with template
- Cross-browser form testing (Chrome, Firefox, Safari, Edge)
- Mobile device testing (iOS, Android)
- Accessibility testing with screen readers
- Performance testing with large form submissions

## Implementation Notes

### Bengali Font Support

**Critical Requirement**: The system must properly render Bengali (Bangla) Unicode text in both the HTML form and the generated PDF.

**Font Selection**:
- **For PDF**: Use "Noto Sans Bengali" font loaded from Google Fonts CDN
- **For HTML**: Use "Noto Sans Bengali" from Google Fonts
- Font is loaded via CDN in the HTML template for consistent rendering

**TCPDF HTML-based Configuration**:
```php
// TCPDF automatically handles fonts when using writeHTML()
// The HTML template includes Google Fonts link
$pdf = new TCPDF();
$pdf->SetFont('dejavusans', '', 10); // Fallback font
$pdf->writeHTML($htmlContent, true, false, true, false, '');
```

**Character Encoding**:
- All PHP files must be saved with UTF-8 encoding
- Database (if used) must use utf8mb4 charset
- HTML template includes `<meta charset="UTF-8">`
- HTTP headers must specify UTF-8: `header('Content-Type: text/html; charset=utf-8');`

### HTML Template-Based PDF Generation

**Approach**: Instead of coordinate-based positioning, the system uses an HTML template that is rendered to PDF.

**Template Structure**:
- Complete HTML document with inline CSS
- Google Fonts CDN for Noto Sans Bengali
- Tailwind CSS CDN for styling
- Two-page layout matching the original form design
- Placeholders for dynamic data (e.g., `{{name_bangla}}`, `{{mobile_number}}`)

**Data Population**:
```php
private function buildHTMLContent(array $data): string
{
    $html = file_get_contents(__DIR__ . '/../../templates/pdf_template.html');
    
    // Replace placeholders with actual data
    foreach ($data as $key => $value) {
        $html = str_replace('{{' . $key . '}}', htmlspecialchars($value), $html);
    }
    
    // Handle photo embedding
    if (!empty($data['photo'])) {
        $photoBase64 = $this->embedPhoto($data['photo']);
        $html = str_replace('{{photo_src}}', $photoBase64, $html);
    }
    
    return $html;
}
```

**Benefits**:
- No need for precise coordinate mapping
- Easier to maintain and update layout
- Better handling of dynamic content and text wrapping
- Consistent styling between preview and PDF
- Simpler implementation

### PDF Coordinate Mapping

The most critical aspect is accurately mapping form fields to PDF coordinates. This requires:

1. **Template Analysis**: Open the template PDF and identify exact positions of each field
2. **Coordinate System**: PDF coordinates start at bottom-left (0,0), not top-left
3. **Measurement Tool**: Use a PDF editor or library to measure field positions
4. **Configuration File**: Store mappings in a separate configuration file for easy adjustment

**Example Configuration**:
```php
// config/pdf_fields.php
return [
    'full_name' => [
        'x' => 50,
        'y' => 750, // Near top of page (assuming 792pt height)
        'width' => 200,
        'height' => 12,
        'font_size' => 10
    ],
    'email' => [
        'x' => 50,
        'y' => 730,
        'width' => 200,
        'height' => 12,
        'font_size' => 10
    ],
    // ... more fields
];
```

### Security Considerations

1. **Input Validation**: Never trust user input
2. **CSRF Protection**: Implement token-based CSRF protection
3. **File Upload**: If allowing file uploads (signatures, photos), validate file types and sizes
4. **SQL Injection**: Use prepared statements if using database
5. **XSS Prevention**: Escape all output, sanitize all input
6. **Path Traversal**: Validate and sanitize filenames
7. **Rate Limiting**: Prevent abuse through rate limiting
8. **HTTPS**: Enforce HTTPS for all form submissions

### Performance Optimization

1. **Caching**: Cache the template PDF in memory for repeated use
2. **Async Processing**: For high-volume scenarios, queue PDF generation
3. **Resource Limits**: Set appropriate PHP memory and execution time limits
4. **File Cleanup**: Implement periodic cleanup of old generated PDFs
5. **Database Indexing**: Index timestamp and submission ID fields

### Deployment Considerations

**Server Requirements**:
- PHP 7.4+ (preferably 8.x)
- GD or Imagick extension (for PDF library)
- Write permissions on storage directory
- Sufficient disk space for PDF storage
- SSL certificate for HTTPS

**Environment Configuration**:
```php
// .env or config file
PDF_TEMPLATE_PATH=/path/to/template.pdf
STORAGE_PATH=/path/to/storage
MAX_UPLOAD_SIZE=10M
ENABLE_ENCRYPTION=true
LOG_LEVEL=error
```

**Directory Permissions**:
```
/storage/submissions - 755 (writable by web server)
/logs - 755 (writable by web server)
/config - 644 (readable only)
```

## Future Enhancements

Potential improvements for future iterations:

1. **Admin Dashboard**: View and manage submissions
2. **Email Notifications**: Send PDF to user's email
3. **Digital Signatures**: Capture and embed signatures
4. **Multi-page Forms**: Split long forms across multiple pages
5. **Draft Saving**: Allow users to save and resume later
6. **Batch Processing**: Generate multiple PDFs at once
7. **API Endpoint**: Provide REST API for programmatic access
8. **Internationalization**: Support multiple languages
9. **Payment Integration**: If membership requires payment
10. **Analytics**: Track form completion rates and abandonment
