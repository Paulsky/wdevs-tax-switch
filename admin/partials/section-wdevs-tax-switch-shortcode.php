<?php
/**
 * Shortcode section settings page
 *
 * @package     Wdevs_Tax_Switch
 * @since       1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap woocommerce">
	<table class="form-table">
		<tbody>
		<tr>
			<th scope="row" class="titledesc">
				<label for="wdevs-tax-switch-shortcode"><?php esc_html_e( 'Tax switch shortcode', 'tax-switch-for-woocommerce' ); ?></label>
			</th>
			<td class="forminp forminp-textarea">
                <textarea name="wdevs-tax-switch-shortcode"
						  id="wdevs-tax-switch-shortcode"
						  rows="5"
						  readonly
						  style="width: 100%; font-family: monospace; max-width: 600px;"><?php echo esc_textarea( '[wdevs_tax_switch]' ); ?></textarea>
				<div style="margin: .35em 0 .5em;">
					<button id="wdevs-generate-shortcode" class="button button-primary">
						<?php esc_html_e( 'Configure shortcode', 'tax-switch-for-woocommerce' ); ?>
					</button>
					<button id="wdevs-copy-shortcode-btn" class="button">
						<?php esc_html_e( 'Copy shortcode', 'tax-switch-for-woocommerce' ); ?>
					</button>
				</div>
				<p class="description"><?php esc_html_e( 'Copy the generated shortcode and paste it where you want the tax switch to appear.', 'tax-switch-for-woocommerce' ); ?></p>
			</td>
		</tr>
		</tbody>
	</table>
</div>
<script type="text/template" id="tmpl-wdevs-tax-switch-modal">
	<div class="wc-backbone-modal wc-shipping-class-modal">
		<div class="wc-backbone-modal-content">
			<section class="wc-backbone-modal-main" role="main">
				<header class="wc-backbone-modal-header">
					<div>
						<h1><?php esc_html_e( 'Configure shortcode', 'tax-switch-for-woocommerce' ); ?></h1>
						<button class="modal-close modal-close-link dashicons dashicons-no-alt">
							<span class="screen-reader-text"><?php esc_html_e( 'Close modal', 'tax-switch-for-woocommerce' ); ?></span>
						</button>
					</div>
				</header>
				<article>
					<form action="" method="post">
						<div class="wc-shipping-class-modal-input">
							<div class="view">
								<?php esc_html_e( 'Switch type', 'tax-switch-for-woocommerce' ); ?>
							</div>
							<div class="edit">
								<select name="switch-type" data-attribute="switch-type" style="width:100%;">
									<option value="switch"><?php esc_html_e( 'Toggle switch', 'tax-switch-for-woocommerce' ); ?></option>
									<option value="buttons"><?php esc_html_e( 'Buttons', 'tax-switch-for-woocommerce' ); ?></option>
								</select>
							</div>
						</div>

						<div class="wc-shipping-class-modal-input">
							<div class="view">
								<?php esc_html_e( 'Switch style', 'tax-switch-for-woocommerce' ); ?>
							</div>
							<div class="edit">
								<select style="width:100%;" id="wdevs-style-selector">
									<option value="is-style-default"><?php esc_html_e( 'Default style', 'tax-switch-for-woocommerce' ); ?></option>
									<option value="is-style-inline"><?php esc_html_e( 'Inline style', 'tax-switch-for-woocommerce' ); ?></option>
									<option value="is-style-flat-pill"><?php esc_html_e( 'Flat pill', 'tax-switch-for-woocommerce' ); ?></option>
								</select>
							</div>
						</div>

						<div class="wc-shipping-class-modal-input">
							<div class="view">
								<label><?php esc_html_e( 'Switch color', 'tax-switch-for-woocommerce' ); ?></label>
							</div>
							<div class="edit">
								<input type="text" name="switch-color" class="color-picker" value="#333333"
									   data-default-color="#333333">
								<div class="wc-shipping-class-modal-help-text"><?php esc_html_e( 'Default', 'tax-switch-for-woocommerce' ); ?>: #333333</div>
							</div>
						</div>

						<div class="wc-shipping-class-modal-input">
							<div class="view">
								<label><?php esc_html_e( 'Switch color checked', 'tax-switch-for-woocommerce' ); ?></label>
							</div>
							<div class="edit">
								<input type="text" name="switch-color-checked" class="color-picker" value="#ffffff"
									   data-default-color="#ffffff">
								<div class="wc-shipping-class-modal-help-text"><?php esc_html_e( 'Default', 'tax-switch-for-woocommerce' ); ?>: #ffffff</div>
							</div>
						</div>

						<div class="wc-shipping-class-modal-input">
							<div class="view">
								<label><?php esc_html_e( 'Background color', 'tax-switch-for-woocommerce' ); ?></label>
							</div>
							<div class="edit">
								<input type="text" name="switch-background-color" class="color-picker" value="#e9e9ea"
									   data-default-color="#e9e9ea">
								<div class="wc-shipping-class-modal-help-text"><?php esc_html_e( 'Default', 'tax-switch-for-woocommerce' ); ?>: #e9e9ea</div>
							</div>
						</div>

						<div class="wc-shipping-class-modal-input">
							<div class="view">
								<label><?php esc_html_e( 'Background color checked', 'tax-switch-for-woocommerce' ); ?></label>
							</div>
							<div class="edit">
								<input type="text" name="switch-background-color-checked" class="color-picker" value="#34c759"
									   data-default-color="#34c759">
								<div class="wc-shipping-class-modal-help-text"><?php esc_html_e( 'Default', 'tax-switch-for-woocommerce' ); ?>: #34c759</div>
							</div>
						</div>

						<div class="wc-shipping-class-modal-input">
							<div class="view">
								<label><?php esc_html_e( 'Text color', 'tax-switch-for-woocommerce' ); ?></label>
							</div>
							<div class="edit">
								<input type="text" name="switch-text-color" class="color-picker" value="#333333"
									   data-default-color="#333333">
								<div class="wc-shipping-class-modal-help-text"><?php esc_html_e( 'Default', 'tax-switch-for-woocommerce' ); ?>: #333333</div>
							</div>
						</div>

						<div class="wc-shipping-class-modal-input">
							<div class="view">
								<?php esc_html_e( 'Including tax label', 'tax-switch-for-woocommerce' ); ?>
							</div>
							<div class="edit">
								<input type="text" name="switch-label-incl" data-attribute="switch-label-incl"
									   placeholder="<?php esc_attr_e( 'Incl. VAT', 'tax-switch-for-woocommerce' ); ?>">
							</div>
						</div>

						<div class="wc-shipping-class-modal-input">
							<div class="view">
								<?php esc_html_e( 'Excluding tax label', 'tax-switch-for-woocommerce' ); ?>
							</div>
							<div class="edit">
								<input type="text" name="switch-label-excl" data-attribute="switch-label-excl"
									   placeholder="<?php esc_attr_e( 'Excl. VAT', 'tax-switch-for-woocommerce' ); ?>">
							</div>
						</div>

						<div class="wc-shipping-class-modal-input">
							<div class="view">
								<?php esc_html_e( 'CSS classes', 'tax-switch-for-woocommerce' ); ?>
							</div>
							<div class="edit">
								<input type="text" name="class-name" data-attribute="class-name"
									   value="is-style-default">
								<div class="wc-shipping-class-modal-help-text"><?php esc_html_e( 'Options: is-style-default, is-style-inline, is-style-flat-pill or/and custom classes', 'tax-switch-for-woocommerce' ); ?></div>
							</div>
						</div>

					</form>
				</article>
				<footer>
					<div class="inner" style="flex-direction: row;">
						<div style="text-align: left;">
							<div class="wc-shipping-class-modal-input" style="padding-bottom: 0; font-size: 14px !important;">
								<div class="view">
									<?php esc_html_e( 'Preview', 'tax-switch-for-woocommerce' ); ?>
								</div>
								<div class="edit">
									<div id="preview" style="min-height: 35px;"><div class="spinner is-active" style="float:left;"></div></div>
									<div class="wc-shipping-class-modal-help-text"><?php esc_html_e( 'This is a preview. The actual style depends on your theme.', 'tax-switch-for-woocommerce' ); ?></div>
								</div>
							</div>
						</div>
						<div style="margin-top: auto;">
							<button id="btn-ok" class="button button-primary button-large">
								<?php esc_html_e( 'Generate shortcode', 'tax-switch-for-woocommerce' ); ?>
							</button>
						</div>
					</div>
				</footer>
			</section>
		</div>
	</div>
	<div class="wc-backbone-modal-backdrop modal-close"></div>
</script>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		document.getElementById('wdevs-copy-shortcode-btn').addEventListener('click', function(e) {
			e.preventDefault();
			const copyText = document.getElementById('wdevs-tax-switch-shortcode');
			copyText.select();
			document.execCommand('copy');

			const originalText = copyText.value;
			copyText.value = '<?php _e("Copied", "tax-switch-for-woocommerce"); ?>!';
			setTimeout(() => copyText.value = originalText, 1500);
		});
	});
</script>
