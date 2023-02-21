<?php
$fields = $args['fields'];
$title = $fields['title'];
$text = $fields['text'];
$image = $fields['image'];
$link = $fields['link'];
$items = $fields['items'];
$subtitle = $fields['subtitle'];
$use_pop_up = $fields['use_pop_up'];
$id = $fields['id'];

?>

<section style="" class="section section-content-full section-with-pop-up" <?php if(!empty($id)) { ?> id="<?php echo $id ?>"<?php } ?>>
    <div class="size-main">
        <div class="section-content-full__wrapper">
            <span class="subtitle"><?php echo $subtitle?></span>

            <?php if(!empty($title)) { ?>

                <h1 class="section-content-full__title title"><?php echo $title ?></h1>

            <?php } ?>

            <div class="section-content-full__content">
                <div class="section-content-full__left">

                <?php if(!empty($image)) { ?>

                    <div class="section-content-full__image">
                        <img src="<?php echo $image['url'] ?>" alt="<?php echo $image['title'] ?>">
                    </div>

                <?php } ?>
                <?php if(!empty($link) && $use_pop_up === false) { ?>

                    <div class="section-content-full__link desktop">
                        <a class="button-link" href="<?php echo $link['url'] ?>">

                            <?php echo $link['title'] ?>

                            <div class="button-link__icon">
                                <svg width="29" height="24" viewBox="0 0 29 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2 10.5C1.17157 10.5 0.5 11.1716 0.5 12C0.5 12.8284 1.17157 13.5 2 13.5L2 10.5ZM28.0607 13.0607C28.6464 12.4749 28.6464 11.5251 28.0607 10.9393L18.5147 1.3934C17.9289 0.807611 16.9792 0.807611 16.3934 1.3934C15.8076 1.97919 15.8076 2.92893 16.3934 3.51472L24.8787 12L16.3934 20.4853C15.8076 21.0711 15.8076 22.0208 16.3934 22.6066C16.9792 23.1924 17.9289 23.1924 18.5147 22.6066L28.0607 13.0607ZM2 13.5L27 13.5V10.5L2 10.5L2 13.5Z" fill="#1D252D"/>
                                </svg>
                            </div>
                        </a>
                    </div>

                <?php } ?>

                <?php if($use_pop_up === true && !empty($link)) { ?>

                    <div class="section-content-full__link desktop">
                        <div class="button-link button-link-pop-up">

                            <?php echo $link['title'] ?>

                            <div class="button-link__icon">
                                <svg width="29" height="24" viewBox="0 0 29 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2 10.5C1.17157 10.5 0.5 11.1716 0.5 12C0.5 12.8284 1.17157 13.5 2 13.5L2 10.5ZM28.0607 13.0607C28.6464 12.4749 28.6464 11.5251 28.0607 10.9393L18.5147 1.3934C17.9289 0.807611 16.9792 0.807611 16.3934 1.3934C15.8076 1.97919 15.8076 2.92893 16.3934 3.51472L24.8787 12L16.3934 20.4853C15.8076 21.0711 15.8076 22.0208 16.3934 22.6066C16.9792 23.1924 17.9289 23.1924 18.5147 22.6066L28.0607 13.0607ZM2 13.5L27 13.5V10.5L2 10.5L2 13.5Z" fill="#1D252D"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                <?php } ?>

                </div>
                <div class="section-content-full__right">

                <?php if(!empty($text)) { ?>

                    <div class="section-content-full__text text"><?php echo $text ?></div>

                <?php } ?>
                <?php if(!empty($items)) { ?>

                    <ul class="section-content-full__items">

                        <?php foreach ($items as $item) {
                            $title_big = $item['title_big'];
                            $title_regular = $item['title_regular'];
                            $description = $item['description'];
                        ?>

                        <li class="section-content-full__item">
                            <span class="section-content-full__item-title-big"><?php echo $title_big ?></span>
                            <h3 class="section-content-full__item-title-regular"><?php echo $title_regular ?></h3>
                            <span class="section-content-full__item-description text"><?php echo $description ?></span>
                        </li>

                        <?php } ?>

                    </ul>

                <?php } ?>
                <?php if(!empty($link) && $use_pop_up === false) { ?>

                    <div class="section-content-full__link mobile">
                        <a class="button-link" href="<?php echo $link['url'] ?>">

                            <span><?php echo $link['title'] ?></span>

                            <div class="button-link__icon">
                                <svg width="29" height="24" viewBox="0 0 29 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2 10.5C1.17157 10.5 0.5 11.1716 0.5 12C0.5 12.8284 1.17157 13.5 2 13.5L2 10.5ZM28.0607 13.0607C28.6464 12.4749 28.6464 11.5251 28.0607 10.9393L18.5147 1.3934C17.9289 0.807611 16.9792 0.807611 16.3934 1.3934C15.8076 1.97919 15.8076 2.92893 16.3934 3.51472L24.8787 12L16.3934 20.4853C15.8076 21.0711 15.8076 22.0208 16.3934 22.6066C16.9792 23.1924 17.9289 23.1924 18.5147 22.6066L28.0607 13.0607ZM2 13.5L27 13.5V10.5L2 10.5L2 13.5Z" fill="#1D252D"/>
                                </svg>
                            </div>
                        </a>
                    </div>

                    <?php } ?>
                    <?php if($use_pop_up === true) { ?>
                        <div class="section-content-full__link mobile">
                            <div class="button-link button-link-pop-up">

                                <?php echo $link['title'] ?>

                                <div class="button-link__icon">
                                    <svg width="29" height="24" viewBox="0 0 29 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2 10.5C1.17157 10.5 0.5 11.1716 0.5 12C0.5 12.8284 1.17157 13.5 2 13.5L2 10.5ZM28.0607 13.0607C28.6464 12.4749 28.6464 11.5251 28.0607 10.9393L18.5147 1.3934C17.9289 0.807611 16.9792 0.807611 16.3934 1.3934C15.8076 1.97919 15.8076 2.92893 16.3934 3.51472L24.8787 12L16.3934 20.4853C15.8076 21.0711 15.8076 22.0208 16.3934 22.6066C16.9792 23.1924 17.9289 23.1924 18.5147 22.6066L28.0607 13.0607ZM2 13.5L27 13.5V10.5L2 10.5L2 13.5Z" fill="#1D252D"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <?php
        if($use_pop_up === true) {
            include get_theme_file_path( '/blocks/contact-pop-up.php' );
        }
    ?>

</section>
