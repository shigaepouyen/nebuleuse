<header class="mb-8">
    <div class="flex justify-between items-start">
        <div>
            <p class="text-sm text-gray-500">
                <a href="/projects/<?= $task['project_id'] ?>" class="hover:underline">
                    Projet: <?= htmlspecialchars($task['project_title']) ?>
                </a>
            </p>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 mt-1">
                <?= htmlspecialchars($task['title']) ?>
            </h1>
        </div>
        
        <div>
            <?php if (is_null($task['done_at'])): ?>
                <form action="/tasks/<?= $task['id'] ?>/complete" method="POST">
                    <?= CSRF::field() ?>
                    <button type="submit" class="rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                        ✔️ Marquer comme "Terminé"
                    </button>
                </form>
            <?php else: ?>
                <span class="inline-flex items-center gap-x-1.5 rounded-md bg-green-100 px-3 py-2 text-sm font-semibold text-green-700">
                    <svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                    Terminée le <?= date('d/m/Y', strtotime($task['done_at'])) ?>
                </span>
            <?php endif; ?>
        </div>
        </div>
</header>

<div class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-lg font-semibold mb-2">Description</h2>
    <div class="prose max-w-none">
        <?php if (!empty($task['description_html'])): ?>
            <?= $task['description_html'] // Le HTML est déjà échappé ou purifié ?>
        <?php else: ?>
            <p class="text-gray-500 italic">Aucune description.</p>
        <?php endif; ?>
    </div>
    
    <hr class="my-6">
    
    <hr class="my-6">

    <div>
        <h2 class="text-lg font-semibold mb-4">Checklist</h2>
        <div class="space-y-2">
            <?php foreach ($checklist as $item): ?>
                <div class="flex items-center">
                    <form action="/tasks/<?= $task['id'] ?>/checklist/<?= $item['id'] ?>/toggle" method="POST" class="mr-2">
                        <?= CSRF::field() ?>
                        <input type="checkbox" name="checked" <?= $item['checked'] ? 'checked' : '' ?> onchange="this.form.submit()"
                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                    </form>
                    <span class="<?= $item['checked'] ? 'line-through text-gray-500' : '' ?>">
                        <?= htmlspecialchars($item['label']) ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
        <form action="/tasks/<?= $task['id'] ?>/checklist" method="POST" class="mt-4 flex gap-2">
            <?= CSRF::field() ?>
            <input type="text" name="label" placeholder="Ajouter un élément..." required
                class="flex-grow block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
            <button type="submit" class="rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Ajouter</button>
        </form>
    </div>

    </div>