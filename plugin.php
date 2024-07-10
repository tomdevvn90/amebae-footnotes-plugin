<?php
/*
Plugin Name: Amebae Footnotes
Plugin URI:  http://amebae.com.au/
Text Domain: footnotes
Description: Add inline footnotes to your post via the footnote icon on the toolbar for editing posts and pages.
Version:     1.0.0
Author:      Amebae
Author URI:  http://amebae.com.au/
*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

if (!defined('PLUGIN_DIR_URL')) {
    define('PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
}

if (!defined('PLUGIN_DIR_PATH')) {
    define('PLUGIN_DIR_PATH', plugin_dir_path(__FILE__)); 
}

if (!defined('VERSION')) {
    define('VERSION', '1.0.0');
}

/**
 * Initializer.
 */

require_once PLUGIN_DIR_PATH . 'inc/load-scripts.php';
require_once PLUGIN_DIR_PATH . 'inc/admin/page.php';
require_once PLUGIN_DIR_PATH . 'inc/init.php';
require_once PLUGIN_DIR_PATH . 'inc/filters.php';
require_once PLUGIN_DIR_PATH . 'inc/hooks.php';
require_once PLUGIN_DIR_PATH . 'inc/functions.php';
require_once PLUGIN_DIR_PATH . 'inc/shortcodes.php';