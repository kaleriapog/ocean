<?php
$fields = $args['fields'];
$subtitle = $fields['subtitle'];
$title = $fields['title'];
$text = $fields['text'];
$data = $fields['workshop'][0]['events'][0]['data'];

?>

<section class="section section-full-events">
    <div class="size-main">
        <div class="section-full-events__wrapper">
            <span class="subtitle"><?php echo $subtitle?></span>
            <div class="section-full-events__content">
                <?php if(!empty($title)) { ?>

                    <div class="section-full-events__title">
                        <h2 class="title"><?php echo $title ?></h2>
                    </div>

                <?php } ?>
                <?php if(!empty($text)) { ?>

                    <div class="section-full-events__text text"><?php echo $text ?></div>

                <?php } ?>

            </div>

            <div class="section-full-events__content">
                <div class="section-full-events__left">
                    <div class="section-full-events__message"><?php echo $message ?></div>
                    <div class="author">
                        <div class="author__photo">
                            <?php echo $author['user_avatar'] ?>
                        </div>
                        <div class="author__info">
                            <div class="author__name"><?php echo $author['display_name'] ?></div>
                            <div class="author__description"><?php echo $author['user_description'] ?></div>
                        </div>
                    </div>
                </div>
                <div class="section-full-events__right">
                    <div class="section-full-events__text content-for-show-more"><?php echo $text ?></div>
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
            <div class="section-full-events__image">
                <img class="image-section" src="<?php echo $image['url'] ?>" alt="<?php echo $image['url'] ?>">
                <div class="section-full-events__decor-image">
                    <img src="<?php echo $image_decor['url'] ?>" alt="<?php echo $image_decor['url'] ?>">
                </div>
            </div>
        </div>
    </div>
</section>