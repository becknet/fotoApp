<?php ob_start(); ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <img 
                src="<?= View::url('/uploads/' . $photo['file_path']) ?>" 
                class="card-img-top" 
                alt="<?= View::escape($photo['title']) ?>"
            >
            <div class="card-body">
                <h1 class="card-title"><?= View::escape($photo['title']) ?></h1>
                
                <?php if ($photo['description']): ?>
                <p class="card-text"><?= View::escape($photo['description']) ?></p>
                <?php endif; ?>
                
                <div class="row text-muted small">
                    <div class="col-md-6">
                        <strong>Uploaded by:</strong> <?= View::escape($photo['user_name']) ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Date:</strong> <?= date('F j, Y \a\t g:i A', strtotime($photo['created_at'])) ?>
                    </div>
                </div>
                
                <?php if ($photo['location']): ?>
                <div class="mt-2 text-muted small">
                    <strong>Location:</strong> <?= View::escape($photo['location']) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="d-grid gap-2">
            <a href="<?= View::url('/') ?>" class="btn btn-outline-secondary">
                ← Back to Gallery
            </a>
            
            <?php if ($isOwner): ?>
            <a href="<?= View::url('/photos/' . $photo['id'] . '/edit') ?>" class="btn btn-primary">
                Edit Photo
            </a>
            
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                Delete Photo
            </button>
            <?php endif; ?>
        </div>
        
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Photo Info</h5>
                <p class="card-text small">
                    <strong>ID:</strong> <?= $photo['id'] ?><br>
                    <strong>Uploaded:</strong> <?= date('M j, Y', strtotime($photo['created_at'])) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php if ($isOwner): ?>
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this photo? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="<?= View::url('/photos/' . $photo['id'] . '/delete') ?>" style="display: inline;">
                    <?= View::csrf() ?>
                    <button type="submit" class="btn btn-danger">Delete Photo</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layout.php'; ?>