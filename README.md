<p align="center">
  <img src="assets/images/logo-s4a.svg" alt="Science for All Logo" width="300">
</p>

A Symfony-based web application for conducting multimodal experiments exploring the relationships between music and sensory experiences. This project is specifically designed for the **Science4All** event in Padova, scheduled for the last weekend of September 2025.

> **Data Privacy Notice**: All participant data are collected anonymously and are processed and analyzed only in aggregated form to ensure privacy protection.

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
- **Language**: PHP 8.3+
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
   # Load foundry fixtures for development data
   php bin/console foundry:make-story:main
   ```

4. **Start the development server**
   ```bash
   symfony server:start
   ```

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

4. **Export trial data**:
   - Navigate to "Music to Aroma" or "Aroma to Music" trial lists in the admin panel
   - Use the "Export CSV" button to download all trial results
   - Filter trials by date using the date filter before exporting
   - CSV files include all trial data: ID, stimuli, choices, match status, time intervals, task info, and timestamps
   - Alternatively, append `?date=YYYY-MM-DD` to the export URL to filter by a specific date

### For Participants

1. **Visit the main application** URL
2. **Complete participant registration**
3. **Follow experiment instructions**
4. **Complete assigned trials** (smells2music or musics2smell)

## Development

### Running Tests

```bash
# Run all tests with detailed output
symfony php vendor/bin/phpunit --testdox
```

### Code Quality

```bash
# Check code style (if configured)
php bin/console lint:yaml config/
php bin/console lint:twig templates/

# Run Rector for automated code refactoring and modernization
vendor/bin/rector process --dry-run

# Apply Rector changes
vendor/bin/rector process
```

### Development Tools

For a better development experience, you can install pre-commit hooks:

```bash
# Install pre-commit (requires Python)
pip install pre-commit

# Install the git hook scripts
pre-commit install

# Optionally, run against all files
pre-commit run --all-files
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

## Bibliography

If you use this software or reference this work, please cite:

```bibtex
@article{spanio_frontiers_2025,
  language = {en},
  author = {Spanio, Matteo and Zampini, Massimiliano and Rodà, Antonio and Pierucci, Franco},
  title = {A multimodal symphony: integrating taste and sound through generative AI},
  journal = {Frontiers in Computer Science},
  volume = {Volume 7 - 2025},
  year = {2025},
  url = {https://www.frontiersin.org/journals/computer-science/articles/10.3389/fcomp.2025.1575741},
  doi = {10.3389/fcomp.2025.1575741},
  issn = {2624-9898}
}
```

## Support

For questions about the experiment or technical issues:

- Create an issue in this repository

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
