<?php

namespace App\Config;

use Framework\App;

use App\Controllers\{HomeController, AboutController, AuthController, ErrorController, ReceiptController, TransactionController};
use App\Middlewares\{AuthRequiredMiddleware, GuestOnlyMiddleware};

function registerRoutes(App $app)
{
    $app->get('/', [HomeController::class, 'index'])->addRouteMiddleware(AuthRequiredMiddleware::class);
    $app->get('/about', [AboutController::class, 'index']);
    $app->get('/register', [AuthController::class, 'registerView'])->addRouteMiddleware(GuestOnlyMiddleware::class);
    $app->post('/register', [AuthController::class, 'register']);
    $app->get('/login', [AuthController::class, 'loginView'])->addRouteMiddleware(GuestOnlyMiddleware::class);
    $app->post('/login', [AuthController::class, 'login']);
    $app->get('/logout', [AuthController::class, 'logout'])->addRouteMiddleware(AuthRequiredMiddleware::class);
    $app->get('/transaction', [TransactionController::class, 'create']);
    $app->post('/transaction', [TransactionController::class, 'store'])->addRouteMiddleware(AuthRequiredMiddleware::class);
    $app->get('/transaction/{transaction}', [TransactionController::class, 'edit'])->addRouteMiddleware(AuthRequiredMiddleware::class);
    $app->post('/transaction/{transaction}', [TransactionController::class, 'update'])->addRouteMiddleware(AuthRequiredMiddleware::class);
    $app->delete('/transaction/{transaction}', [TransactionController::class, 'delete'])->addRouteMiddleware(AuthRequiredMiddleware::class);
    $app->get('/transaction/{transaction}/receipt', [ReceiptController::class, 'create']);
    $app->post('/transaction/{transaction}/receipt', [ReceiptController::class, 'store'])->addRouteMiddleware(AuthRequiredMiddleware::class);
    $app->get('/transaction/{transaction}/receipt/{receipt}', [ReceiptController::class, 'download'])->addRouteMiddleware(AuthRequiredMiddleware::class);
    $app->delete('/transaction/{transaction}/receipt/{receipt}', [ReceiptController::class, 'delete'])->addRouteMiddleware(AuthRequiredMiddleware::class);
    $app->setErrorHandler([ErrorController::class, 'notFound']);
}
