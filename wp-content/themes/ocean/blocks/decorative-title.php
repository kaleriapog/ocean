<?php
$fields = $args['fields'];
$title = $fields['title'];
$id = $fields['id'];

?>

<section class="section-decorative-title" <?php if(!empty($id)) { ?> id="<?php echo $id ?>"<?php } ?>>
    <div class="size-main">
        <div class="section-decorative-title__wrapper">

            <?php if(!empty($title)) { ?>

                <div class="section-decorative-title__title desktop">
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
                <div class="section-decorative-title__title mobile">
                <span class="decor decor-1"></span>
                <span><?php echo $title[0]['title_item'] ?></span>
                <span><?php echo $title[1]['title_item'] ?></span>
                <span><?php echo $title[2]['title_item'] ?></span>
                <span><?php echo $title[3]['title_item'] ?></span>
                <span class="decor decor-2"></span>
                <span><?php echo $title[4]['title_item'] ?></span><br>
                <span><?php echo $title[5]['title_item'] ?></span>
                <span><?php echo $title[6]['title_item'] ?></span><br>
                <span><?php echo $title[7]['title_item'] ?></span>
                <span class="decor decor-3"></span>
                <span class="decor decor-4"></span>
                <span><?php echo $title[8]['title_item'] ?></span>
            </div>

            <?php } ?>

        </div>
    </div>
</section>

