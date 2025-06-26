<?php
/*
Plugin Name: Dock Funnels
Description: Dock Funnels ist ein Minimalistisches aber leistungsstarkes Plugin, das es dir ermöglicht, mehrstufige Formulare zu erstellen und zu verwalten. Es ist ideal für die Erstellung von Umfragen, Anmeldeformularen und anderen Formularen, die mehrere Schritte erfordern.
Version: 0.7.2
Author: Dock26 
*/

if (!defined('ABSPATH')) {
    exit;
}

// include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/db.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin.php';
require_once plugin_dir_path(__FILE__) . 'includes/ajax.php';
require_once plugin_dir_path(__FILE__) . 'includes/mailing.php';

// Load main plugin class
require_once plugin_dir_path(__FILE__) . 'includes/main.php';

// Initialize the plugin
class DockFunnels
{
    public static function init()
    {
        DockFunnels_Main::install();
    }
}
// Initialize the plugin
DockFunnels::init();
// Register activation, deactivation, and uninstall hooks
register_activation_hook(__FILE__, ['DockFunnels_Main', 'activate']);
register_deactivation_hook(__FILE__, ['DockFunnels_Main', 'deactivate']);
register_uninstall_hook(__FILE__, ['DockFunnels_Main', 'uninstall']);