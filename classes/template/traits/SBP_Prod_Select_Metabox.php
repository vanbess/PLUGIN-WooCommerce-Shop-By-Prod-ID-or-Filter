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
        $saved_prod_ids = get_post_meta($post_id, 'sbp_products', true);

        // query saved language
        $saved_lang = get_post_meta($post_id, 'sbp_lang', true);

        // retrieve alternative language product ids if $saved_lang is not EN
        $lang_prod_ids = [];
        if ($saved_lang && $saved_lang !== 'en') :
            foreach ($prod_ids as $pid) :

                $translations = pll_get_post_translations($pid);

                foreach ($translations as $lang => $tid) :
                    if ($lang === $saved_lang) :
                        $lang_prod_ids[] = $tid;
                    endif;
                endforeach;

            endforeach;
        endif;

        // set final product ids
        !empty($lang_prod_ids) ? $final_ids = $lang_prod_ids : $final_ids = $prod_ids;

?>

        <!-- label -->
        <p>
            <label for="sbp-prod-ids">
                <b><?php _e('Select products to display on this page:', 'woocommerce'); ?></b>
            </label>
        </p>

        <!-- products select -->
        <p id="sbp-product-button" data-current-lang="<?php echo $saved_lang; ?>">
            <?php foreach ($final_ids as $pid) : ?>
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

                // set currently select lang
                $('#sbp-lang-select').val($('#sbp-product-button').data('current-lang'));

                // select products
                $(document).on('click', '.sbp-product-select', function(e) {
                    e.preventDefault();
                    $(this).toggleClass('button-primary');
                });

                // save selected products
                $('#sbp-save-products').on('click', function(e) {
                    e.preventDefault();

                    let nonce = $(this).data('nonce'),
                        prod_id = $(this).data('prod-id'),
                        lang = $('#sbp-lang-select').val();

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
                            'prod_id': prod_id,
                            'lang': lang
                        }

                        $.post(ajaxurl, data, function(response) {
                            alert(response);
                            location.reload();
                        });

                    } else {
                        alert('<?php _e('Please select at least one product', 'woocommerce') ?>');
                    }

                });

                // change product language
                $('button#sbp-change-lang').on('click', function(e) {
                    e.preventDefault();

                    // vars
                    let nonce = $(this).data('nonce'),
                        products = $(this).data('prods'),
                        lang = $('#sbp-lang-select').val(),
                        default_text = $(this).text();

                    // change button text while we wait
                    $(this).text('<?php _e('Working...', 'woocommerce') ?>');

                    // if lang not selected, display error, else submit ajax request
                    if (lang.length === 0) {
                        alert('<?php _e('Please select a language', 'woocommerce'); ?>');
                    } else {

                        // json object
                        var data = {
                            '_ajax_nonce': nonce,
                            'action': 'sbp_backend_save_prods',
                            'products': products,
                            'lang': lang
                        }

                        // send ajax request
                        $.post(ajaxurl, data, function(response) {
                            // console.log(response)

                            // if no prod ids returned, display error, else insert alternative set of buttons
                            if (response.success === false) {
                                $('#sbp-product-button').empty().html('<b style="color: red;"><i>' + response.data + '</i></b>');
                                $('button#sbp-change-lang').text(default_text);
                            } else {
                                $('#sbp-product-button').empty().html(response);
                                $('button#sbp-change-lang').text(default_text);
                            }
                        });
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

            $prods   = $_POST['selected'];
            $post_id = $_POST['prod_id'];
            $lang    = $_POST['lang'];

            $updated[] = update_post_meta($post_id, 'sbp_products', $prods);
            $updated[] = update_post_meta($post_id, 'sbp_lang', $lang);

            if (!empty($updated)) :
                wp_send_json(__('Products successfully saved', 'woocommerce'));
            endif;

        endif;

        // change language
        if (isset($_POST['lang'])) :

            $prod_ids = explode(',', $_POST['products']);
            $lang     = $_POST['lang'];

            $new_pids = [];

            foreach ($prod_ids as $pid) :
                $translations = pll_get_post_translations($pid);

                foreach ($translations as $tlang => $tid) :
                    if ($tlang === $lang) :
                        $new_pids[] = $tid;
                    endif;
                endforeach;

            endforeach;

            if (!empty($new_pids)) :
                foreach ($new_pids as $pid) : ?>

                    <button class="button button-secondary sbp-product-select" data-id="<?php echo $pid; ?>" style="margin-bottom: 8px; margin-right: 5px;">
                        <?php echo get_the_title($pid); ?>
                    </button>

<?php endforeach;
            else :
                wp_send_json_error(__('No products found for selected language. Please select a different language and try again.', 'woocommerce'));
            endif;

        endif;

        wp_die();
    }
}
