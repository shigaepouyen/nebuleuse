<?php
// /app/controllers/ProjectController.php

class ProjectController {

    public function show(int $id) {
        $projectModel = new Project();
        $taskModel = new Task();

        $tagModel = new Tag();
        $tags = $tagModel->getForProject($id);

        $project = $projectModel->findById($id);
        if (!$project) {
            http_response_code(404);
            echo "Projet non trouvé.";
            return;
        }

        $columns = $projectModel->getColumns($id);
        $tasks = $taskModel->getTasksForProject($id);
        
        // Organiser les tâches par colonne pour la vue
        $tasksByColumn = [];
        foreach ($tasks as $task) {
            $tasksByColumn[$task['column_id']][] = $task;
        }


        $data = [
            'project' => $project,
            'columns' => $columns,
            'tasksByColumn' => $tasksByColumn,
            'tags' => $tags
        ];

        $this->view('projects/show', $data);
    }
    
    protected function view($view_path, $data = []) {
        extract($data);
        $content = __DIR__ . '/../views/' . $view_path . '.php';
        require __DIR__ . '/../views/layout/base.php';
    }

    // Dans la classe ProjectController du fichier /app/controllers/ProjectController.php

    public function create() {
        $this->view('projects/new');
    }

    public function store() {
        // 1. Sécurité et validation
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            die('Erreur de sécurité CSRF.');
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $github_url = trim($_POST['github_url'] ?? '');

        // --- EN CAS D'ÉCHEC DE VALIDATION ---
        if (!Validator::isTitle($title) || !Validator::isGithubUrl($github_url)) {
            // On définit un message d'erreur avant de rediriger
            Flash::set('error', 'Le titre est invalide (3 caractères min) ou l\'URL GitHub n\'est pas correcte.');
            header('Location: /projects/new');
            exit();
        }
        
        $projectModel = new Project();
        $projectId = $projectModel->create($title, $description, $github_url);

        if ($projectId) {
            // --- GESTION DES TAGS ---
            if (!empty($_POST['tags'])) {
                // 1. On transforme la chaîne de caractères "Pro, Perso" en un tableau propre
                $tagNames = array_map('trim', explode(',', $_POST['tags']));
                $tagNames = array_filter($tagNames); // Supprime les entrées vides

                if (!empty($tagNames)) {
                    // 2. On trouve ou on crée les tags et on récupère leurs IDs
                    $tagModel = new Tag();
                    $tagIds = $tagModel->findOrCreateByName($tagNames);

                    // 3. On synchronise les tags avec le projet
                    $projectModel->syncTags($projectId, $tagIds);
                }
            }
            // --- FIN GESTION DES TAGS ---
            else {
                // --- EN CAS D'ÉCHEC DE CRÉATION ---
                Flash::set('error', 'Une erreur est survenue lors de la création du projet.');
                header('Location: /projects/new');
                exit();
            }
        }
    }
}