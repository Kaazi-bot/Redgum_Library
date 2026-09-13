</main>

<footer class="site-footer">
    <div class="footer-inner">
        <p>&copy; <?= date('Y') ?> Redgum Community Library, Bendigo VIC. All rights reserved.</p>
        <p>
            <a href="<?= BASE_URL ?>privacy.php">Privacy Notice</a> &middot;
            <a href="<?= BASE_URL ?>about.php">About</a> &middot;
            <a href="<?= BASE_URL ?>contact.php">Contact</a>
        </p>
        <p class="footer-note">This is a student project created for ICT726 Assignment 4 (King's Own Institute).</p>
    </div>
</footer>

<script>
    // Mobile navigation toggle
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    if (navToggle) {
        navToggle.addEventListener('click', () => {
            const expanded = navToggle.getAttribute('aria-expanded') === 'true';
            navToggle.setAttribute('aria-expanded', String(!expanded));
            navMenu.classList.toggle('open');
        });
    }
</script>
</body>
</html>
