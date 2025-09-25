<?php
// /public/index.php

session_start();

// Autoloader simple
spl_autoload_register(function ($class_name) {
    $paths = [
        'controllers/',
        'models/',
        'lib/'
    ];
    foreach ($paths as $path) {
        $file = __DIR__ . '/../app/' . $path . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/lib/Auth.php';
require_once __DIR__ . '/../app/lib/CSRF.php';
require_once __DIR__ . '/../app/lib/Validator.php';

// Routage
$router = new Router();

// API pour le JS
$router->add('GET', '/api/projects/(\d+)/columns', [ApiController::class, 'getProjectColumns'], true);

// Routes publiques
$router->add('GET', '/login', [AuthController::class, 'showLogin']);
$router->add('POST', '/login', [AuthController::class, 'handleLogin']);
$router->add('GET', '/logout', [AuthController::class, 'logout']);
$router->add('GET', '/migrate', function() { require __DIR__ . '/../app/scripts/migrate.php'; });

// Routes protégées
$router->add('GET', '/', [DashboardController::class, 'index'], true);

// Projets
$router->add('GET', '/projects/new', [ProjectController::class, 'create'], true);
$router->add('POST', '/projects', [ProjectController::class, 'store'], true);
$router->add('GET', '/projects/(\d+)', [ProjectController::class, 'show'], true);

// Tâches
$router->add('GET', '/projects/(\d+)/tasks/new', [TaskController::class, 'new'], true);
$router->add('POST', '/projects/(\d+)/tasks', [TaskController::class, 'store'], true);
$router->add('GET', '/tasks/(\d+)', [TaskController::class, 'show'], true);
$router->add('POST', '/tasks/(\d+)/complete', [TaskController::class, 'complete'], true); // <-- AJOUTER CETTE LIGNE
$router->add('POST', '/tasks/move', [TaskController::class, 'move'], true);
$router->add('POST', '/tasks/(\d+)/checklist', [TaskController::class, 'addChecklistItem'], true);
$router->add('POST', '/tasks/(\d+)/checklist/(\d+)/toggle', [TaskController::class, 'toggleChecklistItem'], true);


// Idées
$router->add('GET', '/ideas', [IdeaController::class, 'index'], true);
$router->add('POST', '/ideas', [IdeaController::class, 'store'], true);
$router->add('POST', '/ideas/(\d+)/delete', [IdeaController::class, 'delete'], true);
$router->add('GET', '/ideas/(\d+)/convert', [IdeaController::class, 'showConvertToTaskForm'], true);
$router->add('POST', '/ideas/(\d+)/convert', [IdeaController::class, 'handleConvertToTask'], true);

// Maintenance
$router->add('GET', '/maintenance', [MaintenanceController::class, 'index'], true);

$router->dispatch();