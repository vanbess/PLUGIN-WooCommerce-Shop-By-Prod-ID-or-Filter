<?php

/**
 * Registers shortcode with which to display Shop By page template and all associated JS/CSS/AJAX
 */
class SBP_Template
{

    use SBP_Register_Templates,
        SBP_Template_JS,
        SBP_Template_CSS,
        SBP_Frontend_SC,
        SBP_Colors_SC,
        SBP_Features_SC,
        SBP_Price_Slider_SC;

    /**
     * Class init function. Registers all scripts, WP AJAX functions 
     * and shortcode which will be used to display layout on the frontend.
     *
     * @return void
     */
    public static function init()
    {
        // scripts
        add_action('wp_footer', [__CLASS__, 'sbp_frontend_reg_scripts']);

        // frontend ajax filter action
        add_action('wp_ajax_sbp_frontend_ajax', [__CLASS__, 'sbp_frontend_ajax']);
        add_action('wp_ajax_nopriv_sbp_frontend_ajax', [__CLASS__, 'sbp_frontend_ajax']);

        // shortcode to display different shop by pages
        add_shortcode('sbp_shopby_display', [__CLASS__, 'sbp_frontend_display']);

        // shortcode to display color slider in sidebar
        add_shortcode('sbp_colors', [__CLASS__, 'colors']);

        // shortcode to display features list in sidebar
        add_shortcode('sbp_features', [__CLASS__, 'features']);

        // shortcode to display price slider in sidebar
        add_shortcode('sbp_price_slider', [__CLASS__, 'price_slider']);

        // register custom Flatsome page templates for selection in shop by page edit screen 
        add_filter('theme_shop-by_templates', [__CLASS__, 'sbp_register_page_templates'], 10, 4);

        // load custom page templates
        add_filter('template_include', [__CLASS__, 'sbp_load_page_templates']);
    }

    /**
     * Register required CSS and JS scripts for frontend
     *
     * @return void
     */
    public static function sbp_frontend_reg_scripts()
    {
        wp_register_script('sbp-frontend-js', self::sbp_frontend_js(), ['jquery'], false, false);
        wp_register_style('sbp-frontend-css', self::sbp_frontend_css(), [], false);
        wp_register_style('sbp-jquery-ui', SBP_URL . 'assets/jquery.ui.css', [], false);
    }

    /**
     * AJAX function which queries and returns filtered Shop By results in html format
     *
     * @return void
     */
    public static function sbp_frontend_ajax()
    {
        check_ajax_referer('sbp frontend filter query');

        print_r($_POST);

        wp_die();
    }



    /**
     * Query products based on language currently being viewed on the frontend and return array of product ids
     *
     * @param string $current_lang - Currently set Polylang language. Defaults to 'en' or English
     * @return array $prod_ids - Array of matching product IDs to be used to render frontend display of products
     */
    private static function query_products($term)
    {

        $args = [
            'limit'    => -1,
            'category' => [$term],
            'return'   => 'ids',
            'status'   => 'publish'
        ];

        $prod_ids = wc_get_products($args);

        return $prod_ids;
    }
}

SBP_Template::init();
