<header class="mb-8">
    <h1 class="text-3xl font-bold tracking-tight text-gray-900">Boîte à Idées</h1>
    <p class="text-gray-600 mt-1">Capturez tout ce qui vous passe par la tête avant de l'oublier.</p>
</header>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <div class="md:col-span-1">
        <div class="bg-white p-6 rounded-lg shadow sticky top-8">
            <h2 class="text-lg font-semibold mb-4">Nouvelle Idée</h2>
            <form action="/ideas" method="POST" class="space-y-4">
                <?= CSRF::field() ?>
                <div>
                    <label for="title" class="sr-only">Titre</label>
                    <input type="text" name="title" id="title" required placeholder="Titre de l'idée..." class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                </div>
                <div>
                    <label for="notes" class="sr-only">Notes</label>
                    <textarea name="notes" id="notes" rows="4" placeholder="Quelques notes, un lien, un détail..." class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600"></textarea>
                </div>
                <button type="submit" class="w-full rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Ajouter l'idée</button>
            </form>
        </div>
    </div>

    <div class="md:col-span-2 space-y-4">
        <?php if (empty($ideas)): ?>
            <div class="text-center bg-white p-8 rounded-lg shadow">
                <p class="text-gray-500">Votre boîte à idées est vide. Lancez-vous !</p>
            </div>
        <?php else: ?>
            <?php foreach ($ideas as $idea): ?>
                <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                    <form action="/ideas/<?= $idea['id'] ?>/promote" method="POST" onsubmit="return confirm('Voulez-vous vraiment promouvoir cette idée en un nouveau projet ?');">
                        <?= CSRF::field() ?>
                        <button type="submit" class="text-gray-400 hover:text-blue-500" title="Promouvoir en nouveau projet">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path d="M10.75 2.75a.75.75 0 0 0-1.5 0v8.614L6.295 8.235a.75.75 0 1 0-1.09 1.03l4.25 4.5a.75.75 0 0 0 1.09 0l4.25-4.5a.75.75 0 0 0-1.09-1.03l-2.955 3.129V2.75Z" /><path d="M3.5 12.75a.75.75 0 0 0-1.5 0v2.5A2.75 2.75 0 0 0 4.75 18h10.5A2.75 2.75 0 0 0 18 15.25v-2.5a.75.75 0 0 0-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5Z" /></svg>
                        </button>
                    </form>
                    
                    <a href="/ideas/<?= $idea['id'] ?>/convert" class="text-gray-400 hover:text-indigo-600" title="Ajouter à un projet existant">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path d="M3.502 3.502a.75.75 0 0 1 1.06 0l2.25 2.25a.75.75 0 0 1 0 1.06l-2.25 2.25a.75.75 0 0 1-1.06-1.06L4.94 6.5 3.502 5.06a.75.75 0 0 1 0-1.06Zm4.242 0a.75.75 0 0 1 1.06 0l2.25 2.25a.75.75 0 0 1 0 1.06l-2.25 2.25a.75.75 0 0 1-1.06-1.06L8.94 6.5 7.744 5.06a.75.75 0 0 1 0-1.06Zm4.243 0a.75.75 0 0 1 1.06 0l2.25 2.25a.75.75 0 0 1 0 1.06l-2.25 2.25a.75.75 0 0 1-1.06-1.06L12.94 6.5l-1.438-1.438a.75.75 0 0 1 0-1.06ZM3.5 12.5a.75.75 0 0 1 .75-.75h11.5a.75.75 0 0 1 0 1.5H4.25a.75.75 0 0 1-.75-.75Zm0 3a.75.75 0 0 1 .75-.75h11.5a.75.75 0 0 1 0 1.5H4.25a.75.75 0 0 1-.75-.75Z" /></svg>
                    </a>
                    
                    <form action="/ideas/<?= $idea['id'] ?>/delete" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cette idée ?');">
                        <?= CSRF::field() ?>
                        <button type="submit" class="text-gray-400 hover:text-red-500" title="Supprimer">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-1.1.26-1.996 1.253-1.996 2.557v.3c0 .288.232.52.52.52h10.953c.288 0 .52-.232.52-.52v-.3c0-1.304-.896-2.297-1.997-2.557v-.443A2.75 2.75 0 0 0 11.25 1h-2.5ZM7.5 3.75c0-.69.56-1.25 1.25-1.25h2.5c.69 0 1.25.56 1.25 1.25v.443c-.47.11-.894.288-1.25.508V4.25a.75.75 0 0 0-1.5 0v.451c-.356-.22-.78-.398-1.25-.508v-.443ZM4.52 8.25h10.953v5.228a2.75 2.75 0 0 1-2.75 2.75h-5.453a2.75 2.75 0 0 1-2.75-2.75V8.25Z" clip-rule="evenodd" /></svg>
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>