# Daily Journal App

A daily journal web application to keep track of your thoughts, memories, and moments. Built with a focus on minimalism, speed, and a great user experience.

## Features

- **Clean UI**: A clean, minimalist, and modern interface with a sophisticated dark mode.
- **Rich Text Editor**: Seamless integration of Quill.js for intuitive text formatting (bold, italic, lists, quotes).
- **Entries Feed**: Chronological list of past entries with a snapshot preview of your writing.
- **Dynamic Counters**: Live character and word count tracking while you write.
- **Search**: Fast and effective search functionality to quickly find moments from your past.

## Tech Stack

- **Backend:** Laravel 12 (PHP)
- **Frontend:** Vue.js 3, Tailwind CSS
- **Editor:** Quill.js
- **Database:** SQLite (default)

## Prerequisites

Make sure you have the following installed on your machine:
- PHP 8.2 or higher
- Composer
- Node.js and npm
- Git

## Getting Started

Follow these instructions to set up the project locally.

### 1. Clone the Repository
```bash
git clone <your-repo-url>
cd daily-journal-app
```

### 2. Install Dependencies
Install PHP and Node.js dependencies:
```bash
composer install
npm install
```

### 3. Environment Setup
Copy the example environment file and generate an application key:
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Setup
Run the database migrations to set up the SQLite database:
```bash
php artisan migrate
```

### 5. Build Assets & Start Server
Compile frontend assets and start the local development server:
```bash
npm run build
php artisan serve
```

*Note: For hot-reloading during development, you can use `npm run dev` instead of `npm run build`.*

Navigate to `http://localhost:8000` to see your journal in action!

