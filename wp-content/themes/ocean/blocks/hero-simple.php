<?php
$fields = $args['fields'];
$title = $fields['title'];
$text = $fields['text'];
$image = $fields['image'];

?>

<section class="section-hero section-hero-simple">
    <div class="size-main">
        <div class="section-hero-simple__wrapper">
            <h2 class="section-hero-simple__title title-hero"><?php echo $title?></h2>
            <div class="section-hero-simple__text text-hero"><?php echo $text?></div>
            <div class="section-hero-simple__image">
                <img src="<?php echo $image['url']?>" alt="<?php echo $image['title']?>">
            </div>
        </div>
    </div>

</section>