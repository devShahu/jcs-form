# Quick Fix Reference - Admin Auth Issues

## 🚀 Quick Start

### 1. Restart Servers
```bash
# Terminal 1: PHP API
php -S localhost:8000 -t .

# Terminal 2: Frontend
npm run dev
```

### 2. Clear Browser
- Clear cookies for localhost
- Clear localStorage
- Or use incognito mode

### 3. Test
1. Go to `http://localhost:3000/admin/login`
2. Login: `admin` / `admin123`
3. Test Settings → Generate Test PDF

## ✅ What Was Fixed

| Issue | Fix |
|-------|-----|
| 401 Unauthorized errors | Added `withCredentials: true` to all admin API calls |
| CORS wildcard error | Changed from `*` to actual origin + credentials header |
| Session not persisting | Configured PHP session cookies for cross-origin |
| Inconsistent API usage | Created dedicated `adminApi.js` utility |
| Test PDF failing | Fixed authentication + blob handling |

## 📁 Key Files Changed

### New
- `src/utils/adminApi.js` - Admin API with credentials

### Modified
- `api/index.php` - CORS config
- `api/controllers/AdminController.php` - Session config
- All admin pages - Use adminAPI

## 🔍 Verify Fix

### Check Console (Should be clean)
```
✅ No CORS errors
✅ No 401 errors
✅ Successful API responses
```

### Check Network Tab
```
✅ Cookies sent with requests
✅ Access-Control-Allow-Credentials: true
✅ Access-Control-Allow-Origin: http://localhost:3000
```

### Check Application Tab
```
✅ Session cookie present
✅ admin_token in localStorage
```

## 🐛 Still Not Working?

### Quick Checks
```bash
# 1. Check PHP syntax
php -l api/index.php
php -l api/controllers/AdminController.php

# 2. Test session
curl http://localhost:8000/test-session.php

# 3. Check ports
netstat -an | findstr "8000"  # API should be running
netstat -an | findstr "3000"  # Frontend should be running
```

### Nuclear Option
```bash
# Stop everything
# Close all terminals
# Clear browser completely
# Restart computer (if needed)
# Start fresh
```

## 📝 Key Code Snippets

### CORS (api/index.php)
```php
$origin = $request->getHeaderLine('Origin') ?: 'http://localhost:3000';
return $response
    ->withHeader('Access-Control-Allow-Origin', $origin)
    ->withHeader('Access-Control-Allow-Credentials', 'true')
```

### Session (AdminController.php)
```php
ini_set('session.cookie_samesite', 'None');
ini_set('session.cookie_secure', 'false');
ini_set('session.cookie_httponly', 'true');
```

### API Usage (Admin pages)
```javascript
import { adminAPI } from '../../utils/adminApi';

// Login
await adminAPI.login(username, password);

// Get data
await adminAPI.getSubmissions(page, limit);

// Download PDF
await adminAPI.testPDF();
```

## 🎯 Expected Results

### Login Flow
```
1. Enter credentials → 2. POST /api/admin/login
3. Session created → 4. Cookie set
5. Redirect to dashboard → 6. Stats load
✅ Success!
```

### Settings Page
```
1. Click "Generate Test PDF"
2. GET /api/admin/test-pdf (with cookie)
3. PDF blob received
4. Opens in new tab
✅ Success!
```

## 📞 Need Help?

Check these files for details:
- `COMPLETE_FIX_SUMMARY.md` - Full explanation
- `ADMIN_AUTH_FIX.md` - Technical details
- `TROUBLESHOOTING.md` - General troubleshooting

## 🔐 Production Checklist

Before deploying:
- [ ] Change `session.cookie_secure` to `true`
- [ ] Restrict CORS origins to production domain
- [ ] Change admin password
- [ ] Update `.env` with production API URL
- [ ] Test on HTTPS
