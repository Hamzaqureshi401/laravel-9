# Laravel-9 Project Setup

This guide outlines the steps to set up and run a Laravel project locally.

## Step 1: Update Composer and Create .env File

Update Composer and rename the .env.example file to .env:

```bash
composer update
cp .env.example .env

Step 2: Set Up the Database
Create a database in PHPMyAdmin named 'testing'.

Step 3: Run Migrations
Run the database migrations to create the necessary tables:

php artisan migrate


Step 4: Start the Laravel Server
Start the Laravel development server:

php artisan serve


Step 5: Start Queue
php artisan queue:listen


Step 6: Start Redis server in linux

Step 7: Start Elastic search server

Step 8: php artisan run test
