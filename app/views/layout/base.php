<!DOCTYPE html>
<html lang="fr" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Simple style for kanban drag-and-drop */
        .kanban-column .task-card { cursor: grab; }
        .kanban-column .task-card:active { cursor: grabbing; }
        .sortable-ghost { opacity: 0.4; background: #cce5ff; }
    </style>
</head>
<body class="h-full">
    <div class="min-h-full">
        <?php require 'nav.php'; ?>

        <main class="py-10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                
                <?php if ($message = Flash::get('success')): ?>
                    <div class="mb-4 rounded-md bg-green-50 p-4 border border-green-200" role="alert">
                        <p class="text-sm font-medium text-green-800"><?= $message ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($message = Flash::get('error')): ?>
                    <div class="mb-4 rounded-md bg-red-50 p-4 border border-red-200" role="alert">
                        <p class="text-sm font-medium text-red-800"><?= $message ?></p>
                    </div>
                <?php endif; ?>
                <?php require $content; ?>
            </div>
        </main>
    </div>
    
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
</body>
</html>