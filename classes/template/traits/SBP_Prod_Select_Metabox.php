<?php

/**
 * Renders metabox which allows users to select product IDs for display on Shop By page
 */

trait SBP_Prod_Select_Metabox
{

    /**
     * Renders metabox html
     *
     * @return void
     */
    public static function sbp_metabox_html()
    {

        global $post;
        $post_id = $post->ID;

        // query saved product ids
        $saved_prod_ids = get_post_meta($post_id, 'sbp_products', true);

        // query all products
        $prod_ids = wc_get_products([
            'post_status' => 'publish',
            'limit'       => -1,
            'return'      => 'ids'
        ]);

        // query saved product tags
        $saved_prod_tags = get_post_meta($post_id, 'sbp_prod_tags', true);

        // query product tags
        $prod_tags_q = get_terms(['taxonomy' => 'product_tag', 'hide_empty' => false]);

        // empty prod tag data array
        $prod_tags = [];

        // loop through product tag objects and push id/name to $prod_tags
        foreach ($prod_tags_q as $tag_obj) :
            $prod_tags[$tag_obj->slug] = $tag_obj->name;
        endforeach;

        // build initial select2 array
        $select2_arr = [];
        foreach ($prod_ids as $pid) :
            $select2_arr[$pid] = get_the_title($pid);
        endforeach;

        // get all translated post ids and titles (polylang) and push to $select2_arr
        if (function_exists('pll_get_post_translations')) :

            foreach ($prod_ids as $pid) :

                $curr_lang = pll_get_post_language($pid);
                $translations = pll_get_post_translations($pid);

                foreach ($translations as $lang => $tid) :
                    if ($lang !== $curr_lang) :
                        $select2_arr[$tid] = get_the_title($tid);
                    endif;
                endforeach;

            endforeach;

        endif;

?>

        <!-- label for products select -->
        <p>
            <label for="sbp-prod-ids">
                <b><?php _e('Select products to display on this page:', 'woocommerce'); ?></b>
                [<a id="sbp-clear-prods" href="#"><?php _e('clear products', 'woocommerce'); ?></a>]
            </label>
        </p>

        <!-- products select -->
        <p>

            <select name="sbp-prod-ids[]" id="sbp-prod-ids" multiple style="width: 100%; border: 1px solid darkgrey;" data-current="<?php echo implode(',', $saved_prod_ids); ?>">
                <?php foreach ($select2_arr as $pid => $title) : ?>
                    <option value="<?php echo $pid; ?>"><?php echo $title; ?></option>
                <?php endforeach; ?>
            </select>

            <br>
        </p>

        <h3><?php _e('OR', 'woocommerce'); ?></h3>

        <!-- label for product tags select -->
        <p>
            <label for="sbp-prod-tags">
                <b><?php _e('Select product tag(s) for which to display products on this page:', 'woocommerce'); ?></b>
                [<a id="sbp-clear-tags" href="#"><?php _e('clear tags', 'woocommerce'); ?></a>]
            </label>
        </p>

        <!-- product tags select -->
        <p>
            <select name="sbp-prod-tags[]" id="sbp-prod-tags" multiple style="width:  100%; border: 1px solid darkgrey;" data-current="<?php echo implode(',', $saved_prod_tags); ?>">
                <?php foreach ($prod_tags as $tag_id => $tag_name) : ?>
                    <option value="<?php echo $tag_id; ?>"><?php echo $tag_name; ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <!-- save products -->
            <button id="sbp-save-products" class="button button-primary button-large" data-post-id="<?php echo $post_id; ?>" data-nonce="<?php echo wp_create_nonce('sbp process products') ?>" style="width: 40%; font-size: 15px; margin-right: 15px;">
                <?php _e('Update Product IDs/Product Tags', 'woocommerce'); ?>
            </button>
        </p>

    <?php
        // js + css
        wp_enqueue_style('sbp-select2', SBP_URL . 'assets/select2.min.css', [], false);
        wp_enqueue_script('sbp-select2', SBP_URL . 'assets/select2.min.js', ['jquery'], false, true);
        wp_enqueue_script('sbp-backend-js', self::sbp_backend_js(), ['jquery'], false, true);
    }

    /**
     * Handles page edit screen metabox JS and AJAX
     *
     * @return void
     */
    public static function sbp_backend_js()
    { ?>
        <script>
            jQuery(document).ready(function($) {

                // clear products
                $('#sbp-clear-prods').on('click', function(e) {
                    e.preventDefault();
                    $('#sbp-prod-ids').val('').change();
                });

                // clear tags
                $('#sbp-clear-tags').on('click', function(e) {
                    e.preventDefault();
                    $('#sbp-prod-tags').val('').change();
                });

                // preselect currently defined product ids
                let current_prods = $('#sbp-prod-ids').data('current');

                let pre_selected = [];

                $.each(current_prods.split(","), function(i, e) {
                    pre_selected.push(e);
                });

                $('#sbp-prod-ids').val(pre_selected);

                // preselect currently defined product tags
                let current_tags = $('#sbp-prod-tags').data('current');

                let pre_selected_tags = [];

                $.each(current_tags.split(","), function(i, e) {
                    pre_selected_tags.push(e);
                });

                $('#sbp-prod-tags').val(pre_selected_tags);

                // render select2 for product selection
                $('#sbp-prod-ids').select2({
                    placeholder: '<?php _e('click to select products', 'woocommerce'); ?>',
                    minimumInputLength: 2,
                    width: 'resolve',
                });

                // render select2 for product tag selection
                $('#sbp-prod-tags').select2({
                    placeholder: '<?php _e('click to select product tag(s)', 'woocommerce') ?>',
                    minimumInputLength: 2,
                    width: 'resolve',
                });

                // save product ids
                $('#sbp-save-products').on('click', function(e) {
                    e.preventDefault();

                    let nonce = $(this).data('nonce'),
                        prod_ids = $('#sbp-prod-ids').val(),
                        tag_ids = $('#sbp-prod-tags').val(),
                        post_id = $(this).data('post-id');

                    // if selected array not empty, send ajax request to save, else display alert

                    var data = {
                        '_ajax_nonce': nonce,
                        'action': 'sbp_backend_save_prods',
                        'prod_ids': prod_ids,
                        'tag_ids': tag_ids,
                        'post_id': post_id
                    }

                    $.post(ajaxurl, data, function(response) {
                        alert(response);
                        location.reload();
                    });

                });

            });
        </script>
<?php }

    /**
     * AJAX function to save product ids defined in the page edit screen metabox
     *
     * @return void
     */
    public static function sbp_backend_save_prods()
    {

        check_ajax_referer('sbp process products');

        // save product ids
        $prod_ids = $_POST['prod_ids'];
        $tag_ids  = $_POST['tag_ids'];
        $post_id  = $_POST['post_id'];

        // print_r($_POST);

        // wp_die();

        // save/delete prod ids
        if (!empty($prod_ids)) :
            $ids_saved = update_post_meta($post_id, 'sbp_products', $prod_ids);
            if ($ids_saved) :
                wp_send_json(__('Product IDs successfully saved', 'woocommerce'));
                wp_die();
            endif;
        elseif (empty($prod_ids)) :
            $ids_deleted = delete_post_meta($post_id, 'sbp_products');
            if ($ids_deleted) :
                wp_send_json(__('Product ID(s) successfully deleted', 'woocommerce'));
                wp_die();
            endif;
        endif;

        // save/delete tag ids
        if (!empty($tag_ids)) :
            $tag_ids_saved = update_post_meta($post_id, 'sbp_prod_tags', $tag_ids);
            if ($tag_ids_saved) :
                wp_send_json(__('Product tag(s) successfully saved', 'woocommerce'));
                wp_die();
            endif;
        elseif (empty($tag_ids)) :
            $tags_deleted = delete_post_meta($post_id, 'sbp_prod_tags');
            if ($tags_deleted) :
                wp_send_json(__('Product tag(s) successfully deleted', 'woocommerce'));
                wp_die();
            endif;
        endif;

        // if no tag ids or product ids submitted
        if (empty($tag_ids) && empty($prod_ids)) :
            wp_send_json(__('No products or tags selected. Please select at least one product OR one tag and try again.', 'woocommerce'));
            wp_die();
        endif;
    }
}
