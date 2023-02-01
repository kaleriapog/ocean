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
$header_social = $header['header_social'];
$header_button = $header['button'];
$decorative_element = $header['decorative_element'];
$social = get_field('social', 'options');

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
                        <div class="icon-menu-close">
                            <svg class="icon-menu-close__icon" width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.6665 12.6665L25.3332 25.3332" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M25.3332 12.6665L12.6665 25.3332" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>

                        <?php
                        wp_nav_menu(
                            array(
                                'theme_location' => 'menu-main',
                                'menu_id'        => 'primary-menu',
                                'menu_class'          => 'header__menu-list'
                            )
                        );
                        ?>

                        <div class="header__subnav">
                            <div class="header__decor">
                                <img src="<?php echo $decorative_element['url'] ?>" alt="<?php echo $decorative_element['title'] ?>">
                            </div>
                            <ul class="social-list">
                                <?php foreach($social as $key=>$item) :
                                    $url = $item['link'];
                                    $icon = $item['icon'];
                                    $name = $item['name'];
                                    $custom_icon = $item['custom_icon'];
                                    ?>

                                    <li class="social-list__item">
                                        <a class="social-list__item-link <?php if(!empty($name)) { ?>social-list__item-link-text<?php } elseif(!empty($custom_icon)) { ?>social-list__item-link-custom_icon<?php } ?>" href="<?php echo $url ?>">

                                            <?php
                                            if(!empty($name)) { ?>

                                                <span><?php echo $name ?></span>

                                            <?php } elseif (!empty($icon)) {  ?>

                                                <span><?php echo $icon ?></span>

                                            <?php } elseif (!empty($custom_icon)) {  ?>

                                                <img src="<?php echo $custom_icon['url'] ?>" alt="<?php echo $custom_icon['name'] ?>">

                                            <?php }

                                            ?>
                                        </a>
                                    </li>

                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </nav>
                </div>
                <div class="header__right">
                    <div class="header__button">
                        <a class="button" href="<?php echo $header_button['url'] ?>"><?php echo $header_button['title']?></a>
                    </div>
                    <div class="header__social">

                        <a href="<?php echo $header_social['url'] ?>">

                            <?php
                                if(!empty($header_social['custom_icon'])) { ?>
                                    <img src="<?php echo $header_social['custom_icon']['url'] ?>" alt="<?php echo $header_social['name'] ?>">

                            <?php
                                } elseif (!empty($header_social['icon'])) { ?>

                                    <span><?php echo $header_social['icon']?></span>

                                <?php }

                            ?>

                        </a>
                    </div>
                </div>
                <div class="icon-menu"></div>
            </div>
        </div>
	</header>

