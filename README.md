# CLOJO TALENT CONNECT
A student full-stack project by ISHIMWE using Core PHP, MySQL, Tailwind CSS, and vanilla JavaScript.

## Requirements
- PHP 8 or later
- MySQL or MariaDB
- Apache / XAMPP / LAMP
- Internet access while developing (Tailwind CSS CDN and Google Fonts)

## Setup with XAMPP
1. Copy this folder into your web server's document root (often `htdocs`).
2. Start Apache and MySQL.
3. Open `http://localhost/phpmyadmin` and import `database.sql`.
4. Check `db.php`. Default local XAMPP settings are often user `root` and an empty password.
5. Open `http://localhost/clojo-talent-connect/`.

## Create an administrator
1. Register a normal account on the website.
2. In phpMyAdmin, select the `clojo_talent_connect` database and run the following, replacing the email:

```sql
UPDATE users SET role = 'admin' WHERE email = 'your-email@example.com';
```
3. Log out and log back in. Public registration cannot create admin accounts.

## Demo workflow
1. Register as a talent and create a profile.
2. Register another account as a sponsor.
3. Log in as the sponsor and publish an opportunity.
4. Log in as the talent and apply.
5. Log in as the sponsor and accept or reject the application.
6. Log in as the admin and manage demo content.

## Five tables
- `users`: account, password hash, and role.
- `categories`: shared talent/opportunity categories.
- `talent_profiles`: biography, skills, location, category, and image URL.
- `opportunities`: offers posted by sponsors.
- `applications`: talent applications and their status.

Tailwind is loaded from its CDN to keep the student setup simple; internet is required for the styling CDN. For production, compile Tailwind locally. Portfolio images use image URLs in this version, so no upload setup is required. This is an educational local prototype, not a production service.
