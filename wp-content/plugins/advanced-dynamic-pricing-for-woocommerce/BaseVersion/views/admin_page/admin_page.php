<?php

use ADP\BaseVersion\Includes\External\AdminPage\AdminPage;
use ADP\BaseVersion\Includes\External\AdminPage\Interfaces\AdminTabInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * @var $this AdminPage
 * @var $tabs AdminTabInterface[]
 * @var $current_tab AdminTabInterface
 */
?>

<div class="wrap woocommerce">

    <h2 class="wcp_tabs_container nav-tab-wrapper">
		<?php foreach ( $tabs as $tab_key => $tab_handler ): ?>
            <a class="nav-tab <?php echo( $tab_key === $current_tab::get_key() ? 'nav-tab-active' : '' ); ?>"
               href="admin.php?page=wdp_settings&tab=<?php echo $tab_key; ?>"><?php echo $tab_handler::get_title(); ?></a>
		<?php endforeach; ?>
    </h2>

    <div class="wdp_settings ui-page-theme-a">
        <div class="wdp_settings_container">
			<?php
			$this->render_current_tab();
			?>
        </div>
    </div>

</div>