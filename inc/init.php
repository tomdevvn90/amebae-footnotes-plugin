<?php

$af_settings_options = get_option('af_settings_options');

$af_all_posts_data = [];

$af_current_post_number = 0;

$af_shortcodes = ['fn_footnote', 'fn'];

// set default options, if they have not been set
if ($af_settings_options === FALSE) {
    $af_settings_options = [];
}


if (
    get_option('expandable_footnotes_desktop') === FALSE &&
    !isset($af_settings_options['desktop_behavior']) &&
    isset($af_settings_options['use_expandable_desktop']) &&
    $af_settings_options['use_expandable_desktop']
) {
    $af_settings_options['desktop_behavior'] = 'expandable';
    update_option('expandable_desktop', 1);
    update_option('af_settings_options', $af_settings_options);
}
