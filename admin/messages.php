<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_admin();

$pageTitle       = 'Contact Messages';
$pageDescription = 'Admin view of messages submitted via the Contact page.';

$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY submitted_at DESC")->fetchAll();

include '../includes/header.php';
?>
<div class="container">
    <h1>Contact Messages</h1>

    <table>
        <caption class="sr-only">Messages submitted through the contact form</caption>
        <thead>
            <tr><th scope="col">Name</th><th scope="col">Email</th><th scope="col">Message</th><th scope="col">Submitted</th></tr>
        </thead>
        <tbody>
        <?php foreach ($messages as $m): ?>
            <tr>
                <td><?= clean($m['name']) ?></td>
                <td><?= clean($m['email']) ?></td>
                <td><?= nl2br(clean($m['message'])) ?></td>
                <td><?= date('d M Y g:i A', strtotime($m['submitted_at'])) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($messages)): ?>
            <tr><td colspan="4">No messages received yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include '../includes/footer.php'; ?>
