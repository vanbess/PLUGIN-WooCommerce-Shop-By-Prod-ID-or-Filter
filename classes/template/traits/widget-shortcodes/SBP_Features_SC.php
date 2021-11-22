<?php

trait SBP_Features_SC
{

    /**
     * Displays selectable Shop By features
     *
     * @return void
     */
    public static function features()
    {

        global $post;

        // retrieve category name, which should be equal to post title
        $category = $post->post_title;

        // query products
        $prod_ids = self::query_products($category);

        // attrib noncolor features array
        $attrib_noncolor_features = [];

        // loop to retrieve product attribs and push attrib names to $attrib_noncolor_features
        foreach ($prod_ids as $key => $id) :

            // get product object instance
            $prod_obj = wc_get_product($id);

            // get product attributes
            $attributes = $prod_obj->get_attributes();

            foreach ($attributes as $name => $value) :

                // only retrieve data for attribs other than color and non pa_size attribs
                if ($name !== 'pa_color' && $name !== 'size') :

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

                            // get the term name and push to $attrib_noncolor_features
                            $term_name     = $term->name;
                            $attrib_noncolor_features[] = $term_name;
                        endforeach;

                    // If is not taxonomy
                    else :

                        // Loop through attribute option values and push to $attrib_noncolor_features
                        foreach ($value->get_options() as $term_name) :
                            $attrib_noncolor_features[] = $term_name;
                        endforeach;
                    endif;

                endif;
            endforeach; /* $attributes loop */
        endforeach;/* $prod_ids loop */

?>

        <span class="widget-title">
            <span><?php _e('Features', 'woocommerce'); ?></span>
        </span>

        <div class="is-divider small"></div>

        <?php foreach ($attrib_noncolor_features as $key => $feature) : ?>
            <button class="button button-default sbp-feature" title="<?php _e('click to select', 'woocommerce'); ?>">
                <?php echo $feature; ?>
            </button>
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

?>