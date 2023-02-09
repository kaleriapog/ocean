<?php
$fields = $args['fields'];
$title = $fields['title'];
$text = $fields['text'];
$sliders = $fields['team'];
$slider_left = $sliders['slider_left'];
$slider_right = $sliders['slider_right'];

?>

<section class="section section-team">
    <div class="size-main">
        <div class="section-team__wrapper">
            <div class="section-team__headline">
                <h2 class="title"><?php echo $title ?></h2>
                <div class="section-team__text text-medium"><?php echo $text ?></div>
            </div>
            <div class="section-team__sliders">

                <?php if(!empty($slider_left)) { ?>

                    <div class="slider-left team-slider team-slider-left swiper">
                        <div class="swiper-wrapper">

                            <?php for ($i = 1; $i <= 2; $i++) { ?>

                                <?php foreach ($slider_left as $key=>$item) {
                                    $image = $item['photo'];
                                    $name = $item['name'];
                                    $position = $item['position'];

                                    ?>

                                    <div class="swiper-slide team-slider__item">
                                        <div class="team-slider__slide-inner">
                                            <div class="team-slider__slide-image">
                                                <img src="<?php echo $image['url'] ?>" alt="<?php echo $image['title'] ?>">
                                            </div>
                                            <div class="team-slider__slide-text">
                                                <span class="team-slider__slide-name"><?php echo $name ?></span>
                                                <span class="team-slider__slide-position text"><?php echo $position ?></span>
                                            </div>
                                        </div>
                                    </div>

                                <?php } ?>
                            <?php } ?>

                        </div>
                    </div>

                <?php } ?>
                <?php if(!empty($slider_right)) { ?>

                    <div class="slider-right team-slider team-slider-right swiper">
                        <div class="swiper-wrapper">

                            <?php for ($i = 1; $i <= 2; $i++) { ?>

                                <?php foreach ($slider_right as $item) {
                                $image = $item['photo'];
                                $name = $item['name'];
                                $position = $item['position'];
                                ?>

                                <div class="swiper-slide team-slider__item">
                                    <div class="team-slider__slide-inner">
                                        <div class="team-slider__slide-image">
                                            <img src="<?php echo $image['url'] ?>" alt="<?php echo $image['title'] ?>">
                                        </div>
                                        <div class="team-slider__slide-text">
                                            <span class="team-slider__slide-name"><?php echo $name ?></span>
                                            <span class="team-slider__slide-position text"><?php echo $position ?></span>
                                        </div>
                                    </div>
                                </div>

                            <?php } ?>
                            <?php } ?>

                        </div>
                    </div>

                <?php } ?>

            </div>
        </div>
    </div>

</section>