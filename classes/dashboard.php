<?php
/**
* Commission Widget Fokan Dashboard Template Class
* 
* A template for frontend vendor dashboard rendering items
* Author: Lucy Productionz 
*/
class Commission_Widget_Dokan_Dashboard extends Dokan_Template_Dashboard {
	/**
     * Constructor for the Dokan_Enhance_Dashboard class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     *
     * @uses add_action()
     *
     */
     public function __construct() {
	 	$this->user_id        = dokan_get_current_user_id();
	 	add_action( 'dokan_dashboard_left_widgets', array( $this, 'get_commission_widget' ), 99 );
	 }
	 
	 /**
     * Singleton object
     *
     * @staticvar boolean $instance
     *
     * @return \self
     */
    public static function init() {

        static $instance = false;

        if ( !$instance ) {
            $instance = new Commission_Widget_Dokan_Dashboard();
        }

        return $instance;
    }
    
    /**
	* Get commission widget
	*/
	public function get_commission_widget() {
		if ( !current_user_can( 'dokan_view_overview_menu' ) ) {
            return;
        }
        
        $commission = '';
        
        $commission_value   = dokan_get_seller_percentage( $this->user_id );
        $commission_type    = dokan_get_commission_type( $this->user_id );
        
	
        if( 'percentage' === $commission_type ){
            $commission = (100 - $commission_value).'%';
        } else 
        {
        	if(get_woocommerce_currency() === "EUR") {
				$commission_value = number_format($commission_value,2,",",".");
        		$commission_value = $commission_value . ',';
			}
            
            $commission = get_woocommerce_currency_symbol().''.$commission_value;
        }
        
        dokan_get_template_part( 'dashboard/commission-widget', '', array(
            'commissionwidget'            => true,
            'vendor_commission'		=> $commission,
        )
        );
	}
	
}