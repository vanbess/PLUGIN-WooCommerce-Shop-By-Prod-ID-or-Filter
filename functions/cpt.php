<?php

/**
 * Custom post type for displaying various shop by pages
 */

function cptui_register_my_cpts_shop_by()
{

    /**
     * Post Type: Shop By Pages.
     */

    $labels = [
        "name"                     => __("Shop By Pages", "woocommerce"),
        "singular_name"            => __("Shop By Page", "woocommerce"),
        "menu_name"                => __("Shop By Pages", "woocommerce"),
        "all_items"                => __("All Shop By Pages", "woocommerce"),
        "add_new"                  => __("Add new", "woocommerce"),
        "add_new_item"             => __("Add new Shop By Page", "woocommerce"),
        "edit_item"                => __("Edit Shop By Page", "woocommerce"),
        "new_item"                 => __("New Shop By Page", "woocommerce"),
        "view_item"                => __("View Shop By Page", "woocommerce"),
        "view_items"               => __("View Shop By Pages", "woocommerce"),
        "search_items"             => __("Search Shop By Pages", "woocommerce"),
        "not_found"                => __("No Shop By Pages found", "woocommerce"),
        "not_found_in_trash"       => __("No Shop By Pages found in trash", "woocommerce"),
        "parent"                   => __("Parent Shop By Page: ", "woocommerce"),
        "featured_image"           => __("Featured image for this Shop By Page", "woocommerce"),
        "set_featured_image"       => __("Set featured image for this Shop By Page", "woocommerce"),
        "remove_featured_image"    => __("Remove featured image for this Shop By Page", "woocommerce"),
        "use_featured_image"       => __("Use as featured image for this Shop By Page", "woocommerce"),
        "archives"                 => __("Shop By Page archives", "woocommerce"),
        "insert_into_item"         => __("Insert into Shop By Page", "woocommerce"),
        "uploaded_to_this_item"    => __("Upload to this Shop By Page", "woocommerce"),
        "filter_items_list"        => __("Filter Shop By Pages list", "woocommerce"),
        "items_list_navigation"    => __("Shop By Pages list navigation", "woocommerce"),
        "items_list"               => __("Shop By Pages list", "woocommerce"),
        "attributes"               => __("Page attributes", "woocommerce"),
        "name_admin_bar"           => __("Shop By Page", "woocommerce"),
        "item_published"           => __("Shop By Page published", "woocommerce"),
        "item_published_privately" => __("Shop By Page published privately.", "woocommerce"),
        "item_reverted_to_draft"   => __("Shop By Page reverted to draft.", "woocommerce"),
        "item_scheduled"           => __("Shop By Page scheduled", "woocommerce"),
        "item_updated"             => __("Shop By Page updated.", "woocommerce"),
        "parent_item_colon"        => __("Parent Shop By Page:", "woocommerce"),
    ];

    $args = [
        "label"                 => __("Shop By Pages", "woocommerce"),
        "labels"                => $labels,
        "description"           => "",
        "public"                => true,
        "publicly_queryable"    => true,
        "show_ui"               => true,
        "show_in_rest"          => true,
        "rest_base"             => "",
        "rest_controller_class" => "WP_REST_Posts_Controller",
        "has_archive"           => false,
        "show_in_menu"          => true,
        "show_in_nav_menus"     => true,
        "delete_with_user"      => false,
        "exclude_from_search"   => false,
        "capability_type"       => "page",
        "map_meta_cap"          => true,
        "hierarchical"          => false,
        "rewrite"               => [
            "slug"       => "shop-by", 
            "with_front" => true
        ],
        "query_var"             => true,
        "menu_icon"             => "dashicons-admin-plugins",
        "supports"              => ["title", "editor", "page-attributes"],
        "show_in_graphql"       => false,
    ];

    register_post_type("shop-by", $args);
}

add_action('init', 'cptui_register_my_cpts_shop_by');
