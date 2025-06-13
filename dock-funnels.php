<?php
/*
Plugin Name: Dock Funnels
Description: A plugin for creating and managing funnel forms in WordPress.
Version: 1.0
Author: Your Name
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class DockFunnels
{

    public function __construct()
    {
        // Hooks
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);
        register_uninstall_hook(__FILE__, ['DockFunnels', 'uninstall']);

        add_action('admin_menu', [$this, 'admin_menu']);
        add_shortcode('dock_funnel', [$this, 'render_shortcode']);
        add_action('admin_post_dock_funnel_submit', [$this, 'handle_form_submission']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
    }

    public function activate()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $forms_table = $wpdb->prefix . 'dock_funnels';
        $responses_table = $wpdb->prefix . 'dock_funnel_responses';

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql_forms = "CREATE TABLE $forms_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name text NOT NULL,
            fields longtext NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql_responses = "CREATE TABLE $responses_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            form_id mediumint(9) NOT NULL,
            response longtext NOT NULL,
            submitted_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        dbDelta($sql_forms);
        dbDelta($sql_responses);
    }

    public function deactivate()
    {
        // Currently no action on deactivation
    }

    public function enqueue_admin_assets($hook)
    {
        if (strpos($hook, 'dock-funnels') === false) return;

        wp_enqueue_style('dock-funnels-dashboard', plugin_dir_url(__FILE__) . 'front-end/dist/assets/dashboard/dock-funnels.css');
        wp_enqueue_script('dock-funnels-vue', 'https://unpkg.com/vue@3/dist/vue.global.js"', [], null, true);
        wp_enqueue_script('dock-funnels-dashboard', plugin_dir_url(__FILE__) . 'front-end/dist/assets/dashboard/index.iife.js', [], null, true);

        $data = [
            'formsTable' => $this->get_all_forms(),
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('dock_funnels_nonce')
        ];
        wp_localize_script('dock-funnels-dashboard', 'DockFunnelsData', $data);
    }

    public function enqueue_frontend_assets($form_id)
    {
        wp_enqueue_style('dock-funnels-form', plugin_dir_url(__FILE__) . 'front-end/dist/assets/forms/dock-funnels.css');
        wp_enqueue_script('dock-funnels-vue', 'https://unpkg.com/vue@3/dist/vue.global.js', [], null, true);
        wp_enqueue_script('dock-funnels-form', plugin_dir_url(__FILE__) . 'front-end/dist/assets/forms/index.iife.js', [], null, true);


        $data = [
            'formRow' => $this->get_form_by_id($form_id),
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('dock_funnels_form_nonce')
        ];

        wp_localize_script('dock-funnels-form', 'DockFunnelsFormData', $data);
    }

    private function get_all_forms()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dock_funnels';
        return $wpdb->get_results("SELECT id, name FROM $table_name", ARRAY_A);
    }

    private function get_form_by_id($form_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dock_funnels';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $form_id));
    }

    public static function uninstall()
    {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dock_funnels");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dock_funnel_responses");
    }

    public function admin_menu()
    {
        add_menu_page('Dock Funnels', 'Dock Funnels', 'manage_options', 'dock-funnels', [$this, 'dashboard_page']);
        add_submenu_page('dock-funnels', 'Add New', 'Add New', 'manage_options', 'dock-funnels-add', [$this, 'add_form_page']);
        add_submenu_page(null, 'View Responses', 'View Responses', 'manage_options', 'dock-funnels-responses', [$this, 'view_responses_page']);
    }

    public function dashboard_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        echo '<div class="wrap"><h1>Dock Funnels dfldo</h1>';
        echo '<div id="app"></div>';
        global $wpdb;
        $table_name = $wpdb->prefix . 'dock_funnels';
        $forms = $wpdb->get_results("SELECT * FROM $table_name");
        echo '<table class="widefat"><thead><tr><th>ID</th><th>Name</th><th>Created</th><th>Shortcode</th><th>Actions</th></tr></thead><tbody>';
        foreach ($forms as $form) {
            echo '<tr>';
            echo '<td>' . esc_html($form->id) . '</td>';
            echo '<td>' . esc_html($form->name) . '</td>';
            echo '<td>' . esc_html($form->created_at) . '</td>';
            echo '<td>[dock_funnel id=' . esc_html($form->id) . ']</td>';
            echo '<td><a href="admin.php?page=dock-funnels-responses&form_id=' . esc_attr($form->id) . '">View Responses</a></td>';
            echo '</tr>';
        }
        echo '</tbody></table></div>';
    }

    public function add_form_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_name'], $_POST['form_fields'])) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'dock_funnels';
            $wpdb->insert($table_name, [
                'name' => sanitize_text_field($_POST['form_name']),
                'fields' => wp_json_encode(json_decode(stripslashes($_POST['form_fields'])))
            ]);
            echo '<div class="updated"><p>Form added successfully.</p></div>';
        }

        echo '<div class="wrap"><h1>Add New Funnel Form</h1>';
        echo '<form method="post">
            <p><label for="form_name">Form Name</label><br><input type="text" name="form_name" required></p>
            <p><label for="form_fields">Form Fields (JSON format)</label><br>
            <textarea name="form_fields" rows="10" cols="50" required>[{"name": "email", "label": "Email"}, {"name": "name", "label": "Name"}]</textarea></p>
            <p><input type="submit" value="Create Form" class="button button-primary"></p>
        </form></div>';
    }

    public function view_responses_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        echo '<div class="wrap"><h1>Form Responses</h1>';
        if (!isset($_GET['form_id'])) {
            echo '<p>No form selected.</p></div>';
            return;
        }
        $form_id = intval($_GET['form_id']);
        global $wpdb;
        $table_name = $wpdb->prefix . 'dock_funnel_responses';
        $responses = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE form_id = %d", $form_id));

        echo '<table class="widefat"><thead><tr><th>ID</th><th>Response</th><th>Submitted At</th></tr></thead><tbody>';
        foreach ($responses as $response) {
            echo '<tr>';
            echo '<td>' . esc_html($response->id) . '</td>';
            echo '<td><pre>' . esc_html(print_r(json_decode($response->response, true), true)) . '</pre></td>';
            echo '<td>' . esc_html($response->submitted_at) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table></div>';
    }

    public function render_shortcode($atts)
    {

        $atts = shortcode_atts(['id' => 0], $atts);
        $form_id = intval($atts['id']);

        $this->enqueue_frontend_assets($form_id);


        ob_start();
?>
        <div id="dock-funnels-form"></div>
<?php
        return ob_get_clean();
    }

    public function handle_form_submission()
    {
        if (!isset($_POST['form_id']) || !isset($_POST['fields'])) {
            wp_die('Invalid form submission.');
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'dock_funnel_responses';

        $wpdb->insert(
            $table_name,
            [
                'form_id' => intval($_POST['form_id']),
                'response' => wp_json_encode($_POST['fields']),
            ]
        );

        wp_redirect($_SERVER['HTTP_REFERER']);
        exit;
    }
}

new DockFunnels();
