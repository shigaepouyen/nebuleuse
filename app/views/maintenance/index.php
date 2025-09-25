<header class="mb-8">
    <h1 class="text-3xl font-bold tracking-tight text-gray-900">Maintenance</h1>
    <p class="text-gray-600 mt-1">Toutes les tâches de maintenance planifiées, triées par urgence.</p>
</header>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tâche</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Projet</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Échéance</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if (empty($tasks)): ?>
                <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">Aucune tâche de maintenance en cours.</td></tr>
            <?php else: ?>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap"><a href="/tasks/<?= $task['id'] ?>" class="text-indigo-600 hover:underline font-semibold"><?= htmlspecialchars($task['title']) ?></a></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($task['project_title']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $task['due_date'] ? date('d/m/Y', strtotime($task['due_date'])) : 'N/A' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>