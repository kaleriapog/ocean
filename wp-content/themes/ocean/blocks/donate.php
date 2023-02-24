<?php
$fields = $args['fields'];
$title = $fields['title'];
$image = $fields['image'];
$form_shortcode = $fields['form_shortcode'];

?>

<section class="section section-donate" <?php if(!empty($id)) { ?> id="<?php echo $id ?>"<?php } ?>>
    <div class="size-main">
        <div class="section-donate__wrapper">
            <div class="section-donate__form">
                <?php echo do_shortcode( $form_shortcode ); ?>
            </div>
        </div>
    </div>
</section>