<?php
$fields = $args['fields'];
$cards = $fields['cards'];
$id = $fields['id'];

?>

<section class="section section-program-preview" <?php if(!empty($id)) { ?> id="<?php echo $id ?>"<?php } ?>>
    <div class="size-small">
        <div class="section-program-preview__wrapper">

            <?php if(!empty($cards)) { ?>

                <ul class="section-program-preview__list">

                <?php foreach ($cards as $item) {
                    $image = $item['image'];
                    $title = $item['title'];
                    $text = $item['text'];
                    $link = $item['link'];
                    $color_hover_link = $item['color_hover_link'];
                    $decor = $item['decor_image'];
                    ?>

                    <li class="section-program-preview__item">
                        <div class="section-program-preview__item-content">

                            <?php if(!empty($title)) { ?>

                                <h3 class="section-program-preview__title"><?php echo strip_tags($title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?></h3>

                            <?php } ?>
                            <?php if(!empty($text)) { ?>

                                <div class="section-program-preview__text text"><?php echo $text ?></div>

                            <?php } ?>
                            <?php if(!empty($link)) { ?>

                                <a class="button-link <?php
                                if($color_hover_link === 'Black => white') { ?>hover-black-white<?php } ?> <?php
                                if($color_hover_link === 'White => black') { ?>hover-white-black<?php } ?> <?php
                                if($color_hover_link === 'Coral => white') { ?>hover-coral-white<?php } ?> <?php
                                if($color_hover_link === 'White => coral') { ?>hover-white-coral<?php } ?>" href="<?php echo $link['url'] ?>">

                                <?php echo $link['title'] ?>

                                <div class="button-link__icon">
                                    <svg width="29" height="24" viewBox="0 0 29 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2 10.5C1.17157 10.5 0.5 11.1716 0.5 12C0.5 12.8284 1.17157 13.5 2 13.5L2 10.5ZM28.0607 13.0607C28.6464 12.4749 28.6464 11.5251 28.0607 10.9393L18.5147 1.3934C17.9289 0.807611 16.9792 0.807611 16.3934 1.3934C15.8076 1.97919 15.8076 2.92893 16.3934 3.51472L24.8787 12L16.3934 20.4853C15.8076 21.0711 15.8076 22.0208 16.3934 22.6066C16.9792 23.1924 17.9289 23.1924 18.5147 22.6066L28.0607 13.0607ZM2 13.5L27 13.5V10.5L2 10.5L2 13.5Z" fill="#1D252D"/>
                                    </svg>
                                </div>
                            </a>

                            <?php } ?>

                        </div>

                        <?php if(!empty($image)) { ?>

                            <div class="section-program-preview__item-image">
                                <img class="item-image" src="<?php echo $image['url'] ?>" alt="<?php echo $image['title'] ?>">

                                <?php if(!empty($decor)) : ?>

                                <div class="section-program-preview__item-decor">
                                    <img src="<?php echo $decor['url'] ?>" alt="<?php echo $decor['title'] ?>">
                                </div>

                            <?php endif ?>

                            </div>

                        <?php } ?>

                    </li>

                <?php } ?>

            </ul>

            <?php } ?>

        </div>
    </div>
</section>
