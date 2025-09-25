<?php
// /app/controllers/TaskController.php

class TaskController {

    /**
     * Affiche le formulaire de création d'une nouvelle tâche pour un projet.
     */
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

    /**
     * Traite la soumission du formulaire de création de tâche.
     */
    public function store(int $projectId) {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            die('Erreur de sécurité CSRF.');
        }

        $title = trim($_POST['title'] ?? '');
        $column_id = (int)($_POST['column_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        
        if (Validator::isTitle($title) && $column_id > 0) {
            $taskModel = new Task();
            $taskModel->create($projectId, $column_id, $title, $description);
        }
        
        header('Location: /projects/' . $projectId);
        exit();
    }

    /**
     * Affiche la vue détaillée d'une tâche unique avec sa checklist et ses commentaires.
     */
    public function show(int $id) {
        $taskModel = new Task();
        $task = $taskModel->findByIdWithProject($id);

        if (!$task) {
            http_response_code(404);
            die("Tâche non trouvée.");
        }
        
        // Pour le Markdown (nécessite une librairie comme Parsedown)
        // Pour l'instant, on se contente d'une conversion simple et sécurisée.
        $task['description_html'] = nl2br(htmlspecialchars($task['description']));
        
        $checklistModel = new ChecklistItem();
        $checklist = $checklistModel->getForTask($id);
        
        // Note : la partie "Commentaires" n'est pas encore implémentée dans ce contrôleur.
        // Il faudrait ajouter ici le chargement depuis un CommentModel.
        
        $data = [
            'task' => $task,
            'checklist' => $checklist
        ];
        
        $this->view('tasks/show', $data);
    }

    /**
     * Marque une tâche comme terminée.
     */
    public function complete(int $taskId) {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            die('Erreur de sécurité CSRF.');
        }

        $taskModel = new Task();
        $task = $taskModel->findByIdWithProject($taskId);

        if ($task) {
            $taskModel->markAsCompleted($taskId);
            header('Location: /projects/' . $task['project_id']);
            exit();
        }

        header('Location: /');
        exit();
    }

    /**
     * Gère la requête HTMX pour le déplacement (drag & drop) des tâches.
     */
    public function move() {
        header('Content-Type: application/json');

        parse_str(file_get_contents('php://input'), $input);

        if (!CSRF::validateToken($input['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Jeton CSRF invalide']);
            return;
        }

        $taskId = $input['item'] ?? null;
        $newColumnId = $input['to'] ?? null;
        $newIndex = $input['newIndex'] ?? null;

        if ($taskId === null || $newColumnId === null || $newIndex === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Paramètres manquants.']);
            return;
        }

        $taskModel = new Task();
        $success = $taskModel->updatePosition((int)$taskId, (int)$newColumnId, (int)$newIndex);

        if ($success) {
            http_response_code(200);
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Échec de la mise à jour.']);
        }
    }

    // --- Méthodes pour la Checklist ---

    public function addChecklistItem(int $taskId) {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            die('Erreur de sécurité CSRF.');
        }

        $label = trim($_POST['label'] ?? '');
        if (!empty($label)) {
            $model = new ChecklistItem();
            $model->create($taskId, $label);
        }
        header('Location: /tasks/' . $taskId);
        exit();
    }

    public function toggleChecklistItem(int $taskId, int $itemId) {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            die('Erreur de sécurité CSRF.');
        }
        
        $model = new ChecklistItem();
        $checked = isset($_POST['checked']);
        $model->toggle($itemId, $checked);

        // On redirige avec un header au lieu d'afficher une vue
        header('Location: /tasks/' . $taskId);
        exit();
    }
    
    /**
     * Helper protégé pour charger les vues et leur layout.
     */
    protected function view($view_path, $data = []) {
        extract($data);
        $content = __DIR__ . '/../views/' . $view_path . '.php';
        require __DIR__ . '/../views/layout/base.php';
    }
}