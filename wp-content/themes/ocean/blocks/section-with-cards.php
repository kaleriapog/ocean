<?php
$fields = $args['fields'];
$subtitle = $fields['subtitle'];
$title = $fields['title'];
$text = $fields['text'];
$cards = $fields['cards'];

?>

<section class="section section-with-cards">
    <div class="size-main">
        <div class="section-with-cards__wrapper">
            <div class="section-with-cards__headline">
                <div class="section-with-cards__subtitle">
                    <span class="subtitle"><?php echo $subtitle ?></span>
                </div>
                <h2 class="section-with-cards__title title"><?php echo $title ?></h2>
            </div>
            <div class="section-with-cards__text text"><?php echo $text ?></div>
        </div>
        <ul class="section-with-cards__list">

            <?php foreach($cards as $key=>$card) {
                $title = $card['title'];
                $text = $card['text'];

                ?>

                <li class="section-with-cards__item">
                    <div class="section-with-cards__item-title">
                        <h3 class="title-big-card"><?php echo $title ?></h3>
                        <span class="card-line"></span>
                    </div>
                    <div class="section-with-cards__item-text text"><?php echo $text ?></div>
                </li>

            <?php } ?>

        </ul>
    </div>
</section>