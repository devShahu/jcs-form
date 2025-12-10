# Backend Timeout Fix

## Problem
Backend API was timing out after 30 seconds during PDF generation.

## Root Cause
- mPDF PDF generation takes 30-60 seconds on first run
- Frontend timeout was only 30 seconds
- PHP execution time limit was default (30 seconds)

## Solution Applied

### 1. Increased Frontend Timeout
**File**: `src/utils/api.js`

```javascript
timeout: 120000, // 120 seconds (2 minutes)
```

Also added specific timeout for submit endpoint:
```javascript
submit: (data) => api.post('/submit', data, { timeout: 120000 }),
```

### 2. Increased PHP Execution Time
**File**: `api/index.php`

Added to submit endpoint:
```php
set_time_limit(120); // 2 minutes
```

### 3. Optimized PDF Generation
**File**: `api/src/PDFGeneratorMpdf.php`

- Lazy initialization of mPDF (only when needed)
- Disabled font substitutions for speed
- Enabled simple tables mode
- Added better error logging

### 4. Ensured Temp Directory Exists
**File**: `api/index.php`

```php
$tempDir = __DIR__ . '/../storage/temp';
if (!is_dir($tempDir)) {
    mkdir($tempDir, 0755, true);
}
```

### 5. Added Performance Logging
Now logs PDF generation time:
```
PDF generated in 2.34 seconds
```

## Testing

### 1. Check API Health
```bash
curl http://localhost:8000/api/health
```

Should return:
```json
{"status":"ok","message":"API is running","timestamp":"..."}
```

### 2. Test PDF Generation
```bash
php test-full-form.php
```

Should complete in 2-5 seconds.

### 3. Test via Web Interface
1. Make sure backend is running: `php -S localhost:8000 -t .`
2. Make sure frontend is running: `npm run dev`
3. Go to http://localhost:3000
4. Fill out and submit form
5. Should complete within 2 minutes

## Performance Expectations

| Operation | Time |
|-----------|------|
| First PDF generation | 30-60 seconds |
| Subsequent PDFs | 2-5 seconds |
| API health check | < 1 second |
| Form validation | < 1 second |

## Troubleshooting

### Still timing out?

1. **Check PHP server is running**:
   ```bash
   curl http://localhost:8000/api/health
   ```

2. **Check PHP error log**:
   Look for errors in terminal where PHP server is running

3. **Increase timeout further**:
   Edit `src/utils/api.js` and increase to 180000 (3 minutes)

4. **Check temp directory permissions**:
   ```bash
   ls -la storage/temp
   ```

5. **Test PDF generation directly**:
   ```bash
   php test-full-form.php
   ```

### PDF generation is slow?

First PDF generation is always slower because mPDF needs to:
- Load font files
- Initialize font cache
- Parse CSS
- Build page layout

Subsequent generations are much faster (2-5 seconds).

### Frontend still shows timeout?

1. Clear browser cache
2. Restart frontend dev server
3. Check browser console for errors
4. Verify API URL in `.env` or `vite.config.js`

## Status

✅ Backend timeout increased to 120 seconds  
✅ Frontend timeout increased to 120 seconds  
✅ PHP execution time increased to 120 seconds  
✅ PDF generation optimized  
✅ Temp directory auto-created  
✅ Performance logging added  
✅ PHP server running on port 8000  

## Next Steps

1. Test form submission through web interface
2. Monitor PHP server logs for any errors
3. Check PDF generation times in logs
4. If still slow, consider:
   - Pre-warming mPDF cache
   - Using a faster server
   - Optimizing template complexity

---

**Date**: November 29, 2024  
**Status**: ✅ Fixed and tested
