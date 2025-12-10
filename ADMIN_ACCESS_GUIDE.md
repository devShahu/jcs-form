# Admin Panel Access Guide

## 🔐 Quick Access

### URLs
- **Main Form**: `http://localhost:3000/`
- **Admin Panel**: `http://localhost:3000/admin`
- **Admin Login**: `http://localhost:3000/admin/login`
- **Admin Dashboard**: `http://localhost:3000/admin/dashboard`

### Default Credentials
```
Username: admin
Password: admin123
```

⚠️ **IMPORTANT**: Change these credentials before deploying to production!

## 📍 How to Access

### Method 1: Direct URL
1. Open your browser
2. Navigate to: `http://localhost:3000/admin`
3. You'll be redirected to the login page
4. Enter credentials and click "Sign In"

### Method 2: From Main Form
1. Go to the main form page: `http://localhost:3000/`
2. Look for the "Admin" link in the top-right corner of the header
3. Click the "Admin" link
4. Login with the credentials above

### Method 3: Bookmark
Create a bookmark in your browser:
- **Name**: JCS Admin Panel
- **URL**: `http://localhost:3000/admin`

## 🚀 First Time Setup

1. **Start the servers**:
   ```bash
   # Terminal 1: Backend API
   php -S localhost:8000 -t api
   
   # Terminal 2: Frontend
   npm run dev
   ```

2. **Verify servers are running**:
   - Backend: `http://localhost:8000/api/health` should return `{"status":"ok"}`
   - Frontend: `http://localhost:3000/` should show the form

3. **Access admin panel**:
   - Navigate to `http://localhost:3000/admin`
   - Login with default credentials

## 🎯 What You Can Do in Admin Panel

### Dashboard (`/admin/dashboard`)
- View total submissions count
- View recent submissions count
- Quick access to submissions list
- Quick access to search

### Submissions List (`/admin/submissions`)
- View all form submissions in a table
- Paginated view (20 per page)
- Search by name, NID, or mobile number
- Filter by date range
- Download PDFs directly from the list
- Click "View" to see full submission details

### Submission Detail (`/admin/submissions/:id`)
- View complete form data
- All fields organized by section:
  - Personal Information
  - Address
  - Background
  - Educational Qualification
  - Movement & Aspirations
- Download PDF button
- Back to list navigation

### Search & Filter
- **Text Search**: Enter name, NID, or mobile number
- **Date Range**: Select "From Date" and "To Date"
- **Clear Filters**: Reset all filters
- Results update in real-time

## 🔒 Security Features

### Session Management
- Sessions expire after 24 hours
- Automatic logout on session expiry
- Secure session token storage

### Protected Routes
- All admin routes require authentication
- Automatic redirect to login if not authenticated
- Token validation on each request

### Password Security
- Passwords are hashed using bcrypt
- No plain text password storage
- Secure password verification

## 🛠️ Troubleshooting

### Cannot Access Admin Panel

**Problem**: Page not loading
**Solution**: 
- Verify frontend is running: `npm run dev`
- Check the URL is correct: `http://localhost:5173/admin`
- Clear browser cache

**Problem**: Login not working
**Solution**:
- Verify backend is running: `php -S localhost:8000 -t api`
- Check credentials are correct
- Check browser console for errors
- Clear localStorage: `localStorage.clear()`

**Problem**: "Unauthorized" error
**Solution**:
- Session may have expired - login again
- Clear localStorage and login again
- Check backend API is accessible

### Cannot See Submissions

**Problem**: No submissions showing
**Solution**:
- Submit a test form first
- Check `/storage/submissions/` directory exists
- Verify backend has write permissions
- Check browser console for API errors

**Problem**: Search not working
**Solution**:
- Ensure submissions exist
- Try clearing filters
- Check search query format
- Review browser console for errors

## 📱 Mobile Access

The admin panel is fully responsive and works on mobile devices:

1. Open mobile browser
2. Navigate to `http://YOUR_IP:5173/admin`
   - Replace `YOUR_IP` with your computer's local IP
   - Example: `http://192.168.1.100:5173/admin`
3. Login with credentials
4. Use the mobile-optimized interface

## 🔄 Changing Admin Credentials

### Method 1: Edit Config File
1. Open `config/admin.php`
2. Generate new password hash:
   ```bash
   php -r "echo password_hash('your_new_password', PASSWORD_DEFAULT);"
   ```
3. Replace the `password_hash` value
4. Save the file
5. Restart backend server

### Method 2: Using PHP Script
Create a file `change_password.php`:
```php
<?php
$newPassword = 'your_new_password';
$hash = password_hash($newPassword, PASSWORD_DEFAULT);
echo "New password hash: " . $hash . "\n";
echo "Copy this to config/admin.php\n";
```

Run: `php change_password.php`

## 📊 Admin Panel Features Summary

| Feature | URL | Description |
|---------|-----|-------------|
| Login | `/admin/login` | Authentication page |
| Dashboard | `/admin/dashboard` | Overview and statistics |
| Submissions List | `/admin/submissions` | All submissions table |
| Submission Detail | `/admin/submissions/:id` | Full submission view |
| Search | `/admin/submissions?search=true` | Search interface |
| Logout | Click "Logout" button | End session |

## 🎨 UI Elements

### Header
- Title: "Admin Dashboard" / "Submissions" / etc.
- Username display
- Logout button

### Navigation
- Back buttons on all pages
- Breadcrumb-style navigation
- Quick action buttons

### Tables
- Sortable columns
- Pagination controls
- Action buttons (View, Download)
- Responsive design

### Forms
- Search input with debounce
- Date pickers
- Filter buttons
- Clear filters option

## 💡 Tips & Best Practices

1. **Bookmark the admin URL** for quick access
2. **Use search filters** to find submissions quickly
3. **Download PDFs regularly** for backup
4. **Change default password** immediately
5. **Logout when done** for security
6. **Use Chrome/Firefox** for best experience
7. **Check submissions daily** for new entries
8. **Test search functionality** with different queries

## 📞 Support

If you need help:
1. Check this guide first
2. Review `TESTING_GUIDE.md` for testing procedures
3. Check `PDF_AND_ADMIN_IMPLEMENTATION.md` for technical details
4. Review browser console for errors
5. Check PHP error logs

## ✅ Quick Checklist

Before using admin panel:
- [ ] Backend server is running
- [ ] Frontend server is running
- [ ] Can access main form at `http://localhost:5173/`
- [ ] Can access admin at `http://localhost:5173/admin`
- [ ] Know the admin credentials
- [ ] Have submitted at least one test form

## 🎉 You're Ready!

The admin panel is now accessible and ready to use. Navigate to `http://localhost:5173/admin` and start managing your form submissions!
