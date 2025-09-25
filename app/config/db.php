<?php
// /app/config/db.php

function get_db_connection(): PDO {
    static $db = null;
    if ($db === null) {
        $db_path = __DIR__ . '/../data/app.sqlite';
        try {
            $db = new PDO('sqlite:' . $db_path);
            // Options pour la performance et la robustesse
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $db->exec("PRAGMA foreign_keys = ON;");
            $db->exec("PRAGMA journal_mode = WAL;");
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }
    return $db;
}