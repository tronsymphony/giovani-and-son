<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<tr valign="top" class="load-in-backed-checked">
    <th scope="row" class="titledesc"><?php _e( 'Still allow to edit prices in Phone Orders', 'advanced-dynamic-pricing-for-woocommerce' ) ?></th>
    <td class="forminp forminp-checkbox">
        <fieldset>
            <legend class="screen-reader-text">
                <span><?php _e( 'Still allow to edit prices in Phone Orders', 'advanced-dynamic-pricing-for-woocommerce' ) ?></span></legend>
            <label for="allow_to_edit_prices_in_po">
                <input <?php checked( $options['allow_to_edit_prices_in_po'] ) ?>
                        name="allow_to_edit_prices_in_po" id="allow_to_edit_prices_in_po" type="checkbox">
            </label>
        </fieldset>
    </td>
</tr>