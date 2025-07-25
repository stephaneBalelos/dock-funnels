<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class DockFunnels_Admin
{

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

    public static function register_admin_menu()
    {
        add_menu_page(
            'Dock Funnels',
            'Dock Funnels',
            'manage_options',
            'dock-funnels',
            [__CLASS__, 'render_forms_page'],
            'dashicons-feedback'
        );

        add_submenu_page(
            'dock-funnels',
            'Form Responses',
            'Responses',
            'manage_options',
            'dock-funnels-responses',
            [__CLASS__, 'render_responses_page']
        );

        add_submenu_page(
            'dock-funnels',
            'Form Editor',
            'Form Editor',
            'manage_options',
            'dock-funnels-editor',
            [__CLASS__, 'render_form_editor_page']
        );
    }

    public static function render_forms_page()
    {
        if (!current_user_can('manage_options')) {
            echo '<div class="wrap"><h1>Access Denied</h1><p>You do not have permission to view this page.</p></div>';
            return;
        }
        if (isset($_GET['form_id']) && is_numeric($_GET['form_id'])) {
            $form_id = intval($_GET['form_id']);
            $form = DockFunnels_DB::get_form_by_id($form_id);
            print_r($form);
            if ($form) {
                echo '<div id="dock-funnels-editor" class="dock-funnels-root"></div></div>';
                return;
            } else {
                echo '<div class="wrap"><h1>Form Not Found</h1><p>The requested form does not exist.</p></div>';
                return;
            }
        } else {
            $forms = DockFunnels_DB::get_forms();
            echo '<div class="wrap"><h1>Dock Funnels</h1>';
            echo '<p>Verwalten Sie Ihre Formulare und deren Antworten.</p>';
            if (empty($forms)) {
                echo '<br><br><h2>Keine Formulare gefunden</h2><p>Bitte erstellen Sie ein neues Formular.</p>';
                echo '<a href="' . admin_url('admin.php?page=dock-funnels-editor') . '" class="button button-primary">Neues Formular erstellen</a>';
                return;
            }
            self::render_forms_table($forms);
            echo '</div>'; // Close the wrap div
            return;
        }
    }

    public static function render_responses_page()
    {
        if (!current_user_can('manage_options')) {
            echo '<div class="wrap"><h1>Access Denied</h1><p>You do not have permission to view this page.</p></div>';
            return;
        }
        if (!isset($_GET['form_id']) || !is_numeric($_GET['form_id'])) {
            $forms = DockFunnels_DB::get_forms();
            echo '<div class="wrap"><h1>Formular Einreichungen</h1>
                <table class="widefat">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                <tbody>';
            foreach ($forms as $form) {
                echo "<tr>
                <td>{$form['id']}</td>
                <td>{$form['name']}</td>
                <td>
                    <a href='" . admin_url("admin.php?page=dock-funnels-responses&form_id={$form['id']}") . "' class='button'>
                    Antworten anzeigen ({$form['response_count']})</a>
                </td>
                </tr>";
            }
            echo '</tbody></table></div>';
            return;
        } else {
            $form_id = intval($_GET['form_id']);
            $form = DockFunnels_DB::get_form_by_id($form_id);
            if (!$form) {
                echo '<div class="wrap"><h1>Form Not Found</h1><p>The requested form does not exist.</p></div>';
                return;
            } else {
                echo '<div id="dock-funnels-responses" class="dock-funnels-root"></div>';
            }
        }
    }

    public static function render_form_editor_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        if (isset($_GET['form_id']) && is_numeric($_GET['form_id'])) {
            $form_id = intval($_GET['form_id']);
            $form = DockFunnels_DB::get_form_by_id($form_id);
            if (!$form) {
                echo '<div class="wrap"><h1>Formular nicht gefunden</h1><p>
                Das Formular mit der ID <b>"' . $form_id . '"</b> wurde nicht gefunden. Bitte erstellen Sie ein neues Formular.
                <br><br>
                <a href="' . admin_url('admin.php?page=dock-funnels-editor') . '" class="button">Neues Formular erstellen</a>
                </p></div>';
                return;
            }
        } else {
            $form = null; // No form selected, create a new one
        }
        echo '<div id="dock-funnels-editor" class="dock-funnels-root"></div>';
    }

    public static function render_forms_table($forms)
    {
        echo '<table class="widefat">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Shortcode</th>
                    <th>Actions</th>
                </tr>
                </thead>
            <tbody>';
        foreach ($forms as $form) {
            echo "<tr>
            <td>{$form['id']}</td>
            <td>{$form['name']}</td>
            <td><code>[dock_funnel id='{$form['id']}']</code></td>
            <td>
                <a href='" . admin_url("admin.php?page=dock-funnels-editor&form_id={$form['id']}") . "' class='button'>Edit</a>
                <a href='" . admin_url("admin.php?page=dock-funnels-responses&form_id={$form['id']}") . "' class='button'>Responses</a>
            </tr>";
        }
        echo '</tbody></table>';
    }
}
