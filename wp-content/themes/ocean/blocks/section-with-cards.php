<?php
$fields = $args['fields'];
$subtitle = $fields['subtitle'];
$title = $fields['title'];
$text = $fields['text'];
$cards = $fields['cards'];
$id = $fields['id'];

?>

<section class="section section-with-cards" <?php if(!empty($id)) { ?> id="<?php echo $id ?>"<?php } ?>>
    <div class="size-main">
        <div class="section-with-cards__wrapper">
            <div class="section-with-cards__headline">
                <div class="section-with-cards__subtitle">

                    <?php if(!empty($subtitle)) { ?>

                        <span class="subtitle"><?php echo $subtitle ?></span>

                    <?php } ?>

                </div>

                <?php if(!empty($title)) { ?>

                    <h2 class="section-with-cards__title title"><?php echo strip_tags($title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?></h2>

                <?php } ?>

            </div>

            <?php if(!empty($text)) { ?>

                <div class="section-with-cards__text text"><?php echo $text ?></div>

            <?php } ?>

        </div>

        <?php if(!empty($cards)) { ?>

        <ul class="section-with-cards__list">

            <?php foreach($cards as $key=>$card) {
                $title = $card['title'];
                $text = $card['text'];

                ?>

                <li class="section-with-cards__item">
                    <div class="section-with-cards__item-title">

                        <?php if(!empty($title)) { ?>

                            <h3 class="title-big-card"><?php echo strip_tags($title, '<br>, <span>, <strong>, <mark>, <i>, <em>, <b>')?></h3>

                        <?php } ?>

                        <span class="card-line"></span>
                    </div>

                    <?php if(!empty($text)) { ?>

                        <div class="section-with-cards__item-text text"><?php echo $text ?></div>

                    <?php } ?>

                </li>

            <?php } ?>

        </ul>

        <?php } ?>

    </div>
</section>