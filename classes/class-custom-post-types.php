<?php

namespace G2RD;

/**
 * Gestion des types de contenu personnalisés (CPT) et taxonomies
 * 
 * Cette classe gère la création et la configuration des types de contenu personnalisés,
 * notamment le portfolio, ainsi que leurs taxonomies associées et métadonnées.
 *
 * @package G2RD
 * @since 1.0.0
 */
class CPT
{
    /**
     * Enregistre tous les hooks nécessaires pour les CPT
     *
     * @since 1.0.0
     * @return void
     */
    public function registerHooks(): void
    {
        add_action('init', [$this, 'registerPostTypes']);
        add_action('add_meta_boxes', [$this, 'addPortfolioMetaBox']);
        add_action('save_post_portfolio', [$this, 'savePortfolioMeta']);
        add_filter('use_block_editor_for_post_type', [$this, 'disableGutenbergForPortfolio'], 10, 2);
        add_action('init', [$this, 'registerBindingSources']);
    }

    /**
     * Désactive l'éditeur Gutenberg pour le type de contenu Portfolio
     *
     * @since 1.0.0
     * @param bool $use_block_editor État actuel de l'éditeur de blocs
     * @param string $post_type Type de contenu
     * @return bool État modifié de l'éditeur de blocs
     */
    public function disableGutenbergForPortfolio($use_block_editor, $post_type): bool
    {
        if ($post_type === 'portfolio') {
            return false;
        }
        return $use_block_editor;
    }

    /**
     * Enregistre les types de contenu personnalisés et leurs taxonomies
     *
     * @since 1.0.0
     * @return void
     */
    public function registerPostTypes(): void
    {
        # CPT « Portfolio »
        $labels = [
            'name' => 'Portfolio',
            'all_items' => 'Tous les projets',
            'singular_name' => 'Projet',
            'add_new_item' => 'Ajouter un projet',
            'edit_item' => 'Modifier le projet',
            'menu_name' => 'Portfolio'
        ];

        $args = [
            'labels' => $labels,
            'public' => true,
            'show_in_rest' => true, // Garde l'API REST active
            'has_archive' => true,
            'supports' => ['title', 'editor', 'thumbnail', 'revisions', 'custom-fields'],
            'menu_position' => 5,
            'menu_icon' => 'dashicons-admin-appearance',
        ];

        register_post_type('portfolio', $args);

        # Taxonomy « Type de projets »
        $labels = [
            'name' => 'Types de projets',
            'singular_name' => 'Type de projet',
            'add_new_item' => 'Ajouter un Type de Projet',
            'new_item_name' => 'Nom du nouveau Projet',
            'parent_item' => 'Type de projet parent',
        ];

        $args = [
            'labels' => $labels,
            'public' => true,
            'show_in_rest' => true,
            'hierarchical' => true,
        ];

        register_taxonomy('type-projets', 'portfolio', $args);
    }

    /**
     * Ajoute une boîte de métadonnées pour le lien du portfolio
     *
     * @since 1.0.0
     * @return void
     */
    public function addPortfolioMetaBox(): void
    {
        add_meta_box(
            'portfolio_link', // ID unique
            'Lien du projet', // Titre de la boîte
            [$this, 'renderPortfolioLinkMetaBox'], // Fonction de rendu
            'portfolio', // Type de post
            'normal', // Contexte
            'high' // Priorité
        );
    }

    /**
     * Affiche le champ de lien dans la boîte de métadonnées
     *
     * @since 1.0.0
     * @param \WP_Post $post L'objet post actuel
     * @return void
     */
    public function renderPortfolioLinkMetaBox($post): void
    {
        // Récupérer la valeur existante du lien
        $link = get_post_meta($post->ID, '_portfolio_link', true);

        // Ajouter un champ nonce pour la sécurité
        wp_nonce_field('portfolio_link_nonce', 'portfolio_link_nonce');

        // Afficher le champ de saisie
        echo '<p><label for="portfolio_link">URL du projet :</label></p>';
        echo '<p><input type="url" id="portfolio_link" name="portfolio_link" value="' . esc_url($link) . '" style="width: 100%;" /></p>';
        echo '<p class="description">Entrez l\'URL complète du projet (ex: https://www.exemple.com)</p>';
    }

    /**
     * Sauvegarde le lien du portfolio en base de données
     *
     * @since 1.0.0
     * @param int $post_id ID du post
     * @return void
     */
    public function savePortfolioMeta($post_id): void
    {
        // Vérifier le nonce
        if (!isset($_POST['portfolio_link_nonce']) || !wp_verify_nonce($_POST['portfolio_link_nonce'], 'portfolio_link_nonce')) {
            return;
        }

        // Vérifier les permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Sauvegarder le lien
        if (isset($_POST['portfolio_link'])) {
            update_post_meta($post_id, '_portfolio_link', esc_url_raw($_POST['portfolio_link']));
        }
    }

    /**
     * Enregistre les sources de données personnalisées pour le block binding
     *
     * @since 1.0.0
     * @return void
     */
    public function registerBindingSources(): void
    {
        // Enregistrer le shortcode
        add_shortcode('portfolio_link', [$this, 'portfolioLinkShortcode']);
    }

    /**
     * Shortcode pour afficher le lien du portfolio
     *
     * @since 1.0.0
     * @return string URL du projet ou '#' si non définie
     */
    public function portfolioLinkShortcode(): string
    {
        $post_id = get_the_ID();
        $link = get_post_meta($post_id, '_portfolio_link', true);

        if (empty($link)) {
            return '#';
        }

        return esc_url($link);
    }
}
