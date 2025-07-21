<?php ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Photo Gallery</h1>
    <?php if (Session::has('user_id')): ?>
    <a href="<?= View::url('/upload') ?>" class="btn btn-primary">
        Upload Photo
    </a>
    <?php endif; ?>
</div>

<?php if (empty($photos)): ?>
<div class="text-center py-5">
    <h3>No photos yet</h3>
    <p class="text-muted">Be the first to upload a photo!</p>
    <?php if (Session::has('user_id')): ?>
    <a href="<?= View::url('/upload') ?>" class="btn btn-primary">Upload Photo</a>
    <?php else: ?>
    <a href="<?= View::url('/login') ?>" class="btn btn-outline-primary">Login to Upload</a>
    <?php endif; ?>
</div>
<?php else: ?>

<div class="row">
    <?php foreach ($photos as $photo): ?>
    <div class="col-md-4 col-lg-3 mb-4">
        <div class="card">
            <a href="<?= View::url('/photos/' . $photo['id']) ?>">
                <img 
                    src="<?= View::url('/uploads/' . $photo['thumb_path']) ?>" 
                    class="card-img-top" 
                    alt="<?= View::escape($photo['title']) ?>"
                    style="height: 250px; object-fit: cover;"
                >
            </a>
            <div class="card-body">
                <h5 class="card-title">
                    <a href="<?= View::url('/photos/' . $photo['id']) ?>" class="text-decoration-none">
                        <?= View::escape($photo['title']) ?>
                    </a>
                </h5>
                <p class="card-text text-muted small">
                    By <?= View::escape($photo['user_name']) ?>
                    <br>
                    <?= date('M j, Y', strtotime($photo['created_at'])) ?>
                </p>
                <?php if ($photo['description']): ?>
                <p class="card-text">
                    <?= View::escape(substr($photo['description'], 0, 100)) ?>
                    <?= strlen($photo['description']) > 100 ? '...' : '' ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<nav aria-label="Photo pagination">
    <ul class="pagination justify-content-center">
        <?php if ($currentPage > 1): ?>
        <li class="page-item">
            <a class="page-link" href="<?= View::url('/?page=' . ($currentPage - 1)) ?>">Previous</a>
        </li>
        <?php endif; ?>
        
        <li class="page-item active">
            <span class="page-link"><?= $currentPage ?></span>
        </li>
        
        <?php if ($hasMorePhotos): ?>
        <li class="page-item">
            <a class="page-link" href="<?= View::url('/?page=' . ($currentPage + 1)) ?>">Next</a>
        </li>
        <?php endif; ?>
    </ul>
</nav>

<?php endif; ?>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layout.php'; ?>