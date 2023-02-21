<?php
$fields = $args['fields'];
$title = $fields['title'];
$text = $fields['text'];
$video = $fields['video'];
$id = $fields['id'];

?>

<section class="section-hero section-hero-video" <?php if(!empty($id)) { ?> id="<?php echo $id ?>"<?php } ?>>
    <div class="size-main">
        <div class="section-hero-video__wrapper">
            <div class="section-hero-video__headline">
                <h2 class="section-hero-video__title title-hero"><?php echo $title?></h2>
                <div class="section-hero-video__text text-hero"><?php echo $text?></div>
            </div>

            <?php if(!empty($video)) { ?>

                <div class="section-hero-video__video">
                    <div class="section-hero-video__video-inner">
                        <video autoplay muted playsinline loop>
                            <source src="<?php echo $video['url'] ?>" type="video/mp4">
                        </video>
                        <ul class="donuts">
                            <li class="donut">
                                <div class="donut-inner"></div>
                            </li>
                            <li class="donut">
                                <div class="donut-inner"></div>
                            </li>
                        </ul>
                    </div>
                </div>

            <?php } ?>

        </div>
    </div>
</section>