<?php

trait SBP_Features
{

    /**
     * Displays selectable Shop By features
     *
     * @return void
     */
    public static function features()
    {

        global $post;

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

        // attrib noncolor features array
        $attrib_noncolor_features = [];

        // custom features array
        $custom_features_arr = [];

        // loop to retrieve product attribs and push attrib names to $attrib_noncolor_features
        foreach ($prod_ids as $id) :

            // get custom defined features
            $custom_features = maybe_unserialize(get_post_meta($id, 'sbp_attribs', true));

            // if custom features exist, push to $custom_features_arr
            if ($custom_features) :
                foreach ($custom_features as $key => $feature) :
                    $custom_features_arr[] = $feature;
                endforeach;
            endif;

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
                            $term_name = $term->name;
                            $term_id   = $term->term_id;
                            $attrib_noncolor_features[$term_id] = $term_name;
                        endforeach;

                    endif;
                endif;
            endforeach; /* $attributes loop */
        endforeach;/* $prod_ids loop */

        // make $custom_features_arr unique
        $custom_features_arr = array_unique($custom_features_arr);

        if (!empty($attrib_noncolor_features) || !empty($custom_features_arr)) : ?>

            <!-- features cont -->
            <div id="sbp-features-cont">
                <span class="widget-title">
                    <span><?php _e('Features', 'woocommerce'); ?></span>
                </span>

                <div class="is-divider small"></div>

                <?php
                // display features product attribute
                foreach ($attrib_noncolor_features as $term_id => $feature) : ?>
                    <button class="button button-default sbp-feature" data-term-id="<?php echo $term_id; ?>" data-feature="<?php echo $feature; ?>" title="<?php _e('click to select', 'woocommerce'); ?>">
                        <?php echo $feature; ?>
                    </button>
                <?php endforeach;

                // display custom features
                foreach ($custom_features_arr as $key => $feature) : ?>
                    <button class="button button-default sbp-feature sbp-custom-feature" data-feature="<?php echo $feature; ?>" title="<?php _e('click to select', 'woocommerce'); ?>">
                        <?php echo $feature; ?>
                    </button>
                <?php endforeach; ?>
            </div>

<?php endif;
    }
}

?>