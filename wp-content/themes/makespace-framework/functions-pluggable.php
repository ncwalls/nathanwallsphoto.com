<?php

if( !function_exists( 'get_google_map_data' ) ){
	function get_google_map_data(){
		$map = array();
		if( have_rows( 'contact_information', 'option' ) ){
			while( have_rows( 'contact_information', 'option' ) ){
				the_row();
				$map_details = get_sub_field( 'map' );
				if( isset( $map_details[ 'address' ] ) && strlen( $map_details[ 'address' ] ) ){
					$address_pieces = explode( ',', $map_details[ 'address' ] );
					$address_html = '';
					if( count( $address_pieces ) ){
						$address_html = '<span class="address-details" id="' . hash( 'adler32', $map_details[ 'address' ] ) . '">';
						for( $i = 0; $i < count( $address_pieces ); $i++ ){
							$address_html .= '<span class="address-piece-' . $i . '">' . trim( $address_pieces[ $i ] ) . '</span>';
						}
						$address_html .= '<span class="address-piece-link"><a href="http://maps.google.com?q=' . $map_details[ 'lat' ] . ',' . $map_details[ 'lng' ] . '" target="_blank">Get directions</a></span>';
						$address_html .= '</span>';
					}
					$map[] = array(
						'address' => $address_html,
						'marker' => get_sub_field( 'map_marker' ) ? get_sub_field( 'map_marker' ) : '',
						'lat' => isset( $map_details[ 'lat' ] ) ? $map_details[ 'lat' ] : 0,
						'lng' => isset( $map_details[ 'lng' ] ) ? $map_details[ 'lng' ] : 0
					);
				}
			}
		}
		return json_encode( $map );
	}
}