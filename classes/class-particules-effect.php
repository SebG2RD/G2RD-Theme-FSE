<?php
/**
 * Gestion de l'effet de particules interactif
 *
 * @package G2RD
 * @since 1.0.0
 */

namespace G2RD;

/**
 * Gestionnaire de l'effet de particules
 *
 * Cette classe gère l'ajout et la configuration de l'effet de particules
 * interactif dans les blocs de type group, ainsi que les contrôles
 * associés dans l'éditeur de blocs.
 *
 * @package G2RD
 * @since 1.0.0
 */
class ParticlesEffect
{
    /**
     * Enregistre les hooks nécessaires pour l'effet de particules
     *
     * @since 1.0.0
     * @return void
     */
    public function registerHooks(): void
    {
        // Ajouter les contrôles de bloc dans l'éditeur
        \add_action('enqueue_block_editor_assets', [$this, 'registerEditorControls'], 5);
        
        // Ajouter l'attribut personnalisé aux blocs concernés
        \add_filter('render_block', [$this, 'addParticlesAttribute'], 10, 2);
        
        // Ajouter des styles CSS pour l'éditeur
        \add_action('admin_head', [$this, 'addEditorStyles']);
    }

    /**
     * Enregistre et charge les contrôles de l'effet de particules dans l'éditeur
     *
     * @since 1.0.0
     * @return void
     */
    public function registerEditorControls(): void
    {
        // Enregistrer le script de contrôle avec les dépendances correctes
        \wp_enqueue_script(
            'g2rd-particles-sidebar',
            \get_template_directory_uri() . '/assets/js/g2rd-particles-sidebar.js',
            [
                'wp-blocks',
                'wp-dom-ready',
                'wp-element',
                'wp-components',
                'wp-block-editor',
                'wp-compose',
                'wp-data',
                'wp-i18n',
                'wp-hooks',
            ],
            \filemtime(\get_template_directory() . '/assets/js/g2rd-particles-sidebar.js'),
            true
        );
    }
    
    /**
     * Ajoute les styles CSS pour les contrôles de l'effet de particules dans l'éditeur
     *
     * @since 1.0.0
     * @return void
     */
    public function addEditorStyles(): void
    {
        if (!\is_admin() || !function_exists('get_current_screen') || get_current_screen()->base !== 'post') {
            return;
        }
        
        echo '<style>
            /* Assurez-vous que le panneau d\'effet de particules apparaît avant le panneau GSAP */
            .g2rd-particles-panel {
                order: 9 !important;
            }
            
            /* Donner un aspect unique au panneau */
            .g2rd-particles-panel .components-toggle-control .components-base-control__field {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            
            .g2rd-particles-panel .components-panel__body-title {
                color: #0073aa;
            }
        </style>';
    }

    /**
     * Ajoute l'attribut data-particles aux blocs de type group
     *
     * Cette méthode ajoute l'attribut data-particles="true" aux blocs
     * de type group qui ont l'effet de particules activé.
     *
     * @since 1.0.0
     * @param string $block_content Le contenu HTML du bloc
     * @param array  $block        Les informations du bloc
     * @return string Le contenu HTML modifié
     */
    public function addParticlesAttribute(string $block_content, array $block): string
    {
        // Ne s'applique qu'aux blocs de type group
        if ($block['blockName'] !== 'core/group') {
            return $block_content;
        }

        // Vérifier si l'attribut particlesEffect est activé
        $has_particles = isset($block['attrs']['particlesEffect']) && $block['attrs']['particlesEffect'] === true;

        if ($has_particles) {
            // Ajouter l'attribut data-particles="true" à l'élément
            $block_content = preg_replace(
                '/<div/',
                '<div data-particles="true"',
                $block_content,
                1
            );
        }

        return $block_content;
    }
} 