<?php
$fields = $args['fields'];
$subtitle = $fields['subtitle'];
$title = $fields['title'];
$text = $fields['text'];
$video = $fields['video'];
$position_text = $fields['position_text'];

?>

<section class="section section-video">
    <div class="size-main">
        <div class="section-video__wrapper <?php if($position_text === true) { echo 'text-order';} ?>">
            <span class="subtitle"><?php echo $subtitle?></span>
            <div class="section-video__title">
                <h2 class="title"><?php echo $title ?></h2>
            </div>
            <div class="section-video__video">
                <video class="video" playsinline>
                    <source src="<?php echo $video['url'] ?>" type="video/mp4">
                </video>
                <div class="button-video">
                    <span class="button-video__play">Play</span>
                    <span class="button-video__pause">Pause</span>
                </div>
            </div>
            <div class="section-video__text text"><?php echo $text ?></div>
        </div>
    </div>
</section>