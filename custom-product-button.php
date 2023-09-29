<?php
/*
Plugin Name: Custom WooCommerce Buttons
Description: Adds up to 4 custom buttons below the default "Add to Cart" button on the single product page with customizable text and URL.
Author: Oka bRionZ
Author URI: https://www.okabrionz.com
*/

// Add custom buttons below the default "Add to Cart" button on the single product page
function custom_woocommerce_enqueue_styles()
{
    // Enqueue your custom CSS file
    wp_enqueue_style('custom-woocommerce-styles', plugin_dir_url(__FILE__) . 'custom-woocommerce-buttons.css');
}

// Hook into WordPress's 'wp_enqueue_scripts' action to load your CSS
add_action('wp_enqueue_scripts', 'custom_woocommerce_enqueue_styles');

function custom_woocommerce_product_buttons()
{
    global $product;
    echo '<h4 class="custom-button-title">Beli di:</h4>';
    // Loop to create and display up to 4 custom buttons
    for ($i = 1; $i <= 4; $i++) {
        // Get the button text and URL from custom fields
        $button_text = get_post_meta($product->get_id(), "_custom_button_text_$i", true);
        $button_url = get_post_meta($product->get_id(), "_custom_button_url_$i", true);

        // Use default values if custom values are not set
        if (empty($button_text)) {
            $button_text = "Custom Button $i";
        }

        // Check if URL is provided before displaying the button
        if ($button_url) {
            echo '<a href="' . esc_url($button_url) . '" target="_blank" class="custom-button custom-button-1">' . esc_html($button_text) . '</a>';
        }
    }
}
add_action('woocommerce_after_add_to_cart_button', 'custom_woocommerce_product_buttons');

// Add custom fields for up to 4 buttons in product data
function custom_woocommerce_product_custom_fields()
{
    global $post;

    for ($i = 1; $i <= 4; $i++) {
        woocommerce_wp_text_input(
            array(
                'id' => "_custom_button_text_$i",
                'label' => "Custom Button $i Text",
                'desc_tip' => 'true',
                'description' => "Enter the text for Custom Button $i.",
            )
        );

        woocommerce_wp_text_input(
            array(
                'id' => "_custom_button_url_$i",
                'label' => "Custom Button $i URL",
                'desc_tip' => 'true',
                'description' => "Enter the URL for Custom Button $i.",
            )
        );
    }
}
add_action('woocommerce_product_options_general_product_data', 'custom_woocommerce_product_custom_fields');

// Save custom fields for up to 4 buttons when the product is saved
function custom_woocommerce_product_save_custom_fields($post_id)
{
    for ($i = 1; $i <= 4; $i++) {
        $button_text = isset($_POST["_custom_button_text_$i"]) ? sanitize_text_field($_POST["_custom_button_text_$i"]) : '';
        $button_url = isset($_POST["_custom_button_url_$i"]) ? esc_url($_POST["_custom_button_url_$i"]) : '';

        update_post_meta($post_id, "_custom_button_text_$i", $button_text);
        update_post_meta($post_id, "_custom_button_url_$i", $button_url);
    }
}
add_action('woocommerce_process_product_meta', 'custom_woocommerce_product_save_custom_fields');
