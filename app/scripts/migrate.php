<?php
// /app/scripts/migrate.php

require_once __DIR__ . '/../config/db.php';

// Pour un affichage propre dans le navigateur
header('Content-Type: text/plain; charset=utf-8');
echo "--- Script de Migration et d'Initialisation ---\n\n";

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
            status TEXT NOT NULL DEFAULT 'active',
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
            priority TEXT DEFAULT 'medium',
            type TEXT DEFAULT 'feature',
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
            priority INTEGER DEFAULT 2,
            created_at TEXT NOT NULL DEFAULT (datetime('now'))
        );",
        "CREATE TABLE IF NOT EXISTS checklist_item (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            task_id INTEGER NOT NULL REFERENCES task(id) ON DELETE CASCADE,
            label TEXT NOT NULL,
            checked INTEGER NOT NULL DEFAULT 0 CHECK(checked IN (0, 1)),
            position INTEGER NOT NULL DEFAULT 0
        );",
        "CREATE TABLE IF NOT EXISTS comment (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            task_id INTEGER NOT NULL REFERENCES task(id) ON DELETE CASCADE,
            body TEXT NOT NULL,
            created_at TEXT NOT NULL DEFAULT (datetime('now'))
        );",
        // --- TABLES POUR LA GESTION DES TAGS ---
        "CREATE TABLE IF NOT EXISTS tag (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            color TEXT DEFAULT '#64748b' -- slate-500
        );",

        "CREATE TABLE IF NOT EXISTS project_tag (
            project_id INTEGER NOT NULL REFERENCES project(id) ON DELETE CASCADE,
            tag_id INTEGER NOT NULL REFERENCES tag(id) ON DELETE CASCADE,
            PRIMARY KEY (project_id, tag_id)
        );",

        "CREATE TABLE IF NOT EXISTS task_tag (
            task_id INTEGER NOT NULL REFERENCES task(id) ON DELETE CASCADE,
            tag_id INTEGER NOT NULL REFERENCES tag(id) ON DELETE CASCADE,
            PRIMARY KEY (task_id, tag_id)
        );",
        "CREATE INDEX IF NOT EXISTS idx_task_project ON task(project_id);",
        "CREATE INDEX IF NOT EXISTS idx_task_column_position ON task(column_id, position);",
        "CREATE INDEX IF NOT EXISTS idx_task_due_date ON task(due_date);",
        "CREATE INDEX IF NOT EXISTS idx_checklist_item_task ON checklist_item(task_id);",
        "CREATE INDEX IF NOT EXISTS idx_comment_task ON comment(task_id);"
    ];

    echo "\nCréation/Mise à jour du schéma de la base de données...\n";
    foreach ($schema as $query) {
        $db->exec($query);
    }
    echo "Schéma OK.\n";
    

    // --- Seed (Jeu d'essai) ---
    echo "\nInsertion des données de démo (uniquement si la base est vide)...\n";

    // Utilisateur Admin
    $stmt = $db->query("SELECT COUNT(*) FROM user");
    if ($stmt->fetchColumn() == 0) {
        $password = 'admin';
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $db->exec("INSERT INTO user (id, display_name, email, password_hash) VALUES (1, 'Admin', 'admin@example.com', '$hashed_password');");
        echo "- Utilisateur 'Admin' créé (mot de passe : $password).\n";
    }

    // Projet de démo
    $stmt = $db->query("SELECT COUNT(*) FROM project");
    if ($stmt->fetchColumn() == 0) {
        $db->exec("INSERT INTO project (title, description, status, github_url) VALUES ('Projet de Démo', 'Ceci est un projet pour démontrer les fonctionnalités.', 'active', 'https://github.com/user/repo');");
        $project_id = $db->lastInsertId();
        echo "- Projet de démo créé.\n";
        
        // Création des colonnes et récupération de leurs IDs réels
        $columns = ['Idées', 'À faire', 'En cours', 'Terminé'];
        $column_ids = [];
        $stmt = $db->prepare("INSERT INTO board_column (project_id, name, position) VALUES (?, ?, ?)");
        
        foreach ($columns as $index => $name) {
            $stmt->execute([$project_id, $name, $index]);
            $column_ids[$name] = $db->lastInsertId();
        }
        echo "- Colonnes Kanban créées avec des IDs dynamiques.\n";

        // Création des tâches de démo avec les bons IDs de colonnes
        $db->exec("INSERT INTO task (project_id, column_id, title, description, priority, type, due_date) VALUES 
            ($project_id, {$column_ids['À faire']}, 'Configurer le projet', 'Mettre en place la base de code et les dépendances.', 'high', 'feature', date('now', '+3 days')),
            ($project_id, {$column_ids['À faire']}, 'Créer la page de connexion', 'Avec validation et sécurité.', 'high', 'feature', date('now', '+5 days')),
            ($project_id, {$column_ids['En cours']}, 'Développer le tableau de bord', 'Afficher les projets et les idées.', 'medium', 'feature', NULL),
            ($project_id, {$column_ids['Idées']}, 'Ajouter une vue calendrier', 'Visualiser les tâches avec échéance.', 'low', 'feature', NULL),
            ($project_id, {$column_ids['À faire']}, 'Planifier la MAJ des dépendances', 'Vérifier les MAJ de sécurité pour le prochain trimestre.', 'medium', 'maintenance', date('now', '+30 days'));");
        echo "- Tâches de démo créées et associées aux bonnes colonnes.\n";

        // Idée de démo
        $db->exec("INSERT INTO idea (title, notes, priority) VALUES ('Intégrer une API météo', 'Afficher la météo sur le dashboard pourrait être un gadget sympa.', 3);");
        echo "- Idée de démo créée.\n";
    } else {
        echo "La base de données contient déjà des données, le remplissage de démo est ignoré.\n";
    }

    $db->commit();
    echo "\n--- ✅ Migration terminée avec succès ! ---\n";

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    die("\n--- ❌ ERREUR LORS DE LA MIGRATION ---\n\n" . $e->getMessage());
}