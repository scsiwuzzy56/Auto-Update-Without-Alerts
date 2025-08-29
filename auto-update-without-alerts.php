<?php
/*
Plugin Name: Auto Updates Without Alerts
Description: A plugin to disable all auto update email alerts for WordPress, enable automatic core updates, and enable automatic updates for all plugins and themes.
Version: 1.4.0
Author: scsiwuzzy56
Author URI: https://github.com/scsiwuzzy56
*/

// ---------------------
// Admin Menu + Settings
// ---------------------
add_action('admin_menu', 'dauae_add_admin_menu');
add_action('admin_init', 'dauae_settings_init');

function dauae_add_admin_menu() {
    add_options_page(
        'Auto Update Settings',
        'Auto Update Settings',
        'manage_options',
        'dauae_settings',
        'dauae_settings_page'
    );
}

function dauae_settings_init() {
    register_setting('dauae_settings_group', 'dauae_settings');

    add_settings_section(
        'dauae_settings_section',
        __('Auto Update Settings', 'dauae'),
        'dauae_settings_section_callback',
        'dauae_settings'
    );

    $fields = [
        'enable_core_updates'   => __('Enable Core Updates', 'dauae'),
        'enable_plugin_updates' => __('Enable Plugin Updates', 'dauae'),
        'enable_theme_updates'  => __('Enable Theme Updates', 'dauae'),
        'disable_update_emails' => __('Disable Update Emails', 'dauae')
    ];

    foreach ($fields as $id => $label) {
        add_settings_field(
            $id,
            $label,
            'dauae_checkbox_render',
            'dauae_settings',
            'dauae_settings_section',
            ['id' => $id]
        );
    }
}

function dauae_checkbox_render($args) {
    $options = get_option('dauae_settings', []);
    $id = $args['id'];
    $value = $options[$id] ?? 0;
    ?>
    <input type="checkbox" name="dauae_settings[<?php echo esc_attr($id); ?>]" value="1" <?php checked($value, 1); ?>>
    <?php
}

function dauae_settings_section_callback() {
    echo __('Configure the auto update settings below.', 'dauae');
}

function dauae_settings_page() {
    ?>
    <form action="options.php" method="post">
        <h2>Auto Update Settings</h2>
        <?php
        settings_fields('dauae_settings_group');
        do_settings_sections('dauae_settings');
        submit_button();
        ?>
    </form>
    <?php
}

// ---------------------
// Apply Settings
// ---------------------
add_action('plugins_loaded', 'dauae_apply_settings');

function dauae_apply_settings() {
    $options = get_option('dauae_settings', []);

    // Enable automatic core updates
    if (!empty($options['enable_core_updates'])) {
        add_filter('auto_update_core', '__return_true');
    }

    // Enable automatic plugin updates
    if (!empty($options['enable_plugin_updates'])) {
        add_filter('auto_update_plugin', '__return_true');
    }

    // Enable automatic theme updates
    if (!empty($options['enable_theme_updates'])) {
        add_filter('auto_update_theme', '__return_true');
    }

    // Disable ALL update emails (core, plugins, themes)
    if (!empty($options['disable_update_emails'])) {
        add_filter('auto_plugin_update_send_email', '__return_false');
        add_filter('auto_theme_update_send_email', '__return_false');
        add_filter('auto_core_update_send_email', '__return_false', 10, 4);
    }
}

if ( ! class_exists( 'Puc_v4_Factory' ) ) {
    require plugin_dir_path( __FILE__ ) . 'plugin-update-checker/plugin-update-checker.php';
}

$updateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/scsiwuzzy56/auto-updates-without-alerts/', // GitHub repo URL
    __FILE__,                                                      // Plugin file
    'auto-updates-without-alerts'                                  // Plugin slug
);

// Optional: if your repo is private, add auth token like this:
// $updateChecker->setAuthentication('your-personal-access-token');

// Optional: set branch
$updateChecker->setBranch('main');
