<?php
$fields = $args['fields'];
$title = $fields['title'];

?>

<section class="section-decorative-title">
    <div class="size-main">
        <div class="section-decorative-title__wrapper">
            <div class="section-decorative-title__title">

<!--                --><?php //foreach($title as $key=>$title) {
//                    $title = $title['title_item'];
//
//                ?>
<!---->
<!--                <span class="decor"></span>-->
<!--                <span>--><?php //echo $title ?><!--</span>-->
<!---->
<!--                --><?php //} ?>

                <div class="section-decorative-title__title-item">
                    <span class="decor decor-1"></span>
                    <span><?php echo $title[0]['title_item'] ?></span>
                    <span><?php echo $title[1]['title_item'] ?></span>
                    <span class="decor decor-2"></span>
                </div>
                <div class="section-decorative-title__title-item">
                    <span><?php echo $title[2]['title_item'] ?></span>
                    <span class="decor decor-3"></span>
                    <span><?php echo $title[3]['title_item'] ?></span>
                </div>
                <div class="section-decorative-title__title-item">
                    <span class="decor decor-4"></span>
                    <span><?php echo $title[4]['title_item'] ?></span>
                    <span><?php echo $title[5]['title_item'] ?></span>
                    <span><?php echo $title[6]['title_item'] ?></span>
                </div>
                <div class="section-decorative-title__title-item">
                    <span><?php echo $title[7]['title_item'] ?></span>
                    <span><?php echo $title[8]['title_item'] ?></span>
                    <span class="decor decor-5"></span>
                </div>
            </div>
        </div>
    </div>
</section>