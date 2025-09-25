<?php
// /app/lib/Flash.php

/**
 * Gère les messages "flash" stockés en session.
 * Un message flash est lu une seule fois puis détruit.
 */
class Flash {
    
    /**
     * Définit un message flash.
     *
     * @param string $key La clé du message (ex: 'error', 'success')
     * @param string $message Le message à afficher.
     */
    public static function set(string $key, string $message): void {
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Récupère un message flash, en le supprimant de la session.
     *
     * @param string $key La clé du message.
     * @return string|null Le message, ou null s'il n'existe pas.
     */
    public static function get(string $key): ?string {
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]); // Le message est "brûlé" après lecture
            return $message;
        }
        return null;
    }
}