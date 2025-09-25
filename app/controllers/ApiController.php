<?php
// /app/controllers/ApiController.php

class ApiController {
    public function getProjectColumns(int $projectId) {
        header('Content-Type: application/json');
        
        $projectModel = new Project();
        $columns = $projectModel->getColumns($projectId);
        
        echo json_encode($columns);
        exit();
    }
}