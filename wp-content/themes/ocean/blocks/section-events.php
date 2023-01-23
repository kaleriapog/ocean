<?php
$fields = $args['fields'];
$subtitle = $fields['subtitle'];
$title = $fields['title'];
$text = $fields['text'];
$data = $fields['workshop'][0]['events'][0]['data'];
$benefit = $fields['benefit'];
$benefit_title = $benefit['title'];
$benefit_list = $benefit['list'];
$workshop = $fields['workshop'];
?>

<section class="section section-full-events">
    <div class="size-main">
        <div class="section-full-events__wrapper">
            <span class="subtitle"><?php echo $subtitle?></span>
            <div class="section-full-events__content">
                <?php if(!empty($title)) { ?>

                    <div class="section-full-events__title">
                        <h2 class="title"><?php echo $title ?></h2>
                    </div>

                <?php } ?>
                <?php if(!empty($text)) { ?>

                    <div class="section-full-events__text text"><?php echo $text ?></div>

                <?php } ?>
                <?php if(!empty($benefit_title) || !empty($benefit_list)) { ?>

                    <h3 class="section-full-events__benefit-title"><?php echo $benefit_title ?></h3>
                    <ul class="section-full-events__benefit-list">

                        <?php foreach($benefit_list as $key=>$benefit) {
                            $item = $benefit['item'];
                            ?>

                            <li class="section-full-events__benefit-item"><?php echo $item ?></li>

                        <?php } ?>

                    </ul>

                <?php } ?>

            </div>
        </div>

        <?php if (!empty($workshop)) { ?>

            <div class="section-full-events__workshops">

                <?php foreach($workshop as $key=>$item) {
                    $title = $item['title'];
                    $speaker = $item['speaker'];
                    $events = $item['events'];
                ?>

                    <div class="section-full-events__workshop">
                        <div class="section-full-events__left">
                            <span class="section-full-events__workshop-title"><?php echo $title ?></span>
                            <span class="section-full-events__speaker-title"><?php  echo $speaker['title'] ?></span>
                            <div class="author">
                                <div class="author__photo">
                                    <img src="<?php echo $speaker['speaker']['image']['url'] ?>" alt="<?php echo $speaker['speaker']['image']['title'] ?>">
                                </div>
                                <div class="author__info">
                                    <div class="author__name"><?php echo $speaker['speaker']['name'] ?></div>
                                    <div class="author__description"><?php echo $speaker['speaker']['position'] ?></div>
                                </div>
                            </div>
                        </div>
                        <ul class="section-full-events__list">

                            <?php foreach($events as $key=>$event) {
                                $title = $event['title'];
                                $text = $event['text'];
                                $link = $event['link'];
                                $followers = $event['followers'];
                                $info = $event['info'];
                                $data = $event['data'];
                                $time = $event['time'];
                            ?>

                                <li class="event-item">
                                    <div class="event-item__top">
                                        <div class="event-item__headline">
                                            <h3 class="event-item__title"><?php echo $title ?></h3>
                                            <div class="event-item__text text"><?php echo $text ?></div>
                                        </div>
                                        <div class="event-item__data">
                                            <span class="event-item__data-title">Date and time</span>
                                            <span><?php echo $data ?></span>
                                            <span class="event-item__time">
                                                <span><?php echo $time['start'] ?> - <?php echo $time['end'] ?></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="event-item__bottom">
                                        <div class="event-item__bottom-inner">
                                            <div class="event-item__followers">
                                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M20 20C20 8.9543 11.0457 0 0 0V20C0 31.0457 8.9543 40 20 40C31.0457 40 40 31.0457 40 20V0C28.9543 0 20 8.9543 20 20Z" fill="#FFF7CF"/>
                                                </svg>
                                                <span class="event-item__followers-count"><?php echo $followers ?></span>
                                            </div>
                                            <span class="event-item__info"><?php echo $info ?></span>
                                        </div>
                                        <div class="event-item__button">
                                            <a class="button-link button-link-white" href="<?php echo $link['url'] ?>">

                                                <?php echo $link['title'] ?>

                                                <div class="button-link__icon">
                                                    <svg width="29" height="24" viewBox="0 0 29 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M2 10.5C1.17157 10.5 0.5 11.1716 0.5 12C0.5 12.8284 1.17157 13.5 2 13.5L2 10.5ZM28.0607 13.0607C28.6464 12.4749 28.6464 11.5251 28.0607 10.9393L18.5147 1.3934C17.9289 0.807611 16.9792 0.807611 16.3934 1.3934C15.8076 1.97919 15.8076 2.92893 16.3934 3.51472L24.8787 12L16.3934 20.4853C15.8076 21.0711 15.8076 22.0208 16.3934 22.6066C16.9792 23.1924 17.9289 23.1924 18.5147 22.6066L28.0607 13.0607ZM2 13.5L27 13.5V10.5L2 10.5L2 13.5Z" fill="#1D252D"/>
                                                    </svg>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </li>

                            <?php }  ?>

                        </ul>

                        <?php if($key === 1) { ?>

                            <div class="load-more">
                                <span class="load-more__inner">Load more</span>
                            </div>

                        <?php } ?>

                    </div>

                <?php } ?>

            </div>

        <?php } ?>

    </div>
</section>