<?php
/**
 * Plugin Name: Premium Patch Order Manager
 * Plugin URI: https://example.com
 * Description: A WordPress plugin for custom patch ordering with interactive configuration.
 * Version: 2.0.0
 * Author: Rakeeza Sattar
 * License: GPL v2 or later
 * Text Domain: custom-patch-order
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PATCH_ORDER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PATCH_ORDER_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('PATCH_ORDER_VERSION', '1.0.0');

class CustomPatchOrder {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Hook into init with higher priority to ensure post type is registered
        add_action('init', array($this, 'register_post_type_early'), 0);
    }
    
    public function init() {
        // Load required files
        $this->load_dependencies();
        
        // Initialize components
        new PatchOrderPostType();
        new PatchOrderForm();
        new PatchOrderAdmin();
        
        // Register shortcode
        add_shortcode('patch_order_form', array($this, 'display_form_shortcode'));
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    private function load_dependencies() {
        require_once PATCH_ORDER_PLUGIN_PATH . 'includes/class-patch-order-post-type.php';
        require_once PATCH_ORDER_PLUGIN_PATH . 'includes/class-patch-order-form.php';
        require_once PATCH_ORDER_PLUGIN_PATH . 'includes/class-patch-order-admin.php';
        require_once PATCH_ORDER_PLUGIN_PATH . 'assets/svg/patch-shapes.php';
    }
    
    public function display_form_shortcode($atts) {
        $atts = shortcode_atts(array(
            'base_price' => '2.82'
        ), $atts);
        
        ob_start();
        include PATCH_ORDER_PLUGIN_PATH . 'templates/patch-order-form.php';
        return ob_get_clean();
    }
    
    public function enqueue_frontend_scripts() {
        if (is_singular() || is_page()) {
            global $post;
            if (has_shortcode($post->post_content, 'patch_order_form')) {
                wp_enqueue_style('patch-order-form-css', PATCH_ORDER_PLUGIN_URL . 'assets/css/patch-order-form.css', array(), PATCH_ORDER_VERSION);
                wp_enqueue_script('patch-order-form-js', PATCH_ORDER_PLUGIN_URL . 'assets/js/patch-order-form.js', array('jquery'), PATCH_ORDER_VERSION, true);
                
                wp_localize_script('patch-order-form-js', 'patchOrderAjax', array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('patch_order_nonce'),
                    'base_price' => get_option('patch_order_base_price', '2.82')
                ));
            }
        }
    }
    
    public function enqueue_admin_scripts($hook) {
        if ($hook == 'post.php' || $hook == 'post-new.php' || $hook == 'edit.php') {
            global $post_type;
            if ($post_type == 'patch_order') {
                wp_enqueue_style('patch-order-admin-css', PATCH_ORDER_PLUGIN_URL . 'assets/css/patch-order-form.css', array(), PATCH_ORDER_VERSION);
            }
        }
    }
    
    public function activate() {
        // Initialize components to register post type
        $this->load_dependencies();
        new PatchOrderPostType();
        
        // Create upload directory
        $upload_dir = wp_upload_dir();
        $patch_upload_dir = $upload_dir['basedir'] . '/patch-orders';
        
        if (!file_exists($patch_upload_dir)) {
            wp_mkdir_p($patch_upload_dir);
        }
        
        // Set default options
        add_option('patch_order_base_price', '2.82');
        add_option('patch_order_admin_email', get_option('admin_email'));
        
        // Flush rewrite rules to register custom post type
        flush_rewrite_rules();
    }
    
    public function register_post_type_early() {
        // Register post type early to avoid "Invalid post type" errors
        $this->load_dependencies();
        
        // Register the post type directly here to ensure it's available
        $labels = array(
            'name' => 'Patch Orders',
            'singular_name' => 'Patch Order',
            'menu_name' => 'Patch Orders',
            'add_new' => 'Add New Order',
            'add_new_item' => 'Add New Patch Order',
            'edit_item' => 'Edit Patch Order',
            'new_item' => 'New Patch Order',
            'view_item' => 'View Patch Order',
            'search_items' => 'Search Patch Orders',
            'not_found' => 'No patch orders found',
            'not_found_in_trash' => 'No patch orders found in trash'
        );
        
        $args = array(
            'labels' => $labels,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'capability_type' => 'post',
            'hierarchical' => false,
            'supports' => array('title', 'editor'),
            'has_archive' => false,
            'rewrite' => false,
            'query_var' => false,
            'show_in_admin_bar' => false
        );
        
        register_post_type('patch_order', $args);
        
        // Now initialize the class
        new PatchOrderPostType();
    }
    
    public function deactivate() {
        flush_rewrite_rules();
    }
}

// Initialize the plugin
new CustomPatchOrder();
?>