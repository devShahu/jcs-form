# JCS Form - Phase 2 Requirements

## Current Status
✅ Basic form with 3 steps working
✅ Client-side validation working
✅ Form submission saves data
✅ All required fields added (blood group, movement role, aspirations)
⚠️ Bengali typing works but requires space
❌ PDF generation not working
❌ Document upload missing
❌ Admin panel not implemented

## Phase 2 - Critical Fixes Needed

### 1. Fix Bengali Typing (Realtime)
**Issue**: Current Avro implementation requires pressing space
**Solution**: Use a different library or implement custom Bengali input
**Options**:
- Use `bangla-web-keyboard` library
- Implement custom phonetic converter
- Use Google Input Tools API

### 2. Fix PDF Generation
**Current**: Placeholder text instead of actual PDF
**Needed**: Generate proper PDF with form data using HTML template
**Approach**:
- Use TCPDF with HTML support
- Or use a service like wkhtmltopdf
- Or use Puppeteer/Chrome headless via Node.js

### 3. Add Document Upload Fields
**Missing Fields**:
- Document upload (file input)
- Document type dropdown (NID/Birth Certificate/Student ID)

**Location**: Add to Personal Info step (Step 1)

**Implementation**:
```javascript
// Add to form state
document_file: '',
document_type: '',

// Add to PersonalInfoStep.jsx
<select name="document_type">
  <option value="">নির্বাচন করুন</option>
  <option value="nid">জাতীয় পরিচয়পত্র (NID)</option>
  <option value="birth_certificate">জন্ম নিবন্ধন</option>
  <option value="student_id">শিক্ষার্থী পরিচয়পত্র</option>
</select>

<DocumentUpload 
  value={formData.document_file}
  onChange={(value) => onChange('document_file', value)}
/>
```

### 4. Create Admin Panel
**Features Needed**:
- Login system
- View all submissions
- Download PDFs
- Search/filter submissions
- Update organization name and logo
- Manage form settings

**Tech Stack**:
- Same React frontend
- Protected routes
- Admin API endpoints
- Simple authentication

### 5. Proper PDF Generation with HTML Template
**Requirements**:
- Use the provided HTML template
- Fill in form data
- Include uploaded photo
- Append document as new page
- Generate downloadable PDF

**Approach**:
- Create HTML template in PHP
- Use TCPDF's writeHTML() method
- Or use external service (Puppeteer, wkhtmltopdf)

### 6. Update Existing Submissions
**Feature**: When user submits again, update their previous submission
**Implementation**:
- Check if submission exists by NID or email
- If exists, update instead of create new
- Replace old PDF with new one
- Keep submission history (optional)

## Estimated Time for Phase 2

| Task | Time | Priority |
|------|------|----------|
| Fix Bengali typing (realtime) | 2-3 hours | HIGH |
| Add document upload fields | 1 hour | HIGH |
| Fix PDF generation (basic) | 3-4 hours | HIGH |
| PDF with HTML template | 4-6 hours | MEDIUM |
| Admin panel (basic) | 8-10 hours | MEDIUM |
| Update submissions feature | 2-3 hours | LOW |
| **TOTAL** | **20-27 hours** | |

## Recommended Approach

### Quick Fix (2-4 hours):
1. Fix Bengali typing with better library
2. Add document upload fields
3. Generate basic PDF (even if not perfect)

### Complete Solution (20-27 hours):
1. All quick fixes
2. Proper HTML-based PDF generation
3. Basic admin panel
4. Update submissions feature

## Alternative: Use External Services

To speed up development, consider:
- **PDF Generation**: Use API service like PDFShift, DocRaptor
- **Bengali Typing**: Use Google Input Tools embed
- **Admin Panel**: Use existing admin template

## Next Steps

**Option A**: I can implement the Quick Fixes now (2-4 hours of work)
**Option B**: Create a new spec for Phase 2 and implement systematically
**Option C**: Focus on one critical feature at a time

**What would you like me to prioritize?**
