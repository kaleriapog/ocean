<?php
$p = $args['p'];

$cats = get_the_category($p->ID);
$cat_list = '';
if (!empty($cats)) {
    foreach ( $cats as $cat ) {
        $cat_list .= $cat->name;
    }
}
$cat_list = rtrim( $cat_list, ', ' );

?>

<li class="posts__item">
    <a href="<?php echo get_permalink($p); ?>">


        <div class="posts__item-image">

            <?php echo get_the_post_thumbnail($p); ?>

        </div>
        <div class="posts__item-title">

            <?php echo $p->post_title?>

        </div>

        <div class="posts__item-text">

            <?php echo $p->post_excerpt; ?>

        </div>

        <div class="posts__item-bottom">
            <div class="posts__item-info">
                <time>

                    <?php echo get_the_date('j. m. Y', $p); ?>

                </time>
                <div class="posts__item-author">

                    <?php echo get_the_author_meta('display_name', $p->post_author); ?>

                </div>
            </div>

            <?php if (!empty(get_the_category($p))): ?>

                <div class="posts__item-category category" <?php if (!empty(get_the_category($p)[0]->category_description)) {echo 'style="background-color: ' . get_the_category($p)[0]->category_description . ';"';} ?>>

                    <?php echo $cat_list; ?>

                </div>

            <?php endif; ?>

        </div>
    </a>
</li>



