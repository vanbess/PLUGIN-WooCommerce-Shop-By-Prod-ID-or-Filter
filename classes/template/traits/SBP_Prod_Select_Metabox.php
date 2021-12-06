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

        echo $post_id;

        // query products and return ids
        $args = [
            'limit'    => -1,
            'return'   => 'ids',
            'status'   => 'publish'
        ];

        $prod_ids = wc_get_products($args);

        // retrieve available languages
        $langs = pll_languages_list();

        // query saved product ids
        $saved_prod_ids = maybe_unserialize(get_post_meta($post_id, 'sbp_products', true));

?>

        <!-- label -->
        <p>
            <label for="sbp-prod-ids">
                <b><?php _e('Select products to display on this page:', 'woocommerce'); ?></b>
            </label>
        </p>

        <!-- products select -->
        <p>
            <?php foreach ($prod_ids as $pid) : ?>
                <?php if (in_array($pid, $saved_prod_ids)) : ?>
                    <button class="button button-secondary sbp-product-select button-primary" data-id="<?php echo $pid ?>" style="margin-bottom: 8px; margin-right: 5px;">
                        <?php echo get_the_title($pid); ?>
                    </button>
                <?php else : ?>
                    <button class="button button-secondary sbp-product-select" data-id="<?php echo $pid ?>" style="margin-bottom: 8px; margin-right: 5px;">
                        <?php echo get_the_title($pid); ?>
                    </button>
                <?php endif; ?>
            <?php endforeach; ?>
        </p>

        <p>
            <!-- save products -->
            <button id="sbp-save-products" class="button button-primary button-large" data-prod-id="<?php echo $post_id; ?>" data-nonce="<?php echo wp_create_nonce('sbp process products') ?>" style="width: 40%; font-size: 15px; margin-right: 15px;">
                <?php _e('Save Products', 'woocommerce'); ?>
            </button>

            <!-- select language -->

            <label for="sbp-lang-select" style="font-size: 14px;">
                <b><?php _e('Change language:', 'woocommerce'); ?></b>
            </label>

            <select id="sbp-lang-select">
                <option value=""><b><?php _e('select...', 'woocommerce'); ?></b></option>
                <?php foreach ($langs as $lang) : ?>
                    <option value="<?php echo $lang; ?>"><?php echo strtoupper($lang); ?></option>
                <?php endforeach; ?>
            </select>

            <!-- change language -->
            <button id="sbp-change-lang" class="button button-secondary button-large" data-nonce="<?php echo wp_create_nonce('sbp process products') ?>" data-prods="<?php echo implode(',', $prod_ids); ?>" style="width: 40%; font-size: 15px; margin-left: 15px;">
                <?php _e('Change Language', 'woocommerce'); ?>
            </button>
        </p>


    <?php

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

                // vars
                let selected = [];

                // select products
                $('.sbp-product-select').each(function(index, element) {
                    $(this).on('click', function(e) {
                        e.preventDefault();
                        $(this).toggleClass('button-primary');
                    });
                });

                // save selected products
                $('#sbp-save-products').on('click', function(e) {
                    e.preventDefault();

                    let nonce = $(this).data('nonce'),
                        prod_id = $(this).data('prod-id');

                    // push selected products to selected array
                    $('.sbp-product-select').each(function(index, element) {
                        if ($(this).hasClass('button-primary')) {
                            selected.push($(this).data('id'));
                        }
                    });

                    // if selected array not empty, send ajax request to save, else display alert
                    if (selected.length > 0) {

                        var data = {
                            '_ajax_nonce': nonce,
                            'action': 'sbp_backend_save_prods',
                            'selected': selected,
                            'prod_id': prod_id
                        }

                        $.post(ajaxurl, data, function(response) {
                            alert(response);
                            location.reload();
                        });

                    } else {
                        alert('<?php _e('Please select at least one product', 'woocommerce') ?>');
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

        // save selected
        if (isset($_POST['selected'])) :

            $prods = $_POST['selected'];
            $post_id = $_POST['prod_id'];

            $updated = update_post_meta($post_id, 'sbp_products', maybe_serialize($prods));

            if ($updated) :
                wp_send_json(__('Products successfully saved', 'woocommerce'));
            endif;

        endif;

        // change language
        if (isset($_POST['lang'])) :

        endif;

        wp_die();
    }
}
