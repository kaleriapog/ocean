<?php
$fields = $args['fields'];
$title = $fields['title'];
$items = $fields['items'];
$id = $fields['id'];

?>

<section class="section section-logos" <?php if(!empty($id)) { ?> id="<?php echo $id ?>"<?php } ?>>
    <div class="size-main">
        <div class="section-logos__wrapper">
            <div class="section-logos__headline">
                <h2 class="title"><?php echo $title ?></h2>
            </div>

                <?php if(!empty($items)) { ?>

                    <ul class="section-logos__list">

                    <?php foreach ($items as $key=>$item) {
                        $image = $item['item'];

                        ?>

                        <li class="section-logos__item">
                            <div class="section-logos__item-image">
                                <img src="<?php echo $image['url'] ?>" alt="<?php echo $image['title'] ?>">
                            </div>
                        </li>

                    <?php } ?>

                    </ul>

                <?php } ?>

            </div>
        </div>
    </div>
</section>