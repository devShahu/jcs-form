# Admin Authentication & CORS Fix

## Issues Fixed

### 1. CORS Configuration Problem
**Problem**: The API was using wildcard `*` for `Access-Control-Allow-Origin`, which is incompatible with `credentials: 'include'` mode.

**Solution**: Updated CORS middleware in `api/index.php` to:
- Use the actual origin from the request header
- Add `Access-Control-Allow-Credentials: true` header
- This allows cookies/sessions to work across origins

### 2. Session Cookie Configuration
**Problem**: PHP sessions weren't being sent/received properly in cross-origin requests.

**Solution**: Updated `AdminController.php` constructor to configure session cookies:
```php
ini_set('session.cookie_samesite', 'None');
ini_set('session.cookie_secure', 'false'); // Set to 'true' in production with HTTPS
ini_set('session.cookie_httponly', 'true');
ini_set('session.cookie_lifetime', '86400'); // 24 hours
```

### 3. Inconsistent API Usage
**Problem**: Admin pages were using different methods to call the API:
- Some used the regular `api` utility (without credentials)
- Settings page used raw `fetch` calls
- No consistent credential handling

**Solution**: Created dedicated `adminApi.js` utility with:
- `withCredentials: true` for all requests
- Automatic 401 redirect to login
- Consistent error handling
- Proper blob handling for PDF downloads

## Files Changed

### New Files
- `src/utils/adminApi.js` - Dedicated admin API utility with credential support

### Updated Files
- `api/index.php` - Fixed CORS configuration
- `api/controllers/AdminController.php` - Added session cookie configuration
- `src/pages/Admin/AdminLogin.jsx` - Uses adminAPI
- `src/pages/Admin/AdminDashboard.jsx` - Uses adminAPI
- `src/pages/Admin/Settings.jsx` - Uses adminAPI
- `src/pages/Admin/SubmissionsList.jsx` - Uses adminAPI
- `src/pages/Admin/SubmissionDetail.jsx` - Uses adminAPI

## How It Works Now

### Authentication Flow
1. User logs in via `/admin/login`
2. Server creates PHP session and sets session cookie
3. Cookie is sent with `SameSite=None` to allow cross-origin
4. All subsequent admin API calls include `withCredentials: true`
5. Server validates session on each request
6. If session invalid/expired, returns 401
7. Frontend automatically redirects to login on 401

### API Usage
```javascript
import { adminAPI } from '../../utils/adminApi';

// Login
const response = await adminAPI.login(username, password);

// Get submissions
const submissions = await adminAPI.getSubmissions(page, limit);

// Download PDF
const pdfBlob = await adminAPI.testPDF();
```

## Testing

### Test the Fix
1. **Login**: Go to `/admin/login` and login with `admin` / `admin123`
2. **Dashboard**: Should load stats without 401 errors
3. **Settings**: Click "Generate Test PDF" - should open PDF in new tab
4. **Submissions**: Should load list without errors
5. **Search**: Should work without authentication issues

### Check Browser Console
- No CORS errors
- No 401 Unauthorized errors
- Session cookies should be visible in DevTools > Application > Cookies

### Check Network Tab
- Admin API requests should include cookies
- Response headers should show `Access-Control-Allow-Credentials: true`
- Response headers should show `Access-Control-Allow-Origin: http://localhost:3000`

## Production Deployment

### Important Changes for Production

1. **Enable HTTPS**: Update session configuration in `AdminController.php`:
```php
ini_set('session.cookie_secure', 'true'); // Change to true
```

2. **Update CORS Origin**: In `api/index.php`, you may want to restrict to specific origins:
```php
$allowedOrigins = ['https://yourdomain.com', 'https://www.yourdomain.com'];
$origin = $request->getHeaderLine('Origin');
if (in_array($origin, $allowedOrigins)) {
    $response = $response->withHeader('Access-Control-Allow-Origin', $origin);
}
```

3. **Secure Session Settings**: Consider adding in `AdminController.php`:
```php
ini_set('session.cookie_domain', '.yourdomain.com');
ini_set('session.gc_maxlifetime', '86400');
```

## Troubleshooting

### Still Getting 401 Errors?
1. Clear browser cookies
2. Check PHP session directory is writable: `session.save_path`
3. Verify PHP session is starting: Check `session_status()` in logs
4. Check browser DevTools > Network > Request Headers for cookies

### CORS Errors?
1. Verify frontend is running on `http://localhost:3000`
2. Check API is running on `http://localhost:8000`
3. Verify `Access-Control-Allow-Origin` matches frontend origin exactly
4. Ensure `Access-Control-Allow-Credentials: true` is present

### PDF Not Opening?
1. Check browser popup blocker
2. Verify test-pdf endpoint returns 200 status
3. Check PHP error logs for TCPDF errors
4. Ensure `templates/pdf_template.html` exists

## Summary

The authentication system now properly uses PHP sessions with cross-origin cookie support. All admin pages use a consistent API utility that automatically handles credentials and redirects on authentication failures. The CORS configuration allows cookies to be sent and received properly between the frontend and backend.
