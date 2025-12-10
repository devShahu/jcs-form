# Troubleshooting Guide

## ✅ Your Servers ARE Running!

Both your frontend and backend are currently running and responding correctly:
- ✅ Frontend: `http://localhost:3000/` (Status: 200 OK)
- ✅ Backend: `http://localhost:8000/api/health` (Status: 200 OK)

## 🔗 Correct URLs

**Use port 3000, NOT 5173!**

- Main Form: `http://localhost:3000/`
- Admin Panel: `http://localhost:3000/admin`
- Admin Login: `http://localhost:3000/admin/login`

## Common Issues

### Issue: "Nothing shows up"

**Solution**: Make sure you're using the correct port (3000)

1. Open your browser
2. Go to: `http://localhost:3000/`
3. You should see the JCS membership form

If you still see nothing:
- Clear your browser cache (Ctrl+Shift+Delete)
- Try incognito/private mode
- Check browser console for errors (F12)

### Issue: "Admin panel not loading"

**Solution**: 
1. Go to: `http://localhost:3000/admin`
2. You should be redirected to login page
3. Login with:
   - Username: `admin`
   - Password: `admin123`

### Issue: "Form submission not working"

**Check**:
1. Backend is running: `http://localhost:8000/api/health`
2. Should return: `{"status":"ok","message":"API is running",...}`

If not working:
```bash
# Restart backend
php -S localhost:8000 -t api
```

### Issue: "Page is blank"

**Solutions**:
1. Check if servers are running:
   ```bash
   # Check processes
   Get-Process | Where-Object {$_.ProcessName -like "*node*" -or $_.ProcessName -like "*php*"}
   ```

2. Restart frontend:
   ```bash
   # Stop: Ctrl+C
   # Start: 
   npm run dev
   ```

3. Clear browser cache and reload

### Issue: "Cannot access admin panel"

**Check**:
1. Are you using the correct URL? `http://localhost:3000/admin`
2. Is the frontend running? Check `http://localhost:3000/`
3. Try clearing localStorage:
   - Open browser console (F12)
   - Type: `localStorage.clear()`
   - Reload page

## Quick Verification

Run these commands to verify everything is working:

```powershell
# Check frontend
curl http://localhost:3000 -UseBasicParsing

# Check backend
curl http://localhost:8000/api/health -UseBasicParsing

# Check admin login endpoint
curl http://localhost:8000/api/admin/login -UseBasicParsing -Method POST -Body '{"username":"admin","password":"admin123"}' -ContentType "application/json"
```

## Browser Console Errors

If you see errors in the browser console (F12):

### "Failed to fetch" or "Network Error"
- Backend is not running
- Start it: `php -S localhost:8000 -t api`

### "404 Not Found"
- Wrong URL or route doesn't exist
- Check you're using port 3000, not 5173

### "CORS Error"
- Backend CORS is misconfigured
- Check `api/index.php` has CORS middleware

## Still Not Working?

1. **Restart everything**:
   ```bash
   # Stop all (Ctrl+C in both terminals)
   
   # Start backend
   php -S localhost:8000 -t api
   
   # Start frontend (in new terminal)
   npm run dev
   ```

2. **Check the URLs again**:
   - Main form: `http://localhost:3000/`
   - Admin: `http://localhost:3000/admin`

3. **Clear everything**:
   - Clear browser cache
   - Clear localStorage: `localStorage.clear()`
   - Close and reopen browser

4. **Check for port conflicts**:
   ```powershell
   # Check what's using port 3000
   netstat -ano | findstr :3000
   
   # Check what's using port 8000
   netstat -ano | findstr :8000
   ```

## Success Checklist

- [ ] Backend responds at `http://localhost:8000/api/health`
- [ ] Frontend loads at `http://localhost:3000/`
- [ ] Form is visible on the page
- [ ] Admin link is visible in header
- [ ] Admin login page loads at `http://localhost:3000/admin`
- [ ] Can login with admin/admin123

## Need More Help?

Check these files:
- `QUICK_START.md` - Quick start guide
- `ADMIN_ACCESS_GUIDE.md` - Admin panel guide
- `TESTING_GUIDE.md` - Testing procedures

## Current Status

As of now:
- ✅ Backend is running on port 8000
- ✅ Frontend is running on port 3000
- ✅ Both are responding correctly
- ✅ You just need to use the correct URLs!

**Go to: `http://localhost:3000/admin`**
