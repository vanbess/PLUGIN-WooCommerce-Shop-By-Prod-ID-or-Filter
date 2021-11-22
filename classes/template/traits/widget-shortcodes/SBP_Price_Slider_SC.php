<?php

trait SBP_Price_Slider_SC
{

    public static function price_slider()
    {

        // enqueue scripts
        add_action('wp_footer', 'sbp_jq_ui_scripts');

        function sbp_jq_ui_scripts()
        {
            wp_enqueue_style('sbp-jquery-ui');
            wp_enqueue_script('jquery-ui-slider');
        }
?>
        <script>
            jQuery(function($) {
                $("#sbp-slider-range").slider({
                    range: true,
                    min: 0,
                    max: 500,
                    values: [75, 300],
                    slide: function(event, ui) {
                        $("#amount").text("$" + ui.values[0] + " - $" + ui.values[1]);
                        $('#sbp-range-min').val(ui.values[0]);
                        $('#sbp-range-max').val(ui.values[1]);
                    }
                });
                $("#amount").text("$" + $("#sbp-slider-range").slider("values", 0) +" - $" + $("#sbp-slider-range").slider("values", 1));
                $('#sbp-range-min').val($("#sbp-slider-range").slider("values", 0));
                $('#sbp-range-max').val($("#sbp-slider-range").slider("values", 1));

            });
        </script>

        <span class="widget-title">
            <span><?php _e('Price range', 'woocommerce'); ?></span>
        </span>

        <div class="is-divider small"></div>

        <p id="sbp-price-slider-text">
            <span><?php _e('Price: ', 'woocommerce'); ?></span>
            <span type="text" id="amount" readonly style="border:0; color:black; font-weight:bold; box-shadow: none; padding: 0;"></span>
        </p>

        <input type="hidden" id="sbp-range-min">
        <input type="hidden" id="sbp-range-max">

        <div id="sbp-slider-range"></div>

<?php }
}

?>