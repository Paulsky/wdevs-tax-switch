/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { __ } from '@wordpress/i18n';

import {
	InspectorControls,
	PanelColorSettings,
	useBlockProps,
} from '@wordpress/block-editor';

import { TextControl, PanelBody, SelectControl } from '@wordpress/components';

import SwitchComponent from './components/SwitchComponent';
/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	const {
		switchType,
		switchColor,
		switchColorChecked,
		switchBackgroundColor,
		switchBackgroundColorChecked,
		switchTextColor,
		switchLabelIncl,
		switchLabelExcl,
		switchAriaLabel,
	} = attributes;

	const { originalTaxDisplay } = window.wtsEditorObject || {
		originalTaxDisplay: 'incl',
	};

	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<PanelBody
					title={ __(
						'Display options',
						'tax-switch-for-woocommerce'
					) }
					initialOpen={ true }
				>
					<SelectControl
						label={ __(
							'Switch type',
							'tax-switch-for-woocommerce'
						) }
						value={ switchType }
						options={ [
							{
								label: __(
									'Toggle switch',
									'tax-switch-for-woocommerce'
								),
								value: 'switch',
							},
							{
								label: __(
									'Buttons',
									'tax-switch-for-woocommerce'
								),
								value: 'buttons',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { switchType: value } )
						}
					/>
				</PanelBody>
				<PanelColorSettings
					title={ __(
						'Switch colors',
						'tax-switch-for-woocommerce'
					) }
					initialOpen={ true }
					colorSettings={ [
						{
							value: switchColor,
							onChange: ( color ) =>
								setAttributes( { switchColor: color } ),
							label: __(
								'Switch color',
								'tax-switch-for-woocommerce'
							),
						},
						{
							value: switchColorChecked,
							onChange: ( color ) =>
								setAttributes( {
									switchColorChecked: color,
								} ),
							label: __(
								'Switch color checked',
								'tax-switch-for-woocommerce'
							),
						},
						{
							value: switchBackgroundColor,
							onChange: ( color ) =>
								setAttributes( {
									switchBackgroundColor: color,
								} ),
							label: __(
								'Background color',
								'tax-switch-for-woocommerce'
							),
						},
						{
							value: switchBackgroundColorChecked,
							onChange: ( color ) =>
								setAttributes( {
									switchBackgroundColorChecked: color,
								} ),
							label: __(
								'Background color checked',
								'tax-switch-for-woocommerce'
							),
						},
						{
							value: switchTextColor,
							onChange: ( color ) =>
								setAttributes( {
									switchTextColor: color,
								} ),
							label: __(
								'Text color',
								'tax-switch-for-woocommerce'
							),
						},
					] }
				/>
				<PanelBody
					title={ __(
						'Switch labels',
						'tax-switch-for-woocommerce'
					) }
					initialOpen={ true }
				>
					<TextControl
						label={ __(
							'Including tax label',
							'tax-switch-for-woocommerce'
						) }
						value={ switchLabelIncl }
						onChange={ ( value ) =>
							setAttributes( { switchLabelIncl: value } )
						}
					/>
					<TextControl
						label={ __(
							'Excluding tax label',
							'tax-switch-for-woocommerce'
						) }
						value={ switchLabelExcl }
						onChange={ ( value ) =>
							setAttributes( { switchLabelExcl: value } )
						}
					/>
					<TextControl
						label={ __(
							'Switch aria label',
							'tax-switch-for-woocommerce'
						) }
						value={ switchAriaLabel }
						onChange={ ( value ) =>
							setAttributes( { switchAriaLabel: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<SwitchComponent
				{ ...attributes }
				readOnly={ true }
				originalTaxDisplay={ originalTaxDisplay }
			/>
		</div>
	);
}
