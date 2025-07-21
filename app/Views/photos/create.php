<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Upload Photo</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= View::url('/photos') ?>" enctype="multipart/form-data">
                    <?= View::csrf() ?>
                    
                    <div class="mb-3">
                        <label for="photo" class="form-label">Photo <span class="text-danger">*</span></label>
                        <input 
                            type="file" 
                            class="form-control <?= View::error('photo') ? 'is-invalid' : '' ?>" 
                            id="photo" 
                            name="photo" 
                            accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                            required
                        >
                        <?php if ($error = View::error('photo')): ?>
                            <div class="invalid-feedback"><?= View::escape($error) ?></div>
                        <?php endif; ?>
                        <div class="form-text">Max file size: 10MB. Supported formats: JPEG, PNG, GIF, WebP</div>
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            class="form-control <?= View::error('title') ? 'is-invalid' : '' ?>" 
                            id="title" 
                            name="title" 
                            value="<?= View::escape(View::old('title')) ?>"
                            maxlength="140"
                            required
                        >
                        <?php if ($error = View::error('title')): ?>
                            <div class="invalid-feedback"><?= View::escape($error) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea 
                            class="form-control <?= View::error('description') ? 'is-invalid' : '' ?>" 
                            id="description" 
                            name="description" 
                            rows="4"
                            maxlength="1000"
                        ><?= View::escape(View::old('description')) ?></textarea>
                        <?php if ($error = View::error('description')): ?>
                            <div class="invalid-feedback"><?= View::escape($error) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input 
                            type="text" 
                            class="form-control <?= View::error('location') ? 'is-invalid' : '' ?>" 
                            id="location" 
                            name="location" 
                            value="<?= View::escape(View::old('location')) ?>"
                            maxlength="140"
                            placeholder="Where was this photo taken?"
                        >
                        <?php if ($error = View::error('location')): ?>
                            <div class="invalid-feedback"><?= View::escape($error) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= View::url('/') ?>" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Upload Photo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('photo').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const fileName = file.name;
        if (!document.getElementById('title').value) {
            const titleWithoutExt = fileName.substring(0, fileName.lastIndexOf('.')) || fileName;
            document.getElementById('title').value = titleWithoutExt;
        }
    }
});
</script>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layout.php'; ?>