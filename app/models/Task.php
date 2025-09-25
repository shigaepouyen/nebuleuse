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
     * Marque une tâche comme terminée de manière robuste.
     * La syntaxe $this->db est corrigée.
     * L'opération est transactionnelle pour garantir l'intégrité des données.
     * La position dans l'ancienne colonne est comblée pour éviter les "trous".
     */
    public function markAsCompleted(int $taskId): bool {
        // L'opération entière est dans une transaction pour s'assurer que tout réussit ou tout échoue.
        $this->db->beginTransaction();
        try {
            // Étape 1 : Récupérer le contexte complet de la tâche (projet, colonne, position)
            $stmt = $this->db->prepare("SELECT project_id, column_id, position FROM task WHERE id = ?");
            $stmt->execute([$taskId]);
            $taskContext = $stmt->fetch();

            if (!$taskContext) {
                $this->db->rollBack();
                return false; // La tâche n'existe pas.
            }

            // Étape 2 : Combler le "trou" laissé par la tâche dans sa colonne d'origine.
            // On décrémente la position de toutes les tâches qui se trouvaient après elle.
            $stmt = $this->db->prepare(
                "UPDATE task SET position = position - 1 WHERE column_id = ? AND position > ?"
            );
            $stmt->execute([$taskContext['column_id'], $taskContext['position']]);

            // Étape 3 : Trouver la colonne "Terminé" (ou "Done") du projet.
            $colStmt = $this->db->prepare(
                "SELECT id FROM board_column WHERE project_id = ? AND (name = 'Terminé' OR name = 'Done') LIMIT 1"
            );
            $colStmt->execute([$taskContext['project_id']]);
            $doneColumnId = $colStmt->fetchColumn();

            $finalColumnId = $taskContext['column_id']; // Par défaut, la tâche reste dans sa colonne si "Terminé" n'existe pas.
            $newPosition = -1; // Position invalide si elle n'est pas déplacée.

            if ($doneColumnId) {
                $finalColumnId = $doneColumnId;
                // Étape 4 : Calculer la nouvelle position (à la fin de la colonne "Terminé").
                $posStmt = $this->db->prepare("SELECT MAX(position) FROM task WHERE column_id = ?");
                $posStmt->execute([$doneColumnId]);
                $maxPos = $posStmt->fetchColumn();
                $newPosition = ($maxPos === null) ? 0 : $maxPos + 1;
            }

            // Étape 5 : Mettre à jour la tâche pour la marquer comme terminée et la déplacer.
            $updateStmt = $this->db->prepare(
                "UPDATE task SET done_at = datetime('now'), updated_at = datetime('now'), column_id = ?, position = ? WHERE id = ?"
            );
            $updateStmt->execute([$finalColumnId, $newPosition, $taskId]);

            // Si toutes les opérations ont réussi, on valide la transaction.
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            // En cas d'erreur à n'importe quelle étape, on annule tout.
            $this->db->rollBack();
            error_log("Erreur lors de la complétion de la tâche : " . $e->getMessage());
            return false;
        }
    }
}

