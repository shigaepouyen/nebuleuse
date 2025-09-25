<?php
// /app/models/Idea.php

class Idea {
    private $db;

    public function __construct() {
        $this->db = get_db_connection();
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM idea ORDER BY priority DESC, created_at DESC");
        return $stmt->fetchAll();
    }
    
    public function getRecentIdeas(int $limit = 5): array {
        $stmt = $this->db->prepare("SELECT * FROM idea ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    public function create(string $title, string $notes): bool {
        $stmt = $this->db->prepare("INSERT INTO idea (title, notes) VALUES (?, ?)");
        return $stmt->execute([$title, $notes]);
    }
    
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM idea WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Dans la classe Idea du fichier /app/models/Idea.php

    public function findById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM idea WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}