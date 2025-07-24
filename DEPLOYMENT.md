# Railway Deployment Guide

## Prerequisites
- GitHub account
- Railway account (sign up at [railway.app](https://railway.app))

## Step 1: Prepare Your Repository
1. Make sure all your changes are committed and pushed to GitHub
2. Your repository should have these files:
   - `railway.json`
   - `nixpacks.toml`
   - `Procfile`
   - `composer.json`

## Step 2: Deploy to Railway

### 2.1 Connect to Railway
1. Go to [railway.app](https://railway.app)
2. Sign up/Sign in with your GitHub account
3. Click "New Project"
4. Select "Deploy from GitHub repo"
5. Choose your repository

### 2.2 Add Database
1. In your Railway project, click "New"
2. Select "Database" → "MySQL"
3. Railway will automatically add the database environment variables

### 2.3 Configure Environment Variables
Add these environment variables in Railway:

```
APP_NAME="API Services"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.railway.app

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
# DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD will be auto-added by Railway

CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 2.4 Generate App Key
1. In Railway, go to your app service
2. Click "Variables" tab
3. Add: `APP_KEY=base64:your-generated-key`
4. Or let Railway generate it automatically

### 2.5 Deploy
1. Railway will automatically detect Laravel and deploy
2. The deployment will use the `nixpacks.toml` configuration
3. Wait for the build to complete

## Step 3: Run Migrations
1. In Railway, go to your app service
2. Click "Deployments" tab
3. Click on the latest deployment
4. Click "View Logs"
5. Add a custom command: `php artisan migrate --force`

## Step 4: Access Your App
1. Railway will provide a URL like: `https://your-app-name.railway.app`
2. Your Filament admin will be at: `https://your-app-name.railway.app/admin`

## Troubleshooting

### If deployment fails:
1. Check the build logs in Railway
2. Make sure all environment variables are set
3. Verify your `composer.json` is valid

### If database connection fails:
1. Make sure the database service is running
2. Check that database environment variables are set
3. Run migrations manually

### If app key is missing:
1. Generate a new key: `php artisan key:generate`
2. Add it to Railway environment variables

## Railway Benefits
- ✅ Automatic HTTPS
- ✅ Global CDN
- ✅ Automatic deployments from GitHub
- ✅ Built-in database support
- ✅ Good Laravel support
- ✅ Free tier available 