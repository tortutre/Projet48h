<?php
/**
 * Charge les variables du fichier .env
 * Utilisation : require_once('../config/.env.php');
 */

$env_file = __DIR__ . '/../.env';

if (file_exists($env_file)) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Ignore les commentaires
        if (strpos($line, '#') === 0) {
            continue;
        }
        
        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Enlève les guillemets si présents
            if (preg_match('/^"(.*)"$/', $value)) {
                $value = substr($value, 1, -1);
            }
            
            // Défini la variable d'environnement
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}
?>
