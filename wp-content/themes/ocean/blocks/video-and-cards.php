<?php
$fields = $args['fields'];
$title = $fields['title'];
$subtitle = $fields['subtitle'];
$video = $fields['video'];
$cards = $fields['cards'];
?>

<section class="section section-video-and-cards">
    <div class="size-main">
        <div class="section-video-and-cards__wrapper">
            <div class="section-video-and-cards__headline">
                <span class="subtitle"><?php echo $subtitle?></span>
                <h2 class="title"><?php echo $title?></h2>
            </div>

            <?php if(!empty($video)) : ?>

            <div class="section-video-and-cards__video">
                <div class="section-video-and-cards__video-inner">
                    <video autoplay muted loop>
                        <source src="<?php echo $video['url'] ?>" type="video/mp4">
                    </video>
                    <ul class="donuts">
                        <li class="donut">
                            <div class="donut-inner"></div>
                        </li>
                        <li class="donut">
                            <div class="donut-inner"></div>
                        </li>
                        <li class="donut">
                            <div class="donut-inner"></div>
                        </li>
                        <li class="donut">
                            <div class="donut-inner"></div>
                        </li>
                        <li class="donut">
                            <div class="donut-inner"></div>
                        </li>
                        <li class="donut">
                            <div class="donut-inner"></div>
                        </li>
                        <li class="donut">
                            <div class="donut-inner"></div>
                        </li>
                        <li class="donut">
                            <div class="donut-inner"></div>
                        </li>
                    </ul>
                </div>
            </div>

            <?php endif; ?>

            <?php if(!empty($cards)) : ?>

                <ul class="cards-regular">
                    <?php foreach ($cards as $item) {
                        $image = $item['icon'];
                        $title = $item['title'];
                        $text = $item['text'];
                    ?>

                    <li class="cards-regular__item">
                        <div class="cards-regular__icon">
                            <img src="<?php echo $image['url'] ?>" alt="<?php echo $image['title'] ?>">
                        </div>
                        <h3 class="cards-regular__title"><?php echo $title ?></h3>
                        <div class="cards-regular__text"><?php echo $text ?></div>
                    </li>

                    <?php } ?>

                </ul>

            <?php endif; ?>

        </div>
    </div>
</section>