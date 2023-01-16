<?php
$fields = $args['fields'];
$title = $fields['title'];
$link = $fields['link'];
$text = $fields['text'];
$image = $fields['image'];

?>

<section class="section-donate">
    <div class="size-main">
        <div class="section-donate__wrapper">
            <div class="section-donate__content">
                <h2 class="title"><?php echo $title ?></h2>
                <div class="section-donate__description">
                    <a class="button-link button-link-white" href="<?php echo $link['url'] ?>">

                        <?php echo $link['title'] ?>

                        <div class="button-link__icon">
                            <svg width="29" height="24" viewBox="0 0 29 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2 10.5C1.17157 10.5 0.5 11.1716 0.5 12C0.5 12.8284 1.17157 13.5 2 13.5L2 10.5ZM28.0607 13.0607C28.6464 12.4749 28.6464 11.5251 28.0607 10.9393L18.5147 1.3934C17.9289 0.807611 16.9792 0.807611 16.3934 1.3934C15.8076 1.97919 15.8076 2.92893 16.3934 3.51472L24.8787 12L16.3934 20.4853C15.8076 21.0711 15.8076 22.0208 16.3934 22.6066C16.9792 23.1924 17.9289 23.1924 18.5147 22.6066L28.0607 13.0607ZM2 13.5L27 13.5V10.5L2 10.5L2 13.5Z" fill="#1D252D"/>
                            </svg>
                        </div>
                    </a>
                    <div class="section-donate__text text"><?php echo $text ?></div>
                </div>
            </div>
            <div class="section-donate__image">
                <img src="<?php echo $image['url'] ?>" alt="<?php echo $image['title'] ?>">
            </div>
        </div>
    </div>

</section>
