#!/usr/bin/env bash
# Description: Deploy the application to the server
export APP_ENV=prod

# Clear the cache
echo "Clearing cache..."
rm -rf var/cache/*

# Install composer dependencies
echo "Installing composer dependencies..."
composer install --no-dev --optimize-autoloader

# Install assets
echo "Installing assets..."
rm -rf public/assets
symfony console asset-map:compile --no-interaction --env=prod

# Serve the application
echo "Starting the Symfony server..."
APP_ENV=prod symfony server:start --port=8000
