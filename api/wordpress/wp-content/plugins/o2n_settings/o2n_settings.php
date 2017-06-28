<?php
/**
 * Plugin Name: O2N Settings
 * Plugin URI:
 * Description: Set settings for the o2n API
 * Version: 0.0.1
 * Author: Bram van Osta
 * Author URI: http://ecvdigital.fr/
 * License: GPL2
 */
require_once 'o2n_api.php';
class O2nSettings
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $o2nApi;
    /**
     * Start up
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
        add_action('update_option_settings_options', array($this, 'update_settings_options'), 10, 2);
        $this->o2nApi = new O2nApi();
    }
    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        add_menu_page(
            'Settings',
            'API Settings',
            'administrator',
            'api-settings',
            array($this, 'create_admin_page'),
            'dashicons-admin-generic'
        );
    }
    public function update_settings_options($old_value, $new_value)
    {
        // Call Lumen API to update its database
        $this->o2nApi->setSettings($new_value);
    }
    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option('settings_options');
        ?>
        <div class="wrap">
            <h1>API Settings</h1>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields('o2n_settings_group');
                do_settings_sections('api-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'o2n_settings_group', // Option group
            'settings_options', // Option name
            array($this, 'sanitize') // Sanitize
        );
        /***********************************
        ************** TWITTER *************
        ***********************************/
        // Options section
        add_settings_section(
            'setting_section_twitter_options', // ID
            'Twitter settings', // Title
            array($this, 'print_options_section_info'), // Callback
            'api-settings' // Page
        );
        add_settings_field(
            'twitter_username', // ID
            'Username', // Title
            array($this, 'twitter_username_callback'), // Callback
            'api-settings', // Page
            'setting_section_twitter_options' // Section
        );
        add_settings_field(
            'twitter_oauth_access_token', // ID
            'OAuth Access Token', // Title
            array($this, 'twitter_oauth_access_token_callback'), // Callback
            'api-settings', // Page
            'setting_section_twitter_options' // Section
        );
        add_settings_field(
            'twitter_oauth_access_token_secret', // ID
            'OAuth Access Token Secret', // Title
            array($this, 'twitter_oauth_access_token_secret_callback'), // Callback
            'api-settings', // Page
            'setting_section_twitter_options' // Section
        );
        add_settings_field(
            'twitter_consumer_key', // ID
            'Consumer Key', // Title
            array($this, 'twitter_consumer_key_callback'), // Callback
            'api-settings', // Page
            'setting_section_twitter_options' // Section
        );
        add_settings_field(
            'twitter_consumer_secret', // ID
            'Consumer Secret', // Title
            array($this, 'twitter_consumer_secret_callback'), // Callback
            'api-settings', // Page
            'setting_section_twitter_options' // Section
        );
        /***********************************
        ************** YOUTUBE *************
        ***********************************/
        add_settings_section(
            'setting_section_youtube_options', // ID
            'Youtube settings', // Title
            array($this, 'print_options_section_info'), // Callback
            'api-settings' // Page
        );
        add_settings_field(
            'youtube_username', // ID
            'Username', // Title
            array($this, 'youtube_username_callback'), // Callback
            'api-settings', // Page
            'setting_section_youtube_options' // Section
        );
        add_settings_field(
            'youtube_api_key', // ID
            'Api Key', // Title
            array($this, 'youtube_api_key_callback'), // Callback
            'api-settings', // Page
            'setting_section_youtube_options' // Section
        );
        add_settings_field(
            'youtube_max_results', // ID
            'Max Results', // Title
            array($this, 'youtube_max_results_callback'), // Callback
            'api-settings', // Page
            'setting_section_youtube_options' // Section
        );
        /***********************************
        ************* INSTAGRAM ************
        ***********************************/

        add_settings_section(
            'setting_section_instagram_options', // ID
            'Instagram settings', // Title
            array($this, 'print_options_section_info'), // Callback
            'api-settings' // Page
        );

        add_settings_field(
            'instagram_max_results', // ID
            'Max Results', // Title
            array($this, 'instagram_max_results_callback'), // Callback
            'api-settings', // Page
            'setting_section_instagram_options' // Section
        );

        add_settings_field(
            'instagram_token', // ID
            'Token', // Title
            array($this, 'instagram_token_callback'), // Callback
            'api-settings', // Page
            'setting_section_instagram_options' // Section
        );

        /***********************************
        ************** PAYPAL **************
        ***********************************/
        add_settings_section(
            'setting_section_paypal_options', // ID
            'Paypal settings', // Title
            array($this, 'print_options_section_info'), // Callback
            'api-settings' // Page
        );
        add_settings_field(
            'paypal_client_id', // ID
            'Client Id', // Title
            array($this, 'paypal_client_id_callback'), // Callback
            'api-settings', // Page
            'setting_section_paypal_options' // Section
        );
        add_settings_field(
            'paypal_client_secret', // ID
            'Client Secret', // Title
            array($this, 'paypal_client_secret_callback'), // Callback
            'api-settings', // Page
            'setting_section_paypal_options' // Section
        );
        /***********************************
        ************ WOO COMMERCE **********
        ***********************************/
        add_settings_section(
            'setting_section_woocommerce_options', // ID
            'WooCommerce settings', // Title
            array($this, 'print_options_section_info'), // Callback
            'api-settings' // Page
        );
        add_settings_field(
            'woocommerce_consumer_key', // ID
            'Consumer Key', // Title
            array($this, 'woocommerce_consumer_key_callback'), // Callback
            'api-settings', // Page
            'setting_section_woocommerce_options' // Section
        );
        add_settings_field(
            'woocommerce_consumer_secret', // ID
            'Consumer Secret', // Title
            array($this, 'woocommerce_consumer_secret_callback'), // Callback
            'api-settings', // Page
            'setting_section_woocommerce_options' // Section
        );
        /***********************************
        ************ SOUNDCLOUD ************
        ***********************************/
        add_settings_section(
          'setting_section_soundcloud_options', // ID
          'Soundcloud settings', // Title
          array($this, 'print_options_section_info'), // Callback
          'api-settings' // Page
        );
        add_settings_field(
          'soundcloud_api_key', // ID
          'API key', // Title
          array($this, 'soundcloud_api_key_callback'), // Callback
          'api-settings', // Page
          'setting_section_soundcloud_options' // Section
        );
        add_settings_field(
          'soundcloud_user_id', // ID
          'User ID', // Title
          array($this, 'soundcloud_user_id_callback'), // Callback
          'api-settings', // Page
          'setting_section_soundcloud_options' // Section
        );

        /***********************************
        ********* Google Analytics *********
        ***********************************/

        add_settings_section(
          'setting_section_analytics_options', // ID
          'Analytics settings', // Title
          array($this, 'print_options_section_info'), // Callback
          'api-settings' // Page
        );
        add_settings_field(
          'analytics_api_key', // ID
          'API key', // Title
          array($this, 'analytics_api_key_callback'), // Callback
          'api-settings', // Page
          'setting_section_analytics_options' // Section
        );
    }
    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     * @return array $new_input
     */
    public function sanitize($input)
    {
        $new_input = array();
        /***********************************
        ************** TWITTER *************
        ***********************************/
        if (isset($input['twitter_username']))
            $new_input['twitter_username'] = sanitize_text_field($input['twitter_username']);
        if (isset($input['twitter_oauth_access_token']))
            $new_input['twitter_oauth_access_token'] = sanitize_text_field($input['twitter_oauth_access_token']);
        if (isset($input['twitter_oauth_access_token_secret']))
            $new_input['twitter_oauth_access_token_secret'] = sanitize_text_field($input['twitter_oauth_access_token_secret']);
        if (isset($input['twitter_consumer_key']))
            $new_input['twitter_consumer_key'] = sanitize_text_field($input['twitter_consumer_key']);
        if (isset($input['twitter_consumer_secret']))
            $new_input['twitter_consumer_secret'] = sanitize_text_field($input['twitter_consumer_secret']);
        /***********************************
        ************** YOUTUBE *************
        ***********************************/
        if (isset($input['youtube_username']))
            $new_input['youtube_username'] = sanitize_text_field($input['youtube_username']);
        if (isset($input['youtube_api_key']))
            $new_input['youtube_api_key'] = sanitize_text_field($input['youtube_api_key']);
        if (isset($input['youtube_max_results']))
            $new_input['youtube_max_results'] = sanitize_text_field($input['youtube_max_results']);
        /***********************************
        ************* SOUNDCLOUD ***********
        ***********************************/
        if (isset($input['soundcloud_api_key']))
            $new_input['soundcloud_api_key'] = sanitize_text_field($input['soundcloud_api_key']);
        if (isset($input['soundcloud_user_id']))
            $new_input['soundcloud_user_id'] = sanitize_text_field($input['soundcloud_user_id']);
        /***********************************
        ************** INSTAGRAM ***********
        ***********************************/

        if (isset($input['instagram_max_results']))
            $new_input['instagram_max_results'] = sanitize_text_field($input['instagram_max_results']);

        if (isset($input['instagram_token']))
            $new_input['instagram_token'] = sanitize_text_field($input['instagram_token']);

        /***********************************
        ************** PAYPAL **************
        ***********************************/
        if (isset($input['paypal_client_id']))
            $new_input['paypal_client_id'] = sanitize_text_field($input['paypal_client_id']);
        if (isset($input['paypal_client_secret']))
            $new_input['paypal_client_secret'] = sanitize_text_field($input['paypal_client_secret']);
        /***********************************
        ************ WOO COMMERCE **********
        ***********************************/
        if (isset($input['woocommerce_consumer_key']))
            $new_input['woocommerce_consumer_key'] = sanitize_text_field($input['woocommerce_consumer_key']);
        if (isset($input['woocommerce_consumer_secret']))
            $new_input['woocommerce_consumer_secret'] = sanitize_text_field($input['woocommerce_consumer_secret']);


        /***********************************
        ************* ANALYTICS ************
        ***********************************/
        if (isset($input['analytics_api_key']))
            $new_input['analytics_api_key'] = sanitize_text_field($input['analytics_api_key']);

        return $new_input;
    }
    /**
     * Print the Options Section text
     */
    public function print_options_section_info()
    {
        print 'API Settings';
    }
    /**
     * Get the settings option array and print one of its values
     */
    /***********************************
    ************** TWITTER *************
    ***********************************/
    public function twitter_username_callback()
    {
        printf(
            '<input type="text" id="twitter_username" name="settings_options[twitter_username]" value="%s" />',
            isset($this->options['twitter_username']) ? esc_attr($this->options['twitter_username']) : ''
        );
    }
    public function twitter_oauth_access_token_callback()
    {
        printf(
            '<input type="text" id="twitter_oauth_access_token" name="settings_options[twitter_oauth_access_token]" value="%s" />',
            isset($this->options['twitter_oauth_access_token']) ? esc_attr($this->options['twitter_oauth_access_token']) : ''
        );
    }
    public function twitter_oauth_access_token_secret_callback()
    {
        printf(
            '<input type="text" id="twitter_oauth_access_token_secret" name="settings_options[twitter_oauth_access_token_secret]" value="%s" />',
            isset($this->options['twitter_oauth_access_token_secret']) ? esc_attr($this->options['twitter_oauth_access_token_secret']) : ''
        );
    }
    public function twitter_consumer_key_callback()
    {
        printf(
            '<input type="text" id="twitter_consumer_key" name="settings_options[twitter_consumer_key]" value="%s" />',
            isset($this->options['twitter_consumer_key']) ? esc_attr($this->options['twitter_consumer_key']) : ''
        );
    }
    public function twitter_consumer_secret_callback()
    {
        printf(
            '<input type="text" id="twitter_consumer_secret" name="settings_options[twitter_consumer_secret]" value="%s" />',
            isset($this->options['twitter_consumer_secret']) ? esc_attr($this->options['twitter_consumer_secret']) : ''
        );
    }
    /***********************************
    ************** YOUTUBE *************
    ***********************************/
    public function youtube_username_callback()
    {
        printf(
            '<input type="text" id="youtube_username" name="settings_options[youtube_username]" value="%s" />',
            isset($this->options['youtube_username']) ? esc_attr($this->options['youtube_username']) : ''
        );
    }
    public function youtube_api_key_callback()
    {
        printf(
            '<input type="text" id="youtube_api_key" name="settings_options[youtube_api_key]" value="%s" />',
            isset($this->options['youtube_api_key']) ? esc_attr($this->options['youtube_api_key']) : ''
        );
    }
    public function youtube_max_results_callback()
    {
        printf(
            '<input type="number" id="youtube_max_results" name="settings_options[youtube_max_results]" value="%s" />',
            isset($this->options['youtube_max_results']) ? esc_attr($this->options['youtube_max_results']) : ''
        );
    }
    /***********************************
    ************* SOUNDCLOUD ***********
    ***********************************/
    public function soundcloud_api_key_callback()
    {
        printf(
            '<input type="text" id="soundcloud_api_key" name="settings_options[soundcloud_api_key]" value="%s" />',
            isset($this->options['soundcloud_api_key']) ? esc_attr($this->options['soundcloud_api_key']) : ''
        );
    }
    public function soundcloud_user_id_callback()
    {
        printf(
            '<input type="text" id="soundcloud_user_id" name="settings_options[soundcloud_user_id]" value="%s" />',
            isset($this->options['soundcloud_user_id']) ? esc_attr($this->options['soundcloud_user_id']) : ''
        );
    }
    /***********************************
    ************** INSTAGRAM ***********
    ***********************************/

    public function instagram_max_results_callback()
    {
        printf(
            '<input type="number" id="instagram_max_results" name="settings_options[instagram_max_results]" value="%s" />',
            isset($this->options['instagram_max_results']) ? esc_attr($this->options['instagram_max_results']) : ''
        );
    }

    public function instagram_token_callback()
    {
        printf(
            '<input type="text" id="instagram_token" name="settings_options[instagram_token]" value="%s" />',
            isset($this->options['instagram_token']) ? esc_attr($this->options['instagram_token']) : ''
        );
    }

    /***********************************
    ************** PAYPAL **************
    ***********************************/
    public function paypal_client_id_callback()
    {
        printf(
            '<input type="text" id="paypal_client_id" name="settings_options[paypal_client_id]" value="%s" />',
            isset($this->options['paypal_client_id']) ? esc_attr($this->options['paypal_client_id']) : ''
        );
    }
    public function paypal_client_secret_callback()
    {
        printf(
            '<input type="text" id="paypal_client_secret" name="settings_options[paypal_client_secret]" value="%s" />',
            isset($this->options['paypal_client_secret']) ? esc_attr($this->options['paypal_client_secret']) : ''
        );
    }
    /***********************************
    ************ WOO COMMERCE **********
    ***********************************/
    public function woocommerce_consumer_key_callback()
    {
        printf(
            '<input type="text" id="woocommerce_consumer_key" name="settings_options[woocommerce_consumer_key]" value="%s" />',
            isset($this->options['woocommerce_consumer_key']) ? esc_attr($this->options['woocommerce_consumer_key']) : ''
        );
    }
    public function woocommerce_consumer_secret_callback()
    {
        printf(
            '<input type="text" id="woocommerce_consumer_secret" name="settings_options[woocommerce_consumer_secret]" value="%s" />',
            isset($this->options['woocommerce_consumer_secret']) ? esc_attr($this->options['woocommerce_consumer_secret']) : ''
        );
    }

    /***********************************
    ************* ANALYTICS ************
    ***********************************/
    public function analytics_api_key_callback()
    {
        printf(
            '<input type="text" id="analytics_api_key" name="settings_options[analytics_api_key]" value="%s" />',
            isset($this->options['analytics_api_key']) ? esc_attr($this->options['analytics_api_key']) : ''
        );
    }
}
if (is_admin())
    $my_settings_page = new O2nSettings();
