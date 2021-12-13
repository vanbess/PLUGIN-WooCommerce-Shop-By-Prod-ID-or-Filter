<?php

trait SBP_Price_Slider
{

    /**
     * Min price, max price and current currency properties
     */
    static $min_price = '';
    static $max_price = '';

    /**
     * Price slider scripts/css
     *
     * @return void
     */
    public static function sbp_slider_scripts()
    {
        wp_enqueue_style('sbp-jquery-ui');
        wp_enqueue_script('jquery-ui-slider');
        wp_enqueue_script('sbp-price-slider', self::slider_js(), ['jquery'], false);
    }

    /**
     * Render price slider
     *
     * @return void
     */
    public static function price_slider()
    {
        global $post;

        // enqueue scripts
        add_action('wp_footer', [__TRAIT__, 'sbp_slider_scripts']);

        // query saved product tags
        $saved_prod_tags = get_post_meta($post->ID, 'sbp_prod_tags', true);

        if (!empty($saved_prod_tags)) :
            $args = array(
                'tag' => $saved_prod_tags,
                'return' => 'ids'
            );
            $prod_ids = wc_get_products($args);
        else :
            // retrieve product ids instead if tags not defined
            $prod_ids = get_post_meta($post->ID, 'sbp_products', true);
        endif;

        // price array
        $price_arr = [];

        foreach ($prod_ids as $id) :
            
            $prod_data = wc_get_product($id);

            if ($prod_data->get_type() === 'variable') :
                $children = $prod_data->get_children();
                foreach ($children as $child_id) :
                    $price_arr[] = get_post_meta($child_id, '_price', true);
                endforeach;
            else :
                $price_arr[] = get_post_meta($id, '_price', true);
            endif;
        endforeach;

        // sort array
        sort($price_arr);

        // remove duplicate values
        $price_arr = array_unique($price_arr);

        // set min and max price
        self::$min_price = $price_arr[0];
        self::$max_price = end($price_arr);

?>
        <!-- price range cont -->
        <div id="sbp-price-range-cont">

            <!-- widget title -->
            <span class="widget-title">
                <span><?php _e('Price range', 'woocommerce'); ?></span>
            </span>

            <div class="is-divider small"></div>

            <!-- slider text -->
            <p id="sbp-price-slider-text">
                <span><?php _e('Price: ', 'woocommerce'); ?></span>
                <span type="text" id="amount" readonly style="border:0; color:black; font-weight:bold; box-shadow: none; padding: 0;"></span>
            </p>

            <!-- hidden min and max inputs -->
            <input type="hidden" id="sbp-range-min" data-min="<?php echo self::$min_price; ?>">
            <input type="hidden" id="sbp-range-max" data-max="<?php echo self::$max_price; ?>">

            <!-- slider actual -->
            <div id="sbp-slider-range" data-currency="<?php echo get_woocommerce_currency_symbol(); ?>"></div>

        </div>

    <?php }

    /**
     * Javascript
     *
     * @return void
     */
    public static function slider_js()
    { ?>
        <script>
            // slider functionality
            jQuery(function($) {
                $("#sbp-slider-range").slider({
                    range: true,
                    min: $('#sbp-range-min').data('min'),
                    max: $('#sbp-range-max').data('max'),
                    values: [$('#sbp-range-min').data('min'), $('#sbp-range-max').data('max')],
                    slide: function(event, ui) {
                        $("#amount").text($(this).data('currency') + ' ' + ui.values[0] + ' - ' + $(this).data('currency') + ui.values[1]);
                        $('#sbp-range-min').val(ui.values[0]);
                        $('#sbp-range-max').val(ui.values[1]);
                    }
                });
                $("#amount").text($("#sbp-slider-range").data('currency') + ' ' + $("#sbp-slider-range").slider("values", 0) + ' - ' + $("#sbp-slider-range").data('currency') + ' ' + $("#sbp-slider-range").slider("values", 1));
                $('#sbp-range-min').val($("#sbp-slider-range").slider("values", 0));
                $('#sbp-range-max').val($("#sbp-slider-range").slider("values", 1));
            });
        </script>
<?php }
}

?>