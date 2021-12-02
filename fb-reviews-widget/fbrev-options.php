<button class="fbrev-connect"><?php echo fbrev_i('Log In with Facebook'); ?></button>

<div class="fbrev-pages"></div>

<?php global $wp_version; if (version_compare($wp_version, '3.5', '>=')) { wp_enqueue_media(); ?>
<div class="form-group">
    <img id="<?php echo $this->get_field_id('page_photo_img'); ?>" src="<?php echo $page_photo; ?>" alt="<?php echo $page_name; ?>" class="fbrev-page-photo-img" style="display:<?php if ($page_photo) { ?>inline-block<?php } else { ?>none<?php } ?>;width:32px;height:32px;border-radius:50%;">
    <a id="<?php echo $this->get_field_id('page_photo_btn'); ?>" href="#" class="fbrev-page-photo-btn"><?php echo fbrev_i('Change page photo'); ?></a>
    <input type="hidden" id="<?php echo $this->get_field_id('page_photo'); ?>" name="<?php echo $this->get_field_name('page_photo'); ?>" value="<?php echo $page_photo; ?>" class="fbrev-page-photo" tabindex="2"/>
</div>
<?php } ?>

<div class="form-group">
    <input type="text" id="<?php echo $this->get_field_id('page_name'); ?>" name="<?php echo $this->get_field_name('page_name'); ?>" value="<?php echo $page_name; ?>" class="fbrev-page-name" placeholder="<?php echo fbrev_i('Page Name'); ?>" readonly />
</div>

<div class="form-group">
    <input type="text" id="<?php echo $this->get_field_id('page_id'); ?>" name="<?php echo $this->get_field_name('page_id'); ?>" value="<?php echo $page_id; ?>" class="fbrev-page-id" placeholder="<?php echo fbrev_i('Page ID'); ?>" readonly />
</div>

<input type="hidden" id="<?php echo $this->get_field_id('page_access_token'); ?>" name="<?php echo $this->get_field_name('page_access_token'); ?>" value="<?php echo $page_access_token; ?>" class="fbrev-page-token" placeholder="<?php echo fbrev_i('Access token'); ?>" readonly />

<?php if (isset($title)) { ?>
<div class="form-group">
    <label><?php echo fbrev_i('Title'); ?></label>
    <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>"/>
</div>
<?php } ?>

<div class="form-group">
    <label><?php echo fbrev_i('Pagination'); ?></label>
    <input type="text" id="<?php echo $this->get_field_id('pagination'); ?>" name="<?php echo $this->get_field_name('pagination'); ?>" value="<?php echo $pagination; ?>"/>
</div>

<div class="form-group">
    <label><?php echo fbrev_i('Characters before \'read more\' link'); ?></label>
    <input type="text" id="<?php echo $this->get_field_id('text_size'); ?>" name="<?php echo $this->get_field_name('text_size'); ?>" value="<?php echo $text_size; ?>"/>
</div>

<div class="form-group">
    <label for="<?php echo $this->get_field_id('max_width'); ?>"><?php echo fbrev_i('Widget width'); ?></label>
    <input id="<?php echo $this->get_field_id('max_width'); ?>" name="<?php echo $this->get_field_name('max_width'); ?>" value="<?php echo $max_width; ?>" type="text" />
</div>

<div class="form-group">
    <label for="<?php echo $this->get_field_id('max_height'); ?>"><?php echo fbrev_i('Widget height'); ?></label>
    <input id="<?php echo $this->get_field_id('max_height'); ?>" name="<?php echo $this->get_field_name('max_height'); ?>" value="<?php echo $max_height; ?>" type="text" />
</div>

<div class="form-group">
    <label>
        <input id="<?php echo $this->get_field_id('hide_based_on'); ?>" name="<?php echo $this->get_field_name('hide_based_on'); ?>" type="checkbox" value="1" <?php checked('1', $hide_based_on); ?>/>
        <?php echo fbrev_i('Hide \'Based on ... reviews\''); ?>
    </label>
</div>

<div class="form-group">
    <label>
        <input id="<?php echo $this->get_field_id('hide_reviews'); ?>" name="<?php echo $this->get_field_name('hide_reviews'); ?>" type="checkbox" value="1" <?php checked('1', $hide_reviews); ?>/>
        <?php echo fbrev_i('Hide reviews, leave only rating header'); ?>
    </label>
</div>

<div class="form-group">
    <label>
        <input id="<?php echo $this->get_field_id('centered'); ?>" name="<?php echo $this->get_field_name('centered'); ?>" type="checkbox" value="1" <?php checked('1', $centered); ?>/>
        <?php echo fbrev_i('Place by center (only if Width is set)'); ?>
    </label>
</div>

<div class="form-group">
    <label>
        <input id="<?php echo $this->get_field_id('disable_user_link'); ?>" name="<?php echo $this->get_field_name('disable_user_link'); ?>" type="checkbox" value="1" <?php checked('1', $disable_user_link); ?>/>
        <?php echo fbrev_i('Disable user profile links'); ?>
    </label>
</div>

<div class="form-group">
    <label>
        <input id="<?php echo $this->get_field_id('dark_theme'); ?>" name="<?php echo $this->get_field_name('dark_theme'); ?>" type="checkbox" value="1" <?php checked('1', $dark_theme); ?>/>
        <?php echo fbrev_i('Dark background'); ?>
    </label>
</div>

<div class="rplg-options-toggle rplg-toggle"><?php echo fbrev_i('Advance Options'); ?></div>
<div class="rplg-options" style="display:none">
    <div class="form-group">
        <label>
            <input id="<?php echo $this->get_field_id('lazy_load_img'); ?>" name="<?php echo $this->get_field_name('lazy_load_img'); ?>" type="checkbox" value="1" <?php checked('1', $lazy_load_img); ?>/>
            <?php echo fbrev_i('Lazy load images'); ?>
        </label>
    </div>

    <div class="form-group">
        <label>
            <input id="<?php echo $this->get_field_id('show_success_api'); ?>" name="<?php echo $this->get_field_name('show_success_api'); ?>" type="checkbox" value="1" <?php checked('1', $show_success_api); ?>/>
            <?php echo fbrev_i('Show last success API response'); ?>
        </label>
    </div>

    <div class="form-group">
        <label>
            <input id="<?php echo $this->get_field_id('fb_rating_calc'); ?>" name="<?php echo $this->get_field_name('fb_rating_calc'); ?>" type="checkbox" value="1" <?php checked('1', $fb_rating_calc); ?>/>
            <?php echo fbrev_i('Calculate FB rating based on current reviews'); ?>
        </label>
        <span class="rplg-quest rplg-toggle" title="Click to help">?</span>
        <div style="display:none">The plugin gets a FB page rating from the FB Graph API, but sometime, this rating becomes outdated. This option calculates the rating manually based on current reviews/recommendations and keeps it up to date.</div>
    </div>

    <div class="form-group">
        <label>
            <input id="<?php echo $this->get_field_id('open_link'); ?>" name="<?php echo $this->get_field_name('open_link'); ?>" type="checkbox" value="1" <?php checked('1', $open_link); ?>/>
            <?php echo fbrev_i('Open links in new Window'); ?>
        </label>
    </div>

    <div class="form-group">
        <label>
            <input id="<?php echo $this->get_field_id('nofollow_link'); ?>" name="<?php echo $this->get_field_name('nofollow_link'); ?>" type="checkbox" value="1" <?php checked('1', $nofollow_link); ?>/>
            <?php echo fbrev_i('User no follow links'); ?>
        </label>
    </div>

    <div class="form-group">
        <label><?php echo fbrev_i('Reviews limit'); ?></label>
        <input id="<?php echo $this->get_field_id('api_ratings_limit'); ?>" name="<?php echo $this->get_field_name('api_ratings_limit'); ?>" value="<?php echo $api_ratings_limit; ?>" type="text" placeholder="By default: <?php echo FBREV_API_RATINGS_LIMIT; ?>"/>
    </div>

    <div class="form-group">
        <?php echo fbrev_i('Cache data'); ?>
        <select id="<?php echo $this->get_field_id('cache'); ?>" name="<?php echo $this->get_field_name('cache'); ?>">
            <option value="1" <?php selected('1', $cache); ?>><?php echo fbrev_i('1 Hour'); ?></option>
            <option value="3" <?php selected('3', $cache); ?>><?php echo fbrev_i('3 Hours'); ?></option>
            <option value="6" <?php selected('6', $cache); ?>><?php echo fbrev_i('6 Hours'); ?></option>
            <option value="12" <?php selected('12', $cache); ?>><?php echo fbrev_i('12 Hours'); ?></option>
            <option value="24" <?php selected('24', $cache); ?>><?php echo fbrev_i('1 Day'); ?></option>
            <option value="48" <?php selected('48', $cache); ?>><?php echo fbrev_i('2 Days'); ?></option>
            <option value="168" <?php selected('168', $cache); ?>><?php echo fbrev_i('1 Week'); ?></option>
        </select>
    </div>
</div>

<div class="form-group">
    <div class="rplg-pro">
        <?php echo fbrev_i('Try more features in the Business version: '); ?>
        <a href="https://richplugins.com/business-reviews-bundle-wordpress-plugin" target="_blank">
            <?php echo fbrev_i('Upgrade to Business'); ?>
        </a>
    </div>
</div>