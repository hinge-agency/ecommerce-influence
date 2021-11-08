<?php
/*
  Plugin Name: OptiMonk
  Plugin URI: https://www.optimonk.com/
  Description: OptiMonk is an exit-intent popup technology
  Author: OptiMonk
  Version: 1.3.4
  Text Domain: optimonk
  Domain Path: /languages
  Author URI: http://www.optimonk.com/
  License: GPLv2
*/

if (!defined('ABSPATH')) {
    die('');
}
require_once 'wc-attributes.php';
require_once(dirname(__FILE__) . "/optimonk-admin.php");
require_once(dirname(__FILE__) . "/optimonk-front.php");
require_once(dirname(__FILE__) . "/include/class-notification.php");

if (class_exists('OptiMonkAdmin') && class_exists('OptiMonkFront')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443)
        ? "https://"
        : "http://";
    $currentUrl = $protocol . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];

    if (!is_admin() && strpos($currentUrl, wp_login_url()) !== 0) {
        add_action('wp_print_footer_scripts', array('OptiMonkFront', 'init'));
        add_action('wp_enqueue_scripts', array('OptiMonkAdmin', 'initStyleSheet'));
    }

    register_activation_hook(__FILE__, array('OptiMonkAdmin', 'activate'));
    add_action('admin_init', array('OptiMonkAdmin', 'redirectToSettingPage'));
    add_action('admin_init', array('OptiMonkAdmin', 'initFeedbackNotification'));

    $optiMonkAdmin = new OptiMonkAdmin(__FILE__);
    $optiMonkFront = new OptiMonkFront();
}
