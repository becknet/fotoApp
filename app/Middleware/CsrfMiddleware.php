<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Csrf;

class CsrfMiddleware
{
    public function handle(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::verify();
        }
    }
}