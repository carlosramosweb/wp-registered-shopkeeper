<?php
/*
 * Class WP_Registered_Shopkeeper
*/

if (!defined('ABSPATH'))
{
    exit();
}

class WP_Registered_Shopkeeper
{

    public function __construct()
    {
        add_action('init', array($this,'register_posttype'));
        add_action('admin_menu', array($this,'admin_menu_callback'));
        add_filter('manage_shopkeeper_posts_columns', array($this,'set_edit_shopkeeper_columns'));
        add_action('manage_shopkeeper_posts_custom_column', array($this,'manage_shopkeeper_custom_column'), 10, 2);
        add_shortcode('search_shopkeeper', array($this,'page_search_shopkeeper'));
        add_action('wp_ajax_nopriv_search_shopkeeper_state', array($this,'search_shopkeeper_state'));
        add_action('wp_ajax_search_shopkeeper_state', array($this,'search_shopkeeper_state'));
        add_action('wp_ajax_nopriv_search_all_shopkeeper', array($this,'search_all_shopkeeper'));
        add_action('wp_ajax_search_all_shopkeeper', array($this,'search_all_shopkeeper'));

    }

    public function register_posttype()
    {
        $args = array(
            'public' => true,
            'label' => __('Lista de Lojista', 'wp-registered-shopkeeper'),
            'publicly_queryable' => true,
            'public_queryable' => true,
            'exclude_from_search' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => false,
            'show_in_admin_bar' => true,
            'capability_type' => 'post',
            'query_var' => true,
            'has_archive' => false,
            'menu_icon' => 'dashicons-store',
            'supports' => array(
                'title',
                'thumbnail',
                'revisions',
                'author'
            ),
            'rewrite' => false,
            // 'title', 'editor', 'comments', 'revisions', 'trackbacks', 'excerpt', 'page-attributes', 'thumbnail', 'custom-fields', and 'post-formats'
            
        );
        register_post_type('shopkeeper', $args);
    }

    public function admin_menu_callback()
    {
        add_submenu_page(
            'edit.php?post_type=shopkeeper', 
            __('Configuração', 'wp-registered-shopkeeper'), 
            __('Configuração', 'wp-registered-shopkeeper'), 
            'manage_options', 
            'shopkeeper-settings', 
            array($this,'admin_page_settings')
        );
    }

    public function admin_page_settings()
    {
        include_once dirname(__FILE__) . '/admin-page-settings.php';
    }

    public function set_edit_shopkeeper_columns($columns)
    {
        unset($columns['author']);
        unset($columns['date']);
        $columns['shopkeeper_whatsapp'] = __('Whatsapp', 'wp-registered-shopkeeper');
        $columns['shopkeeper_email'] = __('E-mail', 'wp-registered-shopkeeper');
        $columns['shopkeeper_state'] = __('Estado', 'wp-registered-shopkeeper');
        $columns['author'] = __('Autor', 'wp-registered-shopkeeper');
        $columns['date'] = __('Data', 'wp-registered-shopkeeper');
        return $columns;
    }

    public function manage_shopkeeper_custom_column($column, $post_id)
    {
        switch ($column)
        {
            case 'shopkeeper_whatsapp':
                echo get_post_meta($post_id, 'shopkeeper_whatsapp', true);
            break;
            case 'shopkeeper_email':
                echo get_post_meta($post_id, 'shopkeeper_email', true);
            break;
            case 'shopkeeper_state':
                echo get_post_meta($post_id, 'shopkeeper_state', true);
            break;
        }
    }

    public function page_search_shopkeeper()
    {
        include_once dirname(__FILE__) . '/page-search-shopkeeper.php';
    }

    public function search_shopkeeper_state()
    {
        global $wpdb;
        $html = '<option value="">Selecione a Cidade</option>';
        if (isset($_POST['shopkeeper_state']) && !empty($_POST['shopkeeper_state']))
        {
            $state = $_POST['shopkeeper_state'];
            $state_ids = $wpdb->get_results("SELECT DISTINCT post_id FROM wp_postmeta WHERE meta_key = 'shopkeeper_state' AND meta_value LIKE '" . esc_sql($state) . "'");
            if (isset($state_ids[0]) && !empty($state_ids[0]))
            {
                $option = "";
                foreach ($state_ids as $key => $state)
                {
                    $shopkeeper_city = get_post_meta($state->post_id, 'shopkeeper_city', true);
                    $option .= "<option value='" . $shopkeeper_city . "'>" . $shopkeeper_city . "</option>,";
                }
            }

        }
        $unique = implode(',', array_unique(explode(',', $option)));
        $html .= str_replace(',', '', $unique);

        echo $html;
    }

    public function format_phone_number($number)
    {
        if (isset($number) && !empty($number))
        {
            $number = str_replace('(', '', $number);
            $number = str_replace(')', '', $number);
            $number = str_replace('-', '', $number);
            $number = str_replace(' ', '', $number);
            if (strlen($number) == 11)
            {
                $number = '55' . $number;
            }
        }
        return $number;
    }

    public function search_all_shopkeeper()
    {
        global $wpdb;
        $html = '';
        if (isset($_POST['shopkeeper_state']) && !empty($_POST['shopkeeper_state']) || isset($_POST['shopkeeper_city']) && !empty($_POST['shopkeeper_city']))
        {
            $state = $_POST['shopkeeper_state'];
            $city = $_POST['shopkeeper_city'];
            $state_ids = $wpdb->get_results("SELECT DISTINCT post_id FROM wp_postmeta WHERE meta_key = 'shopkeeper_state' AND meta_value LIKE '" . esc_sql($state) . "'");
            if (isset($state_ids[0]) && !empty($state_ids[0]))
            {
                foreach ($state_ids as $key => $state)
                {
                    if (isset($state->post_id) && !empty($state->post_id))
                    {
                        $post_ids = $wpdb->get_results("SELECT DISTINCT post_id FROM wp_postmeta WHERE meta_key = 'shopkeeper_city' AND post_id = '" . esc_sql($state->post_id) . "'  AND meta_value LIKE '%" . esc_sql($city) . "%'");
                        if (isset($post_ids[0]) && !empty($post_ids[0]))
                        {
                            foreach ($post_ids as $key => $item)
                            {
                                $post = get_post($item->post_id);
                                $shopkeeper = $post->post_title;
                                if ($post->post_type == 'shopkeeper')
                                {
                                    $shopkeeper_zip_code = get_post_meta($item->post_id, 'shopkeeper_zip_code', true);
                                    $shopkeeper_address = get_post_meta($item->post_id, 'shopkeeper_address', true);
                                    $shopkeeper_number_address = get_post_meta($item->post_id, 'shopkeeper_number_address', true);
                                    $shopkeeper_neighborhood = get_post_meta($item->post_id, 'shopkeeper_neighborhood', true);
                                    $shopkeeper_state = get_post_meta($item->post_id, 'shopkeeper_state', true);
                                    $shopkeeper_city = get_post_meta($item->post_id, 'shopkeeper_city', true);

                                    $shopkeeper_fantasy_name = get_post_meta($item->post_id, 'shopkeeper_fantasy_name', true);
                                    $shopkeeper_corporate_name = get_post_meta($item->post_id, 'shopkeeper_corporate_name', true);
                                    $shopkeeper_cnpj = get_post_meta($item->post_id, 'shopkeeper_cnpj', true);
                                    $shopkeeper_phone = get_post_meta($item->post_id, 'shopkeeper_phone', true);
                                    $shopkeeper_whatsapp = get_post_meta($item->post_id, 'shopkeeper_whatsapp', true);
                                    $shopkeeper_website = get_post_meta($item->post_id, 'shopkeeper_website', true);
                                    $shopkeeper_email = get_post_meta($item->post_id, 'shopkeeper_email', true);

                                    $thumbnail = get_the_post_thumbnail($item->post_id, 'medium');
                                    if (isset($thumbnail) && empty($thumbnail))
                                    {
                                        $thumbnail = plugins_url() . '/wp-registered-shopkeeper/assets/images/lojista.png';
                                    }

                                    $html .= '<div class="card-body">';
                                    $html .= '<div class="media">';
                                    $html .= '<img src="' . $thumbnail . '" class="mr-3">';
                                    $html .= '<div class="media-body text-left" style="margin-bottom: 15px;">';
                                    $html .= '<h5 class="mt-0">' . $shopkeeper . '</h5>';
                                    $html .= '' . $shopkeeper_address . ', Nº: ' . $shopkeeper_number_address . ', CEP: ' . $shopkeeper_zip_code . '<br>' . $shopkeeper_neighborhood . '/' . $shopkeeper_city . '';
                                    $html .= '</div>';
                                    $html .= '</div>';
                                    $html .= '<a href="https://web.whatsapp.com/send?phone=' . $this->format_phone_number($shopkeeper_whatsapp) . '" class="btn btn-block btn-success rounded-pill" href="lancaperfume-df" role="button" target="_blank" style="padding: 10px 20px;text-decoration: none;">';
                                    $html .= '<img src="' . plugins_url() . '/wp-registered-shopkeeper/assets/images/whats.png" style="display: inline-block;margin-right: 10px;">';
                                    $html .= '<strong>WhatsApp</strong>';
                                    $html .= '</a>';
                                    $html .= '</div>';
                                }
                            }
                        }

                    }

                }
            }

        }
        echo $html;
    }

}

