# JCS Membership Form - PDF Form Filler

A modern web application for filling out JCS membership forms and generating professionally formatted PDF documents.

## Project Structure

```
.
├── api/                    # PHP REST API backend
│   ├── src/               # PHP source code
│   ├── controllers/       # API controllers
│   └── index.php          # API entry point
├── src/                   # Frontend source code (React)
│   ├── components/        # React components
│   ├── main.jsx          # Frontend entry point
│   └── index.html        # HTML template
├── public/               # Built frontend assets (generated)
├── storage/              # User-generated content
│   ├── submissions/      # Generated PDFs
│   ├── photos/          # Uploaded photos
│   └── temp/            # Temporary files
├── config/              # Configuration files
├── fonts/               # Custom fonts (optional)
├── composer.json        # PHP dependencies
├── package.json         # Node.js dependencies
└── .env                 # Environment configuration
```

## Prerequisites

- **PHP**: 8.0 or higher
- **Composer**: For PHP dependency management
- **Node.js**: 18.0 or higher
- **npm**: For frontend dependency management

### Required PHP Extensions

- php-gd (for image processing)
- php-mbstring (for Unicode support)
- php-zip (for PDF library)
- php-xml (for PDF library)

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd jcs-form-filler
```

### 2. Install Backend Dependencies

```bash
composer install
```

### 3. Install Frontend Dependencies

```bash
npm install
```

### 4. Download Bengali Fonts

Follow the instructions in `fonts/README.md` to download the required Bengali fonts:
- Nikosh.ttf
- NotoSansBengali-Regular.ttf

**Quick download (PowerShell on Windows):**
```powershell
cd fonts
Invoke-WebRequest -Uri "https://github.com/potasiyam/Bangla-Fonts/raw/master/Nikosh.ttf" -OutFile "Nikosh.ttf"
Invoke-WebRequest -Uri "https://github.com/notofonts/bengali/raw/main/fonts/NotoSansBengali/hinted/ttf/NotoSansBengali-Regular.ttf" -OutFile "NotoSansBengali-Regular.ttf"
cd ..
```

### 5. Configure Environment

The `.env` file is already created with default settings. Adjust as needed for your environment.

### 6. Set Directory Permissions

Ensure the storage directories are writable:

**Linux/Mac:**
```bash
chmod -R 775 storage
```

**Windows:**
Right-click on the `storage` folder → Properties → Security → Edit permissions to allow write access.

## Development

### Start the Frontend Development Server

```bash
npm run dev
```

The frontend will be available at `http://localhost:3000`

### Start the PHP Backend Server

```bash
php -S localhost:8000 -t api
```

The API will be available at `http://localhost:8000/api`

### Test the API

Visit `http://localhost:8000/api/health` to verify the API is running.

## Building for Production

### Build Frontend

```bash
npm run build
```

This will generate optimized assets in the `public/` directory.

### Deploy

1. Copy all files to your web server
2. Point your web server document root to the `public/` directory
3. Configure your web server to route API requests to `api/index.php`
4. Ensure `storage/` directories are writable by the web server

### Web Server Configuration

**Apache (.htaccess in public/):**
```apache
RewriteEngine On
RewriteBase /

# API requests
RewriteRule ^api/(.*)$ ../api/index.php [L,QSA]

# Frontend SPA
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.html [L]
```

**Nginx:**
```nginx
location /api {
    try_files $uri /api/index.php$is_args$args;
}

location / {
    try_files $uri $uri/ /index.html;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
}
```

## Technology Stack

### Frontend
- React 18
- Vite (build tool)
- Tailwind CSS (styling)
- Axios (HTTP client)
- Zod (validation)
- Framer Motion (animations)

### Backend
- PHP 8.0+
- Slim Framework (routing)
- mPDF 8.2+ (PDF generation with Bengali support)
- Intervention/Image (image processing)
- DotEnv (environment configuration)

## API Endpoints

- `GET /api/health` - Health check
- `POST /api/validate` - Validate form data
- `POST /api/upload-photo` - Upload photo
- `POST /api/submit` - Submit form and generate PDF
- `GET /api/download/:id` - Download generated PDF
- `GET /api/config` - Get form configuration

## Development Workflow

1. Frontend runs on port 3000 with hot reload
2. Backend runs on port 8000
3. Vite proxy forwards `/api` requests to the backend
4. Make changes and see them reflected immediately

## Troubleshooting

### Composer install fails
- Ensure PHP 8.0+ is installed: `php -v`
- Check required extensions: `php -m`

### npm install fails
- Ensure Node.js 18+ is installed: `node -v`
- Clear npm cache: `npm cache clean --force`

### Bengali text shows as "?" in PDFs
- mPDF uses built-in DejaVu Sans font (supports Bengali)
- Run test: `php test-mpdf-bengali.php`
- See TEST_BENGALI_PDF.md for detailed testing guide
- No external fonts needed - works out of the box

### Storage errors
- Ensure `storage/` directories exist and are writable
- Check web server user has write permissions

## License

[Your License Here]

## Support

For issues and questions, please contact [Your Contact Info]
