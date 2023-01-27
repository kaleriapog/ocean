<li class="posts__item">
    <a href="<?php the_permalink($post->ID); ?>">


        <div class="posts__item-image">

            <?php echo get_the_post_thumbnail($post->ID); ?>

        </div>
        <h3 class="posts__item-title">

            <?php the_title($post->ID); ?>

        </h3>

        <div class="posts__item-text">

            <?php the_excerpt($post->ID); ?>

        </div>

        <div class="posts__item-bottom">
            <div class="posts__item-info">
                <time>

                <?php the_time('j. m. Y'); ?>

                </time>
                <div class="posts__item-author">

                    <?php echo get_the_author_meta('display_name', $post->ID); ?>

                </div>
            </div>

            <?php if (!empty(get_the_category($post->ID))): ?>

                <div class="posts__item-category category" <?php if (!empty(get_the_category()[0]->category_description)) {echo 'style="background-color: ' . get_the_category()[0]->category_description . ';"';} ?>>

                    <?php echo get_the_category($post->ID)[0]->cat_name; ?>

                </div>

            <?php endif; ?>

        </div>
    </a>
</li>