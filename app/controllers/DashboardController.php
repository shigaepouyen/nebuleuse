<?php
// /app/controllers/DashboardController.php

class DashboardController {
    public function index() {
        $projectModel = new Project();
        $ideaModel = new Idea();

        $data = [
            'projects' => $projectModel->getActiveProjects(),
            'ideas' => $ideaModel->getRecentIdeas(5)
        ];

        $this->view('dashboard/index', $data);
    }
    
    protected function view($view_path, $data = []) {
        extract($data);
        $content = __DIR__ . '/../views/' . $view_path . '.php';
        require __DIR__ . '/../views/layout/base.php';
    }
}