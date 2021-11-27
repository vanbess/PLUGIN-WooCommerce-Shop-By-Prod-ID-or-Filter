<?php
/*
Template Name: SBP Left Sidebar
Template Post Type: shop-by
*/

get_header(); ?>

<?php do_action('flatsome_before_page'); ?>

<div class="page-wrapper page-left-sidebar">

    <!-- banner header vibe -->
    <div class="banner has-hover">
        <div class="banner-inner fill">
            <div class="banner-bg fill">
                <div class="bg fill bg-fill bg-loaded"></div>
                <div class="overlay"></div>
            </div>
            <div class="banner-layers container">
                <div class="fill banner-link"></div>
                <div class="text-box banner-layer x50 md-x50 lg-x50 y50 md-y50 lg-y50 res-text">
                    <div class="text dark">
                        <div class="text-inner text-center">
                            <h2 class="uppercase"><strong><?php the_title(); ?></strong></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <!-- sidebar -->
        <div class="large-3 col">
            <?php get_sidebar(); ?>
        </div>

        <!-- content -->
        <div id="content" class="large-9 col" role="main">
            <div class="page-inner">
                <div class="shop-container">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php the_content(); ?>
                    <?php endwhile; // end of the loop. 
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php do_action('flatsome_after_page'); ?>

    <?php get_footer(); ?>