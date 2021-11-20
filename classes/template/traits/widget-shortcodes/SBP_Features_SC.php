<?php

trait SBP_Features_SC
{

    /**
     * Displays selectable Shop By features
     *
     * @return void
     */
    public static function features()
    { ?>
        <ul class="woocommerce-widget-layered-nav-list">
            <li class="woocommerce-widget-layered-nav-list__item wc-layered-nav-term ">
                <a rel="nofollow" href="https://dev.nordace.com/en/shop/?filter_feature=internal-organization">Internal Organization</a> 
                <span class="count">(1)</span>
            </li>
            <li class="woocommerce-widget-layered-nav-list__item wc-layered-nav-term ">
                <a rel="nofollow" href="https://dev.nordace.com/en/shop/?filter_feature=usb-charging-port">USB Charging Port</a> 
                <span class="count">(1)</span>
            </li>
            <li class="woocommerce-widget-layered-nav-list__item wc-layered-nav-term ">
                <a rel="nofollow" href="https://dev.nordace.com/en/shop/?filter_feature=water-bottle-pocket">Water Bottle Pocket</a> 
                <span class="count">(1)</span>
            </li>
        </ul>
<?php }
}

?>