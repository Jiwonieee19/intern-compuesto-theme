<?php

defined( 'ABSPATH' ) or exit;

// CONSTANTS
if ( ! defined( 'ASSETS_VERSION' ) ) {
	$_app_dist_dir = wp_normalize_path( get_theme_file_path( 'dist' ) );
	$_app_mtimes   = [];

	foreach ( [ '/css/*.min.css', '/js/*.min.js', '/main.min.css', '/main.min.js' ] as $_app_pattern ) {
		foreach ( (array) glob( $_app_dist_dir . $_app_pattern ) as $_app_file ) {
			$_app_mtime = @filemtime( $_app_file );

			if ( $_app_mtime ) {
				$_app_mtimes[] = $_app_mtime;
			}
		}
	}

	define( 'ASSETS_VERSION', $_app_mtimes ? md5( implode( '|', $_app_mtimes ) ) : '1.0.0' );

	unset( $_app_dist_dir, $_app_mtimes, $_app_pattern, $_app_file, $_app_mtime );
}
defined( 'APP_ALM_NO_RESULTS_TEXT' ) or define( 'APP_ALM_NO_RESULTS_TEXT', 'Sorry, nothing found in this search' );

// AUTOLOAD CORE CLASSES
try {
  spl_autoload_register( function ( $class ) {
    if ( false === strpos( $class, 'App_' ) ) {
      return;
    }

    $filename = dirname( __FILE__ ) . '/core/' . str_replace( '_', '-', strtolower( substr( $class, 4 ) ) ) . '.php';

    if ( is_readable( $filename ) ) {
      include_once( $filename );
    }
  } );
} catch ( Exception $e ) {
}

// AUTOLOAD ELEMENTOR CLASSES
try {
  spl_autoload_register( function ( $class ) {
    if ( false === strpos( $class, 'Elem_' ) ) {
      return;
    }

    if ( false !== strpos( $class, 'Elem_Tag_' ) ) {
      $tag_name = str_replace( '_', '-', strtolower( substr( $class, 9 ) ) );
      $filename = dirname( __FILE__ ) . "/modules/elementor/tags/{$tag_name}.php";
    } else {
      $widget_name = str_replace( '_', '-', strtolower( substr( $class, 5 ) ) );
      $filename    = dirname( __FILE__ ) . "/modules/elementor/widgets/{$widget_name}/{$widget_name}.php";
    }

    if ( is_readable( $filename ) ) {
      include_once( $filename );
    }
  } );
} catch ( Exception $e ) {
}

// EXTRA MODULES
require_once 'modules/elementor/elementor.php';

// CORE CLASS
final class App_Core {
  private static ?App_Core $_instance = null;

  public App_Helpers $helpers;
  public App_Setup $setup;
  public App_Admin $admin;

  public function __construct() {
    $this->helpers = new App_Helpers();
    $this->setup   = new App_Setup();
    $this->admin   = new App_Admin();
  }

  public static function instance(): ?App_Core {
    return ( is_null( self::$_instance ) ? self::$_instance = new App_Core() : self::$_instance );
  }
}

