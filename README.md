<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Task: Image Uploader

### Task Description

Create an image uploader with the ability to upload up to 10 images at once.

### Requirements:
1. **Queue-Based Uploads**: Image uploads should happen asynchronously using Laravel's queues.
2. **`Task` Entity**: You need to create a `Task` entity that tracks the upload status (success or failure).
3. **Image Resizing**: Images should be automatically resized to **400x600** resolution.
4. **Status Update Without Page Reload**: The upload status should update on the frontend without page reload. **Do not use WebSockets**.
5. **Progress Bar**: Implement an interactive progress bar for each image showing the upload percentage (from 0 to 100%).

### Technologies:
- **Backend**: Laravel
- **Frontend**: Vue.js or React (SPA — single-page application)

---

## Installation

### Step 1: Clone the repository

```bash
git clone https://github.com/your-repo/image-uploader.git
cd image-uploader
```
Step 2: Install dependencies
Make sure you have Composer and Laravel installed. Then, install all project dependencies:

```bash
composer install
```
Step 3: Set up the .env file
Copy the .env.example file to .env and configure the parameters for your database and environment:

```bash
cp .env.example .env
```
Generate the application key:

```bash
php artisan key:generate
```
Step 4: Set up the database
Create a database on your server, then run the migrations and seed the data:

```bash
php artisan migrate
```
Step 5: Run Sail (if using Docker)
For those using Laravel Sail, run the following command to start the Docker containers:

```bash
./vendor/bin/sail up -d
```
Step 6: Install image processing library
Install the Intervention Image library for image processing:

```bash
composer require intervention/image
```
Step 7: Run the application
Start the Laravel built-in server or use Sail:

```bash
php artisan serve
```
Or for Sail:

```bash
./vendor/bin/sail up
```
The application will now be available at: http://localhost:8000.
