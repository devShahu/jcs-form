# Fix Blank Screen Issue

## Quick Fix - Restart Everything

### Step 1: Stop All Servers

Press `Ctrl+C` in both terminal windows (frontend and backend)

### Step 2: Restart Backend

```powershell
php -S localhost:8000 -t api
```

Keep this terminal open.

### Step 3: Restart Frontend

**Option A - Manual:**
```powershell
npm run dev
```

**Option B - Using Script:**
```powershell
.\restart-frontend.ps1
```

### Step 4: Wait and Test

1. Wait 10-15 seconds for the server to start
2. Open browser to: `http://localhost:3000`
3. Hard refresh: `Ctrl+Shift+R` or `Ctrl+F5`

## If Still Blank - Check Browser Console

1. Open browser (Chrome/Edge/Firefox)
2. Press `F12` to open Developer Tools
3. Click "Console" tab
4. Refresh the page (`F5`)
5. Look for RED error messages

### Common Errors and Fixes

#### Error: "Failed to fetch module"
**Fix:**
```powershell
# Stop server (Ctrl+C)
# Clear npm cache
npm cache clean --force
# Reinstall
npm install
# Restart
npm run dev
```

#### Error: "Cannot find module 'react-router-dom'"
**Fix:**
```powershell
npm install react-router-dom
npm run dev
```

#### Error: "Unexpected token" or "Syntax error"
**Fix:** There's a code error. Check the console for the file name and line number.

#### Error: Nothing in console, just blank
**Fix:**
1. Clear browser cache: `Ctrl+Shift+Delete`
2. Select "Cached images and files"
3. Click "Clear data"
4. Refresh: `Ctrl+Shift+R`

## Nuclear Option - Complete Reset

If nothing works, do a complete reset:

```powershell
# 1. Stop all servers (Ctrl+C)

# 2. Kill all Node processes
Get-Process node | Stop-Process -Force

# 3. Clear npm cache
npm cache clean --force

# 4. Delete node_modules
Remove-Item -Recurse -Force node_modules

# 5. Reinstall everything
npm install

# 6. Start backend
php -S localhost:8000 -t api

# 7. Start frontend (in new terminal)
npm run dev

# 8. Wait 15 seconds

# 9. Open browser to http://localhost:3000
```

## Check if Server is Actually Running

```powershell
# Check frontend
curl http://localhost:3000 -UseBasicParsing

# Should return HTML with "root" div
```

## Verify React is Loading

1. Open `http://localhost:3000`
2. Press `F12` (Developer Tools)
3. Go to "Elements" or "Inspector" tab
4. Look for `<div id="root">`
5. Inside root, you should see React components

If root is empty (`<div id="root"></div>`), React isn't loading.

## Common Causes of Blank Screen

1. **JavaScript Error** - Check console (F12)
2. **Missing Dependencies** - Run `npm install`
3. **Port Conflict** - Another app using port 3000
4. **Cache Issue** - Clear browser cache
5. **Build Issue** - Delete `node_modules` and reinstall

## Test with Simple HTML

To verify the server works, create a test file:

1. Create `public/test.html`:
```html
<!DOCTYPE html>
<html>
<body>
    <h1>Server Works!</h1>
    <p>If you see this, the server is running.</p>
</body>
</html>
```

2. Open: `http://localhost:3000/test.html`
3. If you see "Server Works!", the server is fine
4. The issue is with React

## Check Network Tab

1. Open browser to `http://localhost:3000`
2. Press `F12`
3. Click "Network" tab
4. Refresh page (`F5`)
5. Look for failed requests (red)

Common issues:
- `main.jsx` - 404: File not found
- `App.jsx` - 404: File not found
- Any 500 errors: Server error

## Still Not Working?

Please provide:
1. Screenshot of browser console (F12 → Console)
2. Screenshot of Network tab (F12 → Network)
3. Output of: `npm run dev`
4. Any error messages you see

## Quick Checklist

- [ ] Backend running: `http://localhost:8000/api/health` returns JSON
- [ ] Frontend running: Terminal shows "Local: http://localhost:3000"
- [ ] Browser console (F12) shows no RED errors
- [ ] Network tab shows no failed requests
- [ ] Cleared browser cache
- [ ] Tried hard refresh (Ctrl+Shift+R)
- [ ] Tried incognito/private mode

## Most Likely Solution

Based on your issue, try this:

1. **Stop the frontend** (Ctrl+C in terminal)
2. **Clear the cache**:
   ```powershell
   npm cache clean --force
   ```
3. **Restart**:
   ```powershell
   npm run dev
   ```
4. **Wait 10 seconds**
5. **Hard refresh browser**: `Ctrl+Shift+R`

If this doesn't work, **check the browser console** (F12) and tell me what errors you see.
