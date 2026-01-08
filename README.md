# Intent CMS Pro

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![PHP](https://img.shields.io/badge/php-8.2%2B-777BB4.svg?logo=php&logoColor=white)
![Status](https://img.shields.io/badge/status-active-success.svg)

**Intent CMS Pro** is a lightweight, AI-native content management system built on the custom **Intent Framework**. It is designed for speed, simplicity, and flexibility, avoiding the bloat of traditional frameworks like Laravel or Symfony.

The project features a modern **Multi-Page Application (MPA)** admin interface built with **PHP**, **Vanilla JavaScript**, and **Tailwind CSS**. It combines the robustness of server-side rendering with the interactivity of a client-side API.

---

## 🚀 Key Features

### Backend (Intent Framework)
- **Zero Heavy Dependencies**: Built on a custom, lightweight core (`Intent\Framework`).
- **Performance First**: Optimized for shared hosting and low-resource environments.
- **Strict Typing**: Codebase adheres to `declare(strict_types=1)` and PHPStan Level 9.
- **REST API Architecture**: Data interactions are handled via secure JSON APIs.
- **Database Agnostic**: Supports MySQL/MariaDB (and SQLite/PostgreSQL via PDO).

### Admin Panel
- **Multi-Page Architecture**: Individual, lightweight PHP pages for better SEO and deep linking.
- **API-Driven UI**: JavaScript fetches data like an SPA, but within a traditional page structure.
- **Modern UI**: Clean, responsive interface using Tailwind CSS and Inter font.
- **Block Editor**: Integrated **Editor.js** for rich, structured content creation.
- **Media Manager**: Drag-and-drop file uploads, image usage tracking, and folder management.
- **Global Toast System**: Unified notification system for user feedback.
- **Dark Mode**: Native support for light and dark themes.

### Workflow
- **Post Management**: Create, edit, draft, and publish posts with auto-slug generation.
- **Verified Security**: CSRF protection, secure session handling, and input sanitization.
- **Developer Friendly**: Detailed `ARCHITECTURE.md` and extensive inline documentation.

---

## 🛠️ Requirements

- **PHP**: 8.2 or higher
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Extensions**: `pdo`, `pdo_mysql`, `json`, `mbstring`, `gd` (for image processing)
- **Composer**: For dependency management

---

## 📦 Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/aamirali51/intent-cms-pro.git
   cd intent-cms-pro
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Configure Environment**
   Duplicate the example config file (if valid) or creating a new `config/database.php`.
   *(Note: The current version uses a `config/database.php` file directly. Ensure your credentials are set there.)*

4. **Run Migrations**
   Initialize the database schema using the built-in CLI tool:
   ```bash
   php intent migrate
   ```

5. **Start the Server**
   You can use the built-in PHP server for development:
   ```bash
   php -S localhost:8000 -t public
   ```
   Or configure your Apache/Nginx web server to point to the `public/` directory.

---

## 📚 Documentation

For a deep dive into the system's internal structure and design decisions, please refer to:
- [**ARCHITECTURE.md**](./ARCHITECTURE.md): Detailed breakdown of the core framework and file structure.
- [**TODO.md**](./TODO.md): Current roadmap and pending features.
- [**CHANGELOG.md**](./CHANGELOG.md): History of changes and updates.

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:
1. Fork the repository.
2. Create a feature branch (`git checkout -b feature/AmazingFeature`).
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`).
4. Push to the branch (`git push origin feature/AmazingFeature`).
5. Open a Pull Request.

**Code Style**: Please ensure all PHP files use `declare(strict_types=1);` and pass PHPStan analysis.

---

## 📄 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

---

**Author**: [Aamir Ali](https://github.com/aamirali51)
