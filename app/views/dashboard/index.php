<header class="mb-8 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Tableau de bord</h1>
        <p class="text-gray-600 mt-1">Vue d'ensemble de votre activité.</p>
    </div>
    <div class="flex gap-2">
        <a href="/projects/new" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">+ Nouveau Projet</a>
        <a href="/ideas" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">+ Nouvelle Idée</a>
    </div>
</header>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <div class="lg:col-span-2">
        <h2 class="text-xl font-semibold mb-4">Projets Actifs</h2>
        <div class="space-y-4">
            <?php if (empty($projects)): ?>
                <div class="text-center bg-white p-8 rounded-lg shadow">
                    <p class="text-gray-500">Aucun projet actif. Cliquez sur "Nouveau Projet" pour commencer.</p>
                </div>
            <?php else: ?>
                <?php foreach ($projects as $project): ?>
                    <a href="/projects/<?= $project['id'] ?>" class="block bg-white p-4 rounded-lg shadow hover:shadow-md transition-shadow">
                        <h3 class="font-bold text-lg text-indigo-600"><?= htmlspecialchars($project['title']) ?></h3>
                        <p class="text-gray-600 text-sm mt-1"><?= htmlspecialchars($project['description']) ?></p>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div>
        <h2 class="text-xl font-semibold mb-4">Dernières Idées</h2>
        <div class="bg-white p-4 rounded-lg shadow">
            <ul class="space-y-3">
            <?php if (empty($ideas)): ?>
                <li class="text-gray-500">Aucune idée pour le moment.</li>
            <?php else: ?>
                <?php foreach ($ideas as $idea): ?>
                    <li class="border-b border-gray-200 pb-2 last:border-b-0">
                        <p class="font-semibold text-gray-800"><?= htmlspecialchars($idea['title']) ?></p>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
            </ul>
            <a href="/ideas" class="mt-4 inline-block text-sm font-semibold text-indigo-600 hover:text-indigo-500">Voir toutes les idées &rarr;</a>
        </div>
    </div>
</div>