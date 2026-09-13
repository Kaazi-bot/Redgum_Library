-- =====================================================================
-- Redgum Community Library - Database Schema
-- ICT726 Assignment 4 - Dynamic Website
-- =====================================================================

CREATE DATABASE IF NOT EXISTS redgum_library
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE redgum_library;

-- ---------------------------------------------------------------------
-- Table: users
-- Stores registered members and administrators.
-- Passwords are stored as bcrypt hashes (never plain text).
-- ---------------------------------------------------------------------
CREATE TABLE users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    full_name     VARCHAR(100)  NOT NULL,
    email         VARCHAR(150)  NOT NULL UNIQUE,
    password_hash VARCHAR(255)  NOT NULL,
    role          ENUM('admin','member') NOT NULL DEFAULT 'member',
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: books
-- The library catalogue. Admin performs CRUD on this table.
-- ---------------------------------------------------------------------
CREATE TABLE books (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    title             VARCHAR(200) NOT NULL,
    author            VARCHAR(150) NOT NULL,
    isbn              VARCHAR(20),
    category          VARCHAR(80)  NOT NULL,
    description       TEXT,
    cover_image       VARCHAR(255) DEFAULT 'default-book.jpg',
    total_copies      INT NOT NULL DEFAULT 1,
    available_copies  INT NOT NULL DEFAULT 1,
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: borrow_records
-- Links members to books they have requested / borrowed / returned.
-- Demonstrates a one-to-many relationship from users and books.
-- ---------------------------------------------------------------------
CREATE TABLE borrow_records (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    user_id       INT NOT NULL,
    book_id       INT NOT NULL,
    request_date  DATE NOT NULL,
    due_date      DATE NULL,
    return_date   DATE NULL,
    status        ENUM('requested','approved','returned','rejected')
                  NOT NULL DEFAULT 'requested',
    CONSTRAINT fk_borrow_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_borrow_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: programs
-- Community programs / events shown on the Services page.
-- ---------------------------------------------------------------------
CREATE TABLE programs (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    title         VARCHAR(150) NOT NULL,
    description   TEXT,
    event_date    DATE,
    image         VARCHAR(255)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: contact_messages
-- Stores submissions from the Contact page form.
-- ---------------------------------------------------------------------
CREATE TABLE contact_messages (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100) NOT NULL,
    email         VARCHAR(150) NOT NULL,
    message       TEXT NOT NULL,
    submitted_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================================
-- Seed data
-- =====================================================================

-- Default admin account -> email: admin@redgum.local / password: Admin@123
-- (Hash generated with PHP password_hash('Admin@123', PASSWORD_BCRYPT))
INSERT INTO users (full_name, email, password_hash, role) VALUES
('Library Admin', 'admin@redgum.local', '$2y$10$WAA72cYfeEqesRvh54pRtucujGsJ057HZ6etlSNOJg9q5fwbd8x/6', 'admin');

INSERT INTO books (title, author, isbn, category, description, cover_image, total_copies, available_copies) VALUES
('The Hobbit', 'J.R.R. Tolkien', '9780547928227', 'Fiction', 'A fantasy classic about a hobbit''s unexpected journey.', 'hobbit.jpg', 3, 3),
('A Brief History of Time', 'Stephen Hawking', '9780553380163', 'Science', 'An accessible introduction to cosmology and physics.', 'briefhistory.jpg', 2, 2),
('Bendigo: A History', 'Local Heritage Society', '9780987654321', 'Local History', 'A history of the Bendigo region and its community.', 'bendigo-history.jpg', 2, 1),
('Introduction to Python', 'Rachel Green', '9781234567897', 'Technology', 'A beginner-friendly guide to programming in Python.', 'python-book.jpg', 4, 4),
('The Gruffalo', 'Julia Donaldson', '9780333710937', 'Children', 'A much-loved picture book for young readers.', 'gruffalo.jpg', 5, 5);

INSERT INTO programs (title, description, event_date, image) VALUES
('Story Time for Kids', 'Weekly story time session for children aged 3-7.', '2025-08-15', 'storytime.jpg'),
('Digital Literacy Workshop', 'Free workshop on using computers, email and the internet.', '2025-08-20', 'digital-workshop.jpg'),
('Community Book Club', 'Monthly discussion of a chosen novel, open to all members.', '2025-08-28', 'bookclub.jpg');
