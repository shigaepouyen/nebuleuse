<?php
// /app/controllers/ProjectController.php

class ProjectController {

    public function show(int $id) {
        $taskModel = new Task();
        $task = $taskModel->findByIdWithProject($id);

        if (!$task) {
            http_response_code(404);
            die("Tâche non trouvée.");
        }
        
        $task['description_html'] = nl2br(htmlspecialchars($task['description']));
        
        $checklistModel = new ChecklistItem();
        $checklist = $checklistModel->getForTask($id);
        
        $data = [
            'task' => $task,
            'checklist' => $checklist,
            // les commentaires viendront ici
        ];
        
        $this->view('tasks/show', $data);
    }
    
    public function new(int $projectId) {
        $projectModel = new Project();
        $project = $projectModel->findById($projectId);
        $columns = $projectModel->getColumns($projectId);

        if (!$project) {
            http_response_code(404);
            die("Projet non trouvé.");
        }

        $this->view('tasks/new', ['project' => $project, 'columns' => $columns]);
    }

    public function store(int $projectId) {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            die('Erreur de sécurité CSRF.');
        }

        $title = trim($_POST['title'] ?? '');
        $column_id = (int)$_POST['column_id'];
        // Récupérer et valider les autres champs...
        
        if (Validator::isTitle($title)) {
            $taskModel = new Task();
            $taskModel->create($projectId, $column_id, $title, $_POST['description'] ?? null);
        }
        
        header('Location: /projects/' . $projectId);
        exit();
    }
    // La méthode view() doit être ajoutée si elle n'est pas dans un contrôleur de base
    protected function view($view_path, $data = []) {
        extract($data);
        $content = __DIR__ . '/../views/' . $view_path . '.php';
        require __DIR__ . '/../views/layout/base.php';
    }

    public function addChecklistItem(int $taskId) {
        // ... validation CSRF et des données ...
        $label = trim($_POST['label'] ?? '');
        if (!empty($label)) {
            $model = new ChecklistItem();
            $model->create($taskId, $label);
        }
        header('Location: /tasks/' . $taskId);
        exit();
    }

    public function toggleChecklistItem(int $taskId, int $itemId) {
        // ... validation CSRF ...
        $model = new ChecklistItem();
        $checked = isset($_POST['checked']);
        $model->toggle($itemId, $checked);
        header('Location: /tasks/' . $taskId);
        exit();
    }

    public function complete(int $taskId) {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            die('Erreur de sécurité CSRF.');
        }

        $taskModel = new Task();
        $task = $taskModel->findByIdWithProject($taskId); // On récupère la tâche pour connaître son projet

        if ($task) {
            $taskModel->markAsCompleted($taskId);
            // On redirige vers le projet pour voir le résultat directement sur le Kanban
            header('Location: /projects/' . $task['project_id']);
            exit();
        }

        // Si la tâche n'existe pas, on redirige vers le tableau de bord
        header('Location: /');
        exit();
    }
}