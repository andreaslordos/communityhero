<?php

/**
 * Prepare price to valid format.
 *
 * @param $price
 * @param $disable_prepare_price
 * @param $prepare_price_to_woo_format
 * @param $convert_decimal_separator
 *
 * @return float|int|string
 *
 * @deprecated since @3.0.0
 */
function pmwi_prepare_price($price, $disable_prepare_price, $prepare_price_to_woo_format, $convert_decimal_separator) {
    return XmlImportWooCommerceService::getInstance()
        ->getPriceService()
        ->preparePrice($price, $disable_prepare_price, $prepare_price_to_woo_format, $convert_decimal_separator);
}