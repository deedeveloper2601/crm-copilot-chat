# VectorLead CRM AI Copilot

![VectorLead Banner](https://img.shields.io/badge/Status-Active-success) ![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel) ![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=flat&logo=vue.js) ![Inertia.js](https://img.shields.io/badge/Inertia.js-3.x-9553E9?style=flat) ![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.x-38B2AC?style=flat&logo=tailwind-css)

VectorLead CRM AI Copilot is an intelligent assistant integrated into a modern CRM platform. It acts as an AI Copilot for sales representatives, analyzing vector database records (using PGVector) to provide actionable insights, answer queries about leads, and summarize recent interactions.

This repository contains the full-stack web application (Frontend + Backend), which connects to a dedicated AI Microservice.

## ✨ Features

- **AI Chat Assistant**: Ask the AI about any lead, and it will answer based on historical CRM data.
- **RAG Architecture**: The Copilot relies on Retrieval-Augmented Generation (RAG) using PostgreSQL and PGVector to provide context-aware responses.
- **Modern Tech Stack**: Built with Laravel 12, Vue 3, and Tailwind CSS v4 for a lightning-fast, reactive user experience.
- **Quick Actions**: One-click suggestions to quickly summarize notes or log calls/notes directly via the AI interface.
- **Stunning UI**: A sleek, dark-themed interface with ambient glowing effects and glassmorphism.

## 🛠 Tech Stack

- **Backend Framework**: [Laravel 12](https://laravel.com/)
- **Frontend Framework**: [Vue 3](https://vuejs.org/) (Composition API)
- **Routing & State**: [Inertia.js](https://inertiajs.com/)
- **Styling**: [Tailwind CSS v4](https://tailwindcss.com/)
- **Build Tool**: [Vite](https://vitejs.dev/)
- **AI Microservice Integration**: Axios proxies requests to a Python (FastAPI + Gemini) service.

## 📋 Prerequisites

Before running this project, make sure you have the following installed:

- **PHP**: `>= 8.2`
- **Composer**: Dependency manager for PHP.
- **Node.js & NPM**: JavaScript runtime and package manager.
- **Python Microservice**: The AI Python microservice must be running locally on `http://127.0.0.1:8001`.

## 🚀 Installation & Setup

We have streamlined the setup process using custom Composer scripts.

1. **Clone the repository:**
   ```bash
   git clone https://github.com/deedeveloper2601/crm-copilot-chat.git
   ```

2. **Run the automated setup:**
   ```bash
   composer setup
   ```
   > **Note:** The `setup` script automatically runs `composer install`, copies `.env.example` to `.env`, generates your application key, runs database migrations, installs NPM packages, and builds the frontend assets.

3. **Configure Environment Variables (Optional):**
   Update the `.env` file with your specific database credentials if you aren't using the default SQLite setup.

## 💻 Running the Application

To start the development server, run:

```bash
composer dev
```

This single command will concurrently start:
- Laravel PHP development server (`php artisan serve`)
- Queue listener (`php artisan queue:listen`)
- Log tailing (`php artisan pail`)
- Vite frontend development server (`npm run dev`)

You can now visit your app (typically at `http://localhost:8000`).

## 🧠 Architecture Overview

The system is divided into three main layers:

1. **Frontend (Vue + Tailwind + Inertia)**: Provides a responsive, real-time chat interface.
2. **Backend (Laravel)**: Acts as a middleman and API gateway. The `CopilotController` validates incoming requests from the frontend and safely proxies them to the internal microservice.
3. **AI Microservice (Python + FastAPI)**: *(Run separately on port 8001)*. Receives prompts, embeds them, searches a PGVector database for relevant lead context, and queries the Gemini AI model to generate an intelligent response.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
