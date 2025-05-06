<?php

namespace G2RD;

/**
 * Gestion de l'auto-chargement des blocs et des assets de l'éditeur
 * 
 * Cette classe gère l'enregistrement automatique des blocs personnalisés,
 * le chargement des styles de blocs et la composition du theme.json.
 *
 * @package G2RD
 * @since 1.0.0
 */
class BlockEditorAutoload
{
    /**
     * Enregistre tous les hooks nécessaires pour l'éditeur de blocs
     *
     * @since 1.0.0
     * @return void
     */
    public function registerHooks(): void
    {
        \add_action('init', [$this, 'registerCustomBlocks']);
        \add_action('init', [$this, 'registerBlocksAssets']);
        \add_filter('wp_theme_json_data_theme', [$this, 'composeThemeJson']);
    }

    /**
     * Enregistre automatiquement les blocs personnalisés du dossier /blocks/
     *
     * @since 1.0.0
     * @return void
     */
    public function registerCustomBlocks(): void
    {
        $folders = \glob(\get_template_directory() . '/blocks/*/');

        foreach ($folders as $folder) {
            $block = basename($folder);
            \register_block_type(dirname(__DIR__) . "/blocks/$block");
        }
    }

    /**
     * Charge automatiquement les styles CSS des blocs
     *
     * @since 1.0.0
     * @return void
     */
    public function registerBlocksAssets(): void
    {
        $files = \glob(\get_template_directory() . '/assets/css/*.css');

        foreach ($files as $file) {
            $filename   = basename($file, '.css');
            $block_name = str_replace('core-', 'core/', $filename);

            \wp_enqueue_block_style(
                $block_name,
                [
                    'handle' => "capitaine-{$filename}",
                    'src'    => \get_theme_file_uri("assets/css/{$filename}.css"),
                    'path'   => \get_theme_file_path("assets/css/{$filename}.css"),
                    'ver'    => filemtime(\get_theme_file_path("assets/css/{$filename}.css")),
                ]
            );
        }
    }

    /**
     * Compose le theme.json à partir des fichiers de configuration
     *
     * Cette méthode fusionne les fichiers theme-styles.json, theme-settings.json
     * et les variations de style pour créer une configuration complète.
     *
     * @since 1.0.0
     * @param \WP_Theme_JSON_Data $theme_json Données du theme.json actuel
     * @return \WP_Theme_JSON_Data Données mises à jour du theme.json
     */
    public function composeThemeJson($theme_json): mixed
    {
        # Charger les JSON secondaires
        $theme_styles_raw = file_get_contents(\get_template_directory() . '/theme-styles.json');
        $theme_settings_raw = file_get_contents(\get_template_directory() . '/theme-settings.json');

        # Convertir le JSON en tableau
        $theme_styles = json_decode($theme_styles_raw, true);
        $theme_settings = json_decode($theme_settings_raw, true);

        # Charger les variations de style
        $styles_dir = \get_template_directory() . '/styles/';
        $style_files = \glob($styles_dir . '*.json');
        $variations = [];

        error_log('Début du chargement des variations de style');
        error_log('Répertoire des styles : ' . $styles_dir);
        error_log('Nombre de fichiers trouvés : ' . count($style_files));

        foreach ($style_files as $style_file) {
            error_log('Traitement du fichier : ' . $style_file);
            $style_data = json_decode(file_get_contents($style_file), true);
            if ($style_data && isset($style_data['title'])) {
                error_log('Style trouvé : ' . $style_data['title']);
                $variations[] = $style_data;
            } else {
                error_log('Erreur dans le fichier : ' . $style_file);
            }
        }

        error_log('Nombre de variations chargées : ' . count($variations));

        # Injecter les données
        $new_data = [
            'version' => 3,
            'settings' => $theme_settings['settings'],
            'styles' => $theme_styles['styles'],
            'variations' => $variations
        ];

        # Mettre à jour le theme.json de WordPress
        return $theme_json->update_with($new_data);
    }
}
