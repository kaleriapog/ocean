<?php
$fields = $args['fields'];
$title = $fields['title'];
$subtitle = $fields['subtitle'];
$slides = $fields['slides'];
$id = $fields['id'];

?>

<section class="section section-slider" <?php if(!empty($id)) { ?> id="<?php echo $id ?>"<?php } ?>>
    <div class="size-main">
        <div class="section-slider__wrapper">

            <?php if(!empty($subtitle)) { ?>

                <span class="subtitle"><?php echo $subtitle?></span>

            <?php } ?>
            <?php if(!empty($title)) { ?>

                <div class="section-slider__headline">
                    <div class="swiper-slide-inner">
                        <h2 class="title-big"><?php echo strip_tags($title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?></h2>
                    </div>
                </div>

            <?php } ?>

            <div class="swiper swiper-achievements">
                <div class="swiper-wrapper">
                    <div class="swiper-slide section-slider__headline">
                        <div class="swiper-slide-inner">

                            <?php if(!empty($title)) { ?>

                                <h2 class="title-big"><?php echo strip_tags($title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?></h2>

                            <?php } ?>

                        </div>
                    </div>

                    <?php if(!empty($slides)) { ?>

                        <?php foreach ($slides as $item) {
                        $title = $item['title'];
                        $text = $item['text'];
                            ?>

                            <div class="swiper-slide section-slider__item">
                            <div class="swiper-slide-inner">

                                <?php if(!empty($title)) { ?>

                                <h3 class="slide-title"><?php echo $title ?></h3>

                                <?php } ?>

                                <?php if(!empty($text)) { ?>

                                    <span class="slide-text"><?php echo $text ?></span>

                                <?php } ?>

                            </div>
                        </div>

                        <?php } ?>

                    <?php } ?>

                </div>
            </div>
        </div>
    </div>
</section>
