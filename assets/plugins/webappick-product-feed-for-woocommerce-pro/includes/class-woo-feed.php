<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://webappick.com
 * @since      1.0.0
 *
 * @package    Woo_Feed
 * @subpackage Woo_Feed/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Woo_Feed
 * @subpackage Woo_Feed/includes
 * @author     Wahid <wahid0003@gmail.com.com>
 */
class Woo_Feed
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Woo_Feed_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $woo_feed The string used to uniquely identify this plugin.
     */
    protected $woo_feed;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {

        $this->woo_feed = 'woo-feed';
        $this->version = '1.5.18';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
    }


    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Woo_Feed_Loader. Orchestrates the hooks of the plugin.
     * - Woo_Feed_i18n. Defines internationalization functionality.
     * - Woo_Feed_Admin. Defines all hooks for the admin area.
     * - Woo_Feed_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-woo-feed-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-woo-feed-i18n.php';

        /**
         * The class responsible for getting all product information
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/classes/class-woo-feed-products.php';
        /**
         * The class responsible for processing feed
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/classes/class-woo-feed-engine.php';

        /**
         * The class contain all merchants attribute dropdown
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/classes/class-woo-feed-dropdown.php';

        /**
         * The class contain merchant attributes
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/classes/class-woo-feed-default-attributes.php';

        /**
         * The class responsible for generating feed
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/feeds/class-woo-feed-generate.php';

        /**
         * The class is a FTP library
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/classes/class-woo-feed-ftp.php';


        /**
         * The class responsible for save feed
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/classes/class-woo-feed-savefile.php';

        /**
         * Merchant classes
         */
        require plugin_dir_path(dirname(__FILE__)) . 'includes/feeds/class-woo-feed-google.php';
        require plugin_dir_path(dirname(__FILE__)) . 'includes/feeds/class-woo-feed-facebook.php';
        require plugin_dir_path(dirname(__FILE__)) . 'includes/feeds/class-woo-feed-nextag.php';
        require plugin_dir_path(dirname(__FILE__)) . 'includes/feeds/class-woo-feed-kelkoo.php';
        require plugin_dir_path(dirname(__FILE__)) . 'includes/feeds/class-woo-feed-pricegrabber.php';
        require plugin_dir_path(dirname(__FILE__)) . 'includes/feeds/class-woo-feed-shopzilla.php';
        require plugin_dir_path(dirname(__FILE__)) . 'includes/feeds/class-woo-feed-shopmania.php';
        require plugin_dir_path(dirname(__FILE__)) . 'includes/feeds/class-woo-feed-shopping.php';
        require plugin_dir_path(dirname(__FILE__)) . 'includes/feeds/class-woo-feed-bing.php';
        require plugin_dir_path(dirname(__FILE__)) . 'includes/feeds/class-woo-feed-become.php';
        require plugin_dir_path(dirname(__FILE__)) . 'includes/feeds/class-woo-feed-connexity.php';
        require plugin_dir_path(dirname(__FILE__)) . 'includes/feeds/class-woo-feed-custom.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-woo-feed-admin.php';

        /**
         * The class responsible for making list table
         */
        if (!class_exists('WP_List_Table')) {
            //require_once(ABSPATH . 'wp-admin/includes/screen.php');//added
            require_once(ABSPATH . 'wp-admin/includes/screen.php');//added
            require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
            require_once(ABSPATH . 'wp-admin/includes/template.php');
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/classes/class-woo-feed-list-table.php';

        /**
         * The class responsible for making category list
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-woo-feed-category-list.php';

        /**
         * The class responsible for making feed list
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-woo-feed-manage-list.php';
        /**
         * The class responsible for making feed list
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-woo-feed-option-list.php';
        /**
         * The class responsible for making dynamic attribute list
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-woo-feed-dynamic-attribute-list.php';


        $this->loader = new Woo_Feed_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Woo_Feed_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Woo_Feed_i18n();
        $plugin_i18n->set_domain($this->get_woo_feed());

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {

        $plugin_admin = new Woo_Feed_Admin($this->get_woo_feed(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'load_admin_pages');

    }


    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_woo_feed()
    {
        return $this->woo_feed;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Woo_Feed_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

}


class API_Manager_WPFP
{

    /**
     * Self Upgrade Values
     */
    // Base URL to the remote upgrade API Manager server. If not set then the Author URI is used.
    public $upgrade_url = 'https://webappick.com/';

    /**
     * @var string
     */
    public $version = '1.5.18';

    /**
     * @var string
     * This version is saved after an upgrade to compare this db version to $version
     */
    public $api_manager_wpfp_version_name = 'plugin_wpfp_version';

    /**
     * @var string
     */
    public $plugin_url;

    /**
     * @var string
     * used to defined localization for translation, but a string literal is preferred
     *
     */
    public $text_domain = 'woo-feed';

    /**
     * Data defaults
     * @var mixed
     */
    private $ame_software_product_id;

    public $ame_data_key;
    public $ame_api_key;
    public $ame_activation_email;
    public $ame_product_id_key;
    public $ame_instance_key;
    public $ame_deactivate_checkbox_key;
    public $ame_activated_key;

    public $ame_deactivate_checkbox;
    public $ame_activation_tab_key;
    public $ame_deactivation_tab_key;
    public $ame_settings_menu_title;
    public $ame_settings_title;
    public $ame_menu_tab_activation_title;
    public $ame_menu_tab_deactivation_title;

    public $ame_options;
    public $ame_plugin_name;
    public $ame_product_id;
    public $ame_renew_license_url;
    public $ame_instance_id;
    public $ame_domain;
    public $ame_software_version;
    public $ame_plugin_or_theme;

    public $ame_update_version;

    /**
     * Used to send any extra information.
     * @var mixed array, object, string, etc.
     */
    public $ame_extra;

    /**
     * @var The single instance of the class
     */
    protected static $_instance = null;

    public static function instance()
    {

        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Cloning is forbidden.
     *
     * @since 1.2
     */
    public function __clone()
    {
    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.2
     */
    public function __wakeup()
    {
    }

    public function __construct()
    {

        // Run the activation function
        // register_activation_hook(__FILE__, array($this, 'activation'));

        // Ready for translation
        //load_plugin_textdomain( $this->text_domain, false, dirname( untrailingslashit( plugin_basename( __FILE__ ) ) ) . '/languages' );

        if (is_admin()) {

            // Check for external connection blocking
            add_action('admin_notices', array($this, 'check_external_blocking'));

            /**
             * Software Product ID is the product title string
             * This value must be unique, and it must match the API tab for the product in WooCommerce
             */
            $this->ame_software_product_id = 'WooCommerce Product Feed Pro';

            /**
             * Set all data defaults here
             */
            $this->ame_data_key = 'api_manager_wpfp';
            $this->ame_api_key = 'api_key';
            $this->ame_activation_email = 'activation_email';
            $this->ame_product_id_key = 'api_manager_wpfp_product_id';
            $this->ame_instance_key = 'api_manager_wpfp_instance';
            $this->ame_deactivate_checkbox_key = 'api_manager_wpfp_deactivate_checkbox';
            $this->ame_activated_key = 'wpfp_activated';

            /**
             * Set all admin menu data
             */
            $this->ame_deactivate_checkbox = 'am_deactivate_checkbox';
            $this->ame_activation_tab_key = 'api_manager_wpfp_dashboard';
            $this->ame_deactivation_tab_key = 'api_manager_wpfp_deactivation';
            $this->ame_settings_menu_title = 'WooCommerce Product Feed Pro';
            $this->ame_settings_title = 'WooCommerce Product Feed Pro';
            $this->ame_menu_tab_activation_title = __('License Activation', 'woo-feed');
            $this->ame_menu_tab_deactivation_title = __('License Deactivation', 'woo-feed');

            /**
             * Set all software update data here
             */
            $this->ame_options = get_option($this->ame_data_key);
            $this->ame_plugin_name = "webappick-product-feed-for-woocommerce-pro/webappick-product-feed-for-woocommerce-pro.php"; //untrailingslashit(plugin_basename(__FILE__)); // same as plugin slug. if a theme use a theme name like 'twentyeleven'
            $this->ame_product_id = get_option($this->ame_product_id_key); // Software Title
            $this->ame_renew_license_url = 'https://webappick.com/my-account/'; // URL to renew a license. Trailing slash in the upgrade_url is required.
            $this->ame_instance_id = get_option($this->ame_instance_key); // Instance ID (unique to each blog activation)

            /**
             * Some web hosts have security policies that block the : (colon) and // (slashes) in http://,
             * so only the host portion of the URL can be sent. For example the host portion might be
             * www.example.com or example.com. http://www.example.com includes the scheme http,
             * and the host www.example.com.
             * Sending only the host also eliminates issues when a client site changes from http to https,
             * but their activation still uses the original scheme.
             * To send only the host, use a line like the one below:
             *
             * $this->ame_domain = str_ireplace( array( 'http://', 'https://' ), '', home_url() ); // blog domain name
             */

            $this->ame_domain = str_ireplace(array('http://', 'https://'), '', home_url()); // blog domain name
            $this->ame_software_version = $this->version; // The software version
            $this->ame_plugin_or_theme = 'plugin';

            // Performs activations and deactivations of API License Keys
            require_once(plugin_dir_path(__FILE__) . 'classes/class-wc-key-api.php');

            // Checks for software updatess
            require_once(plugin_dir_path(__FILE__) . 'classes/class-wc-plugin-update.php');

            // Admin menu with the license key and license email form
            require_once(plugin_dir_path(__FILE__) . 'classes/class-wc-api-manager-menu.php');

            $options = get_option($this->ame_data_key);

            /**
             * Check for software updates
             */
            if (!empty($options) && $options !== false) {

                $this->update_check(
                    $this->upgrade_url,
                    $this->ame_plugin_name,
                    $this->ame_product_id,
                    $this->ame_options[$this->ame_api_key],
                    $this->ame_options[$this->ame_activation_email],
                    $this->ame_renew_license_url,
                    $this->ame_instance_id,
                    $this->ame_domain,
                    $this->ame_software_version,
                    $this->ame_plugin_or_theme,
                    $this->text_domain
                );
            }
        }

        /**
         * Deletes all data if plugin deactivated
         */
        //register_deactivation_hook(__FILE__, array($this, 'uninstall'));

    }

    /** Load Shared Classes as on-demand Instances **********************************************/

    /**
     * API Key Class.
     *
     * @return API_Manager_WPFP_Key
     */
    public function key()
    {
        return API_Manager_WPFP_Key::instance();
    }

    /**
     * Update Check Class.
     *
     * @return API_Manager_WPFP_Update_API_Check
     */
    public function update_check($upgrade_url, $plugin_name, $product_id, $api_key, $activation_email, $renew_license_url, $instance, $domain, $software_version, $plugin_or_theme, $text_domain, $extra = '')
    {

        return API_Manager_WPFP_Update_API_Check::instance($upgrade_url, $plugin_name, $product_id, $api_key, $activation_email, $renew_license_url, $instance, $domain, $software_version, $plugin_or_theme, $text_domain, $extra);
    }

    public function plugin_url()
    {
        if (isset($this->plugin_url)) {
            return $this->plugin_url;
        }

        return $this->plugin_url = plugins_url('/', __FILE__);
    }

    /**
     * Generate the default data arrays
     */
    public function activation()
    {
        # Schedule Update Interval
        update_option('wf_schedule', '3600');
        # Schedule Cron
        wp_schedule_event(time(), 'woo_feed_corn', 'woo_feed_update');

        # Configure Api
        global $wpdb;

        $global_options = array(
            $this->ame_api_key => '',
            $this->ame_activation_email => '',
        );

        update_option($this->ame_data_key, $global_options);

        $single_options = array(
            $this->ame_product_id_key => $this->ame_software_product_id,
            $this->ame_instance_key => wp_generate_password(12, false),
            $this->ame_deactivate_checkbox_key => 'on',
            $this->ame_activated_key => 'Deactivated',
        );

        foreach ($single_options as $key => $value) {
            update_option($key, $value);
        }

        $curr_ver = get_option($this->api_manager_wpfp_version_name);

        // checks if the current plugin version is lower than the version being installed
        if (version_compare($this->version, $curr_ver, '>')) {
            // update the version
            update_option($this->api_manager_wpfp_version_name, $this->version);
        }
    }

    /**
     * Deletes all data if plugin deactivated
     * @return void
     */
    public function uninstall()
    {
        global $wpdb, $blog_id;

        $this->license_key_deactivation();

        // Remove options
        if (is_multisite()) {

            switch_to_blog($blog_id);

            foreach (array(
                         $this->ame_data_key,
                         $this->ame_product_id_key,
                         $this->ame_instance_key,
                         $this->ame_deactivate_checkbox_key,
                         $this->ame_activated_key,
                     ) as $option) {

                delete_option($option);

            }

            restore_current_blog();

        } else {

            foreach (array(
                         $this->ame_data_key,
                         $this->ame_product_id_key,
                         $this->ame_instance_key,
                         $this->ame_deactivate_checkbox_key,
                         $this->ame_activated_key
                     ) as $option) {

                delete_option($option);

            }

        }

    }

    /**
     * Deactivates the license on the API server
     * @return void
     */
    public function license_key_deactivation()
    {

        $activation_status = get_option($this->ame_activated_key);

        $api_email = $this->ame_options[$this->ame_activation_email];
        $api_key = $this->ame_options[$this->ame_api_key];

        $args = array(
            'email' => $api_email,
            'licence_key' => $api_key,
        );

        if ($activation_status == 'Activated' && $api_key != '' && $api_email != '') {
            $this->key()->deactivate($args); // reset license key activation
        }
    }

    /**
     * Displays an inactive notice when the software is inactive.
     */
    public static function am_inactive_notice()
    {
        ?>
        <?php if (!current_user_can('manage_options')) return; ?>
        <?php if (isset($_GET['page']) && 'api_manager_wpfp_dashboard' == $_GET['page']) return; ?>
        <div id="message" class="notice notice-success">
            <p><?php printf(__('You are using the free version of <b>WooCommerce Product Feed Pro</b>. To explore premium features buy a license from <a href="' . esc_url("https://webappick.com") . '">webappick.com</a> or %sClick here%s to activate the license key.', 'api-manager-example'), '<a href="' . esc_url(admin_url('options-general.php?page=api_manager_wpfp_dashboard')) . '">', '</a>'); ?></p>
        </div>
    <?php
    }


    // Returns the API License Key status from the WooCommerce API Manager on the server
    public function license_key_status()
    {
        $activation_status = get_option(WPFP()->ame_activated_key);

        $args = array(
            'email' => WPFP()->ame_options[WPFP()->ame_activation_email],
            'licence_key' => WPFP()->ame_options[WPFP()->ame_api_key],
        );

        return json_decode(WPFP()->key()->status($args), true);
    }

    /**
     * Displays an inactive notice when the software is inactive.
     */
    public function is_pro()
    {
        $license_status = $this->license_key_status();
        $license_status_check = (!empty($license_status['status_check']) && $license_status['status_check'] == 'active') ? 'Activated' : 'Deactivated';
        if (!empty($license_status_check) && $license_status_check == "Activated") {
            return true;
        } elseif (!empty($license_status['status_check']) && $license_status['status_check'] == 'inactive') {
            update_option("wpfp_activated", "Deactivated");
            return false;
        }
        return false;
    }

    public function wpfp_help_section()
    {
        ?>
        <table class="widefat fixed">
            <tbody>
            <tr>
                <td align="center"><b><a target="_blank"
                                         href="http://webappick.helpscoutdocs.com/article/17-woocommerce-product-feed">Help
                            & Docs</a></b></td>
                <td align="center"><b><a style="color:#ee264a;" target="_blank"
                                         href="https://www.youtube.com/channel/UCzy3G9pA3yVgo0YZc-yJmfw">VIDEOS</a></b>
                </td>
                <td>Contact <b style="color:#2CC185;">support@webappick.com</b> for support.</td>
            </tr>
            </tbody>
        </table>
        <br/>
    <?php
    }

    /**
     * Check for external blocking contstant
     * @return string
     */
    public function check_external_blocking()
    {
        // show notice if external requests are blocked through the WP_HTTP_BLOCK_EXTERNAL constant
        if (defined('WP_HTTP_BLOCK_EXTERNAL') && WP_HTTP_BLOCK_EXTERNAL === true) {

            // check if our API endpoint is in the allowed hosts
            $host = parse_url($this->upgrade_url, PHP_URL_HOST);

            if (!defined('WP_ACCESSIBLE_HOSTS') || stristr(WP_ACCESSIBLE_HOSTS, $host) === false) {
                ?>
                <div class="error">
                    <p><?php printf(__('<b>Warning!</b> You\'re blocking external requests which means you won\'t be able to get %s updates. Please add %s to %s.', 'api-manager-example'), $this->ame_software_product_id, '<strong>' . $host . '</strong>', '<code>WP_ACCESSIBLE_HOSTS</code>'); ?></p>
                </div>
            <?php
            }

        }
    }

} // End of class

function WPFP()
{
    return API_Manager_WPFP::instance();
}

// Initialize the class instance only once
WPFP();
