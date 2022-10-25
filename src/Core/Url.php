<?php namespace DP\Std\Core;

class Url {

	public static function get_query_param( $key, $def = null ) {
		return key_exists( $key, $_GET ) ? htmlspecialchars( $_GET[ $key ] ) : $def;
	}

}

?>
