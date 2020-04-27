<?php

/**
 * Adjust price and prepare it to valid format.
 *
 * @param $price
 * @param $field
 * @param $options
 *
 * @return float|int|string
 *
 * @deprecated since @3.0.0
 */
function pmwi_adjust_price($price, $field, $options) {
    return XmlImportWooCommerceService::getInstance()
        ->getPriceService()
        ->adjustPrice($price, $field, $options);
}