<?php
$fields = $args['fields'];
$title = $fields['title'];
$subtitle = $fields['subtitle'];
$text = $fields['text'];
$endorsed = $fields['endorsed'];
$id = $fields['id'];

?>

<section class="section section-mission" <?php if(!empty($id)) { ?> id="<?php echo $id ?>"<?php } ?>>
    <div class="size-main">
        <div class="section-mission__wrapper">
            <div class="section-mission__subtitle">
                <span class="subtitle"><?php echo $subtitle ?></span>
            </div>
            <div class="section-mission__content">
                <h2 class="section-mission__title title"><?php echo $title ?></h2>
                <div class="section-mission__description">
                    <div class="section-mission__endorsed">
                        <span class="endorsed-title"><?php echo $endorsed['title'] ?></span>
                        <div class="endorsed-image">
                            <img src="<?php echo $endorsed['image']['url'] ?>" alt="<?php echo $endorsed['image']['title'] ?>">
                        </div>
                    </div>
                    <div class="section-mission__text text">
                        <div class="section-mission__text-inner content-for-show-more">

                            <?php echo $text ?>

                        </div>
                        <div class="show-more">
                            <span class="show-more__name">
                                <span class="show-more__name-more">Show more</span>
                                <span class="show-more__name-less">Show less</span>
                            </span>
                            <div class="show-more__icon">
                                <svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 1L5 5L9 1" stroke="#0F1010" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>