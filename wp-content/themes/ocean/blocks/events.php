<?php
$fields = $args['fields'];
$subtitle = $fields['subtitle'];
$title = $fields['title'];
$items = $fields['items'];
$id = $fields['id'];

?>

<section class="section section-events" <?php if(!empty($id)) { ?> id="<?php echo $id ?>"<?php } ?>>
    <div class="size-main">
        <div class="section-events__wrapper">

            <?php if(!empty($subtitle)) { ?>

                <span class="subtitle"><?php echo $subtitle?></span>

            <?php } ?>


            <?php if(!empty($title)) { ?>

                <h2 class="section-events__title title"><?php echo strip_tags($title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?></h2>

            <?php } ?>
            <?php if(!empty($items)) { ?>

                <ul class="section-events__items">

                    <?php foreach ($items as $item) {
                        $title = $item['title'];
                        $image = $item['image'];
                        $text = $item['text'];
                        $links = $item['links'];
                        $bg = $item['background'];
                        $main_background_color = $bg['main_background_color'];
                        $use_custom_bg = $bg['use_custom_background_color'];
                        $color_palette_bg = $bg['custom_color_palette'];
                        $color_text = $item['color_text'];
                        $main_color_text = $color_text['main_color'];
                        $use_custom_color = $color_text['use_custom_color'];
                        $color_palette_text = $color_text['custom_color_palette'];

                        ?>

                        <li class="section-events__item <?php
                        if($main_background_color === 'Black' && $use_custom_bg === false) {?>bg-black<?php } ?><?php
                        if($main_background_color === 'White' && $use_custom_bg === false) {?>bg-white<?php } ?><?php
                        if($main_background_color === 'Coral' && $use_custom_bg === false) {?>bg-coral<?php } ?>"
                            style="<?php if($use_custom_bg === true) { ?>background-color:<?php echo $color_palette_bg; } ?>;
                            <?php if($main_color_text === 'Black' && $use_custom_color === false) { ?>color: #0F1010;<?php } ?>;
                            <?php if($main_color_text === 'White' && $use_custom_color === false) { ?>color: #fff;<?php } ?>;
                            <?php if($main_color_text === 'Coral' && $use_custom_color === false) { ?>color: #DC6761;<?php } ?>;
                            <?php if($use_custom_color === true) { ?>color:<?php echo $color_palette_text; } ?>;">

                            <?php if(!empty($image)) { ?>

                                <div class="section-events__item-image">
                                    <img src="<?php echo $image['url'] ?>" alt="<?php echo $image['title'] ?>">
                                </div>

                            <?php } ?>

                            <div class="section-events__item-content">
                                <div class="section-events__item-content-inner">

                                    <?php if(!empty($title)) { ?>

                                        <h3 class="section-events__item-title"><?php echo strip_tags($title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?></h3>

                                    <?php } ?>

                                    <?php if(!empty($text)) { ?>

                                        <div class="section-events__item-text text" style="<?php if($main_color_text === 'Black' && $use_custom_color === false) { ?>color: #0F1010;<?php } ?>;
                                        <?php if($main_color_text === 'White' && $use_custom_color === false) { ?>color: #fff;<?php } ?>;
                                        <?php if($main_color_text === 'Coral' && $use_custom_color === false) { ?>color: #DC6761;<?php } ?>;
                                        <?php if($use_custom_color === true) { ?>color:<?php echo $color_palette_text; } ?>;"><?php echo $text ?></div>

                                    <?php } ?>

                                </div>

                                <?php if(!empty($links)) { ?>

                                    <ul class="section-events__item-links">

                                        <?php foreach ($links as $item) {
                                            $link = $item['link'];
                                        ?>

                                        <li class="section-events__item-link"><a href="<?php echo $link['url'] ?>"><?php echo $link['title'] ?></a></li>

                                        <?php } ?>

                                    </ul>

                                <?php } ?>

                            </div>
                        </li>

                    <?php } ?>

                    </ul>

                <?php } ?>

        </div>
    </div>
</section>