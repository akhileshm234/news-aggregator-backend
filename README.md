# News Aggregator API

A powerful RESTful API built with Laravel that aggregates news from multiple sources, providing a personalized news feed experience. The system pulls articles from various news APIs, stores them efficiently, and offers advanced search and filtering capabilities.

## Features

### User Authentication

-   User registration and login with Laravel Sanctum
-   Secure password reset functionality
-   Token-based authentication for API access

### Article Management

-   Paginated article listings
-   Advanced search and filtering (keyword, date, category, source)
-   Detailed single article view
-   Efficient data storage and indexing

### User Preferences

-   Customizable news sources
-   Category preferences
-   Favorite authors
-   Personalized news feed based on user preferences

### Data Aggregation

-   Automated article fetching from multiple news sources
-   Regular updates via scheduled commands
-   Local data storage for optimal performance

## Setup Instructions

### Prerequisites

-   Docker
-   Docker Compose
-   Git

### Installation

1. Clone the repository:

```bash
git clone https://github.com/akhileshm234/news-aggregator-backend.git
cd news-aggregator-backend
```

2. Copy the docker-compose configuration file:

```bash
cp docker-compose.example.yml docker-compose.yml
```

3. (Optional) Update email credentials in docker-compose.yml if you want to test password reset functionality:

```yaml
MAIL_MAILER: smtp
MAIL_HOST: smtp.gmail.com
MAIL_PORT: 587
MAIL_USERNAME: your_email@gmail.com
MAIL_PASSWORD: your_app_password
MAIL_ENCRYPTION: null
MAIL_FROM_ADDRESS: your_email@gmail.com
```

4. Start the application (this will automatically install dependencies, run migrations, and fetch articles):

```bash
docker-compose up -d
```

The application will be available at `http://localhost:8000`

### (Optional) Create Default User

If you want to create a default user, run:

```bash
docker-compose exec app php artisan db:seed --class=UsersTableSeeder
```

Alternatively, you can register a new user through the `/api/register` endpoint.

### API Keys

The docker-compose.yml file already includes working API keys for:

-   NewsAPI
-   The Guardian
-   New York Times

No additional configuration is required for the news sources.

### Running Tests

```bash
docker-compose exec app php artisan test
```

## API Documentation

Access the API documentation at:

-   Local: `http://localhost:8000/api/documentation`

## Additional Notes

### News Sources

The system currently integrates with the following news APIs:

-   NewsAPI
-   The Guardian
-   New York Times

### Caching Strategy

-   Article data is cached for 15 minutes
-   User preferences are cached until modified
-   Search results are cached based on query parameters

### Rate Limiting

-   API endpoints are rate-limited to 60 requests per minute per user
-   Unauthenticated endpoints are limited to 30 requests per minute

### Security Measures

-   API authentication using Laravel Sanctum
-   Input validation and sanitization
-   Protection against SQL injection and XSS attacks
-   CORS configuration for frontend integration

### Database Credentials

The application comes with pre-configured database settings in docker-compose.yml:

```yaml
DB_HOST: mysql
DB_PORT: 3306
DB_USERNAME: akhileshm
DB_PASSWORD: E7649aksl
DB_DATABASE: laravel
```

These credentials are automatically configured when you run `docker-compose up`. No additional database setup is required as the MySQL container will be created with these credentials.

> **Note**: In a production environment, you should change these credentials to more secure values.

### System Configuration

The application uses several services for optimal performance:

#### Redis Configuration

Redis is used for session management and caching:

```yaml
SESSION_DRIVER: redis
REDIS_HOST: redis
REDIS_PORT: 6379
REDIS_PASSWORD: null
```

Redis is automatically configured when running with Docker.

#### Queue Configuration

The application uses database queue driver for background jobs:

```yaml
QUEUE_CONNECTION: database
```

Background jobs include:

-   Fetching articles from news sources
-   Sending password reset emails
-   Processing user preferences

#### File Storage

Local file system is used for storage:

```yaml
FILESYSTEM_DISK: local
```

All these services are automatically configured and started when running `docker-compose up`. No additional setup is required.

### Automatic Setup Process

When you run `docker-compose up`, the following commands are automatically executed in sequence:

```bash
# 1. Install PHP dependencies
composer install

# 2. Cache configuration
php artisan config:cache

# 3. Generate application key
php artisan key:generate

# 4. Run database migrations
php artisan migrate --force

# 5. Fetch initial articles from news sources
php artisan app:fetch-articles

# 6. Start Laravel development server
php artisan serve --host=0.0.0.0 --port=8000
```

This automated process ensures that:

-   All dependencies are properly installed
-   The application is correctly configured
-   The database is structured
-   Initial news articles are fetched
-   The API server is started and accessible on port 8000

> **Note**: You don't need to run these commands manually - they're all handled automatically by Docker.
