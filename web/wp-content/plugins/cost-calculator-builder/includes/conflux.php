<?php
add_action('admin_footer', 'stm_ccb_render_feature_request');

function stm_ccb_render_feature_request() {
	echo '<a id="ccb-feature-request" href="https://stylemixthemes.cnflx.io/boards/cost-calculator-builder" target="_blank" style="display: none;">
		<img src="' . esc_url(CALC_URL . "/frontend/assets/conflux/feature-request.svg") . '">
		<span>Create a roadmap with us:<br>Vote for next feature</span>
	</a>';
}