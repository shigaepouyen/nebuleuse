<?php // /app/views/layout/nav.php ?>
<nav class="bg-gray-800">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="flex h-16 items-center justify-between">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <a href="/" class="text-white font-bold text-lg">🚀 Project Tracker</a>
        </div>
        <div class="hidden md:block">
          <div class="ml-10 flex items-baseline space-x-4">
            <a href="/" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Tableau de bord</a>
            <a href="/ideas" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Idées</a>
            <a href="/maintenance" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Maintenance</a>
            </div>
        </div>
      </div>
      <div class="ml-4 flex items-center md:ml-6">
        <form action="/ideas/quick-add" method="POST" class="flex items-center mr-4">
            <?= CSRF::field() ?>
            <input type="text" name="quick_idea_title" placeholder="Jeter une idée dans la Pensine..." 
                  class="bg-gray-700 text-white rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-48 transition-all duration-300 focus:w-64"
                  autocomplete="off">
            <button type="submit" class="ml-2 text-gray-400 hover:text-white" title="Ajouter l'idée">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-11.25a.75.75 0 0 0-1.5 0v2.5h-2.5a.75.75 0 0 0 0 1.5h2.5v2.5a.75.75 0 0 0 1.5 0v-2.5h2.5a.75.75 0 0 0 0-1.5h-2.5v-2.5Z" clip-rule="evenodd" /></svg>
            </button>
        </form>
        <a href="/logout" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">Déconnexion</a>
      </div>
      <div class="hidden md:block">
        <div class="ml-4 flex items-center md:ml-6">
          <a href="/logout" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">Déconnexion</a>
        </div>
      </div>
    </div>
  </div>
</nav>