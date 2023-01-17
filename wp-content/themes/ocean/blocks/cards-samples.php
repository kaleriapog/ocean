<?php
$fields = $args['fields'];
$cards = $fields['cards'];
$bg_color = $fields['background_color'];
$endorsed = $fields['endorsed'];

?>

<section class="section-cards-samples" style="background-color: <?php echo $bg_color ?>">
    <div class="size-main">
        <ul class="section-cards-samples__list">

            <?php foreach($cards as $key=>$card) {
                $title = $card['title'];
                $text = $card['text'];

            ?>

                <li class="section-cards-samples__item">
                    <div class="section-cards-samples__title">
                        <h3 class="title-card"><?php echo $title ?></h3>
                        <span class="card-line"></span>
                    </div>
                    <div class="section-cards-samples__text text"><?php echo $text ?></div>
                </li>

            <?php } ?>

        </ul>

        <?php if(!empty($endorsed)) { ?>

        <div class="section-mission__endorsed">
            <span class="endorsed-title"><?php echo $endorsed['title'] ?></span>
            <div class="endorsed-image">
                <img src="<?php echo $endorsed['image']['url'] ?>" alt="<?php echo $endorsed['image']['title'] ?>">
            </div>
        </div>

        <?php } ?>

    </div>
</section>