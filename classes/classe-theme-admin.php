<?php 

namespace G2RD;

/**
 * Gestion de l'interface d'administration WordPress
 * 
 * Cette classe personnalise l'interface d'administration WordPress,
 * notamment la page de connexion et le tableau de bord, avec le branding G2RD.
 *
 * @package G2RD
 * @since 1.0.0
 */
class ThemeAdmin
{
    /**
     * Chemin du logo G2RD
     *
     * @since 1.0.0
     * @var string
     */
    private const LOGO_PATH = '/assets/img/Nouveau-logo-G2RD-Agence-Web-blanc-Horizontale@3x.png';
    
    /**
     * Chemin de l'image de fond de la page de connexion
     *
     * @since 1.0.0
     * @var string
     */
    private const BACKGROUND_IMAGE_PATH = '/assets/img/g2rd_image_admin.jpg';
    
    /**
     * URL du site G2RD
     *
     * @since 1.0.0
     * @var string
     */
    private const G2RD_WEBSITE = 'https://g2rd.fr';
    
    /**
     * Enregistre tous les hooks nécessaires pour la personnalisation de l'admin
     *
     * @since 1.0.0
     * @return void
     */
    public function registerHooks(): void
    {
        // Hooks pour les styles
        \add_action('admin_enqueue_scripts', [$this, 'registerAdminAssets']);
        \add_action('login_enqueue_scripts', [$this, 'registerLoginAssets']);
        
        // Hooks pour personnaliser le logo de connexion
        \add_filter('login_headerurl', [$this, 'customLoginLogoUrl']);
        \add_filter('login_headertext', [$this, 'customLoginLogoText']);
        
        // Hooks pour personnaliser la structure de la page de connexion
        \add_action('login_head', [$this, 'customLoginLogo']);
        \add_action('login_header', [$this, 'customLoginStructure'], 0);
        \add_action('login_footer', [$this, 'customLoginFooter']);
        
        // Hook pour personnaliser le logo dans l'admin
        \add_action('admin_head', [$this, 'customAdminLogo']);
        
        // Ajouter le bouton après le formulaire
        \add_action('login_footer', [$this, 'addG2RDButton']);
    }
    
    /**
     * Obtient l'URL complète du logo G2RD
     *
     * @since 1.0.0
     * @return string URL du logo
     */
    private function getLogoUrl(): string
    {
        return \get_template_directory_uri() . self::LOGO_PATH;
    }
    
    /**
     * Obtient l'URL complète de l'image de fond
     *
     * @since 1.0.0
     * @return string URL de l'image de fond
     */
    private function getBackgroundImageUrl(): string
    {
        return \get_template_directory_uri() . self::BACKGROUND_IMAGE_PATH;
    }
    
    /**
     * Enregistre et charge les styles CSS pour l'interface d'administration
     *
     * @since 1.0.0
     * @return void
     */
    public function registerAdminAssets(): void
    {
        \wp_enqueue_style(
            'g2rd-admin', 
            \get_template_directory_uri() . '/assets/css/admin.css', 
            [], 
            \filemtime(\get_template_directory() . '/assets/css/admin.css')
        );
    }

    /**
     * Enregistre et charge les styles CSS pour la page de connexion
     *
     * @since 1.0.0
     * @return void
     */
    public function registerLoginAssets(): void
    {
        \wp_enqueue_style(
            'g2rd-login', 
            \get_template_directory_uri() . '/assets/css/login.css', 
            [], 
            \filemtime(\get_template_directory() . '/assets/css/login.css')
        );
    }
    
    /**
     * Personnalise le logo et le style de la page de connexion
     *
     * @since 1.0.0
     * @return void
     */
    public function customLoginLogo(): void
    {
        echo '<style>
            .login h1 a {
                background-image: url(' . $this->getLogoUrl() . ') !important;
                background-size: contain !important;
                width: 250px !important;
                height: 70px !important;
                margin-bottom: 30px !important;
            }
            .login-image {
                background-image: url(' . $this->getBackgroundImageUrl() . ') !important;
            }
            .g2rd-button {
                display: block;
                width: 100%;
                margin: 15px 0 5px;
                padding: 12px;
                background: var(--secondary-color);
                color: white;
                text-align: center;
                text-decoration: none;
                border-radius: 8px;
                font-weight: bold;
                transition: all 0.3s ease;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            .g2rd-button:hover {
                background: var(--secondary-color-darker);
                transform: translateY(-2px);
                color: white;
            }
        </style>';
    }

    /**
     * Définit l'URL du logo sur la page de connexion
     *
     * @since 1.0.0
     * @return string URL de la page d'accueil
     */
    public function customLoginLogoUrl(): string
    {
        return \home_url('/');
    }

    /**
     * Définit le texte alternatif du logo sur la page de connexion
     *
     * @since 1.0.0
     * @return string Nom du site
     */
    public function customLoginLogoText(): string
    {
        return \get_bloginfo('name');
    }
    
    /**
     * Ajoute un bouton de redirection vers le site G2RD après le formulaire de connexion
     *
     * @since 1.0.0
     * @return void
     */
    public function addG2RDButton(): void
    {
        echo '<script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
                var loginForm = document.getElementById("loginform");
                if (loginForm) {
                    var button = document.createElement("a");
                    button.href = "' . self::G2RD_WEBSITE . '";
                    button.target = "_blank";
                    button.className = "g2rd-button";
                    button.textContent = "Visiter G2RD Agence Web";
                    loginForm.insertAdjacentElement("afterend", button);
                }
            });
        </script>';
    }
    
    /**
     * Ajoute la structure HTML personnalisée pour la page de connexion
     *
     * @since 1.0.0
     * @return void
     */
    public function customLoginStructure(): void
    {
        echo '<div class="login-container">';
        echo '<div class="login-image"></div>';
    }

    /**
     * Ajoute le pied de page personnalisé pour la page de connexion
     *
     * @since 1.0.0
     * @return void
     */
    public function customLoginFooter(): void
    {
        echo '</div>'; // Fermeture de login-container
    }
    
    /**
     * Personnalise le logo WordPress dans la barre d'administration
     *
     * @since 1.0.0
     * @return void
     */
    public function customAdminLogo(): void
    {
        echo '<style>
            #wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon:before {
                background-image: url(' . $this->getLogoUrl() . ') !important;
                background-position: center center !important;
                background-repeat: no-repeat !important;
                background-size: contain !important;
                content: "" !important;
                top: 0 !important;
                width: 100% !important;
                height: 100% !important;
            }
            #wpadminbar #wp-admin-bar-wp-logo > .ab-item {
                padding-right: 50px !important;
            }
            #wpadminbar #wp-admin-bar-wp-logo > .ab-sub-wrapper {
                display: none !important;
            }
        </style>';
    }
}

