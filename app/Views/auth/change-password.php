<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Change Password</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= \App\View::url('/change-password') ?>">
                    <?= \App\View::csrf() ?>
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input 
                            type="password" 
                            class="form-control <?= \App\View::error('current_password') ? 'is-invalid' : '' ?>" 
                            id="current_password" 
                            name="current_password" 
                            required
                        >
                        <?php if ($error = \App\View::error('current_password')): ?>
                            <div class="invalid-feedback"><?= \App\View::escape($error) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input 
                            type="password" 
                            class="form-control <?= \App\View::error('new_password') ? 'is-invalid' : '' ?>" 
                            id="new_password" 
                            name="new_password" 
                            minlength="8"
                            required
                        >
                        <?php if ($error = \App\View::error('new_password')): ?>
                            <div class="invalid-feedback"><?= \App\View::escape($error) ?></div>
                        <?php endif; ?>
                        <div class="form-text">Minimum 8 characters</div>
                    </div>

                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="new_password_confirmation" 
                            name="new_password_confirmation" 
                            minlength="8"
                            required
                        >
                        <div class="form-text">Re-enter your new password</div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Change Password</button>
                        <a href="<?= \App\View::url('/') ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
                
                <hr>
                
                <div class="alert alert-info">
                    <h6 class="alert-heading">Password Requirements:</h6>
                    <ul class="mb-0 small">
                        <li>Minimum 8 characters</li>
                        <li>Must confirm your current password</li>
                        <li>New password must be confirmed by typing it twice</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layout.php'; ?>