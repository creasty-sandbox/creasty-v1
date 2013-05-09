<?php

/*=== Syntax Highlighter
==============================================================================================*/
add_shortcode( 'code', 'sh_code_func' );
function sh_code_func( $attr, $content = '' ){
	$param = array();
	$force_linenum = false;

	if( !empty( $attr ) ){
		$param[] = ' class="syntax"';

		foreach( $attr as $key => $val ){
			if( 'lang' == $key ){
				$val = esc_attr( $val );
				$param[] = "data-language=\"$val\"";
				continue;
			}

			if( 'highlight' == $key ){
				$val = esc_attr( $val );
				$param[] = "data-highlight=\"$val\"";
				continue;
			}

			if( 'start' == $key ){
				$val = esc_attr( $val );
				$param[] = "data-start=\"$val\"";
				$force_linenum = true;
				continue;
			}
		}
	}

	$content = esc_html( $content );
	$content = trim( $content, "\n\r" );
	$content = preg_replace( '|^\s+$|m', '', $content );

	$html = '<code' . implode( ' ', $param ) . '>' . $content . '</code>';

	if( strpos( $content, "\n" ) || $force_linenum )
		$html = '<pre>' . $html . '</pre>';

	return $html;
}


/*=== Tabnav
==============================================================================================*/
add_shortcode( 'tabnav', 'sh_tabnav_func' );
function sh_tabnav_func( $atts, $content = '' ){
	$content = do_shortcode( $content );

	$atts[ 'class' ] = trim( 'dyn-tabnav tabnav-zen ' . $atts[ 'class' ] );

	$attr = array2attr( $atts );

	$content = "<div$attr>\n$content\n</div>";

	return $content;
}

?>