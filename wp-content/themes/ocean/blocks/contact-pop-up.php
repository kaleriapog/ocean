<?php
$fields = get_field('contact_pop_up_group', 'options');
$title = $fields['title'];
$text = $fields['text'];
$form = $fields['form'];
$id = $fields['id'];

?>

<div class="section section-contact section-contact-pop-up" <?php if(!empty($id)) { ?> id="<?php echo $id ?>"<?php } ?>>
    <div class="pop-up-close">
        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1.66602 1.66663L28.3327 28.3333" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M28.3327 1.66663L1.66602 28.3333" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>
    <div class="size-main">
        <div class="section-contact__wrapper ">

            <?php if(!empty($title)) { ?>

                <h2 class="section-contact__title title-hero">

                    <?php echo strip_tags($title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?>

                    <img class="section-contact__title-image" src="<?php echo get_template_directory_uri() ?>/images/hand.png" alt="hand">
                </h2>

            <?php } ?>
            <?php if(!empty($text)) { ?>

                <div class="section-contact__text text"><?php echo $text?></div>

            <?php } ?>

            <?php if($form == true) { ?>

                <div class="section-contact__form form-regular">

                    <?php echo do_shortcode('[contact-form-7 id="778" title="Form Contact Pop Up"]') ?>

                </div>

            <?php } ?>
        </div>
    </div>
</div>
