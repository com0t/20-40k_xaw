<?php
wp_register_script('rplg_js', plugins_url('/static/js/rplg.js', __FILE__));
wp_enqueue_script('rplg_js', plugins_url('/static/js/rplg.js', __FILE__));

if ($lazy_load_img) {
    wp_register_script('rplg_blazy', plugins_url('/static/js/blazy.min.js', __FILE__));
    wp_enqueue_script('rplg_blazy', plugins_url('/static/js/blazy.min.js', __FILE__));
}

include_once(dirname(__FILE__) . '/fbrev-reviews-helper.php');


$review_count = isset($facebook_count) ? $facebook_count : count($reviews);
if (isset($facebook_rating) && !$fb_rating_calc) {
    $rating = $facebook_rating;
} else {
    $rating = 0;
    $review_count = count($reviews);
    foreach ($reviews as $review) {
        if (isset($review->rating)) {
            $rating = $rating + $review->rating;
        } elseif (isset($review->recommendation_type)) {
            $rating = $rating + ($review->recommendation_type == 'negative' ? 1 : 5);
        } else {
            continue;
        }
    }
    $rating = round($rating / $review_count, 1);
    $rating = number_format((float)$rating, 1, '.', '');
}

$page_img = strlen($page_photo) > 0 ? $page_photo : 'https://graph.facebook.com/' . $page_id . '/picture';

if (is_numeric($max_width)) {
    $max_width = $max_width . 'px';
}
if (is_numeric($max_height)) {
    $max_height = $max_height . 'px';
}

$style = '';
if (isset($max_width) && strlen($max_width) > 0) {
    $style .= 'width:' . $max_width . '!important;';
}
if (isset($max_height) && strlen($max_height) > 0) {
    $style .= 'height:' . $max_height . '!important;overflow-y:auto!important;';
}
if ($centered) {
    $style .= 'margin:0 auto!important;';
}
?>

<div class="wp-fbrev wpac"<?php if ($style) { ?> style="<?php echo $style;?>"<?php } ?>>
    <div class="wp-facebook-list<?php if ($dark_theme) { ?> wp-dark<?php } ?>">
        <div class="wp-facebook-place">
            <?php fbrev_page($page_id, $page_name, $page_img, $rating, $review_count, $hide_based_on, $open_link, $nofollow_link); ?>
        </div>
        <?php if (!$hide_reviews) { ?>
        <div class="wp-facebook-content-inner">
            <?php fbrev_page_reviews($page_id, $reviews, $text_size, $pagination, $disable_user_link, $open_link, $nofollow_link, $lazy_load_img); ?>
        </div>
        <?php } ?>
    </div>
</div>