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
                <?php require $content; ?>
            </div>
        </main>
    </div>
    
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
</body>
</html>