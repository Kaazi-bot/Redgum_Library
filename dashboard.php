<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
require_login(); // access control: must be logged in

$pageTitle       = 'My Dashboard';
$pageDescription = 'Your Redgum Community Library member dashboard.';

include 'includes/header.php';
?>
<div class="container">
    <h1>Welcome, <?= clean($_SESSION['full_name']) ?></h1>
    <p>Role: <strong><?= clean(ucfirst($_SESSION['role'])) ?></strong></p>

    <?php if (is_admin()): ?>
        <div class="grid">
            <div class="card">
                <h2>Manage Book Catalogue</h2>
                <p>Add, edit or remove books from the library catalogue.</p>
                <a class="btn" href="admin/books.php">Manage Books</a>
            </div>
            <div class="card">
                <h2>Borrow Requests</h2>
                <p>Review and approve member borrow requests.</p>
                <a class="btn" href="admin/borrow_requests.php">Manage Requests</a>
            </div>
            <div class="card">
                <h2>Contact Messages</h2>
                <p>View messages submitted through the Contact page.</p>
                <a class="btn" href="admin/messages.php">View Messages</a>
            </div>
        </div>
    <?php else: ?>
        <div class="grid">
            <div class="card">
                <h2>Browse the Catalogue</h2>
                <p>Search for books and request to borrow them.</p>
                <a class="btn" href="catalogue.php">Go to Catalogue</a>
            </div>
            <div class="card">
                <h2>My Borrowed Books</h2>
                <p>View the status of your borrow requests.</p>
                <a class="btn" href="member/my_books.php">My Books</a>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
