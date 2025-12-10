# Implementation Plan

- [x] 1. Set up project structure and dependencies





  - Create directory structure:
    - `/api` - PHP REST API
    - `/src` - Frontend source code
    - `/public` - Built frontend assets
    - `/storage/submissions` - Generated PDFs
    - `/storage/photos` - Uploaded photos
    - `/storage/temp` - Temporary files
    - `/config` - Configuration files
    - `/fonts` - Bengali fonts
  - Backend: Create composer.json with dependencies (FPDI, TCPDF, Slim Framework, Intervention/Image)
  - Frontend: Create package.json with dependencies (React/Vue, Vite, Tailwind CSS, Axios, Zod)
  - Download Bengali fonts (Nikosh.ttf, Noto Sans Bengali) to /fonts directory
  - Create .env configuration file for API paths and settings
  - Set up Git ignore for node_modules, vendor, storage
  - _Requirements: 8.1_

- [x] 2. Create form field configuration


  - Create config/form_fields.php with all JCS form field definitions including:
    - Personal info: form_no, name_bangla, name_english, father_name, mother_name, photo
    - Contact: mobile_number, nid_birth_reg, birth_date
    - Address: present_address, permanent_address
    - Background: political_affiliation, last_position
    - Education table: SSC, HSC, Graduation rows with year, board, group/subject, institution
    - Declaration: responsibility_ideology, declaration_name
    - Committee section: committee_member_name, committee_member_position, committee_member_comments, recommended_position
  - Create config/pdf_coordinates.php with exact PDF position mappings for each field
  - Configure Bengali font (Nikosh/SolaimanLipi) paths for PDF rendering
  - Define field types: text (Bengali/English), textarea, date, file upload (photo)
  - Set validation rules: required fields, phone format, date format
  - _Requirements: 1.1, 1.4_

- [x] 3. Implement FormValidator class


  - Create src/FormValidator.php with validation logic
  - Implement validate() method to check all validation rules
  - Implement sanitize() method to clean user input and prevent XSS
  - Implement getErrors() method to return validation error messages
  - Create ValidationResult class to hold validation results
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 3.2_

- [ ]* 3.1 Write property test for required field validation
  - **Property 5: Required field validation**
  - **Validates: Requirements 2.1**

- [ ]* 3.2 Write property test for format validation
  - **Property 6: Format validation**
  - **Validates: Requirements 2.2, 2.3**

- [ ]* 3.3 Write property test for valid data acceptance
  - **Property 7: Valid data acceptance**
  - **Validates: Requirements 2.4**

- [ ]* 3.4 Write property test for input sanitization
  - **Property 9: Input sanitization**
  - **Validates: Requirements 3.2**

- [x] 4. Build modern frontend application


  - Initialize Vite project with React/Vue/Svelte
  - Set up Tailwind CSS with custom configuration (colors, fonts, spacing)
  - Configure Bengali font loading (Noto Sans Bengali from Google Fonts)
  - Create main App component with routing and state management
  - Implement responsive layout with header and progress indicator
  - Set up Axios for API communication with interceptors
  - Configure Zod schemas for client-side validation
  - Create utility functions for form state management
  - Set up error boundary and loading states
  - _Requirements: 1.1, 9.1, 9.2_

- [x] 5. Create multi-step form components

  - Build FormWizard component to orchestrate steps
  - Implement step navigation with progress bar
  - Create PersonalInfoStep component with all personal fields
  - Create EducationStep component with dynamic table
  - Create DeclarationStep component with text area and agreement
  - Implement step validation before allowing navigation
  - Add smooth animations between steps using Framer Motion or CSS
  - Persist form state to localStorage for draft saving
  - _Requirements: 1.1, 1.2, 1.3, 1.4_

- [x] 6. Implement photo upload component

  - Create PhotoUpload component with drag-and-drop zone
  - Implement file selection via click
  - Add image preview with crop functionality
  - Show upload progress indicator
  - Validate file type (JPEG, PNG) and size (max 5MB)
  - Compress image client-side before upload
  - Call /api/upload-photo endpoint
  - Handle upload errors gracefully
  - _Requirements: 1.1_

- [x] 7. Add real-time validation and feedback

  - Implement field-level validation with Zod schemas
  - Show inline error messages below fields
  - Add visual indicators (red border for errors, green checkmark for valid)
  - Implement debounced validation for text inputs
  - Add format hints and helper text
  - Show character count for text areas
  - Validate Bengali text input
  - Call /api/validate endpoint for server-side validation
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5_

- [ ]* 4.1 Write property test for form field completeness
  - **Property 1: Form field completeness**
  - **Validates: Requirements 1.1**

- [ ]* 4.2 Write property test for field ordering consistency
  - **Property 2: Field ordering consistency**
  - **Validates: Requirements 1.2**

- [ ]* 4.3 Write property test for label association
  - **Property 3: Label association**
  - **Validates: Requirements 1.3**

- [ ]* 4.4 Write property test for input type correctness
  - **Property 4: Input type correctness**
  - **Validates: Requirements 1.4**

- [ ]* 4.5 Write property test for responsive layout adaptation
  - **Property 24: Responsive layout adaptation**
  - **Validates: Requirements 9.2**

- [ ]* 4.6 Write property test for accessibility markup
  - **Property 26: Accessibility markup**
  - **Validates: Requirements 9.4**

- [x] 8. Build PHP REST API endpoints

  - Create api/index.php as main entry point with routing
  - Implement CORS middleware for frontend access
  - Create POST /api/validate endpoint for step validation
  - Create POST /api/upload-photo endpoint for photo uploads
  - Create POST /api/submit endpoint for form submission
  - Create GET /api/download/:id endpoint for PDF download
  - Create GET /api/config endpoint for form configuration
  - Implement request/response JSON handling
  - Add error handling middleware
  - Implement rate limiting middleware
  - _Requirements: 3.1, 3.3, 3.4, 3.5_

- [x] 9. Implement PhotoProcessor class

  - Create api/src/PhotoProcessor.php for image handling
  - Use Intervention/Image library for processing
  - Implement validate() for file type and size checks
  - Implement resize() to passport photo dimensions
  - Implement compress() for optimal file size
  - Implement process() to handle complete workflow
  - Save processed photos to /storage/photos
  - Return photo path and metadata
  - _Requirements: 1.1_

- [x] 10. Create HTML PDF template





  - Create templates/pdf_template.html with the provided HTML structure
  - Include Google Fonts CDN link for Noto Sans Bengali
  - Include Tailwind CSS CDN for styling
  - Add placeholders for all form fields (e.g., {{name_bangla}}, {{mobile_number}})
  - Create two-page layout matching the original design
  - Add placeholder for photo ({{photo_src}})
  - Ensure proper styling for print/PDF rendering
  - Test template renders correctly in browser
  - _Requirements: 4.1, 4.3, 4.4, 4.5_



- [x] 10.1 Implement HTML-based PDFGenerator class



  - Update api/src/PDFGenerator.php to use HTML template approach
  - Remove coordinate-based positioning code
  - Implement buildHTMLContent() to load and populate template
  - Implement placeholder replacement logic for all form fields
  - Implement embedPhoto() to convert photo to base64 data URI
  - Configure TCPDF to use writeHTML() method
  - Handle Bengali text rendering (TCPDF handles this automatically with HTML)
  - Implement generateFromHTML() to create PDF from populated HTML
  - Implement saveTo() to save PDF to file system
  - Implement getBase64() for browser preview
  - Test with sample form data
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.6, 4.7, 5.1, 5.2, 5.3, 8.1, 8.2, 8.3, 8.4_

- [ ]* 10.2 Write property test for HTML template data population
  - **Property 27: HTML template data population**
  - **Validates: Requirements 4.2**

- [ ]* 10.3 Write property test for photo embedding in PDF
  - **Property 28: Photo embedding in PDF**
  - **Validates: Requirements 4.6**

- [ ]* 10.4 Write property test for PDF dimensions preservation
  - **Property 12: PDF dimensions preservation**
  - **Validates: Requirements 4.4**

- [ ]* 10.5 Write property test for PDF round-trip validity
  - **Property 13: PDF round-trip validity**
  - **Validates: Requirements 5.5**

- [ ]* 10.6 Write property test for special character handling
  - **Property 23: Special character handling**
  - **Validates: Requirements 8.4**

- [x] 11. Implement StorageManager class

  - Create api/src/StorageManager.php for data persistence
  - Implement saveSubmission() to store form data as JSON with timestamp
  - Implement savePDF() to save generated PDF in organized directory structure (YYYY/MM/)
  - Implement savePhoto() to store uploaded photos
  - Implement encryption for sensitive fields
  - Implement getSubmission() to retrieve stored data by ID
  - Implement getPDF() to retrieve PDF path
  - Implement listSubmissions() with pagination
  - _Requirements: 6.1, 6.2, 6.3, 6.4_

- [ ]* 6.1 Write property test for submission persistence
  - **Property 16: Submission persistence**
  - **Validates: Requirements 6.1, 6.3, 6.4**

- [ ]* 6.2 Write property test for sensitive data encryption
  - **Property 17: Sensitive data encryption**
  - **Validates: Requirements 6.2**

- [ ]* 6.3 Write property test for directory structure organization
  - **Property 18: Directory structure organization**
  - **Validates: Requirements 6.4**

- [x] 12. Create API controllers

  - Create api/controllers/SubmitController.php
  - Implement validate() method for step validation
  - Implement submit() method for complete form submission
  - Implement download() method for PDF retrieval
  - Create api/controllers/UploadController.php
  - Implement uploadPhoto() method
  - Create api/controllers/ConfigController.php
  - Implement getFormConfig() to return field definitions
  - Coordinate between Validator, PDFGenerator, and StorageManager
  - Return proper JSON responses with status codes
  - _Requirements: 2.5, 3.1, 3.3, 3.4, 3.5, 5.1, 5.2, 5.3, 5.5_

- [ ]* 7.1 Write property test for data preservation on error
  - **Property 8: Data preservation on error**
  - **Validates: Requirements 2.5**

- [ ]* 7.2 Write property test for PDF generation trigger
  - **Property 10: PDF generation trigger**
  - **Validates: Requirements 3.5**

- [ ]* 7.3 Write property test for download headers correctness
  - **Property 14: Download headers correctness**
  - **Validates: Requirements 5.1, 5.3**

- [ ]* 7.4 Write property test for filename generation
  - **Property 15: Filename generation**
  - **Validates: Requirements 5.2**

- [x] 13. Implement form submission flow in frontend

  - Create handleSubmit function in FormWizard component
  - Validate all steps before submission
  - Show loading state with progress indicator
  - Call POST /api/submit with form data
  - Handle success: Show success modal with download button
  - Handle errors: Display error messages, allow retry
  - Implement PDF download functionality
  - Add option to view PDF in browser
  - Clear form state after successful submission
  - _Requirements: 3.3, 3.4, 3.5, 5.1, 5.2, 5.3, 5.4, 5.5_

- [x] 14. Add success and error UI components


  - Create SuccessModal component with celebration animation
  - Add download PDF button with icon
  - Add view PDF in browser option
  - Add submit another form button
  - Create ErrorAlert component for error display
  - Implement toast notifications for quick feedback
  - Add retry functionality for failed operations
  - _Requirements: 3.4, 5.4, 7.3, 7.5_

- [ ] 15. Implement error handling and logging
  - Create api/src/ErrorHandler.php for centralized error handling
  - Implement error logging to file system
  - Implement safe error message generation (no system details exposed)
  - Add try-catch blocks in PDFGenerator for PDF library errors
  - Add try-catch blocks in StorageManager for file system errors
  - Add try-catch blocks in PhotoProcessor for image errors
  - Implement transaction handling for atomic operations
  - Return appropriate HTTP status codes
  - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5_

- [ ]* 8.1 Write property test for error logging
  - **Property 19: Error logging**
  - **Validates: Requirements 7.1**

- [ ]* 8.2 Write property test for safe error messages
  - **Property 20: Safe error messages**
  - **Validates: Requirements 7.2**

- [ ]* 8.3 Write property test for data integrity on failure
  - **Property 21: Data integrity on failure**
  - **Validates: Requirements 7.4**

- [ ] 16. Add security features
  - Implement CSRF token generation and validation in API
  - Add rate limiting middleware to API endpoints
  - Implement input sanitization in FormValidator
  - Add file path validation to prevent path traversal
  - Validate file uploads (type, size, content)
  - Create .htaccess rules to protect sensitive directories
  - Implement secure headers (CORS, CSP, X-Frame-Options)
  - Add request size limits
  - _Requirements: 3.1, 3.2_

- [ ] 17. Implement accessibility features in frontend
  - Add ARIA labels to all form fields
  - Ensure proper semantic HTML structure (form, fieldset, legend)
  - Add keyboard navigation support (tab order, enter to submit, escape to close)
  - Implement focus management between steps
  - Add aria-required and aria-invalid attributes dynamically
  - Ensure color contrast meets WCAG AA standards
  - Add skip links for keyboard users
  - Test with keyboard-only navigation
  - Test with screen readers (NVDA, JAWS)
  - _Requirements: 9.3, 9.4_

- [ ] 18. Style and polish the UI
  - Implement custom Tailwind configuration with JCS brand colors
  - Create reusable UI components (Button, Input, Select, TextArea)
  - Add smooth animations and transitions
  - Implement loading skeletons for async operations
  - Add micro-interactions (hover effects, focus states)
  - Ensure responsive design works on all breakpoints
  - Add empty states and helpful messages
  - Implement dark mode support (optional)
  - Polish typography and spacing
  - _Requirements: 9.1, 9.2_

- [ ]* 10.1 Write property test for keyboard navigation
  - **Property 25: Keyboard navigation**
  - **Validates: Requirements 9.3**

- [ ] 19. Build and optimize frontend for production
  - Configure Vite build settings for optimization
  - Implement code splitting for better performance
  - Optimize images and assets
  - Generate production build
  - Test production build locally
  - Configure web server to serve SPA correctly
  - _Requirements: All_

- [ ] 20. Create installation and configuration documentation
  - Create README.md with setup instructions for both frontend and backend
  - Document required PHP extensions and versions
  - Document Node.js version requirements
  - Document directory permissions needed
  - Create example .env configuration file
  - Document how to configure PDF field coordinates
  - Add API documentation with endpoint descriptions
  - Create deployment guide
  - _Requirements: All_

- [ ] 21. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 22. Integration testing and refinement
  - Test complete workflow: form display → step navigation → validation → photo upload → submission → PDF generation → download
  - Verify generated PDF matches template layout with Bengali text
  - Test with various form data including edge cases (special characters, long text)
  - Verify error handling works correctly at each step
  - Test file storage and retrieval
  - Test on multiple browsers (Chrome, Firefox, Safari, Edge)
  - Test on mobile devices (iOS, Android)
  - Test with slow network conditions
  - Verify accessibility with keyboard and screen readers
  - _Requirements: All_


- [x] 24. Implement admin authentication



  - Create api/controllers/AdminController.php
  - Implement login() method with credential validation
  - Create simple session management (PHP sessions or JWT)
  - Hash and verify admin password
  - Return authentication token/session on success
  - Create admin credentials configuration file

  - _Requirements: 10.1, 10.2_

- [x] 25. Create admin API endpoints



  - Add POST /api/admin/login endpoint for authentication
  - Add GET /api/admin/submissions endpoint for listing all submissions
  - Add GET /api/admin/submissions/:id endpoint for submission details
  - Add GET /api/admin/submissions/:id/pdf endpoint for PDF download
  - Add GET /api/admin/search endpoint for search functionality
  - Implement authentication middleware for protected routes

  - _Requirements: 10.3, 10.4, 10.5, 11.1, 11.2, 11.3_

- [x] 26. Implement admin search and filter logic


  - Add search functionality in StorageManager
  - Implement searchSubmissions() method to filter by name, NID, mobile
  - Implement date range filtering
  - Add pagination support
  - Return filtered results with metadata (total count, page info)
  - _Requirements: 11.1, 11.2, 11.3_

- [ ]* 11.1 Write property test for admin authentication
  - **Property 29: Admin authentication**
  - **Validates: Requirements 10.2**

- [ ]* 11.2 Write property test for submission listing
  - **Property 30: Submission listing**
  - **Validates: Requirements 10.3**

- [ ]* 11.3 Write property test for search functionality
  - **Property 31: Search functionality**
  - **Validates: Requirements 11.1**


- [ ]* 11.4 Write property test for date range filtering
  - **Property 32: Date range filtering**
  - **Validates: Requirements 11.2**

- [x] 27. Create admin frontend components



  - Create src/pages/Admin/ directory structure
  - Create AdminLogin.jsx component with login form
  - Create AdminDashboard.jsx component with overview
  - Create SubmissionsList.jsx component with table
  - Create SubmissionDetail.jsx component for viewing details


  - Create SearchFilter.jsx component for filtering
  - Add admin routes to App.jsx
  - Implement protected route wrapper for admin pages
  - _Requirements: 10.1, 10.2, 10.3, 10.4, 11.1, 11.2_

- [x] 28. Implement admin UI functionality


  - Connect login form to /api/admin/login endpoint
  - Store authentication token in localStorage
  - Fetch and display submissions list
  - Implement pagination controls
  - Add search input with debounced API calls
  - Add date range picker for filtering
  - Implement PDF download functionality
  - Add loading states and error handling
  - Style admin pages with Tailwind CSS
  - _Requirements: 10.2, 10.3, 10.4, 10.5, 11.1, 11.2, 11.3, 11.4, 11.5_

- [ ] 29. Add admin panel security features

  - Implement CSRF protection for admin endpoints
  - Add rate limiting for login attempts
  - Implement session timeout
  - Add logout functionality
  - Validate admin token on each request
  - Log admin actions for audit trail
  - _Requirements: 10.2_

- [ ] 30. Test admin panel functionality

  - Test login with valid and invalid credentials
  - Test submissions list displays correctly
  - Test search and filter functionality
  - Test PDF download from admin panel
  - Test pagination
  - Test responsive design on mobile
  - Test session timeout and re-authentication
  - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5, 11.1, 11.2, 11.3_

- [ ] 31. Final checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.
