<?php
$fields = $args['fields'];
$title = $fields['title'];
$image = $fields['image'];

?>

<section class="section-hero-donate">
    <div class="section-hero-donate__image">
        <div class="section-hero-donate__image-inner">
            <img src="<?php echo $image['url'] ?>" alt="<?php echo $image['title'] ?>">
        </div>
        <ul class="donuts">
            <li class="donut">
                <div class="donut-inner"></div>
            </li>
            <li class="donut">
                <div class="donut-inner"></div>
            </li>
        </ul>
    </div>
    <div class="size-main">
        <div class="section-hero-donate__wrapper">
            <h1 class="section-hero-donate__title title-hero"><?php echo $title ?></h1>
        </div>
    </div>
</section>