<?php

/**
 * Fichier principal du thème G2RD
 *
 * Ce fichier sert de point d'entrée pour le thème et initie les différentes
 * classes et fonctionnalités du thème.
 *
 * @package G2RD
 */

namespace G2RD;

// Inclure les fichiers des classes principales
require_once __DIR__ . '/classes/class-theme-setup.php';
require_once __DIR__ . '/classes/class-custom-post-types.php';
require_once __DIR__ . '/classes/class-block-editor-autoload.php';
require_once __DIR__ . '/classes/classe-theme-admin.php';
require_once __DIR__ . '/classes/class-gsap-animations.php';
require_once __DIR__ . '/classes/class-json-config.php';
require_once __DIR__ . '/classes/class-scripts-manager.php';
require_once __DIR__ . '/classes/class-particules-effect.php';

/**
 * Initialise toutes les composantes du thème
 */
function bootstrap_theme()
{
    // Charger les traductions
    load_theme_textdomain('G2RD', get_template_directory() . '/languages');

    // Instancier et initialiser les classes principales
    $classes = [
        ThemeSetup::class,
        CPT::class,
        BlockEditorAutoload::class,
        ThemeAdmin::class,
        GSAPAnimations::class,
        JsonConfig::class,
        ScriptsManager::class,
        ParticlesEffect::class
    ];

    // Enregistrer les hooks pour chaque classe
    foreach ($classes as $class) {
        (new $class)->registerHooks();
    }
}

// Démarrer le thème
bootstrap_theme();
