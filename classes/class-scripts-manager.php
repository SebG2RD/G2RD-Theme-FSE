<?php
/**
 * Gestion des scripts JavaScript du thème
 *
 * @package G2RD
 * @since 1.0.0
 */

namespace G2RD;

/**
 * Gestionnaire des scripts JavaScript
 *
 * Cette classe gère l'enregistrement et le chargement des scripts JavaScript
 * nécessaires au fonctionnement du thème, notamment pour les interactions
 * utilisateur et les effets visuels.
 *
 * @package G2RD
 * @since 1.0.0
 */
class ScriptsManager
{
    /**
     * Enregistre les hooks WordPress pour la gestion des scripts
     *
     * @since 1.0.0
     * @return void
     */
    public function registerHooks(): void
    {
        // Utiliser le namespace global pour les fonctions WordPress
        \add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
    }

    /**
     * Enregistre et charge les scripts JavaScript du thème
     *
     * Cette méthode charge les scripts nécessaires pour :
     * - Rendre les articles entièrement cliquables
     * - Gérer l'effet de particules interactif
     *
     * @since 1.0.0
     * @return void
     */
    public function enqueueScripts(): void
    {
        // Script pour rendre les articles cliquables
        \wp_enqueue_script(
            'g2rd-clickable-articles',
            \get_template_directory_uri() . '/assets/js/clickable-articles.js',
            [],
            '1.0.0',
            true
        );
        
        // Script pour l'effet de particules
        \wp_enqueue_script(
            'g2rd-particles',
            \get_template_directory_uri() . '/assets/js/g2rd-particles.js',
            [],
            '1.0.0',
            true
        );
    }
} 