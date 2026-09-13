<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
$pageTitle       = 'Services';
$pageDescription = 'Explore Redgum Community Library services: membership, digital learning resources, community programs, study facilities and children\'s activities.';

$programs = $pdo->query("SELECT title, description, event_date, image FROM programs ORDER BY event_date ASC")->fetchAll();

include 'includes/header.php';
?>
<div class="container">
    <h1>Our Services</h1>

    <div class="grid">
        <div class="card">
            <h2>Library Membership</h2>
            <p>Free membership for all Bendigo residents. Borrow books, reserve titles and access
               member-only resources.</p>
        </div>
        <div class="card">
            <h2>Digital Learning Resources</h2>
            <p>Access e-books, research databases and online learning tools from home or in the library.</p>
        </div>
        <div class="card">
            <h2>Study Facilities</h2>
            <p>Quiet study areas, group rooms and free Wi-Fi for students and researchers.</p>
        </div>
        <div class="card">
            <h2>Children's Activities</h2>
            <p>Story time, reading programs and creative activities for younger members.</p>
        </div>
    </div>

    <h2>Community Programs</h2>
    <table>
        <caption class="sr-only">Upcoming community programs</caption>
        <thead>
            <tr><th scope="col">Program</th><th scope="col">Description</th><th scope="col">Date</th></tr>
        </thead>
        <tbody>
        <?php foreach ($programs as $p): ?>
            <tr>
                <td><?= clean($p['title']) ?></td>
                <td><?= clean($p['description']) ?></td>
                <td><?= date('d M Y', strtotime($p['event_date'])) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($programs)): ?>
            <tr><td colspan="3">No programs currently scheduled.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include 'includes/footer.php'; ?>
