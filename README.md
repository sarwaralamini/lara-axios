## About the project
A test project to demonstrate how to implement and consume internal APIs using Axios or Fetch within a Blade-based application, without using a SPA (Single Page Application) approach.

## How to install?

-   Clone the repository: run command - git clone https://github.com/sarwaralamini/lara-axios
-   After clone navigate to project folder, open PowerShell/CMD and run command: **composer install** (Composer must be installed on your system)
-   run command: **copy .env.example .env** to generate .env file
-   run command php artisan key:generate
-   Create database on your local/live server
-   edit .env file and put correct database credentials
-   run command **php artisan optimize:clear** (usually need after editing .env file)
-   run command **php artisan migrate**
-   now finally run command **php artisan serve**
