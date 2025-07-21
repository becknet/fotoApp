<?php ob_start(); ?>

<div class="text-center py-5">
    <h1 class="display-1">404</h1>
    <h2>Page Not Found</h2>
    <p class="lead">The page you are looking for could not be found.</p>
    <a href="<?= View::url('/') ?>" class="btn btn-primary">Go Home</a>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layout.php'; ?>