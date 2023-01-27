<li class="posts__item">
    <a href="<?php the_permalink(); ?>">


        <div class="posts__item-image">

            <?php echo get_the_post_thumbnail(); ?>

        </div>
        <h3 class="posts__item-title">

            <?php the_title(); ?>

        </h3>

        <div class="posts__item-text">

            <?php the_excerpt(); ?>

        </div>

        <div class="posts__item-bottom">
            <div class="posts__item-info">
                <time>

                <?php the_time('j. m. Y'); ?>

                </time>
                <div class="posts__item-author">

                    <?php echo get_the_author_meta('display_name', post_author); ?>

                </div>
            </div>

            <?php if (!empty(get_the_category())): ?>

                <div class="posts__item-category category" <?php if (!empty(get_the_category()[0]->category_description)) {echo 'style="background-color: ' . get_the_category()[0]->category_description . ';"';} ?>>

                    <?php echo get_the_category()[0]->cat_name; ?>

                </div>

            <?php endif; ?>

        </div>
    </a>
</li>