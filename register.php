<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
$pageTitle       = 'Register';
$pageDescription = 'Create a free Redgum Community Library membership account to borrow books and access member services.';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$errors = [];
$full_name = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $errors['general'] = 'Your session expired. Please try again.';
    } else {
        $full_name = trim($_POST['full_name'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $password  = $_POST['password'] ?? '';
        $confirm   = $_POST['confirm_password'] ?? '';

        // ---- Validation ----
        if ($full_name === '' || mb_strlen($full_name) < 2) {
            $errors['full_name'] = 'Please enter your full name.';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }
        if (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters.';
        } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errors['password'] = 'Password must include at least one uppercase letter and one number.';
        }
        if ($password !== $confirm) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }

        // Check for existing email
        if (empty($errors['email'])) {
            $check = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $check->execute([':email' => $email]);
            if ($check->fetch()) {
                $errors['email'] = 'An account with this email already exists.';
            }
        }

        if (empty($errors)) {
            $hash = password_hash($password, PASSWORD_BCRYPT); // secure password storage
            $stmt = $pdo->prepare(
                "INSERT INTO users (full_name, email, password_hash, role) VALUES (:full_name, :email, :hash, 'member')"
            );
            $stmt->execute([
                ':full_name' => $full_name,
                ':email'     => $email,
                ':hash'      => $hash,
            ]);
            $_SESSION['flash_success'] = 'Registration successful! You can now log in.';
            redirect('login.php');
        }
    }
}

include 'includes/header.php';
?>
<div class="container">
    <h1>Create Your Free Membership</h1>

    <?php if (!empty($errors['general'])): ?>
        <p class="field-error"><?= clean($errors['general']) ?></p>
    <?php endif; ?>

    <form method="post" action="register.php" class="stack" novalidate>
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <div class="form-group">
            <label for="full_name">Full Name <span class="required-mark">*</span></label>
            <input type="text" id="full_name" name="full_name" value="<?= clean($full_name) ?>" required maxlength="100">
            <?php if (!empty($errors['full_name'])): ?><p class="field-error"><?= clean($errors['full_name']) ?></p><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email">Email Address <span class="required-mark">*</span></label>
            <input type="email" id="email" name="email" value="<?= clean($email) ?>" required>
            <?php if (!empty($errors['email'])): ?><p class="field-error"><?= clean($errors['email']) ?></p><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password">Password <span class="required-mark">*</span></label>
            <input type="password" id="password" name="password" required minlength="8"
                   aria-describedby="pwHint">
            <p id="pwHint" style="font-size:0.85rem;color:#666;">At least 8 characters, with 1 uppercase letter and 1 number.</p>
            <?php if (!empty($errors['password'])): ?><p class="field-error"><?= clean($errors['password']) ?></p><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password <span class="required-mark">*</span></label>
            <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
            <?php if (!empty($errors['confirm_password'])): ?><p class="field-error"><?= clean($errors['confirm_password']) ?></p><?php endif; ?>
        </div>

        <p style="font-size:0.85rem;">By registering you agree to our <a href="privacy.php">Privacy Notice</a>.</p>

        <button type="submit" class="btn">Register</button>
    </form>

    <p>Already a member? <a href="login.php">Log in here</a>.</p>
</div>
<?php include 'includes/footer.php'; ?>
