#!/bin/bash

# Load .env variables
DB_NAME=$(grep DB_DATABASE .env | cut -d '=' -f2)
DB_USER=$(grep DB_USERNAME .env | cut -d '=' -f2)
DB_PASS=$(grep DB_PASSWORD .env | cut -d '=' -f2)
DB_HOST=$(grep DB_HOST .env | cut -d '=' -f2)

echo "Creating database if it doesn't exist..."
echo "Running migrations and seeders..."

php artisan migrate --force
php artisan db:seed --force

echo "Migrations and seed completed."
echo "Setup completed!"
