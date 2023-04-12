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

                <?php if(!empty($title)) { ?>

                    <h2 class="title"><?php echo strip_tags($title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?></h2>

               <?php } ?>

            </div>

                <?php if(!empty($items)) { ?>

                    <ul class="section-logos__list">

                    <?php foreach ($items as $key=>$item) {
                        $image = $item['item'];
                        $link = $item['link'];

                        ?>

                        <li class="section-logos__item">
                            <a class="section-logos__item-link" href="<?php echo $link ?>">

                                <?php if(!empty($image)) { ?>

                                    <div class="section-logos__item-image">
                                        <img src="<?php echo $image['url'] ?>" alt="<?php echo $image['title'] ?>">
                                    </div>

                                <?php } ?>

                            </a>

                        </li>

                    <?php } ?>

                    </ul>

                <?php } ?>

            </div>
        </div>
    </div>
</section>