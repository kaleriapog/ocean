<?php
$fields = $args['fields'];
$subtitle = $fields['subtitle'];
$title = $fields['title'];
$author = $fields['message']['author'];

?>

<section class="section section-about-program">
    <div class="size-main">
        <div class="section-about-program__wrapper">
            <span class="subtitle"><?php echo $subtitle?></span>

            <?php if(!empty($title)) { ?>

                <h2 class="section-about-program__title title"><?php echo $title ?></h2>

            <?php } ?>

            <div><?php echo $author['display_name'] ?></div>
            <div><?php echo $author['user_description'] ?></div>
            <div style="max-height: 30px; height: 100%; max-width: 30px;">
                <?php echo $author['user_avatar'] ?>
            </div>
        </div>
    </div>
</section>