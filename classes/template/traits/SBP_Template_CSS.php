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
        </style>
<?php }
}
