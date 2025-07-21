<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= View::escape($title ?? 'FotoApp') ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= View::asset('css/app.css') ?>" rel="stylesheet">
    
    <?= View::csrfMeta() ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= View::url('/') ?>">FotoApp</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= View::url('/') ?>">Gallery</a>
                    </li>
                    <?php if (Session::has('user_id')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= View::url('/photos/upload') ?>">Upload</a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (Session::has('user_id')): ?>
                    <li class="nav-item">
                        <span class="navbar-text me-3">
                            Hello, <?= View::escape(Session::get('user_name')) ?>!
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= View::url('/logout') ?>">Logout</a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= View::url('/login') ?>">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= View::url('/register') ?>">Register</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container mt-4">
        <?php if ($message = Session::flash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= View::escape($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if ($message = Session::flash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= View::escape($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= View::asset('js/app.js') ?>"></script>
</body>
</html>