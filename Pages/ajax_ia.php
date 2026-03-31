<?php
session_start();

// Charge les variables d'environnement
require_once('../config/.env.php');

// Sécurité : on bloque l'accès si non connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die("Non autorisé");
}

// Récupère la clé API depuis les variables d'environnement
$api_key = getenv('GEMINI_API_KEY') ?: ($_ENV['GEMINI_API_KEY'] ?? null);

if (!$api_key) {
    http_response_code(500);
    die("Erreur de configuration : clé API Gemini manquante. Vérifie le fichier .env");
}

$bio_actuelle = $_POST['bio'] ?? '';

if (empty(trim($bio_actuelle))) {
    http_response_code(400);
    die("Tu dois écrire quelques mots d'abord pour que je puisse reformuler !");
}

// URL de l'API Gemini — la clé vient uniquement de $api_key
$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $api_key;

// Prompt
$prompt = "Tu es un assistant expert en rédaction. Améliore cette bio pour un réseau social d'étudiants (Ynov Campus). "
        . "Rends-la professionnelle mais cool, dynamique et sans fautes. "
        . "Ne renvoie QUE la bio corrigée, sans guillemets, sans commentaires ni introduction.\n\n"
        . "Bio à corriger :\n" . $bio_actuelle;

// Corps de la requête
$data = [
    "contents" => [
        [
            "parts" => [
                ["text" => $prompt]
            ]
        ]
    ]
];

// Envoi via cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // À activer en production

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Décode la réponse
$result = json_decode($response, true);

// Logs debug (visibles uniquement dans les logs serveur)
error_log("GEMINI HTTP CODE: " . $http_code);
error_log("GEMINI RESPONSE: " . print_r($result, true));

// Renvoie le résultat
if ($http_code === 200 && isset($result['candidates'][0]['content']['parts'][0]['text'])) {
    echo trim($result['candidates'][0]['content']['parts'][0]['text']);
} else {
    http_response_code(500);
    echo "❌ Erreur API (Code: " . $http_code . ")\n";
    if (isset($result['error']['message'])) {
        echo "Message : " . $result['error']['message'];
    } elseif ($curl_error) {
        echo "Erreur cURL : " . $curl_error;
    } else {
        echo "Vérifie ta clé API Gemini et ta connexion internet.";
    }
}
?>