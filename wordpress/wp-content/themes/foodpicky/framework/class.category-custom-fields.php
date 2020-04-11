<?php
// This file is not called from WordPress. We don't like that.
!defined('ABSPATH') and exit;

class AZEXO_Category_Custom_Fields {

    public function __construct() {

        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('admin_print_styles', array($this, 'admin_print_styles'), 10);
        // Add form
        add_action('category_add_form_fields', array($this, 'add_category_fields'));
        add_action('category_edit_form_fields', array($this, 'edit_category_fields'), 10);
        add_action('created_category', array($this, 'save_category_fields'));
        add_action('edited_category', array($this, 'save_category_fields'));
    }

    function admin_enqueue_scripts() {
        global $hook_suffix, $pagenow, $current_screen;

        if (!isset($current_screen) || !is_object($current_screen))
            return;

        if ('edit-tags.php' !== $pagenow && 'edit-category' !== $current_screen->id)
            return;

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }

    function admin_print_styles() {
        global $hook_suffix, $pagenow, $current_screen;

        if (!isset($current_screen) || !is_object($current_screen))
            return;

        if ('edit-tags.php' !== $pagenow && 'edit-category' !== $current_screen->id)
            return;

        $output = '<style type="text/css">
		.wp-picker-container .button{
			width: auto;
		}
		</style>';
        $output = str_replace(array("\r", "\n", "\t"), "", $output);

        print $output . "\n";
    }


    public function add_category_fields() {
        ?>

        <div class="form-field">
            <label for="display_type"><?php esc_html_e('Accent color', 'foodpicky'); ?></label>
            <input data-std="" id="category_color" class="yt-color-picker" name="category_meta[color]" type="text" value="" />
        </div>

        <?php
    }

    public function edit_category_fields($term) {
        $t_id = $term->term_id;
        $cat_meta = get_option("category_$t_id");
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label><?php esc_html_e('Accent color', 'foodpicky'); ?></label></th>
            <td>
                <input data-std="" id="category_color" class="yt-color-picker" name="category_meta[color]" type="text" value="<?php echo isset($cat_meta['color']) ? esc_attr($cat_meta['color']) : ''; ?>" size="7" />
            </td>
        </tr>

        <?php
    }

    public function save_category_fields($term_id) {

        if (isset($_POST['category_meta'])) {
            $cat_meta = get_option("category_$term_id") ? get_option("category_$term_id") : array();

            $cat_keys = array_keys($_POST['category_meta']);
            foreach ($cat_keys as $key) {
                if (isset($_POST['category_meta'][$key])) {
                    $cat_meta[$key] = $_POST['category_meta'][$key];
                }
            }
            //save the option array
            update_option("category_$term_id", $cat_meta);
        }
    }

    public function get_category_meta($term_id, $key) {
        if (!$term_id)
            return;

        $cat_meta = get_option("category_$term_id");

        return isset($key) ? $cat_meta[$key] : '';
    }

}

$GLOBALS['azexo_category_fields'] = new AZEXO_Category_Custom_Fields();
