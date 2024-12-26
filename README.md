
# About the Project

This is a test project to demonstrate how to implement and consume internal APIs using Axios or Fetch within a Blade-based application, without using a Single Page Application (SPA) approach.

# Requirements

- **PHP Version & Composer Dependencies**: Requires PHP version >= 8.2.

# Installation Instructions

1. **Clone the repository**:  
   Run the following command to clone the repository:  
   ```bash
   git clone https://github.com/sarwaralamini/lara-axios
   ```

2. **Install dependencies**:  
   Navigate to the project folder and open PowerShell/CMD, then run:  
   ```bash
   composer install
   ```
   *Note*: Composer must be installed on your system.

3. **Generate `.env` file**:  
   Run the command:  
   ```bash
   copy .env.example .env
   ```

4. **Generate application key**:  
   Run the command:  
   ```bash
   php artisan key:generate
   ```

5. **Set up the database**:  
   - Create a database on your local or live server.
   - Edit the `.env` file and enter the correct database credentials.

6. **Configure your `.env` file**:  
   Add the following settings to your `.env` file:  
   - `APP_URL` (e.g., `http://yoursite.com`)
   - `SESSION_DOMAIN` (e.g., `.yoursite.com`)
   - `SANCTUM_STATEFUL_DOMAINS` (e.g., `http://yoursite.com`)

7. **Run migrations**:  
   Run the command:  
   ```bash
   php artisan migrate --seed
   ```
   This will create a system administrator account and generate 100 random user accounts.

8. **Clear cache and optimize**:  
   Run the command:  
   ```bash
   php artisan optimize:clear
   ```

# Sarwar Popup File Manager: Publishable File Installation Guide

After running `composer install`, the **sarwar/popup-file-manager** package will be set up locally. To publish the required files, follow these steps:

1. **Publish the configuration files**:  
   Run the command:  
   ```bash
   php artisan vendor:publish --tag=plfm_config
   ```

2. **Publish the public assets**:  
   Run the command:  
   ```bash
   php artisan vendor:publish --tag=plfm_public
   ```

3. **Publish the view files**:  
   Run the command:  
   ```bash
   php artisan vendor:publish --tag=plfm_view
   ```

4. **Create a symbolic link for public access**:  
   Run the command:  
   ```bash
   php artisan storage:link
   ```

5. **Run the development server**:  
   Run the command:  
   ```bash
   php artisan serve
   ```

6. **Visit the application**:  
   Open the following URL in your browser:  
   ```text
   http://your_project_url
   ```

7. **Visit the Popup File Manager Demo**:  
   Open the following URL to check the file manager demo:  
   ```text
   http://your_project_url/sarwar/popup-file-manager/demo
   ```

   You should now be able to test the file manager demo by clicking on any file type input.

# System Administrator Credentials

The system administrator account will be created with the following credentials:

- **Username**: `admin`
- **Password**: `password`
