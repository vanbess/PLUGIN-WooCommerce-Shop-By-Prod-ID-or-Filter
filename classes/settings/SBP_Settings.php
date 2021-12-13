<?php

/**
 * Renders settings page
 */

class SBP_Settings
{

    /**
     * Class init
     *
     * @return void
     */
    public static function init()
    {

        // admin scripts
        add_action('admin_footer', [__CLASS__, 'scripts']);

        // admin srttings ajax
        add_action('wp_ajax_sbp_admin_ajax', [__CLASS__, 'sbp_admin_ajax']);
        add_action('wp_ajax_nopriv_sbp_admin_ajax', [__CLASS__, 'sbp_admin_ajax']);

        // product edit screen metabox ajax
        add_action('wp_ajax_sbp_product_ajax', [__CLASS__, 'sbp_product_ajax']);
        add_action('wp_ajax_nopriv_sbp_product_ajax', [__CLASS__, 'sbp_product_ajax']);

        // admin menu
        add_action('admin_menu', [__CLASS__, 'sbp_admin_page']);

        // product meta box
        add_action('add_meta_boxes', [__CLASS__, 'sbp_metabox']);
    }

    /**
     * CSS and JS
     *
     * @return void
     */
    public static function scripts()
    {
        wp_enqueue_script('jquery');
        wp_register_script('sbp-js', self::admin_js(), ['jquery'], false, true);
        wp_register_style('sbp-css', self::admin_css(), [], false, 'all');
        wp_register_script('sbp-prod-js', self::sbp_prod_js(), ['jquery'], false, true);
        wp_register_style('sbp-prod-css', self::sbp_prod_css(), [], false, 'all');
    }

    public static function sbp_metabox()
    {
        add_meta_box('sbp-metabox', __('Shob By Terms', 'woocommerce'), [__CLASS__, 'sbp_render_metabox'], 'product', 'side', 'high', null);
    }

    /**
     * Renders product screen shop by metabox with buttons to select shob by attributes
     *
     * @return void
     */
    public static function sbp_render_metabox()
    {
        global $post;

        // retrieve global and product attributes
        $to_select = array_filter(maybe_unserialize(get_option('sbp_attribs')));
        $prod_attribs = array_filter(maybe_unserialize(get_post_meta($post->ID, 'sbp_attribs', true)));
?>

        <p style="margin-bottom: 0px;"><?php _e('Click to select all attributes you would like this product to show up for on the Shop By page.', 'woocommerce'); ?></p>

        <?php
        // if global attributes present, display buttons with check for existing product attribs, else display error
        if ($to_select) :
            foreach ($to_select as $key => $attrib) :
                if (in_array($attrib, $prod_attribs)) : ?>
                    <button class='button button-secondary button-primary sbp-prod-attr-select' data-attrib="<?php echo $attrib; ?>" title="<?php _e('click to select attribute', 'woocommerce'); ?>">
                        <?php echo $attrib; ?>
                    </button>
                <?php else : ?>
                    <button class='button button-secondary sbp-prod-attr-select' data-attrib="<?php echo $attrib; ?>" title="<?php _e('click to select attribute', 'woocommerce'); ?>">
                        <?php echo $attrib; ?>
                    </button>
        <?php endif;
            endforeach;
        else :
            _e('Shop By attributes have not been defined. Please navigate to Products -> Shop By Settings to add some attributes.', 'woocommerce');
        endif; ?>
        <hr>
        <div class="sbp-save-attribs">
            <button class="button button-primary sbp-save-prod-attribs" data-nonce="<?php echo wp_create_nonce('save product sbp attributes'); ?>">
                <?php _e('Save Shop By Terms', 'woocommerce'); ?>
            </button>
        </div>

    <?php
        // js and css
        wp_enqueue_script('sbp-prod-js');
        wp_enqueue_style('sbp-prod-css');
    }

    /**
     * CSS for product edit screen
     *
     * @return void
     */
    public static function sbp_prod_css()
    { ?>

        <style>
            button.button.button-secondary.sbp-prod-attr-select,
            button.button.button-primary.sbp-prod-attr-select {
                margin-top: 10px;
            }

            button.button.button-primary.sbp-save-prod-attribs {
                width: 100%;
                font-size: 14px;
            }
        </style>

    <?php }

    /**
     * JS for product edit screen
     *
     * @return void
     */
    public static function sbp_prod_js()
    {
        global $post;
    ?>

        <script>
            jQuery(document).ready(function() {

                // vars
                let selected = [];

                // shob by attribute selection
                $('.sbp-prod-attr-select').on('click', function(e) {
                    e.preventDefault();
                    $(this).toggleClass('button-primary');
                });

                // save shop by attributes/terms
                $('.sbp-save-prod-attribs').on('click', function(e) {
                    e.preventDefault();

                    // grab nonce
                    let nonce = $(this).data('nonce');

                    // push selected attribs to selected array via loop
                    $('.sbp-prod-attr-select').each(function(index, element) {
                        if ($(this).hasClass('button-primary')) {
                            selected.push($(this).data('attrib'));
                        }
                    });

                    // if selected not empty, save, else display error message
                    if (selected.length > 0) {

                        var data = {
                            '_ajax_nonce': nonce,
                            'action': 'sbp_product_ajax',
                            'attribs': selected,
                            'prod_id': <?php echo $post->ID; ?>
                        }
                        $.post(ajaxurl, data, function(response) {
                            alert(response);
                            location.reload();
                        });

                    } else {
                        alert('<?php _e('Please select at least one Shop By attribute before attempting to save.', 'woocommerce'); ?>');
                    }
                });
            });
        </script>

    <?php }

    /**
     * Product sell by attributes ajax function
     *
     * @return void
     */
    public static function sbp_product_ajax()
    {

        check_ajax_referer('save product sbp attributes');

        $prod_attribs = $_POST['attribs'];

        $saved = update_post_meta($_POST['prod_id'], 'sbp_attribs', maybe_serialize($prod_attribs));

        if ($saved) {
            wp_send_json(__('Product Shop By attributes saved successfully.', 'woocommerce'));
        }

        wp_die();
    }

    /**
     * Query and return all product attributes
     *
     * @return array $attrib_data - array of attributes
     */
    public static function query_product_attribs()
    {
        // retrieve product attribute taxonomies
        $attrib_query = wc_get_attribute_taxonomies();

        // array to hold attribute names
        $attrib_names = [];

        // loop through attribute query and push attribute names to $attrib_names
        foreach ($attrib_query as $tax => $obj) :
            $attrib_names[] = $obj->attribute_name;
        endforeach;

        $attrib_data = [];

        // loop through attrib names array, query terms for each, and attach to matching tax names
        foreach ($attrib_names as $key => $name) :

            $pa_name = 'pa_' . $name;

            $terms = get_terms(['taxonomy' => $pa_name]);

            foreach ($terms as $term => $t_obj) :
                $attrib_data[$name][] = $t_obj->name;
            endforeach;

        endforeach;

        return $attrib_data;
    }

    /**
     * Admin page registration function
     *
     * @return void
     */
    public static function sbp_admin_page()
    {
        add_submenu_page('edit.php?post_type=shop-by', __('Shop By Settings', 'woocommerce'), __('Shop By Settings', 'woocommerce'), 'manage_options', 'sbp-settings', [__CLASS__, 'sbp_admin_page_content']);
    }

    /**
     * Admin page HTML rendering function
     *
     * @return void
     */
    public static function sbp_admin_page_content()
    {
        global $title;
    ?>
        <div class="wrap woocommerce">

            <h1><?php echo $title; ?></h1>
            <p>
                <b><?php _e('Use the fields below to add a list of custom Shop By attributes. <br>Attributes specified below will be available for selection in product edit screens. <br><u>NOTE:</u> Be sure to add unique attributes below which do not match existing attributes as defined under Products -> Attributes, since thse attributes are used by default for the filtering process.', 'woocommerce'); ?></b>
            </p>

            <!-- attribute input/add/remove wrap -->
            <div class="sbp-attributes-wrap">
                <!-- existing shop by attributes -->
                <?php
                if (get_option('sbp_attribs')) {
                    foreach (maybe_unserialize(get_option('sbp_attribs')) as $key => $val) : ?>
                        <div class="sbp-attrib-inner-wrap">
                            <input type="text" name="sbp-attribute[]" class="sbp-attribute" value="<?php echo $val; ?>">
                            <button class="sbp-attrib-add button button-primary" title="<?php _e('add attribute', 'woocommerce'); ?>">+</button>
                            <button class="sbp-attrib-rem button button-secondary" title="<?php _e('remove attribute', 'woocommerce'); ?>">-</button>
                        </div>
                <?php endforeach;
                }
                ?>

                <div class="sbp-attrib-inner-wrap">
                    <input type="text" name="sbp-attribute[]" class="sbp-attribute" placeholder="<?php _e('attribute name', 'woocommerce'); ?>">
                    <button class="sbp-attrib-add button button-primary" title="<?php _e('add attribute', 'woocommerce'); ?>">+</button>
                    <button class="sbp-attrib-rem button button-secondary" title="<?php _e('remove attribute', 'woocommerce'); ?>">-</button>
                </div>
            </div>

            <br>

            <!-- save list of attributes -->
            <div id="sbp-attributes-save">
                <button class="button button-primary" data-nonce="<?php echo wp_create_nonce('save sbp attributes') ?>"><?php _e('Save attributes', 'woocommerce'); ?></button>
            </div>

        </div>

    <?php }

    /**
     * Admin page CSS function
     *
     * @return void
     */
    public static function admin_css()
    { ?>
        <style>
            button.sbp-attrib-rem.button.button-secondary {
                background: red;
                border-color: red;
                color: white;
                min-width: 28px;
            }

            .sbp-attrib-inner-wrap {
                padding-bottom: 15px;
            }

            div#sbp-existing-attribs button {
                margin-bottom: 5px;
            }

            div#sbp-existing-attribs {
                padding-bottom: 15px;
            }

            .sbp-prod-attr-select {
                margin-top: 10px;
            }
        </style>
    <?php }

    /**
     * Admin page JS function
     *
     * @return void
     */
    public static function admin_js()
    { ?>
        <script>
            jQuery(document).ready(function($) {

                // ************************************************************
                // insert/remove additional inputs on add/remove buttons click
                // ************************************************************
                var input_html, target;

                input_html = '<div class="sbp-attrib-inner-wrap">';
                input_html += '<input type="text" name="sbp-attribute[]" class="sbp-attribute" placeholder="attribute name"> ';
                input_html += '<button class="sbp-attrib-add button button-primary" title="add attribute">+</button> ';
                input_html += '<button class="sbp-attrib-rem button button-secondary" title="remove attribute">-</button>';
                input_html += '</div>';

                target = $('.sbp-attributes-wrap');

                // add attribute input
                $(document).on('click', '.sbp-attrib-add', function(e) {
                    e.preventDefault();
                    target.append(input_html);
                });

                // remove attribute input
                $(document).on('click', '.sbp-attrib-rem', function(e) {
                    e.preventDefault();
                    $(this).parent().remove();
                });

                // ************************
                // save shop by attributes
                // ************************
                $('#sbp-attributes-save button').on('click', function(e) {
                    e.preventDefault();

                    // variables
                    let nonce = $(this).data('nonce');
                    let attribs = [];

                    // grab attributes
                    $('.sbp-attribute').each(function(index, element) {
                        if ($(this).val()) {
                            attribs.push($(this).val());
                        }
                    });

                    // send ajax request if attribs present
                    if (attribs.length > 0) {
                        var data = {
                            '_ajax_nonce': nonce,
                            'action': 'sbp_admin_ajax',
                            'attribs': attribs
                        }
                        $.post(ajaxurl, data, function(response) {
                            alert(response);
                        });
                    } else {
                        alert('<?php _e('Please provide at least one Shop By attribute before attempting to save.', 'woocommerce'); ?>');
                    }

                });

            });
        </script>
<?php }

    /**
     * Admin settings ajax function
     *
     * @return void
     */
    public static function sbp_admin_ajax()
    {

        check_ajax_referer('save sbp attributes');

        $attribs = $_POST['attribs'];

        $saved = update_option('sbp_attribs', maybe_serialize($attribs));

        if ($saved) {
            wp_send_json(__('Shop By attributes saved successfully.', 'woocommerce'));
        }

        wp_die();
    }
}

SBP_Settings::init();
