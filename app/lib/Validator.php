<?php
// /app/lib/Validator.php

class Validator {
    
    /**
     * Valide un titre (longueur min/max).
     */
    public static function isTitle(string $title, int $min = 3, int $max = 200): bool {
        $length = mb_strlen(trim($title));
        return $length >= $min && $length <= $max;
    }

    /**
     * Valide une URL GitHub (format basique).
     */
    public static function isGithubUrl(?string $url): bool {
        // Le champ est optionnel, donc une URL vide est valide.
        if (empty($url)) {
            return true;
        }
        
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        
        return (bool) preg_match('#^https://(www\.)?github\.com/.+#', $url);
    }
}