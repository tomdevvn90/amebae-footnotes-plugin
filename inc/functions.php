<?php

/*  
** enqueue scripts styles if not already enqueued
*/
function af_enqueue_scripts_styles()
{
    if (!wp_style_is('af_styles')) {
        wp_enqueue_style('af_styles');
    }
    if (!wp_script_is('af_scripts')) {
        wp_enqueue_script('af_manifest');
        wp_enqueue_script('af_vendor');
        wp_enqueue_script('af_scripts');
    }
}


function af_footnotes_func($atts, $content = "")
{

    global $af_all_posts_data, $af_settings_options;

    af_enqueue_scripts_styles();

    $additional_classes = '';
    if (
        (isset($af_settings_options['desktop_behavior']) &&  $af_settings_options['desktop_behavior'] == 'expandable')
        || (
            !isset($af_settings_options['desktop_behavior']) &&
            isset($af_settings_options['use_expandable_desktop']) &&
            $af_settings_options['use_expandable_desktop']
        )
    ) {
        $additional_classes .= 'af-footnote--expands-on-desktop ';
    } else if (
        isset($af_settings_options['desktop_behavior']) &&
        $af_settings_options['desktop_behavior'] == 'tooltip_hover'
    ) {
        $additional_classes .= 'af-footnote--hover-on-desktop ';
    }


    if (isset($atts['class'])) {
        $additional_classes .= esc_attr($atts['class']) . ' ';
    }


    $scope_id = af_get_post_scope_id();

    $additional_attributes = '';

    if (isset($atts['referencereset']) && $atts['referencereset'] == 'true') {
        if (isset($af_all_posts_data[$scope_id])) {
            $af_all_posts_data[$scope_id]['used_reference_numbers'] = array();
            $additional_attributes .= ' data-af-reset';
            // store the content of previously used footnotes, in case we are reusing a reference number and we 
            // are also using a list of footnotes. 
            if (!isset($af_all_posts_data[$scope_id]['previously_used'])) {
                $af_all_posts_data[$scope_id]['previously_used'] = array($af_all_posts_data[$scope_id]['footnotes']);
            } else {
                $af_all_posts_data[$scope_id]['previously_used'][] = $af_all_posts_data[$scope_id]['footnotes'];
            }
            $af_all_posts_data[$scope_id]['footnotes'] = [];
        }
    }

    if (isset($atts['referencenumber'])) {
        $display_number = $atts['referencenumber'];
        $additional_attributes = 'refnum="' . esc_attr($display_number) . '"';
    } else if (!isset($af_all_posts_data[$scope_id]) || count($af_all_posts_data[$scope_id]['used_reference_numbers']) == 0) {
        $display_number = 1;
    } else {
        $display_number = max($af_all_posts_data[$scope_id]['used_reference_numbers']) + 1;
    }

    $content = do_shortcode($content); // render out any shortcodes within the contents

    $content = str_replace('<p>', '', $content);
    $content = str_replace('</p>', '<br /><br />', $content);

    if (isset($af_settings_options['show_tooltip_when_hover']) && $af_settings_options['show_tooltip_when_hover']) {
        $additional_attributes .= ' title="' . str_replace('"', '&quot;', strip_tags($content)) . '" ';
    }

    if (!isset($af_all_posts_data[$scope_id])) {
        $af_all_posts_data[$scope_id] = array(
            'af_post_number' => $GLOBALS['af_current_post_number'],
            'used_reference_numbers' => array($display_number),
            'footnotes' => array(
                $display_number => $content
            )
        );
        $GLOBALS['af_current_post_number']++;
    } else {
        if (is_numeric($display_number)) {
            $af_all_posts_data[$scope_id]['used_reference_numbers'][] = $display_number;
        }
        $af_all_posts_data[$scope_id]['footnotes'][$display_number] = $content;
    }


    $content_id = "af-content-" . $scope_id . '-' . preg_replace('/[^a-zA-Z0-9-_]/i', '', esc_attr($display_number));
    
    if (isset($atts['for_rss_feed']) && $atts['for_rss_feed']) {
        $content = '<sup class="af-footnote ' . $additional_classes . '">' . esc_html($display_number) . '</sup>'; // only display the superscript for RSS feeds
    } else {
        $content = '<sup class="af-footnote ' . $additional_classes . '" data-af="' . str_replace('"', "\\\"", esc_attr($display_number)) . '" data-af-post-scope="' . $scope_id . '">' .
            '<a href="javascript:void(0)" ' . $additional_attributes . ' role="button" aria-pressed="false" aria-describedby="' . $content_id . '">' . $display_number . '</a>' .
            '</sup>' .
            '<span id="' . $content_id . '" role="tooltip" class="af-footnote__note" tabindex="0" data-af="' . str_replace('"', "\\\"", $display_number) . '">' . $content . '</span>';
    }

    return $content;
}


function af_list_footnotes($show_only_when_printing = FALSE, $hide_when_printing = FALSE)
{
    global $af_all_posts_data, $af_settings_options;
    $scope_id = af_get_post_scope_id();

    if (empty($af_all_posts_data[$scope_id])) {
        return '';
    }

    $footnotes_used = [];

    if (isset($af_all_posts_data[$scope_id]['af_previously_used'])) {
        foreach ($af_all_posts_data[$scope_id]['af_previously_used'] as $value) {
            $footnotes_used[] = $value;
        }
    }
    $footnotes_used[] = $af_all_posts_data[$scope_id]['footnotes'];

    $content = '';

    if (isset($af_settings_options['af_heading_footnote_list']) && strlen($af_settings_options['af_heading_footnote_list']) > 0) {
        $tag_name = isset($af_settings_options['af_heading_tag_name_footnote_list']) ? $af_settings_options['af_heading_tag_name_footnote_list'] : 'h3';
        $content .= '<' . $tag_name . ' class="af-list-heading ' .
            ($show_only_when_printing ? 'af-list-heading--show-only-for-print' : '') .
            ($hide_when_printing ? 'af-list-heading--hide-for-print' : '')
            . '">' . $af_settings_options['af_heading_footnote_list'] . '</' . $tag_name . '>';
    }

    $content .= '<h4>References:</h4><ul class="af-list ' . ($show_only_when_printing ? 'af-list--show-only-for-print' : '') . ($hide_when_printing ? 'af-list--hide-for-print' : '') . '">';

    foreach ($footnotes_used as $footnote_list) {
        foreach ($footnote_list as $index => $f_content) {
            $content .= '<li>';
            $content .= '<sup>' . $index . ' </sup>';
            $content .= '<div>';
            $content .= $f_content;
            $content .= '</div>';
            $content .= '</li>';
        }
    }
    $content .= '</ul>';

    return $content;
}


function af_get_post_scope_id()
{
    if (isset($GLOBALS['post'])) {
        $global_post = $GLOBALS['post'];

        if (is_object($global_post)) {
            if (property_exists($global_post, 'ID')) {
                $global_post_id = $global_post->ID;
            } else {
                $global_post_id = spl_object_hash($global_post);
            }
        } else {
            $global_post_id = $global_post;
        }
        if (isset($GLOBALS['af_active_query'])) {
            return spl_object_hash($GLOBALS['af_active_query']) . '_' . $global_post_id;
        } else {
            return 'post_' . $global_post_id;
        }
    } else {
        return 'na';
    }
}
