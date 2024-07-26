<?php
/*
Plugin Name: Auto Updates Without Alerts
Description: A plugin to disable auto update email alerts for plugins and themes in WordPress, enable automatic core updates, and enable automatic updates for all plugins.
Version: 1.2
Author: scsiwuzzy56
Author URI: https://github.com/scsiwuzzy56
*/


// Hook to add the admin menu
add_action('admin_menu', 'dauae_add_admin_menu');
add_action('admin_init', 'dauae_settings_init');

// Function to add the settings page to the admin menu
function dauae_add_admin_menu() {
    add_options_page(
        'Auto Update Settings', 
        'Auto Update Settings', 
        'manage_options', 
        'dauae_settings', 
        'dauae_settings_page'
    );
}

// Function to initialize the settings
function dauae_settings_init() {
    register_setting('dauae_settings_group', 'dauae_settings');

    add_settings_section(
        'dauae_settings_section', 
        __('Auto Update Settings', 'dauae'), 
        'dauae_settings_section_callback', 
        'dauae_settings'
    );

    add_settings_field(
        'enable_core_updates', 
        __('Enable Core Updates', 'dauae'), 
        'dauae_enable_core_updates_render', 
        'dauae_settings', 
        'dauae_settings_section'
    );

    add_settings_field(
        'enable_plugin_updates', 
        __('Enable Plugin Updates', 'dauae'), 
        'dauae_enable_plugin_updates_render', 
        'dauae_settings', 
        'dauae_settings_section'
    );

    add_settings_field(
        'enable_theme_updates', 
        __('Enable Theme Updates', 'dauae'), 
        'dauae_enable_theme_updates_render', 
        'dauae_settings', 
        'dauae_settings_section'
    );

    add_settings_field(
        'disable_update_emails', 
        __('Disable Update Emails', 'dauae'), 
        'dauae_disable_update_emails_render', 
        'dauae_settings', 
        'dauae_settings_section'
    );
}

function dauae_enable_core_updates_render() {
    $options = get_option('dauae_settings');
    ?>
    <input type='checkbox' name='dauae_settings[enable_core_updates]' <?php checked($options['enable_core_updates'], 1); ?> value='1'>
    <?php
}

function dauae_enable_plugin_updates_render() {
    $options = get_option('dauae_settings');
    ?>
    <input type='checkbox' name='dauae_settings[enable_plugin_updates]' <?php checked($options['enable_plugin_updates'], 1); ?> value='1'>
    <?php
}

function dauae_enable_theme_updates_render() {
    $options = get_option('dauae_settings');
    ?>
    <input type='checkbox' name='dauae_settings[enable_theme_updates]' <?php checked($options['enable_theme_updates'], 1); ?> value='1'>
    <?php
}

function dauae_disable_update_emails_render() {
    $options = get_option('dauae_settings');
    ?>
    <input type='checkbox' name='dauae_settings[disable_update_emails]' <?php checked($options['disable_update_emails'], 1); ?> value='1'>
    <?php
}

function dauae_settings_section_callback() {
    echo __('Configure the auto update settings below.', 'dauae');
}

function dauae_settings_page() {
    ?>
    <form action='options.php' method='post'>
        <h2>Auto Update Settings</h2>
        <?php
        settings_fields('dauae_settings_group');
        do_settings_sections('dauae_settings');
        submit_button();
        ?>
    </form>
    <?php
}

// Hook to apply the settings
add_action('init', 'dauae_apply_settings');

function dauae_apply_settings() {
    $options = get_option('dauae_settings');

    // Enable automatic core updates
    if (isset($options['enable_core_updates']) && $options['enable_core_updates']) {
        add_filter('auto_update_core', '__return_true');
    }

    // Enable automatic updates for all plugins
    if (isset($options['enable_plugin_updates']) && $options['enable_plugin_updates']) {
        add_filter('auto_update_plugin', '__return_true');
    }

    // Enable automatic updates for all themes
    if (isset($options['enable_theme_updates']) && $options['enable_theme_updates']) {
        add_filter('auto_update_theme', '__return_true');
    }

    // Disable update email notifications
    if (isset($options['disable_update_emails']) && $options['disable_update_emails']) {
        add_filter('auto_plugin_update_send_email', '__return_false');
        add_filter('auto_theme_update_send_email', '__return_false');
        add_filter('auto_core_update_send_email', 'disable_core_update_emails', 10, 4);
    }
}

// Function to disable core update email notifications
function disable_core_update_emails($send, $type, $core_update, $result) {
    if (!empty($type) && $type == 'success') {
        return false;
    }
    return $send;
}
?>
