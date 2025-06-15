<?php

class DockFunnels_Shortcode
{

    public static function render($atts)
    {
        // Enqueue necessary scripts and styles
        if (!is_admin()) {
            self::enqueue_scripts($atts['id']);
        }
        $atts = shortcode_atts(['id' => 0], $atts);
        $form_id = intval($atts['id']);
        $form = DockFunnels_DB::get_form_by_id($form_id);

        if (!$form) return '<p>Form not found.</p>';

        if (!is_admin()) {
            self::enqueue_scripts($atts['id']);
        }

        $fields = json_decode($form->fields, true);
        ob_start();
?>
    <div id="dock-funnels-form"></div>
<?php
        return ob_get_clean();
    }

    public static function enqueue_scripts($form_id)
    {
        wp_enqueue_style('dock-funnels-forms', plugin_dir_url(__FILE__) . '../front-end/dist/assets/forms/dock-funnels.css');
        wp_enqueue_script('dock-funnels-vue', 'https://unpkg.com/vue@3/dist/vue.global.js', [], null, true);
        wp_enqueue_script('dock-funnels-forms', plugin_dir_url(__FILE__) . '../front-end/dist/assets/forms/index.iife.js', [], null, true);

        wp_localize_script('dock-funnels-forms', 'DockFunnelsForm', [
            'formId' => $form_id,
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('dock_funnel_form_nonce')
        ]);
    }
}
