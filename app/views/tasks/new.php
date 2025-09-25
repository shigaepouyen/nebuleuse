<header class="mb-8">
    <h1 class="text-3xl font-bold tracking-tight text-gray-900">Nouvelle Tâche</h1>
    <p class="text-gray-600">Pour le projet : <span class="font-semibold"><?= htmlspecialchars($project['title']) ?></span></p>
</header>

<div class="bg-white p-6 rounded-lg shadow max-w-2xl mx-auto">
    <form action="/projects/<?= $project['id'] ?>/tasks" method="POST" class="space-y-6">
        <?= CSRF::field() ?>
        
        <div>
            <label for="title" class="block text-sm font-medium leading-6 text-gray-900">Titre <span class="text-red-500">*</span></label>
            <div class="mt-2">
                <input type="text" name="title" id="title" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
            </div>
        </div>

        <div>
            <label for="column_id" class="block text-sm font-medium leading-6 text-gray-900">Colonne</label>
            <div class="mt-2">
                <select id="column_id" name="column_id" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                    <?php foreach ($columns as $column): ?>
                        <option value="<?= $column['id'] ?>"><?= htmlspecialchars($column['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div>
            <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Description (Markdown supporté)</label>
            <div class="mt-2">
                <textarea name="description" id="description" rows="5" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600"></textarea>
            </div>
        </div>
        
        <div class="flex justify-end gap-4">
            <a href="/projects/<?= $project['id'] ?>" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Annuler</a>
            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Créer la Tâche</button>
        </div>
    </form>
</div>