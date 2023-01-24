<?php
$fields = $args['fields'];
$title = $fields['title'];
$text = $fields['text'];
$image = $fields['image'];
$main_title = $fields['main_title'];
$cards = $fields['cards'];
$bg_color = $fields['background_color'];

?>

<section class="section section-hero-animate-image">
    <div class="size-main">
        <div class="section-hero-animate-image__wrapper">
            <h1 class="section-hero-animate-image__title title-hero"><?php echo $title ?></h1>
            <div class="section-hero-animate-image__text text-hero"><?php echo $text ?></div>
            <div class="section-hero-animate-image__image">
                <div class="section-hero-animate-image__image-inner">
                    <img src="<?php echo $image['url'] ?>" alt="<?php echo $image['title'] ?>">
                </div>
                <h2 class="section-hero-animate-image__title-main title-hero"><?php echo $main_title ?></h2>
            </div>

            <?php if(!empty($cards)) { ?>

                <ul class="section-cards-samples__list">

                <?php foreach($cards as $key=>$card) {
                    $title = $card['title'];
                    $text = $card['text'];

                    ?>

                    <li class="section-cards-samples__item">
                        <div class="section-cards-samples__title">
                            <h3 class="title-card"><?php echo $title ?></h3>
                            <span class="card-line"></span>
                        </div>
                        <div class="section-cards-samples__text text"><?php echo $text ?></div>
                    </li>

                <?php } ?>

            </ul>

            <?php } ?>

        </div>
    </div>
</section>