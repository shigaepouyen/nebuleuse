<a href="/tasks/<?= $task['id'] ?>" class="task-card block bg-white p-3 rounded-md shadow-sm border border-gray-200 hover:border-indigo-500 hover:ring-1 hover:ring-indigo-500">
    <p class="font-semibold text-sm text-gray-800"><?= htmlspecialchars($task['title']) ?></p>
    <?php if ($task['due_date']): ?>
        <p class="text-xs text-gray-500 mt-1">
            Échéance: <?= date('d/m/Y', strtotime($task['due_date'])) ?>
        </p>
    <?php endif; ?>
</a>