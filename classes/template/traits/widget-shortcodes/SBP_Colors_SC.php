<?php

/**
 * 1. Generate filtered list of colors for current product category
 * 2. Build clickable swatch filter list (multiple selection)
 * 3. Filter products based on selection
 */

trait SBP_Colors_SC
{

    /**
     * flag to check whether colored variable data have been saved or not
     */
    static $colors_saved = false;

    /**
     * Display filter colors
     *
     * @return html
     */
    public static function colors()
    {
        global $post;

        // retrieve category name, which should be equal to post title
        $category = $post->post_title;

        // query prodocts
        $prod_ids = self::query_products($category);

        // build color data
        $color_data = [];

        // loop
        foreach ($prod_ids as $key => $id) :

            // retrieve colored variables meta data
            $attribs = get_post_meta($id, '_coloredvariables', true);

            // if colored variable data available, retrieve colors
            if (!empty($attribs['pa_color'])) :

                $colors = $attribs['pa_color']['values'];

                // loop through colors and push colowr name => color code to $color_data array
                foreach ($colors as $color => $c_data) :
                    $color_data[$color] = $c_data['color'];
                endforeach;

            endif;

        endforeach;

        // save color data to db for later ref
        if (self::$colors_saved === false) :

            $col_saved = update_option('sbp_color_data', $color_data);

            if ($col_saved !== false) :
                self::$colors_saved = true;
            endif;

        endif;

        // retrieve swatch width and height to display correct swatch size
        $s_width = get_option('woocommerce_shop_swatch_width', '32');
        $s_height = get_option('woocommerce_shop_swatch_height', '32');

?>

        <span id="sbp-widget-filter-head"><?php _e('Filter ' . $category, 'woocommerce'); ?></span>

        <span class="widget-title">
            <span><?php _e('Colors', 'woocommerce'); ?></span>
        </span>

        <div class="is-divider small"></div>

        <?php foreach ($color_data as $name => $color_code) : ?>
            <a class="sbp-color-swatch" style="display: inline-block; width: <?php print $s_width; ?>px; height: <?php print $s_height; ?>px; background: <?php print $color_code; ?>" href="#" data-color="<?php echo $name; ?>" title="<?php _e('Click to select ' . $name, 'woocommerce'); ?>">
            </a>
        <?php endforeach; ?>

<?php }


    /**
     * Query products based on language currently being viewed on the frontend and return array of product ids
     *
     * @return array $prod_ids - Array of matching product IDs to be used to render frontend display of products
     */
    private static function query_products($category)
    {

        $args = [
            'limit'    => -1,
            'category' => [$category],
            'return'   => 'ids',
            'status'   => 'publish'
        ];

        $prod_ids = wc_get_products($args);

        return $prod_ids;
    }
}
