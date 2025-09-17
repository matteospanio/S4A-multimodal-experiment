# S4A Multimodal Experiment

A Symfony-based web application for conducting multimodal experiments exploring the relationships between music and sensory experiences. This project is specifically designed for the **Science4All** event in Padova, scheduled for the last weekend of September 2025.

## About Science4All

Science4All is an exciting science communication event that will be held in Padova in September 2025. For more information about the event, visit the [official website](https://science4all.it/).

## Project Overview

This application facilitates research into multimodal perception by presenting participants with experiments that explore the connections between:

- **Music and Flavors**: Understanding how musical compositions relate to taste and smell sensations
- **Sensory Cross-mapping**: Investigating how different sensory modalities influence each other

### Key Features

- **Participant Management**: Registration and tracking of experiment participants
- **Experiment Trials**: Two main experiment types:
  - `smells2music`: From olfactory stimuli to musical associations
  - `musics2smell`: From musical stimuli to olfactory associations
- **Song Library**: Management of audio tracks with associated prompts and expected flavor profiles
- **Flavor Profiles**: Categorization system for different sensory experiences
- **Admin Dashboard**: EasyAdmin-powered interface for experiment management
- **Data Collection**: Comprehensive trial tracking and participant response recording

## Technology Stack

- **Framework**: Symfony 7.3
- **Language**: PHP 8.2+
- **Database**: PostgreSQL 16
- **Frontend**: Twig templates with Stimulus and Turbo
- **Admin Interface**: EasyAdmin Bundle
- **Charts**: Chart.js integration
- **Container**: Docker with Docker Compose

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Docker and Docker Compose (recommended)
- PostgreSQL 16 (if not using Docker)

### Quick Start with Docker

1. **Clone the repository**
   ```bash
   git clone https://github.com/matteospanio/S4A-multimodal-experiment.git
   cd S4A-multimodal-experiment
   ```

2. **Start the application with Docker**
   ```bash
   docker-compose up -d
   ```

3. **Install PHP dependencies**
   ```bash
   docker-compose exec app composer install
   ```

4. **Run database migrations**
   ```bash
   docker-compose exec app php bin/console doctrine:migrations:migrate
   ```

5. **Access the application**
   - Main application: http://localhost:8000
   - Admin dashboard: http://localhost:8000/admin

### Manual Installation

1. **Clone and setup**
   ```bash
   git clone https://github.com/matteospanio/S4A-multimodal-experiment.git
   cd S4A-multimodal-experiment
   composer install
   ```

2. **Configure environment**
   ```bash
   cp .env .env.local
   # Edit .env.local with your database credentials
   ```

3. **Setup database**
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

4. **Start the development server**
   ```bash
   symfony server:start
   # or
   php -S localhost:8000 -t public/
   ```

## Configuration

### Environment Variables

Key configuration options in `.env`:

```bash
# Application environment
APP_ENV=dev
APP_SECRET=your_secret_key

# Database configuration
DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=16&charset=utf8"

# Messenger configuration
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0

# Mailer configuration (for notifications)
MAILER_DSN=null://null
```

### Database Schema

The application uses the following main entities:

- **Participant**: Experiment participants with basic demographic information
- **Trial**: Individual experiment sessions with task types and timestamps
- **Song**: Audio tracks with URLs, prompts, and expected flavor associations
- **Flavor**: Sensory categories with names, icons, and descriptions
- **User**: System users for admin access

## Usage

### For Researchers

1. **Access the admin panel** at `/admin`
2. **Setup experiment data**:
   - Add flavor categories with descriptions and icons
   - Upload songs with associated prompts and expected flavors
   - Configure experiment parameters

3. **Monitor experiments**:
   - Track participant sessions
   - Review trial data
   - Export results for analysis

### For Participants

1. **Visit the main application** URL
2. **Complete participant registration**
3. **Follow experiment instructions**
4. **Complete assigned trials** (smells2music or musics2smell)

## Development

### Project Structure

```
S4A-multimodal-experiment/
├── assets/              # Frontend assets (JS, CSS)
├── bin/                 # Console commands
├── config/              # Symfony configuration
├── migrations/          # Database migrations
├── public/              # Web root
├── src/
│   ├── Controller/      # Web controllers
│   ├── Entity/          # Doctrine entities
│   ├── Repository/      # Data repositories
│   └── Service/         # Business logic
├── templates/           # Twig templates
├── tests/               # Test files
├── composer.json        # PHP dependencies
└── docker-compose.yml   # Docker configuration
```

### Running Tests

```bash
# Run all tests
php bin/phpunit

# Run specific test suite
php bin/phpunit tests/Controller/
```

### Code Quality

```bash
# Check code style (if configured)
php bin/console lint:yaml config/
php bin/console lint:twig templates/
```

### Adding New Features

1. **Create entities** using Maker Bundle:
   ```bash
   php bin/console make:entity
   ```

2. **Generate migrations**:
   ```bash
   php bin/console make:migration
   php bin/console doctrine:migrations:migrate
   ```

3. **Create controllers**:
   ```bash
   php bin/console make:controller
   ```

## Deployment

### Production Setup

1. **Set production environment**
   ```bash
   APP_ENV=prod
   ```

2. **Install production dependencies**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. **Build assets**
   ```bash
   php bin/console asset-map:compile
   ```

4. **Clear cache**
   ```bash
   php bin/console cache:clear --env=prod
   ```

### Docker Production

Use the provided Docker configuration with production overrides:

```bash
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```

## Contributing

This project is developed for the Science4All event. If you'd like to contribute:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## Support

For questions about the experiment or technical issues:

- Create an issue in this repository
- Contact the Science4All organizers through their [website](https://science4all.it/)

## License

This project is proprietary software developed specifically for the Science4All event in Padova 2025.

---

**Science4All 2025 - Exploring the Connections Between Senses Through Technology**