<?php

namespace wpai_woocommerce_add_on\libraries\parser;

use wpai_woocommerce_add_on\libraries\helpers\ParserOptions;

/**
 *
 * Creates parser for particular entity type
 *
 * Created by PhpStorm.
 * User: cmd
 * Date: 11/14/17
 * Time: 11:37 AM
 */
class ParserFactory {

    /**
     * @param $type
     * @param $options
     * @return Parser|bool
     */
    public static function generate($type, $options) {
        // Init parser options.
        $parserOptions = new ParserOptions($options);
        // Define parser class.
        $parser = FALSE;
        switch ($type){
            case 'orders':
                $parser = new OrdersParser($parserOptions);
                break;
            case 'products':
                $parser = new ProductsParser($parserOptions);
                break;
        }
        return $parser;
    }
}