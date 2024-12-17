## About the project

A test project to demonstrate how to implement and consume internal APIs using Axios or Fetch within a Blade-based application, without using a SPA (Single Page Application) approach.

## Requirements

-   **PHP Version & Composer Dependencies** Require PHP version >= 8.2.

## How to install?

-   Clone the repository: run command: **git clone https://github.com/sarwaralamini/lara-axios**
-   After clone navigate to project folder, open PowerShell/CMD and run command: **composer install** (Composer must be installed on your system)
-   run command: **copy .env.example .env** to generate .env file
-   run command **php artisan key:generate**
-   Create database on your local/live server
-   edit .env file and put correct database credentials
-   run command **php artisan optimize:clear** (usually need after editing .env file)
-   run command **php artisan migrate --seed** (The command will create a system administrator account and generate 100 random user accounts.)
-   run command **php artisan storage:link** (Creates a symbolic link for public access to files in storage/app/public.)
-   now finally run command **php artisan serve**

## The system administrator account will be created with the following credentials:

   ```bash
   username: admin
   password: password
   ```

