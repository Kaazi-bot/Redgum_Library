<?php
// Every page sets $pageTitle and $pageDescription before including this file.
$pageTitle       = $pageTitle ?? 'Redgum Community Library';
$pageDescription = $pageDescription ?? 'Redgum Community Library in Bendigo, Victoria - free membership, books, digital resources and community programs for all ages.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO: unique title + meta description per page -->
    <title><?= clean($pageTitle) ?> | Redgum Community Library</title>
    <meta name="description" content="<?= clean($pageDescription) ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://www.redgumlibrary.example/<?= basename($_SERVER['PHP_SELF']) ?>">

    <!-- Open Graph tags for better social sharing (on-page SEO) -->
    <meta property="og:title" content="<?= clean($pageTitle) ?> | Redgum Community Library">
    <meta property="og:description" content="<?= clean($pageDescription) ?>">
    <meta property="og:type" content="website">

    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>
<a class="skip-link" href="#main-content">Skip to main content</a>

    <header class="site-header">
    <div class="header-inner">
        <a href="<?= BASE_URL ?>index.php" class="brand" aria-label="Redgum Community Library home">
            <span class="brand-mark" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 4h6a3 3 0 0 1 3 3v13a2.5 2.5 0 0 0-2.5-2.5H4V4Z" stroke="#F3EEDF" stroke-width="1.4" stroke-linejoin="round"/>
                    <path d="M20 4h-6a3 3 0 0 0-3 3v13a2.5 2.5 0 0 1 2.5-2.5H20V4Z" stroke="#F3EEDF" stroke-width="1.4" stroke-linejoin="round"/>
                </svg>
            </span>
            <span class="brand-text">
                <strong>Redgum Library</strong>
                <span>Bendigo · Victoria</span>
            </span>
        </a>

        <nav class="main-nav" aria-label="Main navigation">
            <button class="nav-toggle" id="navToggle" aria-expanded="false" aria-controls="navMenu">
                <span class="sr-only">Menu</span>
                &#9776;
            </button>
            <ul id="navMenu" class="nav-menu">
                <li><a href="<?= BASE_URL ?>index.php">Home</a></li>
                <li><a href="<?= BASE_URL ?>about.php">About Us</a></li>
                <li><a href="<?= BASE_URL ?>services.php">Services</a></li>
                <li><a href="<?= BASE_URL ?>catalogue.php">Catalogue</a></li>
                <li><a href="<?= BASE_URL ?>gallery.php">Gallery</a></li>
                <li><a href="<?= BASE_URL ?>contact.php">Contact</a></li>
                <?php if (is_logged_in()): ?>
                    <li><a href="<?= BASE_URL ?>dashboard.php">My Dashboard</a></li>
                    <li><a href="<?= BASE_URL ?>logout.php">Logout (<?= clean($_SESSION['full_name'] ?? '') ?>)</a></li>
                <?php else: ?>
                    <li><a href="<?= BASE_URL ?>login.php">Login</a></li>
                    <li><a href="<?= BASE_URL ?>register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<main id="main-content">
<?php
$success = flash('flash_success');
$error   = flash('flash_error');
if ($success): ?>
    <div class="alert alert-success" role="status"><?= clean($success) ?></div>
<?php endif;
if ($error): ?>
    <div class="alert alert-error" role="alert"><?= clean($error) ?></div>
<?php endif; ?>
