<?php
// /app/controllers/ProjectController.php

class ProjectController {

    public function show(int $id) {
        $projectModel = new Project();
        $taskModel = new Task();

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
            'tasksByColumn' => $tasksByColumn
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

        if (!Validator::isTitle($title) || !Validator::isGithubUrl($github_url)) {
            // Rediriger avec un message d'erreur (à implémenter avec des sessions flash)
            header('Location: /projects/new');
            exit();
        }
        
        // 2. Création du projet et de ses colonnes
        $projectModel = new Project();
        $projectId = $projectModel->create($title, $description, $github_url);

        if ($projectId) {
            // Rediriger vers la page du nouveau projet
            header('Location: /projects/' . $projectId);
        } else {
            // Gérer l'erreur
            die('Impossible de créer le projet.');
        }
    }
}