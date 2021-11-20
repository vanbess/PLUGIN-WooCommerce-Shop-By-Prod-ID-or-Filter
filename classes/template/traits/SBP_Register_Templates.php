<?php

/**
 * Handles registration of customized Flatsome templates for use with Shop By post type
 */
trait SBP_Register_Templates
{

    /**
     * Register Shop By post type templates
     *
     * @param  array $post_templates - array which contains post templates
     * @param  array $wp_theme - current theme name/designation, if needed
     * @param  object $post - reference to post type object
     * @param  string $post_type - reference to post type name
     * @return array $post_templates - list of registered post templates
     */
    public static function sbp_register_page_templates($post_templates, $wp_theme, $post, $post_type)
    {
        $post_templates['sbptmpl-blank-title-center.php'] = __('SBP Container Center Title', 'woocommerce');
        $post_templates['sbptmpl-full-width.php'] = __('SBP Full Width', 'woocommerce');
        $post_templates['sbptmpl-left-sidebar.php'] = __('SBP Left Sidebar', 'woocommerce');
        $post_templates['sbptmpl-right-sidebar.php'] = __('SBP Right Sidebar', 'woocommerce');

        return $post_templates;
    }

    /**
     * Attach actual template file to previously registered Sho By post type templates
     *
     * @param  path $template - path to actual template files
     * @return $template file
     */
    public static function sbp_load_page_templates($template)
    {

        // blank title center
        if (get_page_template_slug() === 'sbptmpl-blank-title-center.php') {
            $template = SBP_PATH . 'classes/template/templates/sbp-blank-title-center.php';
            return $template;
        }
        // full width
        elseif (get_page_template_slug() === 'sbptmpl-full-width.php') {
            $template = SBP_PATH . 'classes/template/templates/sbp-full-width.php';
            return $template;
        }
        // left sidebar
        elseif (get_page_template_slug() === 'sbptmpl-left-sidebar.php') {
            $template = SBP_PATH . 'classes/template/templates/sbp-left-sidebar.php';
            return $template;
        }
        // right sidebar
        elseif (get_page_template_slug() === 'sbptmpl-right-sidebar.php') {
            $template = SBP_PATH . 'classes/template/templates/sbp-right-sidebar.php';
            return $template;
        }
    }
}
