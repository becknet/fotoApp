<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Edit Photo</h4>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <img 
                            src="<?= \App\View::url('/uploads/' . $photo['thumb_path']) ?>" 
                            class="img-fluid rounded" 
                            alt="<?= \App\View::escape($photo['title']) ?>"
                        >
                    </div>
                    <div class="col-md-8">
                        <form method="POST" action="<?= \App\View::url('/photos/' . $photo['id'] . '/update') ?>">
                            <?= \App\View::csrf() ?>
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    class="form-control <?= \App\View::error('title') ? 'is-invalid' : '' ?>" 
                                    id="title" 
                                    name="title" 
                                    value="<?= \App\View::escape(\App\View::old('title') ?: $photo['title']) ?>"
                                    maxlength="140"
                                    required
                                >
                                <?php if ($error = \App\View::error('title')): ?>
                                    <div class="invalid-feedback"><?= \App\View::escape($error) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea 
                                    class="form-control <?= \App\View::error('description') ? 'is-invalid' : '' ?>" 
                                    id="description" 
                                    name="description" 
                                    rows="4"
                                    maxlength="1000"
                                ><?= \App\View::escape(\App\View::old('description') ?: $photo['description']) ?></textarea>
                                <?php if ($error = \App\View::error('description')): ?>
                                    <div class="invalid-feedback"><?= \App\View::escape($error) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input 
                                    type="text" 
                                    class="form-control <?= \App\View::error('location') ? 'is-invalid' : '' ?>" 
                                    id="location" 
                                    name="location" 
                                    value="<?= \App\View::escape(\App\View::old('location') ?: $photo['location']) ?>"
                                    maxlength="140"
                                >
                                <?php if ($error = \App\View::error('location')): ?>
                                    <div class="invalid-feedback"><?= \App\View::escape($error) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="<?= \App\View::url('/photos/' . $photo['id']) ?>" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Photo</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layout.php'; ?>