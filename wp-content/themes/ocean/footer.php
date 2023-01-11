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


?>

	<footer id="colophon" class="footer">
		<div class="size-main">
            <div class="footer__wrapper">
                <div class="footer__top">
                    <div>
                        <?php if(!empty($form)) {
                            echo $form;
                        } ?>
                    </div>
                    <div class="footer__social">
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
                </div>
                <div class="footer__content">

                </div>
                <div class="footer__bottom">

                </div>
            </div>
		</div>
	</footer>
</div>

<?php wp_footer(); ?>

</body>
</html>
