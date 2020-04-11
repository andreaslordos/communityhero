<form method="post">
    <div class="horizontal-list-2 p">
        <div>
            <label style="display:inline;" for="from"><?php esc_html_e('From:', 'wc-vendors'); ?></label>
            <input class="date-pick" type="date" name="start_date" id="from"
                   value="<?php echo esc_attr(date('Y-m-d', $start_date)); ?>"/>
        </div>
        <div>
            <label style="display:inline;" for="to"><?php esc_html_e('To:', 'wc-vendors'); ?></label>
            <input type="date" class="date-pick" name="end_date" id="to"
                   value="<?php echo esc_attr(date('Y-m-d', $end_date)); ?>"/>
        </div>
    </div>
    <p>
        <input type="submit" class="btn btn-inverse btn-small" style="float:none;"
               value="<?php esc_html_e('Show', 'wc-vendors'); ?>"/>
    </p>
</form>