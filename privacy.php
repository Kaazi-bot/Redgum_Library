<?php
require_once 'includes/functions.php';
$pageTitle       = 'Privacy Notice';
$pageDescription = 'Redgum Community Library privacy notice: how we collect, use and protect member data.';
include 'includes/header.php';
?>
<div class="container">
    <h1>Privacy Notice</h1>
    <p>Redgum Community Library respects your privacy. This notice explains what information we
       collect, why we collect it, and how it is protected.</p>

    <h2>What We Collect</h2>
    <ul>
        <li><strong>Membership details:</strong> full name and email address, when you register for an account.</li>
        <li><strong>Borrowing activity:</strong> which books you request and their due/return dates, to manage the catalogue.</li>
        <li><strong>Contact messages:</strong> name, email and message content submitted via our Contact form.</li>
    </ul>

    <h2>Why We Collect It</h2>
    <p>Your information is used only to operate your library account (login, borrowing history) and
       to respond to enquiries submitted through the Contact form. We do not sell or share your data
       with third parties.</p>

    <h2>How We Protect It</h2>
    <ul>
        <li>Passwords are never stored in plain text — they are hashed using industry-standard bcrypt hashing.</li>
        <li>All data input is validated and sanitised to prevent malicious submissions.</li>
        <li>Access to member and administrative data is restricted using role-based access control.</li>
        <li>Database queries use prepared statements to prevent SQL injection.</li>
    </ul>

    <h2>Your Rights</h2>
    <p>You may request to view, correct or delete your personal information at any time by contacting
       us via the <a href="contact.php">Contact page</a>.</p>

    <p><em>This is a student project created for educational purposes (ICT726 Assignment 4). No real
        personal data should be submitted to this demonstration site.</em></p>
</div>
<?php include 'includes/footer.php'; ?>
