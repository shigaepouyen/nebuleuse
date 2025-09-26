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

    /**
     * Gère la création d'une idée depuis le champ de saisie rapide.
     */
    public function quickAdd() {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            die('Erreur de sécurité.');
        }

        $title = trim($_POST['quick_idea_title'] ?? '');

        if (!empty($title)) {
            $ideaModel = new Idea();
            $ideaModel->create($title, ''); // Crée l'idée avec un titre, sans notes
            Flash::set('success', 'Idée "' . htmlspecialchars($title) . '" capturée dans la Pensine !');
        } else {
            Flash::set('error', 'Le champ de l\'idée ne peut pas être vide.');
        }
        
        // Redirige l'utilisateur vers la page où il se trouvait
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
        exit();
    }

    /**
     * Transforme une idée en un tout nouveau projet.
     */
    public function promoteToProject(int $id) {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            die('Erreur de sécurité.');
        }
        
        $ideaModel = new Idea();
        $idea = $ideaModel->findById($id);

        if (!$idea) {
            Flash::set('error', 'L\'idée que vous essayez de promouvoir n\'existe pas.');
            header('Location: /ideas');
            exit();
        }
        
        // Création du projet à partir de l'idée
        $projectModel = new Project();
        $projectId = $projectModel->create($idea['title'], $idea['notes'], ''); // Le lien GitHub est vide par défaut

        if ($projectId) {
            // L'idée a rempli sa mission, on la supprime
            $ideaModel->delete($id);
            Flash::set('success', 'L\'idée a été promue en un nouveau projet !');
            // On redirige directement vers le nouveau projet
            header('Location: /projects/' . $projectId);
            exit();
        } else {
            Flash::set('error', 'Une erreur est survenue lors de la création du projet.');
            header('Location: /ideas');
            exit();
        }
    }
}