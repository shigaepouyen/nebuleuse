<?php // /app/views/projects/show.php ?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($project['title']) ?></h1>
            <p class="text-sm text-gray-500"><?= htmlspecialchars($project['description']) ?></p>
        </div>
        </div>
</div>

<input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">

<?php require '_kanban.php'; ?>