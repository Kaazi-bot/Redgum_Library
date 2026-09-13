# Redgum Community Library — Dynamic Website
**ICT726 Web Development — Assignment 4 (Dynamic Website)**
King's Own Institute

## 1. Business Overview
Redgum Community Library is a fictional not-for-profit public library in Bendigo, Victoria,
originally created as a static website for Assignment 3. For Assignment 4 it has been rebuilt
as a **dynamic, database-driven PHP/MySQL web application**, adding member accounts, an
online catalogue with a real borrowing workflow, and admin-managed content.

## 2. Technology Stack
- **Frontend:** HTML5, CSS3 (custom, no framework), vanilla JavaScript
- **Backend:** PHP 8 (PDO for all database access, prepared statements throughout)
- **Database:** MySQL / MariaDB

## 3. Folder Structure
```
redgum-library/
├── admin/                 # Admin-only pages (role-protected)
│   ├── books.php          # CRUD for the book catalogue
│   ├── borrow_requests.php# Approve / reject / return borrow requests
│   └── messages.php       # View contact form submissions
├── member/                 # Logged-in member pages
│   ├── borrow_action.php  # Handles "Request to Borrow" form submissions
│   └── my_books.php       # Member's own borrow history
├── config/
│   ├── db.php              # PDO database connection
│   └── .htaccess           # Blocks direct web access to this folder
├── includes/
│   ├── functions.php       # Auth, access control, sanitisation, CSRF helpers
│   ├── header.php          # Shared <head>, nav, SEO meta tags
│   └── footer.php          # Shared footer + mobile-nav script
├── css/style.css           # Site-wide responsive stylesheet
├── images/, videos/, audio/# Media assets (placeholders included)
├── sql/schema.sql          # Full database schema + seed data
├── index.php, about.php, services.php, catalogue.php,
│   gallery.php, contact.php, privacy.php   # Public pages
├── register.php, login.php, logout.php     # Authentication
├── dashboard.php           # Role-based landing page after login
├── robots.txt, sitemap.xml # SEO
└── README.md
```

## 4. Database Schema
| Table            | Purpose                                                         |
|-------------------|------------------------------------------------------------------|
| `users`           | Members and admins. `role` ENUM('admin','member'). Bcrypt password hashes. |
| `books`           | Library catalogue. Tracks `total_copies` / `available_copies`.  |
| `borrow_records`  | Links `users` ↔ `books`. Status: requested → approved → returned (or rejected). |
| `programs`        | Community events shown on Home/Services pages.                  |
| `contact_messages`| Stores Contact form submissions.                                 |

Relationships: `borrow_records.user_id → users.id`, `borrow_records.book_id → books.id`
(both `ON DELETE CASCADE`).

Run `sql/schema.sql` in phpMyAdmin / MySQL CLI to create the database and seed sample data.

## 5. Key Functionality Mapped to Assignment Requirements

| Requirement | Where it's implemented |
|---|---|
| Register / Login / Logout | `register.php`, `login.php`, `logout.php` |
| Roles (admin / member) | `users.role`; `require_admin()` / `require_login()` in `includes/functions.php` |
| Secure password storage | `password_hash()` / `password_verify()` (bcrypt) |
| Database + CRUD | `admin/books.php` (Create/Read/Update/Delete on `books`) |
| Form #1 with validation | Contact form (`contact.php`) |
| Form #2 with validation | Registration form (`register.php`) — plus catalogue search, book add/edit, borrow actions |
| Error handling / validation | Server-side checks on every form; inline error messages |
| Responsive design | `css/style.css` media queries; mobile nav toggle |
| Semantic HTML / ARIA | `<header>`, `<nav>`, `<main>`, `<footer>`, `aria-*` attributes, skip link |
| SEO | Per-page `<title>`/meta description, semantic headings, `robots.txt`, `sitemap.xml` |
| Privacy notice | `privacy.php` |
| Session management / CSRF | `includes/functions.php` (`csrf_token()`, `csrf_verify()`, secure session cookie) |
| Role-based access control | `require_admin()` guards `/admin/*`; `require_login()` guards `/member/*` and dashboard |

## 6. Setup Instructions (local / XAMPP / InfinityFree)
1. Create a MySQL database and import `sql/schema.sql`.
2. Update credentials in `config/db.php` (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`).
3. Upload/copy the project folder into your web server's document root.
4. Visit `index.php` in your browser.
5. Demo accounts:
   - **Admin:** `admin@redgum.local` / `Admin@123`
   - **Member:** register a new account via `register.php`

## 7. Security Notes
- All SQL queries use PDO prepared statements (protects against SQL injection).
- All output is passed through `clean()` (`htmlspecialchars`) to prevent XSS.
- All state-changing forms include a CSRF token, verified server-side.
- Session ID is regenerated on login to prevent session fixation.
- `config/` is blocked from direct web access via `.htaccess`.

## 8. Known Limitations / Deviations
- Media files (`images/`, `videos/`, `audio/`) are placeholders — replace with real,
  royalty-free assets before final submission (Unsplash/Pexels for images, as per the
  Assignment 3 report's media sourcing).
- Email notifications for borrow approval are not implemented (out of scope for this assignment).
