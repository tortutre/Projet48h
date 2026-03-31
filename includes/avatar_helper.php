<?php
/**
 * Fonction helper pour afficher les avatars de profil de manière cohérente
 */

/**
 * Retourne le chemin vers l'avatar d'un utilisateur
 * 
 * @param string|null $avatar Nom du fichier avatar depuis la BDD
 * @return string Chemin de l'avatar ou image par défaut
 */
function getAvatarPath($avatar) {
    if (!empty($avatar)) {
        return '../assets/img/avatars/' . htmlspecialchars($avatar);
    }
    return '../assets/img/default-avatar.png';
}

/**
 * Retourne le HTML d'une balise img pour un avatar
 * 
 * @param string|null $avatar Nom du fichier avatar
 * @param string $alt Texte alternatif
 * @param string $class Classes CSS optionnelles
 * @param int $size Taille en pixels (optionnel)
 * @return string HTML de l'image
 */
function getAvatarImage($avatar, $alt = '', $class = '', $size = 50) {
    $path = getAvatarPath($avatar);
    $classAttr = $class ? " class=\"{$class}\"" : '';
    $style = " style=\"width: {$size}px; height: {$size}px; border-radius: 50%; object-fit: cover;\"";
    
    return "<img src=\"{$path}\" alt=\"{$alt}\"{$classAttr}{$style} />";
}

/**
 * Retourne le HTML d'un avatar circulaire avec initialles en fallback (CSS)
 * 
 * @param string|null $avatar Nom du fichier avatar
 * @param string $initials Initiales (fallback si pas d'image)
 * @param string $class Classes CSS optionnelles
 * @param int $size Taille en pixels
 * @return string HTML du div avatar
 */
function getAvatarDiv($avatar, $initials = 'U', $class = '', $size = 50) {
    $path = getAvatarPath($avatar);
    $classAttr = $class ? " {$class}" : '';
    $bgImage = !empty($avatar) ? "background-image: url('{$path}'); background-size: cover; background-position: center;" : "background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 18px;";
    
    return "<div class=\"avatar{$classAttr}\" style=\"width: {$size}px; height: {$size}px; border-radius: 50%; flex-shrink: 0; {$bgImage}\">" . (!empty($avatar) ? '' : htmlspecialchars($initials)) . "</div>";
}
?>
