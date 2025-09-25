# 🚀 Nebuleuse

Une webapp minimaliste et auto-hébergée pour le suivi de projets personnels, de l'idéation à la maintenance. Léger, rapide et conçu pour les hébergements mutualisés (PHP + SQLite).



---
## ✨ Fonctionnalités Principales

* **Gestion de Projets :** Créez des projets et suivez leur avancement sur un tableau Kanban interactif (glisser-déposer).
* **Suivi des Tâches :** Chaque tâche dispose d'une page détaillée avec description, priorité, statut et une checklist de sous-tâches.
* **Boîte à Idées :** Un espace pour noter rapidement des idées avant de les oublier, avec la possibilité de les convertir en tâches concrètes dans un projet.
* **Vue Maintenance :** Un tableau de bord dédié pour visualiser toutes les tâches de maintenance (mises à jour de dépendances, corrections de bugs) triées par échéance.
* **Sécurisé :** Authentification mono-utilisateur, protection contre les attaques CSRF sur toutes les actions.
* **Responsive :** L'interface est conçue pour être aussi efficace sur mobile que sur ordinateur.
* **Léger et Sans Dépendances :** Écrit en PHP natif (8.1+) avec une base de données SQLite, il ne nécessite aucune installation complexe (pas de Composer, Node.js, Docker, etc.).

---
## ⚙️ Pile Technique

* **Langage :** PHP 8.1+
* **Base de données :** SQLite (via PDO)
* **Serveur Web :** Apache avec `mod_rewrite` activé
* **Frontend :**
    * HTML5 / CSS3
    * **Tailwind CSS** (utilisé via CDN pour la simplicité)
    * **HTMX** pour l'interactivité du Kanban (glisser-déposer) sans rechargement de page.

---
## 📦 Installation (sur hébergement mutualisé type Infomaniak)

L'installation est conçue pour être la plus simple possible.

1.  **Transfert des Fichiers**
    * Uploadez l'ensemble des fichiers du projet sur votre serveur via FTP ou un gestionnaire de fichiers.

2.  **Configuration du Répertoire Cible (Très Important)**
    * Dans l'interface de gestion de votre hébergement (ex: Manager Infomaniak), configurez la **racine de votre site** (aussi appelée "Répertoire cible" ou "Document Root") pour qu'elle pointe vers le dossier `/public`.
    * Par exemple, si vous avez uploadé le projet dans un dossier `monsite`, le chemin à indiquer sera `/monsite/public`.
    * *Cette étape est cruciale pour la sécurité de l'application.*

3.  **Création de la Base de Données**
    * Assurez-vous que le dossier `/app/data` a les droits d'écriture pour le serveur (généralement `755`).
    * Visitez l'URL `https://votresite.com/migrate` dans votre navigateur. Le script va automatiquement créer le fichier de base de données SQLite, toutes les tables, et insérer les données de démonstration (utilisateur, projet, etc.).

4.  **Sécurisation**
    * Une fois l'étape 3 réussie, **il est impératif de désactiver la route de migration**. Ouvrez le fichier `public/index.php`, trouvez la ligne `$router->add('GET', '/migrate', ...)` et commentez-la ou supprimez-la.

---
## 🔑 Première Utilisation

L'installateur a créé un utilisateur par défaut pour vous permettre de vous connecter immédiatement.

* **URL de connexion :** `https://votresite.com/login`
* **Identifiant (email) :** `admin@example.com`
* **Mot de passe :** `admin`

Il est fortement recommandé de changer ce mot de passe dès que la fonctionnalité de gestion de profil sera implémentée.

---
## 📁 Structure du Projet

Le projet suit une architecture de type MVC (Modèle-Vue-Contrôleur) légère et simplifiée.

* `/app`: Contient tout le cœur de l'application (non accessible publiquement).
    * `/controllers`: Gèrent la logique des requêtes.
    * `/models`: Interagissent avec la base de données.
    * `/views`: Contiennent le code HTML et la présentation.
    * `/lib`: Regroupe les classes utilitaires (Routeur, Auth, etc.).
    * `/data`: Stocke le fichier de la base de données `app.sqlite`.
* `/public`: Seul dossier accessible depuis le web. Il contient le point d'entrée unique `index.php` et les assets (CSS, JS, images).

---
## 🗺️ Roadmap (Évolutions possibles)

Cette application est une base solide. Voici quelques fonctionnalités prévues ou possibles pour l'avenir :

* [ ] Gestion complète des **Tags** (création, assignation, filtrage).
* [ ] Ajout de **Commentaires** sur les tâches.
* [ ] Vues alternatives pour les projets (**Liste** et **Calendrier**).
* [ ] Page de **Paramètres Utilisateur** (changement de mot de passe, thème clair/sombre).
* [ ] Fonctionnalité d'**Export / Backup** des données en un clic.
* [ ] Support multi-utilisateurs simple.

---
## Licence

Ce projet est distribué sous la licence MIT.