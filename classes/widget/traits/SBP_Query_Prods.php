<?php

trait SB_Query_Prods
{

    /**
     * Query products based on language currently being viewed on the frontend and return array of product ids
     *
     * @return array $prod_ids - Array of matching product IDs to be used to render frontend display of products
     */
    private static function sbp_query_products($category)
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
