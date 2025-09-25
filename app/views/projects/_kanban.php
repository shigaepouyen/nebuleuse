<?php // /app/views/projects/_kanban.php ?>
<div class="flex gap-4 overflow-x-auto pb-4" hx-ext="sortable" sortable-animation="150">
    <?php foreach ($columns as $column): ?>
        <div class="w-72 flex-shrink-0 bg-gray-100 rounded-lg flex flex-col">
            <div class="flex justify-between items-center p-3 bg-gray-200 rounded-t-lg">
                <h3 class="font-semibold text-gray-700">
                    <?= htmlspecialchars($column['name']) ?>
                </h3>
                <a href="/projects/<?= $project['id'] ?>/tasks/new?column=<?= $column['id'] ?>" class="text-gray-500 hover:text-indigo-600" title="Nouvelle tâche">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" /></svg>
                </a>
            </div>
            <div class="p-2 space-y-2 sortable-list flex-grow" 
                 data-column-id="<?= $column['id'] ?>"
                 hx-post="/tasks/move"
                 hx-trigger="end"
                 hx-include="[name='csrf_token']"
                 hx-indicator=".htmx-indicator">
                
                <?php foreach ($tasksByColumn[$column['id']] ?? [] as $task): ?>
                    <?php include __DIR__ . '/../partials/_task_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
document.addEventListener('htmx:load', function(event) {
    // Initialiser SortableJS sur toutes les listes
    document.querySelectorAll('.sortable-list').forEach(function(el) {
        new Sortable(el, {
            group: 'kanban', // Permet de glisser entre les colonnes
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function (evt) {
                // HTMX s'occupe de la requête POST grâce aux attributs hx-*
                // On doit juste lui dire de re-scanner la zone pour qu'il prenne en compte les changements
                htmx.process(evt.target.closest('.sortable-list'));
            }
        });
    });
});
</script>