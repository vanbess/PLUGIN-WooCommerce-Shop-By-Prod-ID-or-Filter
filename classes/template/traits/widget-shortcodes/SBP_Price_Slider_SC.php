<?php

trait SBP_Price_Slider_SC
{

    public static function price_slider()
    { ?>
        <aside id="woocommerce_price_filter-2" class="widget woocommerce widget_price_filter">
            <form method="get" action="https://dev.nordace.com/en/shop/">
                <div class="price_slider_wrapper">
                    <div class="price_slider ui-slider ui-corner-all ui-slider-horizontal ui-widget ui-widget-content">
                        <div class="ui-slider-range ui-corner-all ui-widget-header" style="left: 0%; width: 100%;"></div><span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default" style="left: 0%;"></span><span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default" style="left: 100%;"></span>
                    </div>
                    <div class="price_slider_amount" data-step="10">
                        <input type="text" id="min_price" name="min_price" value="0" data-min="0" placeholder="Min price" style="display: none;">
                        <input type="text" id="max_price" name="max_price" value="300" data-max="300" placeholder="Max price" style="display: none;">
                        <button type="submit" class="button">Filter</button>
                        <div class="price_label">
                            Price: <span class="from">$0</span> — <span class="to">$300</span>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </form>
        </aside>
<?php }
}

?>