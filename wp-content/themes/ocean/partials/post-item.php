<?php
$p = $args['p'];
$categories = get_the_category($p);

?>

<li class="posts__item">
    <a href="<?php echo get_permalink($p); ?>">
        <div class="posts__item-image">

            <?php echo get_the_post_thumbnail($p); ?>

        </div>
        <h3 class="posts__item-title">

            <?php echo $p->post_title?>

        </h3>
        <div class="posts__item-text">

            <?php echo $p->post_excerpt; ?>

        </div>
        <div class="posts__item-bottom">
            <div class="posts__item-info">
                <time>

                    <?php echo get_the_date('j.  m.  Y', $p); ?>

                </time>
                <div class="posts__item-author">

                    <?php echo get_the_author_meta('display_name', $p->post_author); ?>

                </div>
            </div>

            <?php if(!empty($categories)) { ?>

                <ul class="category-list">

                    <?php foreach ($categories as $key=>$cat) {
                        $category = $cat->cat_name;
                        $category_description = $cat->category_description?>

                        <li class="post__category category" style="background-color: <?php echo $category_description ?>">

                            <?php echo $category ?>

                        </li>

                    <?php } ?>

                </ul>

            <?php } ?>

        </div>
    </a>
</li>



