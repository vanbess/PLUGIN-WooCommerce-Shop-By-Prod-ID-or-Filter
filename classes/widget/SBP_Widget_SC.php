<?php

/**
 * Renders widget HTML and associated functionality
 */

class SBP_Widget_SC
{

    /**
     * Traits
     */
    use SBP_Colors,
        SBP_Features,
        SBP_Price_Slider,
        SB_Filtered_Prod_HTML;

    /**
     * Init class
     */
    public static function init()
    {

        // js
        add_action('wp_footer', [__CLASS__, 'sbp_filter_js']);

        // ajax
        add_action('wp_ajax_sbp_filter_products', [__CLASS__, 'sbp_filter_products']);
        add_action('wp_ajax_nopriv_sbp_filter_products', [__CLASS__, 'sbp_filter_products']);

        // widget shortcode
        add_shortcode('sbp_filter_widget', [__CLASS__, 'sbp_filter_widget']);
    }

    /**
     * Register Widget JS for AJAX
     */
    public static function sbp_widget_js()
    {
        wp_register_script('sbp-widget-js', self::sbp_filter_js(), ['jquery'], false);
    }

    /**
     * Widget shortcode
     *
     * @return void
     */
    public static function sbp_filter_widget()
    {
        global $post;
        
        // retrieve product ids
        $prod_ids = explode(',', get_post_meta($post->ID, 'sbp_products', true));

        // colors select
        self::colors();

        // features select
        self::features();

        // price slider
        self::price_slider(); ?>

        <div id="sbp-filter-button-cont">
            <!-- submit filter request -->
            <button id="sbp-filter-submit" class="button button-primary" title="<?php _e('click to filter', 'woocommerce'); ?>" data-prod-ids="<?php echo implode(',', $prod_ids) ?>" data-nonce="<?php echo wp_create_nonce('shop by filter products'); ?>">
                <?php _e('Filter', 'woocommerce'); ?>
            </button>
        </div>

    <?php }

    /**
     * AJAX to filter products
     *
     * @return void
     */
    public static function sbp_filter_products()
    {

        check_ajax_referer('shop by filter products');

        // set up vars
        $colors          = $_POST['colors'];
        $features        = $_POST['features'];
        $custom_features = $_POST['custom_features'];
        $max_price       = $_POST['max_price'];
        $min_price       = $_POST['min_price'];
        $prod_ids        = explode(',', $_POST['prod_ids']);

        $core_prod_ids = [];
        foreach ($prod_ids as $pid) :

            $price = get_post_meta($pid, '_price', true);

            if ($price >= $min_price && $price <= $max_price) :
                $core_prod_ids[] = $pid;
            endif;

        endforeach;

        // setup color and featur ids array
        $color_feature_ids = [];

        // ******************
        // if colors defined
        // ******************
        if (!empty($colors)) :
            foreach ($core_prod_ids as $key => $id) :

                $color_meta = get_post_meta($id, '_coloredvariables', true);
                $color_arr = array_keys($color_meta['pa_color']['values']);

                foreach ($colors as $color) :
                    if (in_array($color, $color_arr)) :
                        $color_feature_ids[] = $id;
                    endif;
                endforeach;

            endforeach;
        endif;

        // ***************************
        // if custom features defined
        // ***************************
        if (!empty($custom_features)) :
            foreach ($core_prod_ids as $key => $cid) :

                $custom_features_meta = maybe_unserialize(get_post_meta($cid, 'sbp_attribs', true));

                foreach ($custom_features as $key => $feature) :
                    if (in_array($feature, $custom_features_meta)) :
                        $color_feature_ids[] = $cid;
                    endif;
                endforeach;

            endforeach;
        endif;

        // ********************
        // if features defined
        // ********************
        if (!empty($features)) :

            // inval submitted feature ids
            $features_to_inval = [];
            foreach ($features as $feature) {
                $features_to_inval[] = intval($feature);
            }

            // loop to retrieve product attribs and push attrib names to $attrib_noncolor_features
            foreach ($core_prod_ids as $key => $pid) :

                // get product object instance
                $prod_obj = wc_get_product($pid);

                // get product attributes
                $attributes = $prod_obj->get_attributes();

                foreach ($attributes as $name => $value) :

                    // retrieve data
                    $data = $value->get_data();

                    // check if is taxonomy
                    $is_taxonomy = $data['is_taxonomy'];

                    // If is taxonomy
                    if ($is_taxonomy) :

                        // Get attribute WP_Terms
                        $terms = $value->get_terms();

                        // Loop through attribute WP_Term
                        foreach ($terms as $term) :

                            // get term id
                            $term_id   = $term->term_id;

                            // check if term id is in $features array and push product id to 
                            if (in_array($term_id, $features_to_inval)) :
                                $color_feature_ids[] = $pid;
                            endif;

                        endforeach;

                    endif;
                endforeach; /* $attributes loop */
            endforeach;/* $prod_ids loop */
        endif;

        if (!empty($color_feature_ids)) :
            // wp_send_json($color_feature_ids);
            self::return_html($color_feature_ids);
        else :
            // wp_send_json($core_prod_ids);
            self::return_html($core_prod_ids);
        endif;

        wp_die();
    }

    /**
     * Filter and selection JS
     */
    public static function sbp_filter_js()
    {?>

        <script>
            jQuery(document).ready(function($) {

                // ****************
                // colors on click
                // ****************
                $('a.sbp-color-swatch').on('click', function(e) {
                    e.preventDefault();
                    $(this).toggleClass('sbp-color-selected');
                });

                // ******************
                // features on click
                // ******************
                $('.sbp-feature').on('click', function(e) {
                    e.preventDefault();
                    $(this).toggleClass('sbp-feature-selected');
                });

                // *******
                // submit
                // *******
                $('#sbp-filter-submit').on('click', function(e) {

                    e.preventDefault();

                    // nonce
                    let nonce = $('#sbp-filter-submit').data('nonce');

                    // ajax url
                    let ajaxurl = '<?php echo admin_url('admin-ajax.php') ?>';

                    // prod_ids
                    let prod_ids = $(this).data('prod-ids');

                    // colors
                    let colors = [];

                    $('a.sbp-color-swatch').each(function(index, element) {
                        if ($(this).hasClass('sbp-color-selected')) {
                            colors.push($(this).data('color'));
                        }
                    });

                    // standard features (product taxonomy)
                    let features = [];

                    $('.sbp-feature').each(function(index, element) {
                        if ($(this).hasClass('sbp-feature-selected')) {
                            features.push($(this).data('term-id'));
                        }
                    });

                    // custom features (as defined on Shop By settings page and selected/specified per product)
                    let custom_features = [];
                    $('.sbp-custom-feature').each(function(index, element) {
                        if ($(this).hasClass('sbp-feature-selected')) {
                            custom_features.push($(this).data('feature'));
                        }
                    });

                    // min and max price
                    let min_price = $('#sbp-range-min').val();
                    let max_price = $('#sbp-range-max').val();

                    // ajax data object
                    var data = {
                        '_ajax_nonce': nonce,
                        'action': 'sbp_filter_products',
                        'colors': colors,
                        'features': features,
                        'custom_features': custom_features,
                        'max_price': max_price,
                        'min_price': min_price,
                        'prod_ids': prod_ids
                    }

                    // send request
                    $.post(ajaxurl, data, function(response) {
                        // console.log(response);
                        // return;
                        $('div#sbp-products-cont').empty();
                        $('div#sbp-products-cont').html(response);
                    });

                });
            });
        </script>
<?php }
}

SBP_Widget_SC::init();
