<?php
$fields = $args['fields'];
$title = $fields['title'];
$image = $fields['image'];
$text = $fields['text'];
$form = $fields['form'];
$form_shortcode = $fields['form_shortcode'];
$id = $fields['id'];

?>

<section class="section section-contact" <?php if(!empty($id)) { ?> id="<?php echo $id ?>"<?php } ?>>
    <div class="size-main">
        <div class="section-contact__wrapper">
            <div class="section-contact__content">
                <div class="section-contact__headline">

                    <?php if(!empty($title)) { ?>

                        <h2 class="section-contact__title title-hero">

                            <?php echo $title?>

                            <img class="section-contact__title-image" src="<?php echo get_template_directory_uri() ?>/images/hand.png" alt="hand">
                        </h2>

                    <?php } ?>
                    <?php if(!empty($text)) { ?>

                        <div class="section-contact__text text"><?php echo $text?></div>

                    <?php } ?>

                </div>

                <?php if(!empty($image)) { ?>

                    <div class="section-contact__image">
                        <img class="image" src="<?php echo $image['url'] ?>" alt="<?php echo $image['title'] ?>">
                    </div>

                <?php } ?>

            </div>

            <?php if($form === true) { ?>

                <div class="section-contact__form form-regular">

                    <?php echo do_shortcode($form_shortcode) ?>

                </div>

            <?php } ?>
        </div>
    </div>
</section>
