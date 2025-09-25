<?php // /app/views/projects/show.php ?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($project['title']) ?></h1>
            <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($project['title']) ?></h1>
            <div class="mt-2 flex flex-wrap gap-2">
                <?php foreach ($tags as $tag): ?>
                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium text-white" style="background-color: <?= htmlspecialchars($tag['color']) ?>">
                        <?= htmlspecialchars($tag['name']) ?>
                    </span>
                <?php endforeach; ?>
            </div>
            <p class="text-sm text-gray-500"><?= htmlspecialchars($project['description']) ?></p>
        </div>
        </div>
</div>

<input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">

<?php require '_kanban.php'; ?>