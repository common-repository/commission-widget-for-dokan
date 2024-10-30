<?php
/*
Plugin Name: Commission Widget for Dokan
Plugin URI: http://dokanextensions.com/
Description: Display the Vendor Commission on Dokan Vendor Dashboard
Author: Dokanextensions.com
Author URI: http://dokanextensions.com
Version: 1.0
License: GNU General Public License v2.0 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

class Commission_Widget_Dokan {
	
	/**
     * Plugin version
     *
     * @var string
     */
    public $version = '1.0';
    
    /**
     * Constructor for the Commission_Widget_Dokan class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     *
     * @return void
     */
    public function __construct() {
        add_action( 'plugins_loaded', array( $this, 'check_dokan_lite_exist' ), 10 );
        add_action( 'dokan_loaded', array( $this, 'init_plugin' ), 10 );
    }
    
    /**
     * Check is dokan lite active or not
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function check_dokan_lite_exist() {
        if ( ! class_exists( 'WeDevs_Dokan' ) ) {
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            add_action( 'admin_notices', array( $this, 'activation_notice' ) );
            add_action( 'wp_ajax_commission_widget_dokan_install_dokan_lite', array( $this, 'install_dokan_lite' ) );
        }
    }
    
    /**
     * Dokan main plugin activation notice
     *
     * @since 2.5.2
     *
     * @return void
     * */
    public function activation_notice() {
        ?>
        <div class="updated" id="commission-widget-dokan-installer-notice" style="padding: 1em; position: relative;">
            <h2><?php _e( 'Your Commission Widget for Dokan is almost ready!', 'dokan-commissionwidget' ); ?></h2>

            <?php
            $plugin_file      = basename( dirname( __FILE__ ) ) . '/commissionwidgetfordokan.php';
            $core_plugin_file = 'dokan-lite/dokan.php';
            ?>
            <a href="<?php echo wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=' . $plugin_file . '&amp;plugin_status=all&amp;paged=1&amp;s=', 'deactivate-plugin_' . $plugin_file ); ?>" class="notice-dismiss" style="text-decoration: none;" title="<?php _e( 'Dismiss this notice', 'dokan-commissionwidget' ); ?>"></a>

            <?php if ( file_exists( WP_PLUGIN_DIR . '/' . $core_plugin_file ) && is_plugin_inactive( 'dokan-lite' ) ): ?>
                <p><?php echo sprintf( __( 'You just need to activate the <strong>%s</strong> to make it functional.', 'dokan-commissionwidget' ), 'Dokan (Lite) - Multi-vendor Marketplace plugin' ); ?></p>
                <p>
                    <a class="button button-primary" href="<?php echo wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $core_plugin_file . '&amp;plugin_status=all&amp;paged=1&amp;s=', 'activate-plugin_' . $core_plugin_file ); ?>"  title="<?php _e( 'Activate this plugin', 'dokan-commissionwidget' ); ?>"><?php _e( 'Activate', 'dokan-commissionwidget' ); ?></a>
                </p>
            <?php else: ?>
                <p><?php echo sprintf( __( "You just need to install the %sCore Plugin%s to make it functional.", "dokan" ), '<a target="_blank" href="https://wordpress.org/plugins/dokan-lite/">', '</a>' ); ?></p>

                <p>
                    <button id="commission-widget-dokan-installer" class="button"><?php _e( 'Install Now', 'dokan-commissionwidget' ); ?></button>
                </p>
            <?php endif ?>
        </div>

        <script type="text/javascript">
            ( function ( $ ) {
                $( '#commission-widget-dokan-installer-notice #commission-widget-dokan-installer' ).click( function ( e ) {
                    e.preventDefault();
                    $( this ).addClass( 'install-now updating-message' );
                    $( this ).text( '<?php echo esc_js( 'Installing...', 'dokan-commissionwidget' ); ?>' );

                    var data = {
                        action: 'commission_widget_dokan_install_dokan_lite',
                        _wpnonce: '<?php echo wp_create_nonce( 'commission-widget-dokan-installer-nonce' ); ?>'
                    };

                    $.post( ajaxurl, data, function ( response ) {
                        if ( response.success ) {
                            $( '#commission-widget-dokan-installer-notice #commission-widget-dokan-installer' ).attr( 'disabled', 'disabled' );
                            $( '#commission-widget-dokan-installer-notice #commission-widget-dokan-installer' ).removeClass( 'install-now updating-message' );
                            $( '#commission-widget-dokan-installer-notice #commission-widget-dokan-installer' ).text( '<?php echo esc_js( 'Installed', 'dokan-commissionwidget' ); ?>' );
                            window.location.reload();
                        }
                    } );
                } );
            } )( jQuery );
        </script>
        <?php
    }
    
    /**
     * Install dokan lite
     *
     * @since 2.5.2
     *
     * @return void
     * */
    public function install_dokan_lite() {
        if ( !isset( $_REQUEST['_wpnonce'] ) || !wp_verify_nonce( $_REQUEST['_wpnonce'], 'dokan-pro-installer-nonce' ) ) {
            wp_send_json_error( __( 'Error: Nonce verification failed', 'dokan-commissionwidget' ) );
        }

        include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

        $plugin = 'dokan-lite';
        $api    = plugins_api( 'plugin_information', array( 'slug' => $plugin, 'fields' => array( 'sections' => false ) ) );

        $upgrader = new Plugin_Upgrader( new WP_Ajax_Upgrader_Skin() );
        $result   = $upgrader->install( $api->download_link );
        activate_plugin( 'dokan-lite/dokan.php' );

        wp_send_json_success();
    }
    
    
	
	/**
	* Initializes the WeDevs_Dokan() class
	*
	* Checks for an existing WeDevs_WeDevs_Dokan() instance
    * and if it doesn't find one, creates it.
	* 
	* @since v1.0.0
	*/
	public static function init() {
		static $instance = false;
		if ( !$instance ) {
            $instance = new Commission_Widget_Dokan();
        }

        return $instance;
	}
	
	/**
	* Activate the plugin
	* 
	* @since v1.0.0
	*/
	public function activate() {
		
	}
	
	/**
     * Load all things
     *
     * @since 2.7.3
     *
     * @return void
     */
    public function init_plugin() {
        $this->defined();
        spl_autoload_register( array( $this, 'commission_widget_dokan_autoload' ) );

        //$this->includes();

        $this->load_actions();
        $this->load_filters();
    }
    
    /**
     * Required all class files inside Commission Widget Dokan
     *
     * @since 1.0
     *
     * @param  string $class
     *
     * @return void
     */
    public function commission_widget_dokan_autoload( $class ) {
        if ( stripos( $class, 'Commission_Widget_Dokan' ) !== false ) {
            $class_name = str_replace( array( 'Commission_Widget_Dokan_', '_' ), array( '', '-' ), $class );
            $file_path  = COMMISSION_WIDGET_DOKAN_CLASS . '/' . strtolower( $class_name ) . '.php';

            if ( file_exists( $file_path ) ) {
                require_once $file_path;
            }
        }
    }
    
    /**
     * Define all commission widget module constant
     *
     * @since  1.0
     *
     * @return void
     */
    public function defined() {
        define( 'COMMISSION_WIDGET_DOKAN_PLUGIN_VERSION', $this->version );
        define( 'COMMISSION_WIDGET_DOKAN_FILE', __FILE__ );
        define( 'COMMISSION_WIDGET_DOKAN_DIR', dirname( __FILE__ ) );
        //define( 'COMMISSION_WIDGET_DOKAN_INC', dirname( __FILE__ ) . '/includes' );
        //define( 'COMMISSION_WIDGET_DOKAN_ADMIN_DIR', COMMISSION_WIDGET_DOKAN_INC . '/admin' );
        define( 'COMMISSION_WIDGET_DOKAN_CLASS', dirname( __FILE__ ) . '/classes' );
    }
    
    /**
     * Load all includes file for Commission Widget Dokan
     *
     * @since 1.0
     *
     * @return void
     */
     public function includes() {

	 }
	 
	 
	 
	 /**
     * Load all necessary Actions hooks
     *
     * @since 1.0
     *
     * @return void [description]
     */
     public function load_actions() {
	 	add_action( 'init', array( $this, 'localization_setup' ) );
	 	add_action( 'init', array( $this, 'instantiate' ), 10 );
	 }
	 
	 /**
     * Instantiate all classes
     *
     * @since 1.0
     *
     * @return void
     */
     public function instantiate() {
		
		if( is_user_logged_in() && dokan_is_user_seller( get_current_user_id() ) ) {
			Commission_Widget_Dokan_Dashboard::init();
		}
			
	 }
	 
	 
	 /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
	public function localization_setup() {
        load_plugin_textdomain( 'dokan-commissionwidget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }
	 
	 /**
     * Load all Filters Hook
     *
     * @since 1.0
     *
     * @return void
     */
     public function load_filters() {
	 	add_filter( 'dokan_query_var_filter', array( $this, 'load_query_var' ), 10 );
	 	add_filter( 'dokan_set_template_path', array( $this, 'load_commission_widget_dokan_templates' ), 10, 3 );
	 }
	 
	 
	 /**
     * Load Pro rewrite query vars
     *
     * @since 1.0
     *
     * @param  array $query_vars
     *
     * @return array
     */
    public function load_query_var( $query_vars ) {
        $query_vars[] = 'commissionwidget';

        return $query_vars;
    }
    
    /**
     * Get plugin path
     *
     * @since 2.5.2
     *
     * @return void
     * */
    public function plugin_path() {
        return untrailingslashit( plugin_dir_path( __FILE__ ) );
    }
    
    /**
     * Load Commission Widget for Dokan templates
     *
     * @since 1.0
     *
     * @return void
     * */
    public function load_commission_widget_dokan_templates( $template_path, $template, $args ) {
        if ( isset( $args['commissionwidget'] ) && $args['commissionwidget'] ) {
            return $this->plugin_path() . '/templates';
        }

        return $template_path;
    }
}

/**
 * Load Commission Widget Plugin for dokan
 *
 * @since 1.0.0
 *
 * @return void
 * */
function commission_widget_dokan() {
    return Commission_Widget_Dokan::init();
}

commission_widget_dokan();

//register_activation_hook( __FILE__, array( 'Commission_Widget_Dokan', 'activate' ) );