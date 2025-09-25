<?php
// /app/scripts/migrate.php

require_once __DIR__ . '/../config/db.php';

echo "<pre>";
echo "Script de migration démarré...\n";

try {
    $db = get_db_connection();
    $db->beginTransaction();

    echo "Activation des clés étrangères et du mode WAL...\n";
    $db->exec("PRAGMA foreign_keys = ON;");
    $db->exec("PRAGMA journal_mode = WAL;");

    $schema = [
        "CREATE TABLE IF NOT EXISTS user (
            id INTEGER PRIMARY KEY CHECK (id = 1),
            display_name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            timezone TEXT DEFAULT 'Europe/Paris',
            created_at TEXT NOT NULL DEFAULT (datetime('now'))
        );",
        "CREATE TABLE IF NOT EXISTS project (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT,
            status TEXT NOT NULL DEFAULT 'active', -- idea|active|paused|done|archived
            github_url TEXT,
            created_at TEXT NOT NULL DEFAULT (datetime('now')),
            updated_at TEXT
        );",
        "CREATE TABLE IF NOT EXISTS board_column (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            project_id INTEGER NOT NULL REFERENCES project(id) ON DELETE CASCADE,
            name TEXT NOT NULL,
            position INTEGER NOT NULL
        );",
        "CREATE TABLE IF NOT EXISTS task (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            project_id INTEGER NOT NULL REFERENCES project(id) ON DELETE CASCADE,
            column_id INTEGER REFERENCES board_column(id) ON DELETE SET NULL,
            title TEXT NOT NULL,
            description TEXT,
            priority TEXT DEFAULT 'medium', -- low|medium|high
            type TEXT DEFAULT 'feature', -- feature|bug|maintenance|other
            due_date TEXT,
            position INTEGER NOT NULL DEFAULT 0,
            done_at TEXT,
            created_at TEXT NOT NULL DEFAULT (datetime('now')),
            updated_at TEXT
        );",
         "CREATE TABLE IF NOT EXISTS idea (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            notes TEXT,
            priority INTEGER DEFAULT 2, -- 1..5
            created_at TEXT NOT NULL DEFAULT (datetime('now'))
        );",
        // Indexes pour la performance
        "CREATE INDEX IF NOT EXISTS idx_task_project ON task(project_id);",
        "CREATE INDEX IF NOT EXISTS idx_task_column ON task(column_id, position);",
        "CREATE INDEX IF NOT EXISTS idx_task_due ON task(due_date);"
    ];

    foreach ($schema as $query) {
        $db->exec($query);
        echo "Exécuté : " . substr($query, 0, 60) . "...\n";
    }
    
    // Seed (Jeu d'essai)
    echo "\nInsertion des données de démo (si nécessaire)...\n";

    // Vérifier si l'utilisateur existe déjà
    $stmt = $db->query("SELECT COUNT(*) FROM user");
    if ($stmt->fetchColumn() == 0) {
        $password = 'admin'; // Mot de passe par défaut
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $db->exec("INSERT INTO user (id, display_name, email, password_hash) VALUES (1, 'Admin', 'admin@example.com', '$hashed_password');");
        echo "Utilisateur 'Admin' créé avec le mot de passe '$password'. Changez-le rapidement !\n";
    }

    // Projet de démo
    $stmt = $db->query("SELECT COUNT(*) FROM project");
    if ($stmt->fetchColumn() == 0) {
        $db->exec("INSERT INTO project (id, title, description, status, github_url) VALUES (1, 'Projet de Démo', 'Ceci est un projet pour démontrer les fonctionnalités.', 'active', 'https://github.com/user/repo');");
        $project_id = $db->lastInsertId();
        echo "Projet de démo créé.\n";
        
        // Colonnes Kanban par défaut
        $columns = ['Idées', 'À faire', 'En cours', 'Terminé'];
        foreach ($columns as $index => $name) {
            $stmt = $db->prepare("INSERT INTO board_column (project_id, name, position) VALUES (?, ?, ?)");
            $stmt->execute([$project_id, $name, $index]);
        }
        $idea_col_id = 1; $todo_col_id = 2; $doing_col_id = 3; $done_col_id = 4;
        echo "Colonnes Kanban créées.\n";

        // Tâches de démo
        $db->exec("INSERT INTO task (project_id, column_id, title, description, priority, type, due_date) VALUES 
            ($project_id, $todo_col_id, 'Configurer le projet', 'Mettre en place la base de code et les dépendances.', 'high', 'feature', date('now', '+3 days')),
            ($project_id, $todo_col_id, 'Créer la page de connexion', 'Avec validation et sécurité.', 'high', 'feature', date('now', '+5 days')),
            ($project_id, $doing_col_id, 'Développer le tableau de bord', 'Afficher les projets et les idées.', 'medium', 'feature', NULL),
            ($project_id, $idea_col_id, 'Ajouter une vue calendrier', 'Visualiser les tâches avec échéance.', 'low', 'feature', NULL),
            ($project_id, $todo_col_id, 'Planifier la MAJ des dépendances', 'Vérifier les MAJ de sécurité pour Q4 2025.', 'medium', 'maintenance', date('now', '+30 days'));");
        echo "Tâches de démo créées.\n";

        // Idée de démo
        $db->exec("INSERT INTO idea (title, notes, priority) VALUES ('Intégrer une API météo', 'Afficher la météo sur le dashboard pourrait être un gadget sympa.', 3);");
        echo "Idée de démo créée.\n";
    }

    $db->commit();
    echo "\nMigration terminée avec succès !\n";

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    die("ERREUR : " . $e->getMessage());
}
echo "</pre>";