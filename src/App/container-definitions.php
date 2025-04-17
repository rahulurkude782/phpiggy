<?php

declare(strict_types=1);

use Framework\{Database, TemplateEngine, Container};
use App\Services\{ReceiptService, ValidatorService, UserService, TransactionService};
use App\Config\Paths;

return [
    TemplateEngine::class => fn() => new TemplateEngine(Paths::VIEW),
    ValidatorService::class => fn() => new ValidatorService(),
    Database::class => fn() => new Database($_ENV['DB_DRIVER'], [
        'host' => $_ENV['DB_HOST'],
        'port' => $_ENV['DB_PORT'],
        'dbname' => $_ENV['DB_NAME'],
        'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4'
    ], $_ENV['DB_USERNAME'], $_ENV['DB_PASS']),

    /* Manual dep-injection in action */
    UserService::class => function (Container $container) {
        $db = $container->get(Database::class);
        return new UserService($db);
    },

    TransactionService::class => function (Container $container) {
        $db = $container->get(Database::class);
        return new TransactionService($db);
    },
    ReceiptService::class => function (Container $container) {
        $db = $container->get(Database::class);
        return new ReceiptService($db);
    }
];
