# Individual Contribution Log
**ICT726 Assignment 4 — Redgum Community Library**

> Each team member must complete their own section and submit it individually, per the
> assignment's "Individual Submission" requirement. Reference specific files/commits.

---

## Member 1: [Name / Student ID]
**Feature area:** e.g. Authentication & Access Control
**Files worked on:**
- `register.php`, `login.php`, `logout.php`, `includes/functions.php`

**Contribution summary:**
- Implemented registration with server-side validation and bcrypt password hashing.
- Implemented login with `password_verify()`, session regeneration, and generic error
  messages to prevent user enumeration.
- Built role-based access control helpers (`require_login()`, `require_admin()`).

**Git commits:** [list commit hashes / links]

---

## Member 2: [Name / Student ID]
**Feature area:** e.g. Catalogue & Borrowing System (Database/CRUD)
**Files worked on:**
- `sql/schema.sql`, `catalogue.php`, `admin/books.php`, `member/borrow_action.php`,
  `member/my_books.php`, `admin/borrow_requests.php`

**Contribution summary:**
- Designed the database schema and relationships (`users`, `books`, `borrow_records`).
- Built full CRUD for the book catalogue (admin).
- Built the borrow-request workflow (member request → admin approve/reject → return).

**Git commits:** [list commit hashes / links]

---

## Member 3: [Name / Student ID]
**Feature area:** e.g. Content Pages, Accessibility, SEO & Privacy
**Files worked on:**
- `index.php`, `about.php`, `services.php`, `gallery.php`, `contact.php`, `privacy.php`,
  `css/style.css`, `robots.txt`, `sitemap.xml`

**Contribution summary:**
- Built responsive layout and accessible semantic HTML/ARIA across all public pages.
- Implemented the Contact form with validation and database storage.
- Implemented on-page SEO (titles, meta descriptions, sitemap, robots.txt) and the
  Privacy Notice page.

**Git commits:** [list commit hashes / links]

---

## Notes on Equal Distribution
Work was split roughly into three areas: (1) authentication/security, (2) database/CRUD/
borrowing logic, (3) UI/content/accessibility/SEO. All members reviewed each other's code
via pull requests before merging to `main`.
