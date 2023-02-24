<?php
$fields = $args['fields'];
$title = $fields['title'];
$slides = $fields['items'];
$text = $fields['text'];
$id = $fields['id'];

?>

<section class="section section-slider-bubble" <?php if(!empty($id)) { ?> id="<?php echo $id ?>"<?php } ?>>
    <div class="size-main">
        <div class="section-slider-bubble__wrapper">
            <div class="section-slider-bubble__headline">

                <?php if(!empty($title)) { ?>

                    <h2 class="section-slider-bubble__title title"><?php echo strip_tags($title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?></h2>

                <?php } ?>
                <?php if(!empty($text)) { ?>

                    <div class="section-slider-bubble__text text"><?php echo $text ?></div>

                <?php } ?>

            </div>
        </div>
    </div>

    <?php if(!empty($slides)) { ?>

        <div class="swiper-bubble swiper">
            <div class="swiper-wrapper">

            <?php foreach ($slides as $item) {
                $title = $item['title'];
                $text = $item['text'];
                $img = $item['image'];
                ?>

                <div class="swiper-slide section-slider-bubble__item">
                    <div class="slider-bubble__inner">
                        <?php if(!empty($img)) { ?>

                            <div class="slider-bubble__image">
                                <img src="<?php echo $img['url'] ?>" alt="<?php echo $img['title'] ?>">
                            </div>

                        <?php } ?>

                        <div class="slider-bubble__content">

                            <?php if(!empty($title)) { ?>

                                <h3 class="slider-bubble__title slide-title"><?php echo strip_tags($title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?></h3>

                            <?php } ?>
                            <?php if(!empty($text)) { ?>

                                <span class="slider-bubble__text"><?php echo $text?></span>

                            <?php } ?>

                        </div>

                    </div>
                </div>

            <?php } ?>

        </div>
        </div>

    <?php } ?>

</section>