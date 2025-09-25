<?php
// /app/controllers/MaintenanceController.php

class MaintenanceController {
    public function index() {
        $taskModel = new Task();
        $tasks = $taskModel->getMaintenanceTasks();
        $this->view('maintenance/index', ['tasks' => $tasks]);
    }
    
    protected function view($view_path, $data = []) {
        extract($data);
        $content = __DIR__ . '/../views/' . $view_path . '.php';
        require __DIR__ . '/../views/layout/base.php';
    }
}