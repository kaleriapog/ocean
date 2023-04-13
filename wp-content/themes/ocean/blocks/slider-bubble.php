<?php
$fields = $args['fields'];
$title = $fields['title'];
$slides = $fields['items'];
$text = $fields['text'];
$bg = $fields['background'];
$main_background_color = $bg['main_background_color'];
$use_custom_bg = $bg['use_custom_background_color'];
$color_palette_bg = $bg['custom_color_palette'];
$color_text = $fields['color_text'];
$main_color_text = $color_text['main_color'];
$use_custom_color = $color_text['use_custom_color'];
$color_palette_text = $color_text['custom_color_palette'];
$id = $fields['id'];

?>

<section class="section section-slider-bubble <?php
    if($main_background_color === 'Black' && $use_custom_bg === false) {?>bg-black<?php } ?><?php
    if($main_background_color === 'White' && $use_custom_bg === false) {?>bg-white<?php } ?><?php
    if($main_background_color === 'Coral' && $use_custom_bg === false) {?>bg-coral<?php } ?>"
    <?php if(!empty($id)) { ?> id="<?php echo $id ?>"<?php } ?>
        style="<?php if($use_custom_bg === true) { ?>background-color:<?php echo $color_palette_bg; } ?>;
    <?php if($main_color_text === 'Black' && $use_custom_color === false) { ?>color: #0F1010;<?php } ?>;
    <?php if($main_color_text === 'White' && $use_custom_color === false) { ?>color: #fff;<?php } ?>;
    <?php if($main_color_text === 'Coral' && $use_custom_color === false) { ?>color: #DC6761;<?php } ?>;
    <?php if($use_custom_color === true) { ?>color:<?php echo $color_palette_text; } ?>;">
    <div class="size-main">
        <div class="section-slider-bubble__wrapper">
            <div class="section-slider-bubble__headline">

                <?php if(!empty($title)) { ?>

                    <h2 class="section-slider-bubble__title title" style="
                    <?php if($main_color_text === 'Black' && $use_custom_color === false) { ?>color: #0F1010;<?php } ?>;
                    <?php if($main_color_text === 'White' && $use_custom_color === false) { ?>color: #fff;<?php } ?>;
                    <?php if($main_color_text === 'Coral' && $use_custom_color === false) { ?>color: #DC6761;<?php } ?>;
                    <?php if($use_custom_color === true) { ?>color:<?php echo $color_palette_text; } ?>;"><?php echo strip_tags($title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?></h2>

                <?php } ?>
                <?php if(!empty($text)) { ?>

                    <div class="section-slider-bubble__text text" style="
                    <?php if($main_color_text === 'Black' && $use_custom_color === false) { ?>color: #0F1010;<?php } ?>;
                    <?php if($main_color_text === 'White' && $use_custom_color === false) { ?>color: #fff;<?php } ?>;
                    <?php if($main_color_text === 'Coral' && $use_custom_color === false) { ?>color: #DC6761;<?php } ?>;
                    <?php if($use_custom_color === true) { ?>color:<?php echo $color_palette_text; } ?>;"><?php echo $text ?></div>

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

                        <div class="slider-bubble__content" style="
                        <?php if($main_color_text === 'Black' && $use_custom_color === false) { ?>color: #0F1010;<?php } ?>;
                        <?php if($main_color_text === 'White' && $use_custom_color === false) { ?>color: #fff;<?php } ?>;
                        <?php if($main_color_text === 'Coral' && $use_custom_color === false) { ?>color: #DC6761;<?php } ?>;
                        <?php if($use_custom_color === true) { ?>color:<?php echo $color_palette_text; } ?>;">

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