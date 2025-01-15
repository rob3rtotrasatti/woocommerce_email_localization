# woocommerce_email_localization
This script is useful on WooCommerce for translate the emails sended by the plugin.
Is for use with Loco Translate or similar plugin.
Is possible also to use without plugin and change values manually.

## Usage/Examples

The below code is useful for load some translation file and translate some content.
The replace of {site_title} in Loco Translate file is necessary for find the string and for translate it.

```php
<?php>
// Load the translation file using Loco Translate
$domain = 'woocommerce'; // WooCommerce text domain

// Check if the translation file exists for the user's locale
$locale_file = WP_LANG_DIR . '/plugins/' . $domain . '-' . $order_locale . '.po';
if (file_exists($locale_file)) {
    // Load the .mo file for the given locale
    load_textdomain($domain, $locale_file);
}

// Get the site name
$site_name = get_bloginfo('name');

// Replace the site name with '{site_title}' in the string
$subject_with_placeholder = str_replace($site_name, '{site_title}', $subject);

// Translate the subject based on the current locale
$subject = __($subject_with_placeholder, $domain);

$subject = str_replace('{site_title}', $site_name, $subject);
```

