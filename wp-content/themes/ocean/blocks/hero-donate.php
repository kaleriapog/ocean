<?php
$fields = $args['fields'];
$title = $fields['title'];
$image = $fields['image'];
$id = $fields['id'];

?>

<section class="section-hero-donate" <?php if(!empty($id)) { ?> id="<?php echo $id ?>"<?php } ?>>

    <?php if(!empty($image)) { ?>

        <div class="section-hero-donate__image">
            <div class="section-hero-donate__image-inner">
                <img src="<?php echo $image['url'] ?>" alt="<?php echo $image['title'] ?>">
            </div>
            <div class="section-hero-donate__image-mask">
                <img src="<?php echo get_template_directory_uri() ?>/images/subtract.png" alt="subtract">
            </div>
        </div>

    <?php } ?>

    <div class="size-main">
        <div class="section-hero-donate__wrapper">

            <?php if(!empty($title)) { ?>

                <h1 class="section-hero-donate__title title-hero"><?php echo strip_tags($title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?></h1>

            <?php } ?>

        </div>
    </div>
</section>