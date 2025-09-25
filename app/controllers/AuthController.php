<?php
// /app/controllers/AuthController.php

class AuthController {

    public function showLogin() {
        if (Auth::isLoggedIn()) {
            header('Location: /');
            exit();
        }
        
        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);

        $this->view('auth/login', ['error' => $error]);
    }

    public function handleLogin() {
        if (!isset($_POST['email'], $_POST['password'], $_POST['csrf_token'])) {
            $this->redirectWithError('Champs manquants.');
        }
        
        if (!CSRF::validateToken($_POST['csrf_token'])) {
            $this->redirectWithError('Jeton de sécurité invalide.');
        }

        if (Auth::attemptLogin($_POST['email'], $_POST['password'])) {
            header('Location: /');
            exit();
        } else {
            $this->redirectWithError('Identifiants incorrects.');
        }
    }

    public function logout() {
        Auth::logout();
        header('Location: /login');
        exit();
    }
    
    private function redirectWithError(string $message) {
        $_SESSION['login_error'] = $message;
        header('Location: /login');
        exit();
    }
    
    // Helper pour charger les vues
    protected function view($view_path, $data = []) {
        extract($data);
        $content = __DIR__ . '/../views/' . $view_path . '.php';
        require __DIR__ . '/../views/layout/base.php';
    }
}