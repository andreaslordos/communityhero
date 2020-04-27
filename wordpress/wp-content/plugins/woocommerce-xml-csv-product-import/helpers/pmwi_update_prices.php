<?php
function pmwi_update_prices($pid){
	update_post_meta( $pid, '_regular_price', $tmp = get_post_meta( $pid, '_regular_price_tmp', true) );
	delete_post_meta( $pid, '_regular_price_tmp' );

	update_post_meta( $pid, '_sale_price', $tmp = get_post_meta( $pid, '_sale_price_tmp', true ) );
	delete_post_meta( $pid, '_sale_price_tmp' );

	update_post_meta( $pid, 'pmxi_wholesale_price', $tmp = get_post_meta( $pid, 'pmxi_wholesale_price_tmp', true ) );
	delete_post_meta( $pid, 'pmxi_wholesale_price_tmp' );

	update_post_meta( $pid, '_sale_price_dates_from', $tmp = get_post_meta( $pid, '_sale_price_dates_from_tmp', true ) );
	delete_post_meta( $pid, '_sale_price_dates_from_tmp' );

	update_post_meta( $pid, '_sale_price_dates_to', $tmp = get_post_meta( $pid, '_sale_price_dates_to_tmp', true ) );
	delete_post_meta( $pid, '_sale_price_dates_to_tmp' );

	update_post_meta( $pid, '_price', $tmp = get_post_meta( $pid, '_price_tmp', true ) );			
	delete_post_meta( $pid, '_price_tmp' );			
}