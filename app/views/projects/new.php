<header class="mb-8">
    <h1 class="text-3xl font-bold tracking-tight text-gray-900">Nouveau Projet</h1>
</header>

<div class="bg-white p-6 rounded-lg shadow max-w-2xl mx-auto">
    <form action="/projects" method="POST" class="space-y-6">
        <?= CSRF::field() ?>
        
        <div>
            <label for="title" class="block text-sm font-medium leading-6 text-gray-900">Titre du projet <span class="text-red-500">*</span></label>
            <div class="mt-2">
                <input type="text" name="title" id="title" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
            </div>
        </div>

        <div>
            <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Description courte</label>
            <div class="mt-2">
                <textarea name="description" id="description" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600"></textarea>
            </div>
        </div>

        <div>
            <label for="github_url" class="block text-sm font-medium leading-6 text-gray-900">Lien GitHub (optionnel)</label>
            <div class="mt-2">
                <input type="url" name="github_url" id="github_url" placeholder="https://github.com/user/repo" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
            </div>
        </div>

        <div>
            <label for="tags" class="block text-sm font-medium leading-6 text-gray-900">Tags (séparés par des virgules)</label>
            <div class="mt-2">
                <input type="text" name="tags" id="tags" placeholder="Pro, Perso, Facturable..." class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
            </div>
        </div>
        
        <div class="flex justify-end gap-4">
            <a href="/" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Annuler</a>
            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Créer le projet</button>
        </div>
    </form>
</div>