<?php
/*
Plugin Name: Dock Funnels
Description: A plugin for creating and managing funnel forms in WordPress.
Version: 1.0
Author: Your Name
*/

if (!defined('ABSPATH')) {
    exit;
}

// Autoload all plugin classes
spl_autoload_register(function ($class) {
    if (strpos($class, 'DockFunnels_') === 0) {
        $file = plugin_dir_path(__FILE__) . 'includes/' . strtolower(str_replace('DockFunnels_', '', $class)) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

// Load main plugin class
require_once plugin_dir_path(__FILE__) . 'includes/main.php';

new DockFunnels_Main();