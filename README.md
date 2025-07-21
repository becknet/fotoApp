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

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd fotoApp
   ```

2. **Setup environment**
   ```bash
   cp .env.example .env
   # Edit .env file with your database credentials if needed
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
   - Database admin: http://localhost:8081

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
| `make logs` | View container logs |

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

## License

This project is open source and available under the [MIT License](LICENSE).