<?php
if (!defined('ABSPATH')) {
    exit;
}

class PatchOrderForm {
    
    public function __construct() {
        add_action('wp_ajax_submit_patch_order', array($this, 'handle_form_submission'));
        add_action('wp_ajax_nopriv_submit_patch_order', array($this, 'handle_form_submission'));
        add_action('wp_ajax_upload_patch_file', array($this, 'handle_file_upload'));
        add_action('wp_ajax_nopriv_upload_patch_file', array($this, 'handle_file_upload'));
    }
    
    public function handle_form_submission() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'patch_order_nonce')) {
            wp_die('Security check failed');
        }
        
        // Sanitize input data
        $customer_name = sanitize_text_field($_POST['customer_name']);
        $customer_email = sanitize_email($_POST['customer_email']);
        $customer_phone = sanitize_text_field($_POST['customer_phone']);
        $customer_company = isset($_POST['customer_company']) ? sanitize_text_field($_POST['customer_company']) : '';
        $customer_street = isset($_POST['customer_street']) ? sanitize_text_field($_POST['customer_street']) : '';
        $customer_number = isset($_POST['customer_number']) ? sanitize_text_field($_POST['customer_number']) : '';
        $customer_zip = isset($_POST['customer_zip']) ? sanitize_text_field($_POST['customer_zip']) : '';
        $customer_city = isset($_POST['customer_city']) ? sanitize_text_field($_POST['customer_city']) : '';
        $patch_width = floatval($_POST['patch_width']);
        $patch_height = floatval($_POST['patch_height']);
        $patch_quantity = intval($_POST['patch_quantity']);
        $patch_category = sanitize_text_field($_POST['patch_category']);
        $patch_type = sanitize_text_field($_POST['patch_type']);
        $backing_option = sanitize_text_field($_POST['backing_option']);
        $patch_description = sanitize_textarea_field($_POST['patch_description']);
        $uploaded_file = sanitize_text_field($_POST['uploaded_file']);
        // Price removed from system
        
        // Validate required fields
        if (empty($customer_name) || empty($customer_email) || empty($customer_phone)) {
            wp_send_json_error('Please fill in all required customer information fields.');
            return;
        }
        
        if (!is_email($customer_email)) {
            wp_send_json_error('Please enter a valid email address.');
            return;
        }
        
        if ($patch_width <= 0 || $patch_height <= 0 || $patch_quantity <= 0) {
            wp_send_json_error('Please enter valid patch dimensions and quantity.');
            return;
        }
        
        // Create new patch order post
        $post_data = array(
            'post_title' => 'Patch Order - ' . $customer_name . ' - ' . date('Y-m-d H:i:s'),
            'post_content' => $patch_description,
            'post_status' => 'publish',
            'post_type' => 'patch_order'
        );
        
        $post_id = wp_insert_post($post_data);
        
        if ($post_id) {
            // Save order meta data
            update_post_meta($post_id, '_customer_name', $customer_name);
            update_post_meta($post_id, '_customer_email', $customer_email);
            update_post_meta($post_id, '_customer_phone', $customer_phone);
            update_post_meta($post_id, '_customer_company', $customer_company);
            update_post_meta($post_id, '_customer_street', $customer_street);
            update_post_meta($post_id, '_customer_number', $customer_number);
            update_post_meta($post_id, '_customer_zip', $customer_zip);
            update_post_meta($post_id, '_customer_city', $customer_city);
            update_post_meta($post_id, '_patch_width', $patch_width);
            update_post_meta($post_id, '_patch_height', $patch_height);
            update_post_meta($post_id, '_patch_quantity', $patch_quantity);
            update_post_meta($post_id, '_patch_category', $patch_category);
            update_post_meta($post_id, '_patch_type', $patch_type);
            update_post_meta($post_id, '_backing_option', $backing_option);
            update_post_meta($post_id, '_patch_description', $patch_description);
            update_post_meta($post_id, '_uploaded_file', $uploaded_file);
            update_post_meta($post_id, '_total_price', $total_price);
            update_post_meta($post_id, '_order_date', current_time('mysql'));
            
            // Send email notification to admin
            $this->send_admin_notification($post_id);
            
            wp_send_json_success('Your patch order has been submitted successfully! We will contact you soon.');
        } else {
            wp_send_json_error('Failed to submit your order. Please try again.');
        }
    }
    
    public function handle_file_upload() {
        if (!wp_verify_nonce($_POST['nonce'], 'patch_order_nonce')) {
            wp_die('Security check failed');
        }
        
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        
        $uploadedfile = $_FILES['file'];
        
        // Check file size (max 10MB)
        if ($uploadedfile['size'] > 10485760) {
            wp_send_json_error('File size exceeds 10MB limit.');
            return;
        }
        
        // Check file type
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'ai', 'eps', 'svg');
        $file_type = wp_check_filetype(basename($uploadedfile['name']));
        
        if (!in_array($file_type['ext'], $allowed_types)) {
            wp_send_json_error('Invalid file type. Please upload JPG, PNG, GIF, PDF, AI, EPS, or SVG files only.');
            return;
        }
        
        $upload_overrides = array('test_form' => false);
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
        
        if ($movefile && !isset($movefile['error'])) {
            wp_send_json_success(array(
                'url' => $movefile['url'],
                'file' => $movefile['file']
            ));
        } else {
            wp_send_json_error($movefile['error']);
        }
    }
    
    private function send_admin_notification($post_id) {
        $admin_email = get_option('patch_order_admin_email', get_option('admin_email'));
        $customer_name = get_post_meta($post_id, '_customer_name', true);
        $customer_email = get_post_meta($post_id, '_customer_email', true);
        $total_price = get_post_meta($post_id, '_total_price', true);
        
        $subject = 'New Patch Order Received - ' . $customer_name;
        
        $message = "A new patch order has been received:\n\n";
        $message .= "Customer: " . $customer_name . "\n";
        $message .= "Email: " . $customer_email . "\n";
        $message .= "Total Price: €" . number_format($total_price, 2) . "\n\n";
        $message .= "View full order details in WordPress admin:\n";
        $message .= admin_url('post.php?post=' . $post_id . '&action=edit');
        
        $headers = array('Content-Type: text/plain; charset=UTF-8');
        
        wp_mail($admin_email, $subject, $message, $headers);
    }
}
?>