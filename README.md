# Shop By Product Category Filter Plugin for WooCommerce

## Overview

This plugin, when installed, creates a new post/page type by the name of "shop-by", which displays products based on existing WooCommerce product categories.

On plugin activation, a new page is inserted into the database for each of these product categories, with page titles being that of the category itself. The page title should be left as is when editing each of these pages in the backend, since they are used to query that particular categories products by name.

Changing the page name to something which does not match a product category will cause silent display failure of the products contained in that category. However, if the slug needs to be changed to something else, this can be done without affecting frontend display of products.

If you need to add additional content to a particular page, simply do so in the page text editor, updating the page afterwards.

This will not affect the product display since it lives in a seperate container and the products themselves are inserted directly into the page template itself in order to avoid any potential display errors.

## Page Template

Each page is displayed using a modified left sidebar page template, based on the left sidebar template which comes with the Flatsome theme.

This template is loaded directly from the plugin directory to avoid any accidental overrides of the default theme page templates.

Additional layouts/templates can be added at a later stage if needed, along with support for additional/different themes.

## Sidebar/Widget Product Filters

Product filters are displayed via shortcode in the default widget/sidebar using the following shortcode: [sbp_filter_widget]. You will need to manually add this shortcode to the default widget, along with adding any additional default widgets such as product categories if you wish to display them as well.

Note that a particular filter will only be displayed on the frontend if the current category's products have the appropriate attributes attached. 

For example, if none of a particular category's products have any color options available, the color filter will not be displayed in the sidebar. 

The same goes for the features filter, which displays filters based on attributes other than color and/or custom defined features/attributes.

The price range filter will always be displayed.

## Adding Custom Features/Attributes

Adding custom features and/or attributes can be done by navigating to Products -> Shop By Settings. Features/attributes added on this page will be available for selection in the product edit screen in a metabox situated at the top right of said screen.

Custom features/attributes are used in addition to WooCommerce product attributes for filtering purposes.

