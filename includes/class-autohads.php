<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Main AutoHads Class.
 *
 * @class AutoHads
 * @version	3.2.0
 */
final class AutoHads {

    /**
     * AutoHads version.
     *
     * @var string
     */
    public $version = '3.2.6';
    public static $_instance;
    private $model;
    private $rows_per_page;
    private $tabledb;
    private $tabledb_post;
    private $tabledb_attach;
    private $db;
    private $page_auto_hads = 'auto-hads';
    private $page_auto_hads_list = 'auto-hads-list';
    private $page_auto_hads_woocommerce = 'auto-hads-woocommerce';
    private $page_auto_hads_post = 'auto-hads-posts';

    /**
     * Main AutoHads Instance.
     *
     * Ensures only one instance of AutoHads is loaded or can be loaded.
     *
     * @static
     * @return AutoHads - Main instance.
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     */
    public function __wakeup() {
        
    }

    /**
     * Auto-load in-accessible properties on demand.
     *
     * @param mixed $key Key name.
     * @return mixed
     */
    public function __get($key) {
//		if ( in_array( $key, array( 'payment_gateways', 'shipping', 'mailer', 'checkout' ), true ) ) {
//			return $this->$key();
//		}
    }

    /**
     * AutoHads Constructor.
     */
    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->tabledb = $this->db->base_prefix . 'auto_hads';
        $this->tabledb_post = $this->db->base_prefix . 'auto_hads_posts';
        $this->tabledb_attach = $this->db->base_prefix . 'auto_hads_attachs';

        $this->define_constants();
        $this->includes();

        $this->rows_per_page = 20;
        $collums = array('id', 'link', 'params', 'type', 'status');
        $key = 'id';
        $this->model = new AutoHadsModel($this->tabledb, $collums, $key);

        $this->init_hooks();
    }

    /**
     * Define WC Constants.
     */
    private function define_constants() {
        $this->define('AUTOHADS_ABSPATH', dirname(__FILE__) . '/');
    }

    /**
     * Define constant if not already set.
     *
     * @param string      $name  Constant name.
     * @param string|bool $value Constant value.
     */
    private function define($name, $value) {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    /**
     * What type of request is this?
     *
     * @param  string $type admin, ajax, cron or frontend.
     * @return bool
     */
    private function is_request($type) {
        switch ($type) {
            case 'admin' :
                return is_admin();
            case 'ajax' :
                return defined('DOING_AJAX');
            case 'cron' :
                return defined('DOING_CRON');
            case 'frontend' :
                return (!is_admin() || defined('DOING_AJAX') ) && !defined('DOING_CRON');
        }
    }

    /**
     * Include required core files used in admin and on the frontend.
     */
    public function includes() {
        require_once(AUTO_HADS_PLUGIN_DIR . '/includes/class-image.php');
        require_once(AUTO_HADS_PLUGIN_DIR . '/includes/class-model.php');
        require_once(AUTO_HADS_PLUGIN_DIR . '/includes/class-html-node.php');
        include_once(AUTO_HADS_PLUGIN_DIR . '/includes/ah-funtions.php');
        include_once(AUTO_HADS_PLUGIN_DIR . '/includes/ah-woocommerce-functions.php' );
        include_once(AUTO_HADS_PLUGIN_DIR . '/includes/ah-posts-functions.php' );
    }

    /**
     * Init AutoHads when WordPress Initialises.
     */
    public function init() {
        // Set up localisation.
        $this->load_plugin_textdomain();
    }

    /**
     * Hook into actions and filters.
     *
     * @since 1.0
     */
    private function init_hooks() {
        register_activation_hook(AUTO_HADS_PLUGIN_DIR_FILE, array($this, 'install'));
        register_deactivation_hook(AUTO_HADS_PLUGIN_DIR_FILE, array($this, 'deactivation'));
        register_uninstall_hook(AUTO_HADS_PLUGIN_DIR_FILE, array($this, 'uninstall'));


        add_action('admin_menu', array($this, 'plugin_menu'));

        add_action('wp_ajax_auto_hads_ajax_set_import_post_data', array($this, 'set_import_post_data'));
        add_action('wp_ajax_nopriv_auto_hads_ajax_set_import_post_data', array($this, 'set_import_post_data'));

        add_action('wp_ajax_auto_hads_ajax_get_content_data', array($this, 'get_content_data'));
        add_action('wp_ajax_nopriv_auto_hads_ajax_get_content_data', array($this, 'get_content_data'));


        add_action('wp_ajax_auto_hads_ajax_get_content_test', array($this, 'get_content_test'));
        add_action('wp_ajax_nopriv_auto_hads_ajax_get_content_test', array($this, 'get_content_test'));
    }

    /**
     * add menu
     */
    public function plugin_menu() {

        add_menu_page(__('Auto Hads', 'autohads'), __('Auto Hads', 'autohads'), 'manage_options', $this->page_auto_hads, array($this, 'auto_hads_wellcome'), 'dashicons-controls-forward', 2);
        $page_list = add_submenu_page($this->page_auto_hads, __('All Auto Hads', 'autohads'), __('All Auto Hads', 'autohads'), 'manage_options', $this->page_auto_hads_list, array($this, 'auto_hads_posts_list_all'));
        add_action("admin_print_scripts-$page_list", array($this, 'loadjs_admin_posts_list'));

        $page_posts = add_submenu_page($this->page_auto_hads, __('Manual Posts', 'autohads'), __('Manual Posts', 'autohads'), 'manage_options', $this->page_auto_hads_post, array($this, 'auto_hads_posts'));
        add_action("admin_print_scripts-$page_posts", array($this, 'loadjs_admin_head'));

        $page = add_submenu_page($this->page_auto_hads, __('Manual Products', 'autohads'), __('Manual Products', 'autohads'), 'manage_options', $this->page_auto_hads_woocommerce, array($this, 'auto_hads_woocommerce'));
        add_action("admin_print_scripts-$page", array($this, 'loadjs_admin_head'));
    }

    /**
     * Get list auto all
     */
    public function auto_hads_posts_list_all() {
        $task_id = mt_rand();
        $_SESSION['export'] = $task_id;
        // key word search
        $key_word = "";
        if (isset($_POST['search'])) {
            $key_word = stripslashes_deep($_POST['search']);
        }
        if (isset($_GET['search'])) {
            $key_word = $_GET['search'];
        }
        // order by
        $order_by = "";
        $order = "";
        if (isset($_GET['orderby'])) {
            $order_by = $_GET['orderby'];
            $order = $_GET['order'];
        }

        // manage record quantity
        $begin_row = 0;
        if (isset($_GET['beginrow'])) {
            if (is_numeric($_GET['beginrow'])) {
                $begin_row = $_GET['beginrow'];
            }
        }
        $total = $this->model->count_rows($key_word); // count all data rows
        $next_begin_row = $begin_row + $this->rows_per_page;
        if ($total < $next_begin_row) {
            $next_begin_row = $total;
        }
        $last_begin_row = $this->rows_per_page * (floor(($total - 1) / $this->rows_per_page));

        // stuff to display
        $result = $this->model->select($key_word, $order_by, $order, $begin_row, $this->rows_per_page);

        $args = array(
            'urllist' => 'admin.php?page=' . $this->page_auto_hads_list,
            'urlpost' => 'admin.php?page=' . $this->page_auto_hads_post,
            'items' => $result,
            'begin_row' => $begin_row,
            'total' => $total,
            'next_begin_row' => $next_begin_row,
            'last_begin_row' => $last_begin_row,
            'order_by' => $order_by,
            'order' => $order,
            'key_word' => $key_word,
        );
        $this->get_template('list-posts', $args);
    }
    /**
     * page wellcome
     */
    public function auto_hads_wellcome() {
        $this->get_template('index');
    }
    /**
     * Page woocommerce
     */
    public function auto_hads_woocommerce() {
        $url_id = 0;
        if (isset($_GET['id'])) {
            $url_id = absint($_GET['id']);
        }
        if (isset($_POST['submit'])) {
            $post = $_POST;
            $link_target = auto_hads_request('link_target');
            $type = auto_hads_request('type');
            $id = absint(auto_hads_request('id'));
            if (!empty($link_target)) {
                unset($post['type']);
                unset($post['fnc']);
                unset($post['id']);
                unset($post['submit']);
                $params = serialize($post);
                if (!$id) {
                    $id = $this->db->insert($this->tabledb, array(
                        'link' => $link_target,
                        'type' => $type,
                        'params' => $params,
                        'status' => 1,
                    ));
                    wp_redirect('admin.php?page=' . $this->page_auto_hads_woocommerce);
                } else {
                    $this->model->update(array(
                        'id' => $id,
                        'link' => $link_target,
                        'type' => $type,
                        'params' => $params,
                        'status' => 1,
                    ));
                    wp_redirect('admin.php?page=' . $this->page_auto_hads_woocommerce . '&id=' . $id);
                }
            }
        }
        $atts = array();
        if ($url_id) {
            $row = $this->model->get_row($url_id);
            $atts = unserialize($row->params);
            if (isset($atts['tax_input'])) {
                foreach ($atts['tax_input'] as $key => $brands) {
                    switch ($key) {
                        case 'product_cat':
                            $atts['post_category'] = $brands;
                            break;
                        case 'pwb-brand':
                            $atts['post_brands'] = $brands;
                            break;
                        default:
                            break;
                    }
                }
            }

            $atts['url_id'] = $url_id;
        }
        $pairs = array(
            'url_id' => '',
            'post_category' => array(),
            'post_brands' => array(),
            'urlpost' => 'admin.php?page=' . $this->page_auto_hads_woocommerce,
            'link_target' => '',
            'tag_warper' => '',
            'tag_link' => '',
            'tag_title' => '',
            'tag_short_description' => '',
            'tag_short_description_replace' => '',
            'tag_image' => '',
            'tag_price' => '',
            'tag_sale_chk' => '',
            'tag_price_replace' => '',
            'tag-sale' => '',
            'tag_short_detail' => '',
            'tag_short_detail_replace' => '',
            'tag_warper_content' => '',
            'tag_content_replace' => '',
            'tag_keep_tags' => '',
            'tag_short' => '',
            'tag_short_replace' => '',
            'tag_download_chk' => '',
            'tag_keep_table' => '',
            'tag_image_chk' => '',
            'tag_gallery_chk' => '',
            'tag_warper_image' => '',
            'tag_skip_element' => '',
            'tag_keep_img' => '',
            'tag_images_alignment' => '',
            'tag_tags_chk' => '',
            'tag_tags' => '',
            'tag_tags_replace' => '',
        );
        $args = auto_hads_parser_atts($pairs, $atts);

        $this->get_template('woocommerce', $args);
    }
    /**
     * page for post
     */
    public function auto_hads_posts() {
        $url_id = 0;
        if (isset($_GET['id'])) {
            $url_id = absint($_GET['id']);
        }

        if (isset($_POST['submit'])) {
            $post = $_POST;
            $link_target = auto_hads_request('link_target');
            $type = auto_hads_request('type');
            $id = absint(auto_hads_request('id'));
            if (!empty($link_target)) {
                unset($post['type']);
                unset($post['fnc']);
                unset($post['id']);
                unset($post['submit']);
                $params = serialize($post);
                if (!$id) {
                    $id = $this->db->insert($this->tabledb, array(
                        'link' => $link_target,
                        'type' => $type,
                        'params' => $params,
                        'status' => 1,
                    ));
                    wp_redirect('admin.php?page=' . $this->page_auto_hads_list);
                } else {
                    $this->model->update(array(
                        'id' => $id,
                        'link' => $link_target,
                        'type' => $type,
                        'params' => $params,
                        'status' => 1,
                    ));
                    wp_redirect('admin.php?page=' . $this->page_auto_hads_post . '&id=' . $id);
                }
            }
        }
        $atts = array();
        if ($url_id) {
            $row = $this->model->get_row($url_id);
            $atts = unserialize($row->params);
            $atts['url_id'] = $url_id;
        }

        $pairs = array(
            'url_id' => '',
            'post_category' => array(),
            'urlpost' => 'admin.php?page=' . $this->page_auto_hads_post,
            'link_target' => '',
            'tag_warper' => '',
            'tag_link' => '',
            'tag_title' => '',
            'tag_short_description' => '',
            'tag_short_description_replace' => '',
            'tag_image' => '',
            'tag_attribute_image' => '',
            'tag_warper_content' => '',
            'tag_content_replace' => '',
            'tag_image_chk' => '',
            'tag_download_chk' => '',
            'tag_keep_tags' => '',
            'tag_keep_table' => '',
            'tag_skip_element' => '',
            'tag_keep_img' => '',
            'tag_images_alignment' => '',
            'tag_tags_chk' => '',
            'tag_tags' => '',
            'tag_tags_replace' => '',
        );

        $args = auto_hads_parser_atts($pairs, $atts);


        $this->get_template('post', $args);
    }
    /**
     * Get my theme
     * 
     * @param type $name
     * @param array $args
     */
    public function get_template($name, array $args = array()) {
        foreach ($args AS $key => $val) {
            $$key = $val;
        }
        $file = AUTO_HADS_PLUGIN_DIR . '/themes/' . $name . '.php';
        if (file_exists($file)) {
            include( $file );
        }
    }
    /**
     * add admin js
     */
    public function loadjs_admin_head() {
        wp_enqueue_script('auto-loadjs', AUTO_HADS_PLUGIN_URL . '/js/poststourl.js');
        wp_enqueue_style('auto-hads-admin-style', AUTO_HADS_PLUGIN_URL . '/css/admin-style.css');
    }
    /**
     * add admin style
     */
    public function loadjs_admin_posts_list() {
        wp_enqueue_style('auto-hads-admin-style', AUTO_HADS_PLUGIN_URL . '/css/admin-style.css');
    }

    /**
     * Load Localisation files.
     *
     * Note: the first-loaded translation file overrides any following ones if the same translation is present.
     *
     * Locales found in:
     *      - WP_LANG_DIR/AutoHads/AutoHads-LOCALE.mo
     *      - WP_LANG_DIR/plugins/AutoHads-LOCALE.mo
     */
    public function load_plugin_textdomain() {
        $locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
        $locale = apply_filters('plugin_locale', $locale, 'autohads');

        unload_textdomain('autohads');
        load_textdomain('autohads', WP_LANG_DIR . '/autohads/autohads-' . $locale . '.mo');
        load_plugin_textdomain('autohads', false, plugin_basename(dirname(WC_PLUGIN_FILE)) . '/i18n/languages');
    }
    /**
     * Get Test data description
     * 
     * @return type
     */
    public function get_content_test() {
        $action = auto_hads_request('fnc');
        $get_content_test = 'auto_hads_' . $action . '_detail_data';
        if (!function_exists($get_content_test)) {
            return wp_send_json_error();
        }
        $arg = $get_content_test(true);
        wp_send_json_success($arg);
    }
    /**
     * 
     * Get content data
     * 
     * @return type
     */
    public function get_content_data() {
        $action = auto_hads_request('fnc');
        $content_data_funtions = 'auto_hads_' . $action . '_content_data';
        if (!function_exists($content_data_funtions)) {
            return wp_send_json_error('func not running');
        }
        $xreturn = $content_data_funtions();
        if (!$xreturn) {
            wp_send_json_error($action . 'func running some error');
        }
        wp_send_json_success($xreturn);
    }

    /**
     * Set import data
     * 
     * @return type
     */
    public function set_import_post_data() {
        $action = auto_hads_request('fnc');
        $import_post_funtions = 'auto_hads_' . $action . '_import_post_data';
        if (!function_exists($import_post_funtions)) {
            return wp_send_json_error();
        }
        $xreturn = $import_post_funtions();
        if (!$xreturn) {
            return wp_send_json_error();
        }
        wp_send_json_success($xreturn);
    }

    /**
     * Activation hook
     * Create table if they don't exist and add plugin options
     */
    public function install() {
        // Get the correct character collate
        $charset_collate = 'utf8';
        if (!empty($this->db->charset)) {
            $charset_collate = "DEFAULT CHARACTER SET " . $this->db->charset;
        }
        if (!empty($this->db->collate)) {
            $charset_collate .= " COLLATE " . $this->db->collate;
        }

        if ($this->db->get_var('SHOW TABLES LIKE "' . $this->tabledb . '" ') != $this->tabledb) {
            // Setup chat message table
            $sql = "
                CREATE TABLE IF NOT EXISTS `" . $this->tabledb . "` (
                `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `link` varchar(1000) NOT NULL DEFAULT '',
                `params` longtext NOT NULL,
                `type` varchar(20) NOT NULL DEFAULT 'post',
                `status` TINYINT NOT NULL,
                PRIMARY KEY (`id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=" . $charset_collate . " AUTO_INCREMENT=1
                ;
                ";
            $this->db->query($sql);
        } else {
            $sql = 'TRUNCATE TABLE `' . $this->tabledb . '`';
            $this->db->query($sql);
        }

        if ($this->db->get_var('SHOW TABLES LIKE "' . $this->tabledb_post . '" ') != $this->tabledb_post) {
            // Setup chat message table
            $sql = "
                CREATE TABLE IF NOT EXISTS `" . $this->tabledb_post . "` (
                `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `hads_id` bigint(20) UNSIGNED NOT NULL,
                `post_id` bigint(20) UNSIGNED NOT NULL,
                `link` varchar(1000) NOT NULL DEFAULT '',
                `src` varchar(1000) NOT NULL DEFAULT '',
                `title` varchar(1000) NOT NULL DEFAULT '',
                `status` TINYINT NOT NULL,
                PRIMARY KEY (`id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=" . $charset_collate . " AUTO_INCREMENT=1
                ;
                ";
            $this->db->query($sql);
        } else {
            $sql = 'TRUNCATE TABLE `' . $this->tabledb_post . '`';
            $this->db->query($sql);
        }

        if ($this->db->get_var('SHOW TABLES LIKE "' . $this->tabledb_attach . '" ') != $this->tabledb_attach) {
            // Setup chat message table
            $sql = "
                CREATE TABLE IF NOT EXISTS `" . $this->tabledb_attach . "` (
                `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `attach_id` bigint(20) UNSIGNED NOT NULL,
                `link_target` varchar(1000) NOT NULL DEFAULT '',
                `attach_url` varchar(1000) NOT NULL DEFAULT '',
                `file_path` varchar(1000) NOT NULL DEFAULT '',
                PRIMARY KEY (`id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=" . $charset_collate . " AUTO_INCREMENT=1
                ;
                ";
            $this->db->query($sql);
        } else {
            $sql = 'TRUNCATE TABLE `' . $this->tabledb_attach . '`';
            $this->db->query($sql);
        }
    }

    /**
     * Deactivation hook
     * Clear table
     */
    public function deactivation() {

        $sql = 'TRUNCATE TABLE `' . $this->tabledb . '`';
        $this->db->query($sql);

        $sql = 'TRUNCATE TABLE `' . $this->tabledb_post . '`';
        $this->db->query($sql);

        $sql = 'TRUNCATE TABLE `' . $this->tabledb_attach . '`';
        $this->db->query($sql);
    }

    /**
     * Uninstall hook
     * Remove table and plugin options
     */
    public function uninstall() {
        //remove table
        $sql = 'DROP TABLE IF EXISTS `' . $this->tabledb . '`';
        $this->db->query($sql);

        $sql = 'DROP TABLE IF EXISTS `' . $this->tabledb_post . '`';
        $this->db->query($sql);

        $sql = 'TRUNCATE TABLE `' . $this->tabledb_attach . '`';
        $this->db->query($sql);
    }

}
