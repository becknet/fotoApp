<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\View::escape($title ?? 'FotoApp') ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= \App\View::asset('css/app.css') ?>" rel="stylesheet">
    
    <?= \App\View::csrfMeta() ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= \App\View::url('/') ?>">FotoApp</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= \App\View::url('/') ?>">Gallery</a>
                    </li>
                    <?php if (\App\View::auth()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= \App\View::url('/photos/upload') ?>">Upload</a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (\App\View::auth()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Hello, <?= \App\View::escape(\App\View::userName()) ?>!
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= \App\View::url('/change-password') ?>">Change Password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= \App\View::url('/logout') ?>">Logout</a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= \App\View::url('/login') ?>">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= \App\View::url('/register') ?>">Register</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container mt-4">
        <?php if ($message = \App\View::flash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= \App\View::escape($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if ($message = \App\View::flash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= \App\View::escape($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= \App\View::asset('js/app.js') ?>"></script>
</body>
</html>