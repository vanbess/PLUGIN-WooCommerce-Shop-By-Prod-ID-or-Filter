<?php

trait SBP_Template_CSS
{

    /**
     * Frontend CSS
     *
     * @return void
     */
    public static function sbp_frontend_css()
    {

        $request_uri = $_SERVER['REQUEST_URI'];

?>
        <style>
            .text-box {
                width: 60%;
            }

            .text-box.text {
                font-size: 100%;
            }

            .banner {
                padding-top: 300px;
            }

            .banner.bg.bg-loaded {
                background-image: 7055;
            }

            .banner.overlay {
                background-color: rgba(44, 26, 12, 0.3);
            }

            .banner.bg {
                background-position: 22% 42%;
            }

            <?php
            if (strpos($request_uri, 'shop-by') !== false) : ?>#header {
                margin-bottom: -30px;
            }

            <?php endif; ?>

            /* widget/sidebar styles */
            span#sbp-widget-filter-head {
                display: block;
                font-weight: 600;
                letter-spacing: 1px;
                font-size: 18px;
                text-transform: uppercase;
                text-align: center;
                background: #efefef;
                line-height: 2.5;
                margin-bottom: 20px;
            }

            button.button.button-default.sbp-feature {
                background: #efefef;
                color: black;
                font-size: 12px;
                font-weight: 500;
            }

            p#sbp-price-slider-text {
                font-size: 14px;
            }

            div#sbp-slider-range {
                height: 8px;
            }

            #sbp-slider-range>span {
                border-radius: 50%;
                top: -6px;
            }
        </style>
<?php }
}
