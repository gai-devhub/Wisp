# ✨ WISP
### *Wish • Inspire • Share • Protect*

<p align="center">
  <img src="public/img/logo.png" alt="WISP Logo" width="180">
</p>

<p align="center">
  <strong>WISP</strong> is a premium digital messaging and keepsake platform built with Laravel that transforms ordinary messages into meaningful, interactive experiences.
</p>

<p align="center">
Create beautiful digital greeting cards, schedule messages for future delivery, protect private memories with secure Vault PINs, attach photos, videos, and music, and share them through Email, SMS, or WhatsApp—all from one elegant platform.
</p>

<p align="center">
Designed with a modern glassmorphic interface, WISP combines automation, security, and personalization to help people celebrate life's most important moments.
</p>

---

<p align="center">

![Status](https://img.shields.io/badge/status-active%20development-blue)
![Laravel](https://img.shields.io/badge/Laravel-12-red)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white)
![Database](https://img.shields.io/badge/Database-MySQL-orange)
![Frontend](https://img.shields.io/badge/Frontend-Blade-green)
![License](https://img.shields.io/badge/License-Proprietary-black)

</p>

---

## 🌟 Features

- 🎉 Beautiful digital greeting cards
- 📅 Schedule messages for future delivery
- 🔐 Vault PIN protection for private messages
- 📷 Image and media attachments
- 🎵 Spotify music integration
- 📧 Email delivery
- 📱 SMS notifications
- 💬 WhatsApp messaging
- 🔁 Recurring birthday and anniversary messages
- 📊 Delivery and view analytics
- ⏳ Auto-expiring message links
- 🎨 Modern glassmorphism interface
- 📱 Fully responsive design
---

## 🛠️ Technology Stack

- **Backend:** Laravel 11.x (PHP 8.2+)
- **Database:** MySQL
- **Frontend:** Blade Templating, Vanilla JS, Custom CSS (Flexbox/Grid focused)
- **APIs & Integrations:**
  - **Twilio API:** For SMS and WhatsApp message delivery.
  - **Spotify Web API:** For fetching and embedding track audio.
  - **Mailgun/Brevo (SMTP):** For transactional email delivery.

---

## 🚀 Installation & Setup

Follow these steps to set up WISP on your local development environment.

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL
- Twilio Account (for SMS/WhatsApp)
- Spotify Developer Account (for Audio integration)

### 1. Clone the Repository
```bash
git clone https://github.com/yourusername/wisp.git
cd wisp
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install NPM dependencies
npm install
npm run build
```

### 3. Environment Configuration
Copy the `.env.example` file to create your `.env` file:
```bash
cp .env.example .env
```
Generate your application encryption key:
```bash
php artisan key:generate
```

#### Update your `.env` with the following critical configurations:
```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wisp_db
DB_USERNAME=root
DB_PASSWORD=

# SMTP Email Configuration (Mailgun, Brevo, Gmail, etc.)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# Twilio Configuration (For SMS & WhatsApp)
TWILIO_SID=your_twilio_account_sid
TWILIO_TOKEN=your_twilio_auth_token
TWILIO_FROM=your_twilio_phone_number
TWILIO_WHATSAPP_FROM=your_twilio_whatsapp_number

# Spotify API Configuration
SPOTIFY_CLIENT_ID=your_spotify_client_id
SPOTIFY_CLIENT_SECRET=your_spotify_client_secret
```

### 4. Database Setup
Create a MySQL database named `wisp_db` (or whatever you defined in your `.env`), then run the migrations and seeders:
```bash
php artisan migrate
php artisan db:seed
```

### 5. Storage Linking
WISP handles local image and media uploads. You must link the storage directory to the public folder:
```bash
php artisan storage:link
```

---

## ⚙️ Running the Application

To run the application locally, you need to start the Laravel development server and the background scheduler.

**1. Start the Web Server:**
```bash
php artisan serve
```
Your application will be available at `http://localhost:8000`.

**2. Start the Automation Scheduler:**
WISP relies heavily on Laravel's Task Scheduler to check for pending scheduled shares, recurring messages, and expiring pages every minute. **In a separate terminal window**, run:
```bash
php artisan schedule:work
```
*(Note: In a production environment, you will set up a Cron job for `* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1` instead of running this command manually).*

---

## 🔒 Security Notes

- **Vault PINs:** All user-generated PINs are heavily encrypted in the database using Laravel's `Crypt` facade. They are never stored in plain text.
- **Public Message Views:** The `MessageViewController` strictly guards against unauthorized access. If a message is vaulted, the controller blocks all media and message contents from loading until a successful POST request with the decrypted PIN is validated.

## 🤝 Contributing
Contributions are welcome. Please ensure that any CSS modifications adhere to the custom mobile-responsive media queries (`max-width: 768px`) defined in `public/css/user-page.css`.

## 📄 License
This project is proprietary and confidential.
