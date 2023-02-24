<?php
$fields = $args['fields'];
$cards = $fields['cards'];
$bg_color = $fields['background_color'];
$endorsed = $fields['endorsed'];
$id = $fields['id'];

?>

<section class="section-cards-samples" style="background-color: <?php echo $bg_color ?>" <?php if(!empty($id)) { ?> id="<?php echo $id ?>"<?php } ?>>
    <div class="size-main">
        <ul class="section-cards-samples__list">

            <?php foreach($cards as $key=>$card) {
                $title = $card['title'];
                $text = $card['text'];

            ?>

                <li class="section-cards-samples__item">

                    <?php if(!empty($title)) { ?>

                        <div class="section-cards-samples__title">
                            <h3 class="title-card"><?php echo strip_tags($title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?></h3>
                            <span class="card-line"></span>
                        </div>

                    <?php } ?>
                    <?php if(!empty($text)) { ?>

                        <div class="section-cards-samples__text text"><?php echo $text ?></div>

                    <?php } ?>

                </li>

            <?php } ?>

        </ul>

        <?php if(!empty($endorsed['image'])) { ?>

        <div class="section-mission__endorsed">

            <?php if(!empty($endorsed['title'])) { ?>

                <span class="endorsed-title">

                    <?php echo $endorsed['title'] ?>

                </span>

            <?php } ?>
            <?php if(!empty($endorsed['image'])) { ?>

                <div class="endorsed-image">
                    <img src="<?php echo $endorsed['image']['url'] ?>" alt="<?php echo $endorsed['image']['title'] ?>">
                </div>

            <?php } ?>

        </div>

        <?php } ?>

    </div>
</section>