# 🔥 API Services - Dynamic API Route Management System

A powerful Laravel-based API service management platform that allows you to dynamically manage API routes, integrate external services, and provide a unified API gateway with admin interface.

## 🎯 Project Overview

This project is an **API Services Management Platform** that provides:

- **Dynamic Route Management**: Add/remove API routes without code changes
- **Service Integrations**: YouTube, Spotify, and other external services
- **Admin Interface**: Filament-based admin panel for route management
- **API Gateway**: Unified API endpoints for multiple services
- **Authentication**: Token-based API authentication system

## 🏗️ Architecture

### Core Components

1. **Dynamic Route System**
   - Database-driven route management
   - Real-time route registration
   - Service grouping (YouTube, Spotify, etc.)

2. **Service Integrations**
   - YouTube Downloader Service
   - Token Management Service
   - Extensible service architecture

3. **Admin Management**
   - Filament admin panel
   - Route CRUD operations
   - Service monitoring

## 🚀 Features

### ✅ Dynamic API Route Management
- **Database-driven routes**: Store route configurations in database
- **Real-time registration**: Routes are registered on application boot
- **Service grouping**: Organize routes by service (YouTube, Spotify, etc.)
- **Active/Inactive states**: Enable/disable routes without deletion
- **HTTP method support**: GET, POST, PUT, PATCH, DELETE
- **Smart Form Validation**: Progressive form fields with dependency validation
- **Controller Auto-Discovery**: Dynamic controller detection from file system
- **Method Auto-Discovery**: Automatic method detection from controller classes

### ✅ Service Integrations
- **YouTube Downloader**: Search, convert to MP3/MP4, get video info
- **Token Management**: API token generation and management
- **Extensible**: Easy to add new services

### ✅ Admin Interface
- **Route Management**: Add, edit, delete routes through web interface
- **Service Monitoring**: View active routes and their status
- **Test Functionality**: Test routes directly from admin panel
- **Bulk Operations**: Manage multiple routes at once
- **Smart Form Fields**: Progressive form with dependency-based field enabling
- **Controller Search**: Searchable dropdown for available controllers
- **Method Auto-Population**: Automatic method detection from selected controllers

### ✅ API Gateway
- **Unified Endpoints**: All services accessible through `/api/{service}/` prefix
- **Authentication**: Token-based API authentication
- **Rate Limiting**: Built-in rate limiting support
- **Error Handling**: Comprehensive error handling and logging

## 📁 Project Structure

```
api-services/
├── app/
│   ├── Filament/                    # Admin panel resources
│   │   ├── Resources/
│   │   │   ├── ApiRouteResource.php    # Route management
│   │   │   ├── ServiceResource.php     # Service management
│   │   │   └── UserResource.php        # User management
│   │   └── Pages/
│   │       └── Services.php            # Services overview page
│   ├── Http/Api/                   # API controllers
│   │   └── Youtube/
│   │       └── Controllers/
│   │           └── YouTubeDownloaderController.php
│   ├── Models/                     # Database models
│   │   ├── ApiRoute.php           # Route configuration model
│   │   ├── Service.php            # Service model
│   │   └── User.php               # User model
│   └── Services/                   # Business logic services
│       ├── DynamicRouteService.php    # Route registration service
│       ├── YouTubeDownloaderService.php # YouTube integration
│       └── TokenService.php           # Token management
├── routes/
│   └── api.php                    # API route definitions
└── database/
    └── migrations/                # Database migrations
```

## 🛠️ Installation

### Prerequisites
- PHP 8.1+
- Laravel 11+
- MySQL/PostgreSQL
- Composer

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd api-services
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Start the application**
   ```bash
   php artisan serve
   npm run dev
   ```

## 📖 Usage

### Admin Interface

1. **Access Admin Panel**
   - Navigate to `/admin`
   - Login with admin credentials

2. **Manage API Routes**
   - Go to "API Routes" section
   - Click "Create" to add new routes
   - Configure route parameters:
     - Service Group (e.g., `youtube`) - Required first
     - Controller Name - Searchable dropdown with available controllers
     - Route Name (e.g., `search`) - Enabled after controller selection
     - Method Name - Auto-populated from selected controller
     - HTTP Method (e.g., `POST`)

3. **Service Management**
   - View all available services
   - Monitor service status
   - Manage service configurations

### API Usage

#### YouTube Service Endpoints

```bash
# Search for videos
POST /api/youtube/search
{
    "query": "music video"
}

# Convert to MP3
POST /api/youtube/convert-to-mp3
{
    "url": "https://www.youtube.com/watch?v=example"
}

# Convert to MP4
POST /api/youtube/convert-to-mp4
{
    "url": "https://www.youtube.com/watch?v=example"
}

# Get video info
POST /api/youtube/get-info
{
    "url": "https://www.youtube.com/watch?v=example"
}
```

#### Authentication

```bash
# Get API token
POST /api/auth/token
{
    "email": "user@example.com",
    "password": "password"
}

# Use token in requests
Authorization: Bearer {your-token}
```

## 🔧 Configuration

### Environment Variables

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=api_services
DB_USERNAME=root
DB_PASSWORD=

# YouTube API (if needed)
YOUTUBE_API_KEY=your_youtube_api_key

# App settings
APP_NAME="API Services"
APP_ENV=local
APP_DEBUG=true
```

### Service Configuration

Each service can be configured through the admin interface:

1. **Service Groups**: Organize routes by service type
2. **Route Parameters**: Configure endpoint paths and methods
3. **Authentication**: Set up service-specific authentication
4. **Rate Limiting**: Configure rate limits per service

## 🎨 Admin Interface Features

### Route Management
- ✅ **List View**: All routes with simplified columns and filters
- ✅ **Create/Edit**: Add new routes or modify existing ones
- ✅ **Bulk Actions**: Delete multiple routes
- ✅ **Test Routes**: Test endpoints directly from admin
- ✅ **Status Monitoring**: Active/inactive route status
- ✅ **Smart Form Validation**: Progressive form with dependency validation
- ✅ **Controller Auto-Discovery**: Dynamic controller detection
- ✅ **Method Auto-Population**: Automatic method detection from controllers

### Service Overview
- ✅ **Service List**: View all available services
- ✅ **Service Status**: Monitor service health
- ✅ **Route Count**: Number of routes per service
- ✅ **Quick Actions**: Quick access to service management

## 🔄 Dynamic Route System

### How It Works

1. **Database Storage**: Route configurations stored in `api_routes` table
2. **Boot Registration**: Routes registered on application boot
3. **Controller Resolution**: Dynamic controller class loading
4. **Route Registration**: Laravel Route facade registration
5. **Error Handling**: Graceful handling of missing controllers
6. **Form Validation**: Progressive form with dependency-based field enabling
7. **Controller Discovery**: File system scanning for available controllers
8. **Method Detection**: Reflection-based method discovery from controller classes

### Route Configuration

```php
// Example route record
ApiRoute::create([
    'service_group' => 'youtube',
    'route_name' => 'search',
    'controller_name' => 'YouTubeDownloaderController',
    'method_name' => 'search',
    'http_method' => 'POST',
    'is_active' => true,
    'is_default' => false
]);
```

## 🚀 Adding New Services

### Recent Improvements (Latest Update)

The API Route management system has been enhanced with:

- **Progressive Form Validation**: Fields are enabled/disabled based on dependencies
- **Controller Auto-Discovery**: Dynamic detection of available controllers from file system
- **Searchable Controller Selection**: Dropdown with search functionality for controller names
- **Method Auto-Population**: Automatic detection of available methods from selected controllers
- **Simplified Table View**: Cleaner table columns with essential information
- **Dependency-Based Validation**: Form fields are progressively enabled as dependencies are met

### 1. Create Service Controller

```php
// app/Http/Api/Spotify/Controllers/SpotifyController.php
namespace App\Http\Api\Spotify\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SpotifyController extends Controller
{
    public function play(Request $request): JsonResponse
    {
        // Your Spotify integration logic
        return response()->json(['success' => true]);
    }
}
```

### 2. Add Routes via Admin

1. Go to "API Routes" in admin panel
2. Click "Create"
3. Fill in service details (progressive form):
   - Service Group: `spotify` (required first)
   - Controller Name: Select from available controllers (searchable dropdown)
   - Route Name: `play` (enabled after controller selection)
   - Method Name: Auto-populated from selected controller
   - HTTP Method: `POST`

### 3. Routes Automatically Available

```bash
POST /api/spotify/play
{
    "track_id": "spotify:track:example"
}
```

## 🔒 Security Features

- **Token Authentication**: API token-based authentication
- **Rate Limiting**: Built-in rate limiting per endpoint
- **Input Validation**: Comprehensive request validation
- **Error Handling**: Secure error responses
- **Logging**: Detailed request/response logging

## 📊 Monitoring & Logging

- **Service Health**: Monitor service availability
- **Route Statistics**: Track route usage
- **Error Logging**: Comprehensive error tracking
- **Performance Metrics**: Response time monitoring

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License.

## 🆘 Support

For support and questions:
- Create an issue in the repository
- Check the documentation
- Contact the development team

---

**🔥 Built with Laravel, Filament, and ❤️** 