<?php
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

$title_blog = $args['fields']['title'];
$selected_post = $args['fields']['selected_post'];
$number_of_posts = wp_count_posts()->publish;
?>

    <section class="posts">

        <div class="site--container">
            <h1 class="posts__title ld"><?php echo $title_blog; ?></h1>
            <div class="posts__wrapper">

                <?php if (!empty($all_cats)): ?>

                    <div class="posts-panel">
                        <div class="post-panel__wrapper">
                            <div class="posts-panel-title"><?php _e('Category') ?></div>
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

                <div class="posts_content">
                    <h2 class="posts_items-title">Featured Insights</h2>
                    <div class="posts_content-top">

                        <?php if (!empty($selected_post)): ?>

                            <div class="posts_selected-post">

                                <ul class="posts_items-wrapper">

                                    <?php get_template_part('partials/post-item-big',  NULL,  array('p' => $selected_post)); ?>

                                </ul>

                            </div>

                        <?php endif ?>

                        <?php if (!empty($post_last)): ?>

                            <div class="posts_last-post">

                                <ul class="posts_items-wrapper">

                                    <?php get_template_part('partials/post-item',  NULL,  array('p' => $post_last)); ?>

                                </ul>

                            </div>

                        <?php endif ?>

                    </div>

                    <?php if (!empty($posts)): ?>

                    <div class="posts_items">
                        <h2 class="posts_items-title">more Insights</h2>
                        <ul class="posts_items-wrapper">

                            <?php
                            foreach ($posts as $p):
                                get_template_part('partials/post-item',  NULL,  array('p' => $p));

                            endforeach; ?>

                        </ul>

                        <?php else : ?>

                            <div class="news-nothing"><h1 class="news-nothing__title title-regular"><?php _e('Nothing found', 'theme') ?></h1></div>

                        <?php endif ?>

                    </div>

                    <?php
                    get_template_part(
                        'partials/pagination',
                        NULL,
                        [
                            'query' => $query,
                            'slug' => $post->post_name,
                            'number_of_posts' => $number_of_posts,
                            'current_paged' => $paged,
                            'categories' => $categories,
                            'current_number_of_posts' => $current_number_of_posts,
                            'posts_per_page' => get_option( 'posts_per_page' ),
                        ]
                    );
                    ?>

                </div>
    </section>

<?php wp_reset_postdata() ?>