<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package ocean
 */

$header = get_field('header', 'options');
$social = $header['header_social'];
$header_button = $header['button'];

?>

<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'ocean' ); ?></a>

	<header id="masthead" class="header">
        <div class="size-main">
            <div class="header__wrapper">
                <div class="header__left">
                    <div class="logo">

                        <?php the_custom_logo(); ?>

                    </div>
                    <nav id="site-navigation" class="header__navigation">

                        <?php
                        wp_nav_menu(
                            array(
                                'theme_location' => 'menu-main',
                                'menu_id'        => 'primary-menu',
                                'menu_class'          => 'header__menu-list'
                            )
                        );
                        ?>

                    </nav>
                </div>
                <div class="header__right">
                    <div class="header__button">
                        <a class="button" href="<?php echo $header_button['url'] ?>"><?php echo $header_button['title']?></a>
                    </div>
                    <div class="header__social">

                        <a href="<?php echo $social['url'] ?>">

                            <?php
                                if(!empty($social['custom_icon'])) { ?>
                                    <img src="<?php echo $social['custom_icon']['url'] ?>" alt="<?php echo $social['name'] ?>">

                            <?php
                                } elseif (!empty($social['icon'])) { ?>

                                    <span><?php echo $social['icon']?></span>

                                <?php }

                            ?>

                        </a>
                    </div>
                </div>
            </div>
        </div>
	</header>

