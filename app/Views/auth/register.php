<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Register</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= View::url('/register') ?>">
                    <?= View::csrf() ?>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input 
                            type="text" 
                            class="form-control <?= View::error('name') ? 'is-invalid' : '' ?>" 
                            id="name" 
                            name="name" 
                            value="<?= View::escape(View::old('name')) ?>"
                            maxlength="60"
                            required
                        >
                        <?php if ($error = View::error('name')): ?>
                            <div class="invalid-feedback"><?= View::escape($error) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input 
                            type="email" 
                            class="form-control <?= View::error('email') ? 'is-invalid' : '' ?>" 
                            id="email" 
                            name="email" 
                            value="<?= View::escape(View::old('email')) ?>"
                            maxlength="255"
                            required
                        >
                        <?php if ($error = View::error('email')): ?>
                            <div class="invalid-feedback"><?= View::escape($error) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input 
                            type="password" 
                            class="form-control <?= View::error('password') ? 'is-invalid' : '' ?>" 
                            id="password" 
                            name="password" 
                            minlength="8"
                            required
                        >
                        <?php if ($error = View::error('password')): ?>
                            <div class="invalid-feedback"><?= View::escape($error) ?></div>
                        <?php endif; ?>
                        <div class="form-text">Minimum 8 characters</div>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            minlength="8"
                            required
                        >
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                </form>
                
                <hr>
                
                <p class="text-center mb-0">
                    Already have an account? 
                    <a href="<?= View::url('/login') ?>">Login here</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layout.php'; ?>