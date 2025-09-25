<?php // /app/models/Task.php

class Task {
     private $db;

    public function __construct() {
        $this->db = get_db_connection();
    }
    
    public function getTasksForProject(int $projectId): array {
        $stmt = $this->db->prepare("SELECT * FROM task WHERE project_id = ? ORDER BY position");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll();
    }

    // Dans la classe Task du fichier /app/models/Task.php

    public function updatePosition(int $taskId, int $newColumnId, int $newPosition): bool {
        $this->db->beginTransaction();
        try {
            // 1. Mettre à jour la colonne de la tâche déplacée
            $stmt = $this->db->prepare("UPDATE task SET column_id = ?, position = -1 WHERE id = ?");
            $stmt->execute([$newColumnId, $taskId]);

            // 2. Décaler les autres tâches dans la nouvelle colonne
            $stmt = $this->db->prepare(
                "UPDATE task SET position = position + 1 WHERE project_id = (SELECT project_id FROM task WHERE id = ?) AND column_id = ? AND position >= ? AND id != ?"
            );
            $stmt->execute([$taskId, $newColumnId, $newPosition, $taskId]);

            // 3. Placer la tâche à sa nouvelle position
            $stmt = $this->db->prepare("UPDATE task SET position = ? WHERE id = ?");
            $stmt->execute([$newPosition, $taskId]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            // Loggez l'erreur dans un vrai projet
            error_log($e->getMessage());
            return false;
        }
    }

    // Dans la classe Task du fichier /app/models/Task.php

    public function create(int $projectId, int $columnId, string $title, ?string $description): bool {
        // Obtenir la position la plus élevée dans la colonne et ajouter 1
        $posStmt = $this->db->prepare("SELECT MAX(position) as max_pos FROM task WHERE column_id = ?");
        $posStmt->execute([$columnId]);
        $max_pos = $posStmt->fetchColumn();
        $newPosition = ($max_pos === null) ? 0 : $max_pos + 1;

        $stmt = $this->db->prepare(
            "INSERT INTO task (project_id, column_id, title, description, position) VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$projectId, $columnId, $title, $description, $newPosition]);
    }

    // Dans la classe Task du fichier /app/models/Task.php

    public function findByIdWithProject(int $id) {
        $stmt = $this->db->prepare(
            "SELECT t.*, p.title as project_title 
            FROM task t
            JOIN project p ON t.project_id = p.id
            WHERE t.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Dans la classe Task du fichier /app/models/Task.php

    public function getMaintenanceTasks(): array {
        $stmt = $this->db->query(
            "SELECT t.*, p.title as project_title 
            FROM task t 
            JOIN project p ON t.project_id = p.id
            WHERE t.type = 'maintenance' AND t.done_at IS NULL
            ORDER BY t.due_date ASC, t.priority DESC"
        );
        return $stmt->fetchAll();
    }

    /**
     * Marque une tâche comme terminée, met à jour la date et la déplace si possible.
     */
    public function markAsCompleted(int $taskId): bool {
        $this.db->beginTransaction();
        try {
            // Étape 1: Récupérer l'ID du projet pour trouver la bonne colonne "Terminé"
            $stmt = $this->db->prepare("SELECT project_id FROM task WHERE id = ?");
            $stmt->execute([$taskId]);
            $projectId = $stmt->fetchColumn();

            if (!$projectId) {
                // La tâche n'existe pas, on annule tout
                $this->db->rollBack();
                return false;
            }

            // Étape 2: Trouver l'ID de la colonne "Terminé" (ou "Done") pour ce projet
            $colStmt = $this->db->prepare(
                "SELECT id FROM board_column WHERE project_id = ? AND (name = 'Terminé' OR name = 'Done')"
            );
            $colStmt->execute([$projectId]);
            $doneColumnId = $colStmt->fetchColumn();

            // Étape 3: Mettre à jour la tâche
            $updateStmt = null;
            if ($doneColumnId) {
                // Si on a trouvé la colonne, on déplace la tâche
                $updateStmt = $this->db->prepare(
                    "UPDATE task SET done_at = datetime('now'), updated_at = datetime('now'), column_id = ? WHERE id = ?"
                );
                $updateStmt->execute([$doneColumnId, $taskId]);
            } else {
                // Sinon, on met juste à jour la date de complétion
                $updateStmt = $this->db->prepare(
                    "UPDATE task SET done_at = datetime('now'), updated_at = datetime('now') WHERE id = ?"
                );
                $updateStmt->execute([$taskId]);
            }
            
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage()); // Important pour le débogage
            return false;
        }
    }
}

