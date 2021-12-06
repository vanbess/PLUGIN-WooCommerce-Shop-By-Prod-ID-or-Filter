<?php

/**
 * Registers shortcode with which to display Shop By page template and all associated JS/CSS/AJAX
 */
class SBP_Template
{

    /**
     * Traits
     */
    use SBP_Template_JS,
        SBP_Template_CSS,
        SBP_Frontend_SC,
        SBP_Prod_Select_Metabox;

    /**
     * Class init function. Registers all scripts, WP AJAX functions 
     * and shortcode which will be used to display layout on the frontend.
     *
     * @return void
     */
    public static function init()
    {

        // insert default shop by pages
        add_action('admin_head', [__CLASS__, 'insert_default_pages']);

         // register ajax action for saving sbp products
         add_action('wp_ajax_sbp_backend_save_prods', [__CLASS__, 'sbp_backend_save_prods']);
         add_action('wp_ajax_nopriv_sbp_backend_save_prods', [__CLASS__, 'sbp_backend_save_prods']);

        // scripts
        add_action('wp_footer', [__CLASS__, 'sbp_frontend_reg_scripts']);

        // shortcode to display different shop by pages
        add_shortcode('sbp_shopby_display', [__CLASS__, 'sbp_frontend_display']);

        // load/filter page template
        add_filter('single_template', [__CLASS__, 'sbp_load_page_template']);

        // add custom shop by metabox
        add_action('add_meta_boxes', [__CLASS__, 'sbp_metabox_args']);
    }

    /**
     * Loads custom template for our custom post type
     *
     * @param  string $template
     * @return string $template
     */
    public static function sbp_load_page_template($template)
    {
        global $post;

        if ($post->post_type == "shop-by" && $template !== locate_template(array("sbp-left-sidebar.php"))) {
            return SBP_PATH . "classes/template/templates/sbp-left-sidebar.php";
        }

        return $template;
    }

    /**
     * Register required CSS and JS scripts for frontend
     *
     * @return void
     */
    public static function sbp_frontend_reg_scripts()
    {
        // JS
        wp_register_script('sbp-frontend-js', self::sbp_frontend_js(), ['jquery'], false, false);

        // CSS
        wp_register_style('sbp-frontend-css', self::sbp_frontend_css(), [], false);
        wp_register_style('sbp-jquery-ui', SBP_URL . 'assets/jquery.ui.css', [], false);
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

    public static function sbp_metabox_args()
    {
        add_meta_box(
            'sbp-product-select',
            __('Select Shop-By Products', 'woocommerce'),
            [__CLASS__, 'sbp_metabox_html'],
            'shop-by',
            'advanced',
            'high'
        );
    }
}

SBP_Template::init();
