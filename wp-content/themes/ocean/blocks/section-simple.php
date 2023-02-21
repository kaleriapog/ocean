<?php
$fields = $args['fields'];
$title = $fields['title'];
$text = $fields['text'];
$id = $fields['id'];

?>

<section class="section section-simple" <?php if(!empty($id)) { ?> id="<?php echo $id ?>"<?php } ?>>
    <div class="size-main">
        <div class="section-simple__wrapper">

            <?php if(!empty($title) || !empty($text)) { ?>

                <div class="section-simple__headline">

                    <?php if(!empty($title)) { ?>

                        <h2 class="section-simple__title title"><?php echo $title?></h2>

                    <?php } ?>
                    <?php if(!empty($text)) { ?>

                        <div class=" section-simple__text text"><?php echo $text?></div>

                    <?php } ?>

                </div>

            <?php } ?>

        </div>
    </div>
</section>