<?php
$fields = $args['fields'];
$subtitle = $fields['subtitle'];
$title = $fields['title'];
$items = $fields['items'];
$id = $fields['id'];

?>

<section class="section section-events section-fundraise" <?php if(!empty($id)) { ?> id="<?php echo $id ?>"<?php } ?>>
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
                        $contribution = $item['сontribution'];
                        $image = $item['image'];
                        $text = $item['text'];
                        $link = $item['link'];
                        $color_hover_link = $item['color_hover_link'];
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
                        if($main_background_color === 'Coral' && $use_custom_bg === false) {?>bg-coral<?php } ?>" style="<?php if($use_custom_bg === true) { ?>background-color:<?php echo $color_palette_bg; } ?>
                        <?php if($main_color_text === 'Black' && $use_custom_color === false) { ?>color: #0F1010;<?php } ?>
                        <?php if($main_color_text === 'White' && $use_custom_color === false) { ?>color: #fff;<?php } ?>
                        <?php if($main_color_text === 'Coral' && $use_custom_color === false) { ?>color: #DC6761;<?php } ?>
                        <?php if($use_custom_color === true) { ?>color:<?php echo $color_palette_text; } ?>;">

                            <?php if(!empty($image)) { ?>

                                <div class="section-events__item-image">
                                    <img src="<?php echo $image['url'] ?>" alt="<?php echo $image['title'] ?>">
                                </div>

                            <?php } ?>

                            <div class="section-events__item-content">
                                <div class="section-events__item-content-inner">
                                    <div class="section-events__item-title">

                                        <?php if(!empty($title)) { ?>

                                            <h3><?php echo strip_tags($title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?></h3>

                                        <?php } ?>

                                        <?php if(!empty($contribution)) { ?>

                                            <span class="item-сontribution">£<?php echo $contribution ?></span>

                                        <?php } ?>

                                    </div>

                                    <?php if(!empty($text)) { ?>

                                        <div class="section-events__item-text text" style="<?php if($use_custom_bg === true) { ?>background-color:<?php echo $color_palette_bg; } ?>
                                        <?php if($main_color_text === 'Black' && $use_custom_color === false) { ?>color: #0F1010;<?php } ?>
                                        <?php if($main_color_text === 'White' && $use_custom_color === false) { ?>color: #fff;<?php } ?>
                                        <?php if($main_color_text === 'Coral' && $use_custom_color === false) { ?>color: #DC6761;<?php } ?>
                                        <?php if($use_custom_color === true) { ?>color:<?php echo $color_palette_text; } ?>;"><?php echo $text ?></div>

                                    <?php } ?>

                                </div>

                                <?php if(!empty($link)) { ?>

                                    <a class="button-link <?php
                                    if($color_hover_link === 'Black => white') { ?>hover-black-white<?php } ?> <?php
                                    if($color_hover_link === 'White => black') { ?>hover-white-black<?php } ?> <?php
                                    if($color_hover_link === 'Coral => white') { ?>hover-coral-white<?php } ?> <?php
                                    if($color_hover_link === 'White => coral') { ?>hover-white-coral<?php } ?>" href="<?php echo $link['url'] ?>" style="<?php if($use_custom_bg === true) { ?>background-color:<?php echo $color_palette_bg; } ?>
                                    <?php if($main_color_text === 'Black' && $use_custom_color === false) { ?>color: #0F1010;<?php } ?>
                                    <?php if($main_color_text === 'White' && $use_custom_color === false) { ?>color: #fff;<?php } ?>
                                    <?php if($main_color_text === 'Coral' && $use_custom_color === false) { ?>color: #DC6761;<?php } ?>
                                    <?php if($use_custom_color === true) { ?>color:<?php echo $color_palette_text; } ?>;">

                                        <?php echo $link['title'] ?>

                                        <div class="button-link__icon">
                                            <svg width="29" height="24" viewBox="0 0 29 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M2 10.5C1.17157 10.5 0.5 11.1716 0.5 12C0.5 12.8284 1.17157 13.5 2 13.5L2 10.5ZM28.0607 13.0607C28.6464 12.4749 28.6464 11.5251 28.0607 10.9393L18.5147 1.3934C17.9289 0.807611 16.9792 0.807611 16.3934 1.3934C15.8076 1.97919 15.8076 2.92893 16.3934 3.51472L24.8787 12L16.3934 20.4853C15.8076 21.0711 15.8076 22.0208 16.3934 22.6066C16.9792 23.1924 17.9289 23.1924 18.5147 22.6066L28.0607 13.0607ZM2 13.5L27 13.5V10.5L2 10.5L2 13.5Z" fill="#1D252D"/>
                                            </svg>
                                        </div>
                                    </a>

                                <?php } ?>

                            </div>
                        </li>

                    <?php } ?>

                    </ul>

                <?php } ?>

        </div>
    </div>
</section>