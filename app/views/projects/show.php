<?php // /app/views/projects/show.php ?>
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($project['title']) ?></h1>
            <p class="text-sm text-gray-500"><?= htmlspecialchars($project['description']) ?></p>
        </div>
        <div>
            <?php if ($project['github_url']): ?>
            <a href="<?= htmlspecialchars($project['github_url']) ?>" target="_blank" class="inline-flex items-center gap-x-1.5 rounded-md bg-gray-800 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700">
                Voir sur GitHub
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4"><path d="M4.25 10.75a.75.75 0 0 0 0 1.5h7.5a.75.75 0 0 0 0-1.5h-7.5Z"></path><path fill-rule="evenodd" d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0ZM1.5 8a6.5 6.5 0 1 1 13 0 6.5 6.5 0 0 1-13 0Z"></path></svg>
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require '_kanban.php'; ?>