<?php
$fields = $args['fields'];
$headline = $fields['headline'];
$title = $headline['title'];
$text = $headline['text'];
$icon_before_title = $headline['icon_before_title'];
$image = $fields ['image'];
$id = $fields['id'];

?>

<section class="section section-form-simple" <?php if(!empty($id)) { ?> id="<?php echo $id ?>"<?php } ?>>
    <div class="size-main">
        <div class="section-form-simple__wrapper">

            <?php if(!empty($image)) { ?>

            <div class="section-form-simple__image">
                <img src="<?php echo $image['url'] ?>" alt="<?php echo $image['title'] ?>">
            </div>

            <?php } ?>

            <div class="section-form-simple__headline">
                <h2 class="section-form-simple__title title-hero">

                    <?php echo $title?>

                    <img class="section-form-simple__title-image" src="<?php echo $icon_before_title['url'] ?>" alt="<?php echo $icon_before_title['title'] ?>">
                </h2>
            </div>
            <div class="section-form-simple__content">
                <div class="section-form-simple__form form-regular">

                    <?php echo do_shortcode('[contact-form-7 id="904" title="Form Simple"]') ?>

                </div>
                <div class="section-form-simple__button button-link button-link-white">
                    <div class="button-link__icon trigger-open-form-simple">
                        <svg width="16" height="22" viewBox="0 0 16 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 1.15234C7 0.600059 7.44772 0.152344 8 0.152344C8.55228 0.152344 9 0.600059 9 1.15234H7ZM8.70711 21.5518C8.31658 21.9423 7.68342 21.9423 7.29289 21.5518L0.928932 15.1878C0.538408 14.7973 0.538408 14.1641 0.928932 13.7736C1.31946 13.3831 1.95262 13.3831 2.34315 13.7736L8 19.4304L13.6569 13.7736C14.0474 13.3831 14.6805 13.3831 15.0711 13.7736C15.4616 14.1641 15.4616 14.7973 15.0711 15.1878L8.70711 21.5518ZM9 1.15234L9 20.8447H7L7 1.15234H9Z" fill="white"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>