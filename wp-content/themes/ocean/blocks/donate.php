<?php
$fields = $args['fields'];
$title = $fields['title'];
$image = $fields['image'];

?>

<section class="section section-donate">
    <div class="size-main">
        <div class="section-donate__wrapper">
<!--            <h2 class="section-donate__title title">--><?php //echo $title ?><!--</h2>-->
            <div class="section-donate__form">
                <?php echo do_shortcode( '[wpforms id="798"]' ); ?>
            </div>
        </div>
    </div>
</section>