<?php
$fields = $args['fields'];
$title = $fields['title'];
$text = $fields['text'];
$code = $fields['code'];
$id = $fields['id'];
$bg = $fields['background'];
$main_background_color = $bg['main_background_color'];
$use_custom_bg = $bg['use_custom_background_color'];
$color_palette_bg = $bg['custom_color_palette'];
$color_text = $fields['color_text'];
$main_color_text = $color_text['main_color'];
$use_custom_color = $color_text['use_custom_color'];
$color_palette_text = $color_text['custom_color_palette'];

?>

<section class="section section-simple section-simple-embed <?php
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
        <div class="section-simple__wrapper">

            <?php if(!empty($title) || !empty($text)) { ?>

                <div class="section-simple__headline">

                    <?php if(!empty($title)) { ?>

                        <h2 class="section-simple__title title"><?php echo strip_tags($title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?></h2>

                    <?php } ?>
                    <?php if(!empty($text)) { ?>

                        <div class=" section-simple__text text"><?php echo $text?></div>

                    <?php } ?>

                </div>

            <?php } ?>

            <?php if(!empty($code)) { ?>
                <div class="custom-embed">
                    <?php echo $code ?>
                </div>

            <?php } ?>

        </div>
    </div>
</section>