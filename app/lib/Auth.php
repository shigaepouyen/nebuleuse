<?php // /app/lib/Auth.php
class Auth {
    public static function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    public static function attemptLogin(string $email, string $password): bool {
        $db = get_db_connection();
        $stmt = $db->prepare("SELECT id, password_hash FROM user WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            return true;
        }
        return false;
    }

    public static function logout() {
        session_destroy();
    }
}