<?php
/*
Plugin Name: WP Registered Shopkeeper
Plugin URI: https://wordpress.org/plugins/woo-payment-on-delivery/
Author: carlosramosweb
Author URI: https://www.criacaocriativa.com
Donate link: https://donate.criacaocriativa.com
Description: Sistema de cadsatro de lojistas/Revendedores no Wordpress.
Text Domain: wp-registered-shopkeeper
Domain Path: /languages/
Version: 1.0.0
Requires at least: 3.5.0
Tested up to: 5.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html 
*/

if (!defined('ABSPATH'))
{
    exit();
}

if (!class_exists('WP_Registered_Shopkeeper'))
{

    include_once dirname(__FILE__) . '/includes/class-wp-registered-shopkeeper.php';
    new WP_Registered_Shopkeeper();

    register_activation_hook(__FILE__, 'wp_rs_activate_plugin');
    register_deactivation_hook(__FILE__, 'wp_rs_deactivate_plugin');

    add_action('init', 'wp_rs_load_plugin_textdomain');
    add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wp_rs_action_links');

    function wp_rs_activate_plugin()
    {
        $settings = [];
        $settings = get_option('wp_registered_shopkeeper_settings');
        $new_settings = [];
        $new_settings['telephone'] = '61900000000';
        $new_settings['text_first'] = 'Escolha sua cidade e fale no WhatsApp com a lojista mais próximo de você.';
        $new_settings['text_second'] = 'Selecione o Estado e Cidade para falar no WhatsApp';
        $new_settings['text_third'] = 'Fale com um de nossos atendentes.';
        update_option('wp_registered_shopkeeper_settings', $new_settings);
    }

    function wp_rs_deactivate_plugin()
    {
        delete_option('wp_registered_shopkeeper_settings');
    }

    function wp_rs_action_links($links)
    {
        $plugin_links = [];
        $plugin_links[] = '<a href="' . esc_url(admin_url('edit.php?post_type=shopkeeper&page=shopkeeper-settings')) . '">' . __('Settings', 'wp-registered-shopkeeper') . '</a>';
        $links = array_merge($plugin_links, $links);
        return $links;
    }

    function wp_rs_load_plugin_textdomain()
    {
        load_plugin_textdomain('wp-registered-shopkeeper', false, dirname(__FILE__) . '/languages');
    }

    include_once dirname(__FILE__) . '/advanced-custom-fields/acf.php';
    include_once dirname(__FILE__) . '/includes/shopkeeper-data.php';

}

