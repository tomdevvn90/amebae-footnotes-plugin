<?php

function af_list_func($atts = [], $content = "")
{
    af_enqueue_scripts_styles();
    return '[af_list_execute_after_content_processed]';
}

add_shortcode('fn_list', 'af_list_func');


function af_rss_func($atts, $content = "")
{
    if (!is_array($atts)) {
        $atts = [];
    }
    $atts['for_rss_feed'] = TRUE;
    return af_footnotes_func($atts, $content);
}

global $af_shortcodes;
if (isset($af_settings_options['af_custom_shortcode']) && !empty($af_settings_options['af_custom_shortcode'])) {
    $af_settings_options[] = $af_settings_options['af_custom_shortcode'];
}
foreach ($af_shortcodes as $af_shortcode) {
    add_shortcode($af_shortcode, 'af_footnotes_func');
}
