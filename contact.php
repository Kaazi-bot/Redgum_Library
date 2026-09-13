<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
$pageTitle       = 'Contact Us';
$pageDescription = 'Get in touch with Redgum Community Library in Bendigo, Victoria. Send us a message and we will respond as soon as possible.';

$errors = [];
$name = $email = $message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $errors['general'] = 'Your session expired. Please try submitting the form again.';
    } else {
        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');

        // ---- Server-side validation ----
        if ($name === '' || mb_strlen($name) < 2) {
            $errors['name'] = 'Please enter your full name (at least 2 characters).';
        } elseif (mb_strlen($name) > 100) {
            $errors['name'] = 'Name must be under 100 characters.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if ($message === '' || mb_strlen($message) < 10) {
            $errors['message'] = 'Message must be at least 10 characters.';
        } elseif (mb_strlen($message) > 2000) {
            $errors['message'] = 'Message must be under 2000 characters.';
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message) VALUES (:name, :email, :message)");
            $stmt->execute([
                ':name'    => $name,
                ':email'   => $email,
                ':message' => $message,
            ]);
            $_SESSION['flash_success'] = 'Thank you, ' . $name . '! Your message has been received. We will respond by email soon.';
            redirect('contact.php');
        }
    }
}

include 'includes/header.php';
?>
<div class="container">
    <h1>Contact Us</h1>
    <p>Have a question about membership, services or programs? Send us a message below.</p>

    <?php if (!empty($errors['general'])): ?>
        <p class="field-error"><?= clean($errors['general']) ?></p>
    <?php endif; ?>

    <form method="post" action="contact.php" class="stack" novalidate>
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <div class="form-group">
            <label for="name">Full Name <span class="required-mark">*</span></label>
            <input type="text" id="name" name="name" value="<?= clean($name) ?>" required maxlength="100"
                   aria-describedby="nameError">
            <?php if (!empty($errors['name'])): ?><p id="nameError" class="field-error"><?= clean($errors['name']) ?></p><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email">Email Address <span class="required-mark">*</span></label>
            <input type="email" id="email" name="email" value="<?= clean($email) ?>" required
                   aria-describedby="emailError">
            <?php if (!empty($errors['email'])): ?><p id="emailError" class="field-error"><?= clean($errors['email']) ?></p><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="message">Message <span class="required-mark">*</span></label>
            <textarea id="message" name="message" rows="6" required maxlength="2000"
                      aria-describedby="messageError"><?= clean($message) ?></textarea>
            <?php if (!empty($errors['message'])): ?><p id="messageError" class="field-error"><?= clean($errors['message']) ?></p><?php endif; ?>
        </div>

        <button type="submit" class="btn">Send Message</button>
    </form>
</div>
<?php include 'includes/footer.php'; ?>
