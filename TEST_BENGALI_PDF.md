# Testing Bengali PDF Generation

## Quick Test

Run this command to test Bengali font support:

```bash
php test-mpdf-bengali.php
```

This will create `test-mpdf-bengali-output.pdf` with sample Bengali text.

## Test Form Submission

Run this command to test a complete form with Bengali data:

```bash
php test-form-submission.php
```

This will create `test-form-submission-output.pdf` with a full membership form.

## Test via Web Interface

### 1. Start the servers:

**Backend (PHP):**
```bash
php -S localhost:8000 -t .
```

**Frontend (React):**
```bash
npm run dev
```

### 2. Test Form Submission:
1. Go to http://localhost:3000
2. Fill out the form with Bengali text
3. Submit the form
4. Check the generated PDF in `storage/submissions/`

### 3. Test via Admin Panel:
1. Go to http://localhost:3000/admin
2. Login with credentials from `.env`
3. Go to Settings
4. Click "Test PDF Generation"
5. Download and verify the PDF

## What to Check

✓ **Bengali characters display correctly** (not as "?" or boxes)  
✓ **English characters display correctly**  
✓ **Mixed Bengali/English text works**  
✓ **Bengali numbers (০১২৩৪৫৬৭৮৯) display**  
✓ **Layout and formatting look good**  
✓ **Photos embed correctly**  

## Expected Results

### Good ✓
- Bengali: জাতীয় ছাত্রশক্তি
- English: National Student Power
- Mixed: This is English with বাংলা text

### Bad ✗
- Bengali: ????? ?????????
- Boxes: □□□□□ □□□□□□□□□
- Garbled: ŕŕŕŕŕ ŕŕŕŕŕŕŕŕŕ

## Troubleshooting

### If Bengali shows as "?"
1. Make sure you ran `composer update`
2. Check mPDF is installed: `composer show mpdf/mpdf`
3. Clear any cached PDFs
4. Restart PHP server

### If PDF generation fails
1. Check PHP error log
2. Verify template exists: `templates/pdf_template.html`
3. Check write permissions on `storage/temp/`
4. Run: `composer dump-autoload`

### If fonts look wrong
- DejaVu Sans is built into mPDF
- No external font files needed
- Font should work automatically

## Font Information

**Current Font**: DejaVu Sans (built-in to mPDF)

**Supports**:
- Latin (English, French, German, etc.)
- Bengali (বাংলা)
- Arabic (العربية)
- Devanagari (हिन्दी)
- Greek (Ελληνικά)
- Cyrillic (Русский)
- And many more...

## Performance

- PDF generation: ~1-2 seconds
- File size: ~90-100 KB per form
- Memory usage: ~10-15 MB per PDF

## Next Steps

Once testing is complete:
1. Submit real forms with Bengali data
2. Verify all existing submissions still work
3. Archive old Dompdf-generated PDFs if needed
4. Update documentation for users
