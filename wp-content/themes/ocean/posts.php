<?php
/* Template Name: Posts */

global $wp;
$current_url = home_url( add_query_arg( NULL, NULL ) );
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
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

get_header();
?>

    <section class="posts">
        <div class="posts__headline">
            <h1 class="posts__title ld"><?php the_title(); ?></h1>
        </div>
        <div class="posts__last post-last">
            <div class="size-main">
                <div class="post-last__wrapper">
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
                </div>
            </div>
        </div>
        <div class="size-main">
            <div class="posts__wrapper">

                <?php if (!empty($all_cats)): ?>

                    <div class="posts-panel">
                        <div class="post-panel__wrapper">

                            <?php if (!empty($categories)): ?>

                                <form class="post-filter" action="<?php echo $action ?>">
                                    <div class="post-filter__item">
                                        <label class="posts-panel-item">
                                            <a class="<?php echo empty($cat_ids) ? 'active' : '' ?>" href="<?php echo $action ?>"><?php _e('All Articles') ?></a>
                                        </label>
                                    </div>

                                    <?php foreach ($categories as $cat): ?>

                                        <div class="post-filter__item">
                                            <label class="posts-panel-item">
                                                <input input type="radio" name="cat_ids" value="<?php echo $cat->term_id ?>" <?php if ($cat->term_id == $cat_ids) : ?> checked <?php endif ?>>
                                                <span><?php echo $cat->name ?></span>
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
                                if($key > 0 && $key < 5):
                                    get_template_part('partials/post-item',  NULL,  array('p' => $p));
                                endif;
                            endforeach; ?>

                        </ul>

                        <?php else : ?>

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
            <div class="posts__wrapper">

                <?php if (!empty($all_cats)): ?>

                    <div class="posts-panel">
                        <div class="post-panel__wrapper">

                            <?php if (!empty($categories)): ?>

                                <form class="post-filter" action="<?php echo $action ?>">
                                    <div class="post-filter__item">
                                        <label class="posts-panel-item">
                                            <a class="<?php echo empty($cat_ids) ? 'active' : '' ?>" href="<?php echo $action ?>"><?php _e('All Articles') ?></a>
                                        </label>
                                    </div>

                                    <?php foreach ($categories as $cat): ?>

                                        <div class="post-filter__item">
                                            <label class="posts-panel-item">
                                                <input input type="radio" name="cat_ids" value="<?php echo $cat->term_id ?>" <?php if ($cat->term_id == $cat_ids) : ?> checked <?php endif ?>>
                                                <span><?php echo $cat->name ?></span>
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
                                if($key > 5):
                                    get_template_part('partials/post-item',  NULL,  array('p' => $p));
                                endif;
                            endforeach; ?>

                        </ul>

                        <?php else : ?>

                            <div class="news-nothing"><h1 class="news-nothing__title title-regular"><?php _e('Nothing found', 'theme') ?></h1></div>

                        <?php endif ?>

                    </div>
                </div>
            </div>
        </div>
    </section>

<?php wp_reset_postdata();
get_footer();
?>