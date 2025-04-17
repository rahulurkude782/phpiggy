<?php

declare(strict_types=1);

use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;

// Bootstrap your framework
$app = require __DIR__ . '/path/to/bootstrap.php'; // Adjust the path as needed

$server = new Server("127.0.0.1", 9501);

$server->on("start", function () {
    echo "⚡ Swoole server started at http://127.0.0.1:9501\n";
});

$server->on("request", function (Request $swooleRequest, Response $swooleResponse) use ($app) {
    // Map Swoole request data to PHP superglobals
    $_GET = $swooleRequest->get ?? [];
    $_POST = $swooleRequest->post ?? [];
    $_COOKIE = $swooleRequest->cookie ?? [];
    $_FILES = $swooleRequest->files ?? [];
    $_SERVER = array_change_key_case($swooleRequest->server ?? [], CASE_UPPER);

    // Capture output
    ob_start();

    try {
        $app->run(); // Your framework handles routing, controller logic, etc.
    } catch (Throwable $e) {
        http_response_code(500);
        echo "Internal Server Error: " . $e->getMessage();
    }

    $content = ob_get_clean();
    $swooleResponse->end($content);
});

$server->start();
