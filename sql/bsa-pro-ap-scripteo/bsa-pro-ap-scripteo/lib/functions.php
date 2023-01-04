<?php

add_action('init', 'bsaProMakeSomething', 1);
function bsaProMakeSomething()
{
	if ( isset($_GET['r']) ) {
		$cookie_lifetime = (get_option('bsa_pro_plugin_ap_cookie_lifetime') > 0 ? get_option('bsa_pro_plugin_ap_cookie_lifetime') : 30); // default 30 days
		setcookie('bsaProAffiliate', $_GET['r'], (time() + ($cookie_lifetime * 24 * 60 *60)), '/');

		$get_url = get_option('bsa_pro_plugin_ordering_form_url');
		if ( $get_url != '' ) {
			wp_redirect( $get_url );
			exit;
		}
	}
}

add_shortcode( 'bsa_pro_affiliate_program', 'create_bsa_pro_affiliate_program' );
function create_bsa_pro_affiliate_program( $atts )
{
	$a = shortcode_atts( array(
		'id' 				=> ( isset($atts['id']) ) ? $atts['id'] : ''
	), $atts );

	ob_start();
	$get_url = get_site_url();
	if ( function_exists('bsa_get_trans') && $get_url != '' ):
		$model = new BSA_PRO_Model();
		$referrals = $model->getReferrals();
		$referral_url = (strpos($get_url, '?') !== false ? $get_url.'&r=' : $get_url.'/?r=');
		if (get_option('bsa_pro_plugin_symbol_position') == 'before') {
			$before = get_option('bsa_pro_plugin_currency_symbol');
		} else {
			$before = '';
		}
		if (get_option('bsa_pro_plugin_symbol_position') != 'before') {
			$after = get_option('bsa_pro_plugin_currency_symbol');
		} else {
			$after = '';
		}
	?>
	<div id="bsaProAffiliateWrapper">
		<div class="bsaProAffiliateBalance">
			<div class="bsaProAffiliateBalanceLeft">
				<?php echo bsa_get_trans('affiliate_program', 'commission'); ?> <strong><?php echo get_option('bsa_pro_plugin_ap_commission'); ?>%</strong><br>
				<small><?php echo bsa_get_trans('affiliate_program', 'each_sale'); ?></small>
			</div>
			<div class="bsaProAffiliateBalanceRight">
				<?php echo bsa_get_trans('affiliate_program', 'balance'); ?> <strong><?php echo $before . bsa_number_format($model->getAffiliateBalance()) . $after; ?></strong><br>
				<small><a href="<?php echo admin_url(); ?>admin.php?page=bsa-pro-sub-menu-affiliate"><?php echo bsa_get_trans('affiliate_program', 'make'); ?></a></small>
			</div>
		</div>
		<div class="bsaProAffiliateUrl">
			<h2><?php echo bsa_get_trans('affiliate_program', 'ref_link'); ?></h2>
			<div class="bsaProAffiliateUrlInner">
				<?php if ( get_current_user_id() ): ?>
				<span class="bsaProCopyURL"><?php echo $referral_url . get_current_user_id(); ?></span> <span id="bsaProCopyClipboard" title="copy to clipboard"></span>
				<?php else: ?>
				<span class="bsaProCopyURL"><?php echo bsa_get_trans('affiliate_program', 'ref_notice'); ?></span>
				<?php endif; ?>
			</div>
		</div>
		<div class="bsaProAffiliateList">
			<h4><?php echo bsa_get_trans('affiliate_program', 'ref_users'); ?></h4>
			<table id="bsaProAffiliateTable">
				<tr>
					<th class="bsaProFirst"><?php echo bsa_get_trans('affiliate_program', 'date'); ?></th>
					<th><?php echo bsa_get_trans('affiliate_program', 'buyer'); ?></th>
					<th><?php echo bsa_get_trans('affiliate_program', 'order'); ?></th>
					<th><?php echo bsa_get_trans('affiliate_program', 'comm_rate'); ?></th>
					<th class="bsaProLast"><?php echo bsa_get_trans('affiliate_program', 'your_comm'); ?></th>
				</tr>
				<?php if ( is_array($referrals) && count($referrals) > 0 ): ?>
					<?php foreach ( $referrals as $entry ): ?>
						<?php $buyer = explode('@', $entry['buyer']); ?>
						<tr>
							<td class="bsaProFirst"><?php echo date('Y/m/d', $entry['action_time']); ?></td>
							<td><?php echo ($buyer[0] ? $buyer[0] : '-'); ?></td>
							<td><?php echo $entry['order_amount']; ?></td>
							<td><?php echo $entry['commission_rate']; ?>%</td>
							<td class="bsaProLast"><?php echo $before . $entry['commission'] . $after; ?></td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
				<tr>
					<td colspan="5"><?php echo bsa_get_trans('affiliate_program', 'empty'); ?></td>
				</tr>
				<?php endif; ?>
			</table>
		</div>
	</div>
	<?php echo "<style>";
		$opt = 'ap_custom';
		if ( bsa_get_opt($opt, 'general_bg') != '' || bsa_get_opt($opt, 'general_color') != '' )
			echo '#bsaProAffiliateWrapper { background-color: '.bsa_get_opt($opt, "general_bg").'; color: '.bsa_get_opt($opt, "general_color").'; }';
		if ( bsa_get_opt($opt, 'commission_bg') != '' || bsa_get_opt($opt, 'commission_color') != '' )
			echo '.bsaProAffiliateBalanceLeft { background-color: '.bsa_get_opt($opt, "commission_bg").'; color: '.bsa_get_opt($opt, "commission_color").'; }';
		if ( bsa_get_opt($opt, 'balance_bg') != '' || bsa_get_opt($opt, 'balance_color') != '' )
			echo '.bsaProAffiliateBalanceRight { background-color: '.bsa_get_opt($opt, "balance_bg").'; color: '.bsa_get_opt($opt, "balance_color").'; }';
		if ( bsa_get_opt($opt, 'link_color') != '' )
			echo '#bsaProAffiliateWrapper .bsaProAffiliateBalanceRight a, #bsaProAffiliateWrapper .bsaProAffiliateBalanceRight a:hover, #bsaProAffiliateWrapper .bsaProAffiliateBalanceRight a:active,
#bsaProAffiliateWrapper .bsaProAffiliateBalanceRight a:focus, #bsaProAffiliateWrapper .bsaProAffiliateBalanceRight a:visited { color: '.bsa_get_opt($opt, "link_color").'; }';
		if ( bsa_get_opt($opt, 'ref_bg') != '' || bsa_get_opt($opt, 'ref_color') != '' )
			echo '.bsaProAffiliateUrlInner { background-color: '.bsa_get_opt($opt, "ref_bg").'; color: '.bsa_get_opt($opt, "ref_color").'; }';
		if ( bsa_get_opt($opt, 'table_bg') != '' || bsa_get_opt($opt, 'table_color') != '' )
			echo '#bsaProAffiliateTable, #bsaProAffiliateTable th { background-color: '.bsa_get_opt($opt, "table_bg").'; color: '.bsa_get_opt($opt, "table_color").'; }';
	echo "</style>"; ?>
	<script>
		(function ($) {
			var el = document.getElementById('bsaProCopyClipboard');
			if(el){
				el.addEventListener( 'click', function( event ) { copyText($('.bsaProCopyURL').text()) } );
			}
			function copyText(text) {
				var textField = document.createElement('textarea');
				textField.innerText = text;
				document.body.appendChild(textField);
				textField.select();
				document.execCommand('copy');
				$(textField).remove();
			}
		})(jQuery);
	</script>
	<?php else:
		echo 'This Add-on requires <strong>Ads Pro 2.9.0</strong> or higher version. Download it <strong><a href="http://tinyurl.com/Ads-Pro-WordPress">here</a></strong>.';
	endif;
	return ob_get_clean();
}
