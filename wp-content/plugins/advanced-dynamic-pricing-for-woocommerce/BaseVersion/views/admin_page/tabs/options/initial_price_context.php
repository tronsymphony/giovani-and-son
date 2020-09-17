<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<tr valign="top">
    <th scope="row" class="titledesc">
        <?php _e('Use prices modified by other plugins', 'advanced-dynamic-pricing-for-woocommerce') ?>
        <div style="font-weight: normal; font-style: italic; margin: 10px 0">
            <label><?php _e( '( for example, changed by Currency Switcher )', 'advanced-dynamic-pricing-for-woocommerce' ); ?> </label>
        </div>
    </th>
    <td class="forminp forminp-checkbox">
        <fieldset>
            <legend class="screen-reader-text"><span><?php _e('Use prices modified by other plugins', 'advanced-dynamic-pricing-for-woocommerce') ?></span></legend>
            <label for="initial_price_context">
                <input <?php checked( 'view', $options['initial_price_context'] ); ?> value="view" name="initial_price_context" id="initial_price_context" type="checkbox">
            </label>
        </fieldset>
    </td>
</tr>
