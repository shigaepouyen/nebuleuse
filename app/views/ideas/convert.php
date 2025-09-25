<header class="mb-8">
    <h1 class="text-3xl font-bold tracking-tight text-gray-900">Convertir une Idée en Tâche</h1>
    <p class="text-gray-600 mt-1">Idée : "<span class="font-semibold"><?= htmlspecialchars($idea['title']) ?></span>"</p>
</header>

<div class="bg-white p-6 rounded-lg shadow max-w-2xl mx-auto">
    <form action="/ideas/<?= $idea['id'] ?>/convert" method="POST" class="space-y-6">
        <?= CSRF::field() ?>

        <div>
            <label for="project_id" class="block text-sm font-medium leading-6 text-gray-900">Choisir le projet de destination</label>
            <div class="mt-2">
                <select id="project_id" name="project_id" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                    <option value="">-- Sélectionnez un projet --</option>
                    <?php foreach ($projects as $project): ?>
                        <option value="<?= $project['id'] ?>"><?= htmlspecialchars($project['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div>
            <label for="column_id" class="block text-sm font-medium leading-6 text-gray-900">Choisir la colonne</label>
            <div class="mt-2">
                <select id="column_id" name="column_id" required disabled class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 disabled:bg-gray-100">
                    <option>-- Sélectionnez d'abord un projet --</option>
                </select>
            </div>
        </div>
        
        <div class="flex justify-end gap-4">
            <a href="/ideas" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Annuler</a>
            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Convertir en Tâche</button>
        </div>
    </form>
</div>

<script>
document.getElementById('project_id').addEventListener('change', function() {
    const projectId = this.value;
    const columnSelect = document.getElementById('column_id');
    columnSelect.disabled = true;
    columnSelect.innerHTML = '<option>Chargement...</option>';

    if (!projectId) {
        columnSelect.innerHTML = '<option>-- Sélectionnez d\'abord un projet --</option>';
        return;
    }

    // Appel AJAX pour récupérer les colonnes du projet
    fetch(`/api/projects/${projectId}/columns`)
        .then(response => response.json())
        .then(columns => {
            columnSelect.innerHTML = '';
            if (columns.length > 0) {
                columns.forEach(column => {
                    const option = document.createElement('option');
                    option.value = column.id;
                    option.textContent = column.name;
                    columnSelect.appendChild(option);
                });
                columnSelect.disabled = false;
            } else {
                columnSelect.innerHTML = '<option>Aucune colonne trouvée</option>';
            }
        });
});
</script>