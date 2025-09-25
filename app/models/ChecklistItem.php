<?php
// /app/models/ChecklistItem.php

class ChecklistItem {
    private $db;

    public function __construct() {
        $this->db = get_db_connection();
    }
    
    public function getForTask(int $taskId): array {
        $stmt = $this->db->prepare("SELECT * FROM checklist_item WHERE task_id = ? ORDER BY position");
        $stmt->execute([$taskId]);
        return $stmt->fetchAll();
    }
    
    public function create(int $taskId, string $label): bool {
        $stmt = $this->db->prepare("INSERT INTO checklist_item (task_id, label) VALUES (?, ?)");
        return $stmt->execute([$taskId, $label]);
    }
    
    public function toggle(int $itemId, bool $checked): bool {
        $stmt = $this->db->prepare("UPDATE checklist_item SET checked = ? WHERE id = ?");
        return $stmt->execute([$checked ? 1 : 0, $itemId]);
    }

    public function delete(int $itemId): bool {
        $stmt = $this->db->prepare("DELETE FROM checklist_item WHERE id = ?");
        return $stmt->execute([$itemId]);
    }
}