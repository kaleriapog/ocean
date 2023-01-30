<?php
$fields = $args['fields'];
$title = $fields['title'];
$text = $fields['text'];
$form = $fields['form'];

?>

<section class="section section-contact">
    <div class="size-main">
        <div class="section-contact__wrapper">

            <?php if(!empty($title)) { ?>

                <h2 class="section-contact__title title-hero">

                    <?php echo $title?>

                    <img class="section-contact__title-image" src="<?php echo get_template_directory_uri() ?>/images/hand.png" alt="hand">
                </h2>

            <?php } ?>
            <?php if(!empty($text)) { ?>

                <div class="section-contact__text text"><?php echo $text?></div>

            <?php } ?>

            <?php if($form == true) { ?>

                <div class="section-contact__form form-regular">

                    <?php echo do_shortcode('[contact-form-7 id="770" title="Form Contact Page"]') ?>

                </div>

            <?php } ?>
        </div>
    </div>
</section>
