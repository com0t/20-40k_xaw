<?php
/*
Plugin Name: Social Reviews & Recommendations
Plugin URI: https://richplugins.com/business-reviews-bundle-wordpress-plugin
Description: Allows you to instantly display Facebook reviews and recommendations on your site to increase user confidence and SEO.
Author: RichPlugins <support@richplugins.com>
Version: 1.7.6
Author URI: https://richplugins.com
*/

require(ABSPATH . 'wp-includes/version.php');

include_once(dirname(__FILE__) . '/api/urlopen.php');
include_once(dirname(__FILE__) . '/helper/debug.php');

define('FBREV_VERSION',            '1.7.6');
define('FBREV_GRAPH_API',          'https://graph.facebook.com/v10.0/');
define('FBREV_API_RATINGS_LIMIT',  '500');
define('FBREV_PLUGIN_URL',         plugins_url(basename(plugin_dir_path(__FILE__ )), basename(__FILE__)));
define('FBREV_AVATAR',             FBREV_PLUGIN_URL . '/static/img/avatar.png');

function fbrev_options() {
    return array(
        'fbrev_app_id',
        'fbrev_app_secret',
        'fbrev_version',
        'fbrev_active',
        'fbrev_activation_time',
        'fbrev_rev_notice_hide',
        'rplg_rev_notice_show',
    );
}

/*-------------------------------- Widget --------------------------------*/
function fbrev_init_widget() {
    if (!class_exists('Fb_Reviews_Widget')) {
        require 'fbrev-widget.php';
    }
}
add_action('widgets_init', 'fbrev_init_widget');

function fbrev_register_widget() {
    return register_widget("Fb_Reviews_Widget");
}
add_action('widgets_init', 'fbrev_register_widget');

/*-------------------------------- Menu --------------------------------*/
function fbrev_setting_menu() {
     add_submenu_page(
         'options-general.php',
         'Facebook Reviews',
         'Facebook Reviews',
         'moderate_comments',
         'fbrev',
         'fbrev_setting'
     );
}
add_action('admin_menu', 'fbrev_setting_menu', 10);

function fbrev_setting() {
    include_once(dirname(__FILE__) . '/fbrev-setting.php');
}

/*-------------------------------- Links --------------------------------*/
function fbrev_plugin_action_links($links, $file) {
    $plugin_file = basename(__FILE__);
    if (basename($file) == $plugin_file) {
        $settings_link = '<a href="' . admin_url('options-general.php?page=fbrev') . '">' . fbrev_i('Settings') . '</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
}
add_filter('plugin_action_links', 'fbrev_plugin_action_links', 10, 2);

/*-------------------------------- Row Meta --------------------------------*/
function fbrev_plugin_row_meta($input, $file) {
    if ($file != plugin_basename( __FILE__ )) {
        return $input;
    }

    $links = array(
        //'<a href="' . esc_url('https://richplugins.com') . '" target="_blank">' . fbrev_i('View Documentation') . '</a>',
        '<a href="' . esc_url('https://richplugins.com/business-reviews-bundle-wordpress-plugin') . '" target="_blank">' . fbrev_i('Upgrade to Business') . ' &raquo;</a>',
    );
    $input = array_merge($input, $links);
    return $input;
}
add_filter('plugin_row_meta', 'fbrev_plugin_row_meta', 10, 2);

/*-------------------------------- Activator --------------------------------*/
function fbrev_check_version() {
    if (version_compare(get_option('fbrev_version'), FBREV_VERSION, '<')) {
        fbrev_activate();
    }
}
add_action('init', 'fbrev_check_version');

function fbrev_activation($network_wide = false) {
    $now = time();
    update_option('fbrev_activation_time', $now);

    add_option('fbrev_is_multisite', $network_wide);
    fbrev_activate();
}
register_activation_hook(__FILE__, 'fbrev_activation');

function fbrev_activate() {
    $network_wide = get_option('fbrev_is_multisite');
    if ($network_wide) {
        fbrev_activate_multisite();
    } else {
        fbrev_activate_single_site();
    }
}

function fbrev_activate_multisite() {
    global $wpdb;

    $site_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

    foreach($site_ids as $site_id) {
        switch_to_blog($site_id);
        fbrev_activate_single_site();
        restore_current_blog();
    }
}

function fbrev_activate_single_site() {
    $current_version     = FBREV_VERSION;
    $last_active_version = get_option('fbrev_version');

    if (empty($last_active_version)) {
        fbrev_first_install();
        update_option('fbrev_version', $current_version);
    } elseif ($last_active_version !== $current_version) {
        fbrev_exist_install($current_version, $last_active_version);
        update_option('fbrev_version', $current_version);
    }
}

function fbrev_first_install() {
    add_option('fbrev_active', '1');
}

function fbrev_exist_install($current_version, $last_active_version) {
    global $wpdb;
    switch($last_active_version) {
        case version_compare($last_active_version, '1.6.6', '<'):
            //TODO
        break;
    }
}

/*-------------------------------- Shortcode --------------------------------*/
function fbrev_shortcode($atts) {
    global $wpdb;

    if (!fbrev_enabled()) return '';
    if (!class_exists('Fb_Reviews_Widget')) return '';

    $shortcode_atts = array();
    foreach (Fb_Reviews_Widget::$widget_fields as $field => $value) {
        $shortcode_atts[$field] = isset($atts[$field]) ? strip_tags(stripslashes($atts[$field])) : '';
    }

    foreach ($shortcode_atts as $variable => $value) {
        ${$variable} = esc_attr($shortcode_atts[$variable]);
    }

    ob_start();
    if (empty($page_id)) {
        ?>
        <div class="fbrev-error" style="padding:10px;color:#b94a48;background-color:#f2dede;border-color:#eed3d7;max-width:200px;">
            <?php echo fbrev_i('<b>Facebook Reviews Widget</b>: required attribute page_id is not defined'); ?>
        </div>
        <?php
    } else {
        $response = fbrev_api_rating($page_id, $page_access_token, $shortcode_atts, md5($page_access_token), $cache, $api_ratings_limit, $show_success_api);
        $response_data = $response['data'];
        $response_json = rplg_json_decode($response_data);
        if (isset($response_json->ratings) && isset($response_json->ratings->data)) {
            $reviews = $response_json->ratings->data;
            if (isset($response_json->overall_star_rating)) {
                $facebook_rating = number_format((float)$response_json->overall_star_rating, 1, '.', '');
            }
            if (isset($response_json->rating_count) && $response_json->rating_count > 0) {
                $facebook_count = $response_json->rating_count;
            }
            if ($title) { ?><h2 class="fbrev-widget-title widget-title"><?php echo $title; ?></h2><?php }
            include(dirname(__FILE__) . '/fbrev-reviews.php');
        } else {
            ?>
            <div class="fbrev-error" style="padding:10px;color:#B94A48;background-color:#F2DEDE;border-color:#EED3D7;">
                <?php echo fbrev_i('Facebook API Error: ') . $response_data . fbrev_i('<br><b>Reconnecting to Facebook may fix the issue.</b>'); ?>
            </div>
            <?php
        }
    }
    return preg_replace('/[\n\r]/', '', ob_get_clean());
}
add_shortcode("fbrev", "fbrev_shortcode");

/*-------------------------------- Init language --------------------------------*/
function fbrev_lang_init() {
    $plugin_dir = basename(dirname(__FILE__));
    load_plugin_textdomain('fbrev', false, $plugin_dir . '/languages');
}
add_action('plugins_loaded', 'fbrev_lang_init');

/*-------------------------------- Leave review --------------------------------*/
function fbrev_admin_notice() {
    if (!is_admin()) return;

    $activation_time = get_option('fbrev_activation_time');

    if ($activation_time == '') {
        $activation_time = time() - 86400*28;
        update_option('fbrev_activation_time', $activation_time);
    }

    $rev_notice = isset($_GET['fbrev_rev_notice']) ? $_GET['fbrev_rev_notice'] : '';
    if ($rev_notice == 'later') {
        $activation_time = time() - 86400*23;
        update_option('fbrev_activation_time', $activation_time);
        update_option('fbrev_rev_notice_hide', 'later');
    } else if ($rev_notice == 'never') {
        update_option('fbrev_rev_notice_hide', 'never');
    }

    $rev_notice_hide = get_option('fbrev_rev_notice_hide');
    $rev_notice_show = get_option('rplg_rev_notice_show');

    if ($rev_notice_show == '' || $rev_notice_show == 'fbrev') {

        if ($rev_notice_hide != 'never' && $activation_time < (time() - 86400*30)) {
            update_option('rplg_rev_notice_show', 'fbrev');
            $class = 'notice notice-info is-dismissible';
            $url = remove_query_arg(array('taction', 'tid', 'sortby', 'sortdir', 'opt'));
            $url_later = esc_url(add_query_arg('fbrev_rev_notice', 'later', $url));
            $url_never = esc_url(add_query_arg('fbrev_rev_notice', 'never', $url));

            $notice = '<p style="font-weight:normal;color:#156315">Hey, I noticed you have been using my <b>Facebook Reviews Widget</b> plugin for a while now – that’s awesome!<br>Could you please do me a BIG favor and give it a 5-star rating on WordPress?<br><br>--<br>Thanks!<br>Daniel K.<br></p><ul style="font-weight:bold;"><li><a href="https://wordpress.org/support/plugin/fb-reviews-widget/reviews/#new-post" target="_blank">OK, you deserve it</a></li><li><a href="' . $url_later . '">Not now, maybe later</a></li><li><a href="' . $url_never . '">Do not remind me again</a></li></ul><p>By the way, if you have been thinking about upgrading to the <a href="https://richplugins.com/business-reviews-bundle-wordpress-plugin" target="_blank">Business</a> version, here is a 25% off coupon you can use! ->  <b>business25off</b></p>';

            printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $notice);
        } else {
            update_option('rplg_rev_notice_show', '');
        }

    }
}
add_action('admin_notices', 'fbrev_admin_notice');

/*-------------------------------- Helpers --------------------------------*/
function fbrev_enabled() {
    $active = get_option('fbrev_active');
    if (empty($active) || $active === '0') { return false; }
    return true;
}

function fbrev_api_rating($page_id, $page_access_token, $options, $cache_name, $cache_option, $limit, $show_success_api) {

    $response_cache_key = 'fbrev_' . FBREV_VERSION . '_' . $cache_name . '_api_' . $page_id;
    $options_cache_key = 'fbrev_' . FBREV_VERSION . '_' . $cache_name . '_options_' . $page_id;

    if (!isset($limit) || $limit == null) {
        $limit=FBREV_API_RATINGS_LIMIT;
    }

    $api_response = get_transient($response_cache_key);
    $widget_options = get_transient($options_cache_key);
    $serialized_instance = serialize($options);

    if ($api_response === false || $serialized_instance !== $widget_options) {
        $expiration = $cache_option;
        switch ($expiration) {
            case '1':
                $expiration = 3600;
                break;
            case '3':
                $expiration = 3600 * 3;
                break;
            case '6':
                $expiration = 3600 * 6;
                break;
            case '12':
                $expiration = 3600 * 12;
                break;
            case '24':
                $expiration = 3600 * 24;
                break;
            case '48':
                $expiration = 3600 * 48;
                break;
            case '168':
                $expiration = 3600 * 168;
                break;
            default:
                $expiration = 3600 * 24;
        }

        //string concatenation instead of 'http_build_query', because 'http_build_query' doesn't always work
        $api_url = FBREV_GRAPH_API . $page_id . "?access_token=" . $page_access_token . "&fields=ratings.fields(reviewer{id,name,picture.width(120).height(120)},created_time,rating,recommendation_type,review_text,open_graph_story{id}).limit(" . $limit . "),overall_star_rating";

        if (!$options['fb_rating_calc']) {
            $api_url = $api_url . ",rating_count";
        }

        $api_response = rplg_urlopen($api_url);

        set_transient($response_cache_key, $api_response, $expiration);
        set_transient($options_cache_key, $serialized_instance, $expiration);
    }

    //show the latest success API response if the error happened
    if ($show_success_api) {
        $response_cache_key_success = 'fbrev_' . $cache_name . '_suc_api_' . $page_id;
        $api_response_json = rplg_json_decode($api_response['data']);
        if (isset($api_response_json->ratings)) {
            set_transient($response_cache_key_success, $api_response, 0);
        } else {
            $last_success_api_response = get_transient($response_cache_key_success);
            if ($last_success_api_response !== false) {
                return $last_success_api_response;
            }
        }
    }
    return $api_response;
}

function fbrev_i($text, $params=null) {
    if (!is_array($params)) {
        $params = func_get_args();
        $params = array_slice($params, 1);
    }
    return vsprintf(__($text, 'fbrev'), $params);
}

?>