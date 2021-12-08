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

        <!-- label -->
        <p>
            <label for="sbp-prod-ids">
                <b><?php _e('Specify product IDs to display on this page:', 'woocommerce'); ?></b>
            </label>
        </p>

        <!-- products define -->
        <p>

            <select name="sbp-prod-ids[]" id="sbp-prod-ids" multiple style="width: 100%; border: 1px solid darkgrey 1px;" data-current="<?php echo implode(',', $saved_prod_ids); ?>">
                <?php foreach ($select2_arr as $pid => $title) : ?>
                    <option value="<?php echo $pid; ?>"><?php echo $title; ?></option>
                <?php endforeach; ?>
            </select>

            <br>
        </p>

        <p>
            <!-- save products -->
            <button id="sbp-save-products" class="button button-primary button-large" data-post-id="<?php echo $post_id; ?>" data-nonce="<?php echo wp_create_nonce('sbp process products') ?>" style="width: 40%; font-size: 15px; margin-right: 15px;">
                <?php _e('Save Product IDs', 'woocommerce'); ?>
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

                // retrieve currently defined product ids
                let current_prods = $('#sbp-prod-ids').data('current');

                // setup array which will hold correctly formatted product id list
                let pre_selected = [];

                // loop through current_prods and push vals to pre_selected
                $.each(current_prods.split(","), function(i, e) {
                    pre_selected.push(e);
                });

                // preselect currently defined product ids
                $('#sbp-prod-ids').val(pre_selected);

                // render select2
                $('#sbp-prod-ids').select2({
                    placeholder: 'click to select products',
                    minimumInputLength: 3,
                    width: 'resolve',
                });

                // save product ids
                $('#sbp-save-products').on('click', function(e) {
                    e.preventDefault();

                    let nonce = $(this).data('nonce'),
                        prod_ids = $('#sbp-prod-ids').val(),
                        post_id = $(this).data('post-id');

                    // if selected array not empty, send ajax request to save, else display alert
                    if (prod_ids.length > 0) {

                        var data = {
                            '_ajax_nonce': nonce,
                            'action': 'sbp_backend_save_prods',
                            'prod_ids': prod_ids,
                            'post_id': post_id
                        }

                        $.post(ajaxurl, data, function(response) {
                            alert(response);
                            location.reload();
                        });

                    } else {
                        alert('<?php _e('Please provide at least one product ID', 'woocommerce') ?>');
                    }

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
        $post_id  = $_POST['post_id'];

        $updated[] = update_post_meta($post_id, 'sbp_products', $prod_ids);

        if (!empty($updated)) :
            wp_send_json(__('Product IDs successfully saved', 'woocommerce'));
        endif;


        wp_die();
    }
}
