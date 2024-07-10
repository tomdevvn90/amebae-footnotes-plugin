<?php

function af_block_editor_button()
{
    $currentScreen = get_current_screen();
    if ($currentScreen->id === "widgets") {
        return;
    }

    wp_enqueue_script(
        'af_block_editor_js',
        PLUGIN_DIR_URL . 'assets/js/block-editor.js',
        ['wp-rich-text', 'wp-element', 'wp-block-editor', 'wp-i18n'],
        VERSION
    );

    wp_set_script_translations('af_block_editor_js', 'footnotes');
    wp_enqueue_style('af_block_editor_css', PLUGIN_DIR_URL . 'dist/css/block-editor-button.css', [], VERSION);
}
add_action('enqueue_block_editor_assets', 'af_block_editor_button');



function af_register_scripts_styles()
{
    global $af_shortcodes, $post;
    
    wp_register_style('af_styles', PLUGIN_DIR_URL . 'dist/css/styles.css', [], VERSION);
    wp_register_script('af_manifest', PLUGIN_DIR_URL . 'dist/js/manifest.js', ['jquery'], VERSION, TRUE);
    wp_register_script('af_vendor', PLUGIN_DIR_URL . 'dist/js/vendor.js', ['jquery'], VERSION, TRUE);
    wp_register_script('af_scripts', PLUGIN_DIR_URL . 'dist/js/app.js', ['jquery'], VERSION, TRUE);

    if (is_a($post, 'WP_Post')) {
        $has_shortcode = FALSE;        
        if (isset($af_shortcodes)) {
            foreach ($af_shortcodes as $af_shortcode) {
                if (has_shortcode($post->post_content, $af_shortcode)) {
                    $has_shortcode = TRUE;
                }
            }
        }
        if (has_shortcode($post->post_content, 'af_list')) {
            $has_shortcode = TRUE;
        }

   
        if ($has_shortcode) {
            wp_enqueue_style('af_styles');
            wp_enqueue_script('af_manifest');
            wp_enqueue_script('af_vendor');
            wp_enqueue_script('af_scripts');
        }
    }
}

add_action('wp_enqueue_scripts', 'af_register_scripts_styles');
