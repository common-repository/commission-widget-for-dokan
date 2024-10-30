<?php
/**
 *
 *  Dokan Dahsboard Commission Widget Template
 *  
 *
 *  @package dokan_enhance
 */
?>
<div class="dashboard-widget commission">
	<div class="widget-title"><i class="fa fa-euro"></i> <?php _e( 'Commission', 'dokan-commissionwidget' ); ?></div>
	<?php if( !empty( $vendor_commission ) ) : ?>
	<p style="padding: 10px; "><?php  echo __('Your current commission rate is <b>'. $vendor_commission .'</b> for each order '. get_bloginfo() . ' send to you.', 'dokan-commissionwidget');?></p>
	<?php else : ?>
	<p style="padding: 10px; "> <?php echo __('No Commission configured yet!', 'dokan-commission-widget'); ?></p>
	<?php endif; ?>
</div>