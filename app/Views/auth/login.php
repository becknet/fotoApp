<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Login</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= \App\View::url('/login') ?>">
                    <?= \App\View::csrf() ?>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input 
                            type="email" 
                            class="form-control <?= \App\View::error('email') ? 'is-invalid' : '' ?>" 
                            id="email" 
                            name="email" 
                            value="<?= \App\View::escape(\App\View::old('email')) ?>"
                            required
                        >
                        <?php if ($error = \App\View::error('email')): ?>
                            <div class="invalid-feedback"><?= \App\View::escape($error) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input 
                            type="password" 
                            class="form-control <?= \App\View::error('password') ? 'is-invalid' : '' ?>" 
                            id="password" 
                            name="password" 
                            required
                        >
                        <?php if ($error = \App\View::error('password')): ?>
                            <div class="invalid-feedback"><?= \App\View::escape($error) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
                
                <hr>
                
                <p class="text-center mb-0">
                    Don't have an account? 
                    <a href="<?= \App\View::url('/register') ?>">Register here</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layout.php'; ?>