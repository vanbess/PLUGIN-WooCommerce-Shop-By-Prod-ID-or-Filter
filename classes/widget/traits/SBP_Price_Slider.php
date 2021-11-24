<?php

trait SBP_Price_Slider
{

    use SB_Query_Prods;

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

        // retrieve category name, which should be equal to post title
        $category = $post->post_title;

        // enqueue scripts
        add_action('wp_footer', [__TRAIT__, 'sbp_slider_scripts']);

        // query products and product prices
        $prod_ids = self::sbp_query_products($category);

        // price array
        $price_arr = [];

        foreach ($prod_ids as $key => $id) :
            $price_arr[] = get_post_meta($id, '_price', true);
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
            <div id="sbp-slider-range" data-currency="<?php echo alg_get_current_currency_code(); ?>"></div>

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
                        $("#amount").text(ui.values[0] + ' ' + $(this).data('currency') + ' - ' + ui.values[1] + ' ' + $(this).data('currency'));
                        $('#sbp-range-min').val(ui.values[0]);
                        $('#sbp-range-max').val(ui.values[1]);
                    }
                });
                $("#amount").text($("#sbp-slider-range").slider("values", 0) + ' ' + $("#sbp-slider-range").data('currency') + ' - ' + $("#sbp-slider-range").slider("values", 1) + ' ' + $("#sbp-slider-range").data('currency'));
                $('#sbp-range-min').val($("#sbp-slider-range").slider("values", 0));
                $('#sbp-range-max').val($("#sbp-slider-range").slider("values", 1));
            });
        </script>
<?php }
}

?>