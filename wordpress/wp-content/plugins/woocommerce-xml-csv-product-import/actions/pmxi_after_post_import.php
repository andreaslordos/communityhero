<?php

/**
 * @param $import_id
 */
function pmwi_pmxi_after_post_import($import_id) {
    remove_all_actions('shutdown');
}