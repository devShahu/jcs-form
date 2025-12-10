# Free Deployment Guide with Custom Domain

## 🎯 Recommended Stack (100% Free)

| Component | Service | Free Tier |
|-----------|---------|-----------|
| **Frontend** | Vercel or Netlify | Unlimited |
| **Backend** | Railway or Render | 500 hours/month |
| **Domain** | Freenom or Cloudflare | Free |

---

## Option 1: Vercel (Frontend) + Railway (Backend)

### Step 1: Deploy Frontend to Vercel (Free)

1. **Create Vercel Account**
   - Go to https://vercel.com
   - Sign up with GitHub

2. **Push Code to GitHub**
   ```bash
   git init
   git add .
   git commit -m "Initial commit"
   git remote add origin https://github.com/YOUR_USERNAME/jcs-form.git
   git push -u origin main
   ```

3. **Import to Vercel**
   - Click "New Project"
   - Import your GitHub repo
   - Set build settings:
     - Framework: Vite
     - Build Command: `npm run build`
     - Output Directory: `dist`

4. **Add Environment Variable**
   - Go to Settings → Environment Variables
   - Add: `VITE_API_URL` = `https://your-backend.railway.app/api`

5. **Deploy**
   - Click Deploy
   - Your frontend is live at: `https://your-app.vercel.app`

### Step 2: Deploy Backend to Railway (Free)

1. **Create Railway Account**
   - Go to https://railway.app
   - Sign up with GitHub

2. **Create New Project**
   - Click "New Project"
   - Select "Deploy from GitHub repo"
   - Choose your repo

3. **Configure PHP Service**
   - Railway auto-detects PHP
   - Add these environment variables:
     ```
     ADMIN_USERNAME=admin
     ADMIN_PASSWORD=your_secure_password
     APP_ENV=production
     ```

4. **Add Nixpacks Config**
   Create `nixpacks.toml` in your project root:
   ```toml
   [phases.setup]
   nixPkgs = ["php83", "php83Extensions.gd", "php83Extensions.mbstring", "nodejs_20", "npm"]

   [phases.install]
   cmds = ["composer install --no-dev", "npm install"]

   [start]
   cmd = "php -S 0.0.0.0:$PORT -t ."
   ```

5. **Deploy**
   - Railway will auto-deploy
   - Get your URL: `https://your-app.railway.app`

### Step 3: Add Custom Domain (Free)

**Option A: Freenom (Free Domain)**
1. Go to https://freenom.com
2. Search for free domains (.tk, .ml, .ga, .cf, .gq)
3. Register your domain (free for 12 months)
4. Add DNS records:
   - Frontend: CNAME → `cname.vercel-dns.com`
   - Backend: CNAME → `your-app.railway.app`

**Option B: Use Your Own Domain**
1. In Vercel: Settings → Domains → Add your domain
2. In Railway: Settings → Domains → Add custom domain
3. Update DNS at your registrar

---

## Option 2: Netlify (Frontend) + Render (Backend)

### Step 1: Deploy Frontend to Netlify (Free)

1. **Create Netlify Account**
   - Go to https://netlify.com
   - Sign up with GitHub

2. **Import Project**
   - Click "Add new site" → "Import an existing project"
   - Connect GitHub and select repo

3. **Build Settings**
   ```
   Build command: npm run build
   Publish directory: dist
   ```

4. **Environment Variables**
   - Site settings → Environment variables
   - Add: `VITE_API_URL` = `https://your-backend.onrender.com/api`

5. **Deploy**
   - Your site is live at: `https://your-app.netlify.app`

### Step 2: Deploy Backend to Render (Free)

1. **Create Render Account**
   - Go to https://render.com
   - Sign up with GitHub

2. **Create Web Service**
   - Click "New" → "Web Service"
   - Connect your repo

3. **Configure Service**
   ```
   Name: jcs-backend
   Environment: Docker
   Region: Singapore (closest to Bangladesh)
   Instance Type: Free
   ```

4. **Create Dockerfile**
   ```dockerfile
   FROM php:8.2-cli

   # Install extensions
   RUN apt-get update && apt-get install -y \
       libpng-dev \
       libjpeg-dev \
       libfreetype6-dev \
       nodejs \
       npm \
       && docker-php-ext-configure gd --with-freetype --with-jpeg \
       && docker-php-ext-install gd

   # Install Composer
   COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

   WORKDIR /app
   COPY . .

   RUN composer install --no-dev
   RUN npm install

   EXPOSE 10000
   CMD ["php", "-S", "0.0.0.0:10000", "-t", "."]
   ```

5. **Environment Variables**
   ```
   ADMIN_USERNAME=admin
   ADMIN_PASSWORD=your_secure_password
   APP_ENV=production
   ```

6. **Deploy**
   - Render will build and deploy
   - URL: `https://jcs-backend.onrender.com`

### Step 3: Add Custom Domain

1. **In Netlify**
   - Site settings → Domain management → Add custom domain
   - Follow DNS instructions

2. **In Render**
   - Settings → Custom Domains → Add domain
   - Add CNAME record

---

## Option 3: Single VPS (Cheapest Long-term)

### Oracle Cloud Free Tier (Always Free)

1. **Create Oracle Cloud Account**
   - Go to https://cloud.oracle.com
   - Sign up (requires credit card but won't charge)

2. **Create Free VM**
   - Compute → Instances → Create Instance
   - Shape: VM.Standard.E2.1.Micro (Always Free)
   - OS: Ubuntu 22.04

3. **Install Stack**
   ```bash
   # SSH into your VM
   ssh ubuntu@your-vm-ip

   # Install PHP
   sudo apt update
   sudo apt install php8.1 php8.1-gd php8.1-mbstring php8.1-xml php8.1-curl

   # Install Node.js
   curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
   sudo apt install nodejs

   # Install Composer
   curl -sS https://getcomposer.org/installer | php
   sudo mv composer.phar /usr/local/bin/composer

   # Install Nginx
   sudo apt install nginx

   # Install Certbot (for free SSL)
   sudo apt install certbot python3-certbot-nginx
   ```

4. **Deploy Your App**
   ```bash
   cd /var/www
   git clone https://github.com/YOUR_USERNAME/jcs-form.git
   cd jcs-form
   composer install --no-dev
   npm install
   npm run build
   ```

5. **Configure Nginx**
   ```nginx
   server {
       listen 80;
       server_name yourdomain.com;
       root /var/www/jcs-form/dist;
       index index.html;

       location / {
           try_files $uri $uri/ /index.html;
       }

       location /api {
           proxy_pass http://127.0.0.1:8000;
           proxy_set_header Host $host;
           proxy_set_header X-Real-IP $remote_addr;
       }
   }
   ```

6. **Start Backend**
   ```bash
   # Create systemd service
   sudo nano /etc/systemd/system/jcs-backend.service
   ```
   ```ini
   [Unit]
   Description=JCS Backend
   After=network.target

   [Service]
   Type=simple
   User=www-data
   WorkingDirectory=/var/www/jcs-form
   ExecStart=/usr/bin/php -S 127.0.0.1:8000 -t .
   Restart=always

   [Install]
   WantedBy=multi-user.target
   ```
   ```bash
   sudo systemctl enable jcs-backend
   sudo systemctl start jcs-backend
   ```

7. **Add SSL (Free)**
   ```bash
   sudo certbot --nginx -d yourdomain.com
   ```

---

## 🌐 Custom Domain Setup

### Free Domain Options

| Provider | Domains | Duration |
|----------|---------|----------|
| Freenom | .tk, .ml, .ga, .cf, .gq | 12 months free |
| Cloudflare Registrar | .com, .net, etc. | At cost (~$8/year) |
| GitHub Student | .me | Free with student pack |

### DNS Configuration

**For Vercel:**
```
Type: CNAME
Name: @
Value: cname.vercel-dns.com
```

**For Netlify:**
```
Type: CNAME
Name: @
Value: your-site.netlify.app
```

**For Railway:**
```
Type: CNAME
Name: api
Value: your-app.railway.app
```

### Cloudflare (Free CDN + SSL)

1. Sign up at https://cloudflare.com
2. Add your domain
3. Update nameservers at your registrar
4. Enable:
   - SSL/TLS: Full (strict)
   - Always Use HTTPS: On
   - Auto Minify: On
   - Brotli: On

---

## 📋 Pre-Deployment Checklist

### 1. Update Environment Variables
```bash
# .env.production
VITE_API_URL=https://api.yourdomain.com/api
ADMIN_USERNAME=admin
ADMIN_PASSWORD=CHANGE_THIS_TO_SECURE_PASSWORD
APP_ENV=production
```

### 2. Build Frontend
```bash
npm run build
```

### 3. Test Production Build
```bash
npm run preview
```

### 4. Update CORS in Backend
Edit `api/index.php`:
```php
$allowedOrigins = [
    'https://yourdomain.com',
    'https://www.yourdomain.com'
];
$origin = $request->getHeaderLine('Origin');
if (in_array($origin, $allowedOrigins)) {
    // Allow origin
}
```

### 5. Secure Admin Credentials
- Change default password
- Use strong password (16+ characters)
- Consider adding rate limiting

---

## 🚀 Quick Deploy Commands

### Vercel
```bash
npm i -g vercel
vercel --prod
```

### Netlify
```bash
npm i -g netlify-cli
netlify deploy --prod
```

### Railway
```bash
npm i -g @railway/cli
railway login
railway up
```

---

## 💰 Cost Summary

| Service | Monthly Cost |
|---------|-------------|
| Vercel (Frontend) | $0 |
| Railway (Backend) | $0 (500 hrs free) |
| Freenom Domain | $0 |
| Cloudflare CDN | $0 |
| **Total** | **$0** |

### If You Need More

| Upgrade | Cost |
|---------|------|
| Railway Hobby | $5/month |
| Custom .com domain | $8-12/year |
| Vercel Pro | $20/month |

---

## 🔧 Troubleshooting

### CORS Errors
- Check API URL in frontend
- Verify CORS headers in backend
- Check domain in allowed origins

### 502 Bad Gateway
- Check backend is running
- Check port configuration
- Check logs: `railway logs` or Render dashboard

### SSL Certificate Issues
- Wait 24-48 hours for DNS propagation
- Use Cloudflare for instant SSL
- Check domain verification

### Puppeteer Not Working
- Railway/Render may need Chromium
- Add to Dockerfile:
  ```dockerfile
  RUN apt-get install -y chromium
  ENV PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium
  ```

---

## 📚 Resources

- Vercel Docs: https://vercel.com/docs
- Railway Docs: https://docs.railway.app
- Netlify Docs: https://docs.netlify.com
- Render Docs: https://render.com/docs
- Cloudflare Docs: https://developers.cloudflare.com

---

**Recommended for Beginners**: Vercel + Railway + Freenom domain

**Recommended for Production**: Oracle Cloud VPS + Cloudflare + Custom domain

Both options are 100% free!
