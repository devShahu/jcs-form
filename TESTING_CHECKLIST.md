# Testing Checklist - Admin Authentication Fix

## Pre-Testing Setup

### ☐ 1. Restart Servers
```bash
# Stop any running servers (Ctrl+C)

# Terminal 1: Start PHP API
php -S localhost:8000 -t .

# Terminal 2: Start Frontend
npm run dev
```

### ☐ 2. Clear Browser Data
- [ ] Clear cookies for localhost
- [ ] Clear localStorage
- [ ] Clear session storage
- [ ] Or use incognito/private window

### ☐ 3. Verify Servers Running
- [ ] API: http://localhost:8000/api/health should return JSON
- [ ] Frontend: http://localhost:3000 should load

## Test 1: Login ✅

### Steps
1. [ ] Navigate to http://localhost:3000/admin/login
2. [ ] Enter username: `admin`
3. [ ] Enter password: `admin123`
4. [ ] Click "Sign In"

### Expected Results
- [ ] No errors in console
- [ ] Redirects to `/admin/dashboard`
- [ ] Dashboard loads successfully

### Check Browser DevTools
- [ ] **Console**: No errors
- [ ] **Network**: POST to `/api/admin/login` returns 200
- [ ] **Application > Cookies**: PHPSESSID cookie present
- [ ] **Application > Local Storage**: admin_token present

### If Failed
- Check console for error messages
- Check Network tab for response
- Verify credentials are correct
- Check PHP error logs

---

## Test 2: Dashboard ✅

### Steps
1. [ ] Should already be on dashboard after login
2. [ ] Observe stats loading

### Expected Results
- [ ] "Total Submissions" card shows number
- [ ] "Recent Submissions" card shows number
- [ ] No loading spinner stuck
- [ ] No error messages

### Check Browser DevTools
- [ ] **Console**: No 401 errors
- [ ] **Console**: No CORS errors
- [ ] **Network**: GET to `/api/admin/submissions` returns 200
- [ ] **Network**: Request includes Cookie header
- [ ] **Network**: Response includes `Access-Control-Allow-Credentials: true`

### If Failed
- Check if cookie is being sent (Network > Headers > Request Headers)
- Check CORS headers in response
- Verify session is active (test-session.php)

---

## Test 3: Settings Page - Test PDF ✅

### Steps
1. [ ] Click "Settings & Test PDF" button
2. [ ] Click "Generate Test PDF" button
3. [ ] Wait for PDF generation

### Expected Results
- [ ] Success message appears
- [ ] PDF opens in new tab
- [ ] PDF contains demo data in Bengali
- [ ] PDF is properly formatted (2 pages)

### Check Browser DevTools
- [ ] **Console**: No errors
- [ ] **Network**: GET to `/api/admin/test-pdf` returns 200
- [ ] **Network**: Response type is `application/pdf`
- [ ] **Network**: Request includes Cookie header

### Check PDF Content
- [ ] Page 1: Personal info, education
- [ ] Page 2: Movement role, aspirations
- [ ] Bengali text renders correctly
- [ ] No corruption or garbled text

### If Failed
- Check browser popup blocker
- Check console for errors
- Verify TCPDF is installed: `composer install`
- Check templates/pdf_template.html exists
- Check fonts directory has Bengali fonts

---

## Test 4: Submissions List ✅

### Steps
1. [ ] Click "Back to Dashboard"
2. [ ] Click "View All Submissions"
3. [ ] Observe submissions loading

### Expected Results
- [ ] List loads (may be empty if no submissions)
- [ ] No errors
- [ ] Pagination shows if multiple pages
- [ ] Search form is visible

### Check Browser DevTools
- [ ] **Console**: No errors
- [ ] **Network**: GET to `/api/admin/submissions` returns 200
- [ ] **Network**: Request includes Cookie header

### If Failed
- Check authentication (cookie present?)
- Check API endpoint is working
- Check storage directory exists

---

## Test 5: Search Functionality ✅

### Steps
1. [ ] Enter search term (e.g., "test")
2. [ ] Click "Search" button
3. [ ] Observe results

### Expected Results
- [ ] Search executes without errors
- [ ] Results update (may be empty)
- [ ] "Clear Filters" button works

### Check Browser DevTools
- [ ] **Console**: No errors
- [ ] **Network**: GET to `/api/admin/search` returns 200
- [ ] **Network**: Query parameters included

---

## Test 6: Logout ✅

### Steps
1. [ ] Click "Logout" button
2. [ ] Observe redirect

### Expected Results
- [ ] Redirects to `/admin/login`
- [ ] Session cleared
- [ ] Cookie removed
- [ ] localStorage cleared

### Check Browser DevTools
- [ ] **Application > Cookies**: PHPSESSID removed
- [ ] **Application > Local Storage**: admin_token removed

---

## Test 7: Session Persistence ✅

### Steps
1. [ ] Login successfully
2. [ ] Navigate to different admin pages
3. [ ] Refresh browser (F5)
4. [ ] Close tab and reopen admin URL

### Expected Results
- [ ] Session persists across page navigation
- [ ] Session persists after refresh
- [ ] Session persists after tab close/reopen (within 24 hours)

### If Failed
- Check cookie expiration
- Check session timeout settings
- Verify session directory is writable

---

## Test 8: Authentication Redirect ✅

### Steps
1. [ ] Logout if logged in
2. [ ] Try to access http://localhost:3000/admin/dashboard directly
3. [ ] Try to access http://localhost:3000/admin/settings directly

### Expected Results
- [ ] Automatically redirects to `/admin/login`
- [ ] After login, can access protected pages

---

## Test 9: Multiple Tabs ✅

### Steps
1. [ ] Login in Tab 1
2. [ ] Open Tab 2 to admin dashboard
3. [ ] Logout in Tab 1
4. [ ] Try to use Tab 2

### Expected Results
- [ ] Tab 2 should detect logout
- [ ] Tab 2 should redirect to login on next API call

---

## Test 10: Session Timeout ✅

### Steps
1. [ ] Login successfully
2. [ ] Wait 24+ hours (or modify session timeout for testing)
3. [ ] Try to access admin page

### Expected Results
- [ ] Session expires after timeout
- [ ] Redirects to login
- [ ] Shows appropriate message

---

## Network Tab Verification

For ANY admin API request, verify:

### Request Headers
```
Cookie: PHPSESSID=abc123xyz...
Origin: http://localhost:3000
```

### Response Headers
```
Access-Control-Allow-Origin: http://localhost:3000
Access-Control-Allow-Credentials: true
```

### Status Codes
- [ ] 200 OK for successful requests
- [ ] 401 Unauthorized for unauthenticated requests
- [ ] No 500 errors

---

## Console Verification

### Should NOT See
- ❌ CORS policy errors
- ❌ 401 Unauthorized errors
- ❌ Failed to fetch errors
- ❌ Network errors

### Should See
- ✅ Successful API responses
- ✅ Clean console (or only expected logs)

---

## Final Verification

### All Tests Passed? ✅
- [ ] Login works
- [ ] Dashboard loads
- [ ] Settings/Test PDF works
- [ ] Submissions list works
- [ ] Search works
- [ ] Logout works
- [ ] Session persists
- [ ] Auth redirect works
- [ ] No console errors
- [ ] No CORS errors

### If All Tests Pass
🎉 **Success!** The authentication system is working correctly.

### If Any Test Fails
1. Note which test failed
2. Check the "If Failed" section for that test
3. Review COMPLETE_FIX_SUMMARY.md
4. Check PHP error logs
5. Check browser console
6. Verify all files were updated correctly

---

## Quick Debug Commands

```bash
# Check PHP syntax
php -l api/index.php
php -l api/controllers/AdminController.php

# Test session endpoint
curl http://localhost:8000/test-session.php

# Check if servers are running
netstat -an | findstr "8000"
netstat -an | findstr "3000"

# View PHP error log (location varies)
tail -f /path/to/php/error.log

# Test CORS
curl -H "Origin: http://localhost:3000" \
     -H "Access-Control-Request-Method: POST" \
     -X OPTIONS \
     http://localhost:8000/api/admin/login -v
```

---

## Production Testing Checklist

Before deploying to production:

- [ ] Change admin password
- [ ] Update session.cookie_secure to 'true'
- [ ] Restrict CORS to production domain
- [ ] Update .env with production API URL
- [ ] Test on HTTPS
- [ ] Test with production domain
- [ ] Verify SSL certificate
- [ ] Test session persistence
- [ ] Test logout
- [ ] Test PDF generation
- [ ] Load test with multiple users

---

## Support

If you encounter issues:
1. Check COMPLETE_FIX_SUMMARY.md for detailed explanation
2. Check TROUBLESHOOTING.md for common issues
3. Check AUTH_FLOW_DIAGRAM.md to understand the flow
4. Review QUICK_FIX_REFERENCE.md for quick solutions
