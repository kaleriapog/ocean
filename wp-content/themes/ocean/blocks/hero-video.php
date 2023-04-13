<?php
$fields = $args['fields'];
$title = $fields['title'];
$text = $fields['text'];
$video = $fields['video'];
$show_mask_with_portholes = $fields['show_mask_with_portholes'];
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

<section class="section-hero section-hero-video <?php
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
        <div class="section-hero-video__wrapper">
            <div class="section-hero-video__headline">

                <?php if(!empty($title)) { ?>

                    <h2 class="section-hero-video__title title-hero"><?php echo strip_tags($title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?></h2>

                <?php } ?>
                <?php if(!empty($text)) { ?>

                    <div class="section-hero-video__text text-hero"><?php echo $text?></div>

                <?php } ?>

            </div>

            <?php if(!empty($video)) { ?>

                <div class="section-hero-video__video">
                    <div class="section-hero-video__video-inner">
                        <video autoplay muted playsinline loop>
                            <source src="<?php echo $video['url'] ?>" type="video/mp4">
                        </video>

                        <?php if($show_mask_with_portholes === true) { ?>

                            <ul class="donuts">
                                <li class="donut">
                                    <div class="donut-inner" style="<?php if($use_custom_bg === true) { ?>border-color:<?php echo $color_palette_bg; } ?>;"></div>
                                </li>
                                <li class="donut">
                                    <div class="donut-inner" style="<?php if($use_custom_bg === true) { ?>border-color:<?php echo $color_palette_bg; } ?>;"></div>
                                </li>
                            </ul>

                        <?php } ?>

                    </div>
                </div>

            <?php } ?>

        </div>
    </div>
</section>