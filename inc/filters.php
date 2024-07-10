<?php
//reset the footnote counter for every new post
function af_reset_count($content)
{
    $scope = af_get_post_scope_id();
    if (isset($GLOBALS['af_all_posts_data'][$scope])) {
        unset($GLOBALS['af_all_posts_data'][$scope]);
    }
    return $content;
}

add_filter('the_content', 'af_reset_count', 10);

function af_display_after_content($content)
{
    global $af_settings_options;

    $show_only_when_printing = FALSE;
    $hide_when_printing = FALSE;

    if (isset($af_settings_options['show_when_printing']) && $af_settings_options['show_when_printing']) {
        if (!isset($af_settings_options['display_bottom_posts']) || !$af_settings_options['display_bottom_posts']) {
            $show_only_when_printing = TRUE;
        }
    } else {
        $hide_when_printing = TRUE;
    }
    $content .= af_list_footnotes($show_only_when_printing, $hide_when_printing);


    return $content;
}


add_filter('the_content', 'af_display_after_content', 11);


function af_execute_list_shortcode($content)
{
    $content = str_replace('[af_list_execute_after_content_processed]', af_list_footnotes(), $content);
    return $content;
}
add_filter('the_content', 'af_execute_list_shortcode', 12);


function af_replace_tag_with_shortcode($content)
{
    $content = str_replace('</af>', '<af>', $content);
    $content_parts = explode('<af>', $content);
    $content_data = array();

    $inFootnote = FALSE;
    foreach ($content_parts as $c) {
        $content_data[] = array(
            "content" => $c,
            "inFootnote" => $inFootnote
        );
        $inFootnote = !$inFootnote;
    }
    $wasInFootnote = FALSE;
    for ($i = 0; $i < count($content_data); $i++) {

        $replacedString = preg_replace("/<\/?\\w+\\s?\\w?.*?>/ms", "", $content_data[$i]['content']);
        if (strlen($replacedString) === 0 && !$content_data[$i]['inFootnote'] && $wasInFootnote) {
            $content_data[$i]['inFootnote'] = TRUE;
        } else {
            $wasInFootnote = $content_data[$i]['inFootnote'];
        }
    }
    $final_content = '';
    $inFootnote = FALSE;
    foreach ($content_data as $cd) {
        if ($cd['inFootnote'] && !$inFootnote) {
            $inFootnote = TRUE;
            $final_content .= '[af]';
        } else if ($inFootnote && !$cd['inFootnote']) {
            $inFootnote = FALSE;
            $final_content .= '[/af]';
        }

        $final_content .= $cd['content'];
    }
    if ($inFootnote) {
        $final_content .= '[/af]';
    }
    return $final_content;
}
add_filter('the_content', 'af_replace_tag_with_shortcode');



function af_strip_rendered_tag($content)
{

    global $af_all_posts_data;
    $scope_id = af_get_post_scope_id();

    if (empty($af_all_posts_data[$scope_id])) {
        return $content;
    }

    $footnotes_used = [];
    if (isset($af_all_posts_data[$scope_id]['af_previously_used'])) {
        foreach ($af_all_posts_data[$scope_id]['af_previously_used'] as $f) {
            $footnotes_used[] = $f;
        }
    }
    $footnotes_used[] = $af_all_posts_data[$scope_id]['footnotes'];

    foreach ($footnotes_used as $footnote_list) {
        foreach ($footnote_list as $index => $footnote_content) {
            if (!empty($footnote_content)) {
                $content = str_replace($index . wp_strip_all_tags($footnote_content), '', $content);
            }
        }
    }

    return $content;
}
add_filter('wp_trim_words', 'af_strip_rendered_tag');


