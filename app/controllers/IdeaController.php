<?php
// /app/controllers/IdeaController.php

class IdeaController {

    public function index() {
        $ideaModel = new Idea();
        $ideas = $ideaModel->getAll();

        $this->view('ideas/index', ['ideas' => $ideas]);
    }
    
    public function store() {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            die('Erreur de sécurité CSRF.');
        }

        $title = trim($_POST['title'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if (Validator::isTitle($title)) {
            $ideaModel = new Idea();
            $ideaModel->create($title, $notes);
        }
        
        header('Location: /ideas');
        exit();
    }
    
    public function delete(int $id) {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            die('Erreur de sécurité CSRF.');
        }

        $ideaModel = new Idea();
        $ideaModel->delete($id);
        
        header('Location: /ideas');
        exit();
    }

    protected function view($view_path, $data = []) {
        extract($data);
        $content = __DIR__ . '/../views/' . $view_path . '.php';
        require __DIR__ . '/../views/layout/base.php';
    }

    // Dans la classe IdeaController du fichier /app/controllers/IdeaController.php

    public function showConvertToTaskForm(int $id) {
        $ideaModel = new Idea();
        $idea = $ideaModel->findById($id);

        if (!$idea) {
            http_response_code(404);
            die("Idée non trouvée.");
        }

        $projectModel = new Project();
        $projects = $projectModel->getActiveProjects(); // On a besoin de la liste des projets cibles

        $this->view('ideas/convert', ['idea' => $idea, 'projects' => $projects]);
    }

    public function handleConvertToTask(int $id) {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            die('Erreur de sécurité CSRF.');
        }

        $ideaModel = new Idea();
        $idea = $ideaModel->findById($id);
        if (!$idea) {
            die("Idée non trouvée.");
        }

        $projectId = (int)$_POST['project_id'];
        $columnId = (int)$_POST['column_id'];

        if ($projectId > 0 && $columnId > 0) {
            // Créer la tâche à partir de l'idée
            $taskModel = new Task();
            $taskModel->create($projectId, $columnId, $idea['title'], $idea['notes']);

            // Supprimer l'idée une fois convertie
            $ideaModel->delete($id);

            header('Location: /projects/' . $projectId);
            exit();
        }
        
        // En cas d'erreur, revenir au formulaire
        header('Location: /ideas/' . $id . '/convert');
        exit();
    }
}