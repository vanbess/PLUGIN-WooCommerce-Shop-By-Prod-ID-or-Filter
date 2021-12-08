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

?>

        <!-- label -->
        <p>
            <label for="sbp-prod-ids">
                <b><?php _e('Specify product IDs to display on this page:', 'woocommerce'); ?></b>
            </label>
        </p>

        <!-- products define -->
        <p>
            <input id="sbp-prod-ids" type="text" placeholder="<?php _e('comma separated list of product IDs', 'woocommerce'); ?>" style="width: 100%;" value="<?php echo $saved_prod_ids; ?>">
            <br>
        </p>

        <p>
            <!-- save products -->
            <button id="sbp-save-products" class="button button-primary button-large" data-post-id="<?php echo $post_id; ?>" data-nonce="<?php echo wp_create_nonce('sbp process products') ?>" style="width: 40%; font-size: 15px; margin-right: 15px;">
                <?php _e('Save Product IDs', 'woocommerce'); ?>
            </button>
        </p>


    <?php
        // js
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
