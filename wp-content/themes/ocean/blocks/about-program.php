<?php
$fields = $args['fields'];
$subtitle = $fields['subtitle'];
$title = $fields['title'];
$text = $fields['text'];
$image = $fields['image'];
$image_decor = $fields['decor_image'];
$author = $fields['message']['author'];
$message = $fields['message']['text'];
$id = $fields['id'];

?>

<section class="section section-about-program" <?php if(!empty($id)) { ?> id="<?php echo $id ?>"<?php } ?>>
    <div class="size-main">
        <div class="section-about-program__wrapper">

            <?php if(!empty($subtitle)) { ?>

                <span class="subtitle"><?php echo $subtitle?></span>

            <?php } ?>

            <?php if(!empty($title)) { ?>

                <div class="section-about-program__title">
                    <h2 class="title"><?php echo strip_tags($title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?></h2>
                </div>

            <?php } ?>

            <div class="section-about-program__content">
                <div class="section-about-program__left">

                    <?php if(!empty($message)) { ?>

                        <div class="section-about-program__message"><?php echo $message ?></div>

                    <?php } ?>
                    <?php if(!empty($author)) { ?>

                    <div class="author">

                        <?php if(!empty($author['user_avatar'])) { ?>

                            <div class="author__photo">
                                <?php echo $author['user_avatar'] ?>
                            </div>

                        <?php } ?>

                        <div class="author__info">

                            <?php if(!empty($author['display_name'])) { ?>

                                <div class="author__name"><?php echo $author['display_name'] ?></div>

                            <?php } ?>
                            <?php if(!empty($author['user_description'])) { ?>

                                <div class="author__description"><?php echo $author['user_description'] ?></div>

                            <?php } ?>

                        </div>
                    </div>

                    <?php } ?>

                </div>
                <div class="section-about-program__right">

                    <?php if(!empty($text)) { ?>

                        <div class="section-about-program__text content-for-show-more"><?php echo $text ?></div>

                    <?php } ?>

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

            <?php if(!empty($image)) { ?>

            <div class="section-about-program__image">
                <img class="image-section" src="<?php echo $image['url'] ?>" alt="<?php echo $image['url'] ?>">

                <?php if(!empty($image_decor)) { ?>

                    <div class="section-about-program__decor-image">
                        <img src="<?php echo $image_decor['url'] ?>" alt="<?php echo $image_decor['url'] ?>">
                    </div>

                <?php } ?>

            </div>

            <?php } ?>

        </div>
    </div>
</section>