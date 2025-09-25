<?php
// /app/models/Tag.php

class Tag {
    private $db;

    public function __construct() {
        $this->db = get_db_connection();
    }

    public function getForProject(int $projectId): array {
        $stmt = $this->db->prepare(
            "SELECT t.id, t.name, t.color FROM tag t
             JOIN project_tag pt ON t.id = pt.tag_id
             WHERE pt.project_id = ?"
        );
        $stmt->execute([$projectId]);
        return $stmt->fetchAll();
    }

    /**
     * Trouve des tags par leur nom, ou les crée s'ils n'existent pas.
     * Retourne un tableau de leurs IDs.
     */
    public function findOrCreateByName(array $tagNames): array {
        $tagIds = [];
        $stmt_find = $this->db->prepare("SELECT id FROM tag WHERE name = ?");
        $stmt_create = $this->db->prepare("INSERT INTO tag (name) VALUES (?)");

        foreach ($tagNames as $name) {
            $stmt_find->execute([$name]);
            $id = $stmt_find->fetchColumn();
            if ($id) {
                $tagIds[] = $id;
            } else {
                $stmt_create->execute([$name]);
                $tagIds[] = $this->db->lastInsertId();
            }
        }
        return $tagIds;
    }
}