<?php

namespace G2RD;

/**
 * Gestion des animations GSAP (GreenSock Animation Platform)
 * 
 * Cette classe gère le chargement et la configuration des animations GSAP,
 * à la fois sur le frontend et dans l'éditeur de blocs.
 *
 * @package G2RD
 * @since 1.0.0
 */
class GSAPAnimations
{
    /**
     * Enregistre les hooks nécessaires pour les animations GSAP
     *
     * @since 1.0.0
     * @return void
     */
    public function registerHooks(): void
    {
        // Charger les scripts GSAP sur le frontend
        \add_action('wp_enqueue_scripts', [$this, 'registerFrontendScripts']);

        // Charger les contrôles de bloc dans l'éditeur
        \add_action('enqueue_block_editor_assets', [$this, 'registerEditorScripts']);
    }

    /**
     * Enregistre et charge les scripts GSAP pour le frontend
     * 
     * Charge les bibliothèques GSAP et ScrollTrigger depuis CDN,
     * ainsi que le script personnalisé d'animations.
     *
     * @since 1.0.0
     * @return void
     */
    public function registerFrontendScripts(): void
    {
        // Charger GSAP uniquement sur le frontend
        if (!\is_admin()) {
            \wp_enqueue_script(
                'gsap',
                'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js',
                [],
                null,
                true
            );
            \wp_enqueue_script(
                'scrolltrigger',
                'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js',
                ['gsap'],
                null,
                true
            );
            \wp_enqueue_script(
                'gsap-animation',
                \get_template_directory_uri() . '/assets/js/gsap-animation.js',
                ['gsap', 'scrolltrigger'],
                null,
                true
            );
        }
    }

    /**
     * Enregistre et charge les scripts pour les contrôles d'animation dans l'éditeur
     * 
     * Charge le script de contrôle des blocs et ajoute les données
     * localisées pour les options d'animation disponibles.
     *
     * @since 1.0.0
     * @return void
     */
    public function registerEditorScripts(): void
    {
        // Enregistrer le script de contrôle des blocs
        \wp_enqueue_script(
            'gsap-block-controls',
            \get_template_directory_uri() . '/assets/js/gsap-block-controls.js',
            ['wp-blocks', 'wp-element', 'wp-components', 'wp-editor'],
            \filemtime(\get_template_directory() . '/assets/js/gsap-block-controls.js'),
            true
        );

        // Ajouter les données localisées
        \wp_localize_script('gsap-block-controls', 'gsapBlockData', [
            'animations' => [
                'fadeIn' => 'Fade In',
                'slideUp' => 'Slide Up',
                'slideDown' => 'Slide Down',
                'slideLeft' => 'Slide Left',
                'slideRight' => 'Slide Right',
                'scale' => 'Scale',
                'rotate' => 'Rotate'
            ]
        ]);
    }
}
