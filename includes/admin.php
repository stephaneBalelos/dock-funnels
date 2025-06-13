<?php

class DockFunnels_Admin {

    public static function enqueue_admin_assets($hook)
    {
        if (strpos($hook, 'dock-funnels') === false) return;

        wp_enqueue_style('dock-funnels-dashboard', plugin_dir_url(__FILE__) . '../front-end/dist/assets/dashboard/dock-funnels.css');
        wp_enqueue_script('dock-funnels-vue', 'https://unpkg.com/vue@3/dist/vue.global.js"', [], null, true);
        wp_enqueue_script('dock-funnels-dashboard', plugin_dir_url(__FILE__) . '../front-end/dist/assets/dashboard/index.iife.js', [], null, true);

        $data = [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('dock_funnels_admin_nonce')
        ];
        wp_localize_script('dock-funnels-dashboard', 'DockFunnelsAdmin', $data);
    }

    public static function register_admin_menu() {
        add_menu_page(
            'Dock Funnels',
            'Dock Funnels',
            'manage_options',
            'dock-funnels',
            [ __CLASS__, 'render_forms_page' ],
            'dashicons-feedback'
        );

        add_submenu_page(
            'dock-funnels',
            'Form Responses',
            'Responses',
            'manage_options',
            'dock-funnels-responses',
            [ __CLASS__, 'render_responses_page' ]
        );

        add_submenu_page(
            'dock-funnels',
            'Create Form',
            'Create Form',
            'manage_options',
            'dock-funnels-create-form',
            [ __CLASS__, 'render_create_form_page' ]
        );
    }

    public static function render_forms_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        $forms = DockFunnels_DB::get_forms();
        echo '<div class="wrap"><h1>Dock Funnels</h1><table class="widefat"><thead><tr><th>ID</th><th>Name</th></tr></thead><tbody>';
        foreach ($forms as $form) {
            echo "<tr><td>{$form['id']}</td><td>{$form['name']}</td></tr>";
        }
        echo '</tbody></table></div>';
    }

    public static function render_responses_page() {
        if (!current_user_can('manage_options') || !isset($_GET['form_id'])) {
            echo '<div class="wrap"><h1>Responses</h1><p>No form selected.</p></div>';
            return;
        }

        $form_id = intval($_GET['form_id']);
        $responses = DockFunnels_DB::get_form_responses($form_id);

        echo '<div class="wrap"><h1>Form Responses</h1><table class="widefat"><thead><tr><th>ID</th><th>Submitted At</th><th>Response</th></tr></thead><tbody>';
        foreach ($responses as $r) {
            echo "<tr><td>{$r['id']}</td><td>{$r['submitted_at']}</td><td><pre>" . esc_html($r['response']) . "</pre></td></tr>";
        }
        echo '</tbody></table></div>';
    }

    public static function render_create_form_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        echo '<div id="app"></div>';
    }


}
