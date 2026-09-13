<?php
require_once 'includes/functions.php';
$pageTitle       = 'Gallery';
$pageDescription = 'Photos and videos of Redgum Community Library: reading areas, book collections, study spaces and community events.';
include 'includes/header.php';

$photos = [
    ['src' => 'images/gallery/children_reading.jpg', 'alt' => 'Modern library interior with natural light and reading nooks'],
    ['src' => 'images/gallery/book_shalves.jpg', 'alt' => 'Rows of book collections on wooden shelving'],
    ['src' => 'images/gallery/modern_library.jpg', 'alt' => 'Quiet study area with desks and lamps'],
    ['src' => 'images/gallery/local_event.jpg', 'alt' => 'Community members attending a library event'],
    ['src' => 'images/gallery/library_management.jpg', 'alt' => 'Children\'s reading space with colourful cushions'],
];
?>
<div class="container">
    <h1>Gallery</h1>
    <p>Browse photos of Redgum Community Library. Click any photo to view it larger.</p>

    <div class="grid gallery-grid">
        <?php foreach ($photos as $p): ?>
            <img src="<?= clean($p['src']) ?>" alt="<?= clean($p['alt']) ?>" loading="lazy"
                 onclick="openLightbox(this.src, this.alt)"
                 onerror="this.src='images/gallery/placeholder.jpg'">
        <?php endforeach; ?>
    </div>

    <h2>Video &amp; Audio</h2>
    <div class="grid">
        <div class="card">
            <h3>Virtual Library Tour</h3>
            <video controls width="100%" preload="none">
                <source src="videos/library-tour.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
        <div class="card">
            <h3>Story Time Recording</h3>
            <audio controls preload="none" style="width:100%">
                <source src="audio/librrary.mp3" type="audio/mpeg">
                Your browser does not support the audio element.
            </audio>
        </div>
    </div>
</div>

<!-- Lightbox -->
<div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="Enlarged photo">
    <button class="lightbox-close" onclick="closeLightbox()" aria-label="Close enlarged photo">&times;</button>
    <img id="lightboxImg" src="" alt="">
</div>

<script>
function openLightbox(src, alt) {
    const lb = document.getElementById('lightbox');
    const img = document.getElementById('lightboxImg');
    img.src = src;
    img.alt = alt;
    lb.classList.add('open');
}
function closeLightbox() {
    document.getElementById('lightbox').classList.remove('open');
}
document.getElementById('lightbox').addEventListener('click', (e) => {
    if (e.target.id === 'lightbox') closeLightbox();
});
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeLightbox(); });
</script>

<?php include 'includes/footer.php'; ?>
