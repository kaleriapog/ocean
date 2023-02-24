<?php
$fields = $args['fields'];
$subtitle = $fields['subtitle'];
$title = $fields['title'];
$text = $fields['text'];
$images = $fields['images'];
$id = $fields['id'];

?>

<section class="section section-slider-horizontal" <?php if(!empty($id)) { ?> id="<?php echo $id ?>"<?php } ?>>
    <div class="size-main">
        <div class="section-slider-horizontal__wrapper">

            <div class="section-slider-horizontal__headline">

                <?php if(!empty($subtitle)) { ?>

                    <span class="subtitle"><?php echo $subtitle?></span>

                <?php } ?>

                <?php if(!empty($title)) { ?>

                    <h2 class="section-slider-horizontal__title title"><?php echo strip_tags($title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?></h2>

                <?php } ?>

            </div>

            <?php if(!empty($text)) { ?>

                <div class="section-slider-horizontal__text text"><?php echo $text ?></div>

            <?php } ?>

        </div>
    </div>

    <?php if(!empty($images)) { ?>

        <div class="section-slider-horizontal__items swiper">
            <ul class="section-slider-horizontal__list swiper-wrapper">

                <?php foreach ($images as $item) {
                    $image = $item['image'];

                    ?>

                    <li class="section-slider-horizontal__item swiper-slide">
                        <div class="section-slider-horizontal__item-image">
                            <img src="<?php echo $image['url'] ?>" alt="<?php echo $image['title'] ?>">
                        </div>
                    </li>

                <?php } ?>
                <?php foreach ($images as $item) {
                    $image = $item['image'];

                    ?>

                    <li class="section-slider-horizontal__item swiper-slide">
                        <div class="section-slider-horizontal__item-image">
                            <img src="<?php echo $image['url'] ?>" alt="<?php echo $image['title'] ?>">
                        </div>
                    </li>

                <?php } ?>

            </ul>
            <div class="button-slider" style="cursor: auto">
                <span class="button-slider__drag">Drag</span>
            </div>
        </div>

    <?php } ?>

</section>