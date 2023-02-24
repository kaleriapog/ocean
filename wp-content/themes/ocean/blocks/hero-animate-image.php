<?php
$fields = $args['fields'];
$title = $fields['title'];
$text = $fields['text'];
$image = $fields['image'];
$main_title = $fields['main_title'];
$cards = $fields['cards'];
$bg_color = $fields['background_color'];
$id = $fields['id'];

?>

<section class="section section-hero-animate-image" <?php if(!empty($id)) { ?> id="<?php echo $id ?>"<?php } ?>>
    <div class="size-main">
        <div class="section-hero-animate-image__wrapper">

            <?php if(!empty($title)) { ?>

                <h1 class="section-hero-animate-image__title title-hero"><?php echo strip_tags($title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?></h1>

            <?php } ?>

            <?php if(!empty($text)) { ?>

                <div class="section-hero-animate-image__text text-hero"><?php echo $text ?></div>

            <?php } ?>
            <?php if(!empty($image)) { ?>

                <div class="section-hero-animate-image__image">
                    <div class="section-hero-animate-image__image-inner">
                        <img src="<?php echo $image['url'] ?>" alt="<?php echo $image['title'] ?>">
                    </div>

                    <?php if(!empty($main_title)) { ?>

                        <h2 class="section-hero-animate-image__title-main title-hero"><?php echo strip_tags($main_title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?></h2>

                    <?php } ?>

                </div>

            <?php } ?>
            <?php if(!empty($cards)) { ?>

                <ul class="section-cards-samples__list">

                <?php foreach($cards as $key=>$card) {
                    $title = $card['title'];
                    $text = $card['text'];

                    ?>

                    <li class="section-cards-samples__item">

                        <?php if(!empty($title)) { ?>

                            <div class="section-cards-samples__title">
                                <h3 class="title-card"><?php echo strip_tags($title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?></h3>
                                <span class="card-line"></span>
                            </div>

                        <?php } ?>
                        <?php if(!empty($text)) { ?>

                            <div class="section-cards-samples__text text"><?php echo $text ?></div>

                        <?php } ?>

                    </li>

                <?php } ?>

            </ul>

            <?php } ?>

        </div>
    </div>
</section>