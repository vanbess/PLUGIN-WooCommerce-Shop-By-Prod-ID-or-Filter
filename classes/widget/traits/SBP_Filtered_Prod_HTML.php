<?php

trait SB_Filtered_Prod_HTML
{

    /**
     * Renders filtered product html
     *
     * @param  array $product_ids
     * @return html
     */
    public static function return_html($product_ids)
    {

        $product_ids = array_unique($product_ids);

        foreach ($product_ids as $pid) :

            // retrieve prod object and associated data
            $prod_obj = wc_get_product($pid);

            // retrieve discount percentage
            $disc_perc = get_post_meta($pid, '_flatsome_product_percentage', true);

            // retrieve product type
            $prod_type = $prod_obj->get_type();

            // retrieve pricing data for simple products
            if ($prod_type === 'simple') :
                $reg_price = $prod_obj->get_regular_price();
                $sale_price = $prod_obj->get_sale_price();

            // retrieve pricing data for variable products
            elseif ($prod_type === 'variable') :
                $children = $prod_obj->get_children();
                $counter = 1;
                foreach ($children as $cid) :
                    if ($counter === 1) :
                        $reg_price = get_post_meta($cid, '_regular_price', true);
                        $sale_price = get_post_meta($cid, '_sale_price', true);
                    endif;
                    $counter++;
                endforeach;
            endif;

            // retrieve rest of required product data
            $title = $prod_obj->get_title();
            $price_html = $prod_obj->get_price_html();
            $img = $prod_obj->get_image();
            $url = $prod_obj->get_permalink();

            // display each product
?>
            <div class="product-small col has-hover post-<?php echo $pid; ?> product has-post-thumbnail">
                <div class="col-inner">
                    <?php if ($disc_perc || $sale_price) :
                        $d_perc = (($reg_price - $sale_price) / $reg_price) * 100;
                    ?>
                        <div class="badge-container absolute left top z-1">
                            <div class="callout badge badge-circle">
                                <div class="badge-inner secondary on-sale"><span class="onsale">-<?php $disc_perc ? print $disc_perc : print number_format($d_perc, 0); ?>%</span></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="product-small box ">
                        <div class="box-image">
                            <div class="image-none">
                                <a href="<?php echo $url; ?>">
                                    <?php echo $img; ?>
                                </a>
                            </div>
                        </div>
                        <div class="box-text box-text-products text-center">
                            <div class="title-wrapper">
                                <p class="name product-title woocommerce-loop-product__title" style="height: 23.0469px;">
                                    <a href="<?php echo $url; ?>"><?php echo $title; ?></a>
                                </p>
                            </div>
                            <div class="price-wrapper" style="height: 45.1719px;">
                                <?php echo $price_html; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<?php
        endforeach;
    }
}

?>