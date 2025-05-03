<?php

/*
Plugin Name: Visualiseur de log WordPress (POO)
Description: Affiche le contenu de debug.log dans l'administration avec possibilité de vider le fichier.
Author: votre serviteur !
Version: 1.0
*/
/*info au dessus nécessaire à la reconnaissance par wp du plugin 
attention pas d'espace après le nom de l'entrée*/

//sécurite du plugin
defined('ABSPATH') || exit;

//chargement de la classe principale du plugin
require_once plugin_dir_path(__FILE__) . 'includes/LogViewer.php';


function initplugin()
{
    \WPLogViewer\LogViewer::get_instance()->init();
}
add_action('plugin_loaded', 'initplugin');
// hook d’activation du plugin
function wp_log_viewer_activate() {
    \WPLogViewer\LogViewer::activate();
}
register_activation_hook(__FILE__, 'wp_log_viewer_activate');

// hook de désactivation du plugin
function wp_log_viewer_deactivate() {
    \WPLogViewer\LogViewer::deactivate();
}
register_deactivation_hook(__FILE__, 'wp_log_viewer_deactivate');

// hook de désinstallation du plugin
function wp_log_viewer_uninstall() {
    \WPLogViewer\LogViewer::uninstall();
}
register_uninstall_hook(__FILE__, 'wp_log_viewer_uninstall');

