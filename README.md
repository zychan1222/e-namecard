# 📇 e-Namecard System

## 📖 Overview
The **e-Namecard System** is a Laravel-based web application designed to modernize the traditional business card.  
Instead of relying on physical cards, users can generate, share, and manage **electronic name cards** featuring QR codes, TAC code login, and downloadable `.vcf` contact files.  

It also includes a powerful **Admin Back-Office Panel** with **multi-organization management** inspired by Slack’s workspace model — making it ideal for companies, organizations, and professional networks that need centralized but flexible control.

---

## ✨ Features
- 🔑 **TAC Code Login** – One-time secure access code for quick authentication  
- 📱 **QR Code Generation** – Share contact details instantly with scannable QR codes  
- 📂 **.vcf File Downloads** – Export and download digital contact cards  
- 🌐 **Social Media Integration** – Attach and display social media links  
- 🛠 **Admin Back-Office Panel** – Manage users, cards, and organizations in one place  
- 🏢 **Multi-Organization Support** – Multiple organizations under a single account (Slack-style)  
- 🔐 **Google & GitHub Social Login** – OAuth-based authentication for convenience and security  
- ✅ **Secure Coding & Unit Testing** – Built with best practices for maintainability and scalability  

---

## 🛠 Tech Stack
- **Backend Framework:** Laravel (PHP)  
- **Database:** MySQL / MariaDB  
- **Authentication:** OAuth (Google, GitHub), TAC Code  
- **Other Tools & Services:**  
  - [ngrok](https://ngrok.com/) – Secure local testing & tunneling  
  - Laravel built-in unit testing for QA  
  - Git & GitHub for version control  

---

## ⚙️ Installation

### 🔧 Prerequisites
- PHP >= 8.0  
- Composer  
- MySQL / MariaDB  
- Node.js & NPM (for frontend assets)  
- Git  

### 🚀 Setup Instructions

#### 1. Clone the Repository
```bash
git clone https://github.com/zychan1222/e-namecard.git
cd e-namecard
```

#### 2. Install Dependencies
```bash
composer install
npm install && npm run dev
```

#### 3. Configure Environment
Copy `.env.example` to `.env` and configure database, mail server, and OAuth credentials:
```bash
cp .env.example .env
php artisan key:generate
```

#### 4. Run Migrations
```bash
php artisan migrate --seed
```

#### 5. Start Development Server
```bash
php artisan serve
```

#### 6. (Optional) Expose with ngrok
```bash
ngrok http 8000
```

---

## ▶️ Usage
- Register or log in via **Google/GitHub OAuth** or **TAC code login**  
- Create and customize your electronic name card  
- Generate QR codes or download `.vcf` files to share with others  
- Admin users can manage organizations, users, and cards from the back-office panel  

---

## 🌟 Project Highlights
- Developed a **Laravel-based electronic name card system** featuring TAC code login, QR code generation, `.vcf` downloads, and social media integration  
- Built a **robust admin back-office panel** supporting **multi-organization management** under a single account (inspired by Slack’s workspace model)  
- Integrated **Google and GitHub social login** for accessibility and security  
- Followed **secure coding practices**, applied **unit testing**, and maintained scalability  
- Hands-on experience with **Laravel, ngrok, and OAuth**  

---

## 👨‍💻 Author
Developed by **Chan Zhien Yiet**  