# Settings & PDF Generation Fix

## Issues Fixed

### 1. ❌ Logo Upload Not Working
**Problem**: Logo upload button had no functionality
**Solution**: 
- Created `SettingsManager.php` to handle file uploads
- Added `/api/admin/upload-logo` endpoint
- Implemented proper file validation (type, size)
- Added preview functionality in UI

### 2. ❌ Settings Not Persisting
**Problem**: Settings showed success message but disappeared after refresh
**Solution**:
- Created `SettingsManager.php` to save/load settings from JSON file
- Added `/api/admin/settings` GET/POST endpoints
- Settings now persist in `storage/settings.json`
- UI loads settings on mount

### 3. ❌ PDF Shows Question Marks for Bengali Text
**Problem**: TCPDF default font doesn't support Bengali characters
**Solution**:
- Added Bengali font (Noto Sans Bengali) to TCPDF
- Configured PDF generator to use Bengali font by default
- Font file: `fonts/NotoSansBengali-Regular.ttf`

### 4. ❌ PDF Not Using Organization Settings
**Problem**: PDF always showed hardcoded organization name
**Solution**:
- PDF now loads organization name from settings
- PDF uses uploaded logo from settings
- Template uses `{{org_name_bn}}` placeholder

---

## New Features

### Settings Management
- ✅ Save organization name (Bengali & English)
- ✅ Upload and manage logo
- ✅ Settings persist across sessions
- ✅ Real-time preview of logo
- ✅ File validation (type, size)

### PDF Generation
- ✅ Proper Bengali font support
- ✅ Uses organization settings
- ✅ Dynamic logo from settings
- ✅ Test PDF with demo data
- ✅ Clean, professional layout

---

## Files Created

### Backend
- `api/src/SettingsManager.php` - Settings management class
  - Load/save settings from JSON
  - Handle logo uploads
  - File validation
  - Default settings

### Storage
- `storage/settings.json` - Settings data file (auto-created)

---

## Files Modified

### Backend

#### api/src/PDFGenerator.php
**Changes**:
1. Added Bengali font initialization
2. Uses `NotoSansBengali-Regular.ttf` font
3. Loads settings for organization name and logo
4. Replaces placeholders with settings values

**Key Code**:
```php
private function initializeBengaliFont(): void
{
    $fontPath = __DIR__ . '/../../fonts/NotoSansBengali-Regular.ttf';
    if (file_exists($fontPath)) {
        $this->bengaliFontName = TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 96);
    }
}
```

#### api/index.php
**Changes**:
1. Added `use App\SettingsManager`
2. Added GET `/api/admin/settings` endpoint
3. Added POST `/api/admin/settings` endpoint
4. Added POST `/api/admin/upload-logo` endpoint

**New Endpoints**:
- `GET /api/admin/settings` - Get current settings
- `POST /api/admin/settings` - Update settings
- `POST /api/admin/upload-logo` - Upload logo file

### Frontend

#### src/utils/adminApi.js
**Changes**:
Added settings methods:
```javascript
getSettings: () => adminApi.get('/admin/settings'),
updateSettings: (settings) => adminApi.post('/admin/settings', settings),
uploadLogo: (file) => { /* FormData upload */ }
```

#### src/pages/Admin/Settings.jsx
**Changes**:
1. Added state for logo path and preview
2. Added `loadSettings()` on component mount
3. Implemented `handleSaveSettings()` with API call
4. Implemented `handleLogoChange()` with upload
5. Added file validation
6. Added loading states for all actions
7. Real-time logo preview

**New Features**:
- Settings load from backend
- Settings save to backend
- Logo upload with preview
- File validation (type, size)
- Loading indicators
- Success/error messages

#### templates/pdf_template.html
**Changes**:
- Changed hardcoded organization name to `{{org_name_bn}}` placeholder

---

## How It Works

### Settings Flow
```
1. User opens Settings page
   ↓
2. Frontend calls GET /api/admin/settings
   ↓
3. SettingsManager loads from storage/settings.json
   ↓
4. Settings displayed in form
   ↓
5. User modifies settings
   ↓
6. User clicks "Save Settings"
   ↓
7. Frontend calls POST /api/admin/settings
   ↓
8. SettingsManager saves to storage/settings.json
   ↓
9. Success message shown
```

### Logo Upload Flow
```
1. User selects image file
   ↓
2. Frontend validates file (type, size)
   ↓
3. Frontend shows preview
   ↓
4. Frontend calls POST /api/admin/upload-logo
   ↓
5. SettingsManager validates file
   ↓
6. File saved to public/images/logo.{ext}
   ↓
7. Settings updated with new logo path
   ↓
8. Success message shown
   ↓
9. Settings reloaded to show new logo
```

### PDF Generation Flow
```
1. User clicks "Generate Test PDF"
   ↓
2. Frontend calls GET /api/admin/test-pdf
   ↓
3. PDFGenerator initializes with Bengali font
   ↓
4. PDFGenerator loads settings (org name, logo)
   ↓
5. PDFGenerator loads HTML template
   ↓
6. Placeholders replaced with data + settings
   ↓
7. TCPDF renders HTML to PDF with Bengali font
   ↓
8. PDF returned as blob
   ↓
9. Frontend opens PDF in new tab
```

---

## Settings Data Structure

### storage/settings.json
```json
{
  "org_name_bn": "জাতীয় ছাত্রশক্তি",
  "org_name_en": "Jatiya Chhatra Shakti",
  "logo_path": "/images/logo.png",
  "updated_at": "2024-01-01 12:00:00"
}
```

---

## File Validation

### Logo Upload
- **Allowed types**: JPG, PNG, GIF
- **Max size**: 2MB
- **Recommended**: 200x200px
- **Saved as**: `public/images/logo.{ext}`

---

## Testing

### Test Settings
1. Go to Settings page
2. Change organization names
3. Click "Save Settings"
4. Refresh page
5. ✅ Settings should persist

### Test Logo Upload
1. Go to Settings page
2. Click "Upload New Logo"
3. Select image file (< 2MB)
4. ✅ Preview should show immediately
5. ✅ Success message should appear
6. Refresh page
7. ✅ New logo should still be there

### Test PDF Generation
1. Go to Settings page
2. Click "Generate Test PDF"
3. ✅ PDF should open in new tab
4. ✅ Bengali text should render correctly (not ???)
5. ✅ Organization name from settings should appear
6. ✅ Logo from settings should appear

---

## Troubleshooting

### Bengali Text Still Shows ???

**Check**:
1. Font file exists: `fonts/NotoSansBengali-Regular.ttf`
2. Font file is readable (permissions)
3. TCPDF can write to temp directory
4. Check PHP error logs

**Solution**:
```bash
# Verify font exists
ls -la fonts/NotoSansBengali-Regular.ttf

# Check TCPDF temp directory
php -r "echo sys_get_temp_dir();"

# Make sure it's writable
chmod 755 /path/to/temp
```

### Settings Not Saving

**Check**:
1. `storage` directory exists
2. `storage` directory is writable
3. Check PHP error logs

**Solution**:
```bash
# Create storage directory
mkdir -p storage

# Make it writable
chmod 755 storage

# Check if file was created
ls -la storage/settings.json
```

### Logo Upload Fails

**Check**:
1. `public/images` directory exists
2. `public/images` directory is writable
3. File size < 2MB
4. File type is image
5. Check PHP error logs

**Solution**:
```bash
# Create images directory
mkdir -p public/images

# Make it writable
chmod 755 public/images

# Check upload_max_filesize in php.ini
php -i | grep upload_max_filesize
```

### PDF Generation Fails

**Check**:
1. TCPDF is installed: `composer install`
2. Font file exists
3. Template file exists: `templates/pdf_template.html`
4. Check PHP error logs

**Solution**:
```bash
# Reinstall TCPDF
composer require tecnickcom/tcpdf

# Verify template exists
ls -la templates/pdf_template.html

# Check PHP error log
tail -f /path/to/php/error.log
```

---

## API Endpoints

### GET /api/admin/settings
**Auth**: Required
**Response**:
```json
{
  "success": true,
  "data": {
    "org_name_bn": "জাতীয় ছাত্রশক্তি",
    "org_name_en": "Jatiya Chhatra Shakti",
    "logo_path": "/images/logo.png",
    "updated_at": "2024-01-01 12:00:00"
  }
}
```

### POST /api/admin/settings
**Auth**: Required
**Body**:
```json
{
  "org_name_bn": "নতুন নাম",
  "org_name_en": "New Name"
}
```
**Response**:
```json
{
  "success": true,
  "message": "Settings updated successfully",
  "data": { /* updated settings */ }
}
```

### POST /api/admin/upload-logo
**Auth**: Required
**Content-Type**: multipart/form-data
**Body**: FormData with 'logo' file
**Response**:
```json
{
  "success": true,
  "path": "/images/logo.png",
  "message": "Logo uploaded successfully"
}
```

---

## Production Considerations

### File Permissions
```bash
# Storage directory
chmod 755 storage
chown www-data:www-data storage

# Images directory
chmod 755 public/images
chown www-data:www-data public/images

# Font directory (read-only)
chmod 755 fonts
```

### PHP Configuration
```ini
; php.ini
upload_max_filesize = 2M
post_max_size = 3M
file_uploads = On
```

### Security
- Logo uploads are validated (type, size)
- Settings require admin authentication
- Files saved with safe names
- Directory traversal prevented

---

## Summary

All issues have been fixed:
- ✅ Logo upload works with preview
- ✅ Settings persist across sessions
- ✅ PDF shows Bengali text correctly
- ✅ PDF uses organization settings
- ✅ Professional, clean implementation

The system now has a fully functional settings management system with logo upload and proper Bengali PDF generation.
