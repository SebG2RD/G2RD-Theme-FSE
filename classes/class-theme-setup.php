<?php

namespace G2RD;

/**
 * Classe principale pour la configuration du thème
 * 
 * Cette classe gère l'initialisation et la configuration de base du thème,
 * incluant l'enregistrement des assets, la configuration des types MIME,
 * et la mise en place des fonctionnalités du thème.
 *
 * @package G2RD
 * @since 1.0.0
 */
class ThemeSetup
{
    /**
     * Enregistre tous les hooks nécessaires pour le thème
     *
     * @since 1.0.0
     * @return void
     */
    public function registerHooks(): void
    {
        \add_action('wp_enqueue_scripts', [$this, 'registerAssets']);
        \add_filter('upload_mimes', [$this, 'allowMimeTypes']);
        \add_filter('wp_check_filetype_and_ext', [$this, 'allowFileTypes'], 10, 4);
        \add_filter('sanitize_file_name', 'remove_accents');
        \add_action('init', [$this, 'g2rd_register_block_patterns']);

        $this->setupFeatures();
    }

    /**
     * Enregistre et charge les styles et scripts principaux du thème
     *
     * @since 1.0.0
     * @return void
     */
    public function registerAssets(): void
    {
        \wp_enqueue_style('main', \get_stylesheet_uri(), [], \wp_get_theme()->get('Version'));
    }

    /**
     * Ajoute le support pour les fichiers SVG et WebP
     *
     * @since 1.0.0
     * @param array $mimes Liste des types MIME autorisés
     * @return array Liste mise à jour des types MIME
     */
    public function allowMimeTypes($mimes): array
    {
        $mimes['svg'] = 'image/svg+xml';
        $mimes['webp'] = 'image/webp';

        return $mimes;
    }

    /**
     * Configure la validation des types de fichiers pour SVG et WebP
     *
     * @since 1.0.0
     * @param array $types Types de fichiers
     * @param string $file Chemin du fichier
     * @param string $filename Nom du fichier
     * @param array $mimes Types MIME
     * @return array Types de fichiers mis à jour
     */
    public function allowFileTypes($types, $file, $filename, $mimes): array
    {
        if (false !== strpos($filename, '.webp')) {
            $types['ext'] = 'webp';
            $types['type'] = 'image/webp';
        }

        return $types;
    }

    /**
     * Configure les fonctionnalités de base du thème
     *
     * @since 1.0.0
     * @return void
     */
    public function setupFeatures(): void
    {
        # Retirer la suggestion de blocs
        \remove_theme_support('core-block-patterns');

        # Ajouter des fonctionnalités
        \add_theme_support("editor-styles");

        # Désactiver l'ancienne API XML RPC
        \add_filter('xmlrpc_enabled', '__return_false');

        # Retirer les scripts des Emojis
        \remove_action('admin_print_styles', 'print_emoji_styles');
        \remove_action('wp_head', 'print_emoji_detection_script', 7);
        \remove_action('admin_print_scripts', 'print_emoji_detection_script');
        \remove_action('wp_print_styles', 'print_emoji_styles');
        \remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        \remove_filter('the_content_feed', 'wp_staticize_emoji');
        \remove_filter('comment_text_rss', 'wp_staticize_emoji');
    }

    /**
     * Enregistre les catégories de patterns de blocs personnalisés
     *
     * @since 1.0.0
     * @return void
     */
    function g2rd_register_block_patterns(): void
    {
        // Enregistrer les catégories
        $categories = [
            'design' => \__('Design', 'g2rd'),
            'card' => \__('Card', 'g2rd'),
            'hero' => \__('Hero', 'g2rd'),
            'info' => \__('Info', 'g2rd'),
            'posts' => \__('Posts', 'g2rd'),
            'header' => \__('Header', 'g2rd'),
            'footer' => \__('Footer', 'g2rd'),
            'widgets' => \__('Widgets', 'g2rd')
        ];

        foreach ($categories as $slug => $label) {
            \register_block_pattern_category($slug, ['label' => $label]);
        }
    }
}
