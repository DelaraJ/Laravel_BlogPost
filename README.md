# Blog Post Project in Laravel

## Introduction

This project is a blog post application built using Laravel 11. It allows users to register, log in, create, edit, delete, and search, blog posts. Additionally, users can comment on blog posts, like blogs and comments, and the application supports publishing posts, as well as generating weekly exports of blog data.

## Features

- User registration and authentication
- CRUD (Create, Read, Update, Delete) functionality for blog posts
- Ability to like blog posts and comments
- Commenting on blog posts
- Search functionality for blog posts
- Weekly exports of blogs
- Two new Artisan commands:
    - `php artisan export:blogs-between {start_date} {end_date}`
    - `php artisan export:blogs`
- Email notifications when a blog is published
- Notifications saved in the database
- Seeders for admin user, users, and blog posts for easy database population

## Requirements

- PHP >= 7.4
- Composer
- Laravel = 11.x
- A supported database (MySQL, SQLite, etc.)

## Installation

1. **Clone the repository:**
    
    ```bash
    git clone <repository-url>
    cd <project-directory>
    
    ```
    
2. **Install dependencies:**
    
    ```bash
    composer install
    
    ```
    
3. **Environment Configuration:**
Create a new `.env` file by copying the example file:
    
    ```bash
    cp .env.example .env
    
    ```
    
    Open the `.env` file and set your database connection settings (including the database name) according to your environment.
    
4. **Database Migration:**
Before running the project, ensure that the necessary database tables are created by running the migrations:
    
    ```bash
    php artisan migrate
    
    ```
    
5. **Seed the database:**
After migration, you can seed the database with sample data for users and blog posts by running:
    
    ```bash
    php artisan db:seed DatabaseSeeder
    
    ```
    
6. **Run the application:**
Start the Laravel development server:
    
    ```bash
    php artisan serve
    
    ```
    
    Access the application at [http://127.0.0.1:8000](http://127.0.0.1:8000/).
    

## Usage

- **User Registration:** New users can register through the registration page.
- **User Login:** Once registered, users can log in using their credentials.
- **Creating Blogs:** Logged-in users can create new blog posts, which are published immediately.
- **Editing Blogs:** Users can edit their existing blog posts.
- **Deleting Blogs:** Users can delete their own blog posts at any time.
- **Liking Blogs:** Users can like any blog post.
- **Commenting on Blogs:** Users can add comments to blog posts.
- **Searching Blogs:** Users can search for blog posts using keywords.
- **Exporting Blogs:** Use the commands:
    - `php artisan export:blogs-between {start_date} {end_date}` to export blogs between specified dates.
    - `php artisan export:blogs` to export all blogs in last 7 days.
- **Email Notifications:** Users receive email notifications when a blog is published.

## Notes

To ensure all features work correctly, make sure to run the following commands:

- `php artisan schedule:work`
- `php artisan queue:work`