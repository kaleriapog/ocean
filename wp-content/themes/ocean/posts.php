<?php
/* Template Name: Posts */

global $wp;
$current_url = home_url( add_query_arg( NULL, NULL ) );
//$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$all_cats = get_categories();
$action = '/'.$post->post_name.'/?paged=1';

$args_post = array(
    'posts_per_page' => get_option( 'posts_per_page' ),
    'post_type'   => 'post',
    'post_status' => 'publish',
    'suppress_filters' => false,
    'paged' => $paged,
    'relation' => 'OR',
);

$args_post_last = array(
    'posts_per_page' => get_option( 'posts_per_page' ),
    'post_type'   => 'post',
    'post_status' => 'publish',
    'suppress_filters' => false,
    'paged' => $paged,
    'relation' => 'OR',
);

$categories = get_categories([
    'taxonomy'     => 'category',
    'type'         => 'post',
    'hide_empty'   => false,
]);


$action = '/'.$post->post_name.'/?paged=1';

$cat_ids = NULL;
if(!empty($_GET['cat_ids'])) {
    $cat_ids = $_GET['cat_ids'];
    $args_post['cat'] = $cat_ids;
}
$cat_current_page = get_the_category_by_ID($cat_ids);

$query = new WP_Query();
$posts = $query->query($args_post);
$post_last = get_posts($args_post_last)[0];
$current_number_of_posts = $query->found_posts;

$selected_post = $args['fields']['selected_post'];
$number_of_posts = wp_count_posts()->publish;

$cats = get_the_category($post_last->ID);
$cat_list = '';
if (!empty($cats)) {
    foreach ( $cats as $cat ) {
        $cat_list .= $cat->name;
    }
}
$cat_list = rtrim( $cat_list, ', ' );

$form_subscribe = get_field('form_subscribe', 'options');
$form = $form_subscribe['form'];
$form_title = $form_subscribe['title'];
$form_text = $form_subscribe['text'];
$form_image = $form_subscribe['image'];

$fields_last_block = get_field('last_block_in_posts', 'options');
$title = $fields_last_block['title'];
$link = $fields_last_block['link'];
$text = $fields_last_block['text'];
$image = $fields_last_block['image'];
$decor_title = $fields_last_block['decor_title'];
$color_bg = $fields_last_block['background_color'];

get_header();
?>

    <section class="posts">
        <div class="posts__headline">
            <h1 class="posts__title ld"><?php the_title(); ?></h1>
        </div>
        <div class="posts__last post-last">
            <div class="size-main">
                <a href="<?php echo get_permalink($post_last); ?>" class="post-last__wrapper">
                    <div class="post-last__image">

                        <?php echo get_the_post_thumbnail($post_last); ?>

                    </div>
                    <div class="post-last__content">
                        <div class="post-last__content-inner">
                            <h2 class="post-last__title title"><?php echo $post_last->post_title?></h2>
                            <div class="post-last__text text"><?php echo $post_last->post_excerpt ?></div>
                        </div>
                        <div class="post-last__item-left">
                            <div class="post-last__item-info">
                                <time>

                                    <?php echo get_the_date('j. m. Y', $post_last); ?>

                                </time>
                                <div class="post-last__item-author">

                                    <?php echo get_the_author_meta('display_name', $post_last->post_author); ?>

                                </div>
                            </div>

                            <?php if (!empty(get_the_category($post_last))): ?>

                                <div class="post-last__item-category category" <?php if (!empty(get_the_category($post_last)[0]->category_description)) {echo 'style="background-color: ' . get_the_category($post_last)[0]->category_description . ';"';} ?>>

                                    <?php echo $cat_list; ?>

                                </div>

                            <?php endif; ?>

                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="size-main">
            <div class="posts__wrapper">

                <?php if (!empty($all_cats)): ?>

                    <div class="posts-panel">
                        <div class="posts-panel__wrapper">

                            <?php if (!empty($categories)): ?>

                                <form class="posts-filter" action="<?php echo $action ?>">
                                    <div class="posts-filter__item">
                                        <label class="posts-panel-item">
                                            <a class="<?php echo empty($cat_ids) ? 'active' : '' ?> item-filter" href="<?php echo $action ?>"><?php _e('All articles') ?></a>
                                        </label>
                                    </div>

                                    <?php foreach ($categories as $cat): ?>

                                        <div class="posts-filter__item">
                                            <label class="posts-panel-item">
                                                <input input class="item-filter-input" type="radio" name="cat_ids" value="<?php echo $cat->term_id ?>" <?php if ($cat->term_id == $cat_ids) : ?> checked <?php endif ?>>
                                                <span class="item-filter"><?php echo $cat->name ?></span>
                                            </label>
                                        </div>

                                    <?php endforeach ?>

                                </form>

                            <?php endif ?>

                        </div>
                    </div>

                <?php endif ?>

                <div class="posts__content">

                    <?php if (!empty($posts)): ?>

                    <div class="posts__items">
                        <ul class="posts__items-wrapper">

                            <?php
                            foreach ($posts as $key=>$p):
                                if($key >= 0 && $key <= 5):
                                    get_template_part('partials/post-item',  NULL,  array('p' => $p));
                                endif;
                            endforeach; ?>

                        </ul>

                        <?php endif; ?>
                        <?php if(count($posts) <= 0) : ?>

                            <div class="news-nothing"><h1 class="news-nothing__title title-regular"><?php _e('Nothing found', 'theme') ?></h1></div>

                        <?php endif ?>

                    </div>
                </div>
            </div>
            <div class="posts__form-subscribe posts-form">
                <div class="posts-form__content">
                    <h2 class="posts-form__title title"><?php echo $form_title ?></h2>
                    <div class="posts-form__form"><?php echo $form ?></div>
                    <div class="posts-form__text text"><?php echo $form_text ?></div>
                </div>
                <div class="posts-form__image">
                    <img src="<?php echo $form_image['url'] ?>" alt="<?php echo $form_image['title'] ?>">
                </div>
            </div>

            <?php if(count($posts) >= 6) : ?>

                <div class="posts__wrapper posts-second-list">

                <?php if (!empty($all_cats)): ?>

                    <div class="posts-panel">
                    </div>

                <?php endif; ?>

                <div class="posts__content">

                    <?php if (!empty($posts)): ?>

                    <div class="posts__items">
                        <ul class="posts__items-wrapper">

                            <?php
                            foreach ($posts as $key=>$p):
                                if($key >= 6 && $key <= 12):
                                    get_template_part('partials/post-item',  NULL,  array('p' => $p));
                                endif;
                            endforeach; ?>

                        </ul>

                        <?php if(count($posts) >= 11) : ?>



                            <?php
                                if(!empty($cat_ids)) {
                                    $shortcode = '[ajax_load_more post_type="post" posts_per_page="10" offset="11" pause="true" category="'.$cat_current_page.'"]';

                                } else {
                                    $shortcode = '[ajax_load_more post_type="post" posts_per_page="10" offset="11" pause="true"]';
                                }
                            ?>

                            <div class="news-list">
                                <?php echo do_shortcode($shortcode); ?>
                            </div>

                        <?php endif; ?>

                    <?php endif; ?>

                    </div>

                </div>
            </div>

            <?php endif ?>

        </div>
    </section>
    <section class="section-basic" style="background-color: <?php echo $color_bg ?>">
        <div class="size-main">
            <div class="section-basic__wrapper">
                <div class="section-basic__content">
                    <div class="section-basic__headline">
                        <h2 class="title"><?php echo $title ?></h2>

                        <?php if(!empty($decor_title)) { ?>
                            <div class="section-basic__decor-title">
                                <img src="<?php echo $decor_title['url'] ?>" alt="<?php echo $decor_title['title'] ?>">
                            </div>
                        <?php } ?>

                    </div>
                    <div class="section-basic__description">
                        <a class="button-link button-link-white" href="<?php echo $link['url'] ?>">

                            <?php echo $link['title'] ?>

                            <div class="button-link__icon">
                                <svg width="29" height="24" viewBox="0 0 29 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2 10.5C1.17157 10.5 0.5 11.1716 0.5 12C0.5 12.8284 1.17157 13.5 2 13.5L2 10.5ZM28.0607 13.0607C28.6464 12.4749 28.6464 11.5251 28.0607 10.9393L18.5147 1.3934C17.9289 0.807611 16.9792 0.807611 16.3934 1.3934C15.8076 1.97919 15.8076 2.92893 16.3934 3.51472L24.8787 12L16.3934 20.4853C15.8076 21.0711 15.8076 22.0208 16.3934 22.6066C16.9792 23.1924 17.9289 23.1924 18.5147 22.6066L28.0607 13.0607ZM2 13.5L27 13.5V10.5L2 10.5L2 13.5Z" fill="#1D252D"/>
                                </svg>
                            </div>
                        </a>
                        <div class="section-basic__text text"><?php echo $text ?></div>
                    </div>
                </div>
                <div class="section-basic__image">
                    <img src="<?php echo $image['url'] ?>" alt="<?php echo $image['title'] ?>">
                </div>
            </div>
        </div>

    </section>

<?php wp_reset_postdata();
get_footer();
?>