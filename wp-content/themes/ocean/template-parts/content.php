<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package ocean
 */


global $post;
$category = get_the_category($post->ID)[0]->cat_name;
$category_description = get_the_category($post->ID)[0]->category_description;

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="section post">
        <div class="size-main">
            <div class="post__info">
                <span class="post__data"><?php the_date(); ?></span>
                <span class="post__author"><?php the_author(); ?></span>
                <span class="post__category category" style="background-color: <?php echo $category_description ?>"><?php echo $category ?></span>
            </div>
            <h1 class="post__title"><?php the_title(); ?></h1>
            <div class="post__image">
                <?php echo get_the_post_thumbnail( $page->ID, 'large'); ?>
            </div>
            <div class="post__content">
                <div class="post__share">
                    <span class="share-title">Share this page</span>
                    <ul>
                        <li>
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13.0838 9.58351L14.8335 7.83376C16.122 6.54526 16.122 4.45576 14.8335 3.16651V3.16651C13.545 1.87801 11.4555 1.87801 10.1663 3.16651L8.4165 4.91626" stroke="#1D252D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M6.66748 11.3325L11.3325 6.66748" stroke="#1D252D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M4.9162 8.4165L3.16645 10.1663C1.87795 11.4548 1.87795 13.5443 3.16645 14.8335V14.8335C4.45495 16.122 6.54445 16.122 7.8337 14.8335L9.58345 13.0838" stroke="#1D252D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </li>
                        <li>
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.782 1.641C2.782 2.27116 2.27116 2.782 1.641 2.782C1.01084 2.782 0.5 2.27116 0.5 1.641C0.5 1.01084 1.01084 0.5 1.641 0.5C2.27116 0.5 2.782 1.01084 2.782 1.641Z" fill="#0F1010" stroke="#0F1010"/>
                                <path d="M5.33306 5.0248H7.04656V5.7698V6.2698H7.54656H7.58481H7.88628L8.027 6.00319C8.32182 5.44462 9.07867 4.7998 10.2608 4.7998C11.571 4.7998 12.2346 5.21978 12.61 5.81539C13.0189 6.46395 13.1553 7.42166 13.1553 8.63705V13.1298H11.3271V9.2018V9.189C11.3271 8.6848 11.3271 7.9872 11.1016 7.41576C10.9821 7.11311 10.7909 6.82052 10.4852 6.60706C10.1785 6.39288 9.79925 6.2883 9.35631 6.2883C8.92305 6.2883 8.54527 6.37315 8.22743 6.54931C7.90669 6.72708 7.67707 6.98127 7.51763 7.27099C7.21177 7.82681 7.15981 8.52396 7.15981 9.1268V13.1291H5.33306V5.0248ZM0.726562 5.0248H2.55781V13.1291H0.726562V5.0248Z" fill="#0F1010" stroke="#0F1010"/>
                            </svg>
                        </li>
                        <li>
                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16.7475 3.49622C16.156 3.7583 15.5206 3.93539 14.8527 4.01543C15.5419 3.60303 16.0575 2.95397 16.3034 2.18935C15.6558 2.57396 14.9471 2.84469 14.2081 2.98976C13.7111 2.45913 13.0529 2.10741 12.3355 1.98922C11.6181 1.87104 10.8818 1.99299 10.2409 2.33615C9.59996 2.67931 9.09024 3.22448 8.79088 3.88701C8.49152 4.54955 8.41927 5.29238 8.58535 6.00018C7.2733 5.9343 5.98976 5.59328 4.81803 4.99924C3.6463 4.4052 2.61257 3.57142 1.78394 2.55201C1.5006 3.04076 1.33769 3.60743 1.33769 4.21093C1.33737 4.75422 1.47116 5.28918 1.72718 5.76836C1.98321 6.24754 2.35355 6.65612 2.80535 6.95785C2.28138 6.94117 1.76897 6.79959 1.31077 6.54489V6.58739C1.31072 7.34937 1.57429 8.0879 2.05678 8.67768C2.53926 9.26745 3.21093 9.67213 3.95781 9.82305C3.47174 9.9546 2.96213 9.97398 2.46748 9.87972C2.67821 10.5354 3.08868 11.1087 3.64145 11.5195C4.19421 11.9302 4.86159 12.1578 5.55015 12.1705C4.38128 13.0881 2.93773 13.5858 1.45173 13.5836C1.1885 13.5837 0.925492 13.5683 0.664062 13.5376C2.17244 14.5074 3.9283 15.0221 5.72156 15.0201C11.792 15.0201 15.1105 9.99235 15.1105 5.63185C15.1105 5.49018 15.107 5.3471 15.1006 5.20543C15.7461 4.73862 16.3033 4.16056 16.7461 3.49835L16.7475 3.49622Z" fill="#0F1010"/>
                            </svg>
                        </li>
                        <li>
                            <svg width="9" height="17" viewBox="0 0 9 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.84672 9.07367H5.66228V9.25811V16.8156H2.83837V9.25245V9.068H2.65392H0.184444V6.40569H2.64814H2.83258V6.22125V3.99673C2.83258 2.74251 3.22365 1.79508 3.87965 1.16083C4.53672 0.525545 5.48014 0.184604 6.62179 0.184606L6.62249 0.184603C7.35486 0.18181 8.08685 0.215941 8.81556 0.28685V2.63816H7.37661C6.71227 2.63816 6.26434 2.79306 5.99126 3.11125C5.72342 3.42335 5.66228 3.85031 5.66228 4.29617V6.22692V6.41136H5.84672H8.6983L8.35031 9.07367H5.84672Z" fill="#0F1010" stroke="#0F1010" stroke-width="0.368889"/>
                            </svg>
                        </li>
                    </ul>
                </div>
                <div class="post__content-inner">
                    <?php the_content(); ?>
                </div>
                <div class="post__author-full">
                    <span>Post by:</span>
                    <div>
                        <div></div>
                        <div>
                            <span><?php the_author(); ?></span>
                            <span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




<!--	<header class="entry-header">-->
<!--		--><?php
//		if ( is_singular() ) :
//			the_title( '<h1 class="entry-title">', '</h1>' );
//		else :
//			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
//		endif; ?>
<!---->
<!--	</header> -->
<!---->
<!--	<div class="entry-content">-->
<!--		--><?php
//		the_content(
//			sprintf(
//				wp_kses(
//					/* translators: %s: Name of current post. Only visible to screen readers */
//					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'ocean' ),
//					array(
//						'span' => array(
//							'class' => array(),
//						),
//					)
//				),
//				wp_kses_post( get_the_title() )
//			)
//		);
//
//		wp_link_pages(
//			array(
//				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'ocean' ),
//				'after'  => '</div>',
//			)
//		);
//		?>
<!--	</div>-->
<!---->
<!--	<footer class="entry-footer">-->
<!--		--><?php //ocean_entry_footer(); ?>
<!--	</footer>-->
</article><!-- #post-<?php the_ID(); ?> -->
