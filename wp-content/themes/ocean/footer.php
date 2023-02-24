<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package ocean
 */

$social = get_field('social', 'options');
$footer = get_field('footer', 'options');
$form = $footer['form'];
$logo = $footer['logo'];
$address = $footer['address'];
$emails = $footer['email'];
$endorsed = $footer['endorsed'];
$text = $footer['text'];

?>

	<footer id="colophon" class="footer">
		<div class="size-main">
            <div class="footer__wrapper">
                <a class="footer-logo footer-logo-mobile" href="<?php echo get_option('home') ?>">
                    <img src="<?php echo $logo['url']; ?>" alt="<?php echo $logo['title']; ?>">
                </a>
                <div class="footer__top">
                    <div>
                        <?php echo do_shortcode( '[contact-form-7 id="71" html_class="footer__form" title="Subscribe form"]' ); ?>
                    </div>
                    <div class="footer__social social-desktop">

                        <?php if(!empty($social)) { ?>

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

                        <?php } ?>

                    </div>
                </div>
                <div class="footer__content">
                    <div class="footer__content-left">
                        <div class="footer__content-address">
                            <a class="footer-logo footer-logo-desktop" href="<?php echo get_option('home') ?>">
                                <img src="<?php echo $logo['url']; ?>" alt="<?php echo $logo['title']; ?>">
                            </a>

                            <?php if(!empty($address)) { ?>

                                <address class="address-text">

                                    <?php echo strip_tags($address, '<br>'); ?>

                                </address>

                            <?php } ?>

                        </div>

                        <?php if(!empty($emails)) { ?>

                            <ul class="footer__content-emails">

                            <?php foreach($emails as $key=>$item) {
                                $email = $item['email'];
                                $title = $item['title'];
                            ?>

                                <li class="footer-email">
                                    <span class="footer-email__title"><?php echo $title; ?></span>
                                    <a href="mailto:<?php echo $email; ?>" class="footer-email__link"><?php echo $email; ?></a>
                                </li>

                            <?php } ?>

                        </ul>

                        <?php } ?>

                        <div class="footer__bottom">

                            <?php if(!empty($endorsed)) { ?>

                                <div class="footer__endorsed">
                                <span class="endorsed-title"><?php echo $endorsed['title'] ?></span>
                                <div class="endorsed-image">
                                    <img src="<?php echo $endorsed['image']['url'] ?>" alt="<?php echo $endorsed['image']['title'] ?>">
                                </div>
                            </div>

                            <?php } ?>

                            <?php if(!empty($text )) { ?>

                                <div class="footer__text"><?php echo $text ?></div>

                            <?php } ?>

                        </div>
                    </div>
                    <div class="footer__content-right">
                        <nav class="footer__navigation">

                            <?php
                            wp_nav_menu(
                                array(
                                    'theme_location' => 'menu-footer',
                                    'menu_id'        => 'footer-menu',
                                    'menu_class'     => 'footer__menu-list'
                                )
                            );
                            ?>

                        </nav>
                    </div>
                    <div class="footer__social social-mobile">

                        <?php if(!empty($social)) { ?>

                        <ul class="social-list">
                            <?php foreach($social as $key=>$item) :
                                $url = $item['link'];
                                $icon = $item['icon'];
                                $name = $item['name'];
                                $custom_icon = $item['custom_icon'];
                                ?>

                                <li class="social-list__item <?php if(!empty($name)) { ?>item-name<?php } ?>">
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

                        <?php } ?>

                    </div>
                </div>
            </div>
		</div>
	</footer>
</div>

<?php wp_footer(); ?>

</body>
</html>
