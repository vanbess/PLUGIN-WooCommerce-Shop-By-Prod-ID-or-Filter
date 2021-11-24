<?php

/**
 * Inserts default Shop By pages based on existing product categories
 */
trait SBP_Insert_Default_Pages
{

    /**
     * Inserts default Shop By pages based on existing product categories
     *
     * @return void
     */
    public static function insert_default_pages()
    {

        // retrieve all product categories
        $prod_cats = get_terms(
            [
                'taxonomy'   => 'product_cat',
                'hide_empty' => false,
                // 'parent'     => 0
            ]
        );

        // array which will hold initial cat names
        $cat_names = [];

        // array which will hold filtered cat names
        $filtered_cat_names = [];

        // array which holds term ids (english only)
        $term_ids = [];

        // loop through cat objects and retrieve english names using polylang
        foreach ($prod_cats as $key => $cat_obj) :

            // skip uncategorized
            if ($cat_obj->name === 'Uncategorized') :
                continue;
            endif;

            // push english term ids to $term_ids using polylang as filter
            $term_ids[] = pll_get_term($cat_obj->term_id, 'en');

        endforeach;

        // filter empty values in $term_ids and remove duplicate values
        $term_ids = array_filter(array_unique($term_ids));

        // loop through $term_ids, retrieve term object, get term name and push to $cat_names
        foreach ($term_ids as $key => $id) :
            $cat_obj = get_term($id);
            $cat_names[] = $cat_obj->name;
        endforeach;

        // filter out any cat names which start with pll and push to $final_cat_names
        foreach ($cat_names as $key => $name) :
            if (preg_match("/pll/i", $name)) :
                continue;
            endif;
            $final_cat_names[] = $name;
        endforeach;

        // page ids array
        $page_ids = [];

        // loop through $final_cat_names and insert Shop By page for each if not previously inserted
        if (!get_option('sbp_pages_inserted')) :

            foreach ($final_cat_names as $key => $name) :

                $page_id = wp_insert_post([
                    'post_title'   => $name,
                    'post_content' => '[sbp_shopby_display]',
                    'post_status'  => 'publish',
                    'post_type'    => 'shop-by',
                    'meta_input'   => [
                        '_wp_page_template' => 'sbptmpl-left-sidebar.php'
                    ]
                ]);

                if (!is_wp_error($page_id)) :
                    $page_ids[] = $page_id;
                endif;

            endforeach;

            // insert flag in db to avoid duplicating pages
            if (!empty($page_ids)) :
                update_option('sbp_pages_inserted', 'yes');
            endif;

        endif;
    }
}
