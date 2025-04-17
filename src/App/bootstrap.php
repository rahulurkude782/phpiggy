<?php

declare(strict_types=1);

namespace App;

require __DIR__ . '/../../vendor/autoload.php';

use App\Config\Paths;
use Framework\App;

use function App\Config\registerMiddlewares;
use function App\Config\registerRoutes;
use Dotenv\Dotenv;

$env = Dotenv::createImmutable(Paths::ROOT);
$env->load();

$containerDefinitionsPath = 'App/container-definitions.php';

$app = new App($containerDefinitionsPath);

registerRoutes($app);
registerMiddlewares($app);

return $app;
