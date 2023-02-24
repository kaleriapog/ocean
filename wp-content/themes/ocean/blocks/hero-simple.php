<?php
$fields = $args['fields'];
$title = $fields['title'];
$text = $fields['text'];
$image = $fields['image'];
$id = $fields['id'];

?>

<section class="section-hero section-hero-simple" <?php if(!empty($id)) { ?> id="<?php echo $id ?>"<?php } ?>>
    <div class="size-main">
        <div class="section-hero-simple__wrapper">

            <?php if(!empty($title)) { ?>

                <h2 class="section-hero-simple__title title-hero"><?php echo strip_tags($title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?></h2>

            <?php } ?>
            <?php if(!empty($text)) { ?>

                <div class="section-hero-simple__text text-hero"><?php echo $text?></div>

            <?php } ?>

            <?php if(!empty($image)) { ?>

                <div class="section-hero-simple__image">
                    <img src="<?php echo $image['url']?>" alt="<?php echo $image['title']?>">
                </div>

            <?php } ?>

        </div>
    </div>
</section>