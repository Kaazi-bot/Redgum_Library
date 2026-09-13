<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

$pageTitle       = 'Home';
$pageDescription = 'Redgum Community Library in Bendigo offers free membership, a wide book catalogue, digital resources, study spaces and community programs for all ages.';

// Pull 3 upcoming community programs for the homepage teaser (read-only query)
$stmt = $pdo->query("SELECT title, description, event_date FROM programs ORDER BY event_date ASC LIMIT 3");
$programs = $stmt->fetchAll();

include 'includes/header.php';
?>

<section class="hero">
    <h1>Welcome to Redgum Community Library</h1>
    <p>A free, not-for-profit community library in Bendigo, Victoria — offering books, digital
       resources, study spaces and programs for local residents, students and families.</p>
    <a href="register.php" class="btn">Join for Free</a>
    <a href="catalogue.php" class="btn btn-secondary">Browse Catalogue</a>
</section>

<div class="container">
    <h2>What We Offer</h2>
    <div class="grid">
        <div class="card">
            <h3>Book Borrowing</h3>
            <p>Thousands of titles across fiction, non-fiction, local history and children's books.</p>
        </div>
        <div class="card">
            <h3>Digital Resources</h3>
            <p>Free access to e-books, research databases and digital learning tools.</p>
        </div>
        <div class="card">
            <h3>Study Spaces</h3>
            <p>Quiet study areas and community rooms available for members.</p>
        </div>
        <div class="card">
            <h3>Community Programs</h3>
            <p>Story time, digital literacy workshops, and a monthly book club — for all ages.</p>
        </div>
    </div>

    <h2>Upcoming Programs</h2>
    <div class="grid">
        <?php foreach ($programs as $p): ?>
            <div class="card">
                <h3><?= clean($p['title']) ?></h3>
                <p><?= clean($p['description']) ?></p>
                <p><strong>Date:</strong> <?= date('d M Y', strtotime($p['event_date'])) ?></p>
            </div>
        <?php endforeach; ?>
        <?php if (empty($programs)): ?>
            <p>No upcoming programs at the moment. Please check back soon.</p>
        <?php endif; ?>
    </div>

    <p><a href="services.php">See all services &rarr;</a></p>
</div>

<?php include 'includes/footer.php'; ?>
