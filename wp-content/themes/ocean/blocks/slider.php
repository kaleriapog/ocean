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
            <span class="subtitle"><?php echo $subtitle?></span>
            <div class="section-slider__headline">
                <div class="swiper-slide-inner">
                    <h2 class="title-big"><?php echo $title?></h2>
                </div>
            </div>
            <div class="swiper swiper-achievements">
                <div class="swiper-wrapper">
                    <div class="swiper-slide section-slider__headline">
                        <div class="swiper-slide-inner">
                            <h2 class="title-big"><?php echo $title?></h2>
                        </div>
                    </div>

                    <?php foreach ($slides as $item) {
                    $title = $item['title'];
                    $text = $item['text'];
                    ?>

                    <div class="swiper-slide section-slider__item">
                        <div class="swiper-slide-inner">
                            <h3 class="slide-title"><?php echo $title?></h3>
                            <span class="slide-text"><?php echo $text?></span>
                        </div>
                    </div>

                    <?php } ?>

                </div>
            </div>
        </div>
    </div>

</section>