<?php
$fields = $args['fields'];
$title = $fields['title'];
$text = $fields['text'];
$sliders = $fields['team'];
$slider_left = $sliders['slider_left'];
$slider_right = $sliders['slider_right'];
$id = $fields['id'];

?>

<section class="section section-team" <?php if(!empty($id)) { ?> id="<?php echo $id ?>"<?php } ?>>
    <div class="size-main">
        <div class="section-team__wrapper">
            <div class="section-team__headline">

                <?php if(!empty($title)) { ?>

                    <h2 class="title"><?php echo strip_tags($title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?></h2>

                <?php } ?>

                <?php if(!empty($text)) { ?>

                    <div class="section-team__text text-medium"><?php echo $text ?></div>

                <?php } ?>

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

                                            <?php if(!empty($image)) { ?>

                                                <div class="team-slider__slide-image">
                                                    <img src="<?php echo $image['url'] ?>" alt="<?php echo $image['title'] ?>">
                                                </div>

                                            <?php } ?>

                                            <div class="team-slider__slide-text">

                                                <?php if(!empty($name)) { ?>

                                                    <span class="team-slider__slide-name"><?php echo $name ?></span>

                                                <?php } ?>
                                                <?php if(!empty($position)) { ?>

                                                    <span class="team-slider__slide-position text"><?php echo $position ?></span>

                                                <?php } ?>

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

                                        <?php if(!empty($image)) { ?>

                                            <div class="team-slider__slide-image">
                                                <img src="<?php echo $image['url'] ?>" alt="<?php echo $image['title'] ?>">
                                            </div>

                                        <?php } ?>

                                        <div class="team-slider__slide-text">

                                            <?php if(!empty($name)) { ?>

                                                <span class="team-slider__slide-name"><?php echo $name ?></span>

                                            <?php } ?>
                                            <?php if(!empty($position)) { ?>

                                                <span class="team-slider__slide-position text"><?php echo $position ?></span>

                                            <?php } ?>

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