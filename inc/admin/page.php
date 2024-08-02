<?php
function af_menu()
{
    add_options_page(
        __('Amebae Footnotes Settings', 'footnotes'),
        __('Amebae Footnotes', 'footnotes'),
        'manage_options',
        __FILE__,
        'af_footnotes_options'
    );
}

function af_footnotes_options()
{
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    echo '<div class="wrap">';
    echo '<h2>Instructions</h2>';
    echo '<p>Use a footnote in your post by using the footnote icon in the WordPress editor or by using the shortcode: <code><b>[fn]this will be a footnote[/fn]</b></code></br> The plugin will automatically associate sequential numbers with each plugin.</p>';
    echo '<p>On desktop, footnotes will appear as a tooltip when the user clicks on the number. On mobile, footnotes will expand as a section below the current text.</p>';
    echo '<p>You can also use the <code><b>[fn_list]</b></code> shortcode to display a list of footnotes used in the article.</p>';
    echo '<h3>Shortcodes</h3>';
    echo '<p>You can modify some behaviours or styles of your footnotes by using the following options within our shortcode.</p>';
    echo '<p><code><b>[fn]This footnote will automatically numbered[/fn]</b></code></p>';
    echo '<p><code><b>[fn class=’my-custom-class’]This footnote will have ‘my-custom-class’ as additional class, allowing for custom styling of individual footnotes.[/fn]</b></code></p>';
    echo '<p><code><b>[fn referencereset=’true’]This footnote will reset the footnote counter and therfore receive 1 as its number. Following footnotes will also receive their number according to this new start.[/fn]</b></code></p>';
    // echo '<form method="post" action="options.php">';
    // settings_fields('af_settings_options');
    // do_settings_sections(__FILE__);
    // submit_button();
    // echo '</form>';
    echo '</div>';
}


function af_register_settings()
{
    register_setting(
        'af_settings_options',
        'af_settings_options',
        array(
            'type' => 'boolean',
            'default' => FALSE,
            'sanitize_callback' => 'af_sanitize_callback'
        )
    );
    add_settings_section(
        'af_option_group_section',
        __('Options', 'footnotes'),
        function () {
        },
        __FILE__
    );

    add_settings_field(
        'desktop_behavior',
        __('Desktop behavior', 'footnotes'),
        'af_desktop_footnote_behavior_dropdown_callback',
        __FILE__,
        'af_option_group_section'
    );

    add_settings_field(
        'show_tooltip_when_hover',
        __('Show browser tooltip on hover', 'footnotes'),
        'af_checkbox_element_callback',
        __FILE__,
        'af_option_group_section',
        array(
            'property_name' => 'show_tooltip_when_hover',
            'property_label' => 'Make footnote content appear in web browser\'s native tooltip when hovering over footnote number'
        )
    );

    add_settings_field(
        'display_bottom_posts',
        __('Display footnote list at bottom of posts', 'footnotes'),
        'af_checkbox_element_callback',
        __FILE__,
        'af_option_group_section',
        array(
            'property_name' => 'display_bottom_posts',
            'property_label' => 'Display footnote list at bottom of posts'
        )
    );
    add_settings_field(
        'show_when_printing',
        __('When printing, list footnotes at the bottom of posts', 'footnotes'),
        'af_checkbox_element_callback',
        __FILE__,
        'af_option_group_section',
        array(
            'property_name' => 'show_when_printing',
            'property_label' => 'When printing, list footnotes at the bottom of posts'
        )
    );

    add_settings_field(
        'af_heading_footnote_list',
        __('Heading for footnote list', 'footnotes'),
        'af_textbox_element_callback',
        __FILE__,
        'af_option_group_section',
        array(
            'property_name' => 'af_heading_footnote_list',
            'property_label' => 'If provided, this text will be displayed above footnote lists'
        )
    );

    add_settings_field(
        'af_heading_tag_name_footnote_list',
        __('Heading tag name for footnote list', 'footnotes'),
        'af_tag_name_for_footnote_list_dropdown_callback',
        __FILE__,
        'af_option_group_section'
    );
}




function af_sanitize_callback($plugin_options)
{
    global $af_settings_options;

    if (isset($plugin_options['af_custom_css']) && !empty($plugin_options['af_custom_css'])) {
        //strip style HTML tags from the custom CSS property
        $plugin_options['af_custom_css'] = preg_replace('/<\/?style.*?>/i', '', $plugin_options['af_custom_css']);
    }

    if (isset($plugin_options['af_custom_shortcode']) && !empty($plugin_options['af_custom_shortcode'])) {
        //remove invalid characters from shortcode
        $plugin_options['af_custom_shortcode'] = preg_replace('/[^a-zA-Z0-9-_]/i', '', $plugin_options['af_custom_shortcode']);
        if ((!isset($af_settings_options['af_custom_shortcode']) || $af_settings_options['af_custom_shortcode'] != $plugin_options['af_custom_shortcode']) &&
            shortcode_exists($plugin_options['af_custom_shortcode'])
        ) {
            add_settings_error('af_custom_shortcode', 'shortcode-in-use', 'The shortcode "' . $plugin_options['af_custom_shortcode'] . '" is already in use, please enter a different one');
            $plugin_options['af_custom_shortcode'] = '';
        }
    }
    return $plugin_options;
}


function af_checkbox_element_callback($args)
{
    global $af_settings_options;

    $property_name = $args['property_name'];
    $property_label = $args['property_label'];

    $html = '<input type="checkbox" id="%1$s" name="af_settings_options[%1$s]" value="1"' . checked(1, isset($af_settings_options[$property_name]) && $af_settings_options[$property_name], FALSE) . '/>';
    $html .= '<label for="%1$s">' .
        esc_html__($property_label, 'footnotes') .
        '</label>';
    $html = sprintf($html, $property_name);

    echo $html;
}


function af_textbox_element_callback($args)
{
    global $af_settings_options;

    $property_name = $args['property_name'];
    $property_label = $args['property_label'];

    $html = '<input type="text" id="%1$s" name="af_settings_options[%1$s]" value="%2$s" />';
    $html .= ' <label for="%1$s">' .
        esc_html__($property_label, 'footnotes') .
        '</label>';
    $html = sprintf($html, $property_name, isset($af_settings_options[$property_name]) ? esc_attr($af_settings_options[$property_name]) : '');

    echo $html;
}


function af_desktop_footnote_behavior_dropdown_callback()
{
    global $af_settings_options;

    $selected_value = isset($af_settings_options['desktop_behavior']) ? $af_settings_options['desktop_behavior'] : '';

    $options = array(
        'tooltip_click' => __('Tooltip footnotes that open on click', 'footnotes'),
        'tooltip_hover' => __('Tooltip footnotes that open on hover', 'footnotes'),
        'expandable' => __('Expandable footnotes', 'modern-footnotes'),
    );

    $html = '<select id="af_desktop_behavior" name="af_settings_options[desktop_behavior]"> aria-label="%1$s"';
    foreach ($options as $key => $value) {
        $option_html = '<option value="%s" %s>%s</option>';
        $html .= sprintf($option_html, esc_attr($key), $selected_value == $key ? 'selected' : '', esc_html($value));
    }
    $html .= '</select>';

    $html = sprintf($html, __('Desktop footnote behavior', 'footnotes'));

    echo $html;
}


function af_tag_name_for_footnote_list_dropdown_callback()
{
    global $af_settings_options;

    $selected_value = isset($af_settings_options['af_heading_tag_name_footnote_list']) ? $af_settings_options['af_heading_tag_name_footnote_list'] : 'h3';

    $options = array(
        'h2' => __('Heading 2', 'footnotes'),
        'h3' => __('Heading 3', 'footnotes'),
        'h4' => __('Heading 4', 'footnotes'),
        'h5' => __('Heading 5', 'footnotes'),
        'h6' => __('Heading 6', 'footnotes')
    );

    $html = '<select id="af_af_heading_tag_name_list" name="af_settings_options[af_heading_tag_name_footnote_list]"> aria-label="%1$s"';
    foreach ($options as $key => $value) {
        $option_html = '<option value="%s" %s>%s</option>';
        $html .= sprintf($option_html, esc_attr($key), $selected_value == $key ? 'selected' : '', esc_html($value));
    }
    $html .= '</select>';

    $html = sprintf($html, __('Heading tag name for footnote list', 'footnotes'));

    echo $html;
}

function af_add_container_button()
{
    if (!current_user_can('edit_posts') && !current_user_can('edit_pages'))
        return;
    if (get_user_option('rich_editing') == 'true') {
        add_filter('mce_external_plugins', 'af_add_container_plugin');
        add_filter('mce_buttons', 'af_register_container_button');
    }
}
if (is_admin()) {
    add_filter('init', 'af_add_container_button');

    function af_enqueue_admin_scripts()
    {
        wp_enqueue_style('af_footnotes', plugin_dir_url(__FILE__) . '/mce-button.css', array(), '1.0.0');
    }

    add_action('admin_enqueue_scripts', 'af_enqueue_admin_scripts');
}


function af_register_container_button($buttons)
{
    array_push($buttons, "af_footnotes");
    return $buttons;
}

function af_add_container_plugin($plugin_array)
{
    $plugin_array['af_footnotes'] = plugin_dir_url(__FILE__) . '/mce-button.js';
    return $plugin_array;
}


if (is_admin()) { // admin actions
    add_action('admin_menu', 'af_menu');
    add_action('admin_init', 'af_register_settings');
}
