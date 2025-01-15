<?php

// Hook into the email header to switch locale before email content
add_action('woocommerce_email_header','set_email_locale_based_on_order', 10, 2);
add_action('woocommerce_email_before_order_table','set_email_locale_based_on_order', 10, 2);

// Hook into the email footer to restore the original locale after email content
add_action('woocommerce_email_footer','restore_email_locale', 10, 1);

// Hook into resending emails to handle locale switching
add_action('woocommerce_before_resend_order_emails','set_locale_on_woocommerce_before_resend_order_emails', 10, 1);

// Hook into email recipient filter to switch locale before generating the subject
add_filter('woocommerce_email_recipient_new_order','set_locale_on_woocommerce_email_recipient_new_order', 10, 2);

// Hook into WooCommerce email subject filters for locale switching
add_filter('woocommerce_email_subject_customer_new_order', 'set_email_subject_locale', 10, 2);
add_filter('woocommerce_email_subject_customer_processing_order', 'set_email_subject_locale', 10, 2);
add_filter('woocommerce_email_subject_customer_completed_order', 'set_email_subject_locale', 10, 2);

function set_email_locale_based_on_order($email_heading, $email)
{   
    if ($email->object != null) {
        // Get the billing country from the order
        $billing_country = $email->object->get_billing_country();

        // Determine the locale based on the billing country
        $order_locale = get_locale_from_billing_country($billing_country);

        if ($order_locale) {
            // Switch to the order's locale
            switch_to_locale($order_locale);
        }
    }
}


function restore_email_locale()
{
    restore_previous_locale();
}


function set_locale_on_woocommerce_before_resend_order_emails($order)
{
    if ($order) {
        // Get the billing country from the order
        $billing_country = $order->get_billing_country();

        // Determine the locale based on the billing country
        $order_locale = get_locale_from_billing_country($billing_country);

        if ($order_locale) {
            // Switch to the order's locale
            switch_to_locale($order_locale);
        }
    }
}


function set_locale_on_woocommerce_email_recipient_new_order($to, $order)
{
    // Get the billing country from the order
    $billing_country = $order->get_billing_country();

    // Determine the locale based on the billing country
    $order_locale = get_locale_from_billing_country($billing_country);

    if ($order_locale) {
        // Switch to the order's locale before generating the subject
        switch_to_locale($order_locale);
    }

    return $to;
}

/**
 * Switch locale for email subjects.
 */
function set_email_subject_locale($subject, $order)
{
    if ($order) {
        $billing_country = $order->get_billing_country();
        $order_locale = get_locale_from_billing_country($billing_country);

        if ($order_locale) {
            switch_to_locale($order_locale);
            // Load the translation file using Loco Translate
            $domain = 'woocommerce'; // WooCommerce text domain

            // Check if the translation file exists for the user's locale
            $locale_file = WP_LANG_DIR . '/plugins/' . $domain . '-' . $order_locale . '.po';
            if (file_exists($locale_file)) {
                // Load the .mo file for the given locale
                load_textdomain($domain, $locale_file);
            }
            
            // Get the site name (e.g., "Baobab Miniatures")
            $site_name = get_bloginfo('name');
            
            // Replace the site name with '{site_title}' in the string
            $subject_with_placeholder = str_replace($site_name, '{site_title}', $subject);

            // Translate the subject based on the current locale
            $subject = __($subject_with_placeholder, $domain);
            
            $subject = str_replace('{site_title}', $site_name, $subject);
        }
    }

    return $subject;
}

/**
 * Map billing country to WordPress locale.
 *
 * @param string $billing_country The billing country code.
 * @return string The locale code for the country.
 */
function get_locale_from_billing_country($billing_country)
{
    $locale_mapping = [
        'IT' => 'it_IT', // Italy
        // Add more countries and locales as needed
    ];

    return $locale_mapping[$billing_country] ?? 'en_US'; // Default to English
}
