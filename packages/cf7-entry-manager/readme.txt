=== Entry Manager for Contact Form 7 ===
Contributors: ferywardiyanto
Tags: contact form 7, cf7 entry manager, leads capture, form storage, contact form submissions
Plugin Name: Entry Manager for Contact Form 7
Description: Never lose a lead again. Save, manage, and convert every Contact Form 7 submission directly in your WordPress dashboard.
Text Domain: cf7-entry-manager
Stable tag: 0.1.0
Tested up to: 6.9
Requires at least: 6.0
Requires PHP: 8.1
Author: Fery Wardiyanto
Author URI: https://feryardiant.id
License: GPLv3 or later
Requires Plugins: contact-form-7
Donate link: https://github.com/sponsors/feryardiant

Never lose a lead again. Save, manage, and convert every Contact Form 7 submission directly in your WordPress dashboard.

== Description ==

Stop relying on unreliable email notifications. **Entry Manager for Contact Form 7** acts as your ultimate safety net, capturing every single submission and storing it securely in your WordPress database.

Whether it's a server error, a spam filter, or a full inbox, you can rest easy knowing your data is safe and accessible right from your dashboard.

= Smart Lead Generation =
Unlike basic storage plugins, this plugin allows you to map submissions to WordPress users. Automatically register your leads as "Subscribers" and capture their Phone Numbers, making it the perfect bridge between your forms and your CRM.

= Key Features =
*   **Zero-Loss Capture:** Every submission is recorded before the email is even sent.
*   **Smart Author Mapping:** Automatically create or update WordPress users from form entries.
*   **Custom Field Mapping:** Map your form tags to specific submission properties (Subject, Name, Email, Phone).
*   **Read/Unread Status:** Keep track of which leads you've already handled.
*   **Per-Form Control:** Choose exactly which forms should record data and which shouldn't.
*   **Developer Friendly:** Lightweight architecture with local SMTP support for dev environments.

= Minimum Requirements =

* WordPress 6.0 or greater
* PHP version 8.1 or greater

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Edit your Contact Form 7 form and look for the new "Submissions" tab.
4. Enable "Record" and map your fields.

== Screenshots ==

1. **Capture Settings**: Map form tags to submission properties and enable lead storage for each Contact Form 7 form.
2. **Submissions Dashboard**: A centralized hub to manage all captured leads with read/unread status and smart author integration.
3. **Detailed Entry View**: Inspect full submission data, metadata, and mapped author information in a clean, organized layout.

== Frequently Asked Questions ==

= Does this work with any CF7 form? =
Yes! You can configure the submission settings for each form individually.

= Where are the submissions stored? =
Submissions are stored as a private Custom Post Type called `form-submissions`, ensuring they are indexed and secure without bloating your options table.

= Can I export the data? =
In the current version (0.1.0), you can manage them via the dashboard. CSV Export is a planned feature for future updates.

== Changelog ==

= 0.1.0 =
* Initial release.
