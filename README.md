# FotoApp 📸

A secure photo sharing application built with PHP 8.3, MySQL 8, and Bootstrap 5.3. Users can upload, view, edit, and delete photos with automatic thumbnail generation and owner-based access control.

## Features

- 🔒 **Secure Authentication** - User registration and login with session management
- 📱 **Responsive Design** - Mobile-friendly interface with Bootstrap 5.3
- 📸 **Photo Upload** - Support for JPEG, PNG, GIF, WebP up to 10MB
- 🖼️ **Thumbnail Generation** - Automatic 300px thumbnails using GD library
- 🎯 **Owner Controls** - Only photo owners can edit/delete their content
- 🌐 **Public Gallery** - Browse all photos without authentication
- 🔐 **Security First** - CSRF protection, XSS prevention, SQL injection protection
- 🎨 **Rich Metadata** - Title, description, and location for each photo

## Tech Stack

- **Backend**: PHP 8.3 with strict types
- **Database**: MySQL 8 with utf8mb4 encoding
- **Frontend**: HTML5, Bootstrap 5.3, Vanilla JavaScript
- **Image Processing**: GD library (with Imagick fallback)
- **Architecture**: MVC pattern with PSR-12 coding standards

## Quick Start

### Prerequisites

- Docker and Docker Compose
- Git
- **Note**: This project is optimized for Apple Silicon Macs (ARM64) but also works on Intel/AMD64

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/becknet/fotoApp.git
   cd fotoApp
   ```

2. **Setup environment**
   ```bash
   cp .env.example .env
   # The default settings work out of the box for Docker development
   ```

3. **Start the development environment**
   ```bash
   make dev
   ```

4. **Install PHP dependencies**
   ```bash
   make install
   ```

5. **Access the application**
   - Main application: http://localhost:8080
   - phpMyAdmin: http://localhost:8081 (username: `app_user`, password: `secure_password`)
   - Database shell: `make db-shell` for command line access

## Development Commands

| Command | Description |
|---------|-------------|
| `make dev` | Start Docker development stack |
| `make stop` | Stop Docker containers |
| `make install` | Install PHP dependencies |
| `make test` | Run PHPUnit tests |
| `make format` | Format code (PSR-12 compliance) |
| `make analyse` | Run static analysis (PHPStan level 8) |
| `make shell` | Access PHP container shell |
| `make db-shell` | Access MySQL shell |
| `make db-backup` | Create timestamped database backup |
| `make db-restore FILE=backup.sql` | Restore database from backup file |
| `make logs` | View container logs |

## Getting Started Guide

### First Time Setup

1. **Start the containers**: `make dev`
2. **Install dependencies**: `make install` 
3. **Open the app**: Visit http://localhost:8080
4. **Create an account**: Click "Register" to create your first user
5. **Upload photos**: Once logged in, use "Upload Photo" to add images
6. **Browse gallery**: View all uploaded photos in the public gallery

### Development Workflow

- **Making changes**: Edit files locally, changes are reflected immediately
- **View logs**: `make logs` to debug issues
- **Access database**: `make db-shell` for CLI or http://localhost:8081 for phpMyAdmin
- **Backup/restore**: `make db-backup` and `make db-restore FILE=backup.sql`
- **Code quality**: Run `make format` and `make analyse` before committing

## Project Structure

```
fotoApp/
├── app/
│   ├── Controllers/     # Request handlers
│   ├── Models/          # Database models
│   ├── Views/           # Template files
│   ├── Middleware/      # Request middleware
│   ├── Database.php     # PDO connection handler
│   ├── Router.php       # URL routing
│   ├── Session.php      # Session management
│   ├── Csrf.php         # CSRF protection
│   ├── View.php         # Template engine
│   ├── FileUpload.php   # File handling
│   └── ImageProcessor.php # Thumbnail generation
├── config/
│   └── app.php          # Application configuration
├── database/
│   └── migrations/      # Database schema
├── docker/              # Docker configuration
├── public/              # Web root
│   ├── css/            # Stylesheets
│   ├── js/             # JavaScript files
│   └── index.php       # Application entry point
├── tests/               # PHPUnit tests
├── uploads/             # User uploaded files
└── vendor/             # Composer dependencies
```

## Security Features

- **Password Security**: bcrypt hashing with cost factor 12
- **Session Security**: Secure cookies, HttpOnly, SameSite protection
- **CSRF Protection**: Tokens on all state-changing operations
- **XSS Prevention**: Proper output escaping with `htmlspecialchars()`
- **SQL Injection Protection**: PDO prepared statements only
- **File Upload Security**: Type validation, size limits, secure naming
- **Access Control**: Owner-only permissions for photo management

## Database Schema

### Users Table
- `id` - Primary key
- `name` - Display name (max 60 chars)
- `email` - Unique login identifier
- `password_hash` - bcrypt hashed password
- `created_at` - Registration timestamp

### Photos Table
- `id` - Primary key
- `user_id` - Foreign key to users table
- `title` - Photo title (max 140 chars)
- `description` - Optional description text
- `location` - Optional location info
- `file_path` - Path to original image
- `thumb_path` - Path to thumbnail
- `created_at` - Upload timestamp

## File Organization

Photos are organized in a date-based directory structure:
```
uploads/
└── YYYY/
    └── MM/
        ├── uuid-original.ext
        └── uuid-original_thumb.ext
```

## Troubleshooting

### Common Issues

**Docker build fails**: 
- Ensure Docker Desktop is running
- Try `docker system prune -f` to clean up
- For Apple Silicon: Make sure ARM64 support is enabled

**Database connection errors**:
- Check containers are running: `docker-compose ps`
- Verify database is ready: `make logs`
- Database takes ~30 seconds to initialize on first run

**Permission errors**:
- Ensure uploads directory is writable: `chmod 755 uploads/`
- Check Docker has file access permissions

**PHP errors**:
- View container logs: `docker-compose logs web`
- Access container: `make shell` to debug

### Performance Tips

- **Image optimization**: Large images are automatically resized
- **Development**: Use `make logs` to monitor performance
- **Production**: Enable PHP OPcache and set `APP_DEBUG=false`

## Testing

Run the test suite:
```bash
make test
```

Run static analysis:
```bash
make analyse
```

Format code:
```bash
make format
```

## Contributing

1. Follow PSR-12 coding standards
2. Use strict types (`declare(strict_types=1)`) in all PHP files
3. Write tests for new features
4. Ensure all security requirements are met
5. Run `make format` before committing

## Recent Updates

### v1.2.0 - Latest
- ✅ **Password change functionality** for logged-in users with secure validation
- ✅ **phpMyAdmin integration** for visual database management
- ✅ **Database backup/restore** commands via Makefile
- ✅ **Enhanced dropdown navigation** with user account options

### v1.1.0
- ✅ **Fixed namespace issues** in view templates
- ✅ **Improved navbar layout** with proper user greeting alignment
- ✅ **Added Apple Silicon support** for ARM64 Docker containers
- ✅ **Enhanced error handling** and debugging capabilities
- ✅ **Working photo upload** with automatic thumbnail generation

### v1.0.0 - Initial Release
- ✅ Complete MVC architecture with security-first approach
- ✅ User authentication and session management
- ✅ Photo upload, editing, and gallery functionality
- ✅ Docker development environment
- ✅ Comprehensive testing and code quality tools

## License

This project is open source and available under the [MIT License](LICENSE).

---

**Made with ❤️ using PHP 8.3, MySQL 8, and Bootstrap 5.3**