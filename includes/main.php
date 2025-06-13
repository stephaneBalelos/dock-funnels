<?php

class DockFunnels_Main {

    public function __construct() {
        register_activation_hook(__FILE__, [ 'DockFunnels_DB', 'activate' ]);
        register_deactivation_hook(__FILE__, [ 'DockFunnels_DB', 'deactivate' ]);
        register_uninstall_hook(__FILE__, [ 'DockFunnels_DB', 'uninstall' ]);

        add_action('init', [ $this, 'register_shortcode' ]);
        add_action('admin_menu', [ 'DockFunnels_Admin', 'register_admin_menu' ]);

        add_action('wp_ajax_dock_funnel_ajax_create_form', [ 'DockFunnels_Ajax', 'create_form' ]);
        add_action('wp_ajax_nopriv_dock_funnel_ajax_submit', [ 'DockFunnels_Ajax', 'handle_form_submission' ]);

        add_action('admin_enqueue_scripts', [ 'DockFunnels_Admin', 'enqueue_admin_assets' ]);
    }

    public function register_shortcode() {
        add_shortcode('dock_funnel', [ 'DockFunnels_Shortcode', 'render' ]);
    }
}
