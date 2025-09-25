<?php
// /app/models/Project.php

class Project {
    private $db;

    public function __construct() {
        $this->db = get_db_connection();
    }

    public function getActiveProjects(): array {
        $stmt = $this->db->query("SELECT * FROM project WHERE status = 'active' ORDER BY title");
        return $stmt->fetchAll();
    }
    
    public function findById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM project WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getColumns(int $projectId): array {
        $stmt = $this->db->prepare("SELECT * FROM board_column WHERE project_id = ? ORDER BY position");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll();
    }

    // Dans la classe Project du fichier /app/models/Project.php

    public function create(string $title, string $description, string $github_url): int|false {
        $this->db->beginTransaction();
        try {
            // Insérer le projet
            $stmt = $this->db->prepare(
                "INSERT INTO project (title, description, github_url) VALUES (?, ?, ?)"
            );
            $stmt->execute([$title, $description, $github_url]);
            $projectId = $this->db->lastInsertId();

            // Insérer les colonnes par défaut
            $defaultColumns = ['Idées', 'À faire', 'En cours', 'En revue', 'Terminé'];
            $colStmt = $this->db->prepare(
                "INSERT INTO board_column (project_id, name, position) VALUES (?, ?, ?)"
            );
            foreach ($defaultColumns as $index => $name) {
                $colStmt->execute([$projectId, $name, $index]);
            }

            $this->db->commit();
            return (int)$projectId;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage()); // Log l'erreur pour le débogage
            return false;
        }
    }
}