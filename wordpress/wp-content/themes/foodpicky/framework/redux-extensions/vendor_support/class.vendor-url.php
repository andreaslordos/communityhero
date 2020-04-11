<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Redux_VendorURL' ) ) {
    class Redux_VendorURL {
        static public $url;
        static public $dir;

        public static function get_url( $handle ) {
            $min    = Redux_Functions::isMin();

            if ( $handle == 'ace-editor-js' && file_exists( self::$dir . 'vendor/ace_editor/ace.js' ) ) {
                return self::$url . 'vendor/ace_editor/ace.js';
            } elseif ( $handle == 'select2-js' && file_exists( self::$dir . 'vendor/select2/select2' . $min . '.js' ) ) {
                return self::$url . 'vendor/select2/select2' . $min . '.js';
            } elseif ( $handle == 'select2-css' && file_exists( self::$dir . 'vendor/select2/select2.css' ) ) {
                return self::$url . 'vendor/select2/select2.css';
            }
        }
    }
}