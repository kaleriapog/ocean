<?php
$fields = $args['fields'];
$title = $fields['title'];
$text = $fields['text'];
$link = $fields['link'];
$posts = $fields['posts'];
$id = $fields['id'];

?>

<section class="section section-recent-posts" <?php if(!empty($id)) { ?> id="<?php echo $id ?>"<?php } ?>>
    <div class="size-main">
        <div class="section-recent-posts__wrapper">

            <?php if(!empty($title) || !empty($text)) { ?>

                <div class="section-recent-posts__headline">

                    <?php if(!empty($title)) { ?>

                        <h2 class="title"><?php echo strip_tags($title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?></h2>

                    <?php } ?>
                    <?php if(!empty($text)) { ?>

                        <div class=" section-recent-posts__text text"><?php echo $text?></div>

                    <?php } ?>

                </div>

            <?php } ?>

            <div class="section-recent-posts__posts">
                <div class="section-recent-posts__posts-left">

                    <?php foreach($posts as $key=>$post) {
                        $title = $post['post']->post_title;
                        $author = get_the_author_meta('display_name', $post['post']->post_author);
                        $excerpt = $post['post']->post_excerpt;
                        $thumbnail = get_the_post_thumbnail_url($post['post']);
                        $permalink = get_post_permalink($post['post']);

                        if($key === 0) { ?>

                        <a class="post-big" href="<?php echo $permalink ?>">

                                <?php if(!empty($thumbnail)) { ?>

                                    <div class="post-big__image">
                                        <img src="<?php echo $thumbnail ?>" alt="<?php echo $title ?>">
                                    </div>

                                <?php } ?>

                                <div class="post-big__info">
                                    <time datetime="<?php echo get_the_date('Y-m-j', $post['post']); ?>">

                                        <?php echo get_the_date('j. m. Y', $post['post']); ?>

                                    </time>

                                    <?php if(!empty($author)) { ?>

                                        <span class="post-big__author"><?php echo $author ?></span>

                                    <?php } ?>

                                </div>

                                <?php if(!empty($title)) { ?>

                                    <h3 class="post-big__title"><?php echo strip_tags($title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?></h3>

                                <?php } ?>
                                <?php if(!empty($excerpt)) { ?>

                                    <div class="post-big__text text"><?php echo $excerpt ?></div>

                                <?php } ?>
                            </a>

                    <?php }} ?>

                </div>
                <div class="section-recent-posts__right">

                    <?php foreach($posts as $key=>$post) {
                        $title = $post['post']->post_title;
                        $author = get_the_author_meta('display_name', $post['post']->post_author);
                        $data = $post['post']->post_date;
                        $excerpt = $post['post']->post_excerpt;
                        $permalink = get_post_permalink($post['post']);

                        if($key > 0) { ?>

                        <a class="post-regular" href="<?php echo $permalink ?>">
                            <div class="post-regular__info">
                                <time datetime="<?php echo get_the_date('Y-m-j', $post['post']); ?>">

                                    <?php echo get_the_date('j. m. Y', $post['post']); ?>

                                </time>

                                <?php if(!empty($author)) { ?>

                                    <span class="post-regular__author"><?php echo $author ?></span>

                                <?php } ?>

                            </div>
                            <?php if(!empty($title)) { ?>

                                <h3 class="post-regular__title"><?php echo $title ?></h3>

                            <?php } ?>
                            <?php if(!empty($excerpt)) { ?>

                                <div class="post-regular__text text"><?php echo $excerpt ?></div>

                            <?php } ?>

                        </a>

                    <?php }} ?>

                </div>
            </div>

            <?php if(!empty($link)) { ?>

                <div class="section-recent-posts__link">
                    <a class="button-link" href="<?php echo $link['url'] ?>">

                        <?php echo $link['title'] ?>

                        <div class="button-link__icon">
                            <svg width="29" height="24" viewBox="0 0 29 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2 10.5C1.17157 10.5 0.5 11.1716 0.5 12C0.5 12.8284 1.17157 13.5 2 13.5L2 10.5ZM28.0607 13.0607C28.6464 12.4749 28.6464 11.5251 28.0607 10.9393L18.5147 1.3934C17.9289 0.807611 16.9792 0.807611 16.3934 1.3934C15.8076 1.97919 15.8076 2.92893 16.3934 3.51472L24.8787 12L16.3934 20.4853C15.8076 21.0711 15.8076 22.0208 16.3934 22.6066C16.9792 23.1924 17.9289 23.1924 18.5147 22.6066L28.0607 13.0607ZM2 13.5L27 13.5V10.5L2 10.5L2 13.5Z" fill="#1D252D"/>
                            </svg>
                        </div>
                    </a>
                </div>

            <?php } ?>

        </div>
    </div>
</section>