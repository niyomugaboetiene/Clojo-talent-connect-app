-- CLOJO TALENT CONNECT. Import this file using phpMyAdmin.
CREATE DATABASE IF NOT EXISTS clojo_talent_connect CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE clojo_talent_connect;
CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100) NOT NULL,email VARCHAR(150) NOT NULL UNIQUE,password VARCHAR(255) NOT NULL,role ENUM('talent','sponsor','admin') NOT NULL DEFAULT 'talent',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS categories (id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(80) NOT NULL UNIQUE) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS talent_profiles (id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NOT NULL UNIQUE,bio TEXT,skills VARCHAR(500),location VARCHAR(100),category_id INT NULL,image_url VARCHAR(500),FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,FOREIGN KEY(category_id) REFERENCES categories(id) ON DELETE SET NULL) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS opportunities (id INT AUTO_INCREMENT PRIMARY KEY,sponsor_id INT NOT NULL,title VARCHAR(150) NOT NULL,description TEXT NOT NULL,support_type ENUM('Funding','Equipment','Mentorship','Training','Exposure') NOT NULL DEFAULT 'Mentorship',category_id INT NULL,deadline DATE NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(sponsor_id) REFERENCES users(id) ON DELETE CASCADE,FOREIGN KEY(category_id) REFERENCES categories(id) ON DELETE SET NULL) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS applications (id INT AUTO_INCREMENT PRIMARY KEY,talent_id INT NOT NULL,opportunity_id INT NOT NULL,status ENUM('pending','accepted','rejected') NOT NULL DEFAULT 'pending',applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,UNIQUE KEY one_application_per_opportunity(talent_id,opportunity_id),FOREIGN KEY(talent_id) REFERENCES users(id) ON DELETE CASCADE,FOREIGN KEY(opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE) ENGINE=InnoDB;
INSERT IGNORE INTO categories(name) VALUES ('Film & Media'),('Music'),('Creative Arts'),('Technology'),('Fashion'),('Culinary Arts'),('Sports'),('Photography'),('Education'),('Other');
-- To create the first admin: register normally, then run:
-- UPDATE users SET role='admin' WHERE email='your-email@example.com';
-- Log out and back in after changing the role.
