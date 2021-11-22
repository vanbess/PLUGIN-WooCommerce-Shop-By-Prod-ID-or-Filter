<?php

/**
 * Plugin Name: SBWC Shop By Page
 * Version: 1.0.0
 * Description: Allows the addition/creation of custom product features or features created from product attributes. Features are used to display filtererd product list on frontend. Created specifically for Flatsome theme.
 * Author: WC Bessinger
 */

!defined('ABSPATH') ? exit() : '';

add_action('plugins_loaded', 'sbwc_shop_by_init');

function sbwc_shop_by_init()
{
    // plugin path and url
    define('SBP_PATH', plugin_dir_path(__FILE__));
    define('SBP_URL', plugin_dir_url(__FILE__));

    /**
     * Classes and traits
     */

    // settings
    include SBP_PATH . 'classes/settings/SBP_Settings.php';

    // frontend
    include SBP_PATH . 'classes/template/traits/widget-shortcodes/SBP_Colors_SC.php';
    include SBP_PATH . 'classes/template/traits/widget-shortcodes/SBP_Features_SC.php';
    include SBP_PATH . 'classes/template/traits/widget-shortcodes/SBP_Price_Slider_SC.php';
    include SBP_PATH . 'classes/template/traits/SBP_Register_Templates.php';
    include SBP_PATH . 'classes/template/traits/SBP_Template_CSS.php';
    include SBP_PATH . 'classes/template/traits/SBP_Template_JS.php';
    include SBP_PATH . 'classes/template/traits/SBP_Frontend_SC.php';
    include SBP_PATH . 'classes/template/SBP_Template.php';

    // cpt
    include SBP_PATH . 'functions/cpt.php';
    
}
