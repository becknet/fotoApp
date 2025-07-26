<?php ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1>My Photos</h1>
        <p class="text-muted mb-0">You have uploaded <?= $totalPhotos ?> photo<?= $totalPhotos !== 1 ? 's' : '' ?></p>
    </div>
    <a href="<?= \App\View::url('/upload') ?>" class="btn btn-primary">
        Upload New Photo
    </a>
</div>

<?php if (empty($photos)): ?>
<div class="text-center py-5">
    <h3>No photos yet</h3>
    <p class="text-muted">Upload your first photo to get started!</p>
    <a href="<?= \App\View::url('/upload') ?>" class="btn btn-primary">Upload Photo</a>
</div>
<?php else: ?>

<div class="row">
    <?php foreach ($photos as $photo): ?>
    <div class="col-md-4 col-lg-3 mb-4">
        <div class="card">
            <a href="<?= \App\View::url('/photos/' . $photo['id']) ?>">
                <img 
                    src="<?= \App\View::url('/uploads/' . $photo['thumb_path']) ?>" 
                    class="card-img-top" 
                    alt="<?= \App\View::escape($photo['title']) ?>"
                    style="height: 250px; object-fit: cover;"
                >
            </a>
            <div class="card-body">
                <h5 class="card-title">
                    <a href="<?= \App\View::url('/photos/' . $photo['id']) ?>" class="text-decoration-none">
                        <?= \App\View::escape($photo['title']) ?>
                    </a>
                </h5>
                <p class="card-text text-muted small">
                    Uploaded on <?= date('M j, Y', strtotime($photo['created_at'])) ?>
                </p>
                <?php if ($photo['description']): ?>
                <p class="card-text">
                    <?= \App\View::escape(substr($photo['description'], 0, 80)) ?>
                    <?= strlen($photo['description']) > 80 ? '...' : '' ?>
                </p>
                <?php endif; ?>
                
                <div class="d-flex gap-2 mt-3">
                    <a href="<?= \App\View::url('/photos/' . $photo['id'] . '/edit') ?>" 
                       class="btn btn-sm btn-outline-primary flex-fill">
                        Edit
                    </a>
                    <form method="POST" action="<?= \App\View::url('/photos/' . $photo['id'] . '/delete') ?>" 
                          class="flex-fill" 
                          onsubmit="return confirm('Are you sure you want to delete this photo? This action cannot be undone.')">
                        <?= \App\View::csrf() ?>
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if ($currentPage > 1 || $hasMorePhotos): ?>
<nav aria-label="Dashboard pagination">
    <ul class="pagination justify-content-center">
        <?php if ($currentPage > 1): ?>
        <li class="page-item">
            <a class="page-link" href="<?= \App\View::url('/dashboard?page=' . ($currentPage - 1)) ?>">Previous</a>
        </li>
        <?php endif; ?>
        
        <li class="page-item active">
            <span class="page-link"><?= $currentPage ?></span>
        </li>
        
        <?php if ($hasMorePhotos): ?>
        <li class="page-item">
            <a class="page-link" href="<?= \App\View::url('/dashboard?page=' . ($currentPage + 1)) ?>">Next</a>
        </li>
        <?php endif; ?>
    </ul>
</nav>
<?php endif; ?>

<?php endif; ?>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layout.php'; ?>